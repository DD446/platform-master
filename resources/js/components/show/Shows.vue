<template>
    <b-container fluid>

        <!-- User Interface controls -->
        <b-row class="justify-content-between">
            <b-col cols="12" md="auto" lg="3" class="mb-5">

                <b-form-group class="mb-4">
                    <b-input-group class="input-group-sm">
                        <b-form-input v-model="filter" placeholder="Ergebnisliste einschränken"/>
                        <b-input-group-append>
                            <b-btn :disabled="!filter" @click="filter = ''">X</b-btn>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>

                <nav>
                    <b-nav class="flex-md-column">
                        <b-navbar-brand>{{$t('shows.text_label_states')}}</b-navbar-brand>
                        <b-nav-item @click="filter = ''" :disabled="!filter" v-show="filter">{{$t('shows.text_label_state_all')}}</b-nav-item>
                        <b-nav>
                            <b-nav-item @click="filter = 'status:draft'" :disabled="(filter === 'status:draft')">{{$t('shows.text_label_state_draft')}}</b-nav-item>
                        </b-nav>
                        <b-nav>
                            <b-nav-item @click="filter = 'status:published'" :disabled="(filter === 'status:published')">{{$t('shows.text_label_state_published')}}</b-nav-item>
                        </b-nav>
                        <b-nav>
                            <b-nav-item @click="filter = 'status:scheduled'" :disabled="(filter === 'status:scheduled')">{{$t('shows.text_label_state_scheduled')}}</b-nav-item>
                        </b-nav>
                        <b-nav>
                            <b-nav-item @click="filter = 'status:noenclosure'" :disabled="(filter === 'status:noenclosure')">{{$t('shows.text_label_state_noenclosure')}}</b-nav-item>
                        </b-nav>
                        <b-nav>
                            <b-nav-item @click="filter = 'status:nocover'" :disabled="(filter === 'status:nocover')">{{$t('shows.text_label_state_nocover')}}</b-nav-item>
                        </b-nav>
                    </b-nav>
                </nav>
                <hr class="short">
                <b-button
                    :to="'/podcast/' + feedId + '/episode'"
                    variant="primary">{{$t('shows.text_button_label_add_show')}}</b-button>
            </b-col>

            <b-col md="10" cols="12" lg="9" class="my-1">

                <div class="row justify-content-between align-items-center mb-4">
                    <div class="col-lg-6 col-md-12">
                        <span class="text-muted text-small">
                            {{$t('shows.text_results_count', {count: resultCount, recent: resultRecent, total: resultTotal})}}
                        </span>
                    </div>
                    <form class="col-lg-6 col-md-12 align-items-center">

                        <b-form-group label-cols-sm="4" label-cols-lg="3" label="Sortierung" class="mb-0">
                            <b-input-group>
                                <b-form-select v-model="sortBy" :options="sortOptions" v-on:change="persist">
                                    <option slot="first" :value="null">{{$t('shows.text_sort_option_none')}}</option>
                                </b-form-select>
                                <b-form-select :disabled="!sortBy" v-model="sortDesc" slot="append" v-on:change="persist">
                                    <option :value=false>{{$t('shows.text_sort_option_ascending')}}</option>
                                    <option :value=true>{{$t('shows.text_sort_option_descending')}}</option>
                                </b-form-select>
                            </b-input-group>
                        </b-form-group>
                    </form>
                </div>

                <!-- Main table element -->
                <b-table
                    show-empty
                    hover
                    stacked="md"
                    ref="stable"
                    :items="items"
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :filterIncludedFields="filterOn"
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc"
                    :sort-direction="sortDirection"
                    :empty-filtered-text="$t('shows.empty_filtered_text')"
                    @filtered="onFiltered"
                    :apiUrl="apiUrl"
                >
                    <template v-slot:head></template>

                    <template v-slot:table-busy>
                        <div class="text-center">
                            <b-spinner label="Lade Daten..." class="m-5" style="width: 3rem; height: 3rem;" aria-hidden="true" />
                        </div>
                    </template>

                    <template v-slot:empty>
                        <div class="pt-4 pb-4">
                            <span class="alert alert-warning">{{ $t('shows.text_no_shows') }}</span>
                        </div>
                    </template>

                    <template v-slot:cell(logo)="row">
                        <b-img-lazy
                            left
                            fluid
                            class="mr-3"
                            style="border: 1px solid #dee2e6;max-height:125px;max-width:125px"
                            :alt="$t('shows.image_placeholder')"
                            :src="row.item.attributes.logo"
                            v-if="row.item.attributes.logo"></b-img-lazy>

                        <div class="mr-3 justify-content-center" style="position: relative;width:125px;height:125px" v-if="!row.item.attributes.logo">
                            <b-link :to="'/podcast/' + feedId + '/episode/' + row.item.attributes.guid">
                                <b-img
                                    thumbnail
                                    fluid
                                    blank
                                    blank-color="#fff"
                                    width="125px"
                                    height="125px"
                                    style="position: absolute;top: 0;left: 0;max-height:125px;max-width:125px"
                                    :alt="$t('shows.image_missing_placeholder')"
                                ></b-img>
                                <b-button
                                    variant="warning"
                                    style="position: absolute;top: 35px;left: 10px"
                                    >{{$t('shows.text_no_logo')}}</b-button>
