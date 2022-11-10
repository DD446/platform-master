<template>
    <div class="pt-5">
        <h4 class="display-4">{{ name }}</h4>
        <div class="row">
            <div class="col-9">
                <b-button size="lg" :variant="btnState" :disabled="disabled" v-on:click="submitButton">Bei Spotify {{ btnText }}melden</b-button>
            </div>
            <div class="col-3">
                <a
                    href="#"
                    v-on:click="openInNewWindow"
                    v-show="suri"
                    v-b-popover.hover.top="'Öffnet die öffentliche Detailseite des Podcasts bei Spotify'">
                        <i class="socicon-spotify"></i> Spotify</a>
            </div>
        </div>
    </div>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "SpotifyPodcastEntry",

        data() {
            return {
                id: null,
                name: null,
                suri: null,
                supdated: null,
                btnState: 'primary',
                btnText: 'an',
                disabled: false,
            }
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
        },

        created() {
            this.id = this.feed_id;
            this.name = this.feed_title;
            this.suri = this.spotify_uri;
            this.supdated = this.spotify_updated;

            if (this.suri) {
                this.setOff();
            }
        },

        props: {
            feed_id: String,
            feed_title: String,
            spotify_uri: String,
            spotify_updated: Object,
        },

        methods: {
            openInNewWindow() {
                window.open('https://open.spotify.com/show/' + this.suri.replace(/spotify:show:/, ""));
            },
            submitButton() {
                this.disabled = true;

                if (this.btnText === 'ab') {
                    if (confirm('Podcast wirklich wieder bei Spotify abmelden?')) { // TODO: I18N
                        axios.delete('/spotify/' + this.id)
                            .then(response => {
                                this.showMessage(response);
                                this.suri = null;
                                this.setOn();
                            })
                            .catch(error => {
                                this.showError(error);
                            });
                    }
                } else {
                    axios.post('/spotify', this.$data)
                        .then(response => {
                            this.suri = response.data.uri;
                            this.setOff();
                        })
                        .catch(error => {
                            this.showError(error);
                        });
                }
            },
            setOn() {
                this.btnState = 'primary';
                this.btnText = 'an';
                this.disabled = false;
            },
            setOff() {
                this.btnState = 'warning';
                this.btnText = 'ab';
                this.disabled = false;
            }
        }
    }
</script>

<style scoped>

</style>
