<template>
    <b-card
            :title="$t('feeds.header_step_url')"
            class="mb-2">
        <b-card-text>
            {{ $t('feeds.text_step_url') }}
        </b-card-text>

        <b-card-text>
            <b-row>
                <b-col cols="12" lg="5" class="pr-lg-0">
                    <b-input-group
                        prepend="https://">
                        <b-input
                            name="subdomain"
                            v-model="feed.domain.subdomain"
                            required
                            trim
                            autofocus
                            :state="subdomainState"
                            :placeholder="$t('feeds.placeholder_subdomain')"
                            pattern="[a-z\-0-9]{3,25}"
                            aria-describedby="input-live-feedback-subdomain"
                            type="text"></b-input>
                    </b-input-group>

                    <b-form-invalid-feedback id="input-live-feedback-subdomain">
                        {{ $t('feeds.text_error_subdomain') }}
                    </b-form-invalid-feedback>
                </b-col>
                <b-col cols="12" lg="3" class="pl-lg-0 pr-lg-0 pt-lg-0 pt-sm-1 pt-1">
                    <b-input-group
                        prepend=".">
                        <b-select
                            v-model="feed.domain.domain"
                            required
                            :state="domainState"
                            :aria-placeholder="$t('feeds.placeholder_domains')"
                            :options="dat.domains"
                        ></b-select>
                    </b-input-group>
                </b-col>
                <b-col cols="12" lg="4" class="pl-lg-0 pt-lg-0 pt-sm-1 pt-1">
                    <b-input-group
                        prepend="/"
                        append=".rss">
                        <b-input
                            type="text"
                            v-model="feed.feed_id"
                            required
                            trim
                            :state="feedIdState"
                            :placeholder="$t('feeds.placeholder_feed_id')"
                            aria-describedby="input-live-feedback-feed-id"
                            pattern="^[a-zA-Z0-9]+(-[a-zA-Z0-9]+)*$"></b-input>
                    </b-input-group>

                    <b-form-invalid-feedback id="input-live-feedback-feed-id">
                        {{ $t('feeds.text_error_feed_id') }}
                    </b-form-invalid-feedback>
                </b-col>
            </b-row>

            <span class="help-block" v-show="feed.domain.subdomain && feed.domain.domain && feed.feed_id">
                <div class="pt-1">
                    So wird der Link zu Deinem Podcast-Feed aussehen: <span class="font-italic font-weight-bolder">https://{{ feed.domain.subdomain }}.{{ feed.domain.domain }}/{{ feed.feed_id }}.rss</span>
                </div>
                <div>
                    ...und so der Link zu Deinem podcaster Blog: <span class="font-italic font-weight-bolder">https://{{ feed.domain.subdomain }}.{{ feed.domain.domain }}</span>
                </div>
            </span>
        </b-card-text>

    </b-card>
</template>

<script>
    import {eventHub} from "../../../app";

    export default {
        name: "StepUrl",

        data() {
            return {
                feed: {
                    domain: {
                        subdomain: null,
                        domain: 'podcaster.de'
                    },
                    feed_id: null,
                },
                subdomainState: null,
                domainState: null,
                feedIdState: null,
            }
        },

        props: {
            dat: {
                type: Object,
                default: {}
            }
        },

        methods: {
            async validate() {
                let hasError = false;

                this.subdomainState = true;
                this.domainState = true;
                this.feedIdState = true;

                if (!this.feed.domain.domain) {
                    this.domainState = false;
                    hasError = true;
                }
                let minLength = 3;

                if (this.dat.canUseShortSubdomains) {
                    minLength = 2;
                }

                if (this.feed.domain.subdomain.length < minLength) {
                    this.subdomainState = false;
                    hasError = true;
                }
                if (!this.feed.feed_id) {
                    this.feedIdState = false;
                    hasError = true;
                }

                if (hasError) {
                    return !hasError;
                }

                // TODO: Check feed_id for availability
                    await axios.post('/api/feed/url/check', this.$data.feed)
                        .then(response => {
                        })
                        .catch(error => {
                            hasError = true;
                            this.showError(error);
                            for (let key in error.response.data.errors) {
                                switch (key) {
                                    case 'feed_id':
                                        this.feedIdState = false;
                                        break;
                                    case 'domain.subdomain':
                                        this.subdomainState = false;
                                        break;
                                }
                            }
                        })
                        .then(() => {
                            window.scrollTo(0,0);
                        });

                if (!hasError) {
                    eventHub.$emit('on-validate', this.$data, true);
                }

                return !hasError;
            },
            slug(str) {
                str = str.replace(/^\s+|\s+$/g, ''); // trim
                str = str.toLowerCase();

                // remove accents, swap ñ for n, etc
                var from = "àáäâẽèéëêìíïîòóôùúûñç·/_,:;";
                var to   = "aaaaeeeeeiiiiooouuunc------";
                for (var i=0, l=from.length ; i<l ; i++) {
                    str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
                }
                str = str.replace(new RegExp('ä', 'g'), 'ae');
                str = str.replace(new RegExp('ö', 'g'), 'oe');
                str = str.replace(new RegExp('ü', 'g'), 'ue');
                str = str.replace(new RegExp('ß', 'g'), 'ss');
                str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                    .replace(/\s+/g, '-') // collapse whitespace and replace by -
                    .replace(/-+/g, '-'); // collapse dashes

                str = str.replace(/([-]*)$/g, '');

                return str.trim();
            }
        },

        watch: {
            dat: function(newVal, oldVal) {
                if (!this.feed.feed_id) {
                    this.feed.feed_id = this.slug(newVal.rss.title);
                }

                if (!this.feed.domain.subdomain) {
                    this.feed.domain.subdomain = newVal.username;
                } else {
                    this.feed.domain.subdomain = this.feed.domain.subdomain.toLowerCase();
                }
            }
        }
    }
</script>
