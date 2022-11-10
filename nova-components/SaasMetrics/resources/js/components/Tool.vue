<template>
    <div>
        <loading-view :loading="initialLoading">
            <heading class="mb-6">SaaS Metrics</heading>

            <visitors-signups></visitors-signups>

            <paying-customers></paying-customers>
        </loading-view>
    </div>
</template>

<script>
import VisitorsSignups from "./VisitorsSignups";
import PayingCustomers from "./PayingCustomers";
function initialState (){
    return {
        errors: {},
        initialLoading: true,
        loading: false,
    }
}

export default {
    components: {VisitorsSignups, PayingCustomers},
    metaInfo() {
        return {
            title: 'SaaS Metrics',
        }
    },

    data() {
        return initialState();
    },

    methods: {
    },

    mounted() {
        this.initialLoading = false;
    },
}
</script>

<style>
/* Scoped Styles */
</style>
