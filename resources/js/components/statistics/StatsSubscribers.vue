<template>
    <b-row class="mt-5 mb-2">
        <b-col cols="3" offset="9" sm="2" offset-sm="10" lg="2" offset-lg="10" xl="1" offset-xl="11">
            <report-view></report-view>
        </b-col>
        <b-col cols="12" lg="11">
            <subscriber-chart v-show="reportview==='chart'" :items="items" :is-loading="isLoading"></subscriber-chart>
            <subscriber-table v-show="reportview==='table'" :items="tableData" :is-loading="isLoading"></subscriber-table>
        </b-col>
    </b-row>
</template>


<script>
import SubscriberChart from "./SubscriberChart";
import ReportView from "./ReportView";
import SubscriberTable from "./SubscriberTable";
import {eventHub} from "../../app";

export default {
    name: "StatsSubscribers",

    components: {
        ReportView,
        SubscriberChart,
        SubscriberTable,
        eventHub
    },

    data() {
        return {
            items: {},
            tableData: [],
            isLoading: true,
            reportview: 'chart',
            range: {
                start: this.$route.query.df ? new Date(this.$route.query.df) : (d => new Date(d.setDate(d.getDate()-31)) )(new Date),
                end: this.$route.query.dt ? new Date(this.$route.query.dt) : (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
            },
            perPage: 20,
            currentPage: 1,
            action: '/api/stats/subscribers',
            source: this.$route.query.s ? this.$route.query.s : null
        }
    },

    mounted() {
        this.getData();

        eventHub.$on('reportview:changed', type => {
            this.reportview = type;
        });

        eventHub.$on('change', (type, params) => {
            if (this.$route.name && this.$route.name.startsWith("podcaster-")) {
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
                } else if (type === 'source') {
                    if (params !== this.source) {
                        this.source = params;
                        refresh = true;
                    }
                }

                if (refresh) {
                    this.getData();
                }
            }
        });
    },

    methods: {
        getData() {
            this.isLoading = true;
            if (typeof this.$refs.statsSubscribers != 'undefined') {
                this.$refs.statsSubscribers.clear();
            }
            let url = this.action + '?df=' + this.range.start.toISOString() + '&dt=' + this.range.end.toISOString();

            if (this.source) {
                url += '&feed_id=' + this.source;
            }

            axios.get(url)
                .then(response => {
                    this.items = response.data;
                    let _data = [];
                    for (const [key, value] of Object.entries( response.data.all.total)) {
                        _data.push({date: key, hits: value});
                    }
                    this.tableData = _data;
                })
                .catch(error => {
                    this.showError(error);
                }).then(() => {
                    this.isLoading = false;
                }
            );
        }
    }
}
</script>

<style scoped>

</style>
