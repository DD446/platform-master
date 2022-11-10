<template>
    <div class="mb-5">
        <h3>Paying Customers</h3>

        <div class="pt-5">
            <table
                cellpadding="0"
                cellspacing="0"
                class="table w-full bg-white">
                <thead>
                    <tr>
                        <th>
                            Month
                        </th>
                        <th v-for="(month, key) in pc">
                            {{ key }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            Customers beginning of the month
                        </td>
                        <td v-for="(month, k) in pc">
                            {{ month.customersstartmonth }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Total new signups
                        </td>
                        <td v-for="(month, k) in pc">
                            {{ month.totalnewsignups }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            New customers
                        </td>
                        <td v-for="(month, k) in pc">
                            {{ month.newcustomers }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Conversion rate
                        </td>
                        <td v-for="(month, k) in pc">
                            <span :class="changeColor(month.conversionrate)">
                                {{ month.conversionrate }}%
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Lost customers
                        </td>
                        <td v-for="(month, k) in pc">
                            {{ month.lostcustomers }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Churn rate
                        </td>
                        <td v-for="(month, k) in pc">
                            <span :class="changeColor(month.churnrate)">
                                {{ month.churnrate }}%
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Net new customers
                        </td>
                        <td v-for="(month, k) in pc">
                            {{ month.netnewcustomers }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Customers end of month
                        </td>
                        <td v-for="(month, k) in pc">
                            {{ month.customersendmonth }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            m/m growth customers
                        </td>
                        <td v-for="(month, k) in pc">
                            <span :class="changeColor(month.mmgrowthcustomers)">
                                {{ month.mmgrowthcustomers }}%
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
function initialState (){
    return {
        pc: [],
        errors: {},
        loading: true,
    }
}

export default {
    name: "PayingCustomers",

    data() {
        return initialState();
    },

    methods: {
        getPayingCustomers() {
            Nova.request()
                .get('/nova-vendor/saas-metrics/paying-customers')
                .then(response => {
                    this.pc = response.data;
                    this.loading = false;
                });
        },
        changeColor(num) {
            if (num < 0) return 'text-danger';
            if (num > 0) return 'text-success';
        }
    },

    mounted() {
        this.getPayingCustomers();
    },
}
</script>

<style scoped>

</style>
