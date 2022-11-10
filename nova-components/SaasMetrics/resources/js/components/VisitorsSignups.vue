<template>
    <div class="mb-5">
        <h3>Visitors & Signups</h3>

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
                            <th v-for="(month, key) in vs">
                                {{ key }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                Monthly visitors
                            </td>
                            <td v-for="(month, k) in vs">
                                {{ month.visitors }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                m/m growth visitors
                            </td>
                            <td v-for="(month, k) in vs">
                                <span :class="changeColor(month.mmgrowthvisitors)">
                                    {{ month.mmgrowthvisitors }}%
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Signups beginning of the month
                            </td>
                            <td v-for="(month, k) in vs">
                                {{ month.signups }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Total new signups
                            </td>
                            <td v-for="(month, k) in vs">
                                {{ month.totalnewsignups }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                m/m growth new signups
                            </td>
                            <td v-for="(month, k) in vs">
                                <span :class="changeColor(month.mmgrowthnewsignups)">
                                    {{ month.mmgrowthnewsignups }}%
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Visitor-to-Signup Conversion Rate
                            </td>
                            <td v-for="(month, k) in vs">
                                <span :class="changeColor(month.visitortosignupconversionrate)">
                                    {{ month.visitortosignupconversionrate }}%
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
        vs: [],
        errors: {},
        loading: true,
    }
}

export default {
    name: "VisitorsSignups",

    data() {
        return initialState();
    },

    methods: {
        getVisitors() {
            Nova.request()
                .get('/nova-vendor/saas-metrics/visitors-signups')
                .then(response => {
                    this.vs = response.data;
                    this.loading = false;
                });
        },
        changeColor(num) {
            if (num < 0) return 'text-danger';
            if (num > 0) return 'text-success';
        }
    },

    mounted() {
        this.getVisitors();
    },
}
</script>

<style>
/* Scoped Styles */
</style>
