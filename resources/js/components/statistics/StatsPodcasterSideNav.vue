<template>
    <b-nav vertical class="w-25">
        <b-nav-item :active='!$route.name' :to="getRoute('')">{{ $t('stats.title_dashboard') }}</b-nav-item>

        <div v-if="!useNewStatistics">
            <b-nav-item :active='$route.name==="listeners"' href="/legacy/statistiken/hoerer">{{$t('stats.title_listeners')}}</b-nav-item>
            <b-nav-item :active='$route.name==="downloads"' href="/legacy/statistiken">Downloads</b-nav-item>
        </div>
<!--        <b-nav-item :active='$route.name==="podcaster-listeners"' :to="getRoute('podcaster/listeners')">{{$t('stats.title_listeners')}} (neu)</b-nav-item>-->
        <b-nav-item :active='$route.name==="podcaster-listeners"' v-if="useNewStatistics" :to="getRoute('podcaster/listeners')">{{$t('stats.title_listeners')}}</b-nav-item>
        <b-nav-item :active='$route.name==="podcaster-subscribers"' :to="getRoute('podcaster/subscribers')">{{$t('stats.title_subscribers')}}</b-nav-item>
    </b-nav>
</template>

<script>
export default {
    name: "StatsPodcasterSideNav",

    props: {
        useNewStatistics: {
            type: Boolean,
            default: false
        }
    }
}
</script>

<style scoped>
</style>
