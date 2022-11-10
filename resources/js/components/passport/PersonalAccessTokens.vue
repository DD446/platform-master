<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>
        <div>
            <div class="card card-default">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>
                            {{ $t('pat.header_personal_access_tokens') }}
                        </span>

                        <b-button v-b-modal.modal-create-token>{{ $t("pat.create_new_token") }}</b-button>
                    </div>
                </div>

                <div class="card-body">

                    <div class="text-center" v-show="loading">
                        <div class="spinner-grow m-5 h-1" role="status">
                            <span class="sr-only">{{ $t("pat.is_loading") }}</span>
                        </div>
                    </div>

                    <!-- No Tokens Notice -->
                    <p class="mb-0" v-if="tokens.length === 0">
                        {{ $t('pat.hint_no_tokens') }}
                    </p>

                    <!-- Personal Access Tokens -->
                    <table class="table table-borderless mb-0" v-if="tokens.length > 0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="token in tokens">
                                <!-- Client Name -->
                                <td style="vertical-align: middle;">
                                    {{ token.name }}
                                </td>

                                <!-- Delete Button -->
                                <td style="vertical-align: middle;">
                                    <b-button variant="danger" @click="revoke(token)">{{ $t('pat.action_delete') }}</b-button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Token Modal -->
        <b-modal class="modal fade" id="modal-create-token" tabindex="-1" role="dialog"
                 @shown="focusMyElement"
                 :title="this.$t('pat.header_create_token')"
                 :ok-title="this.$t('clients.create')"
                 @ok="handleOk"
                 :cancel-title="this.$t('clients.close')">
            <!-- Form Errors -->
            <div class="alert alert-danger" v-if="form.errors.length > 0">
                <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                <br>
                <ul>
                    <li v-for="error in form.errors">
                        {{ error }}
                    </li>
                </ul>
            </div>

            <!-- Create Token Form -->
            <b-form role="form" @submit.prevent="handleOk" ref="patForm">
                <!-- Name -->
                <b-form-group
                    :label="$t('pat.header_name')"
                    label-for="create-token-name"
                >
                        <b-input
                            id="create-token-name"
                            name="name"
                            max="191"
                            :placeholder="$t('pat.placeholder_name')"
                            v-model="form.name"
                            ref="focusThis"
                            required></b-input>
                    <b-form-text id="password-help-block">
                        {{ $t('pat.help_name') }}
                    </b-form-text>
                </b-form-group>
                <!-- Scopes -->
                <div class="form-group" v-if="scopes.length > 0">
                    <label class="col-12 col-form-label">{{ $t('pat.header_scopes') }}</label>

                    <div class="col-12">
                        <div v-for="scope in scopes">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"
                                        @click="toggleScope(scope.id)"
                                        :checked="scopeIsAssigned(scope.id)">
                                        <span class="font-weight-bolder mr-3">{{ scope.id }}</span> {{scope.description}}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </b-form>
        </b-modal>

        <!-- Access Token Modal -->
        <b-modal
            class="modal fade"
            id="modal-access-token"
            tabindex="-1"
            role="dialog"
            ok-only
            @shown="focusMyElement"
            :title="this.$t('pat.header_personal_access_token')">
                <p>
                    {{ $t('pat.hint_new_token') }}
                </p>
                <textarea
                    readonly
                    class="form-control"
                    ref="focusThis"
                    rows="10">{{ accessToken }}</textarea>
        </b-modal>
    </div>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                accessToken: null,

                tokens: [],
                scopes: [],

                form: {
                    name: '',
                    scopes: [],
                    errors: []
                },

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
             * Prepare the component.
             */
            prepareComponent() {
                this.getTokens();
                this.getScopes();
            },

            /**
             * Get all of the personal access tokens for the user.
             */
            getTokens() {
                axios.get('/oauth/personal-access-tokens')
                        .then(response => {
                            this.tokens = response.data;
                        });
            },

            /**
             * Get all of the available scopes.
             */
            getScopes() {
                axios.get('/oauth/scopes')
                        .then(response => {
                            this.scopes = response.data;
                            this.loading = false;
                        });
            },

            checkFormValidity() {
                return this.$refs.patForm.checkValidity();
            },
            handleOk(bvModalEvt) {
                // Exit when the form isn't valid
                if (!this.checkFormValidity()) {
                    bvModalEvt.preventDefault();
                    return;
                }
                this.store();
            },

            /**
             * Create a new personal access token.
             */
            store() {
                this.accessToken = null;

                this.form.errors = [];

                const config = {
/*                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }*/
                }
                axios.post('/oauth/personal-access-tokens', this.form, config)
                        .then(response => {
                            this.form.name = '';
                            this.form.scopes = [];
                            this.form.errors = [];

                            this.tokens.push(response.data.token);

                            this.showAccessToken(response.data.accessToken);
                        })
                        .catch(error => {
                            if (typeof error.response.data === 'object') {
                                this.form.errors = _.flatten(_.toArray(error.response.data.errors));
                            } else {
                                this.form.errors = ['Something went wrong. Please try again.'];
                            }
                        });
            },

            /**
             * Toggle the given scope in the list of assigned scopes.
             */
            toggleScope(scope) {
                if (this.scopeIsAssigned(scope)) {
                    this.form.scopes = _.reject(this.form.scopes, s => s == scope);
                } else {
                    this.form.scopes.push(scope);
                }
            },

            /**
             * Determine if the given scope has been assigned to the token.
             */
            scopeIsAssigned(scope) {
                return _.indexOf(this.form.scopes, scope) >= 0;
            },

            /**
             * Show the given access token to the user.
             */
            showAccessToken(accessToken) {
                this.$root.$emit('bv::hide::modal', 'modal-create-token');
                this.accessToken = accessToken;
                this.$root.$emit('bv::show::modal', 'modal-access-token');
            },

            /**
             * Revoke the given token.
             */
            revoke(token) {
                axios.delete('/oauth/personal-access-tokens/' + token.id)
                        .then(response => {
                            this.getTokens();
                        });
            },

            focusMyElement(e) {
                this.$refs.focusThis.focus()
            }
        }
    }
</script>
