<template>
    <span :class="state" v-if="amount || amount === 0" v-text="info"></span>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "Funds",

        props: {
            currency: null,
            amount: null,
        },

        data() {
            return {
                currencyCode: this.currency || '€',
                fundsSum: this.amount || 0,
            }
        },

        computed: {
            state() {
                if (this.fundsSum >= 0) {
                    return 'badge badge-success';
                }
                return 'badge badge-danger';
            },
            info() {
                return this.fundsSum + '' + this.currencyCode;
            }
        },

        mounted() {
            eventHub.$on("funds:add", amount => {
                this.fundsSum += parseInt(amount, 10);
            });
        },
    }
</script>
