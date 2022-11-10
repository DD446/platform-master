<template>
    <div>
        <validation-observer ref="observer" v-slot="{ invalid, valid, validated, passes, reset }">
            <b-overlay :show="isLoading" rounded="lg" class="p-0 p-md-2 p-lg-5">
                <alert-container></alert-container>
                <b-form ref="form"
                        @submit.stop.prevent="passes(onSubmit)">

                    <b-form-group :label="$t('audiotakes.label_transfer_funds_payout_options')" v-slot="{ ariaDescribedby }">
                            <b-form-radio-group
                            id="btn-radios-1"
                            v-model="model.payoutOption"
                            :options="payoutOptions"
                            :aria-describedby="ariaDescribedby"
                            name="radios-btn-default"
                        ></b-form-radio-group>
                    </b-form-group>

                    <b-form-group
                        :label="$t('audiotakes.label_payout_value')"
                        v-if="model.payoutOption === 'funds' || model.payoutOption === 'payment' && payoutGoalOptions.length > 1">
                        <b-row>
                            <b-col cols="12" md="9">
                                <validation-provider
                                    vid="payoutFunds"
                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                    :name="$t('audiotakes.label_payout_value')"
                                >
                                    <b-select
                                        required
                                        id="payoutFunds"
                                        :state="getValidationState(validationContext)"
                                        class="custom-select custom-select-lg mb-3"
                                        v-model="model.payoutFunds"
                                        :options="getFundsOptions"
                                    ></b-select>
                                    <b-form-invalid-feedback
                                        id="input-payout-funds-feedback">{{ validationContext.errors[0] }}
                                    </b-form-invalid-feedback>
                                </validation-provider>
                            </b-col>
                        </b-row>
                    </b-form-group>

                    <b-form-group
                        v-if="model.payoutOption === 'payment'"
                        :label="$t('audiotakes.label_contract_name')">
                        <b-row>
                            <b-col cols="12" md="9">
                                <b-select
                                    class="custom-select custom-select-lg mb-3"
                                    required
                                    v-model="model.audiotakes_contract_partner_id"
                                    :options="contracts"
                                ></b-select>
                            </b-col>
                        </b-row>
                    </b-form-group>

                    <b-form-group
                        :label="$t('audiotakes.label_payout_goal')"
                        v-if="model.payoutOption === 'payment'">
                        <template slot="label">
                            <legend class="bv-no-focus-ring col-form-label pt-0">
                                {{ $t('audiotakes.label_payout_goal') }}
                                <b-link
                                    v-if="payoutGoalOptions.length>1"
                                    class="btn btn-warning btn-sm"
                                    v-b-popover.hover.auto="$t('audiotakes.text_create_payout_goal')"
                                    @click="openPayoutGoalModal">{{ $t('audiotakes.text_create_payout_goal_short') }}</b-link>
                            </legend>
                        </template>
                        <b-row>
                            <b-col cols="12" md="9" v-if="payoutGoalOptions.length>1">
                                <b-select
                                    required
                                    id="selector"
                                    class="custom-select custom-select-lg mb-3"
                                    v-model="model.payoutGoal"
                                    :options="payoutGoalOptions"></b-select>
                            </b-col>
                            <b-col cols="12" md="9" v-else>
                                <b-link
                                    class="btn btn-warning"
                                    @click="openPayoutGoalModal">{{ $t('audiotakes.text_create_payout_goal') }}</b-link>
                            </b-col>
                        </b-row>
                    </b-form-group>

                    <b-form-group>
                        <b-row>
                            <b-col cols="12" md="9">
                                <b-button
                                    type="submit"
                                    variant="primary"
                                    size="lg"
                                    class="float-right"
                                    :disabled="!model.payoutFunds || isLoading"
                                    v-show="model.payoutOption === 'funds'
                                    || model.payoutOption === 'payment' && payoutGoalOptions.length > 1">
                                    <span v-text="buttonText"></span>
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-form-group>

                </b-form>
            </b-overlay>
        </validation-observer>
        <add-payout-account-modal :contracts="contracts" :country-list="countryList"></add-payout-account-modal>
    </div>
</template>

