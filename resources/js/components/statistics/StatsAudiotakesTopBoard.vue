<template>
    <div class="card height-20 bg-primary text-light">

        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <span class="h5">
                        {{ $t('audiotakes.header_estimated_revenue') }}
                    </span>
                </div>
            </div>

            <b-overlay :show="isLoading" rounded="sm" class="p-5">
                <div class="row mt-3">
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2 mt-sm-2 text-center">
                        <span class="h1 mb-0">{{ revenue.today }}&euro;</span>
                        <span>Heute bis jetzt</span>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2 mt-4 mt-sm-2 text-center">
                        <span class="h1 mb-0">{{ revenue.yesterday }}&euro;</span>
                        <span>Gestern</span>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2 mt-4 mt-sm-2 text-center">
                        <span class="h1 mb-0">{{ revenue.last7days }}&euro;</span>
                        <span>Letzte 7 Tage</span>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2 mt-4 mt-sm-2 text-center">
                        <span class="h1 mb-0">{{ revenue.thisMonth }}&euro;</span>
                        <span>Diesen Monat</span>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2 mt-4 mt-sm-2 text-center">
                        <span class="h1 mb-0">{{ revenue.last30days }}&euro;</span>
                        <span>Letzte 30 Tage</span>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2 mt-4 mt-sm-2 text-center">
                        <span class="h1 mb-0">{{ revenue.funds }}&euro;</span>
                        <span>Guthaben</span>
                    </div>
                </div>
            </b-overlay>
        </div>
    </div>
</template>

<script>
import {eventHub} from "../../app";

export default {
    name: "StatsAudiotakesTopBoard",

    data(){
        return {
            revenue: {
                today: 0,
                yesterday: 0,
                last7days: 0,
                last30days: 0,
                thisMonth: 0,
                funds: 0,
            },
            isLoading: true,
            source: this.$route.query.i ? this.$route.query.i : null,
        }
    },

    mounted() {
        this.getCount();

        eventHub.$on('change', (type, params) => {
            if (this.$route.name && this.$route.name.startsWith("audiotakes")) {
                let refresh = false;
                // Only update on changes
                if (type === 'psource') {
                    if (params != this.source) {
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

    props: {
        action: {
            type: String,
            default: '/api/stats/pioniere/earnings'
        },
        title: String,
        subtitle: null
    },

    methods: {
        getCount() {
            this.isLoading = true;
            let url = this.action;

            if (this.source) {
                url += '?i=' + this.source;
            }

            axios.get(url)
                .then(response => {
                    this.$data.revenue = response.data;
                })
                .catch(error => {
                }).then(() => {
                    this.isLoading = false;
                });
        },
    },
}
</script>

<style scoped>

</style>
