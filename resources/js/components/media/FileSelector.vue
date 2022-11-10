<template>
    <multiselect
            id="file-selector"
            v-model="value"
            :options="options"
            searchable
            close-on-select
            show-labels
            :placeholder="placeholder"
            :selectedLabel="selectedLabel"
            :selectLabel="selectLabel"
            :deselectLabel="deselectLabel"
            :loading="isLoading"
            :noResult="noResult"
            :noOptions="noOptions"
            track-by="id"
            label="name"
            @select="onSelect"
            @search-change="fetchFiles">
        <template slot="singleLabel" slot-scope="{ option }">{{ option.name }}</template>
        <template slot="noResult">{{$t('main.text_slot_file_selector_no_results')}}</template>
    </multiselect>
</template>

<script>
    import Multiselect from "vue-multiselect";
    import {eventHub} from '../../app';

    export default {
        name: "FileSelector",

        components: {
            Multiselect
        },

        props: {
            placeholder: {
                type: String,
                default: function () {
                    return this.$t("main.text_file_selector_placeholder")
                }
            },
            selectedLabel: {
                type: String,
                default: function () {
                    return this.$t("main.text_file_selector_selected_label")
                }
            },
            selectLabel: {
                type: String,
                default: function () {
                    return this.$t("main.text_file_selector_select_label")
                }
            },
            deselectLabel: {
                type: String,
                default: function () {
                    return this.$t("main.text_file_selector_deselect_label")
                }
            },
            noResult: {
                type: String,
                default: function () {
                    return this.$t("main.text_file_selector_noresult_label")
                }
            },
            noOptions: {
                type: String,
                default: function () {
                    return this.$t("main.text_file_selector_nooptions_label")
                }
            },
            filter: {
                type: String,
                default: null
            },
            limit: {
                type: String, // Should be: Number
                default: null
            }
        },

        data () {
            return {
                value: '',
                options: [],
                isLoading: false,
            }
        },

        methods: {
            init() {
                eventHub.$on("fileselector:change", file => {
                    this.value = file;
                });
                eventHub.$on("fileselector:reset", () => {
                    this.value = '';
                });
                this.isLoading = true;
                this.fetchFiles('');
            },
            onSelect(option) {
                eventHub.$emit('file:selected', option);
            },
            sourceUrl(query) {
                let url = '/mediathek';
                let params = {};
                let _filter = this.filter;

                if (query && query.length > 2) {
                    _filter += ' ' + query;
                }

                if (this.filter) {
                    params.filter = _filter;
                }

                if (this.limit) {
                    params.perPage = this.limit;
                }

                let _params = new URLSearchParams(params);

                return url + '?' + _params;
            },
            fetchFiles(query) {
                axios.get(this.sourceUrl(query))
                    .then(response => {
                        this.options = response.data.items;
                    })
                    .catch(error => {
                        // handle error
                        this.showError(error);
                    }).then(() => {
                    this.isLoading = false;
                });
            }
        },

        mounted() {
            this.init();
        },

        computed: {
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
