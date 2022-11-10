<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>

        <div class="text-center" v-show="loading">
            <div class="spinner-grow m-5 h-1" role="status">
                <span class="sr-only">{{ $t("pat.is_loading") }}</span>
            </div>
        </div>

        <div v-if="tokens.length > 0">
            <div class="card card-default">
                <div class="card-header">{{ $t('clients.header_authorized_clients') }}</div>

                <div class="card-body">
                    <!-- Authorized Tokens -->
                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>{{$t('clients.header_authorized_clients_name')}}</th>
                                <th>{{$t('clients.header_authorized_clients_scopes')}}</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="token in tokens">
                                <!-- Client Name -->
                                <td style="vertical-align: middle;">
                                    {{ token.client.name }}
                                </td>

                                <!-- Scopes -->
                                <td style="vertical-align: middle;">
                                    <span v-if="token.scopes.length > 0">
                                        {{ token.scopes.join(', ') }}
                                    </span>
                                </td>

                                <!-- Revoke Button -->
                                <td style="vertical-align: middle;">
                                    <b-button variant="warning" @click="revoke(token)">
                                        {{$t('clients.header_authorized_clients_revoke')}}
                                    </b-button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                tokens: [],

                loading: true
            };
        },

        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component (Vue 2.x).
             */
            prepareComponent() {
                this.getTokens();
            },

            /**
             * Get all of the authorized tokens for the user.
             */
            getTokens() {
                axios.get('/oauth/tokens')
                        .then(response => {
                            this.tokens = response.data;
                            this.loading = false;
                        });
            },

            /**
             * Revoke the given token.
             */
            revoke(token) {
                axios.delete('/oauth/tokens/' + token.id)
                        .then(response => {
                            this.getTokens();
                        });
            }
        }
    }
</script>
