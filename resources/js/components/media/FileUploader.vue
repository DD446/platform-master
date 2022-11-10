<template>
    <div>
        <vue-dropzone
            ref="myVueDropzone"
            id="dropzone"
            :options="dropzoneOptions"
            :useCustomSlot=true
            :use-font-awesome="true"
            v-on:vdropzone-error="failed"
            v-on:vdropzone-sending="sendingEvent"
            v-on:vdropzone-success="succeeded"
            v-show="!blockUpload && !iOS()"
        >
            <div class="dropzone-custom-content">
                <h3 class="dropzone-custom-title"><i class='fa fa-cloud-upload'></i> {{ uploadHint }}</h3>
                <div class="subtitle">{{ uploadHintExtra }}</div>
            </div>
        </vue-dropzone>

        <form action="/media/upload" method="post" enctype="multipart/form-data" v-if="iOS()">
            <input type="hidden" :value="csrf" name="_token">
            <label>Datei(en) auswählen</label>
            <input type="file" name="file" />
            <button type="submit" class="btn btn-primary mt-2 float-right">Datei(en) hochladen</button>
        </form>

        <b-row class="m-4" v-if="blockUpload">
            <div class="text-center alert-warning m-1 p-4">
                <b-card-text v-html="$t('main.text_hint_upgrade_package_or_buy_storage', {routePackages: '/pakete', routePackageExtras: '/pakete/extras'})"></b-card-text>
            </div>
        </b-row>
    </div>
</template>

