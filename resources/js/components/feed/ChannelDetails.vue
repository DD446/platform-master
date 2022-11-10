<template>
    <div class="mb-3 mb-lg-5">
        <b-overlay :show="isLoading" rounded="lg">
            <vue-form-generator
                v-if="isLoaded"
                :schema="schema"
                :model="model"
                :options="formOptions"></vue-form-generator>
<!--            @validated="onValidated"
            @model-updated="onModelUpdated"            -->
        </b-overlay>
    </div>
</template>

<script>
    import {eventHub} from '../../app';
    import VueFormGenerator from "vue-form-generator";
    import 'vue-form-generator/dist/vfg.css';
    import language from '../../language.json'; // TODO: I18N
    import itunescats from '../../itunes_categories.de.json';
    import googleplaycats from '../../googleplay_categories.de.json';

    export default {
        name: "ChannelDetails",

        components: {
            "vue-form-generator": VueFormGenerator.component,
        },

        props: {
            feedId: {
                type: String,
                required: true
            }
        },

        data () {
            return {
                model: {
                    feed_id: null,
                    rss: {
                        title: null,
                        description: null,
                        link: null,
                        author: null,
                        email: null,
                        copyright: null,
                        language: null,
                        category: null,
                    },
                    itunes: {
                        subtitle: null,
                        explicit: false,
                        block: 'no',
                        type: 'episodic',
                        complete: 'no',
                        category0: null,
                        category1: null,
                        category2: null,
                        'new-feed-url': null,
                    },
                    googleplay: {
                        author: null,
                        description: null,
                        category: null,
                        explicit: 'no',
                        block: 'no',
                    }
                },
                schema: {
                    fields: [
                        {
                            type: 'input',
                            inputType: 'hidden',
                            model: 'feed_id',
                            readonly: true,
                        }
                    ],
                    groups: [
                        {
                            styleClasses: "mt-3",
                            legend: this.$t('feeds.legend_common'),
                            fields: [
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('feeds.label_rss_title'),
                                    model: 'rss.title',
                                    required: true,
                                    styleClasses: "col-12",
                                    placeholder: this.$t('feeds.placeholder_rss_title'),
                                    min: 1,
                                    max: 255,
                                    validator: VueFormGenerator.validators.string.locale({
                                        fieldIsRequired: this.$t('feeds.validation_title_required'),
                                    }),
                                    help: this.$t('feeds.help_rss_title'),
                                },
                                {
                                    type: 'textArea',
                                    label: this.$t('feeds.label_rss_description'),
                                    model: 'rss.description',
                                    required: true,
                                    max: 4000,
                                    styleClasses: "col-12",
                                    placeholder: this.$t('feeds.placeholder_rss_description'),
                                    min: 1,
                                    validator: VueFormGenerator.validators.string.locale({
                                        fieldIsRequired: this.$t('feeds.validation_description_required'),
                                    }),
                                    help: this.$t('feeds.help_rss_description'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('feeds.label_rss_author'),
                                    model: 'rss.author',
                                    required: true,
                                    styleClasses: "col-md-6",
                                    placeholder: this.$t('feeds.placeholder_rss_author'),
                                    min: 2,
                                    validator: VueFormGenerator.validators.string.locale({
                                        fieldIsRequired: this.$t('feeds.validation_author_required'),
                                    }),
                                    help: this.$t('feeds.help_rss_author'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'email',
                                    label: this.$t('feeds.label_rss_email'),
                                    model: 'rss.email',
                                    required: true,
                                    styleClasses: "col-md-6",
                                    placeholder: this.$t('feeds.placeholder_rss_email'),
                                    min: 6,
                                    validator: [
                                        VueFormGenerator.validators.email.locale({
                                            invalidEmail: this.$t('feeds.validation_email_invalid'),
                                            fieldIsRequired: this.$t('feeds.validation_email_required'),
                                        })
                                    ],
                                    help: this.$t('feeds.help_rss_email'),
                                },
                                {
                                    type: 'input',
                                    inputType: 'url',
                                    label: this.$t('feeds.label_rss_link'),
                                    model: 'rss.link',
                                    styleClasses: "col-md-6",
                                    min: 6,
                                    help: this.$t('feeds.help_rss_link'),
                                    placeholder: this.$t('feeds.placeholder_rss_link'),
                                    validator: VueFormGenerator.validators.url.locale({
                                        fieldIsRequired: this.$t('feeds.validation_url_required'),
                                        invalidURL: this.$t('feeds.validation_url_invalid'),
                                    })
                                },
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('feeds.label_rss_copyright'),
                                    model: 'rss.copyright',
                                    required: true,
                                    styleClasses: "col-md-6",
                                    min: 6,
                                    validator: VueFormGenerator.validators.string.locale({
                                        fieldIsRequired: this.$t('feeds.validation_copyright_required'),
                                    }),
                                    placeholder: this.$t('feeds.placeholder_rss_copyright'),
                                    help: this.$t('feeds.help_rss_copyright'),
                                },
                                {
                                    type: "select",
                                    label: this.$t('feeds.label_rss_language'),
                                    model: 'rss.language',
                                    required: true,
                                    styleClasses: "col-md-6",
                                    min: 6,
                                    values: this.getLanguages(),
                                    help: this.$t('feeds.help_rss_language'),
                                    selectOptions: {
                                        noneSelectedText: this.$t('feeds.none_selected_language'),
                                        hideNoneSelectedText: false,
                                    },
                                },
                                {
                                    type: "input",
                                    inputType: 'text',
                                    label: this.$t('feeds.label_rss_category'),
                                    placeholder: this.$t('feeds.placeholder_rss_category'),
                                    model: "rss.category",
                                    styleClasses: "col-md-6",
                                    help: this.$t('feeds.help_rss_category'),
                                },
                            ]
                        },
                        {
                            styleClasses: "mt-3",
                            legend: this.$t('feeds.legend_itunes'),
                            fields: [
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('feeds.label_itunes_subtitle'),
                                    model: 'itunes.subtitle',
                                    styleClasses: "col-12",
                                    placeholder: this.$t('feeds.placeholder_itunes_subtitle'),
                                    min: 1,
                                    max: 255,
                                    help: this.$t('feeds.help_itunes_subtitle'),
                                },
                                {
                                    type: "select",
                                    label: this.$t('feeds.label_itunes_category'),
                                    model: 'itunes.category0',
                                    required: true,
                                    styleClasses: "col-md-4",
                                    values: this.getItunesCategories(),
                                    help: this.$t('feeds.help_itunes_category'),
                                    selectOptions: {
                                        noneSelectedText: this.$t('feeds.none_selected'),
                                        hideNoneSelectedText: false,
                                    },
                                },
                                {
                                    type: "select",
                                    label: this.$t('feeds.label_itunes_category1'),
                                    model: 'itunes.category1',
                                    required: false,
                                    styleClasses: "col-md-4",
                                    values: this.getItunesCategories(),
                                    selectOptions: {
                                        noneSelectedText: this.$t('feeds.no_choice'),
                                        hideNoneSelectedText: false,
                                    },
                                },
                                {
                                    type: "select",
                                    label: this.$t('feeds.label_itunes_category2'),
                                    model: 'itunes.category2',
                                    required: false,
                                    styleClasses: "col-md-4",
                                    values: this.getItunesCategories(),
                                    selectOptions: {
                                        noneSelectedText: this.$t('feeds.no_choice'),
                                        hideNoneSelectedText: false,
                                    },
                                },
                                {
                                    type: "select",
                                    label: this.$t('feeds.label_itunes_type'),
                                    model: 'itunes.type',
                                    required: true,
                                    styleClasses: "col-md-6",
                                    values: [
                                        {
                                            id: 'episodic',
                                            name: this.$t('feeds.itunes_type_episodic')
                                        },
                                        {
                                            id: 'serial',
                                            name: this.$t('feeds.itunes_type_serial')
                                        }
                                    ],
                                    help: this.$t('feeds.help_itunes_type'),
                                    selectOptions: {
                                        noneSelectedText: this.$t('feeds.none_selected'),
                                        hideNoneSelectedText: false,
                                    },
                                },
                                {
                                    type: "select",
                                    label: this.$t('feeds.label_itunes_explicit'),
                                    model: 'itunes.explicit',
                                    required: true,
                                    styleClasses: "col-md-6",
                                    values: [
                                        {
                                            id: 'no',
                                            name: this.$t('feeds.select_no')
                                        },
                                        {
                                            id: 'yes',
                                            name: this.$t('feeds.select_yes')
                                        }
                                    ],
                                    help: this.$t('feeds.help_itunes_explicit'),
                                    selectOptions: {
                                        noneSelectedText: this.$t('feeds.none_selected'),
                                        hideNoneSelectedText: false,
                                    },
                                },
                                {
                                    type: "select",
                                    label: this.$t('feeds.label_itunes_complete'),
                                    model: 'itunes.complete',
                                    required: true,
                                    styleClasses: "col-md-6",
                                    default: 'no',
                                    values: [
                                        {
                                            id: 'no',
                                            name: this.$t('feeds.select_no')
                                        },
                                        {
                                            id: 'yes',
                                            name: this.$t('feeds.select_yes')
                                        }
                                    ],
                                    help: this.$t('feeds.help_itunes_complete'),
                                    selectOptions: {
                                        noneSelectedText: this.$t('feeds.none_selected'),
                                        hideNoneSelectedText: false,
                                    },
                                },
                                {
                                    type: "select",
                                    label: this.$t('feeds.label_itunes_block'),
                                    model: 'itunes.block',
                                    required: true,
                                    styleClasses: "col-md-6",
                                    default: 'no',
                                    values: [
                                        {
                                            id: 'no',
                                            name: this.$t('feeds.select_no')
                                        },
                                        {
                                            id: 'yes',
                                            name: this.$t('feeds.select_yes')
                                        }
                                    ],
                                    help: this.$t('feeds.help_itunes_block'),
                                    selectOptions: {
                                        noneSelectedText: this.$t('feeds.none_selected'),
                                        hideNoneSelectedText: false,
                                    },
                                },
                                {
                                    type: 'input',
                                    inputType: 'url',
                                    label: this.$t('feeds.label_itunes_new-feed-url'),
                                    model: 'itunes.new-feed-url',
                                    styleClasses: "col-12",
                                    min: 6,
                                    help: this.$t('feeds.help_itunes_new-feed-url'),
                                    placeholder: this.$t('feeds.placeholder_itunes_new-feed-url'),
                                    validator: VueFormGenerator.validators.url.locale({
                                        invalidURL: this.$t('feeds.validation_url_invalid'),
                                    })
                                },
                            ]
                        },
                        {
                            styleClasses: "mt-3",
                            legend: this.$t('feeds.legend_google'),
                            fields: [
                                {
                                    type: 'textArea',
                                    label: this.$t('feeds.label_google_description'),
                                    model: 'googleplay.description',
                                    max: 4000,
                                    styleClasses: "col-12",
                                    placeholder: this.$t('feeds.placeholder_google_description'),
                                    min: 1,
                                    help: this.$t('feeds.help_google_description'),
                                },
                                {
                                    type: "select",
                                    label: this.$t('feeds.label_google_category'),
                                    model: 'googleplay.category',
                                    required: true,
                                    styleClasses: "col-md-6",
                                    values: this.getGooglePlayCategories(),
                                    help: this.$t('feeds.help_google_category'),
                                    selectOptions: {
                                        noneSelectedText: this.$t('feeds.none_selected'),
                                        hideNoneSelectedText: false,
                                    },
                                },
                                {
                                    type: "select",
                                    label: this.$t('feeds.label_google_explicit'),
                                    model: 'googleplay.explicit',
                                    required: true,
                                    styleClasses: "col-md-6",
                                    values: [
                                        {
                                            id: 'no',
                                            name: this.$t('feeds.select_no')
                                        },
                                        {
                                            id: 'yes',
                                            name: this.$t('feeds.select_yes')
                                        }
                                    ],
                                    help: this.$t('feeds.help_itunes_explicit'),
                                    selectOptions: {
                                        noneSelectedText: this.$t('feeds.none_selected'),
                                        hideNoneSelectedText: false,
                                    },
                                },
                                {
                                    type: "select",
                                    label: this.$t('feeds.label_google_block'),
                                    model: 'googleplay.block',
                                    required: true,
                                    styleClasses: "col-md-6",
                                    default: 'no',
                                    values: [
                                        {
                                            id: 'no',
                                            name: this.$t('feeds.select_no')
                                        },
                                        {
                                            id: 'yes',
                                            name: this.$t('feeds.select_yes')
                                        }
                                    ],
                                    help: this.$t('feeds.help_google_block'),
                                    selectOptions: {
                                        noneSelectedText: this.$t('feeds.none_selected'),
                                        hideNoneSelectedText: false,
                                    },
                                },
                            ]
                        },
/*                        {
                            styleClasses: "mt-3",
                            legend: this.$t('feeds.legend_defaults'),
                            fields: [
                                {
                                    type: 'input',
                                    inputType: 'text',
                                    label: this.$t('feeds.label_default_title'),
                                    model: 'settings.default_item_title',
                                    styleClasses: "col-12",
                                    placeholder: this.$t('feeds.placeholder_default_title'),
                                    min: 1,
                                    max: 255,
                                    help: this.$t('feeds.help_default_title'),
                                },
                                {
                                    type: 'textArea',
                                    label: this.$t('feeds.label_default_description'),
                                    model: 'settings.default_item_description',
                                    max: 4000,
                                    styleClasses: "col-12",
                                    placeholder: this.$t('feeds.placeholder_default_description'),
                                    min: 1,
                                    help: this.$t('feeds.help_default_description'),
                                },
                            ]
                        },*/
/*                        {
                            styleClasses: "mt-3",
                            legend: this.$t('feeds.legend_approvals'),
                            fields: []
                        },
                        {
                            styleClasses: "mt-3",
                            legend: this.$t('feeds.legend_extras'),
                            fields: []
                        },*/
                        {
                            styleClasses: "mt-3 mb-2",
                            fields: [
                                {
                                    /*styleClasses: "btn btn-primary float-right",*/
                                    type: 'submit',
                                    buttonText: this.$t('feeds.button_save_details'),
                                    styleClasses: "mt-2",
                                    onSubmit: () => {
                                        return this.onSubmit();
                                    },
                                    validateBeforeSubmit: true,
                                },
                            ]
                        }
                    ],
                },
                formOptions: {
                    validateAfterLoad: true,
                    validateAfterChanged: true,
                    validateAsync: true
                },
                errors: [],
                isLoaded: false,
                isLoading: false,
            }
        },

        methods: {
            init() {
                this.emitPageInfo();
                Object.assign(this.$data.model, this.feed.attributes);
                this.$data.model.feed_id = this.feedId;
                if (!this.$data.model.itunes.complete) {
                    this.$data.model.itunes.complete = 'no';
                }
                this.$data.model.itunes.category0 = this.feed.attributes.itunes.category[0];
                this.$data.model.itunes.category1 = this.feed.attributes.itunes.category[1];
                this.$data.model.itunes.category2 = this.feed.attributes.itunes.category[2];
                this.$data.model.itunes.category = [];

                if (!this.$data.model.googleplay || Array.isArray(this.$data.model.googleplay) && this.$data.model.googleplay.length === 0) {
                    this.$data.model.googleplay = {
                        description: null,
                        category: null,
                        explicit: 'no',
                        block: 'no',
                    };
                }

                this.isLoaded = true;
            },
/*            onValidated(state) {
            },
            onModelUpdated(value, field) {
            },*/
            onSubmit(e) {
                window.scrollTo(0,275);
                this.isLoading = true;
                this.model.itunes.category = [];
                let i = 0;
                if (this.model.itunes.category0) {
                    this.model.itunes.category[i] = this.model.itunes.category0;
                    i++;
                }
                if (this.model.itunes.category1) {
                    this.model.itunes.category[i] = this.model.itunes.category1;
                    i++;
                }
                if (this.model.itunes.category2) {
                    this.model.itunes.category[i] = this.model.itunes.category2;
                    i++;
                }

                axios.put('/api/feeds/' + this.model.feed_id, this.model)
                    .then((response) => {
                        eventHub.$emit('show-message:success', response.data);
                    }).catch((error) => {
                        eventHub.$emit('show-message:error', error.response.data.errors ? error.response.data.errors : error);
                    }).then(() => {
                        this.isLoading = false;
                    });
            },
            getLanguages() {
                let langs = [];
                Object.entries(language).forEach(([key, value]) => {
                    let lang = {
                        id: key,
                        name: value
                    };
                    langs.push(lang);
                });

                return langs;
            },
            getItunesCategories() {
                var cats = [];
                itunescats.forEach(function(cat) {
                    cats.push({ id: cat.value, name: cat.text })
                });

                return cats;
            },
            getGooglePlayCategories() {
                var cats = [];
                googleplaycats.forEach(function(cat) {
                    cats.push({ id: cat.value, name: cat.text })
                });

                return cats;
            },
            getFeed() {
                this.isLoading = true;
                axios.get('/api/feeds/' + this.feedId)
                    .then((response) => {
                        this.feed = response.data;
                        this.init();
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                        this.isLoading = false;
                    });
            },
            emitPageInfo() {
                let items = [{
                    text: this.feed.attributes.title,
                    href: '#/podcast/' + this.feedId,
                },{
                    text: this.$t('nav.feed_details'),
                    href: '#/podcast/' + this.feedId + '/details',
                }];

                let page = {
                    header: this.$t('feeds.header_channel'),
                    subheader: this.$t('feeds.subheader_channel', {title: this.feed.attributes.title}),
                }
                eventHub.$emit('podcasts:page:infos', items, page);
            }
        },

        mounted() {
            this.getFeed();
        }
    }
</script>
