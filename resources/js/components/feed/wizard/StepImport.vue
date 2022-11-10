<template>
    <b-card
            :title="$t('feeds.header_step_import')"
            class="mb-2">
        <b-card-text>
            {{ $t('feeds.text_step_import') }}
        </b-card-text>

        <div class="text-center">
            <b-button variant="podcaster" size="lg" v-on:click="askForFeed">{{ $t('feeds.button_choice_yes') }}</b-button>
            <b-button variant="outline-dark" size="lg" v-on:click="askForMeta">{{ $t('feeds.button_choice_no') }}</b-button>
        </div>

    </b-card>
</template>

<script>
    import {eventHub} from '../../../app';

    export default {
        name: "StepImport",

        components: {
        },

        props: {
        },

        data() {
            return {
                feed: {
                    subdomain: null,
                    domain: null,
                    feed_id: null,
                }
            }
        },

        methods: {
            askForFeed() {
                //window.location = '/legacy/podcast/wizard/domain?frmFeedSource=import';
                eventHub.$emit('enable-step:feed');
            },
            askForMedia() {
                // TODO: forLater - replaces askForMeta
                eventHub.$emit('enable-step:media');
            },
            askForMeta() {
                eventHub.$emit('jump-step:meta');
            },
            validate() {
                return true;
            }
        },

        mounted() {
        }
    }
</script>

<style scoped>

</style>
