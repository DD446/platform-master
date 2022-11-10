<template>
    <div class="mt-3">

        <div class="text-center" v-show="isLoading">
            <div class="spinner-grow m-5 h-1" role="status">
                <span class="sr-only">{{ $t("pat.is_loading") }}</span>
            </div>
        </div>

        <alert-container></alert-container>

        <vue-form-generator
            :schema="schema"
            :model="model"
            :options="formOptions"
            @validated="onValidated"
            @model-updated="onModelUpdated"></vue-form-generator>
    </div>
</template>

<script>
    import {eventHub} from '../app';
    import VueFormGenerator from "vue-form-generator";

    export default {
        name: "BillingAddress",

        components: {
            "vue-form-generator": VueFormGenerator.component,
        },

        props: {
            canDownloadBills: Boolean,
            user: {
                type: Object
            },
            countries: {
                type: [Object, Array],
                required: true
            }
        },

        data () {
            return {
                model: {
                    id: null,
                    first_name: null,
                    last_name: null,
                    email: null,
                    bill_by_email: null,
                    organisation: null,
                    department: null,
                    street: null,
                    housenumber: null,
                    city: null,
                    country: null,
                    post_code: null,
                    vat_id: null,
                    extras: null,
                },
                schema: {
                    fields: [
                        {
                            type: 'input',
                            inputType: 'hidden',
                            model: 'id',
                            readonly: true,
                        }
                    ],
                    groups: [
                        {
                            styleClasses: "mt-3",
                            legend: this.$t('bills.legend_name'),
                            fields: [
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('bills.label_first_name'),
                                    model: 'first_name',
                                    styleClasses: "col-md-6",
                                    placeholder: this.$t('bills.placeholder_first_name'),
                                    featured: false,
                                    required: false
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('bills.label_last_name'),
                                    model: 'last_name',
                                    styleClasses: "col-md-6",
                                    placeholder: this.$t('bills.placeholder_last_name'),
                                    required: true,
                                    validator: [VueFormGenerator.validators.string.locale({
                                        fieldIsRequired: this.$t('bills.validation_last_name_required'),
                                    })],
                                },
                                {
                                    type: 'input',
                                    inputType: 'email',
                                    label: this.$t('bills.label_email'),
                                    model: 'email',
                                    styleClasses: "col-md-6",
                                    placeholder: this.$t('bills.placeholder_email'),
                                    min: 6,
                                    validator: [
                                        VueFormGenerator.validators.email.locale({
                                            invalidEmail: this.$t('bills.validation_email_invalid'),
                                        })
                                    ],
                                },
                                {
                                    type: 'checkbox',
                                    label: this.$t('bills.label_bill_by_email'),
                                    styleClasses: "col-md-6",
                                    model: 'bill_by_email',
                                    default: true,
                                    hint: this.$t('bills.hint_bill_by_email'),
                                },
                            ]
                        },
                        {
                            styleClasses: "mt-3",
                            legend: this.$t('bills.legend_address'),
                            fields: [
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('bills.label_street'),
                                    styleClasses: "col-md-6",
                                    model: 'street',
                                    required: true,
                                    placeholder: this.$t('bills.placeholder_street'),
                                    validator: [VueFormGenerator.validators.string.locale({
                                        fieldIsRequired: this.$t('bills.validation_street_required'),
                                    })],
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('bills.label_housenumber'),
                                    styleClasses: "col-md-6",
                                    model: 'housenumber',
                                    required: true,
                                    placeholder: this.$t('bills.placeholder_housenumber'),
                                    validator: [VueFormGenerator.validators.string.locale({
                                        fieldIsRequired: this.$t('bills.validation_housenumber_required'),
                                    })],
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('bills.label_postcode'),
                                    styleClasses: "col-md-6",
                                    model: 'post_code',
                                    required: true,
                                    placeholder: this.$t('bills.placeholder_postcode'),
                                    validator: [VueFormGenerator.validators.string.locale({
                                        fieldIsRequired: this.$t('bills.validation_postcode_required'),
                                    })],
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('bills.label_city'),
                                    styleClasses: "col-md-6",
                                    model: 'city',
                                    required: true,
                                    placeholder: this.$t('bills.placeholder_city'),
                                    validator: [VueFormGenerator.validators.string.locale({
                                        fieldIsRequired: this.$t('bills.validation_city_required'),
                                    })],
                                },{
                                    type: "select",
                                    label: this.$t('bills.label_country'),
                                    placeholder: this.$t('bills.placeholder_country'),
                                    styleClasses: "col-md-6",
                                    model: "country",
                                    selectOptions: {
                                        noneSelectedText: this.$t('bills.choose_a_country'),
                                        hideNoneSelectedText: false,
                                    },
                                    required: true,
                                    values: [],
                                    validator: [
                                        VueFormGenerator.validators.string.locale({
                                            fieldIsRequired: this.$t('bills.validation_country_required'),
                                        })
                                    ],
                                }
                            ]
                        },
                        {
                            styleClasses: "mt-3",
                            legend: this.$t('bills.legend_organisation'),
                            fields: [
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('bills.label_organisation'),
                                    styleClasses: "col-md-6",
                                    model: 'organisation',
                                    placeholder: this.$t('bills.placeholder_organisation'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('bills.label_department'),
                                    styleClasses: "col-md-6",
                                    model: 'department',
                                    placeholder: this.$t('bills.placeholder_department'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('bills.label_vatid'),
                                    styleClasses: "col-md-12",
                                    model: 'vat_id',
                                    validator: [
                                        'regexp'
                                    ],
                                    placeholder: this.$t('bills.placeholder_vatid'),
                                    pattern: [
                                        '^(ATU[0-9]{8}|BE0[0-9]{9}|BG[0-9]{9,10}|CY[0-9]{8}L|CZ[0-9]{8,10}|DE[0-9]{9}|DK[0-9]{8}|EE[0-9]{9}|(EL|GR)[0-9]{9}|ES[0-9A-Z][0-9]{7}[0-9A-Z]|FI[0-9]{8}|FR[0-9A-Z]{2}[0-9]{9}|GB([0-9]{9}([0-9]{3})?|[A-Z]{2}[0-9]{3})|HU[0-9]{8}|IE[0-9]S[0-9]{5}L|IT[0-9]{11}|LT([0-9]{9}|[0-9]{12})|LU[0-9]{8}|LV[0-9]{11}|MT[0-9]{8}|NL[0-9]{9}B[0-9]{2}|PL[0-9]{10}|PT[0-9]{9}|RO[0-9]{2,10}|SE[0-9]{12}|SI[0-9]{8}|SK[0-9]{10})$'
                                    ]
                                },
                                {
                                    type: 'textArea',
                                    label: this.$t('bills.label_extras'),
                                    styleClasses: "col-md-12",
                                    model: 'extras',
                                    placeholder: this.$t('bills.placeholder_extras'),
                                    hint: this.$t('bills.hint_extras'),
                                }
                            ]
                        },
                        {
                            styleClasses: "mt-3",
                            fields: [
                                {
                                    type: 'submit',
                                    buttonText: this.$t('bills.button_text_submit'),
                                    onSubmit: () => {
                                        return this.onSubmit();
                                    },
                                    validateBeforeSubmit: true
                                },
                            ]
                        }
                    ],
                },
                formOptions: {
                    validateAfterLoad: true,
                    validateAfterChanged: true,
                    validateAsync: true
                },
                errors: [],
                forcedEntry: !this.canDownloadBills || false,
                isLoading: false
            }
        },

        methods: {
            init() {
                this.$data.model = this.user;
            },
            onValidated(state) {
            },
            onModelUpdated(value, field) {
            },
            onSubmit(e) {
                this.isLoading = true;
                window.scrollTo(0,275);
                axios.put('/billing', this.model)
                    .then((response) => {
                        eventHub.$emit('show-message:success', response.data);
                        if (undefined !== this.$root.$refs.billtab) {
                            this.$root.$refs.billtab.disabled = false;
                        }
                    })
                    .catch((error) => {
                        eventHub.$emit('show-message:error', error.response.data.errors ? error.response.data.errors : error);
                    }).then(() => {
                        this.isLoading = false;
                    });
            }
        },

        mounted() {
            let countryList = [];
            for(let i in this.countries) {
                let a = {};
                a['id'] = i;
                a['name'] = this.countries[i];
                countryList.push(a);
            }
            this.schema.groups[1].fields[4].values = countryList;
            this.init();
        }
    }
</script>
