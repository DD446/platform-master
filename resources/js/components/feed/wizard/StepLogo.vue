<template>
    <b-card
            :title="$t('feeds.header_step_logo')"
            class="mb-2">
        <b-card-text>
            {{ $t('feeds.text_step_logo') }}
        </b-card-text>

        <b-card-body>
            <b-col>
                <div>
                    <b-form
                        ref="logoForm"
                        enctype="multipart/form-data"
                        v-if="!image"
                    >
                        <b-form-group>
                            <b-form-file
                                id="logo-upload"
                                size="lg"
                                :state="Boolean(imageObj)"
                                v-model="imageObj"
                                autofocus
                                :aria-label="$t('feeds.text_logo_upload_label')"
                                @input="uploadLogo"
                                :browse-text="$t('feeds.text_logo_upload_browse_text')"
                                :drop-placeholder="$t('feeds.placeholder_logo_upload_drag_n_drop')"
                                :placeholder="$t('feeds.placeholder_logo_upload')"
                                accept=".jpg,.png"></b-form-file>
                        </b-form-group>
                    </b-form>

                    <div v-if="image">
                        <b-row class="pb-2 pr-2 pb-lg-4 pr-lg-3">
                            <b-col cols="11">
                                <span v-if="imagename">
                                    <b-input-group :prepend="$t('feeds.text_logo_selected_file')" size="sm">
                                        <b-input size="sm" readonly disabled :value="imagename"></b-input>
                                    </b-input-group>
                                </span>
                                <span v-else>
                                    <b-input-group :prepend="$t('feeds.text_logo_from_url')" size="sm">
                                        <b-input size="sm" readonly disabled :value="this.dat.imageUrl"></b-input>
                                    </b-input-group>
                                </span>
                            </b-col>
                            <b-col cols="1">
<!--                                <b-btn-close
                                    size="lg"
                                    @click="image = imagename = null"
                                    v-b-popover.hover="$t('feeds.text_logo_upload_deselect')"></b-btn-close>-->
                                <b-button variant="warning"
                                          size="sm"
                                          @click="imageObj = image = imagename = feed.logo.itunes = null"
                                          v-b-popover.hover="$t('feeds.text_logo_upload_deselect')">&times;</b-button>
                            </b-col>
                        </b-row>
                        <b-overlay
                            :show="!image"
                            rounded="sm">
                            <b-container class="bg-dark p-1">
                                <b-img
                                    fluid-grow
                                    :alt="$t('feeds.text_logo_preview_alternative_text')"
                                    :src="image"></b-img>
                            </b-container>
<!--                            <cropper
                                ref="cropper"
                                classname="img-fluid"
                                :src="imgUri"
                                :auto-zoom="true"
                                :stencil-props="{ aspectRatio: 1, minWidth: 1400, maxWidth: 3000, minHeight: 1400, maxHeight: 3000, defaultSize: 1400 }"
                                @change="change"
                            />-->
<!--                            <b-button
                                variant="primary"
                                size="lg"
                                @click="crop"
                                >{{$t('feeds.text_button_logo_upload_crop')}}</b-button>-->
<!--                            <b-button
                                class="mt-2 align-content-center"
                                variant="primary"
                                size="lg"
                                @click="uploadLogo"
                                >{{$t('feeds.text_button_logo_upload')}}</b-button>-->
                        </b-overlay>
                    </div>

                    <div class="alert alert-info" role="alert" v-show="!image">
                        <p>{{ $t('feeds.warning_no_logo_text')}}</p>
                        <ul class="list-group" v-for="value in restrictions">
                            <li class="list-group-item list-group-item-light">{{ $t('feeds.hint_logo_requirement_' + value) }}</li>
                        </ul>
                    </div>
                </div>
            </b-col>
        </b-card-body>

    </b-card>
</template>

