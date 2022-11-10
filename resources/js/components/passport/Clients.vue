<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        {{ $t("clients.clients") }}
                    </span>

                    <b-button v-b-modal.modal-create-client>{{ $t("clients.create_new_client") }}</b-button>
                </div>
            </div>

            <div class="card-body">

                <div class="text-center" v-show="loading">
                    <div class="spinner-grow m-5 h-1" role="status">
                        <span class="sr-only">{{ $t("pat.is_loading") }}</span>
                    </div>
                </div>

                <!-- Current Clients -->
                <p class="mb-0" v-if="clients.length === 0 && !loading">
                    {{ $t("clients.no_client_hint") }}
                </p>

                <table class="table table-borderless mb-0" v-if="clients.length > 0">
                    <thead>
                        <tr>
                            <th nowrap>{{ $t("clients.client_id") }}</th>
                            <th>{{ $t("clients.client_name") }}</th>
                            <th>{{ $t("clients.client_secret") }}</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="client in clients">
                            <!-- ID -->
                            <td style="vertical-align: middle;">
                                {{ client.id }}
                            </td>

                            <!-- Name -->
                            <td style="vertical-align: middle;">
                                {{ client.name }}
                            </td>

                            <!-- Secret -->
                            <td style="vertical-align: middle;">
                                <code>{{ client.secret }}</code>
                            </td>

                            <!-- Edit Button -->
                            <td style="vertical-align: middle;">
                                <b-button @click="edit(client)">{{ $t("clients.edit_action") }}</b-button>
                            </td>

                            <!-- Delete Button -->
                            <td style="vertical-align: middle;">
                                <b-button variant="danger" @click="destroy(client)">{{ $t("clients.delete_action") }}</b-button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Client Modal -->
        <b-modal class="modal fade" id="modal-create-client"
                 tabindex="-1"
                 role="dialog"
                 @shown="focusMyElement"
                 :title="this.$t('clients.create_client')"
                 :ok-title="this.$t('clients.create')" @ok="store"
                 :cancel-title="this.$t('clients.close')">
                <!-- Form Errors -->
                <div class="alert alert-danger" v-if="createForm.errors.length > 0">
                    <p class="mb-0"><strong>{{ $t("clients.whoops") }}</strong> {{ $t("clients.error_message") }}</p>
                    <br>
                    <ul>
                        <li v-for="error in createForm.errors">
                            {{ error }}
                        </li>
                    </ul>
                </div>

                <!-- Create Client Form -->
                <form role="form">
                    <!-- Name -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ $t("clients.client_name") }}</label>

                        <div class="col-md-9">
                            <input id="create-client-name" type="text" class="form-control" ref="focusThis"
                                                        @keyup.enter="store" v-model="createForm.name">

                            <span class="form-text text-muted">
                                {{ $t("clients.name_hint") }}
                            </span>
                        </div>
                    </div>

                    <!-- Redirect URL -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ $t("clients.client_redirect_url") }}</label>

                        <div class="col-md-9">
                            <input type="text" class="form-control" name="redirect"
                                            @keyup.enter="store" v-model="createForm.redirect">

                            <span class="form-text text-muted">
                                {{ $t("clients.redirect_url_hint") }}
                            </span>
                        </div>
                    </div>
                </form>
        </b-modal>

        <!-- Edit Client Modal -->
        <b-modal class="modal fade" id="modal-edit-client" tabindex="-1" role="dialog"
                 @shown="focusMyElement"
                 :title="this.$t('clients.edit_client')"
                 @ok="update" :ok-title="this.$t('clients.save_changes')"
                 :cancel-title="this.$t('clients.close')">
            <!-- Form Errors -->
            <div class="alert alert-danger" v-if="editForm.errors.length > 0">
                <p class="mb-0"><strong>{{ $t("clients.whoops") }}</strong> {{ $t("clients.error_message") }}</p>
                <br>
                <ul>
                    <li v-for="error in editForm.errors">
                        {{ error }}
                    </li>
                </ul>
            </div>

            <!-- Edit Client Form -->
            <form role="form">
                <!-- Name -->
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">{{ $t("clients.client_name") }}</label>

                    <div class="col-md-9">
                        <input
                            id="edit-client-name"
                            type="text"
                            class="form-control"
                            ref="focusThis"
                            @keyup.enter="update"
                            v-model="editForm.name"
                            required>

                        <span class="form-text text-muted">
                            {{ $t("clients.name_hint") }}
                        </span>
                    </div>
                </div>

                <!-- Redirect URL -->
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">{{ $t("clients.client_redirect_url") }}</label>

                    <div class="col-md-9">
                        <input type="text" class="form-control" name="redirect" :placeholder="$t('clients.name_placeholder')"
                                        @keyup.enter="update" v-model="editForm.redirect" required>

                        <span class="form-text text-muted">
                            {{ $t("clients.redirect_url_hint") }}
                        </span>
                    </div>
                </div>
            </form>
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
                clients: [],

                createForm: {
                    errors: [],
                    name: '',
                    redirect: ''
                },

                editForm: {
                    errors: [],
                    name: '',
                    redirect: ''
                },

                loading: true
            };
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
                this.getClients();
            },

            /**
             * Get all of the OAuth clients for the user.
             */
            getClients() {
                axios.get('/oauth/clients')
                        .then(response => {
                            this.clients = response.data;
                            this.loading = false;
                        });
            },

            /**
             * Create a new OAuth client for the user.
             */
            store(bvModalEvt) {
                // Prevent modal from closing
                bvModalEvt.preventDefault();
                this.persistClient(
                    'post', '/oauth/clients',
                    this.createForm, 'modal-create-client'
                );
            },

            /**
             * Edit the given client.
             */
            edit(client) {
                this.editForm.id = client.id;
                this.editForm.name = client.name;
                this.editForm.redirect = client.redirect;

                this.$root.$emit('bv::show::modal', 'modal-edit-client');
            },

            /**
             * Update the client being edited.
             */
            update() {
                this.persistClient(
                    'put', '/oauth/clients/' + this.editForm.id, this.editForm, 'modal-edit-client'
                );
            },

            /**
             * Persist the client to storage using the given form.
             */
            persistClient(method, uri, form, modal) {
                form.errors = [];

                axios[method](uri, form)
                    .then(response => {
                        this.getClients();

                        form.name = '';
                        form.redirect = '';
                        form.errors = [];

                        this.$root.$emit('bv::hide::modal', modal);
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = _.flatten(_.toArray(error.response.data.errors));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                    });
            },

            /**
             * Destroy the given client.
             */
            destroy(client) {
                axios.delete('/oauth/clients/' + client.id)
                        .then(response => {
                            this.getClients();
                        });
            },

            focusMyElement(e) {
                this.$refs.focusThis.focus()
            }
        }
    }
</script>

<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>
