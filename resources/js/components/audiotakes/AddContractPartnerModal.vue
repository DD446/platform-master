<template>

    <b-modal
        ref="addContractPartnerModal"
        :title="$t('audiotakes.header_add_contract_partner')"
        :ok-title="$t('audiotakes.button_add_contract_partner')"
        :cancel-title="$t('audiotakes.button_cancel_add_contract_partner')"
        lazy
        size="lg"
        @ok="handleOk">
        <validation-observer ref="observer" v-slot="{ invalid, valid, validated, passes, reset }">
            <b-form
                ref="form"
                @submit.stop.prevent="handleSubmit"
                @keydown="form.onKeydown($event)">
                <div class="row mt-3">
                    <b-col cols="12" class="mt-1">
                        <h4>{{ $t('audiotakes.label_contract_partner') }}</h4>
                    </b-col>
                    <b-col cols="12" class="mt-3">
                        <b-form-group v-slot="{ ariaDescribedby }">
                            <b-form-radio-group
                                id="radio-group-2"
                                v-model="form.contract_partner"
                                :aria-describedby="ariaDescribedby"
                                name="radio-sub-component"
                            >
                                <b-form-radio value="private">{{ $t('audiotakes.contract_partner_private') }}</b-form-radio>
                                <b-form-radio value="corporate">{{ $t('audiotakes.contract_partner_corporate') }}</b-form-radio>
                            </b-form-radio-group>
                        </b-form-group>
                    </b-col>
                </div>

                <div class="row mt-1">
                    <b-col cols="12" class="mt-1">
                        <h4>{{ $t('audiotakes.header_contact_person') }}</h4>
                    </b-col>
                    <b-col cols="12" md="6" class="mt-3">
                        <label for="first_name">{{ $t('audiotakes.first_name') }}</label>
                        <validation-provider
                            vid="first_name"
                            :rules="{ required: true, max: 255 }"
                            v-slot="validationContext"
                            :name="$t('audiotakes.first_name')"
                        >
                            <b-input
                                id="first_name"
                                v-model="form.user.first_name"
                                autofocus
                                required
                                :state="getValidationState(validationContext)"
                                :placeholder="$t('audiotakes.placeholder_first_name')"
                            ></b-input>
                            <b-form-invalid-feedback
                                id="input-first_name-live-feedback">{{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </validation-provider>
                    </b-col>
                    <b-col cols="12" md="6" class="mt-3">
                        <label for="last_name">{{ $t('audiotakes.last_name') }}</label>
                        <validation-provider
                            vid="last_name"
                            :rules="{ required: true, max: 255 }"
                            v-slot="validationContext"
                            :name="$t('audiotakes.last_name')"
                        >
                            <b-input
                                id="last_name"
                                v-model="form.user.last_name"
                                required
                                :state="getValidationState(validationContext)"
                                :placeholder="$t('audiotakes.placeholder_last_name')"
                            ></b-input>
                            <b-form-invalid-feedback
                                id="input-last_name-live-feedback">{{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </validation-provider>
                    </b-col>
                    <b-col cols="12" md="6" class="mt-3">
                        <label for="email">{{ $t('audiotakes.email') }}</label>
                        <b-input
                            id="email"
                            v-model="form.user.email"
                            :placeholder="$t('audiotakes.placeholder_email')"
                        ></b-input>
                    </b-col>
                    <b-col cols="12" md="6" class="mt-3">
                        <label for="telephone">{{ $t('audiotakes.telephone') }}</label>
                        <b-input
                            id="telephone"
                            v-model="form.user.telephone"
                            :placeholder="$t('audiotakes.placeholder_telephone')"
                        ></b-input>
                    </b-col>
                </div>

                <div class="row mt-1" v-show="form.contract_partner === 'corporate'">
                    <b-col cols="12" class="mt-3">
                        <h4>{{ $t('audiotakes.header_organisation') }}</h4>
                    </b-col>
                    <b-col cols="12" md="6" class="mt-3">
                        <label for="organisation">{{ $t('audiotakes.organisation_name') }}</label>
                        <b-input
                            id="organisation"
                            v-model="form.user.organisation"
                            :required="form.contract_partner==='corporate'"
                            :placeholder="$t('audiotakes.placeholder_organisation')"
                        ></b-input>
                    </b-col>
                    <b-col cols="12" md="6" class="mt-3">
                        <label for="vatid">{{ $t('audiotakes.vat_id') }}</label>
                        <b-input
                            id="vatid"
                            v-model="form.user.vat_id"
                            :placeholder="$t('audiotakes.placeholder_vat_id')"
                        ></b-input>
                    </b-col>
                </div>

                <div class="row">
                    <b-col cols="12" class="mt-3">
                        <h4>{{ $t('audiotakes.header_address') }}</h4>
                    </b-col>
                    <b-col cols="12" md="6" class="mt-3">
                        <label for="street">{{ $t('audiotakes.street') }}</label>
                        <validation-provider
                            vid="street"
                            :rules="{ required: true, max: 255 }"
                            v-slot="validationContext"
                            :name="$t('audiotakes.street')"
                        >
                            <b-input
                                id="street"
                                required
                                v-model="form.user.street"
                                :state="getValidationState(validationContext)"
                                :placeholder="$t('audiotakes.placeholder_street')"
                            ></b-input>
                            <b-form-invalid-feedback
                                id="input-street-live-feedback">{{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </validation-provider>
                    </b-col>
                    <b-col cols="12" md="6" class="mt-3">
                        <label for="housenumber">{{ $t('audiotakes.housenumber') }}</label>
                        <validation-provider
                            vid="housenumber"
                            :rules="{ required: true, max: 10 }"
                            v-slot="validationContext"
                            :name="$t('audiotakes.housenumber')"
                        >
                            <b-input
                                id="housenumber"
                                required
                                v-model="form.user.housenumber"
                                :state="getValidationState(validationContext)"
                                :placeholder="$t('audiotakes.placeholder_housenumber')"
                            ></b-input>
                            <b-form-invalid-feedback
                                id="input-housenumber-live-feedback">{{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </validation-provider>
                    </b-col>
                </div>
                <div class="row">
                    <b-col cols="12" md="6" class="mt-3">
                        <label for="post_code">{{ $t('audiotakes.post_code') }}</label>
                        <validation-provider
                            vid="post_code"
                            :rules="{ required: true, max: 10 }"
                            v-slot="validationContext"
                            :name="$t('audiotakes.post_code')"
                        >
                            <b-input
                                id="post_code"
                                required
                                v-model="form.user.post_code"
                                :state="getValidationState(validationContext)"
                                :placeholder="$t('audiotakes.placeholder_post_code')"
                            ></b-input>
                            <b-form-invalid-feedback
                                id="input-post_code-live-feedback">{{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </validation-provider>
                    </b-col>
                    <b-col cols="12" md="6" class="mt-3">
                        <label for="city">{{ $t('audiotakes.city') }}</label>
                        <validation-provider
                            vid="city"
                            :rules="{ required: true, max: 50 }"
                            v-slot="validationContext"
                            :name="$t('audiotakes.city')"
                        >
                            <b-input
                                id="city"
                                required
                                v-model="form.user.city"
                                :state="getValidationState(validationContext)"
                                :placeholder="$t('audiotakes.placeholder_city')"
                            ></b-input>
                            <b-form-invalid-feedback
                                id="input-city-live-feedback">{{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </validation-provider>
                    </b-col>
                </div>
                <div class="row">
                    <div class="col-12 mt-4">
                        <label for="country-selector">
                            {{ $t('audiotakes.label_country') }}
                        </label>
                        <validation-provider
                            vid="country"
                            :rules="{ required: true }"
                            v-slot="validationContext"
                            :name="$t('audiotakes.label_country')"
                        >
                            <b-select
                                v-model="form.user.country"
                                :options="countries">
                            </b-select>
                            <b-form-invalid-feedback
                                id="input-country-feedback">{{ validationContext.errors[0] }}
                            </b-form-invalid-feedback>
                        </validation-provider>
                    </div>
                </div>
            </b-form>
        </validation-observer>
    </b-modal>
</template>

<script>

import CountrySelector from "../CountrySelector";
import {eventHub} from "../../app";
import { ValidationProvider, ValidationObserver, extend, localize } from 'vee-validate';
import * as rules from "vee-validate/dist/rules";
import de from 'vee-validate/dist/locale/de.json';
import Form from "vform";
localize('de', de);
Object.keys(rules).forEach(rule => {
    extend(rule, rules[rule]);
});

export default {

    name: "AddContractPartnerModal",

    components: {
        CountrySelector,
        ValidationProvider,
        ValidationObserver,
    },

    data() {
        return {
            form: new Form({
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
                },
                contract_partner: 'private'
            }),
            isLoading: false,
        }
    },

    props: {
        contracts: {
            type: Array,
            required: false
        },
        userdata: {
            type: Object,
            required: false
        },
        countries: {
            type: Array,
            required: true
        }
    },

    methods: {

        show() {
            this.$refs.addContractPartnerModal.show();
        },
        handleOk(bvModalEvt) {
            // Prevent modal from closing
            bvModalEvt.preventDefault();
            this.handleSubmit();
        },
        handleSubmit() {
            // Exit when the form isn't valid
            const valid = this.$refs.form.checkValidity();

            if (!valid) {
                return;
            }
            // Hide the modal manually
            this.$nextTick(() => {
                this.create();
            })
        },
        async create() {
            this.isLoading = true;
            try {
                const response = await this.form.post('/api/contract/partners')
                this.showMessage(response);
                eventHub.$emit('audiotakes-add-contract-partner-modal:added', response.data.id);
                this.form = new Form({
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
                    },
                    contract_partner: 'private'
                });
                this.$refs.addContractPartnerModal.hide();
            } catch (e) {
                this.showError(e);
            }
            this.isLoading = false;
        },
        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },
    },

    mounted() {
        this.form.user = this.userdata;
        eventHub.$on("audiotakes-add-contract-partner-modal:show", () => {
            this.show();
        });
    }
}
</script>

<style scoped>

</style>
