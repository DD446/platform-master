<template>
    <div class="mt-2">

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
    import {eventHub} from '../../app';
    import VueFormGenerator from "vue-form-generator";
    import 'vue-form-generator/dist/vfg.css';

    export default {
        name: "ProfileAddress",

        components: {
            "vue-form-generator": VueFormGenerator.component,
        },

        props: {
            user: {
                type: Object
            }
        },

        data () {
            return {
                model: {
                    usr_id: null,
                    name_title: null,
                    first_name: null,
                    last_name: null,
                    email: null,
                    passwd: null,
                    organisation: null,
                    department: null,
                    street: null,
                    housenumber: null,
                    city: null,
                    country: null,
                    post_code: null,
                    additional_specifications: null,
                    register_court: null,
                    register_number: null,
                    vat_id: null,
                    board: null,
                    chairman: null,
                    representative: null,
                    mediarepresentative: null,
                    controlling_authority: null,
                },
                schema: {
                    fields: [
                        {
                            type: 'input',
                            inputType: 'hidden',
                            model: 'usr_id',
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
                                    label: this.$t('profile.label_title'),
                                    model: 'name_title',
                                    styleClasses: "col-md-2",
                                    placeholder: this.$t('profile.placeholder_name_title'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('bills.label_first_name'),
                                    model: 'first_name',
                                    styleClasses: "col-md-4",
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
                                    readonly: true,
                                    disabled: true,
                                    placeholder: this.$t('bills.placeholder_email'),
                                    min: 6,
                                    validator: [
                                        VueFormGenerator.validators.email.locale({
                                            invalidEmail: this.$t('bills.validation_email_invalid'),
                                        })
                                    ],
                                    hint: this.$t('profile.hint_email_change'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('profile.label_password'),
                                    styleClasses: "col-md-6",
                                    model: "passwd",
                                    readonly: true,
                                    disabled: true,
                                    placeholder: this.$t('profile.placeholder_password'),
                                    hint: this.$t('profile.hint_password_change'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'tel',
                                    label: this.$t('profile.label_phone'),
                                    model: 'telephone',
                                    styleClasses: "col-md-6",
                                    placeholder: this.$t('profile.placeholder_phone'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'tel',
                                    label: this.$t('profile.label_fax'),
                                    model: 'telefax',
                                    styleClasses: "col-md-6",
                                    placeholder: this.$t('profile.placeholder_fax'),
                                }
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
                                    model: 'street',
                                    styleClasses: "col-md-6",
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
                                    model: 'housenumber',
                                    styleClasses: "col-md-6",
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
                                    model: 'post_code',
                                    styleClasses: "col-md-2",
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
                                    model: 'city',
                                    styleClasses: "col-md-4",
                                    required: true,
                                    placeholder: this.$t('bills.placeholder_city'),
                                    validator: [VueFormGenerator.validators.string.locale({
                                        fieldIsRequired: this.$t('bills.validation_city_required'),
                                    })],
                                },
                                {
                                    type: "select",
                                    label: this.$t('bills.label_country'),
                                    placeholder: this.$t('bills.placeholder_country'),
                                    model: "country",
                                    styleClasses: "col-md-6",
                                    required: true,
                                    values: [],
                                    selectOptions: {
                                        noneSelectedText: this.$t('profile.choose_a_country'),
                                        hideNoneSelectedText: false,
                                    },
                                    validator: [
                                        VueFormGenerator.validators.string.locale({
                                            fieldIsRequired: this.$t('bills.validation_country_required'),
                                        })
                                    ],
                                },
                                {
                                    type: 'input',
                                    inputType: 'url',
                                    label: this.$t('profile.label_url'),
                                    model: 'url',
                                    styleClasses: "col-md-12",
                                    placeholder: this.$t('profile.placeholder_url'),
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
                                    label: this.$t('profile.label_representative'),
                                    styleClasses: "col-md-6",
                                    model: 'representative',
                                    placeholder: this.$t('profile.placeholder_representative'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('profile.label_mediarepresentative'),
                                    styleClasses: "col-md-6",
                                    model: 'mediarepresentative',
                                    placeholder: this.$t('profile.placeholder_mediarepresentative'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('profile.label_register_court'),
                                    styleClasses: "col-md-6",
                                    model: 'register_court',
                                    placeholder: this.$t('profile.placeholder_register_court'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('profile.label_register_number'),
                                    styleClasses: "col-md-6",
                                    model: 'register_number',
                                    placeholder: this.$t('profile.placeholder_register_number'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('profile.label_board'),
                                    styleClasses: "col-md-6",
                                    model: 'board',
                                    placeholder: this.$t('profile.placeholder_board'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('profile.label_chairman'),
                                    styleClasses: "col-md-6",
                                    model: 'chairman',
                                    placeholder: this.$t('profile.placeholder_chairman'),
                                },
                                {
                                    type: 'textArea',
                                    label: this.$t('profile.label_controlling_authority'),
                                    styleClasses: "col-md-12",
                                    model: 'controlling_authority',
                                    placeholder: this.$t('profile.placeholder_controlling_authority'),
                                }
                            ]
                        },
                        {
                            styleClasses: "mt-3 mb-2",
                            legend: this.$t('bills.legend_extras'),
                            fields: [
                                {
                                    type: 'textArea',
                                    label: this.$t('bills.label_extras'),
                                    model: 'additional_specifications',
                                    styleClasses: "col-md-12",
                                    placeholder: this.$t('bills.placeholder_extras'),
                                },
                                {
                                    /*styleClasses: "btn btn-primary float-right",*/
                                    type: 'submit',
                                    buttonText: this.$t('profile.button_save_address'),
                                    styleClasses: "mt-2",
                                    onSubmit: () => {
                                        return this.onSubmit();
                                    },
                                    validateBeforeSubmit: true,
                                },
                            ]
                        }
                    ],
                },
                formOptions: {
                    validateAfterLoad: false,
                    validateAfterChanged: true,
                    validateAsync: true
                },
                errors: [],
                isLoading: false
            }
        },

        methods: {
            init() {
                this.getCountries();
                //this.getContactData();
                this.$data.model = this.user;
                this.$data.model.passwd = '*****************';
            },
            getContactData() {
                axios.get('/api/user')
                    .then((response) => {
                        this.$data.model = response.data;
                    })
                    .catch(error => {
                        eventHub.$emit('show-message:error', error.response.data.errors ? error.response.data.errors : error);
                    });
            },
            getCountries() {
                axios.get('/countries')
                    .then((response) => {

                        let countries = [];

                        for(let i in response.data) {
                            let a = {};
                            a['id'] = i;
                            a['name'] = response.data[i];
                            countries.push(a);
                        }

                        this.schema.groups[1].fields[4].values = countries;
                    })
                    .catch(error => {
                        eventHub.$emit('show-message:error', error.response.data.errors ? error.response.data.errors : error);
                    });
            },
            onValidated(state) {
            },
            onModelUpdated(value, field) {
            },
            onSubmit(e) {
                //this.errors = [];
                window.scrollTo(0,275);
                this.isLoading = true;
                axios.put('/api/user/' + this.model.usr_id, this.model)
                    .then((response) => {
                        eventHub.$emit('show-message:success', response.data);
                    })
                    .catch((error) => {
                        eventHub.$emit('show-message:error', error.response.data.errors ? error.response.data.errors : error);
                    }).then(() => {
                        this.isLoading = false;
                    });
            }
        },

        mounted() {
            this.init();
        }
    }
</script>
