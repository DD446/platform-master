<template>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <b-spinner label="Lade Daten..." class="m-5" style="width: 3rem; height: 3rem;" aria-hidden="true" v-show="loading" />
                <div v-for="option in options">
                    <SpotifyPodcastEntry
                            :feed_id="option.id"
                            :feed_title="option.name"
                            :spotify_uri="option.spotify_uri"
                            :spotify_updated="option.spotify_updated"
                    ></SpotifyPodcastEntry>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import SpotifyPodcastEntry from "./SpotifyPodcastEntry";
    import {eventHub} from '../../app';

    export default {
        name: "Spotify",

        data() {
            return {
                loading: true,
                options: [],
            }
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        created() {
        },

        methods: {
            prepareComponent() {
                axios.get('/spotify')
                    .then(response => {
                        this.options = response.data;
                    })
                    .catch(error => {
                        // handle error
                        this.showError(error);
                    }).then(() => {
                        // always executed
                        this.loading = false;
                    });
            }
        },

        components: {
            SpotifyPodcastEntry
        }
    }
</script>

<style scoped>

</style>
