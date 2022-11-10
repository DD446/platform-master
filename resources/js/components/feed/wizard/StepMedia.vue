<template>
    <b-card
            :title="$t('feeds.header_step_media')"
            class="mb-2">
        <b-card-text>
            {{ $t('feeds.text_step_media') }}
        </b-card-text>

        <b-card no-body>
            <b-tabs card>
                <b-tab :title="$t('feeds.tab_step_media_title_upload')" active>
                    <b-card-text>
                        {{ $t('feeds.tab_step_media_text_upload') }}
                    </b-card-text>

                    <fileuploader
                        maxfiles="1"></fileuploader>
                </b-tab>
                <b-tab :title="$t('feeds.tab_step_media_title_remote')">
                    <b-card-text>{{ $t('feeds.tab_step_media_text_remote') }}</b-card-text>

                    <b-form-group>
                        <b-input type="url" :placeholder="$t('feeds.placeholder_media_url')" required></b-input>
                    </b-form-group>
                </b-tab>
                <b-tab :title="$t('feeds.tab_step_media_title_dropbox')">
                    <b-card-text>{{ $t('feeds.tab_step_media_text_dropbox') }}</b-card-text>

                    <file-downloader-dropbox></file-downloader-dropbox>
                </b-tab>
                <b-tab :title="$t('feeds.tab_step_media_title_mediacenter')">
                    <b-card-text>{{ $t('feeds.tab_step_media_text_mediacenter') }}</b-card-text>

                    <file-selector></file-selector>
                </b-tab>
                <b-tab :title="$t('feeds.tab_step_media_title_auphonic')">
                    <b-card-text>{{ $t('feeds.tab_step_media_text_auphonic') }}</b-card-text>
                </b-tab>
            </b-tabs>
        </b-card>
    </b-card>
</template>

<script>
    import FileUploader from "../../media/FileUploader";
    import FileDownloaderDropbox from "../../media/FileDownloaderDropbox";
    import FileSelector from "../../media/FileSelector";

    export default {
        name: "StepMedia",

        components: {
            FileSelector,
            FileUploader,
            FileDownloaderDropbox
        }
    }
</script>

<style scoped>

</style>
