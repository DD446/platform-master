<template>
    <b-modal
        ref="editLinkModal"
        :title="modalTitle"
        :ok-title="okTitle"
        :ok-disabled="okDisabled"
        :cancel-title="$t('feeds.title_button_abort')"
        @ok="okButtonClicked($event)"
        @show="resetModal"
        @hidden="resetModal"
        @cancel="resetModal"
        size="lg"
        lazy>
        <template #modal-title>
            <div class="d-flex">
                <div>
                    <span v-text="modalTitle"></span>
                </div>
                <div class="pl-1">
                    <a href="https://www.podcaster.de/hilfe/video/5-feed-adresse-basis-url-andern"
                       onclick="window.open(this.href, 'help','width=1300,height=750,top=15,left=15,scrollbars=yes');return false;"
                       v-b-popover.hover.top="$t('help.popover_video')">
                        <img src="/images1/help/hilfe-video.png" :alt="$t('help.alt_video')">
                    </a>
                </div>
            </div>
        </template>
        <alert-container></alert-container>
        <b-overlay :show="isLoading" rounded="sm">
            <b-form v-show="step < 2">
                <b-form-group
                    :label="$t('feeds.label_domain_kind')"
                >
                    <b-form-radio-group
                        required
                        v-model="selected"
                        name="type"
                        :options="domainTypeOptions"
                        size="lg"
                        class="mt-3"
                        button-variant="outline-primary">
                    </b-form-radio-group>
                </b-form-group>

                <p class="alert alert-warning text-sm-center" v-if="!isCustomDomainAvailable">
                    <b-card-text v-html="$t('feeds.warning_feature_custom_domains_not_available', {link: packageRoute})"></b-card-text>
                </p>
            </b-form>

            <b-form v-if="step === 2">

<!--                <b-form-group>
                    <b-form-radio-group
                        required
                        v-model="existsornew"
                        name="type"
                        :options="existsornewOptions"
                        buttons
                        button-variant="outline-primary">
                    </b-form-radio-group>
                </b-form-group>-->

                <b-form-group
                    id="fieldset-1"
                    :label="$t('feeds.label_enter_domain')"
                    label-for="input-domain"
                    :invalid-feedback="invalidFeedbackDomain"
                    :state="stateDomain"
                    v-show="existsornew === 'new'"
                >
                    <b-input-group
                        prepend="https://"
                        class="mt-3">
                        <b-input
                            id="input-domain"
                            :state="stateDomain"
                            autofocus
                            required
                            trim
                            debounce="250"
                            :formatter="formatter"
                            lazy-formatter
                            :placeholder="placeholderDomain"
                            v-model="domain"></b-input>
                        <!--
                        TODO: pattern="\w+(?:\.\w\w)?\.\w+$"
                         Must match: domain die.domain je.de.domain an-dere an-dere-domain die.an-dere-domain
                        -->

                        <template v-slot:append>
                            <b-overlay :show="isLoadingTlds" rounded="sm">
                                <b-select
                                    required
                                    :options="toplevels"
                                    v-model="tld"></b-select>
                            </b-overlay>
                        </template>
                    </b-input-group>
                </b-form-group>

                <domain-selector
                    :feed-id="feedId"
                    :type="selected"
                    v-show="existsornew === 'exists'"></domain-selector>
            </b-form>

            <b-form v-if="step === 3">
                <p>{{$t('feeds.confirm_url_changes')}}</p>
                <b-spinner
                    type="border"
                    :label="$t('feeds.loading_confirm_url_changes')"
                    v-show="isLoadingChanges"></b-spinner>
                <b-list-group v-show="!isLoadingChanges">
                    <b-list-group-item class="flex-column align-items-start" v-for="(change, key) in changes" :key="key">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{change.title}}</h5>
                        </div>
                        <p class="alert alert-warning">
                            <strong>{{ $t('feeds.podcast_feed') }}</strong>

                            {{change.feed.old}} <span class="font-weight-light">{{$t('feeds.confirm_url_before')}}</span>

