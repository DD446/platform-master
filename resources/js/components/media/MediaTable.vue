<template>
    <b-container>
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
                        <b-navbar-brand>Typ</b-navbar-brand>
                        <b-nav-item @click="filter = ''" :disabled="!filter" v-show="filter">Alle</b-nav-item>
                        <b-nav>
                            <!--<b-nav-item @click="filter = ''" v-show="(filter === 'type:audio')">x</b-nav-item>-->
                            <b-nav-item @click="filter = 'type:audio'" :disabled="(filter === 'type:audio')">Audio</b-nav-item>
                        </b-nav>
                        <b-nav>
                            <!--<b-nav-item @click="filter = ''" v-show="(filter === 'type:image')">x</b-nav-item>-->
                            <b-nav-item @click="filter = 'type:image'" :disabled="(filter === 'type:image')">Bild</b-nav-item>
                        </b-nav>
                        <b-nav>
                            <!--<b-nav-item @click="filter = ''" v-show="(filter === 'type:image')">x</b-nav-item>-->
                            <b-nav-item @click="filter = 'type:text'" :disabled="(filter === 'type:text')">Text</b-nav-item>
                        </b-nav>
                        <b-nav>
                            <!--<b-nav-item @click="filter = ''" v-show="(filter === 'type:video')">x</b-nav-item>-->
                            <b-nav-item @click="filter = 'type:video'" :disabled="(filter === 'type:video')">Video</b-nav-item>
                        </b-nav>
                    </b-nav>
                </nav>
                <template v-if="hasLabels">
                    <hr class="short">
                    <b-navbar-nav class="flex-md-column">
                        <b-navbar-brand>Gruppe</b-navbar-brand>
                        <b-nav-item v-for="(value) in labels" @click='filter="folder:\"" + value + "\""' :key="value">
                            {{ value }}
                        </b-nav-item>
                    </b-navbar-nav>
                </template>
                <hr class="short">
                <b-button variant="primary" v-on:click="openUploader">Datei(en) hochladen</b-button>
            </b-col>
            <b-col md="10" cols="12" lg="9" class="my-1">

                <div class="row justify-content-between align-items-center mb-4">
                    <div class="col-lg-6 col-md-12">
                        <span class="text-muted text-small">
                            Ergebnisse {{ resultCount }} - {{ resultRecent }} von {{ resultTotal }}
                        </span>
                    </div>
                    <form class="col-lg-6 col-md-12 align-items-center">

                        <b-form-group label-cols-sm="4" label-cols-lg="3" label="Sortierung" class="mb-0">
                            <b-input-group>
                                <b-form-select v-model="sortBy" :options="sortOptions" v-on:change="persist">
                                    <option slot="first" :value="null">-- Keine --</option>
                                </b-form-select>
                                <b-form-select :disabled="!sortBy" v-model="sortDesc" slot="append" v-on:change="persist">
                                    <option :value=false>Aufsteigend</option>
                                    <option :value=true>Absteigend</option>
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
                    :items="items"
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc"
                    :busy.sync="isBusy"
                    no-local-sorting
                    :empty-text="'Du hast noch keine Mediendateien hochgeladen.'"
                    :empty-filtered-text="'Es gibt keinen Treffer für Deine Auswahl.'"
                    responsive="true"
                    ref="mtable"
                    :apiUrl="baseUrl"
                    :busy="isBusy"
                    @row-selected="rowSelected"
                    @sort-changed="sortChanged"
                    selectable
                    select-mode="range"
                >

                    <template v-slot:cell(created)="row">
                        {{ row.item.created_date }}
                        <br>
                        <span class="text-muted text-small">
                                {{ row.item.created_time }} Uhr
                        </span>
                    </template>

                    <template v-slot:cell(actions)="row">
                        <!-- We use @click.stop here to prevent a 'row-clicked' event from also happening -->
                        <b-dropdown id="ddown-divider" no-caret size="sm">
                            <template slot="button-content">
                                <i class="icon-dots-three-horizontal"></i>
                            </template>
                            <b-dropdown-item-button @click.passive="createShow(row.item)">
                                Episode anlegen
                            </b-dropdown-item-button>
                            <b-dropdown-item-button v-b-modal.renameModal @click.passive="setChosen(row.item)">
                                Umbenennen
                            </b-dropdown-item-button>
                            <b-dropdown-item-button @click.stop="showModal('embed', row.item)">
                                Einbetten
                            </b-dropdown-item-button>
                            <b-dropdown-item-button  v-on:click="openReplacer(row.item.id)">
                                Ersetzen
                            </b-dropdown-item-button>
                            <b-dropdown-item-button
                                @click.stop="showModal('group', row.item)">
                                Gruppe ändern
                            </b-dropdown-item-button>
                            <b-dropdown-item-button @click.stop="download(row.item.id)">
                                Herunterladen
                            </b-dropdown-item-button>
                            <b-dropdown-item-button
                                v-b-modal.copyModal
                                @click.passive="setChosen(row.item)">
                                Kopieren
                            </b-dropdown-item-button>
                            <b-dropdown-item-button
                                @click.stop="showModal('meta', { item: row.item, type: 'meta' })">
                                {{ $t('mediamanager.action_metadata') }}
                            </b-dropdown-item-button>
