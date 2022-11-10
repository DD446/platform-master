<template>
    <span
        class="text-muted"
        v-b-tooltip.hover.top="$t('feeds.tooltip_feeds_count', {used: feedsUsed, total: feedsTotal, included: feedsIncluded, extra: feedsExtra })">({{ feedsUsed }}/{{ feedsTotal }})</span>
</template>

<script>
    export default {
        name: "ChannelCount",

        props: ["username"],

        data() {
            return {
                feedsUsed: '',
                feedsTotal: '',
                feedsIncluded: '',
                feedsExtra: '',
            }
        },

        methods: {
            getFeedCount() {
                axios.get('/feed/count')
                    .then(response => {
                        this.feedsUsed = response.data.used;
                        this.feedsTotal = response.data.total;
                        this.feedsIncluded = response.data.included;
                        this.feedsExtra = response.data.extra;
                    })
                    .catch(error => {
                    });
            }
        },

        mounted() {
            this.getFeedCount();
        },

    }
</script>

<style scoped>

</style>
