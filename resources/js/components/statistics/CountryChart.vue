<template>
    <b-overlay :show="isLoading">

        <b-row>
            <b-col cols="12">
                <v-chart class="chart" ref="statsCountries" :option="option" />
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
<!--                <b-pagination
                    v-model="currentPage"
                    :total-rows="rows"
                    :per-page="perPage"
                    aria-controls="my-table"
                ></b-pagination>-->
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
    PieChart,
} from "echarts/charts";
import {eventHub} from "../../app";

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
    name: "CountryChart",

    components: {
        VChart
    },

    provide: {
        [THEME_KEY]: "light"
    },

    props: {
        countries: {
            type: [Array, Object],
            required: false
        },
        regional: {
            type: [Array, Object],
            required: false
        },
        title: {
            type: String,
            default: function() { return this.$t('stats.option_title_text_countries'); },
        },
        subtitle: {
            type: String,
            default: function() { return this.$t('stats.option_title_subtext_countries'); },
        }
    },

    data() {
        return {
            option: {
                title: {
                    text: this.title,
                    subtext: this.subtitle,
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
            isLoading: false,
            perPage: 20,
            currentPage: 1,
            items: [],
            fields: [
                {
                    key: 'name',
                    label: this.$t('audiotakes.field_country'),
                    sortable: true
                },
                {
                    key: 'value',
                    label: this.$t('audiotakes.field_count'),
                    sortable: true
                },
                {
                    key: 'percentage',
                    label: this.$t('audiotakes.field_percentage'),
                    sortable: true
                }
            ]
        }
    },

    mounted() {
        //this.option.series[0].data = this.countries;
        if (this.regional) {
            let data = [];
            this.regional.forEach(function(dat) {
                data.push({
                    name: dat.total.geoRegionName,
                    value: dat.total.inventory
                })
            });
            this.option.series[0].data = data;
            this.items = data;
        }

        eventHub.$on('change', (type, params) => {
            if (type === 'country') {
                this.option.series[0].data = params;
                this.items = params;
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
