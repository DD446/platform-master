<template>
    <b-modal
        ref="statsExportModal"
        :title="$t('stats.title_file_metadata')"
        :ok-only="true"
        :ok-title="$t('stats.close_modal')"
        size="lg"
        centered
        ok-variant="outline-secondary"
        scrollable
        lazy>
        <b-container>
            <b-form @submit="onSubmit" @reset="onReset" v-if="showForm">
                <alert-container></alert-container>

                <b-row class="mt-1 mb-3 ml-lg-1" v-if="!this.canExport">
                    <div class="text-center alert-warning m-1 p-4">
                        <b-card-text v-html="$t('stats.text_hint_upgrade_package_for_export', {route: '/pakete'})"></b-card-text>
                    </div>
                </b-row>

                <b-row>
                    <b-col cols="12" class="mt-3">
                        <b-form-group
                            :label="$t('stats.label_date_range')"
                            label-for="date"
                            class="mb-4"
                        >
                            <b-input class="form-control" name="date" readonly v-if="form.date==='all'" :value="$t('stats.header_date_all')"></b-input>
                            <b-input class="form-control" name="date" readonly v-else :value="form.start.toLocaleDateString() + ' - ' + form.end.toLocaleDateString()"></b-input>
                        </b-form-group>
<!--                        <b-form-group
                            label="Daten-Format"
                            label-for="datatype"
                        >
                            <b-form-select v-model="selectedDataType" :options="datatypes" class="mb-3" id="datatype" size="lg" v-on:change="setDataType"></b-form-select>
                        </b-form-group>-->

                        <b-form-group
                            :label="$t('stats.label_choose_podcast')"
                            label-for="feeds"
                        >
                            <b-form-select v-model="form.feed_id" :options="feeds" class="mb-3" id="feeds" v-on:change="getShows" ></b-form-select>
                        </b-form-group>

                        <b-form-group
                            :label="$t('stats.label_choose_show')"
                            label-for="episodes"
                        >
                            <b-form-select v-model="form.show_id" :options="shows" class="mb-3" id="episodes" :disabled="form.feed_id==='__FEEDS__' || true"></b-form-select>
                        </b-form-group>

                        <b-form-group
                            :label="$t('stats.label_choose_sort_order')"
                        >
                            <div class="row mb-2">
                                <div class="col-6">
                                    <b-form-select v-model="form.sort_by" :options="[{value: 'hits', text: $t('stats.option_label_sort_hits')}, {value: 'date', text: $t('stats.option_label_sort_date')}]"></b-form-select>
                                </div>
                                <div class="col-6">
                                    <b-form-select v-model="form.sort_order" :options="[{value: 'desc', text: $t('stats.option_label_order_desc')}, {value: 'asc', text: $t('stats.option_label_order_asc')}]"></b-form-select>
                                </div>
                            </div>
                        </b-form-group>

                        <b-form-group
                            :label="$t('stats.label_choose_limits')"
                        >
                            <div class="row mb-2">
                                <div class="col-6">
                                    <b-form-select v-model="form.limit" :options="[{value: null, text: $t('stats.label_all_results')}, {value: 10, text: $t('stats.label_count_results', { count: 10 })}, {value: 15, text: $t('stats.label_count_results', { count: 15})}, {value: 25, text: $t('stats.label_count_results', { count: 25})}, {value: 50, text: $t('stats.label_count_results', { count: 50})}, {value: 100, text: $t('stats.label_count_results', { count: 100})}, {value: 250, text: $t('stats.label_count_results', { count: 250})}, {value: 500, text: $t('stats.label_count_results', { count: 500})}, {value: 1000, text: $t('stats.label_count_results', { count: 1000})}]"></b-form-select>
                                </div>
                                <div class="col-6">
                                    <b-form-select v-model="form.offset" :options="[{value: null, text: $t('stats.label_from_start')}, {value: 10, text: $t('stats.label_from_count', { count: 11 })}, {value: 15, text: $t('stats.label_from_count', { count: 16 })}, {value: 25, text: $t('stats.label_from_count', { count: 26 })}, {value: 50, text: $t('stats.label_from_count', { count: 51 })}, {value: 100, text: $t('stats.label_from_count', { count: 101 })}, {value: 250, text: $t('stats.label_from_count', { count: 251 })}, {value: 500, text: $t('stats.label_from_count', { count: 501 })}, {value: 1000, text: $t('stats.label_from_count', { count: 1001 })}]"></b-form-select>
                                </div>
                            </div>
                        </b-form-group>

                        <b-form-group
                            :label="$t('stats.label_restriction')"
                        >
                            <div class="row mb-2" v-if="form.restrict">
                                <div class="col-12 col-sm-3">
                                    <b-form-select v-model="form.restrict_limit" :options="[{value: 10, text: 10}, {value: 15, text: 15}, {value: 25, text: 25}, {value: 50, text: 50}, {value: 100, text: 100}]"></b-form-select>
                                </div>
                                <div class="col-12 col-sm-9">
                                    <b-form-select v-model="form.restrict" :options="[{value: null, text: $t('stats.option_label_no_restriction')}, {value: 'newest', text: $t('stats.option_label_restriction_newest')}]"></b-form-select>
                                </div>
                            </div>
                            <div class="row mb-2" v-else>
                                <div class="col-5">
                                    <b-button @click="form.restrict='newest'" variant="outline-secondary">
