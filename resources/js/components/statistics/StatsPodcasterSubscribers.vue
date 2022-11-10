<template>
    <div>
        <b-row>
        <b-col cols="12">
            <img src="/images1/podcaster_logo_260x90_trans.png" alt="Podcaster Logo" class="img img-fluid left mr-3" style="max-height: 85px">
        </b-col>
    </b-row>
        <b-row>
        <b-col cols="12" lg="2" class="mt-5">
            <stats-podcaster-side-nav></stats-podcaster-side-nav>
        </b-col>
        <b-col cols="12" lg="10">
            <h2>{{ $t('stats.title_subscribers') }}</h2>

            <b-col>
                <div class="row">
                    <b-col lg="4" offset-lg="6" cols="12" class="pb-4 text-right">
                        <stats-daterange-picker></stats-daterange-picker>
                    </b-col>
                    <b-col lg="2" cols="10" offset="2" offset-lg="0">
                        <stats-source-select :default-value-label="$t('stats.text_option_all_podcasts')"></stats-source-select>
                    </b-col>
                </div>
            </b-col>

            <b-row>
                <b-col cols="12" sm="6" lg="2" class="stats-counter">
                    <stats-counter
                        :title="$t('stats.count_title_subscribers')"
                        :subtitle="$t('stats.count_subtitle_subscribers_last_7_days')"
                        text=""
                        action="/api/stats/counter?stat=subscribers&range=last7days"></stats-counter>
                </b-col>

                <b-col cols="12" sm="6" lg="2" class="stats-counter">
                    <stats-counter
                        :title="$t('stats.count_title_subscribers')"
                        :subtitle="$t('stats.count_subtitle_subscribers_last_30_days')"
                        text=""
                        action="/api/stats/counter?stat=subscribers&range=last30days"></stats-counter>
                </b-col>

                <b-col cols="12" sm="6" lg="2" class="stats-counter">
                    <stats-counter
                        :title="$t('stats.count_title_subscribers')"
                        :subtitle="$t('stats.count_subtitle_subscribers_quarter')"
                        text=""
                        action="/api/stats/counter?stat=subscribers&range=quarter"></stats-counter>
                </b-col>

                <b-col cols="12" sm="6" lg="2" class="stats-counter">
                    <stats-counter
                        :title="$t('stats.count_title_subscribers')"
                        :subtitle="$t('stats.count_subtitle_subscribers_lastquarter')"
                        text=""
                        action="/api/stats/counter?stat=subscribers&range=lastquarter"></stats-counter>
                </b-col>

                <b-col cols="12" sm="6" lg="2" class="stats-counter">
                    <stats-counter
                        :title="$t('stats.count_title_subscribers')"
                        :subtitle="$t('stats.count_subtitle_subscribers_year')"
                        text=""
                        action="/api/stats/counter?stat=subscribers&range=year"></stats-counter>
                </b-col>

                <b-col cols="12" sm="6" lg="2" class="stats-counter">
                    <stats-counter
                        :title="$t('stats.count_title_subscribers')"
                        :subtitle="$t('stats.count_subtitle_subscribers_lastyear')"
                        text=""
                        action="/api/stats/counter?stat=subscribers&range=lastyear"></stats-counter>
                </b-col>
            </b-row>

            <stats-subscribers></stats-subscribers>

            <b-row class="mt-5 mb-2" v-show="!$route.query.s">
                <b-col cols="12">
                    <h4>
                        Externe Dienste
                        <p class="small font-weight-light">Abonnenten gestern</p>
                    </h4>
                </b-col>
                <b-col cols="12">
                    <stats-subscribers-external></stats-subscribers-external>
                </b-col>
            </b-row>
        </b-col>
    </b-row>
    </div>
</template>

<script>
import StatsPodcasterSideNav from "./StatsPodcasterSideNav";
import StatsCounter from "./StatsCounter";
import StatsSourceSelect from "./StatsSourceSelect";
import StatsDaterangePicker from "./StatsDaterangePicker";
import StatsSubscribers from "./StatsSubscribers";
import StatsSubscribersExternal from "./StatsSubscribersExternal";

export default {
        name: "StatsPodcasterSubscribers",

        components: {
            StatsSubscribersExternal,
            StatsPodcasterSideNav,
            StatsCounter,
            StatsSourceSelect,
            StatsDaterangePicker,
            StatsSubscribers
        }
    }
</script>

