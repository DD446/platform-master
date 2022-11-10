<template>
    <b-row>
        <b-col>
            <v-chart :loading="isLoading" class="chart" ref="statsSubscribers" :option="getData" :initOpts="initOpts" />
        </b-col>
    </b-row>
</template>

<script>
import 'echarts/i18n/langDE';
import { use } from 'echarts/core'
import { SVGRenderer } from "echarts/renderers";
import VChart, { THEME_KEY } from "vue-echarts";
import {
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    ToolboxComponent,
    DatasetComponent,
    TransformComponent
} from "echarts/components";
import {
    LineChart
} from "echarts/charts";
import {eventHub} from "../../app";

use([
    SVGRenderer,
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    ToolboxComponent,
    DatasetComponent,
    TransformComponent,
    LineChart
]);

const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
const docLocale = document.documentElement.lang;
export default {
    name: "SubscriberChart",

    components: {
        VChart
    },

    provide: {
        [THEME_KEY]: "light"
    },

    props: {
        items: {
            type: [Object, Array],
            required: true
        },
        isLoading: {
            type: Boolean,
            default: true
        }
    },

    data() {
        return {
            option: {
                title: {
                    text: this.$t('stats.option_title_text_subscriptions'),
                    subtext: this.$t('stats.option_title_subtext_subscriptions'),
                    left: "center"
                },
                legend: {
                    type: 'scroll',
                    bottom: 5,
                    data: []
                },
                tooltip: {
                    trigger: 'item',
                    show: true,
                    showContent: true,
                    formatter: function (params) {
                        return params.seriesName + '<br>' + params.name + '<br>' + params.value + ' Zugriffe';
                    }
                },
                xAxis: {
                    type: 'category',
                    data: []
                },
                yAxis: {},
                series: [],
            },
            fields: [
                {
/*                    key: 'date',
                    label: this.$t('audiotakes.field_date'),
                    sortable: false*/
                },
            ],
            initOpts: {
                locale: 'DE'
            },
        }
    },

    computed: {
        getData() {
            if (typeof this.$refs.statsSubscribers != 'undefined') {
                this.$refs.statsSubscribers.clear();
            }

            let total = [];
            let xAxis = [];

            this.option.legend.data = [];
            this.option.xAxis.data = [];
            this.option.yAxis = {};
            this.option.series = [];

            if (typeof this.items != "undefined"
                && typeof this.items.feeds != "undefined"
                &&  typeof this.items.all != "undefined") {
                const hasMultiple = this.items.feeds.length > 1;
                // Build xAxis once
                for (const [key, value] of Object.entries(this.items.all.total)) {
                    const date = new Date(parseInt(key));

                    if (hasMultiple) {
                        total.push({
                            value: value
                        });
                    }
                    xAxis.push({
                        value: date.toLocaleDateString(docLocale, dateOptions),
                    });
                }

                this.option.title.subtext = this.$t('stats.option_title_subtext_subscriptions_daterange', {
                    df: xAxis[0].value,
                    dt: xAxis[xAxis.length - 1].value
                });

                let i = 0;

                if (hasMultiple) {
                    // Add series
                    this.option.series.push({
                        name: 'Gesamt',
                        type: 'line',
                        smooth: false,
                        data: total
                    })
                    this.option.legend.data.push('Gesamt');
                }

                for (const [feedId, feed] of Object.entries(this.items.feeds)) {
                    let total = [];
                    for (const [key, value] of Object.entries(feed.total)) {
                        const date = new Date(parseInt(key));
                        total.push({
                            value: value
                        });
                    }
                    // Add series
                    this.option.series.push({
                        name: feed.title,
                        type: 'line',
                        smooth: false,
                        data: total
                    })
                    this.option.legend.data.push(feed.title);
                }
                this.option.xAxis.data = xAxis;
            }

            return this.option;
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
