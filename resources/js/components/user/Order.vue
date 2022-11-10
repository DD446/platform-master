<template>
    <validation-observer ref="observer" v-slot="{ invalid, valid, validated, passes, reset }">
        <b-form ref="form" @submit.stop.prevent="passes(onSubmit)">
            <b-form-group
                :label="$t('login.label_email')">
                <validation-provider
                    vid="email"
                    :rules="{ required: true, max: 255, email: true }"
                    v-slot="validationContext"
                    :name="$t('package.placeholder_email')"
                >
                <b-input
                    type="email"
                    required
                    :state="getValidationState(validationContext)"
                    :placeholder="$t('package.placeholder_email')"
                    v-model="model.email"></b-input>
                </validation-provider>
            </b-form-group>

<!--            <b-form-group :label="$t('login.label_country')">
                <validation-provider
                    vid="country"
                    :rules="{ required: true }"
                    v-slot="validationContext"
                    :name="$t('audiotakes.label_country')"
                >
                    <b-select
                        required
                        :state="getValidationState(validationContext)"
                        v-model="country"
                        :options="countries"
                    ></b-select>
                    <b-form-invalid-feedback
                        id="input-country-feedback">{{ validationContext.errors[0] }}
                    </b-form-invalid-feedback>
                </validation-provider>
            </b-form-group>-->

            <b-form-group class="mt-3">
                <validation-provider
                    vid="terms"
                    :rules="{ required: { allowFalse: false }, is: 'yes' }"
                    v-slot="validationContext"
                >
                    <b-checkbox
                        required
                        name="terms"
                        value="yes"
                        unchecked-value=false
                        v-model="model.terms"
                        :state="getValidationState(validationContext)"
                    >
                        <span v-html="termsLabel()"></span>
                    </b-checkbox>
                    <b-form-invalid-feedback
                        id="input-terms-live-feedback">{{ validationContext.errors[0] }}
                    </b-form-invalid-feedback>
                </validation-provider>
            </b-form-group>

            <b-button
                type="submit"
                variant="success"
                size="lg"
                class="btn-block"
                :disabled="isLoading">{{ $t('package.button_create_account') }}</b-button>
        </b-form>
    </validation-observer>
</template>

<script>

import CountrySelector from "../CountrySelector";
import {eventHub} from "../../app";
import { ValidationProvider, ValidationObserver, extend, localize } from 'vee-validate';
import * as rules from "vee-validate/dist/rules";
import de from 'vee-validate/dist/locale/de.json';
localize('de', de);
Object.keys(rules).forEach(rule => {
    extend(rule, rules[rule]);
});

export default {
    name: "Order",

    components: {
        CountrySelector,
        ValidationProvider,
        ValidationObserver,
    },

    data () {
        return {
            model: {
                id: this.id,
                email: null,
                terms: false,
            },
/*            country: 'de',
            countries: [
                {value: null, text: 'Land des Wohnsitzes auswählen', disabled: true},
                {value: 'de', text: 'Deutschland'},
            ]*/
            isLoading: false
        }
    },

    methods: {
        onSubmit() {
            this.isLoading = true;
            this.errors = [];
            window.scrollTo(0,650);

            this.isLoading = true;
            axios.put('/paket/' + this.id + '/bestellen', this.model)
                .then((response) => {
                    this.showMessage(response);
                    window.location = '/bestellung/hinweis';
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
        termsLabel() {
            return this.$t('package.legal_text_links', { 'terms' : this.termsUrl, 'privacy' : this.privacy });
        }
    },

    props: {
        id: {
            required: true
        },
        termsUrl: {
            type: String,
            required: true
        },
        privacy: {
            type: String,
            required: true
        }
    }
}
</script>

<style scoped>

</style>
