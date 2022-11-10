<template>
    <player-configurator
        :username="username"
        :url="playerBaseUrl"
        :can-embed="canEmbed"
        :can-create-player-configurations="canCreatePlayerConfigurations"
        :can-use-custom-player-styles="canUseCustomPlayerStyles"
        :amount-player-configurations="amountPlayerConfigurations"
        :feed="feedId"></player-configurator>
</template>

<script>
import {eventHub} from "../../app";
import PlayerConfigurator from "../player/PlayerConfigurator";

export default {
    name: "ChannelPlayer",

    data() {
        return {
            feed: null,
        }
    },

    components: {
        PlayerConfigurator
    },

    props: {
        feedId: String,
        feeds: {
            required: true,
            type: Array
        },
        username: {
            type: String,
            required: true,
        },
        playerBaseUrl: {
            type: String,
            required: true,
        },
        canEmbed: {
            type: Boolean,
            required: false,
            default: true
        },
        canCreatePlayerConfigurations: {
            type: Boolean,
            required: false,
            default: false
        },
        canUseCustomPlayerStyles: {
            type: Boolean,
            required: false,
            default: false
        },
        amountPlayerConfigurations: {
            type: [Number, String],
            required: false,
            default: 0
        }
    },

    methods: {
        emitPageInfo() {
            let items = [{
                text: this.feed.attributes.title,
                href: '#/podcast/' + this.feedId,
            },{
                text: this.$t('nav.link_player'),
                href: '#/podcast/' + this.feedId + '/player',
            }];

            let page = {
                header: this.$t('player.header_configurator_channel', {title: this.feed.attributes.title}),
                subheader: this.$t('player.subheader_configurator_channel'),
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