<!--                            <b-icon-arrow-right></b-icon-arrow-right>-->
                            &rarr;

                            {{change.feed.new}} <span class="font-weight-light">{{ $t('feeds.confirm_url_after') }}</span>
                        </p>
                        <p v-if="change.website" class="alert alert-warning">
                            <strong>{{ $t('feeds.website') }}</strong>

                            {{change.website.old}} <span class="font-weight-light">{{$t('feeds.confirm_url_before')}}</span>

                            <!--                            <b-icon-arrow-right></b-icon-arrow-right>-->
                            &rarr;

                            {{change.website.new}} <span class="font-weight-light">{{ $t('feeds.confirm_url_after') }}</span>
                        </p>
                    </b-list-group-item>
                    <b-list-group-item>
    <!--                    <b-checkbox>
                            {{$t('feeds.url_change_add_redirect')}}
                        </b-checkbox>-->
                    </b-list-group-item>
                </b-list-group>
            </b-form>

        </b-overlay>
    </b-modal>
</template>

<script>
    import {eventHub} from '../../app';
    import DomainSelector from "./DomainSelector";

    export default {
        name: "EditLinkModal",

        components: {
            DomainSelector
        },

        data() {
            return {
                canSave: false,
                selected: null,
                isCustomDomainAvailable: false,
                customDomainsUsed: 0,
                customDomainsTotal: 0,
                tlds: [],
                userdomains: [],
                hostdomains: [],
                domain: null,
                tld: null,
                step: 1,
                isLoading: false,
                isLoadingChanges: true,
                isLoadingTlds: true,
                changes: [],
                existsornew: 'new',
                domainTypeOptions: [
                    { text: this.$t('feeds.label_domain_of_service'), value: 'local' },
                    { text: this.$t('feeds.label_custom_domain') + ' (' + this.customDomainFeature.used + '/' + this.customDomainFeature.total + ')', value: 'custom', disabled: !Boolean(this.customDomainFeature.total) },
                ],
                existsornewOptions: [
                    { text: this.$t('feeds.label_domain_new'), value: 'new' },
                    /*{ text: this.$t('feeds.label_domain_used'), value: 'exists' },*/
                ],
            }
        },

        props: {
            feedId: {
                type: String,
                required: true,
            },
            customDomainFeature: {
                type: Object,
                required: true,
            },
            packageRoute: {
                type: String,
                required: false,
                default: '/pakete'
            }
        },

        methods: {
            show() {
                this.$refs.editLinkModal.show();
            },
            resetModal() {
                this.selected = this.domain = null;
                this.isLoadingChanges = this.isLoadingTlds = this.isLoading = this.canSave = false;
                this.tld = 'de';
                this.step = 1;
                this.existsornew = 'new';
            },
            okButtonClicked(event) {
                switch (this.step) {
                    case 1:
                        switch (this.selected) {
                            case 'local':
                                this.domain = null;
                                this.tld = 'podcaster.de';
                                break;
                            case 'custom':
                                this.domain = null;
                                this.tld = 'de';
                        }
                        ++this.step;
                    break;
                    case 2:
                        this.checkDomain();
                        break;
                    case 3:
                        this.isLoading = true;
                        ++this.step;
                        this.saveChanges();
                        break;
                }

                if (this.step < 4) {
                    event.preventDefault();
                }
                return true;
            },
            getTlds() {
                this.isLoadingTlds = true;
                axios.get('/beta/domains/tlds')
                    .then(response => {
                        let aTld = response.data;
                        let placeholder = this.$t('feeds.placeholder_edit_link_tld');
                        aTld.unshift({value: null, text: placeholder});
                        this.tlds = aTld;
                    })
                    .catch(error => {
                        // handle error
                        this.showError(error);
                    }).then(() => {
                        this.isLoadingTlds = false;
                    });
            },
            checkDomain() {
                this.isLoading = true;
                axios.post('/beta/domains/check', {domain: this.domain, tld: this.tld, is_custom: this.selected === 'custom'})
                    .then(response => {
                        this.$emit(response.data.message);
                    })
                    .catch(error => {
                        // handle error
                        this.showError(error);
                    }).then(() => {
                        this.isLoading = false;
                    });
            },
            getChanges() {
                this.isLoadingChanges = true;
                axios.post('/beta/domains/changes', {feedId: this.feedId, domain: this.domain, tld: this.tld, is_custom: this.selected === 'custom'})
                    .then(response => {
                        this.changes = response.data;
                    })
                    .catch(error => {
                        // handle error
                        this.showError(error);
                    }).then(() => {
                        this.isLoading = false;
                        this.isLoadingChanges = false;
                    });
                //this.isLoading = false;
            },
            saveChanges() {
                this.isLoadingChanges = true;

                axios.put('/beta/domains/changes', {feed_id: this.feedId, domain: this.domain, tld: this.tld, is_custom: this.selected === 'custom'})
                    .then(response => {
                        eventHub.$emit('usage:refresh:' + this.feedId);
                        //this.changes = response.data;
                        this.showMessage(response);
                        this.$bvModal.hide('edit-link-modal');
                    })
                    .catch(error => {
                        // handle error
                        this.showError(error);
                        --this.step;
                    }).then(() => {
                        this.isLoading = false;
                        this.isLoadingChanges = false;
                    });
            },
            formatter(value) {
                return value.toLowerCase()
            },
        },

        mounted() {
            this.isCustomDomainAvailable = Boolean(this.customDomainFeature.total);
            this.customDomainsUsed = this.customDomainFeature.used;
            this.customDomainsTotal = this.customDomainFeature.total;

            this.getTlds();

            eventHub.$on("edit-link-modal:show:" + this.feedId, () => {
                this.show();
            });

            eventHub.$on("domains:fetched:" + this.feedId, (/*domain, */tlds, hostdomains) => {
                //this.domain = domain.stripped_subdomain;
                //this.tld = domain.domain;
                this.userdomains = tlds;
                this.hostdomains = hostdomains;
                this.isLoadingTlds = false;
            });

            eventHub.$on("domain:selected:" + this.feedId, (domain) => {
                this.domain = domain.stripped_subdomain;
                this.tld = domain.domain;
            });

            eventHub.$on("domain:deselected:" + this.feedId, () => {
                this.domain = this.tld = null;
            });

            this.$on('checkPassed', () => {
                this.getChanges();
                ++this.step;
            });
        },

        computed: {
            okDisabled() {
                switch (this.step) {
                    case 1:
                        return !this.selected;
                    case 2:
                        return !this.domain || !this.tld;
                    case 3:
                        return this.isLoadingChanges;
                }
            },
            okTitle() {
                switch (this.step) {
                    case 1:
                        return this.$t('feeds.button_first_step_change_url');
                    case 2:
                        return this.$t('feeds.button_second_step_change_url')
                    case 3:
                        return this.$t('feeds.button_third_step_change_url');
                }
            },
            toplevels() {
                switch (this.selected) {
                    case 'local':
                        return this.hostdomains;
                    case 'custom':
                        return this.tlds;
                }
            },
            placeholderDomain() {
                switch (this.selected) {
                    case 'custom':
                        return this.$t('feeds.placeholder_edit_link_domain');
                    default:
                        return this.$t('feeds.placeholder_edit_link_subdomain');
                }

            },
            modalTitle() {
                let title = this.$t('feeds.title_change_url');
                switch (this.selected) {
                    case 'local':
                        return title + ': ' + this.$t('feeds.label_domain_of_service');
                    case 'custom':
                        return title + ': ' + this.$t('feeds.label_custom_domain');
                    default:
                        return title;
                }
            },
            minLengthDomain() {
                if (this.selected === 'local') {
                    return 2;
                }
                return 3;
            },
            stateDomain() {
                if (!this.domain) {
                    return false;
                }
                return this.domain.length >= this.minLengthDomain;
            },
            invalidFeedbackDomain() {
                if (this.domain && this.domain.length >= this.minLengthDomain) {
                    return ''
                } else if (this.domain && this.domain.length > 0) {
                    if (this.selected === 'local') {
                        return this.$t('feeds.feedback_minlength_subdomain', {minlength: this.minLengthDomain});
                    }
                    return this.$t('feeds.feedback_minlength_domain', {minlength: 1});
                } else {
                    if (this.selected === 'local') {
                        return this.$t('feeds.feedback_enter_subdomain');
                    }
                    return this.$t('feeds.feedback_enter_subdomain_and_domain');
                }
            },
/*            validFeedbackDomain() {
                return this.state === true ? 'Thank you' : ''
            },*/
            customDomainLabel() {
                return this.$t('feeds.label_custom_domain') + ' (<span v-text="customDomainsUsed"></span>/<span v-text="customDomainsTotal"></span>' + this.$t("feeds.hint_custom_domain_count") + ')';
            }
        }
    }
</script>

<style scoped>
</style>
