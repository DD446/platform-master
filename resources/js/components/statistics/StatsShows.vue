<template>
    <b-overlay :show="isLoading">
        <b-alert variant="warning" :show="!episode">{{ $t('stats.message_choose_episode') }}</b-alert>

<!--        <b-row>
            <b-col cols="12" lg="2">
                <stats-counter-card
                    :count="uniqueListeners"
                    :title="$t('stats.title_unique_listeners')"
                    :subtitle="''"></stats-counter-card>
            </b-col>
            <b-col cols="12" lg="2">
                <stats-counter-card
                    :count="listeners"
                    :title="$t('stats.title_listeners')"
                    :subtitle="''"></stats-counter-card>
            </b-col>
        </b-row>-->

        <b-row class="pt-5 mx-lg-5">
            <b-col>
                <v-chart class="chart" ref="statsShows" :option="option" v-if="episode" />
            </b-col>
        </b-row>

        <b-row class="pt-5 mx-lg-5">
            <b-col>
                <b-table
                    responsive
                    small
                    striped
                    hover
                    :per-page="perPage"
                    :current-page="currentPage"
                    :empty-text="$t('audiotakes.no_data')"
                    :items="items"
                    :fields="fields"
                    :busy="isLoading">
                    <template #table-busy>
                        <div class="text-center my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>Lade Daten...</strong>
                        </div>
                    </template>
                    <template #empty="scope">
                        <h4>{{ scope.emptyText }}</h4>
                    </template>
                </b-table>
                <b-pagination
                    v-model="currentPage"
                    :total-rows="rows"
                    :per-page="perPage"
                    aria-controls="my-table"
                ></b-pagination>
            </b-col>
        </b-row>

        <b-row class="mt-5">
            <b-col cols="12" lg="6">
                <h3>{{ $t('stats.header_countries') }}</h3>
                <country-chart></country-chart>
            </b-col>
            <b-col cols="12" lg="6">
                <h3>{{ $t('stats.header_os') }}</h3>
                <os-chart></os-chart>
            </b-col>
        </b-row>

        <b-row class="mt-5">
            <b-col cols="12" lg="6">
                <h3>{{ $t('stats.header_client_types') }}</h3>
                <client-type-chart></client-type-chart>
            </b-col>
            <b-col cols="12" lg="6">
                <h3>{{ $t('stats.header_apps') }}</h3>
                <apps-chart></apps-chart>
            </b-col>
        </b-row>
    </b-overlay>
</template>

<script>
import { use } from 'echarts/core'
import { SVGRenderer } from "echarts/renderers";
import VChart, { THEME_KEY } from "vue-echarts";
import {
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent,
    ToolboxComponent
} from "echarts/components";
import {
    LineChart
} from "echarts/charts";
import {eventHub} from "../../app";
import ShowSelector from "../ShowSelector";
import CountryChart from "./CountryChart";
import OsChart from "./OsChart";
import ClientTypeChart from "./ClientTypeChart";
import StatsCounterCard from "./StatsCounterCard";
import AppsChart from "./AppsChart";

use([
    SVGRenderer,
    LineChart,
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent,
    ToolboxComponent
]);