<!--                                        <b-icon icon="plus"></b-icon>-->
                                        <svg data-v-2267c81e="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="plus" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-plus b-icon bi"><g data-v-2267c81e=""><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"></path></g></svg>
                                        {{ $t('stats.button_label_add_restriction') }}</b-button>
                                </div>
                            </div>
                        </b-form-group>

                        <div class="form-group">
                            <b-button type="submit" variant="primary" :disabled="exceeded" class="float-right">{{ $t('stats.button_export_data') }}</b-button>
                            <p class="help-block">
                                <small class="text-muted">
                                    {{ $t('stats.help_available_exports') }} <span v-if="availableExports.available===-1">{{ $t('stats.unlimited_available_exports') }}</span> <span v-else>{{ availableExports.available }}/{{ availableExports.total }}</span>
                                    <i class="ml-2 icon icon-info-with-circle text-blue" v-b-popover.hover.click="$t('stats.popover_available_exports')"></i>
                                </small>
                            </p>
                        </div>
                    </b-col>

                    <hr>

                    <b-col cols="12" class="mt-3">
                        <h3>{{ $t('stats.header_last_exports', {count: visibleExports}) }}</h3>

                        <b-table
                            responsive
                            show-empty
                            empty-text="Keine Exporte vorhanden"
                            :items="exports"
                            :fields="fields"
                            ref="etable"
                            :busy="isEtableBusy"
                        >
                            <template slot="table-busy">
                                <div>
                                    <b-spinner type="grow" label="Lade Exporte..." />
                                </div>
                            </template>

                            <template v-slot:cell(params)="row">
                                <p>
                                    <small class="">{{ row.item.created_at | dateTime }}</small>
                                </p>
                                <ul>
                                    <li>
                                        <div v-if="row.item.feed_id=='__FEEDS__'">
                                            {{ $t('stats.option_label_all_feeds') }}
                                        </div>
                                        <div v-else>
                                            {{ row.item.feed_id }}
                                        </div>
                                    </li>
                                    <li>
                                        <div v-if="row.item.show_id=='__SHOWS__'">
                                            {{ $t('stats.option_label_all_shows') }}
                                        </div>
                                        <div v-else>
                                            {{ row.item.show_id }}
                                        </div>
                                    </li>
                                    <li>
                                        <div v-if="row.item.start">
                                            {{ $t('stats.label_date_range') }}: {{ row.item.start | dateOnly }} - {{ row.item.end | dateOnly }}
                                        </div>
                                        <div v-else>
                                            {{ $t('stats.label_date_range') }}: {{ $t('stats.header_date_all') }}
                                        </div>
                                    </li>
                                    <li>
                                        {{ $t('stats.option_label_sort_' + row.item.sort_by) }} {{ $t('stats.option_label_order_' + row.item.sort_order) }}
                                    </li>
                                    <li>
                                        <span v-if="row.item.limit">
                                            {{ $t('stats.label_count_results', { count: row.item.limit }) }}
                                        </span>
                                        <span v-else>
                                            {{ $t('stats.label_all_results') }}
                                        </span>

                                        <span v-if="row.item.offset">
                                            {{ $t('stats.label_from_count', {count: row.item.offset + 1 }) }}
                                        </span>
                                        <span v-else>
                                            {{ $t('stats.label_from_start') }}
                                        </span>
                                    </li>
                                    <li v-if="row.item.restrict">
                                        {{ row.item.restrict_limit }} {{ $t('stats.option_label_restriction_' + row.item.restrict) }}
                                    </li>
                                </ul>
                            </template>


                            <template v-slot:cell(actions)="row">
                                <div v-if="row.item.filename">
                                    <b-button-group>
                                        <b-link variant="outline-primary" class="btn btn-primary" :href="'/statistics/export/' + row.item.filename + '.' + row.item.format" v-b-popover.hover="$t('stats.popover_download_export')" @click.passive="trackDownload(row.item.id)" target="_top"><i class="icon-download"></i></b-link>
                                        <b-button variant="outline-danger" @click.stop="deleteExport(row)" v-b-popover.hover="$t('stats.popover_delete_export')"><i class="icon-trash"></i></b-button>
                                    </b-button-group>
                                </div>
                                <div v-else>
                                    <b-progress :max="100" height="25px">
                                        <b-progress-bar :value="100" variant="info" animated class="p-2">
                                            Export läuft...
                                        </b-progress-bar>
                                    </b-progress>
                                </div>
                            </template>
                        </b-table>
                    </b-col>
                </b-row>
            </b-form>
        </b-container>
    </b-modal>
