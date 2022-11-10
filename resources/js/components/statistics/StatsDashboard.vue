<template>
    <div>
        <b-row>
            <b-col cols="12">
                <img src="/images1/podcaster_logo_260x90_trans.png" alt="Podcaster Logo" class="img img-fluid left mr-3" style="max-height: 85px">
            </b-col>
        </b-row>
        <b-row>
            <b-col cols="12" xl="2" class="mt-5">
                <stats-podcaster-side-nav :useNewStatistics="$attrs.useNewStatistics"></stats-podcaster-side-nav>
            </b-col>
            <b-col cols="12" xl="10">
                <div class="row">
                    <b-col lg="4" offset-lg="6" cols="12" class="pb-4 text-right">
                        <stats-daterange-picker></stats-daterange-picker>
                    </b-col>
                    <b-col lg="2" cols="10" offset="2" offset-lg="0">
                        <stats-source-select></stats-source-select>
                    </b-col>
                </div>
                <b-row class="">
                    <b-col cols="12" sm="6" lg="2">
                        <stats-counter
                            :title="$t('stats.count_title_listeners')"
                            :subtitle="$t('stats.count_subtitle_listeners_yesterday')"
                            action="/api/stats/counter?stat=listeners&range=yesterday"></stats-counter>
                    </b-col>
                    <b-col cols="12" sm="6" lg="2" class="stats-counter">
                        <stats-counter
                            :title="$t('stats.count_title_listeners')"
                            :subtitle="$t('stats.count_subtitle_listeners_last7days')"
                            action="/api/stats/counter?stat=listeners&range=last7days"></stats-counter>
                    </b-col>
                    <b-col cols="12" sm="6" lg="2" class="stats-counter">
                        <stats-counter
                            :title="$t('stats.count_title_listeners')"
                            :subtitle="$t('stats.count_subtitle_listeners_last30days')"
                            action="/api/stats/counter?stat=listeners&range=last30days"></stats-counter>
                    </b-col>
                    <b-col cols="12" sm="6" lg="2" class="stats-counter">
                        <stats-counter
                            :title="$t('stats.count_title_listeners')"
                            :subtitle="$t('stats.count_subtitle_listeners_alltime')"
                            action="/api/stats/counter?stat=listeners&range=alltime"></stats-counter>
                    </b-col>
                    <b-col cols="12" sm="6" lg="2">
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
                </b-row>

                <b-row class="mt-3">
                    <b-col cols="12" md="6" xl="4" class="pt-3">
                        <stats-shows-table
                            :action="'/api/stats/shows/last'"
                            :export-settings="{sort: 'hits', restrict: 'newest', restrict_limit: 10}"
                            :header="$t('stats.header_last_shows')"></stats-shows-table>
                    </b-col>
                    <b-col cols="12" md="6" xl="4" class="pt-3">
                        <stats-shows-table
                            :action="'/api/stats/shows/top'"
                            :export-settings="{sort: 'hits'}"
                            :header="$t('stats.header_charts')"></stats-shows-table>
                    </b-col>
                    <b-col cols="12" md="6" xl="4" class="pt-3">
                        <stats-shows-table-complete
                            :action="'/api/stats/shows/charts'"
                            :export-settings="{sort: 'hits', date: 'all'}"
                            :header="$t('stats.header_top_shows', { count: 5 })"></stats-shows-table-complete>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    import StatsPodcasterSideNav from "./StatsPodcasterSideNav";
    import StatsCounter from "./StatsCounter";
    import StatsShowsTable from "./StatsShowsTable";
    import StatsShowsTableComplete from "./StatsShowsTableComplete";

    import StatsDaterangePicker from "./StatsDaterangePicker";
    import StatsSourceSelect from "./StatsSourceSelect";

    export default {
        name: "StatsDashboard",

        components: {
            StatsPodcasterSideNav,
            StatsShowsTable,
            StatsCounter,
            StatsShowsTableComplete,
            StatsSourceSelect,
            StatsDaterangePicker,
        }
    }
</script>

<style scoped>
</style>
