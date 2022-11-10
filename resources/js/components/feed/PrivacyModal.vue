<template>
    <b-modal
        ref="privacyModal"
        :title="$t('feeds.title_settings_privacy')"
        :ok-title="$t('feeds.button_title_save_settings')"
        :cancel-title="$t('feeds.button_cancel')"
        @ok="handleOk"
        @show="resetModal"
        @hidden="resetModal"
        lazy
        size="lg">
        <alert-container></alert-container>

        <div class="spinner-border m-5"
             role="status"
             v-if="loading">
            <span class="sr-only">{{$t('package.text_loading')}}</span>
        </div>

        <b-overlay :show="isLoading">
            <b-card v-show="!loading && can.protection">
                <div v-if="domainIsCustom">
                    <b-form autocomplete="off" ref="privacyForm" @submit.stop.prevent="handleSubmit" v-if="!isProtected">
                        <b-col cols="12">
                            <b-row class="my-3 mx-1">
                                {{ $t('feeds.intro_password_protection') }}
                            </b-row>
                        </b-col>
                        <b-col cols="12">
                            <b-row class="my-1">
                                <b-col sm="3">
                                    <label for="authname">{{ $t('feeds.label_password_protection_authname') }}</label>
                                </b-col>
                                <b-col sm="9">
                                    <b-input-group
                                        :invalid-feedback="$t('feeds.feedback_password_protection_authname')"
                                        :state="authnameState"
                                    >
                                        <b-input
                                            :placeholder="$t('feeds.placeholder_password_protection_authname')"
                                            id="authname"
                                            ref="authname"
                                            type="text"
                                            required
                                            autofocus
                                            autocomplete="off"
                                            :state="authnameState"
                                            v-model="model.authname"></b-input>
                                    </b-input-group>
                                </b-col>
                            </b-row>
                        </b-col>
                        <b-col cols="12">
                            <b-row class="my-1">
                                <b-col sm="3">
                                    <label for="password">{{ $t('feeds.label_password_protection_password') }}</label>
                                </b-col>
                                <b-col sm="9">
                                    <b-input-group
                                        :invalid-feedback="$t('feeds.feedback_password_protection_password')"
                                        :state="passwordState"
                                    >
                                        <b-input
                                            :placeholder="$t('feeds.placeholder_password_protection_password')"
                                            id="password"
                                            type="password"
                                            ref="password"
                                            autocomplete="new-password"
                                            :state="passwordState"
                                            required
                                            v-model="model.password"></b-input>
                                    </b-input-group>
                                </b-col>
                            </b-row>
                        </b-col>
                        <b-col cols="12" class="mt-3">
                            <b-row class="my-1">
                                <b-col sm="12">
                                    <b-input-group
                                        :state="confirmedState"
                                    >
                                        <b-checkbox
                                            v-model="model.confirmed"
                                            value="yes"
                                            unchecked-value="no"
                                            required
                                            ref="confirmed"
                                            :state="confirmedState"
                                        ><span v-html="$t('feeds.label_password_protection_confirmed')"></span></b-checkbox>
                                    </b-input-group>
                                </b-col>
                            </b-row>
                        </b-col>
                    </b-form>

                    <b-form v-if="isProtected">
                        <b-button variant="danger" @click.prevent="removeProtection">{{ $t('feeds.button_label_remove_protection') }}</b-button>
                    </b-form>
                </div>
                <div v-if="!domainIsCustom">
                    <div class="text-center alert-danger m-1 p-4">
                        <b-card-text>
                            <span v-html="$t('feeds.hint_change_domain_name')"></span>
<!--                            <b-button v-b-modal.edit-link-modal>{{ $t('feeds.asd') }}</b-button>-->
                        </b-card-text>
                    </div>
                </div>
            </b-card>
            <b-card v-show="!loading && !can.protection">
                <div class="text-center alert-warning m-1 p-4">
                    <b-card-text v-html="$t('feeds.text_hint_upgrade_package_for_protection_feature', {route: '/pakete'})"></b-card-text>
                </div>
            </b-card>
        </b-overlay>
    </b-modal>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "PrivacyModal",

        data() {
            return {
                model: {
                    feed_id: this.feedId,
                    authname: '',
                    password: '',
                    confirmed: 'no'
                },
                checked: false,
                loading: true,
                isLoading: true,
                isProtected: false,
                can: {
                    protection: false,
                },
                authnameState: null,
                passwordState: null,
                confirmedState: null,
                domainIsCustom: false
            }
        },

        props: {
            feedId: {
                type: String,
                required: true
            }
        },

        methods: {
            show() {
                this.$refs.privacyModal.show();
                this.checkDomain();
                this.getSettings();
            },
            checkDomain() {
                axios.head('/feed/' + this.feedId + '/settings/domain?check=isCustom')
                    .then((response) => {
                        this.domainIsCustom = response.status === 200;
                    }).catch(error => {
                        this.showError(error);
                    }).then(() => {
                    });
            },
            getSettings() {
                axios.get('/feed/' + this.feedId + '/settings')
                    .then((response) => {
                        let settings = response.data || {};
                        this.can.protection = settings.can.protection;
                        this.isProtected = settings.uses_protection === 'yes';
                        this.isLoading = false;
                    }).catch(error => {
                        this.showError(error);
                    }).then(() => {
                        this.loading = false;
                    });
            },
            saveSettings() {
                this.isLoading = true;
                axios.put('/feed/' + this.feedId + '/settings/privacy', this.$data.model)
                    .then((response) => {
                        this.showMessage(response);
                        this.$refs.privacyModal.hide();
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                        this.isLoading = false;
                    });
            },
            checkFormValidity() {
                this.authnameState = this.$refs.authname.checkValidity();
                this.passwordState = this.$refs.password.checkValidity();
                this.confirmedState = this.$refs.confirmed.localChecked === 'yes';
                return this.authnameState && this.passwordState && this.confirmedState;
            },
            resetModal() {
                this.model = {
                    feed_id: this.feedId,
                    authname: '',
                    password: '',
                    confirmed: 'no'
                }
                this.authnameState = null;
                this.passwordState = null;
                this.confirmedState = null;
            },
            handleOk(bvModalEvt) {
                // Prevent modal from closing
                bvModalEvt.preventDefault()
                // Trigger submit handler
                this.handleSubmit()
            },
            handleSubmit() {
                // Exit when the form isn't valid
                if (!this.checkFormValidity()) {
                    return;
                }
                this.saveSettings();

                // Hide the modal manually
/*                this.$nextTick(() => {
                    this.$bvModal.hide('modal-prevent-closing')
                })*/
            },
            removeProtection() {
                this.isLoading = true;
                axios.delete('/feed/' + this.feedId + '/settings/protection')
                    .then((response) => {
                        this.showMessage(response);
                        this.getSettings();
                    }).catch(error => {
                        this.showError(error);
                    }).then(() => {
                        this.isLoading = false;
                    });
            }
        },

        computed: {
            checkboxLabel() {
                if (this.checked) {
                    return this.$t('feeds.password_protection_active');
                }
                return this.$t('feeds.password_protection_inactive');
            }
        },

        mounted() {
            eventHub.$on("privacy-modal:show:" + this.feedId, () => {
                this.show();
            });
        },

        created() {
        }
    }
</script>

<style scoped>
</style>
