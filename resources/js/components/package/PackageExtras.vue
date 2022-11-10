<template>
    <div>
        <b-row>
            <b-col xl="6" lg="6" md="12" sm="12">
                <package-extras-booked></package-extras-booked>
            </b-col>
            <b-col xl="6" lg="6" md="12" sm="12">
                <package-booking
                    :bookable="bookable"
                    :can-add-player-configuration="canAddPlayerConfiguration"
                    :can-add-member="canAddMember"
                    :can-add-stats-export="canAddStatsExport"></package-booking>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    import PackageExtrasBooked from "./PackageExtrasBooked";
    import PackageBooking from "./PackageBooking";
    export default {
        name: "PackageExtras",

        components: {PackageBooking, PackageExtrasBooked},

        props: {
            bookable: Object,
            canAddPlayerConfiguration: {
                type: Boolean,
                default: false,
            },
            canAddMember: {
                type: Boolean,
                default: false,
            },
            canAddStatsExport: {
                type: Boolean,
                default: false,
            }
        },
    }

</script>

<style scoped>

</style>
