<template>
    <div>
        <label for="domain-selector" class="d-block">
            {{ $t('feeds.label_choose_domain') }}
        </label>
        <multiselect
            id="domain-selector"
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
            track-by="hostname"
            @select="onSelect"
            @remove="onRemove"
            label="name"
            :allowEmpty="allowEmpty"
            :disabled="isDisabled"
            v-if="!isDisabled"
        >
            <!--<template slot="singleLabel" slot-scope="{ option }">{{ option.name }}</template>-->
            <template slot="noResult">{{ $t('feeds.text_slot_domain_selector_no_results') }}</template>
            <template slot="noOptions">{{ $t('feeds.text_slot_domain_selector_no_options') }}</template>
        </multiselect>

        <b-row class="m-4" v-if="isDisabled">
            <div class="text-center alert-warning m-1 p-4">
                <b-card-text v-html="$t('feeds.text_hint_no_custom_domain')"></b-card-text>
            </div>
        </b-row>
    </div>
</template>

<script>
    import {eventHub} from '../../app';
    import Multiselect from "vue-multiselect";

    export default {
        name: "DomainSelector",

        components: {
            Multiselect
        },

        props: {
            feedId: {
                type: String,
                required: true,
            },
            placeholder: {
                type: String,
                default: function () {
                    return this.$t("feeds.text_domain_selector_placeholder")
                }
            },
            selectedLabel: {
                type: String,
                default: function () {
                    return this.$t("feeds.text_domain_selector_selected_label")
                }
            },
            selectLabel: {
                type: String,
                default: function () {
                    return this.$t("feeds.text_domain_selector_select_label")
                }
            },
            deselectLabel: {
                type: String,
                default: function () {
                    return this.$t("feeds.text_domain_selector_deselect_label")
                }
            },
            filter: {
                type: String,
                default: null
            },
            limit: {
                type: String, // Should be: Number
                default: null
            },
            type: {
                type: String,
                required: true,
            },
            allowEmpty: {
                type: Boolean,
                default: true,
            },
        },

        data () {
            return {
                value: {},
                options: [],
                isLoading: false,
                isDisabled: false,
            }
        },

        methods: {
            init() {
                this.isLoading = true;
                axios.get('/beta/domains/user?feedId=' + this.feedId + '&type=' + this.type)
                    .then(response => {
                        //this.value = response.data.selected;
                        // This should be done using a watcher
                        eventHub.$emit('domains:fetched:' + this.feedId,/* response.data.selected,*/ response.data.tlds, response.data.hostdomains);
                        this.options = response.data.feeds;
                        this.isDisabled = !this.options.length;
                    })
                    .catch(error => {
                        // handle error
                        this.showError(error);
                    }).then(() => {
                        this.isLoading = false;
                    });
            },
            onSelect(option) {
                eventHub.$emit('domain:selected:' + this.feedId, option);
            },
            onRemove() {
                eventHub.$emit('domain:deselected:' + this.feedId);
            },
        },

        mounted() {
            this.init();
        },

        computed: {
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
