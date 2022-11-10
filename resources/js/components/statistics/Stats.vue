<template>
    <div class="container-fluid">

        <div class="mb-2">
            <b-nav tabs>
                <b-nav-item
                    :to="getRoute('')"
                    :active='$route.name && $route.name.startsWith("podcaster-") || !$route.name'
                    lazy>podcaster.de</b-nav-item>
                <b-nav-item
                    :to="getRoute('pioniere')"
                    :active='$route.name==="pioniere" || $route.name==="audiotakes"'
                    lazy>Podcast Pioniere</b-nav-item>
                <b-nav-item
                    :to="getRoute('spotify')"
                    :active='$route.name==="spotify"'
                    lazy>Spotify</b-nav-item>
            </b-nav>
        </div>

        <div class="row">
            <b-col cols="12">
                <div class="bg-white p-1 p-lg-5">
                    <alert-container></alert-container>
                    <router-view
                        :audiotakes="usesAudiotakes"
                        :useNewStatistics="useNewStatistics"></router-view>
                </div>
            </b-col>
        </div>

        <stats-export-modal
            :can-export="this.canExport"></stats-export-modal>
    </div>
</template>

<script>
import StatsExportModal from "./StatsExportModal";

export default {
        name: "Stats",

        components: {
            StatsExportModal
        },

        methods: {
        },

        props: {
            useNewStatistics: {
                default: false,
                type: [Boolean, Number]
            },
            usesAudiotakes: {
                default: false,
                type: [Boolean, Number]
            },
            canExport: {
                default: false,
                type: [Boolean, Number]
            },
        }
    }
</script>

<style scoped>
</style>
