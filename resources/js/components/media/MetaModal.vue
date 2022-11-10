<template>
    <b-modal
        id="meta-modal"
        ref="metaModal"
        :title="$t('mediamanager.title_file_metadata')"
        :ok-only="!editable"
        @ok="handleOk"
        :ok-title="okTitle()"
        @hide="resetModal"
        :cancel-title="$t('mediamanager.button_cancel')"
        lazy>
        <b-spinner v-show="!details"></b-spinner>

        <b-row v-if="details && Object.entries(details).length > 0">
            <b-form ref="form" @submit.stop.prevent="handleOk">
                <b-col cols="5" offset="7">
                    <b-checkbox v-model="editable" switch>{{ labelEditable() }}</b-checkbox>
                </b-col>
                <b-col cols="12">
                    <b-row v-for="(detail, name) of details" :key="name" class="mb-2">
                        <template v-if="type==='chapters'">
                            <label class="col-12 col-form-label">{{ $t('mediamanager.label_chapter', { count: name }) }}</label>
                            <b-col cols="12" v-for="(item, key) of detail" :key="key">
                                <b-input-group size="sm" :prepend="$t('mediamanager.label_chaptermark_' + key )" class="mb-1">
                                    <b-input class="form-control" :readonly="!editable" :value="item"></b-input>
                                </b-input-group>
                            </b-col>
                        </template>
                        <template v-else>
                            <label class="col-12 col-form-label text-capitalize">{{ getName(name) }}</label>
                            <b-col cols="12" v-for="(item, key) of detail" :key="key">
                                <b-form-textarea :readonly="!editable" :value="item" v-if="name==='comment'" class="mb-2"></b-form-textarea>
                                <b-input :readonly="!editable" class="mb-1" v-model="details[name][key]" v-else></b-input>
                            </b-col>
                        </template>
                    </b-row>
                </b-col>
<!--                <b-col cols="12" v-show="editable">
                    <hr>
                    <b-row class="m-1">
                        <label>{{ $t('mediamanager.label_add_field') }}</label>
                        <b-input-group :prepend="$t('mediamanager.field')">
                            <b-select
                                v-model="fieldType"
                                :options="fieldList"></b-select>
                            <b-input-group-append>
                                <b-button @click="addField">{{ $t('mediamanager.button_add_field') }}</b-button>
                            </b-input-group-append>
                        </b-input-group>
                    </b-row>
                </b-col>-->
            </b-form>
        </b-row>
        <b-row v-else>
            <b-col cols="12">
                <div class="alert alert-secondary m-3 p-3">
                    {{ stateHint }}
                </div>
            </b-col>
        </b-row>
    </b-modal>
</template>

<script>
    import {eventHub} from '../../app';

    const availableFields = [{
        value: 'title',
        text: 'Title',
        disabled: false
    },{
        value: 'comment',
        text: 'Comment',
        disabled: false
    },{
        value: 'track_number',
        text: 'Track Number',
        disabled: false
    }];

    function initialState() {
        return {
            item: '',
            type: null,
            details: false,
            editable: false,
            fieldList: availableFields,
            stateHint: '',
            fieldType: null
        }
    }

    export default {
        name: "meta-modal",

        data() {
            return initialState();
        },

        components: {
        },

        methods: {
            getInfo() {
                this.stateHint = this.$t('mediamanager.meta_modal_state_hint_loading');

                axios.get('/api/media/' + this.item.id + '/metadata?type=' + this.type)
                    .then((response) => {
                        this.details = response.data;
                        let _this = this;
                        availableFields.forEach(function (item) {
                            item.text = _this.$t('mediamanager.meta_name_' + item.value);
                        });

                        if (Object.entries(this.details).length < 1) {
                            this.stateHint = this.$t('mediamanager.meta_modal_state_hint_no_data');
                        }
                    })
                    .catch(error => {
                        this.details = {};
                        this.showError(error);
                    });
            },
            show() {
                this.$refs.metaModal.show();
                this.getInfo();
            },
            resetModal() {
                Object.assign(this.$data, initialState());
            },
            okTitle() {
                if (!this.editable) {
                    return this.$t('mediamanager.close_modal');
                }
                return this.$t('mediamanager.button_save_meta_data');
            },
            checkFormValidity() {
                const valid = this.$refs.form.checkValidity()
                return valid;
            },
            handleOk(bvModalEvent) {
                if (!this.editable) {
                    return;
                }
                // Prevent modal from closing
                bvModalEvent.preventDefault()
                // Trigger submit handler
                this.handleSubmit()
            },
            handleSubmit() {
                // Exit when the form isn't valid
                if (!this.checkFormValidity()) {
                    return;
                }

                axios.put('/api/media/' + this.item.id + '/metadata', this.details);

                // Hide the modal manually
                this.$nextTick(() => {
                    //this.$bvModal.hide('meta-modal')
                    this.editable = false;
                })
            },
            getName(name) {
                let trans = this.$t('mediamanager.meta_name_' + name);

                if (trans.startsWith('mediamanager.meta_name_')) return name;

                return trans;
            },
            labelEditable() {
                if (this.editable) return this.$t('mediamanager.label_is_editable');

                return this.$t('mediamanager.label_read_only');
            },
            addField() {
                if (!this.fieldType) return;

                switch (this.fieldType) {
                    case 'comment':
                        return;
                    default:
                        this.$set(this.details, name, {name: ""})
                }
            }
        },
        mounted() {
            eventHub.$on("meta-modal:show", pass => {
                this.item = pass.item;
                this.type = pass.type;
                this.show();
            });
        }
    }
</script>
