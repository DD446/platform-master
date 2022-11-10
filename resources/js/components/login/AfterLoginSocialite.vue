<template>
    <b-container>
        <b-row>
            <b-col>
                <b-alert variant="success" show>
                    {{ $t('You are logged in successfully!') }}
                </b-alert>

                <b-link @click="closeWindow">{{ $t('Close window') }}</b-link>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
    export default {
        name: "AfterLoginSocialite",

        props: {
            accesstoken: null,
            credentials: null,
        },

        methods: {
            closeWindow() {
                window.close();
            }
        },

        mounted() {
            window.opener.location.reload();
            this.closeWindow();
        },

        created() {
            if (this.accesstoken) {
                this.$parent.$store.dispatch('login', this.accesstoken);
            }

            if (this.credentials) {
                this.$parent.$store.dispatch('saveCredentials', this.credentials);
            }
        }
    }
</script>