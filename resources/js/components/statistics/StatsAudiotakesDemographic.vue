<template>
    <div>
        <b-row>
            <b-col cols="12">
                <v-chart class="chart" ref="statsDemograph" :option="option" />
            </b-col>
        </b-row>

        <b-table
            responsive
            striped
            hover
            :items="items"
            :fields="fields"
            :busy="isLoading">
            <template #table-busy>
                <div class="text-center my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Lade Daten...</strong>
                </div>
            </template>
        </b-table>
    </div>
</template>

<script>
import {eventHub} from "../../app";
import {use} from "echarts/core";
import {SVGRenderer} from "echarts/renderers";
import VChart, { THEME_KEY } from "vue-echarts";
import {PieChart} from "echarts/charts";
import {GridComponent, LegendComponent, TitleComponent, ToolboxComponent, TooltipComponent} from "echarts/components";

use([
    SVGRenderer,
    PieChart,
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent,
    ToolboxComponent
]);

export default {
    name: "StatsAudiotakesDemographic",

    components: {
        VChart
    },

    provide: {
        [THEME_KEY]: "light"
    },

    data() {
        return {
            range: {
                start: this.$route.query.df ? new Date(this.$route.query.df) : (d => new Date(d.setDate(d.getDate()-8)) )(new Date),
                end: this.$route.query.dt ? new Date(this.$route.query.dt) : (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
            },
            pageSize: 10,
            pageNumber: 1,
            isLoading: true,
            items: [],
            fields: [
                {
                    key: this.$t('audiotakes.adswizz_metrics.age_group'),
                    sortable: true
                },
                {
                    key: this.$t('audiotakes.adswizz_metrics.inventory'),
                    sortable: true
                },
                {
                    key: this.$t('audiotakes.adswizz_metrics.spread'),
                    sortable: true
                }
            ],
            source: this.$route.query.i ? this.$route.query.i : null,
            option: {
                title: {
                    text: this.$t('stats.option_title_age_spreading'),
                    //subtext: this.subtitle,
                    left: 'center'
                },
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    orient: 'vertical',
                    left: 'left'
                },
                series: [
                    {
                        name: '',
                        type: 'pie',
                        radius: '50%',
                        data: [],
                        emphasis: {
                            scale: true,
                            scaleSize: 20,
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ],
            },
        }
    },

    mounted() {
        this.getCount();

        eventHub.$on('change', (type, params) => {
            if (this.$route.name && this.$route.name.startsWith("audiotakes")) {
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
                    if (params !== this.source) {
                        this.source = params;
                        refresh = true;
                    }
                }

                if (refresh) {
                    this.getCount();
                }
            }
        });
    },

    methods: {
        getCount() {
            this.isLoading = true;

            let url = this.action + '?df=' + this.range.start.toISOString() + '&dt=' + this.range.end.toISOString() + '&page[size]=' + this.pageSize + '&page[number]=' + this.pageNumber;

            if (this.source) {
                url += '&i=' + this.source;
            }

            axios.get(url)
                .then(response => {
                    this.items = response.data;
                    let data = [];
                    response.data.forEach(function (val) {
                        data.push({
                            name: val.Altersgruppe,
                            value: val['Werbe-Slots']
                        })
                    })
                    this.option.series[0].data = data;
                })
                .catch(error => {
                    eventHub.$emit('show-message:error', error);
                }).then(() => {
                    this.isLoading = false;
                });
        },
    },

    props: {
        action: {
            type: String,
            default: '/api/stats/pioniere/demographic'
        }
    },
}
</script>

<style scoped>
.chart {
    width: 100%;
    min-height: 400px;
}
</style>
