<template>
    <div class="pt-5">
        <h4 class="display-4">{{ name }}</h4>

        <template v-if="deezer_id">
            <div class="row">
                <div class="col-12">
                    <b-overlay :show="loading" rounded="sm">
                        <p class="alert alert-info">
                            <span class="float-right">
                            </span>
                                <b-btn-close
                                    class="float-right"
                                    variant="primary"
                                    size="lg"
                                    @click="withdrawSubmission"
                                    v-b-popover.hover="$t('deezer.popover_withdraw_deezer_submission')"></b-btn-close>
                            {{ $t('deezer.text_podcast_added') }}
                        </p>
                    </b-overlay>
                </div>
            </div>
        </template>
        <template v-else>
            <b-overlay :show="isLoading" rounded="sm">
                <div class="row">
                    <div class="col-12 col-md-4 col-lg-2 mt-2">
                        <label for="country-selector">
                            {{ $t('deezer.label_country_of_origin') }}<span class="">*</span>
                        </label>
                    </div>
                    <div class="col-11 col-md-7 col-lg-9 mt-2">
                        <country-selector :feedId="feed_id"></country-selector>
                    </div>
                    <div class="col-1 mt-2">
                        <i class="icon icon-info-with-circle text-blue" v-b-popover.hover.click="$t('deezer.popover_country_of_origin')"></i>
                    </div>
                </div>
                <div class="row">
                    <b-col cols="12" class="mt-3">
                            <b-button
                                size="lg"
                                class="float-right"
                                variant="primary"
                                :disabled="!coo"
                                v-on:click="submitButton">{{ $t('deezer.text_button_add') }}</b-button>
                    </b-col>
                </div>
            </b-overlay>
        </template>
    </div>
</template>

<script>

    import CountrySelector from "../../../CountrySelector";
    import {eventHub} from "../../../../app";

    export default {
        name: "DeezerPodcastEntry",

        components: {
            CountrySelector
        },

        data() {
            return {
                isLoading: false,
                loading: false,
                id: null,
                name: null,
                coo: null,
                deezer_id: null,
            }
        },

        mounted() {
            eventHub.$on('country:selected:' + this.feed_id, (country) => {
                this.coo = country.code;
            });
        },

        created() {
            this.id = this.feed_id;
            this.name = this.feed_title;
            this.coo = this.deezer['coo'];
            this.deezer_id = this.deezer['id'];
        },

        props: {
            feed_id: String,
            feed_title: String,
            deezer: {
                type: Array,
                default: {date: null, coo: null},
            }
        },

        methods: {
            submitButton() {
                this.isLoading = true;
                axios.post('/deezer', { feed_id: this.id, coo: this.coo })
                    .then(response => {
                        this.date = new Date();
                        this.showMessage(response);
                    }).catch(error => {
                        this.showError(error);
                    }).then(() => {
                        window.scrollTo(0,275);
                        this.isLoading = false;
                    });
            },
            withdrawSubmission() {
                if (confirm(this.$t('deezer.confirm_withdraw_submission'))) {
                    this.loading = true;
                    axios.delete('/deezer/' + this.id)
                        .then(response => {
                            this.showMessage(response);
                            this.coo = null;
                        })
                        .catch(error => {
                            this.showError(error);
                        }).then(() => {
                            window.scrollTo(0,275);
                            this.loading = false;
                        });
                }
            }
        }
    }
</script>

<style scoped>

</style>
