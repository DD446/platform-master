<template>
    <b-modal
        ref="addPayoutAccountModal"
        :title="$t('audiotakes.header_create_account')"
        :ok-title="$t('audiotakes.button_create_account')"
        :cancel-title="$t('audiotakes.button_cancel_create_account')"
        @ok="handleOk"
    >
        <alert-container></alert-container>
        <b-overlay :show="isLoading">
            <b-form
                ref="form"
                method="post"
                @submit.stop.prevent="handleSubmit"
                @keydown="form.onKeydown($event)"
            >
                <b-form-group
                    :label="$t('audiotakes.label_payout_name')">
                    <b-form-input
                        required
                        autofocus
                        v-model="form.name"
                        :state="payoutNameState"
                        :placeholder="$t('audiotakes.placeholder_payout_name')"></b-form-input>
                    <div v-if="form.errors.has('name')" v-html="form.errors.get('name')" />
                </b-form-group>

                <b-form-group
                    :label="$t('audiotakes.label_country')">
                    <b-select
                        required
                        v-model="form.country"
                        :options="countries"></b-select>
                </b-form-group>

                <b-form-group
                    :label="$t('audiotakes.label_vat_required')">
                    <b-form-radio-group
                        required
                        v-model="form.vat_required"
                        :options="payoutVatRequiredOptions"
                    ></b-form-radio-group>
                </b-form-group>

                <b-form-group
                    v-if="!form.vat_required"
                    :label="$t('audiotakes.label_payout_tax_id')">
                    <b-form-input
                        required
                        v-model="form.tax_id"
                        :state="payoutTaxIdState"
                        :placeholder="$t('audiotakes.placeholder_payout_tax_id')"></b-form-input>
                </b-form-group>

                <b-form-group
                    :label="$t('audiotakes.label_vat_id')"
                    v-if="form.vat_required">
                    <b-form-input
                        required
                        v-model="form.vat_id"
                        :state="payoutVatIdState"
                        :placeholder="$t('audiotakes.placeholder_payout_vat_id')"></b-form-input>
                </b-form-group>

                <b-form-group
                    :label="$t('audiotakes.label_payout_goal_type')"
                >
                    <b-form-radio-group
                        required
                        v-model="form.goal_type"
                        :options="payoutGoalTypes"></b-form-radio-group>
                    <div v-if="form.errors.has('goal_type')" v-html="form.errors.get('goal_type')" />
                </b-form-group>

                <b-form-group
                    :label="$t('audiotakes.label_payout_email')"
                    v-if="form.goal_type === 'paypal'">
                    <b-form-input
                        required
                        type="email"
                        v-model="form.paypal"
                        :state="payoutPaypalState"
                        :placeholder="$t('audiotakes.placeholder_payout_email')"></b-form-input>
                </b-form-group>

                <div v-if="form.goal_type === 'bank'">
                    <b-form-group
                        :label="$t('audiotakes.label_payout_bank_account_owner')">
                        <b-form-input
                            required
                            v-model="form.bank_account_owner"
                            :state="payoutBankAccountOwnerState"
                            :placeholder="$t('audiotakes.placeholder_payout_bank_account_owner')"></b-form-input>
                    </b-form-group>

                    <b-form-group
                        :label="$t('audiotakes.label_payout_iban')"
                        >
                        <b-form-input
                            required
                            v-model="form.iban"
                            :state="payoutIbanState"
                            :placeholder="$t('audiotakes.placeholder_payout_iban')"></b-form-input>
                    </b-form-group>
                </div>
            </b-form>
        </b-overlay>
    </b-modal>
</template>

<script>
import {eventHub} from "../../app";
import Form from "vform";

export default {
    name: "AddPayoutAccountModal",
    data() {
        return {
            payoutGoalTypes: [
                {
                    value: 'paypal',
                    text: this.$t('audiotakes.option_payout_goal_type_paypal'),
                },
                {
                    value: 'bank',
                    text: this.$t('audiotakes.option_payout_goal_type_bank')
                }
            ],
            payoutVatRequiredOptions: [
                {
                    value: 0,
                    text: this.$t('audiotakes.option_payout_vat_required_no'),
                },
                {
                    value: 1,
                    text: this.$t('audiotakes.option_payout_vat_required_yes'),
                }
            ],
            isLoading: false,
            countries: [
                {
                    value: null,
                    text: this.$t('audiotakes.default_option_countries'),
                    disabled: true,
                }
            ],
            form: new Form({
                'name': '',
                'goal_type': 'paypal',
                'country': 'DE',
                'vat_required': 0,
                'vat_id': null,
                'tax_id': '',
                'paypal': '',
                'iban': '',
            }),
        }
    },

    props: {
        countryList: {
            type: [Object, Array],
            required: true
        }
    },

    methods: {
        show() {
            this.$refs.addPayoutAccountModal.show();
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
                const response = await this.form.post('/api/payouts/contacts')
                this.showMessage(response);
                this.form = new Form({
                    name: '',
                    'goal_type': 'paypal',
                    country: 'DE',
                    'vat_required': 0,
                    'vat_id': null,
                    'tax_id': '',
                    paypal: '',
                    iban: '',
                    'bank_account_owner': '',
                });
                eventHub.$emit('audiotakes-add-payout-account-modal:added');
                this.$refs.addPayoutAccountModal.hide();
            } catch (e) {
                this.showError(e);
            }
            this.isLoading = false;
        }
    },

    computed: {
        payoutNameState() {
            if (this.form.errors.has('name')) return false;
            if (this.form.name.length < 1) {
                return null;
            }
            return this.form.name.length > 2;
        },
        payoutTaxIdState() {
            if (this.form.errors.has('tax_id')) return false;
            if (!this.form.tax_id || this.form.tax_id.length < 1) {
                return null;
            }
            return this.form.tax_id.length > 5;
        },
        payoutPaypalState() {
            if (this.form.errors.has('paypal')) return false;
            if (!this.form.paypal || this.form.paypal.length < 7) {
                return null;
            }
            return this.form.paypal.includes('.') && this.form.paypal.includes('@');
        },
        payoutIbanState() {
            if (this.form.errors.has('iban')) return false;
            if (!this.form.iban || this.form.iban.length < 16) {
                return null;
            }
            return this.form.iban.length >= 16;
        },
        payoutBankAccountOwnerState() {
            if (this.form.errors.has('bank_account_owner')) return false;
            if (!this.form.bank_account_owner || this.form.bank_account_owner.length < 3) {
                return null;
            }
            return this.form.bank_account_owner.length >= 3;
        },
        payoutVatIdState() {
            if (this.form.errors.has('vat_id')) return false;

            return null;
        }
    },

    mounted() {
        for(let i in this.countryList) {
            let a = {};
            a['value'] = i;
            a['text'] = this.countryList[i];
            this.countries.push(a);
        }
        eventHub.$on("audiotakes-add-payout-account-modal:show", () => {
            this.show();
        });
    },
}
</script>

<style scoped>

</style>
