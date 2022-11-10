<template>
    <div>
        <b-row>
            <b-col cols="12">
                <h3 class="h3">{{ getHeader }}</h3>
            </b-col>
        </b-row>
        <b-row class="mt-3">
            <b-col cols="9">
                {{ $t('feeds.is_submitted') }}
                <b-link :href="helpLink" v-if="helpLink" rel="noreferrer" target="_blank" v-b-popover.hover="{ content: $t('feeds.popover_help_link', { service: $t('feeds.submit_type_' + this.type) }) }">
<!--                    <b-icon
                        icon="info-circle"
                        variant="info"
                        v-b-popover.hover="{ content: $t('feeds.popover_help_link', { service: $t('feeds.submit_type_' + this.type) }) }"></b-icon>-->
                    <svg data-v-1f4a1f28="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="info circle" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-info-circle b-icon bi text-info"><g data-v-1f4a1f28=""><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"></path><path d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"></path></g></svg>
                </b-link>
                <span class="pl-3">
                    <span v-if="submitted === true">
                        <b-link
                            href="#"
                            @click.prevent="onDeleteLink"
                            v-b-popover.hover="{ content: $t('feeds.placeholder_delete_submit_link', { service: $t('feeds.submit_type_' + this.type) }) }">{{ $t('feeds.status_submitted_yes') }}</b-link>
                    </span>
                    <span v-if="submitted === false">
                        {{ $t('feeds.status_submitted_no') }}
                        <b-link href="#" v-b-popover.hover.blur="{ title: $t('feeds.title_add_link_to_podcast_entry', { service: $t('feeds.submit_type_' + this.type) }), content: $t('feeds.content_add_link_to_podcast_entry', { service: $t('feeds.submit_type_' + this.type) })}" @click.prevent="showCollapse = !showCollapse">
                            {{ $t('feeds.status_submitted_not_found') }}
                        </b-link>
                    </span>
                    <span v-if="submitted === null">
                        <b-link href="#" v-b-popover.hover.blur="{ title: $t('feeds.title_add_link_to_podcast_entry', { service: $t('feeds.submit_type_' + this.type) }), content: $t('feeds.content_add_link_to_podcast_entry', { service: $t('feeds.submit_type_' + this.type) })}" @click.prevent="showCollapse = !showCollapse">
                            {{ $t('feeds.status_submitted_unknown') }}
                        </b-link>
                    </span>
                    <b-collapse :id="'collapse-' + this.type" class="mt-2" v-model="showCollapse">
                        <b-form @submit.prevent="onSaveLink">
                            <b-input-group class="mb-3">
                                <b-input v-model="submitLink" type="url" required :placeholder="$t('feeds.placeholder_add_submit_link', { service: $t('feeds.submit_type_' + this.type), link: placeholderLink })" v-b-popover.hover.bottom.blur="{ content: $t('feeds.placeholder_add_submit_link', { service: $t('feeds.submit_type_' + this.type) }) }"></b-input>
                            </b-input-group>
                            <b-button type="submit" variant="primary">{{ $t('feeds.button_add_submit_link') }}</b-button>
                        </b-form>
                    </b-collapse>
                </span>
            </b-col>
            <b-col cols="3">
                <b-spinner :label="$t('feeds.label_loading')" v-show="isLoading"></b-spinner>
                <a href="#" class="btn btn-success btn-lg" v-if="link" @click.stop="openLink(link)">{{ $t('feeds.link_view_podcast_entry') }}</a>
                <a href="#" class="btn btn-primary btn-lg" v-if="submit" @click.stop="openLink(submit)">{{ $t('feeds.link_submit_podcast') }}</a>
                <span v-html="form" v-if="isForm"></span>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "SubmitItem",

        props: {
            type: String,
            feed: String,
        },

        data() {
            return {
                link: null,
                placeholderLink: null,
                submitLink: null,
                helpLink: null,
                submit: null,
                submitted: null,
                isLoading: true,
                showCollapse: false,
                isForm: false,
            }
        },

        methods: {
            getStatus() {
                axios.post('/feedsubmit', { type: this.type, feed: this.feed })
                    .then((response) => {
                            this.submitted = response.data.submitted;

                            if (!response.data.canValidate && !response.data.link) {
                                this.submitted = null;
                            }

                            if (response.data.submitted === false) {
                                if (response.data.isForm) {
                                    this.isForm = response.data.isForm;
                                    this.form = response.data.submit;
                                } else {
                                    this.submit = response.data.submit;
                                }
                                this.placeholderLink = response.data.placeholderLink;
                                this.helpLink = response.data.helpLink;
                            } else if (response.data.submitted === true) {
                                this.link = response.data.link;
                            }
                            this.isLoading = false;
                        }
                    );
            },
            openLink(url) {
                window.open(url);
                return false;
            },
            onSaveLink() {
                axios.put('/feedsubmit', { type: this.type, feed: this.feed, link: this.submitLink })
                    .then((response) => {
                            this.link = this.submitLink;
                            this.submitLink = null;
                            this.isForm = null;
                            this.showCollapse = this.submit = false;
                            this.submitted = true;
                            eventHub.$emit('show-message:success', response.data.message ? response.data.message : response.toString());
                        },
                        (error) => {
                            eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
                        })
                    .catch(error => {
                        eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
                    });
            },
            onDeleteLink() {
                if (confirm(this.$t('feeds.confirm_delete_link', { 'name': this.$t('feeds.submit_type_' + this.type)}))) {
                    axios.delete('/feedsubmit/' +  this.type + '/' + this.feed )
                        .then((response) => {
                                this.getStatus();
                                this.link = null;
                        },
                            (error) => {
                                eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
                            })
                        .catch(error => {
                            eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
                        });
                }
            }
        },

        created() {
            this.getStatus();
        },

        computed: {
            getHeader() {
                return this.$t('feeds.submit_type_' + this.type);
            },
            getSubmitStatus() {
                switch (this.submitted) {
                    case false :
                        return this.$t('feeds.status_submitted_no');
                    case true :
                        return this.$t('feeds.status_submitted_yes');
                    default:
                        let service = this.$t('feeds.submit_type_' + this.type);
                        return '<a href="#" title="' + this.$t('feeds.title_add_link_to_podcast_entry', { service: service }) + '">' + this.$t('feeds.status_submitted_unknown') + '</a>';
//                        return this.$t('feeds.status_submitted_unknown');
                }
            }
        }
    }
</script>

<style scoped>

</style>
