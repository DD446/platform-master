<template>
    <div>
        <nav aria-label="breadcrumb" role="navigation" class="bg-primary text-white">
            <div class="row">
                <div class="col ml-3">
                    <b-breadcrumb :items="items"></b-breadcrumb>
                </div>
            </div>
        </nav>

        <section class="bg-info text-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-10">
                        <h1 class="h2 mb-2">{{ pageHeader }} <channel-count username="username" v-show="$route.path === '/'"></channel-count></h1>
                        <span>{{ pageSubheader }}</span>
                    </div>
                    <div class="col-2" v-if="$route.name === 'add-show'">
                        <div class="bg-white p-2 rounded d-flex justify-content-around field-wrap">
<!--                            <a href="" v-b-popover.hover.top="$t('help.popover_general')">
                                <img src="/images1/help/hilfe.png" :alt="$t('help.')" class="">
                            </a>-->
                            <a href="https://www.podcaster.de/hilfe/video/2-episode-veroffentlichen"
                               onclick="window.open(this.href, 'help','width=1300,height=750,top=15,left=15,scrollbars=yes');return false;"
                               v-b-popover.hover.top="$t('help.popover_video')">
                                <img src="/images1/help/hilfe-video.png" :alt="$t('help.alt_video')">
                            </a>
                            <a href="https://www.podcaster.de/faq/antwort-90-wie-erstelle-und-veroeffentliche-ich-eine-episode"
                               class="d-none d-sm-block"
                               onclick="window.open(this.href, 'help','width=1300,height=750,top=15,left=15,scrollbars=yes');return false;"
                               v-b-popover.hover.top="$t('help.popover_faq')">
                                <img src="/images1/help/hilfe-faq.png" :alt="$t('help.alt_faq')" class="">
                            </a>
                        </div>
                    </div>

                    <div class="col-2" v-if="$route.name === 'Submit'">
                        <div class="bg-white p-2 rounded d-flex justify-content-around field-wrap">
                            <a href="https://www.podcaster.de/hilfe/video/4-anmelde-werkzeug"
                               onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;"
                               v-b-popover.hover.top="$t('help.popover_video')">
                                <img src="/images1/help/hilfe-video.png" :alt="$t('help.alt_video')">
                            </a>
                            <a href="https://www.podcaster.de/faq/antwort-89-wie-veroeffentliche-ich-meinen-podcast-auf-podcast-portalen-und-streamingdiensten"
                               class="d-none d-sm-block"
                               onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;"
                               v-b-popover.hover.top="$t('help.popover_faq')">
                                <img src="/images1/help/hilfe-faq.png" :alt="$t('help.alt_faq')" class="">
                            </a>
                        </div>
                    </div>

                    <div class="col-2" v-if="$route.name === 'Player'">
                        <div class="bg-white p-2 rounded d-flex justify-content-around field-wrap">
                            <a href="https://www.podcaster.de/faq/antwort-94-wie-konfiguriere-ich-den-player-zum-einbinden-in-meine-website"
                               class="d-none d-sm-block"
                               onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;"
                               v-b-popover.hover.top="$t('help.popover_faq')">
                                <img src="/images1/help/hilfe-faq.png" :alt="$t('help.alt_faq')" class="">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="p-2 pt-4 bg-white">

            <alert-container></alert-container>

            <b-overlay :show="isLoadingChannels" rounded="sm">
                <b-row>
                    <b-col offset-lg="2" lg="8" md="12" sm="12">
                        <router-view
                            :feeds="feeds"
                            :logoUploadUrl="logoUploadUrl"
                            :playerBaseUrl="playerBaseUrl"
                            :username="username"
                            :uuid="uuid"
                            :canEmbed="canEmbed"
                            :hasAuphonic="hasAuphonic"
                            :canSchedulePosts="canSchedulePosts"
                            :canCreatePlayerConfigurations="canCreatePlayerConfigurations"
                            :canUseCustomPlayerStyles="canUseCustomPlayerStyles"
                            :amountPlayerConfigurations="amountPlayerConfigurations"
                            :customDomainFeature="customDomainFeature"></router-view>
                    </b-col>
                </b-row>

                <empty-channel-list
                    v-if="feeds.length === 0"
                    v-show="!isLoading && !hasError"></empty-channel-list>
            </b-overlay>

            <server-error v-show="hasError"></server-error>

            <section v-if="$route.path === '/' && feeds.length > 0">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-12 col-12 mb-3">
                            <a href="/podcast/wizard" class="btn btn-outline-primary"
                               v-b-popover.hover.auto="$t('feeds.popover_create_channel')">
                                <i class="icon-add-to-list display-4 opacity-20"></i>
                                {{ $t('feeds.link_create_feed') }}
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
import EmptyChannelList from "./EmptyChannelList";
import Channel from "./Channel";
import ServerError from "../ServerError";
import {eventHub} from "../../app";
import AlertContainer from "../AlertContainer";

const defaultItems = [
    {
        text: 'Start',
        href: '/'
    },
    {
        text: 'Podcasts',
        href: '#/'
    },
];

export default {
    name: "Channels",

    components: {
        EmptyChannelList,
        Channel,
        ServerError,
        AlertContainer
    },

    props: {
        "logoUploadUrl": {
            type: String,
            required: true
        },
        "playerBaseUrl": {
            type: String,
            required: true
        },
        "username": {
            type: String,
            required: true
        },
        "canEmbed": {
            type: Boolean,
            required: false,
            default: false
        },
        "hasAuphonic": {
            type: Boolean,
            required: false,
            default: false
        },
        "customDomainFeature": {
            type: Object,
            required: true
        },
        "canSchedulePosts": {
            type: Boolean,
            required: false,
            default: false
        },
        "canCreatePlayerConfigurations": {
            type: Boolean,
            required: false,
            default: false
        },
        "canUseCustomPlayerStyles": {
            type: Boolean,
            required: false,
            default: false
        },
        "amountPlayerConfigurations": {
            type: [Number, String],
            required: false,
            default: 0
        },
        "uuid": {
            type: String,
            required: true
        }
    },

    data() {
        return {
            isLoading: true,
            isLoadingChannels: true,
            hasError: false,
            feeds: [],
            items: defaultItems,
            pageHeader: this.$t('feeds.page_header_podcasts'),
            pageSubheader: this.$t('feeds.page_subheader_podcasts'),
        }
    },

    methods: {
        getFeeds() {
            axios.get('/api/feeds')
                .then((response) => {
                    this.feeds = response.data.data;
                    this.isLoading = false;
                })
                .catch(error => {
                    if (error.response && error.response.status === 500) {
                        error.message = error.response.data.message;
                    }
                    this.showError(error);
                    this.isLoading = false;
                    this.hasError = true;
                }).then(() => {
                    this.isLoadingChannels = false;

/*                    if (this.feeds.length === 1) {
                        this.$router.push('/podcast/' + this.feeds[0].id);
                    }*/
                });
        },
    },

    mounted() {
        this.getFeeds();

        eventHub.$on("update:channels", () => {
            this.isLoadingChannels = true;
            this.getFeeds();
        });

        eventHub.$on("podcasts:page:infos", (breadcrumb, page) => {
            this.items = defaultItems.concat(breadcrumb);
            this.pageHeader = page.header;
            this.pageSubheader = page.subheader;
        });
    },

    // and when route changes
    watch: {
        $route: function (o) {
            if(o.path === '/') {
                this.items = defaultItems;
                this.pageHeader = this.$t('feeds.page_header_podcasts');
                this.pageSubheader = this.$t('feeds.page_subheader_podcasts');
            }
        }
    },
}

</script>
