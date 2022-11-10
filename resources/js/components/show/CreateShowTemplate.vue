<template>
    <b-modal
        :ok-title="$t('shows.ok_title_create_show_template')"
        :cancel-title="$t('shows.cancel_title_create_show_template')"
        :title="$t('shows.modal_title_create_show_template')"
        ref="createShowTemplateModal"
        @shown="focusTitle"
        @ok="handleOk"
        id="create-show-template-modal"
    >
        <validation-observer ref="observer" v-slot="{ invalid, valid, validated, passes, reset }">
            <b-form
            ref="addShowTemplateForm"
        >
            <b-form-group
                :label="$t('shows.label_create_show_template_title')"
            >
                <validation-provider
                    vid="name"
                    :rules="{ required: true, max: 120 }"
                    v-slot="validationContext"
                    :name="$t('shows.validation_name')">
                        <b-form-input
                            required
                            id="name"
                            max="120"
                            v-model="form.name"
                            ref="focusFirst"
                            :state="getValidationState(validationContext)"
                        ></b-form-input>
                    <b-form-invalid-feedback
                        id="input-name-live-feedback">{{ validationContext.errors[0] }}
                    </b-form-invalid-feedback>
                </validation-provider>
            </b-form-group>
            <b-form-group
                :label="$t('shows.label_create_show_template_feeds')"
            >
                <b-form-select
                    v-model="form.feed_id"
                    :options="feeds"
                ></b-form-select>
            </b-form-group>
        </b-form>
        </validation-observer>
    </b-modal>
</template>

<script>
import {eventHub} from "../../app";
import Form from "vform";

import { ValidationProvider, ValidationObserver, extend, localize } from 'vee-validate';
import * as rules from "vee-validate/dist/rules";
import de from 'vee-validate/dist/locale/de.json';
localize('de', de);
Object.keys(rules).forEach(rule => {
    extend(rule, rules[rule]);
});

export default {
    name: "CreateShowTemplate",

    components: {
        ValidationProvider,
        ValidationObserver,
    },

    data() {
        return {
            form: new Form({
                name: null,
                feed_id: null,
                title: null,
                description: null,
                author: null,
                copyright: null,
                link: null,
                itunes_title: null,
                itunes_subtitle: null,
                itunes_summary: null,
                itunes_episode_type: null,
                itunes_season: null,
                itunes_explicit: null,
                is_public: null,
            }),
            feeds: [],
            isLoading: false,
            url: '/api/show/templates'
        }
    },

    mounted() {
        eventHub.$on("create-show-template-modal:show", (f) => {
            this.form.title = f.title;
            this.form.description = f.description;
            this.form.author = f.author;
            this.form.copyright = f.copyright;
            this.form.link = f.link;
            this.form.itunes_title = f.itunes.title;
            this.form.itunes_subtitle = f.itunes.subtitle;
            this.form.itunes_summary = f.itunes.summary;
            this.form.itunes_episode_type = f.itunes.episodeType;
            this.form.itunes_season = f.itunes.season;
            this.form.itunes_explicit = f.itunes.explicit;
            this.form.itunes_logo = f.itunes.logo;
            this.form.is_public = f.is_public;
            this.show();
        });
        this.getFeeds();
    },

    methods: {
        focusTitle() {
            this.$refs.focusFirst.focus();
        },
        show() {
            this.$refs.createShowTemplateModal.show();
        },
        save() {
            this.isLoading = true;
            try {
                this.form.post(this.url)
                    .then((response) => {
                        this.showMessage(response);
                        this.isSuccess = true;
                    })
                    .catch((error) => {
                        this.showError(error);
                    }).then(() => {
                    this.isLoading = false;
                });
            } catch (e) {
                this.showError(e);
                this.isLoading = false;
            }

            this.$bvModal.hide('createShowTemplateModal');
        },
        getFeeds() {
            axios.get('/api/feeds')
                .then((response) => {
                    let feeds = response.data.data;
                    let _channels = [];
                    _channels.push({ value: null, text: this.$t('shows.text_type_select_item'), disabled: true });
                    _channels.push({ value: null, text: this.$t('shows.text_type_select_separator'), disabled: true });
                    if (feeds.length > 0) {
                        feeds.forEach(function (feed) {
                            let _feed = {
                                value: feed.id, text: feed.attributes.title
                            };
                            _channels.push(_feed);
                        });
                    } else {
                        let _feed = {
                            value: null, text: this.$t('player.message_no_channel_entry'), disabled: true
                        };
                        _channels.push(_feed);
                    }
                    this.feeds = _channels;
                })
                .catch(error => {
                    this.showError(error);
                });
        },
        checkFormValidity() {

        },
        handleOk(bvModalEvent) {
            bvModalEvent.preventDefault();

            this.handleSubmit();
        },
        handleSubmit() {
            const valid = this.$refs.addShowTemplateForm.checkValidity();

            if (!valid) {
                return;
            }

            this.$nextTick(() => {
                this.save();
            })
        },
        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        }
    }
}
</script>

<style scoped>

</style>
