<template>
    <b-form>
        <b-card
            :title="$t('feeds.header_step_meta')"
            class="mb-2">
        <b-card-text>
            {{ $t('feeds.text_step_meta') }}
        </b-card-text>

        <b-card-text>
            <b-form-group
                label-size="lg"
                label-for="title"
                class="mb-0"
            >
                <template slot="label">
                    {{$t('feeds.label_rss_title')}}
                    <i class="icon icon-info-with-circle text-blue"
                       v-b-popover.hover.click="$t('feeds.help_rss_title')"></i>
                </template>
                <b-input
                    id="title"
                    type="text"
                    v-model="feed.rss.title"
                    autofocus
                    required
                    trim
                    maxlength="255"
                    :state="titleState"
                    aria-describedby="input-live-feedback-title"
                    :placeholder="$t('feeds.placeholder_rss_title')"
                    >
                </b-input>

                <!-- This will only be shown if the preceding input has an invalid state -->
                <b-form-invalid-feedback id="input-live-feedback-title">
                    {{ $t('feeds.text_error_missing_title') }}
                </b-form-invalid-feedback>
            </b-form-group>
        </b-card-text>

        <b-card-text>
            <b-form-group
                label-size="lg"
                label-for="author"
                class="mb-0"
            >
                <template slot="label">
                    {{$t('feeds.label_rss_author')}}
                    <i class="icon icon-info-with-circle text-blue" v-b-popover.hover.click="$t('feeds.help_rss_author')"></i>
                </template>
                <b-input
                    id="author"
                    type="text"
                    v-model="feed.rss.author"
                    required
                    trim
                    maxlength="255"
                    :state="authorState"
                    aria-describedby="input-live-feedback-author"
                    :placeholder="$t('feeds.placeholder_rss_author')"
                    >
                </b-input>

                <!-- This will only be shown if the preceding input has an invalid state -->
                <b-form-invalid-feedback id="input-live-feedback-author">
                    {{ $t('feeds.text_error_missing_author') }}
                </b-form-invalid-feedback>
            </b-form-group>
        </b-card-text>

        <b-card-text>
            <b-form-group
                label-size="lg"
                label-for="description"
                class="mb-0"
            >
                <template slot="label">
                    {{$t('feeds.label_rss_description')}}
                    <i class="icon icon-info-with-circle text-blue" v-b-popover.hover.click="$t('feeds.help_rss_description')"></i>
                </template>
                <b-textarea
                    id="description"
                    type="text"
                    v-model="feed.rss.description"
                    trim
                    required
                    maxlength="4000"
                    rows="10"
                    :state="descriptionState"
                    aria-describedby="input-live-feedback-description"
                    :placeholder="$t('feeds.placeholder_rss_description')"
                    >
                </b-textarea>

                <!-- This will only be shown if the preceding input has an invalid state -->
                <b-form-invalid-feedback id="input-live-feedback-description">
                    {{ $t('feeds.text_error_missing_description') }}
                </b-form-invalid-feedback>
            </b-form-group>
        </b-card-text>

        <b-card-text>
            <div class="row">
                <b-col cols="12" lg="4" v-for="(c,key) in feed.itunes.category" :key="key">
                    <b-form-group
                        label-size="lg"
                        :label-for="`category-` + key"
                        class="mb-0">
                        <template slot="label">
                            {{$t('feeds.label_rss_category')}}
                            <i class="icon icon-info-with-circle text-blue" v-b-popover.hover.click="$t('feeds.help_itunes_category')" v-show="key === 0"></i>
                        </template>
                        <b-select
                            :id="`category-` + key"
                            v-model="feed.itunes.category[key]"
                            :state="key === 0 ? categoryState : null"
                            aria-describedby="input-live-feedback-category"
                            :required="key === 0"
                            :options="categoryOptions"
                        ></b-select>

                        <!-- This will only be shown if the preceding input has an invalid state -->
                        <b-form-invalid-feedback id="input-live-feedback-category">
                            {{ $t('feeds.text_error_missing_category') }}
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
            </div>
        </b-card-text>

            <b-card-text>
                <b-form-group
                    label-size="lg"
                    label-for="email"
                    class="mb-0"
                >
                    <template slot="label">
                        {{$t('feeds.label_rss_email')}}
                        <i class="icon icon-info-with-circle text-blue" v-b-popover.hover.click="$t('feeds.help_rss_email')"></i>
                    </template>
                    <b-input
                        id="email"
                        type="email"
                        v-model="feed.rss.email"
                        trim
                        required
                        maxlength="255"
                        :state="emailState"
                        aria-describedby="input-live-feedback-email"
                        :placeholder="$t('feeds.placeholder_rss_email')"
                    ></b-input>

                    <!-- This will only be shown if the preceding input has an invalid state -->
                    <b-form-invalid-feedback id="input-live-feedback-email">
                        {{ $t('feeds.text_error_missing_email') }}
                    </b-form-invalid-feedback>
                </b-form-group>
            </b-card-text>

        <b-card-text>
            <b-form-group
                label-size="lg"
                label-for="copyright"
                class="mb-0"
            >
                <template slot="label">
                    {{$t('feeds.label_rss_copyright')}}
                    <i class="icon icon-info-with-circle text-blue" v-b-popover.hover.click="$t('feeds.help_rss_copyright')"></i>
                </template>
                <b-input
                    id="copyright"
                    type="text"
                    maxlength="255"
                    v-model="feed.rss.copyright"
                    trim
                    required
                    :state="copyrightState"
                    aria-describedby="input-live-feedback-copyright"
                    :placeholder="$t('feeds.placeholder_rss_copyright')"
                >
                </b-input>

                <!-- This will only be shown if the preceding input has an invalid state -->
                <b-form-invalid-feedback id="input-live-feedback-copyright">
                    {{ $t('feeds.text_error_missing_copyright') }}
                </b-form-invalid-feedback>
            </b-form-group>
        </b-card-text>

            <b-card-text>
                <div class="row">
                    <b-col cols="12" lg="4">
                        <b-form-group
                            label-size="lg"
                            label-for="lang"
                            class="mb-0">
                            <template slot="label">
                                {{$t('feeds.label_rss_lang')}}
                                <i class="icon icon-info-with-circle text-blue" v-b-popover.hover.click="$t('feeds.help_itunes_lang')"></i>
                            </template>
                            <b-select
                                id="lang"
                                v-model="feed.rss.language"
                                :state="langState"
                                aria-describedby="input-live-feedback-lang"
                                required
                                :options="langOptions"
                            ></b-select>

                            <b-form-invalid-feedback id="input-live-feedback-lang">
                                {{ $t('feeds.text_error_missing_lang') }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-col>
                </div>
            </b-card-text>

        <b-card-text>
            <b-form-group
                label-size="lg"
                label-for="link"
                class="mb-0"
            >
                <template slot="label">
                    {{$t('feeds.label_rss_link')}}
                    <i class="icon icon-info-with-circle text-blue" v-b-popover.hover.click="$t('feeds.help_rss_link')"></i>
                </template>
                <b-input
                    id="link"
                    type="url"
                    trim
                    maxlength="255"
                    v-model="feed.rss.link"
                    :state="linkState"
                    aria-describedby="input-live-feedback-link"
                    :placeholder="$t('feeds.placeholder_rss_link')"
                >
                </b-input>

                <!-- This will only be shown if the preceding input has an invalid state -->
                <b-form-invalid-feedback id="input-live-feedback-link">
                    {{ $t('feeds.text_error_link') }}
                </b-form-invalid-feedback>
            </b-form-group>
        </b-card-text>

    </b-card>
    </b-form>
</template>

<script>
    import {eventHub} from "../../../app";

    export default {
        name: "StepMeta",

        data() {
            return {
                feed: this.dat,
                titleState: null,
                authorState: null,
                emailState: null,
                categoryState: null,
                langState: null,
                copyrightState: null,
                linkState: null,
                descriptionState: null,
                categoryOptions: [],
                langOptions: []
            }
        },

        props: {
             dat: {
                 type: Object,
                 default: {
                     itunes: {
                         category: [[],[],[]]
                     },
                     rss: {
                         language: []
                     }
                 }
             }
        },

        methods: {
            validate() {
                let hasError = false;
                this.titleState = true;
                this.authorState = true;
                this.emailState = true;
                this.copyrightState = true;
                this.descriptionState = true;
                this.categoryState = true;
                this.linkState = true;
                this.langState = true;

                if(!this.feed.rss.title || this.feed.rss.title.length > 255) {
                    this.titleState = false;
                    hasError = true;
                }

                if(!this.feed.rss.author || this.feed.rss.author.length > 255) {
                    this.authorState = false;
                    hasError = true;
                }

                if(!this.feed.rss.email || this.feed.rss.email.length < 5 || !this.feed.rss.email.includes('@')) {
                    this.emailState = false;
                    hasError = true;
                }

                if(!this.feed.rss.copyright || this.feed.rss.copyright.length > 255) {
                    this.copyrightState = false;
                    hasError = true;
                }

                if(!this.feed.rss.description || this.feed.rss.description.length > 4000) {
                    this.descriptionState = false;
                    hasError = true;
                }

                if(!this.feed.itunes.category[0]
                    || this.feed.itunes.category[0] === this.feed.itunes.category[1]
                    || this.feed.itunes.category[0] === this.feed.itunes.category[2]
                    || this.feed.itunes.category[1] && this.feed.itunes.category[1] === this.feed.itunes.category[2]
                ) {
                    this.categoryState = false;
                    hasError = true;
                }

                if(this.feed.rss.link
                    && this.feed.rss.link.length > 0
                    && !this.feed.rss.link.startsWith('http')
                    && !this.feed.rss.link.includes('://')) {
                    this.linkState = false;
                    hasError = true;
                }

                if (!this.feed.rss.language) {
                    this.langState = false;
                    hasError = true;
                }

                if (!hasError) {
                    eventHub.$emit('on-validate', this.$data, true);
                }

                return !hasError;
            },
            getLanguages() {
                axios.get('/api/list/lang?grouped')
                    .then(response => {
                        this.langOptions = response.data;
                    })
                    .catch(error => {
                        // handle error
                        eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
                    });
            },
            getItunes() {
                axios.get('/api/list/itunes')
                    .then(response => {
                        this.categoryOptions = response.data;
                    })
                    .catch(error => {
                        // handle error
                        eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
                    });
            }
        },

        mounted() {
            this.getLanguages();
            this.getItunes();
        },

        created() {
        },

        computed: {
        },

        watch: {
            dat(newVal) {
                this.feed = newVal;

                while (this.feed.itunes.category.length < 3) {
                    this.feed.itunes.category.push([]);
                }
            },
            feed(newVal) {
                this.feed.itunes.category_trans = [];
                for (var i in newVal.itunes.category) {
                    var cat = newVal.itunes.category[i];
                    var _cat = this.categoryOptions.filter(o => { return o.value === cat});
                    if (_cat && _cat.length > 0) {
                        this.feed.itunes.category_trans.push(_cat[0].text);
                    }
                }
            }
        },
    }
</script>

<style scoped>

</style>
