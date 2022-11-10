<template>
    <div>
        <b-row class="container">
            <b-col lg="11" class="m-5">
                <b-button variant="primary" id="dropbox-container" v-on:click="openDropboxWindow">
                    <i class="fa fa-dropbox"></i> Datei aus Dropbox auswählen
                </b-button>
            </b-col>
            <b-col lg="11" class="mb-5" v-if="isLoading">
                <b-row>
                    <b-col cols="3">
                        <b-img-lazy :src="thumbnail" thumbnail v-show="thumbnail"></b-img-lazy>
                    </b-col>
                    <b-col cols="9">
                        <b-spinner class="mr-2" size="lg"></b-spinner> Übertrage Datei <span v-text="filename" class="font-weight-bold"></span>...
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    import {eventHub} from "../../app";

    let options = {

        // Required. Called when a user selects an item in the Chooser.
        success: function(files) {

        },

        // Optional. Called when the user closes the dialog without selecting a file
        // and does not include any parameters.
        cancel: function() {

        },

        // Optional. "preview" (default) is a preview link to the document for sharing,
        // "direct" is an expiring link to download the contents of the file. For more
        // information about link types, see Link types below.
        linkType: "direct", // or "direct"

        // Optional. A value of false (default) limits selection to a single file, while
        // true enables multiple file selection.
        multiselect: false, // or true

        // Optional. This is a list of file extensions. If specified, the user will
        // only be able to select files with these extensions. You may also specify
        // file types, such as "video" or "images" in the list. For more information,
        // see File types below. By default, all extensions are allowed.
        extensions: ['.pdf', '.png', '.jpg', '.jpeg', '.mp3', '.aac', '.m4a', '.mp4', '.ogg', '.weba', '.webm', '.txt'],

        // Optional. A value of false (default) limits selection to files,
        // while true allows the user to select both folders and files.
        // You cannot specify `linkType: "direct"` when using `folderselect: true`.
        folderselect: false, // or true

        // Optional. A limit on the size of each file that may be selected, in bytes.
        // If specified, the user will only be able to select files with size
        // less than or equal to this limit.
        // For the purposes of this option, folders have size zero.
        //sizeLimit: 1024, // or any positive number
    };

    let redirectToIndex = function(data) {
        alert(data);
    };


    export default {
        name: 'FileDownloaderDropbox',

        components: {
        },

        props: [],

        data(){

            return{
                dboptions: {
                    success: this.uploadCallback,

                    // Optional. Called when the user closes the dialog without selecting a file
                    // and does not include any parameters.
                    cancel: function() {

                    },

                    // Optional. "preview" (default) is a preview link to the document for sharing,
                    // "direct" is an expiring link to download the contents of the file. For more
                    // information about link types, see Link types below.
                    linkType: "direct", // or "direct"

                    // Optional. A value of false (default) limits selection to a single file, while
                    // true enables multiple file selection.
                    multiselect: false, // or true

                    // Optional. This is a list of file extensions. If specified, the user will
                    // only be able to select files with these extensions. You may also specify
                    // file types, such as "video" or "images" in the list. For more information,
                    // see File types below. By default, all extensions are allowed.
                    extensions: ['.pdf', '.png', '.jpg', '.jpeg', '.mp3', '.aac', '.m4a', '.mp4', '.ogg', '.weba', '.webm', '.txt'],

                    // Optional. A value of false (default) limits selection to files,
                    // while true allows the user to select both folders and files.
                    // You cannot specify `linkType: "direct"` when using `folderselect: true`.
                    folderselect: false, // or true

                    // Optional. A limit on the size of each file that may be selected, in bytes.
                    // If specified, the user will only be able to select files with size
                    // less than or equal to this limit.
                    // For the purposes of this option, folders have size zero.
                    //sizeLimit: 1024, // or any positive number
                },
                isLoading: false,
                thumbnail: null,
                filename: null,
                value: 0,
                max: 100,
            }
        },
        methods:{

            uploadCallback: function(files) {
                this.isLoading = true;
                this.thumbnail = files[0].thumbnailLink;
                this.filename = files[0].name;

                axios.post('/media/load', { url: files[0].link, name: files[0].name })
                .then(response => {
                    eventHub.$emit('show-message:success', response.data);
                }).catch(function(error){
                    eventHub.$emit('show-message:error', error);
                }).then(() => {
                    eventHub.$emit('usage:refresh');
                    this.isLoading = false;
                });
            },

            openDropboxWindow: function() {
                Dropbox.choose(this.dboptions);
            }
        }
    }
</script>

<style scoped>

</style>
