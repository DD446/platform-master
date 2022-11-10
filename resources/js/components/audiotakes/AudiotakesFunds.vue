<template>
    <span class="badge badge-success">{{ availableFunds }}&euro;</span>
</template>

<script>
import {eventHub} from "../../app";

export default {
    name: "AudiotakesFunds",

    data() {
        return {
            availableFunds: 0,
        }
    },

    props: {
        funds: {
            required: true,
            type: [Number, String],
        }
    },

    mounted() {
        this.availableFunds = this.funds;

        eventHub.$on("audiotakes:funds", funds => {
            this.availableFunds = funds;
        });
    },
}
</script>

<style scoped>

</style>
