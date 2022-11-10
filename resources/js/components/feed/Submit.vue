<template>
    <b-container>
        <submit-item
                class="mt-4"
                v-for="(item, index) in items"
                :key="index"
                :type="item"
                :feed="feed"
        ></submit-item>
    </b-container>
</template>

<script>
    import SubmitItem from "./SubmitItem";

    export default {
        name: "Submit",

        props: {
            feed: String,
        },

        components: {
            SubmitItem
        },

        data() {
            return {
                items: [
                    'itunes',
                    'spotify',
                    'podcast',
                    'google',
                    'amazon',
                    'deezer',
                    'samsung',
                    'tunein',
                    'stitcher',
                    'playerfm',
                    'listennotes',
                    'fyyd',
                    // Subscribe on Android?
                    'castbox',
                    'pocketcasts',
                    //'fyeo',
                    //'audionow',
                    //'castro', // Läuft über Itunes oder Itunes-ID
                ]
            }
        }
    }
</script>
