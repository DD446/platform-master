<template>
    <div>
        <heading class="mb-6">Unbezahlte Rechnungen</heading>

        <card class="bg-20 flex flex-col items-center justify-center p-6" style="min-height: 300px">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Kunde</th>
                    <th>Rechnungsnummer</th>
                </tr>
                <tr v-for="bill in bills">
                    <td>{{ bill.payment_id }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </card>

    </div>
</template>

<script>
    export default {
        name: "UnpaidBills",

        data() {
            bills: {}
        },

        methods: {
            getBills() {
                Nova.request().get('/nova-vendor/funds-management/bills').then(response => {
                    this.bills = response.data;
                });
            },
        },

        mounted() {
            this.getBills();
        },
    }
</script>

<style scoped>

</style>