<script>
    import vue2Dropzone from 'vue2-dropzone';
    import 'vue2-dropzone/dist/vue2Dropzone.min.css';
    import {eventHub} from '../../app';

    export default {
        name: 'fileuploader',

        components: {
            vueDropzone: vue2Dropzone
        },

        props: {
            'url': {
                type: String,
                default: '/media/upload',
            },
            'method': {
                type: String,
                default: 'POST',
            },
            'maxfiles': {
                type: String,
                default: null
            },
            'paralleluploads': {
                type: String,
                default: "1"
            },
            'acceptedFiles': {
                type: String,
                default: null
            },
            'uploadText': {
                type: String
            },
            'uploadTextExtra': {
                type: String
            },
            'maxFilesize': {
                type: String,
                default: "4000"
            },
            'thumbnailWidth': {
                type: String,
                default: "200"
            },
            'feed': {
                type: String,
                default: null,
            },
            'guid': {
                type: String,
                default: null,
            },
            'usage': {
                type: String,
                default: null,
            },
            'noChunking': {
                type: Boolean,
                default: false,
            },
        },

        data: function () {
            return {
                dropzoneOptions: {
                    url: this.url,
                    thumbnailWidth: this.thumbnailWidth,
                    addRemoveLinks: true,
                    method: this.method,
                    timeout: 0,
                    headers: {
                        "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content,
                        "Accept": "application/json"
                    },
                    acceptedFiles: this.acceptedFiles,
                    maxFiles: this.maxfiles,
                    maxFilesize: this.maxFilesize,
                    duplicateCheck: true,
                    parallelUploads: this.paralleluploads,
                    chunking: !this.noChunking,
                    // CAUTION: This ends in a 504 Gateway Timeout ! - Do NOT set to true
                    // If true, the individual chunks of a file are being uploaded simultaneously.
                    parallelChunkUploads: false,
                    chunkSize: 5000000,
                    retryChunks: true,
                    retryChunksLimit: 3,
                    dictFallbackMessage: this.$t('fileuploader.dictFallbackMessage'),
                    dictFallbackText: this.$t('fileuploader.dictFallbackText'),
                    dictFileTooBig: this.$t('fileuploader.dictFileTooBig'),
                    dictInvalidFileType: this.$t('fileuploader.dictInvalidFileType'),
                    dictResponseError: this.$t('fileuploader.dictResponseError'),
                    dictCancelUpload: this.$t('fileuploader.dictCancelUpload'),
                    dictUploadCanceled: this.$t('fileuploader.dictUploadCanceled'),
                    dictCancelUploadConfirmation: this.$t('fileuploader.dictCancelUploadConfirmation'),
                    dictRemoveFile: this.$t('fileuploader.dictRemoveFile'),
                    dictMaxFilesExceeded: this.$t('fileuploader.dictMaxFilesExceeded'),
                },
                blockUpload: false,
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        },

        computed: {
            uploadHint() {
                if (this.uploadText) {
                    return this.uploadText;
                }

                if (this.maxfiles == 1) {
                    return this.$t('fileuploader.header_upload_form_single');
                } else if(this.maxfiles > 1) {
                    return this.$t('fileuploader.header_upload_form_multi', { count: this.maxfiles });
                } else {
                    return this.$t('fileuploader.header_upload_form');
                }
            },

            uploadHintExtra() {

                if (this.uploadTextExtra) {
                    return this.uploadTextExtra;
                }

                if (this.maxfiles == 1) {
                    return this.$t('fileuploader.header_upload_hint_extra_single');
                } else {
                    return this.$t('fileuploader.header_upload_hint_extra_multi');
                }
            }
        },

        methods: {
            failed: function(file, message, xhr) {
                if (xhr !== undefined) {
                    let response = xhr.response || {};
                    let parse = JSON.parse(response, (key, value) => {
                        return value;
                    });
                    let msg = '';
                    if (parse.errors !== undefined) {
                        parse.errors.file.forEach(function(element) {
                            msg += element + "\n";
                        })
                    } else {
                        msg = parse.message;
                    }
                    eventHub.$emit('show-message:error', msg);
                    let err = document.querySelectorAll('[data-dz-errormessage]')[0];
                    err.textContent = msg;
                }
            },
            sendingEvent (file, xhr, formData) {
                if (this.method === 'PUT') {
                    formData.append('_method', 'PUT');
                }
                if (this.feed) {
                    formData.append('feed', this.feed);
                }
                xhr.ontimeout = function (e) {
                    let err = document.querySelectorAll('[data-dz-errormessage]')[0];
                    err.textContent = this.$t('fileuploader.error_timeout');
                };
            },
            succeeded(file, _xhr) {
                // On chunks upload with files that actually get chunked
                // the XMLHttpRequest, here: _xhr, is empty
                // so we need this workaround because the xhr is attached to the first parameter "file"
                var xhr = _xhr || JSON.parse(file.xhr.response);

                if (typeof xhr.done != 'undefined') {
                    // Return from chunk upload
                } else if (this.guid) {
                    // File Uploader used in AddShow / UpdateShow for uploading logo and file
                    if (this.usage === 'logo') {
                        eventHub.$emit('show:image:set', xhr.file);
                    } else {
                        eventHub.$emit('file:selected', xhr.file);
                    }
                    this.showMessage(xhr.message);
                } else if(this.feed) {
                    eventHub.$emit('usage:refresh:' + this.feed);
                } else {
                    if (typeof window.opener !== 'undefined' && window.opener) {
                        let opener = window.opener.media;

                        if (opener && typeof opener.$children !== 'undefined') { // File Uploader initiated from mediathek
                            if (opener.$children[1].$refs.mtable) {
                                opener.$children[1].$refs.mtable.refresh();

                                if (window.name === 'replacer') {
                                    opener.$children[0].showAlert(xhr.message.success);
                                    window.close();
                                } else {
                                    opener.$children[0].showAlert(xhr.message.success);
                                }
                                eventHub.$emit('usage:refresh');
                            }
                        } else {
                            opener = window.opener;

                            if (opener) {
                                opener.location.reload();
                            }
                        }
                    }
                }
            },

            setFilesizeOutputOption() {
                this.$refs.myVueDropzone.dropzone.filesize = function (bytes) {
                    let selectedSize = 0;
                    let selectedUnit = 'b';
                    let units = ['kb', 'mb', 'gb', 'tb'];

                    if (Math.abs(bytes) < this.options.filesizeBase) {
                        selectedSize = bytes;
                    } else {
                        var u = -1;
                        do {
                            bytes /= this.options.filesizeBase;
                            ++u;
                        } while (Math.abs(bytes) >= this.options.filesizeBase && u < units.length - 1);

                        selectedSize = bytes.toFixed(1);
                        selectedUnit = units[u];
                    }

                    return `<strong>${selectedSize}</strong> ${this.options.dictFileSizeUnits[selectedUnit]}`;
                };
            },
            iOS() {
                return [
                        'iPad Simulator',
                        'iPhone Simulator',
                        'iPod Simulator',
                        'iPad',
                        'iPhone',
                        'iPod'
                    ].includes(navigator.platform)
                    // iPad on iOS 13 detection
                    || (navigator.userAgent.includes("Mac") && "ontouchend" in document);
            }
        },

        mounted() {
            this.setFilesizeOutputOption();
        },

        created() {
            eventHub.$on("upload:block", () => {
                this.blockUpload = true;
            });
            eventHub.$on("upload:clear", () => {
                //this.$refs.myVueDropzone.removeAllFiles();
                this.$emit("vdropzone-reset");
            });
        },
    }
</script>

<style scoped>

</style>
