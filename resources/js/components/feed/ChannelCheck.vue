<template>
    <div class="container">

        <div class="text-center">
            <div class="spinner-grow m-5" role="status" v-if="false">
                <span class="sr-only">@lang('feeds.text_loading_data')</span>
            </div>
        </div>

        <websocket-connection-check></websocket-connection-check>

        <state-check :feed="feedId" :uuid="uuid"></state-check>

        <section>
            <h6>Legende</h6>
            <p>Die Tests sind teilweise voneinander abhängig. Fehler von oben nach unten beheben.</p>
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item">
                    <b-spinner class="ml-auto" label="Daten werden geladen" v-b-popover.click.hover="'Wenn ich nach 10 Sekunden neben den Tests noch sichtbar bin, lade die Seite erneut.'" /> Ich lade!
                </li>
                <li class="list-group-item">
                    <b-badge class="p-1" pill variant="success" v-b-popover.click.hover="'Ich bin die Gute!'"><i class="icon-check"></i></b-badge>  Alles ist gut!
                </li>
                <li class="list-group-item">
                    <b-badge class="p-1" pill variant="warning" v-b-popover.click.hover="'Ich bin neutral.'"><i class="icon-bell"></i></b-badge>  Wirf besser einen Blick drauf!
                </li>
                <li class="list-group-item">
                    <b-badge class="p-1" pill variant="danger" v-b-popover.click.hover="'Ich bin der Böse!'"><i class="icon-flag"></i></b-badge>  Oh, oh. Da musst du ran!
                </li>
            </ul>
            <p class="pt-2 text-muted font-weight-lighter">Wenn du über ein Icon fährst, erhältst du einen Hinweis. Mit Klick auf das Icon bleibt der Hinweis angezeigt. Probier es aus!</p>
        </section>
    </div>
</template>

<script>
import StateCheck from "./StateCheck";
import WebsocketConnectionCheck from "../WebsocketConnectionCheck";
import {eventHub} from "../../app";

export default {
    name: "ChannelCheck",

    components: {
        WebsocketConnectionCheck,
        StateCheck
    },

    data() {
        return {
            feed: null,
        }
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
                href: '#/podcast/' + this.feedId + '/status',
            }];

            let page = {
                header: this.$t('feeds.header_state', {feed: this.feed.attributes.title}),
                subheader: this.$t('feeds.subheader_state'),
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
