<template>
    <b-modal
        ref="blogBackendModal"
        :title="$t('feeds.title_blog_backend_login')"
        :ok-title="$t('feeds.button_submit_blog_backend')"
        @ok="handleOk"
        :cancel-title="$t('feeds.button_cancel')"
        lazy>

        <b-form :action="subdomain + `/wp-login.php`" method="post" target="_blank" ref="blogBackendLoginForm">
            <input type="hidden" name="log" :value="loggedOnUser" />
            <input type="hidden" name="redirect_to" :value="subdomain + `/wp-admin/?testcookie=1`" />
            <b-form-group
                :label="$t('feeds.label_blog_backend_password')"
                :description="$t('feeds.description_blog_backend_password')"
            >
                <b-input
                    type="password"
                    name="pwd"
                    :value="subdomain + `/wp-admin/?testcookie=1`"
                    :placeholder="$t('feeds.placeholder_enter_your_blog_backend_password')"
                    required
                    tabindex="1"
                    accesskey="p"
                ></b-input>
            </b-form-group>
        </b-form>

    </b-modal>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "BlogBackendModal",

        data() {
            return {
                loading: true
            }
        },

        props: {
            feedId: String,
            subdomain: String,
            loggedOnUser: String,
        },

        methods: {
            show() {
                this.$refs.blogBackendModal.show();
            },
            checkFormValidity() {
                const valid = this.$refs.blogBackendLoginForm.checkValidity();
                this.linkState = valid;
                return valid;
            },
            handleOk(bvModalEvt) {
                // Exit when the form isn't valid
                if (!this.checkFormValidity()) {
                    bvModalEvt.preventDefault();
                    return;
                }
                this.$refs.blogBackendLoginForm.submit();
            }
        },

        computed: {
        },

        mounted() {
            eventHub.$on("blog-backend-modal:show:" + this.feedId, () => {
                this.show();
            });
        },

        created() {
        }
    }
</script>

<style scoped>
</style>
