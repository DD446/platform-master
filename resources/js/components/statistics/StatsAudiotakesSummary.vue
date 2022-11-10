<template>
    <div class="container">
        <b-overlay :show="isLoading" rounded="sm" class="p-5">
            <div class="row justify-content-around">
                <div class="col-auto text-center mt-2">
                    <span class="h1 mb-0">{{ stats.inventory }}</span>
                    <span>{{ $t('audiotakes.adswizz_metrics.inventory') }}</span>
                </div>
                <!--end of col-->
                <div class="col-auto text-center mt-2">
                    <span class="h1 mb-0">{{ stats.objectiveCountableSum }}</span>
                    <span>{{ $t('audiotakes.adswizz_metrics.objectiveCountableSum') }}</span>
                </div>
                <!--end of col-->
                <div class="col-auto text-center mt-2">
                    <span class="h1 mb-0">{{ stats.listenerIdHLL }}</span>
                    <span>{{ $t('audiotakes.adswizz_metrics.listenerIdHLL') }}</span>
                </div>
                <!--end of col-->
                <div class="col-auto text-center mt-2">
                    <span class="h1 mb-0">{{ stats.fillRate }}%</span>
                    <span>{{ $t('audiotakes.adswizz_metrics.fillRate') }}</span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </b-overlay>
    </div>
    <!--end of container-->
</template>

<script>
import {eventHub} from "../../app";

export default {
    name: "StatsAudiotakesSummary",

    data(){
        return {
            stats: {
                listenerIdHLL: 0,
                objectiveCountableSum: 0,
                inventory: 0,
                fillRate: 0,
            },
            range: {
                start: this.$route.query.df ? new Date(this.$route.query.df) : (d => new Date(d.setDate(d.getDate()-8)) )(new Date),
                end:  this.$route.query.dt ? new Date(this.$route.query.dt) : (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
            },
            isLoading: true,
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

    props: {
        action: {
            type: String,
            default: '/api/stats/pioniere/summary',
        },
    },

    methods: {
        getCount() {
            this.isLoading = true;
            let url = this.action + '?df=' + this.range.start.toISOString() + '&dt=' + this.range.end.toISOString();

            if (this.source) {
                url += '&i=' + this.source;
            }

            axios.get(url)
                .then(response => {
                    this.stats = response.data;
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
