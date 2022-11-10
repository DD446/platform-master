<template>
    <b-modal
        id="redirect-link-modal"
        ref="redirectLinkModal"
        :title="$t('feeds.title_set_redirect')"
        :ok-title="$t('feeds.title_button_set_redirect')"
        ok-variant="danger"
        :cancel-title="$t('feeds.title_button_abort')"
        @show="resetModal"
        @hidden="resetModal"
        @ok="handleOk"
        lazy>
        <b-row class="mb-3">
            <b-col>
                <span class="bg-warning">
                    {{ $t('feeds.hint_permanent_redirect') }}
                </span>
            </b-col>
        </b-row>
        <b-form ref="form">
            <b-form-group
                :label="$t('feeds.text_label_redirect_goal')"
                label-for="redirect-goal"
                label-sr-only
                :invalid-feedback="invalidFeedback"
                :state="state"
            >
                <b-input
                    id="redirect-goal"
                    :state="linkState"
                    type="url"
                    v-model="link" autofocus></b-input>
            </b-form-group>
        </b-form>
    </b-modal>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "RedirectLinkModal",

        data() {
            return {
                link: 'https://',
                linkState: null,
                type: 'rss',
            }
        },

        props: {
            feedId: String,
        },

        methods: {
            show() {
                this.$refs.redirectLinkModal.show();
            },
            resetModal() {
                this.feed = {};
                this.link = 'https://';
            },
            checkFormValidity() {
                const valid = this.$refs.form.checkValidity();
                this.linkState = valid;
                return valid;
            },
            handleOk(bvModalEvt) {
                // Prevent modal from closing
                bvModalEvt.preventDefault()
                // Trigger submit handler
                this.handleSubmit()
            },
            handleSubmit() {
                // Exit when the form isn't valid
                if (!this.checkFormValidity()) {
                    return;
                }

                let msg = this.$t('feeds.confirm_set_redirect_feed');

                if (this.type === 'blog') {
                    msg = this.$t('feeds.confirm_set_redirect_website');
                }

                if (confirm(msg)) {
                    eventHub.$emit('update:content:' + this.feedId, this.type, 'set-redirect', this.link);
                    this.closeModal();
                } else {
                    this.closeModal();
                }
            },
            closeModal() {
                // Hide the modal manually
                this.$nextTick(() => {
                    this.$bvModal.hide('redirect-link-modal')
                })
            }
        },

        computed: {
            state() {
                return this.link !== 'https://';
            },
            invalidFeedback() {
                return this.$t('feeds.text_hint_set_redirect_link');
            },
        },

        mounted() {
            eventHub.$on("redirect-link-modal:show:" + this.feedId, (type) => {
                this.type = type;
                this.show();
            });
        },
    }
</script>

<style scoped>
</style>