const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
const docLocale = document.documentElement.lang;
export default {
    name: "StatsShows",

    components: {
        AppsChart,
        CountryChart,
        OsChart,
        VChart,
        ShowSelector,
        StatsCounterCard,
        ClientTypeChart
    },

    provide: {
        [THEME_KEY]: "light"
    },

    data() {
        return {
            option: {
                title: {
                    text: this.$t('stats.option_title_text_downloads_streams'),
                    left: "center"
                },
                tooltip: {
                    trigger: "axis",
                    show: true,
                    //formatter: "{a} <br/>{b} : {c} ({d}%)",
                    formatter: function (a) {
                        let params = a[0];

                        return params.axisValueLabel + '<br>' + `${a[2].value}` + ' Gesamt' + '<br>' + `${a[1].value}` + ' Streams' + '<br>' + `${a[0].value}` + ' Downloads';
                    }
                },
                legend: {
                    //orient: "vertical",
                    //left: "left",
                    type: 'scroll',
                    bottom: 5,
                    data: null
                },
                toolbox: {
                    feature: {
/*                        dataZoom: {
                            yAxisIndex: 'none'
                        },*/
/*                        saveAsImage: {
                            opts: {
                                type: 'png',
                                backgroundColor: 'white'
                            }
                        }*/
                    },
                    orient: 'vertical'
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    axisLabel: {
                        //formatter: '{dd}.{MM}.{yyyy}'
                    },
                    interval: 1
                },
                yAxis: {
                    //type: 'value'
                    name: 'Abrufe',
                    splitLine: {
                        show: false
                    },
                    axisLine: {
                        lineStyle: {
                            //color: colors[1],
                        },
                        onZero: 0,  //add this code
                    }
                },
                series: null,
/*                dataZoom: [
                    {
                        show: true,
                        realtime: true,
                        start: 30,
                        end: 70,
                        xAxisIndex: [0, 1]
                    },
                    {
                        type: 'inside',
                        realtime: true,
                        start: 30,
                        end: 70,
                        xAxisIndex: [0, 1]
                    }
                ],*/
            },
            //options: opts,
            isLoading: false,
            action: '/api/stats/shows',
            range: {
                start: this.$route.query.df ? new Date(this.$route.query.df) : (d => new Date(d.setDate(d.getDate()-8)) )(new Date),
                end: this.$route.query.dt ? new Date(this.$route.query.dt) : (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
            },
            episode: null,
            items: [],
            fields: [
                {
                    key: 'date',
                    label: this.$t('audiotakes.field_date'),
                    sortable: false
                },
                {
                    key: 'downloads',
                    label: this.$t('audiotakes.field_downloads'),
                    sortable: true
                },
                {
                    key: 'streams',
                    label: this.$t('audiotakes.field_streams'),
                    sortable: true
                },
                {
                    key: 'total',
                    label: this.$t('audiotakes.field_total'),
                    sortable: true
                }
            ],
            perPage: 20,
            currentPage: 1,
            listeners: { now: '-' },
            uniqueListeners: { now: '-' }
        }
    },

    methods: {
        getData() {
            this.isLoading = true;
            if (typeof this.$refs.statsShows != 'undefined') {
                this.$refs.statsShows.clear();
            }
            let url = this.action + '?df=' + this.range.start.toISOString() + '&dt=' + this.range.end.toISOString();

/*            if (this.source) {
                url += '&i=' + this.source;
            }*/

            if (this.episode) {
                url += '&e=' + this.episode.value + '&i=' + this.episode.feed_id;
            }

            axios.get(url)
                .then(response => {
                    this.option.title = response.data.option.title;
                    this.option.series = response.data.option.series;
                    this.option.legend.data = response.data.option.legend;

                    let xAxisData = [];
                    response.data.option.xAxis.forEach(function(day) {
                        const date = new Date(day);
                        xAxisData.push(date.toLocaleDateString(docLocale, dateOptions));
                    });
                    this.option.xAxis.data = xAxisData;

                    let _this = this;
                    this.items = [];

                    for (const [row, val] of Object.entries(response.data.table)) {
                        const date = new Date(val.date);
                        _this.items.push({
                            date: date.toLocaleDateString(docLocale, dateOptions),
                            downloads: val.downloads,
                            streams: val.streams,
                            total: val.total,
                        });
                    }

                    if (typeof response.data.countries != 'undefined') {
                        eventHub.$emit('change', 'country', response.data.countries);
                    }
                    if (typeof response.data.os != 'undefined') {
                        eventHub.$emit('change', 'os', response.data.os);
                    }
                    if (typeof response.data.clientTypes != 'undefined') {
                        eventHub.$emit('change', 'client_type', response.data.clientTypes);
                    }
                    if (typeof response.data.apps != 'undefined') {
                        eventHub.$emit('change', 'apps', response.data.apps);
                    }
                    if (typeof response.data.uniqueListeners != 'undefined') {
                        this.uniqueListeners = response.data.uniqueListeners;
                    }
                    if (typeof response.data.listeners != 'undefined') {
                        this.listeners = response.data.listeners;
                    }
                })
                .catch(error => {
                    this.option.legend.data = [];
                    this.option.xAxis.data = [];
                    this.option.series = [];
                    this.showError(error);
                }).then(() => {
                    this.isLoading = false;
                }
            );
        }
    },

    mounted() {
        eventHub.$on('change', (type, params) => {
            if (this.$route.name.startsWith("pioniere")) {
                // Prevent unnessasary backend calls
                let refresh = false;

                if (type === 'date') {
                    if (!params.start
                        || params.start.getTime() != this.range.start.getTime()) {
                        this.range.start = params.start;
                        refresh = true;
                    }
                    if (!params.end
                        || params.end.getTime() != this.range.end.getTime()) {
                        this.range.end = params.end;
                        refresh = true;
                    }
                } else if (type === 'psource') {
                    if (params != this.source) {
                        this.source = params;
                        refresh = true;
                    }
                }

                if (refresh && this.episode) {
                    this.getData();
                }
            }
        });
    },

    created() {
        eventHub.$on('show:selected', val => {
            if (val != this.episode) {
                this.episode = val;

                if (val) {
                    this.getData();
                }
            }
        });
    },

    computed: {
        rows() {
            return this.items.length
        }
    }
}
</script>

<style scoped>
.chart {
    width: 100%;
    min-height: 400px;
}
</style>
