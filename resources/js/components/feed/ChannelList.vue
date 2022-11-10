<template>
    <div>
        <b-overlay :show="isLoadingChannels" rounded="sm">
            <b-row>
                <b-col offset-xl="2" offset-lg="2" xl="8" lg="8" md="12" sm="12">
                    <channel v-for="feed of feeds"
                        :oFeed="feed"
                        :key="feed.id"
                        :logoUploadUrl="logoUploadUrl"
                        :username="username"
                        :canEmbed="canEmbed"
                        :hasAuphonic="hasAuphonic"
                         :custom-domain-feature="customDomainFeature"
                    ></channel>
                </b-col>

                <b-col xl="2" lg="1" md="12" sm="12" v-if="feeds.length > 1" v-b-scrollspy>
                    <b-list-group class="bd-toc">
                        <b-list-group-item v-for="feed of feeds" :key="feed.id">
                            <b-link :href="'#podcast-' + feed.id">
                                {{ feed.attributes.title }}
                            </b-link>
                        </b-list-group-item>
                    </b-list-group>
                </b-col>
            </b-row>
            <empty-channel-list
                    v-if="feeds.length === 0"
                    v-show="!isLoading && !hasError"></empty-channel-list>
        </b-overlay>

        <server-error v-show="hasError"></server-error>
    </div>
</template>

<script>
    import Channel from "./Channel";
    import {eventHub} from '../../app';
    import EmptyChannelList from "./EmptyChannelList";
    import ServerError from "../ServerError";

    export default {
        name: "channel-list",

        components: {
            EmptyChannelList,
            Channel,
            ServerError,
        },

        props: {
            "logoUploadUrl": {
                type: String,
            },
            "username": {
                type: String,
            },
            "canEmbed": {
                type: [Boolean, String],
            },
            "hasAuphonic": {
                type: [Boolean, String],
            },
            "customDomainFeature": {
                type: Object,
            }
        },

        data() {
            return {
                isLoading: true,
                isLoadingChannels: false,
                hasError: false,
                feeds: []
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
                    });
            },
        },

        mounted() {
            this.getFeeds();

            eventHub.$on("update:channels", () => {
                this.isLoadingChannels = true;
                this.getFeeds();
            });
        },
    }
</script>

<style scoped>

</style>
