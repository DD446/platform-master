<template>
    <div>
        <b-table responsive striped hover :items="items" :fields="fields" :busy="isLoading">
            <template #table-busy>
                <div class="text-center my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Lade Daten...</strong>
                </div>
            </template>

            <template #cell(country_name)="row">
                <span size="sm" @click="row.toggleDetails" class="mr-2">
                    {{ row.detailsShowing ? '▾' : '▸'}}
                </span>

                {{ row.item.Land }}
            </template>

            <template #row-details="row">
                <country-chart :regional="row.item.regional_data" title="" subtitle=""></country-chart>
            </template>
        </b-table>
    </div>
</template>

<script>
import {eventHub} from "../../app";
import CountryChart from "./CountryChart";

export default {
    name: "StatsAudiotakesRegion",

    components: {
        CountryChart,
    },

    data() {
        return {
            range: {
                start:  this.$route.query.df ? new Date(this.$route.query.df) : (d => new Date(d.setDate(d.getDate()-8)) )(new Date),
                end:  this.$route.query.dt ? new Date(this.$route.query.dt) : (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
            },
            pageSize: 10,
            pageNumber: 1,
            isLoading: true,
            items: [],
            fields: [
                {
                    key: 'country_name',
                    label: this.$t('audiotakes.adswizz_metrics.geoCountryName'),
                    sortable: true
                },
                {
                    key: this.$t('audiotakes.adswizz_metrics.inventory'),
                    sortable: true
                },
                {
                    key: this.$t('audiotakes.adswizz_metrics.objectiveCountableSum'),
                    sortable: true
                }
            ],
            source: this.$route.query.i ? this.$route.query.i : null,
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
            default: '/api/stats/pioniere/region'
        }
    },
}
</script>

<style scoped>

</style>
