<template>
    <div>
        <b-table
            responsive
            striped
            hover
            small
            no-provider-sorting
            :perPage="perPage"
            :items="items"
            :fields="fields"
            :filter="range"
            :busy.sync="isLoading">
            <template #table-busy>
                <div class="text-center my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Lade Daten...</strong>
                </div>
            </template>
        </b-table>

<!--        <b-row>
            <b-col md="9" class="my-1">
                <b-pagination
                    v-show="totalRows > perPage"
                    :total-rows="totalRows"
                    :per-page="perPage"
                    v-model="currentPage"
                    class="my-0"/>
            </b-col>
            <b-col md="3" class="my-1">
                <b-form-group label="Ergebnisse pro Seite" class="mb-0">
                    <b-form-select :options="pageOptions" v-model="perPage"/>
                </b-form-group>
            </b-col>
        </b-row>-->
    </div>
</template>

<script>
import {eventHub} from "../../app";

export default {
    name: "StatsAudiotakesHits",

    data() {
        return {
            range: {
                start: this.$route.query.df ? new Date(this.$route.query.df) : (d => new Date(d.setDate(d.getDate()-8)) )(new Date),
                end:  this.$route.query.dt ? new Date(this.$route.query.dt) : (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
            },
            pageNumber: 1,
            totalRows: 0,
            isLoading: false,
            items: this.myProvider,
            sortBy: '__time',
            sortDesc: true,
            fields: [
                {
                    key: this.$t('audiotakes.adswizz_metrics.__time'),
                    sortable: true
                },
                {
                    key: this.$t('audiotakes.adswizz_metrics.inventory'),
                    sortable: true
                },
                {
                    key: this.$t('audiotakes.adswizz_metrics.objectiveCountableSum'),
                    sortable: true
                },
                {
                    key: this.$t('audiotakes.adswizz_metrics.listenerIdHLL'),
                    sortable: true
                },
                {
                    key: this.$t('audiotakes.adswizz_metrics.fillRate'),
                    sortable: true
                }
            ],
            currentPage: 1,
            perPage: 100,
            pageOptions: [5, 10, 15, 25, 50, 250, 500],
            source: this.$route.query.i ? this.$route.query.i : null
        }
    },

    mounted() {
        eventHub.$on('change', (type, params) => {
            if (type === 'date') {
                this.range.start = params.start;
                this.range.end = params.end;
            } else if (type === 'psource') {
                this.source = params;
            }
        });
    },

    methods: {
        myProvider(ctx) {
            let url = this.action + '?df=' + this.range.start.toISOString()
            + '&dt=' + this.range.end.toISOString()
            + '&page[number]=' + ctx.currentPage
            + '&page[size]=' + (ctx.perPage > 0 ? ctx.perPage : 10)
            + '&sortBy=' + ctx.sortBy
            + '&sortDesc=' + ctx.sortDesc;

            if (this.source) {
                url += '&i=' + this.source;
            }

            const promise = axios.get(url);

            // Must return a promise that resolves to an array of items
            return promise.then(data => {
                // Pluck the array of items off our axios response
                const items = data.data.items
                this.perPage = data.data.count;
                // Must return an array of items or an empty array if an error occurred
                return items || []
            })
        }
    },

    props: {
        action: {
            type: String,
            default: '/api/stats/pioniere/hits'
        }
    },
}
</script>

<style scoped>

</style>