<!--                            <b-dropdown-item-button
                                @click.stop="showModal('meta',  { item: row.item, type: 'chapters' })">
                                {{ $t('mediamanager.action_chaptermarks') }}
                            </b-dropdown-item-button>-->
                            <b-dropdown-divider></b-dropdown-divider>
                            <b-dropdown-item-button @click.stop="showConfirmDialog(row.item)" variant="danger">
                                Löschen
                            </b-dropdown-item-button>
                        </b-dropdown>
                    </template>

                    <template v-slot:cell(name)="data">
                        <!-- A custom formatted data column cell -->
                        <div v-if="showCategory(data.item.cat)">
                            <span class="text-muted text-small">
                                {{ showCategory(data.item.cat) }}
                            </span>
                            <br>
                        </div>
                        <a href="#" @click.stop="showModal('info', data.item)">{{ data.value }}</a>
                    </template>

                    <template v-slot:table-busy>
                        <div class="text-center">
                            <b-spinner label="Lade Daten..." class="m-5" style="width: 3rem; height: 3rem;" aria-hidden="true" />
                        </div>
                    </template>

                    <template v-slot:empty>
                        <div class="pt-4 pb-4">
                            <span class="alert alert-warning">Keine Daten vorhanden.</span>
                        </div>
                    </template>

                    <template v-slot:table-caption>
                        <div v-if="rowsSelected">
                            <b-row>
                                <b-col>
                                    <b-form-group
                                        :label="countRowsSelected"
                                        label-for="multiActions"
                                        label-cols-sm="4"
                                        label-cols-lg="4"
                                    >
                                        <b-select id="multiActions" v-model="selectedMultiAction" :options="multiActions"></b-select>
                                    </b-form-group>
                                </b-col>
                                <b-col cols="1">
                                    <b-button variant="primary" @click.stop="execMultiActions">&gt;</b-button>
                                </b-col>
                            </b-row>
                        </div>
                        <p class="text-small text-muted font-weight-light">
                            Mehrere Zeilen mit Strg-Taste markieren. Liste von Zeilen mit Umschalttaste auswählen.
                        </p>
                    </template>

                </b-table>

                <b-row>
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

        <b-modal
                id="copyModal"
                ref="copyModal"
                title="Datei kopieren"
                @ok="handleCopy"
                @shown="focusMyElement"
                @cancel="clearName"
                @hidden="clearForm"
                centered
                :ok-only="true"
                ok-title="Kopieren"
                :state="state"
                lazy>
            <form @submit.stop.prevent="handleCopy" @keydown="form.onKeydown($event)">
                <alert-container></alert-container>
                <label for="copy">Datei</label>
                <b-form-input type="text"
                              id="copy"
                              required
                              ref="focusThis"
                              placeholder="Bitte gib den Namen ein."
                              v-model.trim="name"
                              aria-describedby="inputLiveHelpCopy inputLiveFeedbackCopy"
                              v-model="name"
                ></b-form-input>
                <b-form-invalid-feedback id="inputLiveFeedbackCopy">
                    <!-- This will only be shown if the preceeding input has an invalid state -->
                    Bitte gib einen Dateinamen an.
                </b-form-invalid-feedback>
                <b-form-text id="inputLiveHelpCopy">
                    <!-- this is a form text block (formerly known as help block) -->
                    Dateiname eingeben
                </b-form-text>
            </form>
        </b-modal>

        <b-modal
                id="renameModal"
                ref="renameModal"
                title="Datei umbenennen"
                @ok="handleRename"
                @shown="focusMyElement"
                @cancel="clearName"
                centered
                :ok-only="true"
                ok-title="Umbenennen"
                lazy>
            <form @submit.once="handleRename" @keydown="form.onKeydown($event)">
                <alert-container></alert-container>
                <input type="hidden" name="group" v-model="category">
                <label for="rename">Datei</label>
                <b-form-input type="text"
                              id="rename"
                              required
                              ref="focusThis"
                              placeholder="Bitte gib den neuen Namen ein."
                              v-model.trim="name"
                              aria-describedby="inputLiveHelp inputLiveFeedback"
                              v-model="name"
                              :state="state"
                ></b-form-input>
                <b-form-invalid-feedback id="inputLiveFeedback">
                    <!-- This will only be shown if the preceeding input has an invalid state -->
                    Bitte gib einen Dateinamen an.
                </b-form-invalid-feedback>
                <b-form-text id="inputLiveHelp">
                    <!-- this is a form text block (formerly known as help block) -->
                    Dateiname eingeben
                </b-form-text>
            </form>
        </b-modal>

        <b-modal
                id="createModal"
                ref="createModal"
                title="Episode anlegen"
                :ok-only="true"
                ok-title="Abbrechen"
                centered
                lazy>
            <h6>Wähle einen Podcast-Kanal aus:</h6>
            <b-list-group>
                <b-list-group-item
                        style="cursor:pointer;"
                        v-for="(feed) in feeds"
                        :key="feed.id"
                        v-on:click="handleCreate(feed)"
                >{{ feed.attributes.title }}</b-list-group-item>
            </b-list-group>
        </b-modal>

        <embed-modal></embed-modal>
        <info-modal></info-modal>
        <meta-modal></meta-modal>
        <group-modal></group-modal>
        <confirm-dialog title="Datei löschen"></confirm-dialog>

    </b-container>

