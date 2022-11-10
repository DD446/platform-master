<template>
    <b-card
            :title="$t('feeds.header_step_feed')"
            class="mb-2">
        <b-card-text>
            {{ $t('feeds.text_step_feed') }}
        </b-card-text>

        <b-form-group>
            <b-input
                autofocus
                type="url"
                v-model="feed.feedUrl"
                @blur="checkInput"
                :state="feedUrlState"
                aria-describedby="input-live-feedback"
                :placeholder="$t('feeds.placeholder_feed_url')"
                required></b-input>

            <!-- This will only be shown if the preceding input has an invalid state -->
            <b-form-invalid-feedback id="input-live-feedback">
                {{ $t('feeds.text_error_missing_valid_feed_url') }}
            </b-form-invalid-feedback>
        </b-form-group>

<!--        <b-form-group>
            <b-form-checkbox
                    id="checkbox-import-media"
                    v-model="importMedia"
                    name="import-media"
                    value="yes"
                    unchecked-value="no"
            >
                {{ $t('feeds.label_import_media') }}
            </b-form-checkbox>
        </b-form-group>-->
    </b-card>
</template>

<script>
    import {eventHub} from '../../../app';

    export default {
        name: "StepFeed",

        data() {
            return {
                importMedia: "yes",
                feedUrlState: null,
                feed: {
                    feedUrl: "",
                    rss: {
                        title: null,
                        description: null,
                        email: null,
                        author: null,
                        copyright: null,
                        link: null,
                    },
                    authors: null,
                    itunes: {
                        category: []
                    },
                    categories: null,
                    image: null
                },
                imgUri: null,
            }
        },

        methods: {
            async validate() {
                if(this.feed.feedUrl.length < 10) {
                    this.feedUrlState = false;
                    return false;
                }

                let valid = false;
                await axios.post('/feed/check', {url: this.feed.feedUrl})
                    .then(response => {
                        if (!response.data.passed) {
                            this.showError(response.data);
                            this.feedUrlState = false;
                        } else {
                            valid = true;
                            this.feed = Object.assign({}, this.feed, response.data.feed);
                            this.showMessage(response);
                        }
                    })
                    .catch(error => {
                        this.showError(error);
                    });

                if (valid) {
                    eventHub.$emit('on-validate', this.$data, true);
                }

                return valid;
            },

            checkInput() {
                if(this.feed.feedUrl.length > 10
                    && this.feed.feedUrl.startsWith('http')) {
                    this.feedUrlState = true;
                    return true;
                }
                return false;
            },
        },

        mounted() {
        }
    }
</script>

<style scoped>

</style>
