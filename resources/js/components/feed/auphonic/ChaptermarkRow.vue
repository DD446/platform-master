<template>
    <b-row class="mb-1">
        <b-col cols="1" offset="11" class="d-none d-md-block">
            <b-button
                @click=""
                variant="danger"
                size="sm">{{ $t('auphonic.button_chaptermark_delete_row') }}</b-button>
        </b-col>
        <b-col cols="12" md="3">
            <b-form-group
                :label="$t('auphonic.label_hour') + ':' + $t('auphonic.label_minute') + ':' + $t('auphonic.label_second')"
            >
                <b-input-group>
                    <b-form-input
                        v-model="chapter.hour"
                        type="number"
                        required
                        max="999"
                        min="0"
                        aria-label="Hours"></b-form-input>
                    <b-input-group-prepend is-text><b>:</b></b-input-group-prepend>
                    <b-form-input
                        v-model="chapter.minute"
                        type="number"
                        required
                        min="0"
                        max="59"
                        aria-label="Minutes"></b-form-input>
                    <b-input-group-prepend is-text><b>:</b></b-input-group-prepend>
                    <b-form-input
                        v-model="chapter.second"
                        type="number"
                        required
                        min="0"
                        max="59"
                        aria-label="Seconds"></b-form-input>
                </b-input-group>
            </b-form-group>
        </b-col>
        <b-col cols="12" md="3" class="mt-2 mt-md-0">
            <b-form-group
                :label="$t('auphonic.label_chaptermark_title')"
            >
                <b-input
                    v-model="chapter.title"
                    required
                ></b-input>
            </b-form-group>
        </b-col>
        <b-col cols="12" md="3" class="mt-2 mt-md-0">
            <b-form-group
                :label="$t('auphonic.label_chaptermark_url')"
            >
                <b-input
                    v-model="chapter.url"
                    :placeholder="$t('auphonic.chapter_url_placeholder')"
                    type="url"
                ></b-input>
            </b-form-group>
        </b-col>
        <b-col cols="12" md="3" class="mt-2 mt-md-0">
            <b-form-group
                :label="$t('auphonic.label_chaptermark_url')"
            >
                <b-file></b-file>
            </b-form-group>
        </b-col>
        <b-col cols="1" class="d-block d-md-none">
            <b-button
                @click=""
                variant="danger"
                size="sm">{{ $t('auphonic.button_chaptermark_delete_row') }}</b-button>
        </b-col>
    </b-row>
</template>

<script>
export default {
    name: "ChaptermarkRow",

    data() {
        return {
            chapter: {
                hour: 0,
                minute: 0,
                second: 0,
                title: null,
                url: null,
            }
        }
    },

    mounted() {
        //this.chapter = this.row;
    },

    props: {
        row: {
            type: Object,
            default: {}
        },
    },
}
</script>

<style scoped>

</style>
