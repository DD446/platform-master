<template>
    <div>
        <b-container>
            <b-row>
                <b-col cols="12">
                    <b-alert
                            :variant="avariant"
                            :show="dismissCountDown"
                            dismissible
                            fade
                            dismiss-label="X"
                            @dismissed="dismissCountDown=0"
                            @dismiss-count-down="countDownChanged"
                    >{{ atext }}</b-alert>
                </b-col>
            </b-row>

            <b-row>
                <div class="col col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <b-form @submit="onSubmit" @reset="onReset" v-if="show">

                        <b-form-group
                                label="Datumsbereich auswählen"
                                label-for="rangepicker"
                        >
                            <rangepicker :minDate="minDate" :maxDate="maxDate()" v-on:selected="setDateRange"></rangepicker>
                        </b-form-group>

                        <b-form-group
                                label="Daten-Format"
                                label-for="datatype"
                        >
                            <b-form-select v-model="selectedDataType" :options="datatypes" class="mb-3" id="datatype" size="lg" v-on:change="setDataType"></b-form-select>
                        </b-form-group>

                        <b-form-group
                                label="Episode auswählen"
                                label-for="files"
                        >
                            <b-form-select v-model="selectedFile" :options="files" class="mb-3" id="files" size="lg" v-on:change="setFile"></b-form-select>
                        </b-form-group>

                        <div class="form-group">
                            <b-button type="submit" variant="primary" :disabled="exceeded">Daten exportieren</b-button>
                            <!--<b-link v-on:click.stop="download" class="btn btn-primary">Klick</b-link>-->
                        </div>
                    </b-form>
                </div>

                <div class="col offset-xl-1 offset-lg-1 col-xl-8 col-lg-5 col-md-6 col-sm-12">
                    <h3>Exporte ({{ numberExports }}/{{ maxExports }})</h3>

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

                        <template slot="name" slot-scope="row">
                            <div v-if="row.item.show_title">
                                {{ row.item.show_title }}
                            </div>
                            <div v-else>
                                Alle Episoden
                            </div>
                        </template>

                        <template slot="date_range" slot-scope="row">
                            {{ row.item.start | dateOnly }} - {{ row.item.end | dateOnly }}
                        </template>

                        <template slot="actions" slot-scope="row">
                            <div v-if="row.item.is_exported">
                                <b-button-group>
                                    <b-button variant="primary" :href="'/spotify/statistiken/exports/' + row.item.id"><i class="icon-download"> Herunterladen</i></b-button>
                                    <b-button variant="outline-danger" @click.stop="deleteExport(row)"><i class="icon-trash"> Löschen</i></b-button>
                                </b-button-group>
                            </div>
                            <div v-else>
                                <b-progress :max="100" height="25px">
                                    <b-progress-bar :value="100" variant="info" animated>
                                        Export läuft...
                                    </b-progress-bar>
                                </b-progress>
                            </div>
                        </template>
                    </b-table>
                </div>
            </b-row>
        </b-container>
    </div>

</template>

<script>
    export default {
        name: "SpotifyStats",

        data () {
            return {
                form: {
                    start: this.daysAgoDate(7),
                    end: this.maxDate(),
                    show_title: null,
                    data_type: "raw",
                },
                show: true,
                isEtableBusy: false,
                minDate: this.minimumDate(),
                selectedDataType: "raw",
                selectedFile: null,
                datatypes: [
                    { value: "raw", text: 'Rohdaten' },
                    { value: 'daily', text: 'Tage (derzeit noch nicht verfügbar)', disabled: true },
                    { value: 'weekly', text: 'Wochen (derzeit noch nicht verfügbar)', disabled: true },
                    { value: 'monthly', text: 'Monate (derzeit noch nicht verfügbar)', disabled: true },
                ],
                files: [],
                avariant: 'success',
                atext: null,
                dismissSecs: 5,
                dismissCountDown: 0,
                maxExports: 3,
                exports: [],
                fields: [
                    {key: 'name', label: 'Episode', sortable: true, sortDirection: 'asc'},
                    {key: 'date_range', label: 'Zeitraum', sortable: true, sortDirection: 'desc'},
                    {key: 'actions', label: 'Aktionen'},
                ],
            }
        },
        methods: {
            onSubmit (evt) {
                evt.preventDefault();
                axios.post('/spotify/statistiken/exports', this.form)
                    .then(response => {
                        this.showAlert(response.data.message);
                        this.getExports();

                        Echo.private('spotify.stats.' + response.data.id)
                            .listen('\\App\\Events\\SpotifyStatisticsExportStarted', (e) => {
                                let idx = this.exports.findIndex(x => x.id === response.data.id);
                            })
                            .listen('\\App\\Events\\SpotifyStatisticsExportCreated', (e) => {
                                // @TODO
                                this.getExports();
                            });
                    })
                    .catch(error => {
                        this.showAlert(error.response.data.message, 'danger');
                    });
            },
            onReset (evt) {
                evt.preventDefault();
                /* Reset our form values */
                this.form.show_title = null;
                /* Trick to reset/clear native browser form validation state */
                this.show = false;
                this.$nextTick(() => { this.show = true });
            },
            minimumDate() {
                return new Date(2018, 3, 10);
            },
            maxDate() {
                return this.daysAgoDate(1);
            },
            daysAgoDate(days) {
                let d = new Date();
                d.setDate(d.getDate() - days);

                return d;
            },
            getMinimumDate() {
                axios.get('/spotify/statistiken/first')
                    .then(response => {
                        this.minDate = new Date(response.data*1000);
                    });
            },
            getFiles() {
                axios.post('/spotify/statistiken')
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
                        this.showAlert(error.response.data.message, 'danger');
                    });
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
            showAlert(msg, variant) {
                this.dismissCountDown = this.dismissSecs;
                this.atext = msg;
                this.avariant = variant || 'success';
            },
            getExports() {
                axios.get('/spotify/statistiken/exports')
                    .then(response => {
                        this.exports = response.data;
                    })
                    .catch(error => {
                        this.showAlert(error.response.data.message, 'danger');
                    });
            },
            deleteExport(row) {
                let id = row.item.id;

                if (confirm("Willst Du den Export wirklich löschen?")) {
                    let idx = this.exports.findIndex(x => x.id === id);
                    this.exports.splice(idx, 1);
                    axios.delete('/spotify/statistiken/exports/' + id)
                        .then((response) => {
                                this.showAlert(response.data.message);
                            },
                            (error) => {
                                this.showAlert(error.response.data.message, 'danger');
                                this.getExports();
                            })
                        .catch(error => {
                            this.showAlert(error.response.data.message, 'danger');
                            this.getExports();
                        });
                }
            },
        },

        mounted() {
            this.getMinimumDate();
            this.getFiles();
            this.getExports();
        },

        computed: {
            exceeded() {
                return this.exports.length >= this.maxExports;
            },
            numberExports() {
                return this.exports.length;
            }
        }
    }
</script>

<style scoped>

</style>
