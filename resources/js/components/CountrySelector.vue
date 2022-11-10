<template>
    <div>
        <multiselect
                id="country-selector"
                v-model="value"
                :options="options"
                :searchable="true"
                :close-on-select="true"
                :show-labels="true"
                :loading="isLoading"
                :placeholder="placeholder"
                :selectLabel="selectLabel"
                :selectedLabel="selectedLabel"
                :deselectLabel="deselectLabel"
                :noResult="noResult"
                :noOptions="noOptions"
                @select="onSelect"
                @remove="onRemove"
                track-by="code"
                label="name">
        </multiselect>
    </div>
</template>

<script>
    import Multiselect from "vue-multiselect";
    import {eventHub} from '../app';

    export default {
        name: "CountrySelector",

        components: {
            Multiselect
        },

        props: {
            feedId: {
                type: String,
                required: false
            },
            country: {
                type: Object,
                required: false,
            }
        },

        data () {
            return {
                placeholder: this.$t('main.placeholder_country_selector_select_country'),
                selectLabel: this.$t('main.text_file_selector_select_label'),
                deselectLabel: this.$t('main.text_file_selector_deselect_label'),
                selectedLabel: this.$t('main.text_file_selector_selected_label'),
                noResult: this.$t('main.text_file_selector_noresult_label'),
                noOptions: this.$t('main.text_file_selector_nooptions_label'),
                value: null,
                options: [],
                isLoading: false,
            }
        },

        methods: {
            onSelect(option) {
                if (this.feedId) {
                    eventHub.$emit('country:selected:' + this.feedId, option);
                }
            },
            onRemove() {
                if (this.feedId) {
                    eventHub.$emit('country:removed:' + this.feedId);
                }
            },
            fetchCountries() {
                this.isLoading = true;
                axios.get('/countries?grouped')
                    .then(response => {
                        this.options = response.data;
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
            this.fetchCountries();
            this.value = this.country;
        },

        watch: {
/*            options: {
                immediate: true,
                handler(values) {
                    this.value = this.options.filter(x => values.includes(x[this.trackBy]));
                }
            }*/
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
