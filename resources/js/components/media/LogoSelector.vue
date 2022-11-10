<template>
    <div>
        <!--<label for="file-selector">{{ $t('mediamanager.select_logo_from_mediacenter') }}</label>-->
        <multiselect
                id="logo-selector"
                v-model="value"
                :options="options"
                searchable
                close-on-select
                show-labels
                :loading="isLoading"
                :placeholder="placeholder"
                :selectLabel="selectLabel"
                :selectedLabel="selectedLabel"
                :deselectLabel="deselectLabel"
                :noResult="noResult"
                :noOptions="noOptions"
                track-by="id"
                label="name"
                @select="onSelect"
                @search-change="fetchLogos"
                :option-height="150">
            <template slot="singleLabel" slot-scope="props">
                <div class="row">
                    <div class="col-6" style="position:relative">
                        <img class="img-thumbnail img-fluid" :src="props.option.intern" :alt="props.option.name">
<!--                        <div class="p-1" style="position:absolute;bottom: 10px;right:10px;background-color:#0b0b0b">
                            <div class="text-white">
                                {{ props.option.title }}
                            </div>
                        </div>-->
                        <span class="badge badge-secondary" style="position:absolute;bottom: 10px;right:20px;">{{ props.option.name }}</span>
                    </div>
                </div>
            </template>
            <template slot="option" slot-scope="props">
                <div class="row">
                    <div class="col-6" style="position:relative">
                        <img class="img-thumbnail img-fluid" :src="props.option.intern" :alt="props.option.name">
<!--                        <div class="p-1" style="position:absolute;bottom: 10px;right:10px;background-color:#0b0b0b">
                            <div class="text-white">
                                {{ props.option.title }}
                            </div>
                        </div>-->
                        <span class="badge badge-info" style="position:absolute;bottom: 10px;right:20px;">{{ props.option.name }}</span>
                    </div>
                </div>
            </template>
        </multiselect>
    </div>
</template>

<script>
    import Multiselect from "vue-multiselect";
    import {eventHub} from '../../app';

    export default {
        name: "LogoSelector",

        components: {
            Multiselect
        },

        props: {
            feed: {
                type: String,
            },
            placeholder: {
                type: String,
                default: function () {
                    return this.$t('mediamanager.placeholder_select_logo')
                }
            },
            type: {
                type: String,
                default: 'showlogo'
            }
        },

        data () {
            return {
                selectLabel: this.$t('mediamanager.select_label_logo'),
                deselectLabel: this.$t('mediamanager.deselect_label_logo'),
                selectedLabel: this.$t('mediamanager.selected_label_logo'),
                noResult: this.$t('mediamanager.no_result_logo'),
                noOptions: this.$t('mediamanager.no_options_logo'),
                value: null,
                options: [],
                isLoading: false,
            }
        },

        methods: {
            onSelect(option) {
                eventHub.$emit('update:content:' + this.feed, this.type, option);
            },
            fetchLogos(query) {
                this.isLoading = true;
                let url = '/mediathek?perPage=20&currentPage=1&sortDesc=true&sortBy=date&filter=type:logo';

                if (query) {
                    url += ' ' + query;
                }
                axios.get(url)
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
            this.fetchLogos();
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