<!--                                v-b-popover.hover.top="$t('shows.text_popover_set_show_logo')"-->
                            </b-link>
                        </div>
                    </template>

                    <template v-slot:cell(title)="row">
                        <editable
                            :placeholder="$t('shows.text_editable_placeholder_title')"
                            :title="$t('shows.text_editable_header_show_title')"
                            :content="row.item.attributes.title"
                            :feed="feedId"
                            :guid="row.item.attributes.guid"
                            type="showTitle"></editable>

<!--                        <b-button-->
<!--                            v-on:click="play(row.item.attributes.enclosure_url)"-->
<!--                            variant="outline-success"-->
<!--                            pill-->
<!--                            v-b-popover.hover.top="{title: $t('shows.title_audio_is_available'), content: $t('shows.content_audio_is_available', { file: row.item.attributes.file })}"><i :class="playPauseState"></i></b-button> <span v-text="row.item.attributes.duration_formatted" class="text-muted ml-2"></span>-->

                        <audio-play-button
                            v-if="row.item.attributes.enclosure_url && row.item.attributes.duration_formatted && row.item.attributes.type === 'audio'"
                            class="mt-2"
                            size="sm"
                            :url="row.item.attributes.enclosure_url"
                            :filename="row.item.attributes.file"
                            :duration="row.item.attributes.duration_formatted"></audio-play-button>

                        <video-player-button
                            v-if="row.item.attributes.enclosure_url && row.item.attributes.type === 'video'"
                            class="mt-2"
                            :url="row.item.attributes.enclosure_url"></video-player-button>

                        <b-button
                            v-if="!row.item.attributes.enclosure_url"
                            :to="'/podcast/' + feedId + '/episode/' + row.item.attributes.guid"
                            variant="warning"
                            size="sm"
                            class="mt-2"
                            v-b-popover.hover.top.blur="{title: $t('shows.title_enclosure_is_missing'), content: $t('shows.content_enclosure_is_missing')}">{{ $t('shows.enclosure_is_missing') }}</b-button>
                    </template>

                    <template v-slot:cell(is_public)="row">
                        {{$tc('shows.text_choice_state', row.item.attributes.is_published)}}
                    </template>

                    <template v-slot:cell(lastUpdate)="row">
                        {{ row.item.attributes.publish_date_formatted }}
