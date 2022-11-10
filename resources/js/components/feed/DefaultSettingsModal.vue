<template>
    <b-modal
        ref="defaultSettingsModal"
        :title="$t('feeds.title_settings_defaults')"
        :ok-title="$t('feeds.button_title_save_defaults')"
        :cancel-title="$t('feeds.button_cancel')"
        @ok="saveSettings"
        lazy
        size="lg">
        <alert-container></alert-container>

<!--        <template #modal-title>
            <div class="d-flex">
                <div>
                    <span v-text="$t('feeds.title_settings_defaults')"></span>
                </div>
                <div class="pl-1">
                </div>
            </div>
        </template>-->

        <div class="spinner-border m-5"
             role="status"
             v-if="loading">
            <span class="sr-only">{{$t('package.text_loading')}}</span>
        </div>

        <b-overlay :show="isLoading">
            <b-card v-show="!loading">
                <b-row>
                    <b-col sm="12" class="mt-2">
                        <b-form>
                            <b-form-group
                                :description="$t('feeds.description_default_show_title')"
                                :label="$t('feeds.label_default_show_title')">
                                <b-input type="text" v-model="settings.default_item_title"></b-input>
                            </b-form-group>

                            <b-form-group
                                :description="$t('feeds.description_default_show_description')"
                                :label="$t('feeds.label_default_show_description')">
                                <b-textarea v-model="settings.default_item_description"></b-textarea>
                            </b-form-group>

                            <b-form-group
                                :description="$t('feeds.description_default_download_link')"
                                :label="$t('feeds.label_default_download_link')">
                                <b-checkbox v-model="settings.downloadlink" value="1" unchecked-value="0">
                                    {{ $t('feeds.label_add_download') }}
                                </b-checkbox>
                                <b-textarea
                                    :readonly="settings.downloadlink !== '1'"
                                    v-model="settings.downloadlink_description"></b-textarea>
                            </b-form-group>
                            <b-form-group
                                :description="$t('feeds.description_deactive_blog_comments')"
                            >
                                <b-checkbox v-model="settings.inactiveComments" value="1" unchecked-value="0">
                                    {{ $t('feeds.label_deactive_blog_comments') }}
                                </b-checkbox>
                            </b-form-group>
                            <b-form-group
                                :description="$t('feeds.description_feed_entries')"
                                :label="$t('feeds.label_feed_entries')">
                                <b-select
                                    v-model="settings.feed_entries"
                                    :options="feedEntryOptions"
                                >
                                </b-select>
                            </b-form-group>
                            <b-form-group
                                :description="$t('feeds.description_ping_portals')"
                            >
                                <template #description>
                                    <div class="d-flex">
                                        <div>
                                            <span v-text="$t('feeds.description_ping_portals')"></span>
                                        </div>
                                        <div class="pl-1">
                                            <a href="https://www.podcaster.de/faq/antwort-6-koennen-podcast-portale-automatisch-angepingt-werden-und-was-ist-das"
                                               class="d-none d-sm-block"
                                               onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;"
                                               v-b-popover.hover.top="$t('help.popover_faq')">
                                                <img src="/images1/help/hilfe-faq.png" :alt="$t('help.alt_faq')" class="">
                                            </a>
                                        </div>
                                    </div>
                                </template>
                                <b-checkbox v-model="settings.ping" value="1" unchecked-value="0">
                                    {{ $t('feeds.label_ping_portals') }}
                                </b-checkbox>
                            </b-form-group>
                            <b-form-group
                                :description="$t('feeds.description_styled_feed')"
                            >
                                <b-checkbox v-model="settings.styled_feed" value="1" unchecked-value="0">
                                    {{ $t('feeds.label_styled_feed') }}
                                </b-checkbox>
                            </b-form-group>
                        </b-form>
                    </b-col>
                </b-row>
            </b-card>
        </b-overlay>
    </b-modal>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "DefaultSettingsModal",

        data() {
            return {
                settings: {
                    default_item_title: null,
                    default_item_description: null,
                    downloadlink: 0,
                    downloadlink_description: this.$t('feeds.default_download_link_entry'),
                    inactiveComments: 0,
                    feed_entries: 50,
                    ping: 1,
                    styled_feed: 0,
                },
                loading: true,
                isLoading: false,
                feedEntryOptions: [
                    { value: 1, text: 1 },
                    { value: 10, text: 10 },
                    { value: 20, text: 20 },
                    { value: 30, text: 30 },
                    { value: 40, text: 40 },
                    { value: 50, text: 50 },
                    { value: 100, text: 100 },
                    { value: 150, text: 150 },
                    { value: 200, text: 200 },
                    { value: 250, text: 250 },
                    { value: 500, text: 500 },
                    { value: 1000, text: 1000 },
                    { value: 5000, text: 5000 }
                ]
            }
        },

        props: {
            feedId: String,
        },

        methods: {
            show() {
                this.getSettings();
                this.$refs.defaultSettingsModal.show();
            },
            getSettings() {
                axios.get('/feed/' + this.feedId + '/settings')
                    .then((response) => {
                        this.settings = response.data || {};
                    }).catch(error => {
                        this.showError(error);
                    }).then(() => {
                        this.loading = false;
                    });
            },
            saveSettings(bvModalEvt) {
                bvModalEvt.preventDefault();
                this.isLoading = true;
                axios.put('/feed/' + this.feedId + '/settings/defaults', this.settings)
                    .then((response) => {
                        this.showMessage(response);
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                        this.isLoading = false;
                    });
            },
        },

        mounted() {
            eventHub.$on("default-settings-modal:show:" + this.feedId, () => {
                this.show();
            });
        }
    }
</script>

<style scoped>
</style>
