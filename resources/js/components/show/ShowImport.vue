<template>
    <div>
        <b-card-title>
            <b-row>
                <b-col cols="12">
                    {{ $t('feeds.title_shows') }}
                    <b-button
                        size="sm">{{ shows.length }}
                    </b-button>
                </b-col>
            </b-row>
        </b-card-title>
        <b-card-header>
            <b-row>
                <b-col cols="12">
                    {{ counter }} {{ $t('feeds.message_import_of_shows_is_running') }}
                    <b-spinner type="grow" variant="success" class="m-1" :label="$t('shows.shows_loading')"></b-spinner>
                </b-col>
            </b-row>
        </b-card-header>
        <b-card-body v-show="shows.length > 0">
            <b-card-text v-for="(show, key) in shows" :key="show.title">
<!--                <b-icon
                    icon="check"
                    class="rounded-circle bg-succes p-2"
                    variant="light"></b-icon>-->
                <i class="icon-check"></i>

                {{ show.title }}
            </b-card-text>
        </b-card-body>
    </div>
</template>

<script>
import {eventHub} from "../../app";

export default {
    name: "ShowImport",

    data() {
        return  {
            status: null,
            shows: [],
            isLoading: true,
            counter: null,
        }
    },

    props: {
        feedId: {
            type: String,
            required: true
        }
    },

    mounted() {
        Echo.private('channel.show.import.' + this.feedId)
            .listen('\\App\\Events\\ChannelShowImportEvent', (e) => {
                if (e.state === 'finished') {
                    eventHub.$emit('update:shows:' + e.feedId);
                    eventHub.$emit('show-message:success', this.$t('feeds.message_shows_imported_successful'));
                } else if (e.state === 'failed') {
                    eventHub.$emit('update:shows:' + e.feedId);
                    eventHub.$emit('show-message:error', this.$t('feeds.message_shows_import_failed'));
                } else if (e.state === 'started') {
                    // Gives the number of shows to import
                    this.counter = e.title;
                } else {
                    this.shows.unshift({title: e.title, state: e.state});
                }
            });

        Echo.private('webserver.reloaded.' + this.feedId)
            .listen('\\App\\Events\\WebserverReloadedEvent', (e) => {
                eventHub.$emit('update:shows:' + e.feedId);
            });
    },
}
</script>

<style scoped>

</style>