</template>

<script>
import {eventHub} from "../../app";
import StatsDaterangePicker from "./StatsDaterangePicker";

function initialFormData(route) {
    return {
        start: route.query.df ? new Date(route.query.df) : (d => new Date(d.setDate(d.getDate()-8)) )(new Date),
        end: route.query.dt ? new Date(route.query.dt) : (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
        date: 'custom',
        feed_id: route.query.s ? route.query.s : '__FEEDS__',
        show_id: '__SHOWS__',
        data_type: "raw",
        sort_by: 'hits',
        sort_order: 'desc',
        limit: null,
        offset: null,
        restrict: '',
        restrict_limit: 10,
    }
}

export default {
    name: "StatsExportModal",

    components: {
        StatsDaterangePicker,
    },

    data () {
        return {
            form: initialFormData(this.$route),
            showForm: true,
            isEtableBusy: false,
            datatypes: [
                { value: "raw", text: 'Rohdaten' },
                { value: 'daily', text: 'Tage' },
                { value: 'weekly', text: 'Wochen' },
                { value: 'monthly', text: 'Monate' },
            ],
            files: [],
            feeds: [
                { value: "__FEEDS__", text: this.$t('stats.option_label_all_feeds') }
            ],
            shows: [
                { value: "__SHOWS__", text: this.$t('stats.option_label_all_shows') }
            ],
            availableExports: {
                'available': 0,
                'total': 0
            },
            visibleExports: 5,
            exports: [],
            fields: [
                {key: 'params', label: this.$t('stats.label_params'), sortable: true, sortDirection: 'asc'},
                {key: 'actions', label: 'Aktionen'},
            ]
        }
    },

    props: {
        canExport: {
            type: Boolean
        },
    },

    methods: {
        onSubmit (evt) {
            evt.preventDefault();
            window.scrollTo(0,275);
            axios.post('/api/stats/export', this.form)
                .then(response => {
                    this.showMessage(response);
                    this.getExports();

                    Echo.private('stats.exported.' + response.data.id)
                        .listen('\\App\\Events\\StatsExportStarted', (e) => {
                            let idx = this.exports.findIndex(x => x.id === response.data.id);
                        })
                        .listen('\\App\\Events\\StatsExportedEvent', (e) => {
                            this.getExports();
                            this.showMessage(this.$t('stats.message_new_export_is_ready'));
                        });
                })
                .catch(error => {
                    this.showError(error, 'danger');
                });
        },
        onReset (evt) {
            evt.preventDefault();
            /* Reset our form values */
            this.form = initialFormData(this.$route);
            /* Trick to reset/clear native browser form validation state */
            this.showForm = false;
            this.$nextTick(() => { this.showForm = true });
        },
        daysAgoDate(days) {
            let d = new Date();
            d.setDate(d.getDate() - days);

            return d;
        },
        getShows(feedId) {
            // TODO
/*            axios.get('/api/feed/' + feedId + '/shows')
                .then(response => {
                    let _options = [{
                        value: null,
                        text:  "Alle Episoden"
                    }];
                    for (const s of response.data) {
                        _options.push({
                            value: s.show_title,
                            text:  s.show_title
                        });
                    }
                    this.files = _options;
                })
                .catch(error => {
                    this.showError(error, 'danger');
                });*/
        },
        setDateRange(range) {
            this.form.start = range.start;
            this.form.end = range.end;
        },
        setDataType: function(selected) {
            this.form.data_type = selected;
        },
        setFile: function(selected) {
            this.form.show_title = selected;
        },
        countDownChanged(dismissCountDown) {
            this.dismissCountDown = dismissCountDown
        },
        getExports() {
            axios.get('/api/stats/export')
                .then(response => {
                    this.exports = response.data.exports;
                    this.availableExports = response.data.availableExports;
                })
                .catch(error => {
                    this.showError(error, 'danger');
                });
        },
        deleteExport(row) {
            let id = row.item.id;

            if (confirm("Willst Du den Export wirklich löschen?")) {
                let idx = this.exports.findIndex(x => x.id === id);
                this.exports.splice(idx, 1);
                axios.delete('/api/stats/export/' + id)
                    .then((response) => {
                            this.showMessage(response);
                            this.getExports();
                        },
                        (error) => {
                            this.showError(error, 'danger');
                            this.getExports();
                        })
                    .catch(error => {
                        this.showError(error, 'danger');
                        this.getExports();
                    });
            }
        },
        async trackDownload(id) {
            axios.put('/api/stats/export/' + id);
        },
        show() {
            this.$refs.statsExportModal.show();
        },
        getSources() {
            if (this.feeds.length > 1) return;
            axios.get('/api/stats/sources')
                .then(response => {
                    let opts = response.data;
                    let _this = this;

                    opts.forEach(function(element) {
                        _this.feeds.push({
                            value: element.feed_id, text: element.rss.title
                        })
                    });
                })
                .catch(error => {
                    // handle error
                    this.showError(error);
                }).then(() => {
                    this.isLoading = false;
                }
            );
        }
    },

    mounted() {
        eventHub.$on("stats-export-modal:show", (settings) => {
            this.form = initialFormData(this.$route);
            this.form = array_merge(this.form, settings);

            if (this.canExport) {
                this.getExports();
                this.getSources();
            }
            this.show();
        });
        eventHub.$on('change', (type, params) => {
            if (type === 'date') {
                this.form.start = params.start;
                this.form.end = params.end;
            } else if (type === 'source') {
                this.form.feed_id = params;
            }
        });
    },

    computed: {
        exceeded() {
            if (!this.canExport) {
                return true;
            }

            return this.availableExports.available === 0;
        },
        numberExports() {
            return this.exports.length;
        }
    }
}
</script>

<style scoped>

</style>