<script>
import { ValidationProvider, ValidationObserver, extend, localize } from 'vee-validate';
import * as rules from "vee-validate/dist/rules";
import de from 'vee-validate/dist/locale/de.json';
import CountrySelector from "../CountrySelector";
import {eventHub} from "../../app";
import AddPayoutAccountModal from "./AddPayoutAccountModal";
localize('de', de);
Object.keys(rules).forEach(rule => {
    extend(rule, rules[rule]);
});

export default {
    name: "AudiotakesTransferFunds",

    components: {
        AddPayoutAccountModal,
        ValidationProvider,
        ValidationObserver,
    },

    data() {
        return {
            model: {
                payoutOption: 'funds',
                payoutFunds: null,
                payoutGoal: null,
                audiotakes_contract_partner_id: null,
            },
            payoutOptions: this.getPayoutOptions(),
            payoutGoalOptions: [
                {
                    value: null,
                    text: this.$t('audiotakes.text_option_payout_goal_default'),
                    disabled: true
                }
            ],
            isLoading: false,
            availableFunds: 0
        }
    },

    props: {
        funds: {
            type: [Number, String],
            default: 0
        },
        minTransferLimit: {
            type: Number,
            default: 5
        },
        minPayoutLimit: {
            type: Number,
            default: 100
        },
        contracts: {
            type: Array,
            required: true,
        },
        countryList: {
            type: [Object, Array],
            required: true
        }
    },

    methods: {
        getPayoutOptions() {
            return [
                {
                    value: 'funds',
                    text: this.$t('audiotakes.text_option_payout_funds')
                },
                {
                    value: 'payment',
                    text: this.$t('audiotakes.text_option_payout_payment')
                }
            ]
        },
        openPayoutGoalModal() {
            eventHub.$emit('audiotakes-add-payout-account-modal:show');
        },
        onSubmit() {
            this.isLoading = true;
            this.errors = [];
            window.scrollTo(0,650);
            axios.post('/api/payouts', this.model)
                .then((response) => {
                    this.showMessage(response);
                    this.availableFunds = response.data.funds;
                    eventHub.$emit('audiotakes:funds', this.availableFunds);
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
                    this.model.payoutFunds = this.minPayoutLimit;
                });
        },
        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },
        getPayoutAccounts() {
            axios.get('/api/payouts/contacts')
                .then((response) => {
                    // Completely reset options to avoid duplicates
                    this.payoutGoalOptions = [
                        {
                            value: null,
                            text: this.$t('audiotakes.text_option_payout_goal_default'),
                            disabled: true
                        }
                    ]
                    for(let i in response.data) {
                        let a = {};
                        a['value'] = response.data[i].id;
                        a['text'] = response.data[i].name + ', ' + (response.data[i].paypal ? response.data[i].paypal + ' (PayPal)' : response.data[i].iban + ' (Bank)');
                        this.payoutGoalOptions.push(a);
                    }
                })
                .catch(error => {
                    this.showError(error);
                });
        }
    },

    computed: {
        buttonText() {
            if (this.model.payoutOption === 'funds') {
                return this.$t('audiotakes.button_transfer_funds');
            }
            return this.$t('audiotakes.button_payout_funds');
        },
        getFundsOptions() {
            let limit = this.minPayoutLimit;
            let start = limit+10;

            if (this.model.payoutOption === 'funds') {
                limit = this.minTransferLimit;
                start = limit*2;
            }

            let opts = [
                {
                    value: null,
                    text: this.$t('audiotakes.text_option_select'),
                    disabled: true
                }
            ];

            if (this.availableFunds >= limit) {
                opts.push({
                    value: limit,
                    text: limit + ' EUR', // TODO: I18N
                });

                for (var i = start; i < this.availableFunds; i += 10) {
                    opts.push({
                        value: i,
                        text: i + ' EUR', // TODO: I18N
                    });
                }
            } else {
                opts.push({
                    value: null,
                    text: this.$t('audiotakes.text_option_minimum_payout_limit_not_reached', {limit: limit}),
                    disabled: true
                });
            }

            return opts;
        }
    },

    mounted() {
        this.availableFunds = this.funds;
        this.getPayoutAccounts();

        eventHub.$on("audiotakes-add-payout-account-modal:added", () => {
            this.getPayoutAccounts();
        });
    },
}
</script>

<style scoped>

</style>
