<template>
    <div>
        <b-button variant="outline-primary" @click.prevent="openLoginWindow">
            <i class="socicon-facebook"></i> {{ $t('login.text_login_with_facebook') }}
        </b-button>
    </div>
</template>

<script>
    export default {
        name: "LoginFacebook",

        methods: {
            openLoginWindow() {
                let strWindowFeatures = "menubar=no,location=no,resizable=yes,scrollbars=yes,status=no,left=100,top=100,height=800,width=650";
                window.open('/social/login/facebook', 'SocialLoginWindow', strWindowFeatures);
            }
        }
    }
</script>