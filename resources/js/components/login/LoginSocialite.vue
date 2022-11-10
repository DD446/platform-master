<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $t('Login in process', { name: this.name }) }}</div>

                    <div class="card-body">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">{{ $t('Loading...') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "LoginSocialite",

        props: {
            name: String,
        },

        methods: {
            login(){
                const self = this;

                let socialLogin = {
                    name: this.name
                };

                axios.post('/login/social', socialLogin)
                    .then((response) => {
                        window.location = response.data;
                    },
                        (error) => {
                            //eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : (typeof error === 'string' ? error : error.toString()));
                        })
                    .catch(error => {
                        eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : (typeof error === 'string' ? error : error.toString()));
                    });
            }
        },

        mounted() {
            this.login();
        }
    }
</script>

<style scoped>

</style>