<!--                        {{$d(new Date(row.item.attributes.publish_date * 1000))}}
                        <br>
                        <span class="text-muted text-small">
                            {{$d(new Date(row.item.attributes.publish_date * 1000), 'time')}} {{$t('shows.text_time_oclock')}}
                        </span>-->
                    </template>

                    <template v-slot:cell(actions)="row">
                        <!-- We use @click.stop here to prevent a 'row-clicked' event from also happening -->
                        <b-dropdown id="ddown-divider" no-caret size="sm">
                            <template slot="button-content">
                                <i class="icon-dots-three-horizontal"></i>
                            </template>
                            <b-nav vertical>
                                <b-nav-item
                                    :to="'/podcast/' + feedId + '/episode/' + row.item.attributes.guid"><i class="icon-pencil"></i> {{$t('shows.text_label_action_edit')}}</b-nav-item>
                                <b-nav-item
                                    @click="showCopyConfirmation(row.item)"
                                    v-b-popover.hover.righttop="$t('shows.text_popover_action_duplicate')"><i class="icon-copy"></i> {{$t('shows.text_label_action_duplicate')}}</b-nav-item>
<!--                                <b-nav-item
                                    @click="showMoveConfirmation(row.item)"
                                    v-b-popover.hover.top="$t('shows.text_popover_action_move')"><i class="icon-mouse-pointer"></i> {{$t('shows.text_label_action_move')}}</b-nav-item>-->
                                <b-nav-item
                                    :disabled="row.item.attributes.is_published==0 || row.item.attributes.is_published==2"
                                    :href="'/beta/podcast/' + feedId + '/episode/' + row.item.attributes.guid + '/share'"
                                ><i class="icon-share"></i> {{$t('shows.text_label_action_share')}}</b-nav-item>
                                <b-dropdown-divider></b-dropdown-divider>
                                <b-nav-item
                                    @click.prevent="showDeleteConfirmation(row.item)"><span class="text-danger"><i class="icon-trash"></i> {{$t('shows.text_label_action_delete')}}</span></b-nav-item>
                            </b-nav>
                        </b-dropdown>
                    </template>
                </b-table>

                <b-row v-show="resultTotal > resultCount">
                    <b-col md="9" class="my-1">
                        <b-pagination
                            v-show="resultTotal > perPage"
                            :total-rows="totalRows"
                            :per-page="perPage"
                            v-model="currentPage"
                            class="my-0"/>
                    </b-col>
                    <b-col md="3" class="my-1">
                        <b-form-group label="Ergebnisse pro Seite" class="mb-0">
                            <b-form-select :options="pageOptions" v-model="perPage"/>
                        </b-form-group>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
    import store from "store2";
    import Show from "./Show";
    import Editable from "../Editable";
    import {eventHub} from "../../app";
    import AudioPlayButton from "../AudioPlayButton";
    import VideoPlayerButton from "../VideoPlayerButton";

    let storeKey = 'showsSettings';

    export default {
        name: "Shows",

        components: {
            Show,
            Editable,
            AudioPlayButton,
            VideoPlayerButton,
        },

        data() {
            return {
                fields: [
                    {key: 'logo', label: '', sortable: false },
                    {key: 'title', label: this.$t('shows.text_label_title'), sortable: true },
                    {key: 'lastUpdate', label: this.$t('shows.text_label_created'), sortable: true, class: 'text-center'},
                    {key: 'is_public', label: this.$t('shows.text_label_status'), sortable: true, class: 'text-center'},
                    {key: 'actions', label: this.$t('shows.text_label_actions'), sortable: false}
                ],
                currentPage: 1,
                perPage: 10,
                pageOptions: [5, 10, 15, 25, 50],
                sortBy: 'lastUpdate',
                sortDesc: true,
                sortDirection: 'asc',
                filter: null,
                filterOn: [],
                modalInfo: {title: '', content: ''},
                isBusy: false,
                totalRows: 0,
                name: '',
                item: {},
                items: this.myProvider,
                apiUrl: '/api/feed/' + this.feedId + '/shows',
                state: true,
                isPlaying: false,
                feed: null,
            };
        },
        props: {
            feedId: {
                type: String,
                required: true
            },
            feeds: {
                required: true,
                type: Array
            },
        },
        computed: {
            sortOptions() {
                // Create an options list from our fields
                return this.fields
                    .filter(f => f.sortable)
                    .map(f => {
                        return { text: f.label, value: f.key }
                    })
            },
            rowsSelected() {
                return this.selected.length > 0;
            },
            countRowsSelected() {
                return this.selected.length  + ' ausgewählte Datei' + (this.selected.length > 1 ? 'en' : '' ) + ':';
            },
            resultCount() {
                return ((this.currentPage-1)*this.perPage)+1;
            },
            resultRecent() {
                return this.currentPage*this.perPage > this.totalRows ? this.totalRows : this.currentPage*this.perPage;
            },
            resultTotal() {
                return this.totalRows;
            }
        },

        mounted() {
            let settings = store.get(storeKey);

            if (settings) {
                this.sortBy = settings.sortBy;
                this.sortDesc = settings.sortDesc;
                this.perPage = settings.perPage;
            }

            // Set the initial number of items
            this.totalRows = this.items.length;

            // If this (sub)page is called from parent
            // feeds is (pre-)filled
            // but if the page is called directly
            // we have the fallback in watch
            // and have to wait til feeds gets filled from its parent
            if (this.feeds && this.feeds.length > 0) {
                this.updatePageInfo();
            }

            eventHub.$on("update:content:" + this.feedId, (type, content, guid) => {
                this.updateContent(type, content, guid);
            });
        },

        created() {
        },

        beforeDestroy() {
            eventHub.$off("update:content:" + this.feedId);
        },

        methods: {
            myProvider(oParam) {
                this.isBusy = true;
                // Here we don't set isBusy prop, so busy state will be handled by table itself
                let promise = axios.get(oParam.apiUrl
                    + '?page[number]=' + oParam.currentPage
                    + '&filter=' + oParam.filter
                    + '&page[size]=' + oParam.perPage
                    + '&sortBy=' + oParam.sortBy
                    + '&sortDesc=' + oParam.sortDesc);

                return promise.then((response) => {
                    this.totalRows = response.data.total_shows;
                    // Here we could override the busy state, setting isBusy to false
                    this.isBusy = false;
                    // Pluck the array of items off our axios response
                    // Must return an array of items or an empty array if an error occurred
                    return(response.data.data || []);
                });
            },
            info(item, index, button) {
                this.infoModal.title = `Row index: ${index}`;
                this.infoModal.content = JSON.stringify(item, null, 2);
                this.$root.$emit('bv::show::modal', this.infoModal.id, button);
            },
            resetInfoModal() {
                this.infoModal.title = '';
                this.infoModal.content = '';
            },
            onFiltered(filteredItems) {
                // Trigger pagination to update the number of buttons/pages due to filtering
                this.totalRows = filteredItems.length;
                this.currentPage = 1
            },
            sortChanged(ctx) {
                store.set(storeKey, { sortBy: ctx.sortBy, sortDesc: ctx.sortDesc, perPage: ctx.perPage });
            },
            persist() {
                store.set(storeKey, { sortBy: this.sortBy, sortDesc: this.sortDesc, perPage: this.perPage });
            },
            showCopyConfirmation(item) {
                this.$bvModal.msgBoxConfirm(this.$t('shows.confirm_copy_show', {title: item.attributes.title}), {
                    title: this.$t('shows.confirm_copy_show_header'),
                    size: 'md',
                    buttonSize: 'md',
                    okTitle: this.$t('shows.confirm_duplicate_show_yes'),
                    cancelTitle: this.$t('shows.confirm_duplicate_show_cancel'),
                    footerClass: 'p-2',
                    hideHeaderClose: false,
                    centered: true
                })
                    .then(confirmed => {
                        if (confirmed) {
                            axios.post('/api/feed/' + this.feedId + '/show/' + item.id + '/copy')
                                .then((response) => {
                                    eventHub.$emit('show-message:success', response.data);
                                    this.$refs.stable.refresh();
                                })
                                .catch(error => {
                                    this.showError(error);
                                });
                        }
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                        window.scrollTo(0,275);
                    });
            },
            showMoveConfirmation(item) {
                this.$bvModal.msgBoxConfirm(this.$t('shows.confirm_move_show', {title: item.attributes.title}), {
                    title: this.$t('shows.confirm_move_show_header'),
                    size: 'md',
                    buttonSize: 'md',
                    okTitle: this.$t('shows.confirm_move_show_yes'),
                    cancelTitle: this.$t('shows.confirm_move_show_cancel'),
                    footerClass: 'p-2',
                    hideHeaderClose: false,
                    centered: true
                })
                    .then(confirmed => {
                        if (confirmed) {
                            axios.post('/api/feed/' + this.feedId + '/show/' + item.id + '/move')
                                .then((response) => {
                                    eventHub.$emit('show-message:success', response.data);
                                    this.$refs.stable.refresh();
                                })
                                .catch(error => {
                                    this.showError(error);
                                });
                        }
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                        window.scrollTo(0,275);
                    });
            },
            showDeleteConfirmation(item) {
                this.$bvModal.msgBoxConfirm(this.$t('shows.confirm_delete_show', {title: item.attributes.title}), {
                    title: this.$t('shows.confirm_delete_show_header'),
                    size: 'md',
                    buttonSize: 'md',
                    okVariant: 'danger',
                    okTitle: this.$t('shows.confirm_delete_show_yes'),
                    cancelTitle: this.$t('shows.confirm_delete_show_cancel'),
                    footerClass: 'p-2',
                    hideHeaderClose: false,
                    centered: true
                })
                    .then(confirmed => {
                        if (confirmed) {
                            axios.delete('/api/feed/' + this.feedId + '/show/' + item.id)
                                .then((response) => {
                                    eventHub.$emit('show-message:success', response.data);
                                    this.$refs.stable.refresh();
                                })
                                .catch(error => {
                                    this.showError(error);
                                });
                        }
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                        window.scrollTo(0,275);
                    });
            },
            play(url) {
                if (!this.audio) {
                    this.audio = new Audio(url);
                }

                if (this.audio) {
                    if (!this.isPlaying) {
                        this.audio.play();
                        this.isPlaying = true;
                    } else {
                        this.audio.pause();
                        this.isPlaying = false;
                    }
                }
            },
            playPauseState() {
                if (this.isPlaying) {
                    return 'icon-controller-paus';
                } else {
                    return 'icon-controller-play';
                }
            },
            updateContent(type, content, guid) {
                let data = {
                    type: type,
                    content: content,
                    guid: guid
                };
                axios.put('/api/feeds/' + this.feedId, data)
                    .then((response) => {
                        eventHub.$emit('show-message:success', response.data);
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                        eventHub.$emit('content:update:finished:' + this.feedId);
                    });
            },
            emitPageInfo() {
                let items = [{
                    text: this.feed.attributes.title,
                    href: '#/podcast/' + this.feedId,
                },{
                    text: this.$t('nav.shows'),
                    href: '#/podcast/' + this.feedId + '/episoden',
                }];
                let page = {
                    header: this.$t('shows.header'),
                    subheader: this.$t('shows.subheader', {title: this.feed.attributes.title}),
                }
                eventHub.$emit('podcasts:page:infos', items, page);
            },
            getFeedFromFeeds() {
                let _feedId = this.feedId;
                return this.feeds.filter(function (feed) {
                    return feed.id === _feedId;
                })
            },
            updatePageInfo() {
                this.feed = this.getFeedFromFeeds().shift();
                this.emitPageInfo();
            }
        },

        watch: {
            feeds: function() {
                this.updatePageInfo();
            }
        }
    }
</script>

<style scoped>

</style>
