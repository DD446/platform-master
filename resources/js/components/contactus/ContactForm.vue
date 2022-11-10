<template>
    <div id="cf">
        <validation-observer ref="observer" v-slot="{ passes }">
            <alert-container></alert-container>

            <b-form @submit.stop.prevent="passes(onSubmit)">
                <b-form-group
                    label-for="name"
                    :label="$t('contact_us.name')">
                    <validation-provider
                        :name="$t('contact_us.label_name')"
                        :rules="{ required: true, min: 2, alpha_spaces: true }"
                        v-slot="validationContext"
                    >
                        <b-input
                            v-model="form.name"
                            :placeholder="$t('contact_us.placeholder_name')"
                            size="lg"
                            name="name"
                            id="name"
                            required
                            autofocus
                            :state="getValidationState(validationContext)"
                            aria-describedby="input-name-live-feedback"
                        ></b-input>
                        <b-form-invalid-feedback
                            id="input-name-live-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </validation-provider>
                </b-form-group>

                <b-form-group
                    label-for="email"
                    :label="$t('contact_us.email')">
                    <validation-provider
                        :name="$t('contact_us.label_email')"
                        :rules="{ required: true, min: 6, email: true }"
                        v-slot="validationContext"
                    >
                        <b-input
                            v-model="form.email"
                            :placeholder="$t('contact_us.placeholder_email')"
                            size="lg"
                            name="email"
                            type="email"
                            id="email"
                            required
                            :state="getValidationState(validationContext)"
                            aria-describedby="input-email-live-feedback"
                        ></b-input>
                        <b-form-invalid-feedback
                            id="input-email-live-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </validation-provider>
                </b-form-group>

                <b-form-group
                    label-for="type"
                    :label="$t('contact_us.enquiry_type')">
                    <validation-provider
                        :name="$t('contact_us.label_enquiry_type')"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-select
                            v-model="form.enquiry_type"
                            :options="enquiry_types"
                            required
                            id="type"
                            size="lg"
                            aria-describedby="input-type-live-feedback"
                        >
                            <template v-slot:first>
                                <option disabled>{{$t('contact_us.text_option_enquiry_type_default')}}</option>
                            </template>
                        </b-select>
                        <b-form-invalid-feedback
                            id="input-type-live-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </validation-provider>
                </b-form-group>

                <b-form-group
                    label-for="comment"
                    :label="$t('contact_us.comment')">
                    <validation-provider
                        :name="$t('contact_us.label_comment')"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-textarea
                            class="form-control"
                            id="comment"
                            v-model="form.comment"
                            :placeholder="$t('contact_us.placeholder_comment')"
                            required
                            :state="getValidationState(validationContext)"
                            aria-describedby="input-comment-live-feedback"
                            rows="15"
                            cols="10"
                            max-rows="10"></b-textarea>
                        <b-form-invalid-feedback
                            id="input-comment-live-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </validation-provider>
                </b-form-group>

                <b-row>
                    <b-col sm="12" class="mt-4">
                        <p class="small" v-html="$t('contact_us.text_legal', {terms: privacyRoute})"></p>
                    </b-col>
                </b-row>

                <b-row>
                    <b-col sm="12" class="mt-4">
                        <b-button
                            type="submit"
                            class="btn btn-lg btn-success float-right"
                            :disabled="!passes">{{ $t('contact_us.text_button_send_message') }}</b-button>
                    </b-col>
                </b-row>
            </b-form>
        </validation-observer>
    </div>
</template>

<script>
    import { ValidationProvider, ValidationObserver, extend, localize } from 'vee-validate';
    import * as rules from "vee-validate/dist/rules";
    import de from 'vee-validate/dist/locale/de.json';
    /*import en from 'vee-validate/dist/locale/en.json';*/

    localize('de', de);

    /*localize({de, en});*/

    Object.keys(rules).forEach(rule => {
        extend(rule, rules[rule]);
    });

    function initialState(enquiryTypes) {
        return {
            form: {
                name: null,
                email: '',
                comment: '',
                enquiry_type: ''
            },
            enquiry_types: enquiryTypes
        }
    }

    export default {
        name: "ContactForm",

        components: {
            ValidationProvider,
            ValidationObserver,
            rules
        },

        data() {
            return initialState(this.getEnquiryTypes());
        },

        props: {
            nameUser: {
                type: String,
                default: "",
                description: "Name"
            },
            emailUser: {
                type: String,
                default: "",
                description: "Email"
            },
            privacyRoute: {
                type: String,
                default: "/privacy",
                description: "Privacy route"
            },
            type: {
                type: String
            },
            comment: {
                type: String
            },
            formRoute: {
                type: String,
                required: true,
            }
        },

        methods: {
            getValidationState({ dirty, validated, valid = null }) {
                return dirty || validated ? valid : null;
            },
            onSubmit() {
                window.scrollTo(0,0);
                axios.put(this.formRoute, this.form)
                    .then((response) => {
                        this.showMessage(response);
                        Object.assign(this.$data, initialState(this.getEnquiryTypes()));
                    })
                    .catch((error) => {
                        this.$refs.observer.setErrors(error.response.data.errors);
                    });
            },
            getEnquiryTypes() {
                let trans = this.$t('contact_us.enquiry');
                let opts = [];

                for(let key in trans) {
                    let a = {};
                    a['value'] = key;
                    a['text'] = trans[key];
                    opts.push(a);
                }
                return opts;
            }
        },

        mounted() {
            this.form.name = this.nameUser;
            this.form.email = this.emailUser;
            this.form.enquiry_type = this.type;
            this.form.comment = this.comment;
            this.enquiry_types = this.getEnquiryTypes();
        }
    }
</script>
