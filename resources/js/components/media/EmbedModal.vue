<template>
    <b-modal
            ref="embedModal"
            :title="$t('mediamanager.embed_file')"
            :ok-only="true"
            :ok-title="$t('mediamanager.close_modal')"
            lazy>
        <template #modal-title>
            <div class="d-flex">
                <div>
                    <span v-text="$t('mediamanager.embed_file')"></span>
                </div>
                <div class="pl-1">
                    <a href="https://www.podcaster.de/faq/antwort-23-wie-finde-ich-den-link-zu-einer-mediendatei-um-diese-auf-einer-externen-seite-einzubinden"
                       class="d-none d-sm-block"
                       onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;"
                       v-b-popover.hover.top="$t('help.popover_faq')">
                        <img src="/images1/help/hilfe-faq.png" :alt="$t('help.alt_faq')" class="">
                    </a>
                </div>
            </div>
        </template>
        <p>Kopiere die passende Anweisung, um die Datei in eine Webseite einzubetten.</p>
        <form action="#" class="std">
            <h3>Direkt-Link</h3>
            <input type="url" class="form-control" :value="url" readonly="" @focus="doCopy">

            <hr>

            <h3>WordPress</h3>
            <input type="url" class="form-control" :value="wordpress" readonly="" @focus="doCopy">

            <hr>

            <h3>HTML5 Player (Jimdo, Wix, ...)</h3>
            <textarea readonly="" rows="5" class="form-control" @focus="doCopy">{{ html5 }}</textarea>

            <hr>

            <h3>Download-Link (HTML)</h3>
            <textarea readonly="" rows="5" class="form-control" @focus="doCopy">{{ download }}</textarea>

            <div v-if="shortcode">
                <hr>

                <h3>Shortcode Podlove</h3>
                <textarea readonly="" rows="5" class="form-control" @focus="doCopy">{{ shortcode }}</textarea>
            </div>
        </form>
    </b-modal>
</template>

<script>
    import {eventHub} from '../../app';

    export default {

        name: "embed-modal",

        data() {
            return {
                item: ''
            }
        },

        computed: {
            url() {
                return this.item.url;
            },

            html5() {
                switch (this.item.extension) {
                    case 'mp3':
                    case 'm4a':
                    case 'aac':
                    case 'ogg':
                    case 'oga':
                    case 'wav':
                    case 'weba':
                    case 'flac':
                        return '<audio controls>\n' +
                            '        <source src="' + this.item.url + '" type="' + this.item.mimetype + '">\n' +
                            '</audio>';
                    case 'mp4':
                    case 'ogv':
                    case 'webm':
                    case 'mov':
                    case 'avi':
                        return '<video controls>\n' +
                            '        <source src="' + this.item.url + '" type="' + this.item.mimetype + '">\n' +
                            '</video>';
                    case 'png':
                    case 'jpeg':
                    case 'jpg':
                    case 'gif':
                        return '<img src="' + this.item.url + '" alt="Bild">';
                    default:
                        return '<a href="' + this.item.url + '" onclick="window.open(this.href, \'_top\');return false;" download>Download</a>';
                }
            },

            wordpress() {
                if (this.item) {
                    return this.item.url.split('?').shift();
                }
            },

            download() {
                return '<a href="' + this.item.url + '" onclick="window.open(this.href, \'_top\');return false;" download>Episode herunterladen</a>';
            },

            shortcode() {

                switch (this.item.extension) {
                    case 'mp3':
                    case 'm4a':
                    case 'aac':
                    case 'ogg':
                    case 'oga':
                    case 'wav':
                    case 'weba':
                    case 'flac':
                        return '[podloveaudio src="' + this.item.url + '" title="" poster=""]';
                    case 'mp4':
                    case 'ogv':
                    case 'webm':
                    case 'mov':
                    case 'avi':
                        return '[podlovevideo src="' + this.item.url + '" title="" poster=""]';
                    default:
                        return '';
                }
            },

        },

        methods: {
            show() {
                this.$refs.embedModal.show();
            },

            doCopy: function (e) {
                e.target.select();

/*                let container = this.$refs;

                this.$copyText(e.target.value, container).then(function () {
                    eventHub.$emit('show-message:success', "Erfolgreich kopiert.");
                }, function (e) {
                    eventHub.$emit('show-message:error', e.toString());
                });*/
            }
        },

        created() {
        },

        mounted() {
            eventHub.$on("embed-modal:show", item => {
                this.item = item;
                this.show();
            });
        },
    }
</script>

<style scoped>

</style>