<script>
    import FileUploader from "../../media/FileUploader";
    import {eventHub} from "../../../app";
    //import { Cropper } from 'vue-advanced-cropper';
    //import 'vue-advanced-cropper/dist/style.css';

    function b64ToBlob(b64Image) {
        var img = atob(b64Image.split(',')[1]);
        var img_buffer = [];
        var i = 0;
        while (i < img.length) {
            img_buffer.push(img.charCodeAt(i));
            i++;
        }

        return new Blob([ new Uint8Array(img_buffer) ], {type: "image/jpg"});
    }

    var isValidImageFromUrl = function(src) {
        try {
            var img = new Image();
            img.onload = () => {
                var height = img.height;
                var width = img.width;

                if (height !== width) {
                    throw "Image is not square.";
                }

                if (height < 1400) {
                    throw "Image is too small. Side length is " + height;
                }

                if (height > 3000) {
                    throw "Image is too big.";
                }
                return true;
            }
            img.src = src;
        } catch (e) {
            return false;
        }
    }

    export default {
        name: "StepLogo",

        components: {
            FileUploader
            //Cropper
        },

        props: {
            dat: {
                type: Object,
                default: {
                    image: null
                }
            },
            logoUploadUrl: {
                type: String,
                default: "/feed/logo"
            }
        },

        data() {
            return {
                feed: this.dat,
                image: null,
                imagename: null,
                imageObj: null,
                croppedImage: null,
                restrictions: [
                    'type',
                    'extension',
                    'square',
                    'dimension_min',
                    'dimension_max',
                    'size',
                    'colormodel',
                    'transparency',
                ],
                coordinates: {
                    width: 0,
                    height: 0,
                    left: 0,
                    top: 0
                },
            }
        },

        methods: {
            previewUpload(e) {
                const file = e.target.files[0];
                this.image = URL.createObjectURL(file);
                this.imagename = file.name;
            },
            crop() {
                const {coordinates, canvas} = this.$refs.cropper.getResult();
                this.coordinates = coordinates;
                // You able to do different manipulations at a canvas
                // but there we just get a cropped image
                this.croppedImage = this.imgUri = canvas.toDataURL()
            },
            uploadLogo(file) {
                this.imagename = file.name;
                let data = new FormData();
                data.append('file', file);

                let config = {
                    header : {
                        'Content-Type' : 'multipart/form-data'
                    }
                };

                axios.post('/feed/logo', data, config)
                    .then(response => {
                        //eventHub.$emit('enable-step:link');
                        this.showMessage(response);
                        this.image = URL.createObjectURL(file);
                        this.feed.logo.itunes = response.data.id;
                    }).catch(error => {
                        this.showError(error);
                    }).then(() => {
                        window.scrollTo(0,0);
                    });
            },
            uploadCroppedLogo() {
                let data = new FormData();
                data.append('name', 'image');
                data.append('file', b64ToBlob(this.croppedImage));

                let config = {
                    header : {
                        'Content-Type' : 'multipart/form-data'
                    }
                };

                axios.post('/feed/logo', data, config)
                    .then(response => {
                        eventHub.$emit('enable-step:link');
                        //this.showMessage(response);
                    }).catch(error => {
                        this.showError(error);
                    });
            },
            async validate() {
                let valid = true;

                if (!this.image) {
                    return valid;
                }

/*                await axios.post('/feed/logo/check', {file: this.image, name: this.imagename})
                    .then(response => {
                    }).catch(error => {
                        window.scrollTo(0,0);
                        this.showError(error);
                        valid = false;
                    });*/

                if (valid) {
                    eventHub.$emit('on-validate', this.$data, true);
                }

                return valid;
            }
/*            change({ coordinates, canvas }) {
                console.log(coordinates, canvas)
            }*/

        },

/*        mounted() {
            this.feed = this.dat;
        },*/

        watch: {
            dat(newVal, oldVal) {
                this.feed = newVal;

                if (!this.image && this.feed.logo.itunes) {
                    this.image = '/mediathek/' + this.feed.logo.itunes;
                }
            }
        },
    }
</script>

<style scoped>

</style>
