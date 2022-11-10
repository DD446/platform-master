<template>
    <div>
        <alert-container></alert-container>
        <b-overlay :show="isLoading">
            <b-form @submit.prevent="onSubmit">
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-12">
                        <b-input-group
                                prepend="€"
                                append=".00">
                            <b-input
                                    id="input-live"
                                    v-model="amount"
                                    min="10"
                                    step="1"
                                    type="number"
                                    :state="amountState"
                                    :placeholder="$t('accounting.placeholder_amount_bill')"
                                    trim
                            ></b-input>
                        </b-input-group>

                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-12 mt-3 mt-sm-0">
                        <b-button type="submit" variant="primary" :disabled="isLoading">{{ $t('accounting.button_bill_pay', { amount: validAmount }) }}</b-button>
                    </div>
                </div>
            </b-form>
        </b-overlay>
    </div>
</template>

<script>
    import {eventHub} from '../../app';

    const minimumAmount = 10;

    export default {
        name: "Bill",

        data() {
            return {
                amount: 50,
                isLoading: false,
            }
        },

        computed: {
            amountState() {
                return this.amount >= minimumAmount;
            },
            validAmount() {
                if (isNaN(this.amount) || this.amount < 0) {
                    return minimumAmount;
                }
                if (this.amount < minimumAmount) {
                    return minimumAmount;
                }
                return this.amount;
            }
        },

        methods: {
            onSubmit() {
                this.isLoading = true;
                let funds = this.amount;
                axios.post('/beta/payment', { amount: funds })
                    .then(response => {
                        eventHub.$emit('show-message:success', response.data);
                        eventHub.$emit('funds:add', funds);
                        this.amount = 50;
                    },
                    (error) => {
                        this.showError(error);
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                        this.isLoading = false;
                    });
            },
        },
    }
</script>

<style scoped>

</style>
