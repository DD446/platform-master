<template>
    <submit :feed="feedId" :uuid="uuid"></submit>
</template>

<script>
import {eventHub} from "../../app";
import Submit from "./Submit";

export default {
    name: "ChannelSubmit",

    data() {
        return {
            feed: null,
        }
    },

    components: {
        Submit
    },

    props: {
        feedId: String,
        uuid: String,
        feeds: {
            required: true,
            type: Array
        },
    },

    methods: {
        emitPageInfo() {
            let items = [{
                text: this.feed.attributes.title,
                href: '#/podcast/' + this.feedId,
            },{
                text: this.$t('nav.link_check'),
                href: '#/podcast/' + this.feedId + '/promotion',
            }];

            let page = {
                header: this.$t('feeds.header_submit'),
                subheader: this.$t('feeds.subheader_submit', {title: this.feed.attributes.title}),
            }
            eventHub.$emit('podcasts:page:infos', items, page);
        },
        getFeedFromFeeds() {
            let _feedId = this.feedId;
            return this.feeds.filter(function (feed) {
                return feed.id === _feedId;
            })
        },
        updatePageInfo() {
            this.feed = this.getFeedFromFeeds().shift();
            this.emitPageInfo();
        }
    },

    mounted() {
        // If this (sub)page is called from parent
        // feeds is (pre-)filled
        // but if the page is called directly
        // we have the fallback in watch
        // and have to wait til feeds gets filled from its parent
        if (this.feeds && this.feeds.length > 0) {
            this.updatePageInfo();
        }
    },

    watch: {
        feeds: function() {
            this.updatePageInfo();
        }
    }
}
</script>

<style scoped>

</style>
