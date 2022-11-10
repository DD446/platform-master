<template>
    <div>
        <div class="row mt-1">
            <b-col cols="12" class="mt-1">
                <h4>{{ $t('audiotakes.header_podcast') }}</h4>
            </b-col>
            <b-col cols="12" class="mt-3">
                <span class="display-4">{{ feedTitle }}</span>
            </b-col>
        </div>

        <b-overlay :show="isLoading" rounded="lg" class="p-0 p-md-3 p-lg-5">
            <validation-observer ref="observer" v-slot="{ invalid, valid, validated, passes, reset }">
                <b-form ref="form" @submit.prevent="onSubmit">
                    <b-col class="row mt-2">
                <b-col cols="12" lg="6">
                    <b-form-group>
                        <template slot="label">
                            <legend class="bv-no-focus-ring col-form-label pt-0">
                                <h4>
                                    {{ $t('audiotakes.label_contract_partners') }}
                                    <b-link
                                        v-if="contractPartners.length>0"
                                        class="btn btn-warning btn-sm"
                                        v-b-popover.hover.righttop="$t('audiotakes.popover_add_contract_partner')"
                                        @click="openPayoutGoalModal">{{ $t('audiotakes.text_create_payout_goal_short') }}</b-link>
                                </h4>
                            </legend>
                        </template>
                        <b-row>
                            <b-col cols="12" md="9">
                                <div class="mt-4">
                                    <b-select
                                        v-if="contractPartners.length>0"
                                        class="custom-select custom-select-lg mb-3"
                                        required
                                        v-model="model.audiotakes_contract_partner_id"
                                        :options="contractPartnerList"></b-select>
                                    <b-button
                                        variant="primary"
                                        v-if="contractPartners.length<1"
                                        @click="openPayoutGoalModal">{{ $t('audiotakes.label_add_contract_partner') }}</b-button>
                                </div>
                            </b-col>
                        </b-row>
                    </b-form-group>

                    <div class="row">
                        <div class="col-12 mt-4">
                            <validation-provider
                                vid="terms_accepted"
                                :rules="{ required: true }"
                                v-slot="validationContext"
                                :name="$t('audiotakes.city')"
                            >
                                <b-checkbox
                                    size="lg"
                                    v-model="model.toc"
                                    name="terms_accepted"
                                    value="yes"
                                    :state="getValidationState(validationContext)"
                                    unchecked-value="no"
                                >{{ $t('audiotakes.label_checkbox_terms') }}</b-checkbox>
                                <b-form-invalid-feedback
                                    id="input-terms_accepted-live-feedback">{{ validationContext.errors[0] }}
                                </b-form-invalid-feedback>
                            </validation-provider>
                        </div>
                    </div>
                    <div class="row">
                        <b-col cols="12" class="mt-3">
                            <b-button
                                type="submit"
                                size="lg"
                                class="float-right"
                                variant="primary"
                                :disabled="model.toc === 'no' || !model.toc || !model.audiotakes_contract_partner_id">{{ $t('audiotakes.text_button_sign_contract') }}</b-button>
                        </b-col>
                    </div>

                </b-col>
                <b-col cols="12" lg="6">
                    <h4>{{ $t('audiotakes.label_contract') }}</h4>
                    <iframe :src="termsSource" style="min-height:500px;height:750px;width:100%" class="mr-0 mr-md-2 mr-lg-3"></iframe>
    <!--                <b-embed type="iframe" :src="termsSource"></b-embed>-->
                </b-col>
            </b-col>
                </b-form>
            </validation-observer>
        </b-overlay>
        <add-contract-partner-modal :userdata="userdata" :countries="countries"></add-contract-partner-modal>
    </div>
</template>

<script>
import {eventHub} from "../../app";
import { ValidationProvider, ValidationObserver, extend, localize } from 'vee-validate';
import * as rules from "vee-validate/dist/rules";
import de from 'vee-validate/dist/locale/de.json';
localize('de', de);
Object.keys(rules).forEach(rule => {
    extend(rule, rules[rule]);
});
import AddContractPartnerModal from "./AddContractPartnerModal";

