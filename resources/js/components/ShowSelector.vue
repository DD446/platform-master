<template>
    <div>
<!--        <div class="text-left">
            <label>
                {{ $t('feeds.header_episode') }}
            </label>
        </div>-->

        <b-alert variant="warning" :show="!value" class="mb-2">
            {{ $t('stats.message_choose_episode') }}
        </b-alert>

        <multiselect
                id="show-selector"
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
                track-by="value"
                label="text"
                group-values="options"
                group-label="label"
                @select="onSelect"
                :option-height="150">
<!--            <template slot="singleLabel" slot-scope="props">
                <div class="row">
                    <div class="col-6" style="position:relative">
                        <img class="img-thumbnail img-fluid" :src="props.option.logo" :alt="props.option.title">
                        {{ option.title }}
                        <span class="badge badge-secondary" style="position:absolute;bottom: 10px;right:20px;">{{ props.option.title }}</span>
                    </div>
                </div>
            </template>
            <template slot="option" slot-scope="props">
                <div class="row">
                    <div class="col-6" style="position:relative">
                        <img class="img-thumbnail img-fluid" :src="props.option.logo" :alt="props.option.title">
                        <span class="badge badge-info" style="position:absolute;bottom: 10px;right:20px;">{{ props.option.name }}</span>
                    </div>
                </div>
            </template>-->
        </multiselect>
    </div>
</template>

<script>
    import Multiselect from "vue-multiselect";
    import {eventHub} from '../app';

    export default {
        name: "ShowSelector",

        components: {
            Multiselect
        },

        props: {
            feed: {
                type: String,
            }
        },

        data () {
            return {
                placeholder: this.$t('feeds.list_option_select_an_episode'),
                selectLabel: this.$t('feeds.select_label_episode'),
                deselectLabel: this.$t('feeds.deselect_label_episode'),
                selectedLabel: this.$t('feeds.selected_label_episode'),
                noResult: this.$t('feeds.no_result_episodes'),
                noOptions: this.$t('feeds.no_options_episodes'),
                value: [],
                options: [],
                isLoading: false,
            }
        },

        methods: {
            onSelect(option) {
                const query = Object.assign({}, this.$route.query);
                query.e = option.value;
                query.s = option.feed_id;
                query.t = option.text;
                this.$router.push({ query });
                eventHub.$emit('show:selected', option);
            },
            getData() {
                this.isLoading = true;

                let url = '/api/shows?pp=1';

                if (this.$route.query.s) {
                    url += '&s=' + this.$route.query.s;
                }

                if (this.$route.query.i) {
                    url += '&i=' + this.$route.query.i;
                }

                axios.get(url)
                    .then(response => {

                        let feeds = response.data.data;

                        this.options = [];

                        let _this = this;

                        let newest = null;

                        Object.values(feeds).forEach(function(feed) {
                            let childOpts = [];
                            let shows = feed.attributes.shows.data;
                            shows.sort(function (a, b) {
                                if (a.attributes.publish_date > b.attributes.publish_date) {
                                    return -1;
                                }
                                if (a.attributes.publish_date < b.attributes.publish_date) {
                                    return 1;
                                }
                                return 0;
                            })
                            shows.forEach(function(show) {
                                if(show.attributes.is_published == 1) {
                                    childOpts.push({
                                        feed_id: feed.id,
                                        value: show.attributes.episode_id,
                                        text: (show.attributes.title ? show.attributes.title : show.attributes.guid) + ' (' + show.attributes.publish_date_formatted + ')'
                                    })

                                    if (!newest || show.attributes.publish_date > newest.show.attributes.publish_date) {
                                        newest = {
                                            feed_id: feed.id,
                                            show: show
                                        };
                                    }
                                }
                            });
                            _this.options.push({
                                label: feed.attributes.title,
                                options: childOpts
                            });

                            if (newest) {
                                let range = {
                                    start: new Date(newest.show.attributes.publish_date*1000),
                                    end: _this.$route.query.dt ? new Date(_this.$route.query.dt) : (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
                                }
                                eventHub.$emit('change', 'mate', range);
                                _this.value = {
                                    feed_id: newest.feed_id,
                                    value: newest.show.attributes.episode_id,
                                    text: (newest.show.attributes.title ? newest.show.attributes.title : newest.show.attributes.guid) + ' (' + newest.show.attributes.publish_date_formatted + ')'
                                };
                                _this.onSelect(_this.value);
                            }
                        });
                    })
                    .catch(error => {
                        this.options = [];
                        // handle error
                        this.showError(error);
                    }).then(() => {
                        this.isLoading = false;
                    });
            }
        },

        mounted() {
            this.getData();

            eventHub.$on('change', (type, params) => {
                if (type === 'psource') {
                    // Deselect previously selected episode
                    // as this might not be available anymore
                    this.value = [];
                    const query = Object.assign({}, this.$route.query);
                    delete query.e;
                    delete query.s;
                    delete query.t;
                    this.$router.push({ query });
                    this.getData();
                    eventHub.$emit('show:selected', null);
                }
            });

            if (this.$route.query.e) {
                this.value = {
                    value: this.$route.query.e ? this.$route.query.e : null,
                    feed_id: this.$route.query.s ? this.$route.query.s : null,
                    text: this.$route.query.t ? this.$route.query.t : null,
                }
                eventHub.$emit('show:selected', this.value);
            }
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
