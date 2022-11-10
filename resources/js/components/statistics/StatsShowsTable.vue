<template>
    <div>
        <div class="d-flex justify-content-between">
            <div>
                <h3 class="mb-1">{{ header }}</h3>
                <span class="font-weight-light text-sm">Abrufe vom {{ range.start.toLocaleDateString() }} - {{ range.end.toLocaleDateString() }}</span>
            </div>
            <div style="font-size: 2rem;cursor:pointer" class="right-auto" @click.prevent="showExportOpts" v-b-popover.hover.right="$t('stats.popover_export')">
<!--                <b-icon icon="arrow-up-right" class="right-auto border p-1" v-b-popover.hover.right="$t('stats.popover_export')"></b-icon>-->
                <svg data-v-1d22998c="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="arrow up right" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-arrow-up-right right-auto border p-1 b-icon bi"><g data-v-1d22998c=""><path fill-rule="evenodd" d="M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0v-6z"></path></g></svg>
            </div>
        </div>
        <b-table
            class="mt-2"
            responsive
            show-empty
            stacked="md"
            :items="items"
            :fields="fields"
            :busy="isLoading"
            small
            hover
            striped
        >
            <template v-slot:cell(title)="row">
                <b-row>
                    <b-col md="auto" class="pr-1 d-none d-lg-block">
                        <img :src="row.item.cover" :alt="row.item.title" class="float-left img img-thumbnail" style="width:75px">
                    </b-col>
                    <b-col>
                            <span v-b-popover.hover.right="$t('stats.label_filename') + ': ' + row.item.file">
                                {{ row.item.title }}
                                <br>
                                <small>{{ row.item.podcast }}</small>
                            </span>
                    </b-col>
                </b-row>
            </template>

            <template v-slot:table-busy>
                <div class="text-center">
                    <b-spinner label="Lade Daten..." class="m-5" style="width: 3rem; height: 3rem;" aria-hidden="true" />
                </div>
            </template>

            <template v-slot:empty>
                <div class="pt-4 pb-4 text-center">
                    {{ $t('stats.warning_no_data') }}
                </div>
            </template>
        </b-table>
    </div>
</template>

<script>
import {eventHub} from "../../app";

export default {
    name: "StatsShowsTable",

    data () {
        return {
            items: [],
            isLoading: true,
            pageSize: 5,
            pageNumber: 1,
            range: {
                start: (d => new Date(d.setDate(d.getDate()-8)) )(new Date),
                end: (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
            },
            fields: [
                {key: 'title', label: this.$t('stats.label_show_title'), sortable: false, tdClass: '' },
                /*{key: 'podcast', label: this.$t('stats.label_feed_title'), sortable: true, class: 'text-center'},*/
                {key: 'published', label: this.$t('stats.label_show_published'), sortable: false, class: 'text-center'},
                {key: 'hits', label: this.$t('stats.label_hits'), sortable: true, class: 'text-center'}
            ],
            source: this.$route.query.s ? this.$route.query.s : null,
        }
    },

    props: {
        header: {
            type: String
        },
        action: {
            type: String
        },
        exportSettings: {
            type: Object
        }
    },

    methods: {
        getCount() {
            this.isLoading = true;
            let url = this.action + '?df=' + this.range.start.toISOString() + '&dt=' + this.range.end.toISOString() + '&page[size]=' + this.pageSize + '&page[number]=' + this.pageNumber;

            if (this.source) {
                url += '&s=' + this.source;
            }

            axios.get(url)
                .then(response => {
                    this.items = response.data;
                })
                .catch(error => {
                    eventHub.$emit('show-message:error', error);
                }).then(() => {
                    this.isLoading = false;
                });
        },
        showExportOpts() {
            eventHub.$emit('stats-export-modal:show', this.exportSettings);
        }
    },

    mounted() {
        this.getCount();

        eventHub.$on('change', (type, params) => {
            if (this.$route.name && this.$route.name.startsWith("podcaster-") || !this.$route.name) {
                if (type === 'date') {
                    this.range.start = params.start;
                    this.range.end = params.end;
                } else if (type === 'source') {
                    this.source = params;
                }
                this.getCount();
            }
        });
    }
}
</script>

<style scoped>
</style>
