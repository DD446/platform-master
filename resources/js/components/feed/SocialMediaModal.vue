<template>
    <b-modal
        ref="socialMediaModal"
        :title="$t('feeds.title_settings_social_media')"
        :ok-title="$t('feeds.button_title_save_approvals')"
        :cancel-title="$t('feeds.button_cancel')"
        @ok="saveSettings"
        lazy
        size="lg">
        <alert-container></alert-container>

        <div class="spinner-border m-5"
             role="status"
             v-if="loading">
            <span class="sr-only">{{$t('package.text_loading')}}</span>
        </div>

        <b-overlay :show="isLoading">
            <b-card v-show="!loading">
                <b-tabs card>
                    <b-tab active>
                        <template slot="title">
                            <i class="socicon-twitter"></i>
                            {{ $t('feeds.text_tab_socialmedia_twitter') }}
                        </template>
                        <b-card-body>
                            <b-row>
                                <b-col sm="12" class="mt-sm-1">
                                    <p>{{ $t('feeds.hint_send_tweets') }}</p>

                                    <b-list-group>
                                        <b-list-group-item v-for="(approval) of filteredApprovals('twitter')" :key="approval.id">
                                            <b-checkbox
                                                v-model="feedApprovals.twitter"
                                                :value="approval.id"
                                                size="lg">
                                                {{approval.screen_name}}
                                            </b-checkbox>
                                        </b-list-group-item>

                                        <b-list-group-item v-if="filteredApprovals('twitter').length < 1" class="p-1 p-lg-4">
                                                <span class="alert alert-warning">
                                                    {{ $t('feeds.hint_no_approvals_added', {service: 'Twitter'}) }}
                                                </span>
                                        </b-list-group-item>
                                    </b-list-group>
                                </b-col>
                            </b-row>
                        </b-card-body>
                    </b-tab>
<!--                    <b-tab>
                        <template slot="title">
                            <i class="socicon-auphonic float-left mr-1"></i>
                            {{ $t('feeds.text_tab_socialmedia_auphonic') }}
                        </template>
                        <b-card-body>
                            <div>
                                <div>
                                    <b-row class="mt-4">
                                        <b-col sm="12">
                                            <b-list-group-item v-for="(approval) of filteredApprovals('auphonic')" :key="approval.id">
                                                <b-checkbox
                                                    v-model="feedApprovals.auphonic"
                                                    :value="approval.id"
                                                    size="lg">
                                                    {{approval.screen_name}}
                                                </b-checkbox>
                                            </b-list-group-item>

                                            <b-list-group-item v-if="filteredApprovals('auphonic').length < 1" class="p-4">
                                                <span class="alert alert-info">
                                                    {{ $t('feeds.hint_no_approvals_added') }}
                                                </span>
                                            </b-list-group-item>
                                        </b-col>
                                    </b-row>
                                </div>
                            </div>

                            <b-row class="mt-1 ml-lg-1" v-if="!can.auphonic">
                                <div class="text-center alert-warning m-1 p-4">
                                    <b-card-text v-html="$t('feeds.text_hint_upgrade_package', {route: '/pakete'})"></b-card-text>
                                </div>
                            </b-row>
                        </b-card-body>
                    </b-tab>-->
                </b-tabs>
                <b-link href="/freigaben" class="card-link"><!--<b-icon icon="link"></b-icon>--> 🔗 {{ $t('feeds.link_add_approvals') }}</b-link>
            </b-card>
        </b-overlay>
    </b-modal>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "SocialMediaModal",

        data() {
            return {
                globalApprovals: [],
                feedApprovals: {},
                loading: true,
                isLoading: false,
                can: {
                    auphonic: false,
                    socialmedia: false,
                },
            }
        },

        props: {
            feedId: String,
        },

        methods: {
            show() {
                this.getGlobalApprovals();
                this.getFeedApprovals();
                this.$refs.socialMediaModal.show();
            },
            getGlobalApprovals() {
                axios.get('/api/approvals')
                    .then((response) => {
                        this.globalApprovals = response.data || [];
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                    });
            },
            getFeedApprovals() {
                axios.get('/feed/' + this.feedId + '/settings')
                    .then((response) => {
                        this.feedApprovals = response.data.approvals || {};
                        this.can = response.data.can || {};
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                        this.loading = false;
                    });
            },
            saveSettings(bvModalEvt) {
                bvModalEvt.preventDefault();
                this.isLoading = true;
                axios.put('/feed/' + this.feedId + '/settings/approvals', {approvals: this.feedApprovals})
                    .then((response) => {
                        this.showMessage(response);
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                    this.isLoading = false;
                });
            },
            filteredApprovals(service) {
                return this.globalApprovals.filter(function(o){return o["service"] === service;});
            }
        },

        computed: {
        },

        mounted() {
            eventHub.$on("social-media-modal:show:" + this.feedId, () => {
                this.show();
            });
        },

        created() {
        }
    }
</script>

<style scoped>
</style>