export default {

    name: "AudiotakesSignUp",

    components: {
        ValidationProvider,
        ValidationObserver,
        AddContractPartnerModal,
    },

    data() {
        return {
            model: {
                audiotakes_contract_partner_id: null,
                feed_id: this.feedId,
                toc: 'no',
            },
            feed_title: null,
            isLoading: false,
            contractPartner: {},
            contractPartners: [],
            contractPartnerList: [
                {
                    value: null,
                    text: this.$t('audiotakes.option_choose_partner'),
                    disabled: true
                }
            ],
            user: {
                first_name: '',
                last_name: '',
                street: '',
                housenumber: '',
                post_code: '',
                city: '',
                email: '',
                country: 'DE',
                organisation: '',
                vat_id: null,
            }
        }
    },

    props: {
        userdata: {
            type: Object,
            required: false
        },
        feedId: {
            type: String,
            required: true,
        },
        feedTitle: {
            type: String,
            required: true,
        },
        countries: {
            type: Array,
            required: true
        }
    },

    methods: {
        onSubmit() {
            this.isLoading = true;
            this.errors = [];
            window.scrollTo(0,650);

            this.isLoading = true;
            axios.post('/audiotakes', this.model)
                .then((response) => {
                    this.showMessage(response);
                    window.location = '/audiotakes';
                })
                .catch((error) => {
                    // In case we do not have a validation error
                    if (typeof error.message !== undefined) {
                        this.showError(error);
                    } else {
                        this.$refs.observer.setErrors(error.response.data.errors);
                    }
                }).then(() => {
                    this.isLoading = false;
                });
        },
        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },
        getContractPartners() {
            this.isLoading = true;
            axios.get('/api/contract/partners')
                .then((response) => {
                    this.contractPartners = response.data;
                    this.contractPartnerList =  [
                        {
                            value: null,
                            text: this.$t('audiotakes.option_choose_partner'),
                            disabled: true
                        }
                    ];
                    let _this = this;
                    this.contractPartners.forEach(function(element) {
                        let txt = '';
                        if (element.organisation) {
                            txt = element.organisation + ', ';
                            if (element.vat_id) {
                                txt += element.vat_id + ', ';
                            }
                        }
                        txt += element.first_name + ' ' + element.last_name + ', ';
                        txt += element.street + ' ' + element.housenumber + ', ';
                        txt += element.post_code + ' ' + element.city;

                        if (element.email) {
                            txt += ', ' + element.email;
                        }

                        if (element.telephone) {
                            txt += ', ' + element.telephone;
                        }

                        _this.contractPartnerList.push({
                            value: element.id,
                            text: txt
                        })
                    });
                })
                .catch((error) => {
                    this.showError(error);
                }).then(() => {
                    this.isLoading = false;
                });
        },
        openPayoutGoalModal() {
            eventHub.$emit("audiotakes-add-contract-partner-modal:show");
        }
    },

    mounted() {
        this.feed_title = this.feedTitle;
        this.getContractPartners();

        eventHub.$on('audiotakes-add-contract-partner-modal:added', id => {
            this.model.audiotakes_contract_partner_id = id;
            window.scrollTo(0,650);
            this.getContractPartners();
        });
    },

    computed: {
        termsSource()
        {
            let params = 'v=' + (new Date().getTime());

            if (typeof this.user != 'undefined') {
                if (this.user.first_name) {
                    params += '&first_name=' + this.user.first_name;
                }

                if (this.user.last_name) {
                    params += '&last_name=' + this.user.last_name;
                }

                if (this.user.street) {
                    params += '&street=' + this.user.street;
                }

                if (this.user.housenumber) {
                    params += '&housenumber=' + this.user.housenumber;
                }

                if (this.user.post_code) {
                    params += '&post_code=' + this.user.post_code;
                }

                if (this.user.city) {
                    params += '&city=' + this.user.city;
                }

                if (this.user.email) {
                    params += '&email=' + this.user.email
                }

                if (this.user.country) {
                    params += '&country=' + this.user.country;
                }

                if (this.user.organisation) {
                    params += '&organisation=' + this.user.organisation;
                }
            }

            if (this.feed_title) {
                params += '&feed_title=' + this.feed_title;
            }

            if (this.model.feed_id) {
                params += '&feed_id=' + this.model.feed_id;
            }

            return '/audiotakes/terms?' + params;
        }
    },

    watch: {
        model: {
            handler(val){
                this.user = this.contractPartners.find(obj => {
                    return obj.id === val.audiotakes_contract_partner_id;
                });
            },
            deep: true
        },
    },
}
</script>

<style scoped>

</style>
