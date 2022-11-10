<template>
    <div>
        <b-row class="mt-3">
            <b-col>
                <stats-audiotakes-top-board></stats-audiotakes-top-board>
            </b-col>
        </b-row>

        <div class="row mt-4">
            <div class="col-12 text-center">

                <div class="row">
                    <b-col lg="2" offset-lg="8" cols="12" class="pb-4 text-right">
                        <stats-daterange-picker></stats-daterange-picker>
                    </b-col>
                    <b-col lg="2" cols="10" offset="2" offset-lg="0">
                        <stats-pioniere-source-select></stats-pioniere-source-select>
                    </b-col>
                </div>
                <!--                <div class="row justify-content-around mt-2">
                                    <div class="col-auto text-center">
                                        <span class="help-block">Zahlen für den Zeitraum vom {{ range.start.toLocaleDateString() }} bis {{ range.end.toLocaleDateString() }}</span>
                                    </div>
                                </div>-->
            </div>
        </div>

        <b-row>
            <b-col class="space-sm">
                <stats-audiotakes-summary></stats-audiotakes-summary>
            </b-col>
        </b-row>

        <div class="row mt-4">
            <div class="col-12 text-center">
                <b-card no-body>
                    <b-tabs pills card vertical>
                        <b-tab :title="$t('stats.nav_title_pioniere_hits')" active lazy>
                            <b-card-text>
                                <h3>{{$t('stats.nav_title_pioniere_hits')}}</h3>
                                <stats-audiotakes-hits></stats-audiotakes-hits>
                            </b-card-text>
                        </b-tab>
                        <b-tab :title="$t('stats.nav_title_pioniere_region')" lazy>
                            <b-card-text>
                                <h3>Regionalität</h3>
                                <stats-audiotakes-region></stats-audiotakes-region>
                            </b-card-text>
                        </b-tab>
                        <b-tab :title="$t('stats.nav_title_pioniere_demographics')" lazy>
                            <b-card-text>
                                <h3>Demographie</h3>
                                <stats-audiotakes-demographic></stats-audiotakes-demographic>
                            </b-card-text>
                        </b-tab>
                    </b-tabs>
                </b-card>
            </div>
        </div>
        </div>
</template>

<script>
import StatsAudiotakesTopBoard from "./StatsAudiotakesTopBoard";
import StatsAudiotakesSummary from "./StatsAudiotakesSummary";
import StatsAudiotakesHits from "./StatsAudiotakesHits";
import StatsAudiotakesRegion from "./StatsAudiotakesRegion";
import StatsAudiotakesDemographic from "./StatsAudiotakesDemographic";
import StatsDaterangePicker from "./StatsDaterangePicker";
import StatsPioniereSourceSelect from "./StatsPioniereSourceSelect";

export default {
    name: "StatsAudiotakes",

    components: {
        StatsAudiotakesTopBoard,
        StatsAudiotakesSummary,
        StatsAudiotakesHits,
        StatsAudiotakesDemographic,
        StatsAudiotakesRegion,
        StatsPioniereSourceSelect,
        StatsDaterangePicker,
    },

    data(){
        return {

        }
    },
}
</script>

<style scoped>
</style>
