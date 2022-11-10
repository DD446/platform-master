<template>
    <b-card
            :title="$t('feeds.header_step_import_media')"
            class="mb-2">
        <b-card-text>
            {{ $t('feeds.text_step_import_media') }}
        </b-card-text>

        <div class="text-center">
            <b-button variant="podcaster" size="lg" v-on:click="goToMedia">{{ $t('feeds.button_choice_yes') }}</b-button>
            <b-button variant="outline-dark" size="lg" v-on:click="goToMeta">{{ $t('feeds.button_choice_no') }}</b-button>
        </div>

    </b-card>
</template>

<script>
    import {eventHub} from '../../../app';

    export default {
        name: "StepMediaPre",

        components: {
        },

        data() {
            return {
            }
        },

        methods: {
            goToMedia() {
                eventHub.$emit('jump-step:media');
            },
            goToMeta() {
                eventHub.$emit('jump-step:meta');
            },
        },

        mounted() {
        }
    }
</script>

<style scoped>

</style>