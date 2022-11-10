<template>
    <div class="pt-5">
        <h4 class="display-4">{{ name }}</h4>

        <template v-if="date">
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
                                    v-b-popover.hover="$t('amazon.popover_withdraw_amazon_submission')"></b-btn-close>
                            {{ $t('amazon.text_podcast_added') }}
                        </p>
                    </b-overlay>
                </div>
            </div>
        </template>
        <template v-else>
            <b-overlay :show="isLoading" rounded="sm">
                <div class="row">
                    <div class="col-12">
                        <iframe src="/amazon/terms" style="max-height:275px;width:100%"></iframe>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mt-2">
                        <b-checkbox
                            size="lg"
                            v-model="toc"
                            name="terms_accepted"
                            value="yes"
                            unchecked-value="no"
                        >{{ $t('amazon.label_checkbox_terms') }}</b-checkbox>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-4 col-lg-2 mt-2">
                        <label for="country-selector">
                            {{ $t('amazon.label_country_of_origin') }}<span class="">*</span>
                        </label>
                    </div>
                    <div class="col-11 col-md-7 col-lg-9 mt-2">
                        <country-selector :feedId="feed_id"></country-selector>
                    </div>
                    <div class="col-1 mt-2">
                        <i class="icon icon-info-with-circle text-blue" v-b-popover.hover.click="$t('amazon.popover_country_of_origin')"></i>
                    </div>
                </div>
                <div class="row">
                    <b-col cols="12" class="mt-3">
                            <b-button
                                size="lg"
                                class="float-right"
                                variant="primary"
                                :disabled="toc === 'no' || !coo"
                                v-on:click="submitButton">{{ $t('amazon.text_button_add') }}</b-button>
                    </b-col>
                </div>
            </b-overlay>
        </template>
    </div>
</template>

<script>

    import CountrySelector from "../CountrySelector";
    import {eventHub} from "../../app";

    export default {
        name: "AmazonPodcastEntry",

        components: {
            CountrySelector
        },

        data() {
            return {
                isLoading: false,
                loading: false,
                id: null,
                name: null,
                toc: 'no',
                date: null,
                coo: null,
            }
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            eventHub.$on('country:selected:' + this.feed_id, (country) => {
                this.coo = country.code;
            });
        },

        created() {
            this.id = this.feed_id;
            this.name = this.feed_title;
            this.date = this.amazon['date'];
            this.coo = this.amazon['coo'];
        },

        props: {
            feed_id: String,
            feed_title: String,
            amazon: {
                type: Array,
                default: {date: null, coo: null},
            }
        },

        methods: {
            openInNewWindow() {
                //window.open();
            },
            submitButton() {
                this.isLoading = true;
                axios.post('/amazon', { id: this.id, toc: this.toc, coo: this.coo })
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
                if (confirm(this.$t('amazon.confirm_withdraw_submission'))) {
                    this.loading = true;
                    axios.delete('/amazon/' + this.id)
                        .then(response => {
                            this.showMessage(response);
                            this.date = this.coo = null;
                            this.toc = 'no'
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
