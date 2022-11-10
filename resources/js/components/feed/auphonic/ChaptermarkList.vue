<template>
    <div>
<!--        <b-row>
            <b-col class="d-none d-md-block">
                <label>
                    {{ $t('auphonic.label_hour') }}</label> : <label>{{ $t('auphonic.label_minute') }}</label> : <label>{{ $t('auphonic.label_second') }}</label></b-col>
            <b-col class="d-none d-md-block">
                <label>
                    {{ $t('auphonic.label_chaptermark_title') }}
                </label>
            </b-col>
            <b-col class="d-none d-md-block">
                <label>
                    {{ $t('auphonic.label_chaptermark_url') }}
                </label>
            </b-col>
            <b-col cols="1"></b-col>
        </b-row>-->
        <ul class="list-unstyled">
            <li v-for="(row, key) in chapters" class="">
                <b-row>
                    <b-col>
                        <h5>{{ $t('auphonic.header_chapter') }}{{ ++key }}</h5>
                    </b-col>
                </b-row>
                <chaptermark-row :row="row"></chaptermark-row>
            </li>
        </ul>
        <div class="mt-2 float-right">
            <b-button @click="chapters.push({})" size="sm" variant="info">{{ $t('auphonic.button_add_row') }}</b-button>
        </div>
    </div>
</template>

<script>
import ChaptermarkRow from "./ChaptermarkRow";
export default {
    name: "ChaptermarkList",

    components: {ChaptermarkRow},

    data() {
        return {
            chapters: [{}]
        }
    },
}
</script>

<style scoped>

</style>
