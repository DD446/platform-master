<template>
    <div>
        <div class="row">
            <div class="col-12 col-lg-6">
                <a href="https://www.podcastpioniere.de?mtm_campaign=PodcasterStatistiken" target="_blank">
                    <img src="/images1/podcastpioniere.svg" alt="Podcast Pioniere Logo" class="img img-fluid left mr-3" style="max-height: 85px">
                </a>
            </div>
            <div class="col-12 col-lg-6 mt-4 mt-lg-0">
                <a href="https://audiotakes.net?mtm_campaign=PodcasterStatistiken" target="_blank">
                    <img src="/images1/audiotakes.svg" alt="audiotakes Logo" class="img img-fluid right ml-3" style="max-height: 65px">
                </a>
            </div>
        </div>
        <div v-if="$attrs.audiotakes">

            <div class="mb-2 mt-3">
                <b-nav tabs>
                    <b-nav-item
                        :to="getRoute('pioniere')"
                        :active='$route.name==="pioniere"'
                        lazy>{{ $t('audiotakes.tab_title_hosting') }}</b-nav-item>
                    <b-nav-item
                        :to="getRoute('pioniere/audiotakes')"
                        :active='$route.name==="audiotakes"'
                        lazy>{{ $t('audiotakes.tab_title_commercialization') }}</b-nav-item>
                </b-nav>
            </div>

            <div v-if="$route.name==='pioniere'">
                <stats-pioniere-hosting></stats-pioniere-hosting>
            </div>

            <div v-if="$route.name==='audiotakes'">
                <stats-audiotakes></stats-audiotakes>
            </div>

            <!--            <b-tabs class="mt-3">
                            <b-tab
                                active
                                lazy
                                :title="$t('audiotakes.tab_title_hosting')">
                                <stats-pioniere-hosting></stats-pioniere-hosting>
                            </b-tab>
                            <b-tab
                                lazy
                                :title="$t('audiotakes.tab_title_commercialization')">
                                <b-row class="mt-3">
                                    <b-col>
                                        <stats-pioniere-top-board></stats-pioniere-top-board>
                                    </b-col>
                                </b-row>

                                <div class="row mt-4">
                                    <div class="col-12 text-center">
                                        &lt;!&ndash;                <h1 class="display-4">
                                                            Vermarktungs-Statistiken
                                                        </h1>&ndash;&gt;

                                        <div class="row">
                                            <b-col lg="2" offset-lg="8" cols="12" class="pb-4 text-right">
                                                <stats-daterange-picker></stats-daterange-picker>
                                            </b-col>
                                            <b-col lg="2" cols="10" offset="2" offset-lg="0">
                                                <stats-pioniere-source-select></stats-pioniere-source-select>
                                            </b-col>
                                        </div>
                                        &lt;!&ndash;                <div class="row justify-content-around mt-2">
                                                            <div class="col-auto text-center">
                                                                <span class="help-block">Zahlen für den Zeitraum vom {{ range.start.toLocaleDateString() }} bis {{ range.end.toLocaleDateString() }}</span>
                                                            </div>
                                                        </div>&ndash;&gt;
                                    </div>
                                </div>

                                <b-row>
                                    <b-col class="space-sm">
                                        <stats-pioniere-summary></stats-pioniere-summary>
                                    </b-col>
                                </b-row>

                                <div class="row mt-4">
                                    <div class="col-12 text-center">
                                        <b-card no-body>
                                            <b-tabs pills card vertical>
                                                <b-tab :title="$t('stats.nav_title_pioniere_hits')" active>
                                                    <b-card-text>
                                                        <h3>{{$t('stats.nav_title_pioniere_hits')}}</h3>
                                                        <stats-pioniere-hits></stats-pioniere-hits>
                                                    </b-card-text>
                                                </b-tab>
                                                <b-tab :title="$t('stats.nav_title_pioniere_region')" lazy>
                                                    <b-card-text>
                                                        <h3>Regionalität</h3>
                                                        <stats-pioniere-region></stats-pioniere-region>
                                                    </b-card-text>
                                                </b-tab>
                                                <b-tab :title="$t('stats.nav_title_pioniere_demographics')" lazy>
                                                    <b-card-text>
                                                        <h3>Demographie</h3>
                                                        <stats-pioniere-demographic></stats-pioniere-demographic>
                                                    </b-card-text>
                                                </b-tab>
                                            </b-tabs>
                                        </b-card>
                                    </div>
                                </div>
                            </b-tab>
                        </b-tabs>-->
        </div>
        <div v-else>
            <div class="container-fluid bg-white p-3">
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <b-card>
                            <b-card-text>
                                Für keinen Deiner Podcasts ist bisher die Vermarktung über die Podcast Pioniere aktiviert, daher werden hier keine Zahlen angezeigt.
                            </b-card-text>
                            <b-card-text>
                                <a href="/vermarktung" class="btn btn-success">Starte jetzt mit der Monetarisierung Deiner Podcasts und verdiene Geld und/oder Guthaben!</a>
                            </b-card-text>
<!--                            <hr>
                            <b-card-body>
                                Statistik-Vorschau
                                <figure>
                                    <img src="/images1/audiotakes-statistiken-vorschau.png" alt="Statistik Vorschau" class="img img-fluid img-thumbnail" />
                                    <figcaption>Diese Statistiken stehen Dir als Pionier zur Verfügung.</figcaption>
                                </figure>
                            </b-card-body>-->
                        </b-card>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import StatsPioniereHosting from "./StatsPioniereHosting";
import StatsAudiotakes from "./StatsAudiotakes";

export default {
    name: "StatsPioniere",

    components: {
        StatsPioniereHosting,
        StatsAudiotakes
    },

    data(){
        return {
        }
    },
}
</script>

<style scoped>
</style>