</template>

<script>
    import Form from 'vform';
    import EmbedModal from './EmbedModal';
    import InfoModal from './InfoModal';
    import GroupModal from './GroupModal';
    import MetaModal from "./MetaModal";
    import ConfirmDialog from './ConfirmDialog';
    import {eventHub} from '../../app';
    import store from 'store2';

    export default {
        name: "mediatable",

        data() {
            return {
                fields: [
                    /*{key: 'multiaction', label: ''},*/
                    {key: 'name', label: 'Name', sortable: true, tdClass: 'mediatable-name-column' },
                    {key: 'size', label: 'Größe', sortable: true, class: 'text-center text-nowrap'},
                    {key: 'created', label: 'Erstellt', sortable: true, class: 'text-center'},
                    {key: 'actions', label: 'Aktionen'}
                ],
                currentPage: 1,
                perPage: 10,
                pageOptions: [5, 10, 15, 25, 50, 250, 500],
                sortBy: 'created',
                sortDesc: true,
                filter: null,
                isBusy: false,
                labels: [],
                feeds: [],
                totalRows: 0,
                name: '',
                category: '',
                item: {},
                items: this.myProvider,
                form: new Form(),
                selected: [],
                selectedMultiAction: null,
                baseUrl: '/mediathek',
                multiActions: [
                    { value: 'group', text: 'Gruppe ändern' },
                    { value: 'delete', text: 'Löschen' },
                ],
                state: true,
            };
        },
        computed: {
            sortOptions() {
                // Create an options list from our fields
                return this.fields
                    .filter(f => f.sortable)
                    .map(f => {
                        return {text: f.label, value: f.key}
                    })
            },
            nameState() {
                return this.name.length >= 1;
            },
            params() {
                return '?'
                    + 'currentPage=' + this.currentPage
                    + '&perPage=' + this.perPage
                    + '&sortBy=' + this.sortBy
                    + '&sortDesc=' + this.sortDesc
                    + '&filter=' + this.filter;
            },
            hasLabels() {
                return this.labels.length > 0;
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

        methods: {
            showModal(type, item) {
                eventHub.$emit(type + '-modal:show', item);
            },
            showConfirmDialog(item) {
                eventHub.$emit('confirm-dialog:show', item);
            },
/*            onFiltered(filteredItems) {
            },*/
            myProvider(oParam) {
                this.isBusy = true;
                // Here we don't set isBusy prop, so busy state will be handled by table itself
                let promise = axios.get(oParam.apiUrl
                    + '?currentPage=' + oParam.currentPage
                    + '&filter=' + oParam.filter
                    + '&perPage=' + oParam.perPage
                    + '&sortBy=' + oParam.sortBy
                    + '&sortDesc=' + oParam.sortDesc);

                return promise.then((response) => {
                    this.totalRows = response.data.count;
                    this.labels = response.data.labels;
                    // Here we could override the busy state, setting isBusy to false
                    this.isBusy = false;
                    // Pluck the array of items off our axios response
                    // Must return an array of items or an empty array if an error occurred
                    return(response.data.items || []);
                });
            },
            focusMyElement(e) {
                this.$refs.focusThis.focus();
            },
            setState(state) {
                this.state = state;
            },
            clearForm() {
                this.form = new Form();
            },
            setChosen(item) {
                this.item = item;
                this.name = item.name;
                this.category = item.cat || '_default_';
            },
            clearName() {
                this.name = ''
            },
            handleCopy(evt) {
                // Prevent modal from closing
                evt.preventDefault();
                if (!this.name) {
                    this.focusMyElement();
                    this.setState(false);
                } else {
                    this.form = new Form({
                        media_id: this.item.id,
                        label: this.name,
                        filesize: this.item.byte,
                        category: this.category
                    });
                    this.form.post('/api/media/' + this.item.id + '/copy')
                        .then(response => {
                            this.showMessage(response);
                            this.$refs.mtable.refresh();
                            this.$refs.copyModal.hide();
                        })
                        .catch(error => {
                            this.setState(!this.form.errors.has('name'));
                            this.showError(error);
                        });
                }
            },
            handleRename(evt) {
                // Prevent modal from closing
                evt.preventDefault();
                if (!this.name) {
                    this.focusMyElement();
                    this.setState(false);
                } else {
                    this.form = new Form({
                        id: this.item.id,
                        label: this.name,
                        category: this.category
                    });
                    this.form.post('/media/rename')
                        .then(response => {
                            this.showMessage(response);
                            this.$refs.mtable.refresh();
                            this.$refs.renameModal.hide();
                        })
                        .catch(error => {
                            this.setState(!this.form.errors.has('name'));
                            this.showError(error);
                        });
                }
            },
            download(id) {
                //axios.post('/media/download/' + id);
                let url = '/media/download/' + id;
                window.open(url, '_top');
            },
            createShow(item) {
                axios.get('/api/feeds')
                    .then((response) => {
                        this.setChosen(item);
                        let feeds = response.data.data;

                        if (feeds.length === 0) {
                            this.showAlert('Du musst zuerst einen Podcast-Kanal anlegen!', 'danger');
                        } else if (feeds.length === 1) {
                            window.location = '/podcasts#/podcast/' + feeds[0]['id'] + '/episode?media=' + this.item.id;
                            return false;
                        } else {
                            this.feeds = feeds;
                            this.$refs.createModal.show();
                        }
                    })
            },
            handleCreate(feed) {
                window.location = '/podcasts#/podcast/' + feed.id + '/episode?media=' + this.item.id;
            },
            openUploader() {
                window.open('/media/upload', 'uploader','width=1000,height=850,top=15,left=15,scrollbars=yes');
            },
            openReplacer(id) {
                window.open('/media/replace/' + id, 'replacer','width=1000,height=850,top=100,left=200,scrollbars=yes');
            },
            rowSelected(items) {
                this.selected = items;
            },
            execMultiActions() {
                if (this.selectedMultiAction === 'delete') {
                    this.showConfirmDialog(this.selected);
                }
                if (this.selectedMultiAction === 'group') {
                    this.showModal('group', this.selected);
                }
            },
            showCategory(category) {
                if (category !== '_default_') {
                    return category;
                }
            },
            showMessage(response) {
                eventHub.$emit('show-message:success', response.data.message ? response.data.message : response.data);
            },
            showError(error) {
                eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
            },
            sortChanged(ctx) {
                store.set('mediacenterSettings', { sortBy: ctx.sortBy, sortDesc: ctx.sortDesc, perPage: ctx.perPage });
            },
            persist() {
                store.set('mediacenterSettings', { sortBy: this.sortBy, sortDesc: this.sortDesc, perPage: this.perPage });
            }
        },
        created() {
            eventHub.$on("media-table:refresh", () => {
                this.$refs.mtable.refresh();
            });
/*            eventHub.$on("media-table:remove-item", item => {
            });*/
        },
        mounted() {
            let settings = store.get('mediacenterSettings');

            if (settings) {
                this.sortBy = settings.sortBy;
                this.sortDesc = settings.sortDesc;
                this.perPage = settings.perPage;
            }
        },
        components: {
            EmbedModal,
            InfoModal,
            GroupModal,
            MetaModal,
            ConfirmDialog
        }
    }
</script>

<style scoped>

</style>
