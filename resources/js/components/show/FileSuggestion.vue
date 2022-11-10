<template>
    <b-card
        class="mb-2"
    >
        <template v-slot:header>
            <span
                class="pt-2"
                style="overflow: hidden;white-space: nowrap;text-overflow: ellipsis;display: inherit" :title="file.name">{{file.name}}</span>
        </template>

        <b-card-text>
            <b-row>
                <b-col cols="6">
                    <audio-play-button
                        hide-tooltip
                        size="sm"
                        :url="this.file.intern"
                        :filename="this.file.name"
                        :duration="this.file.size"></audio-play-button>
                </b-col>
                <b-col cols="6">
                    <b-button
                        class="float-right"
                        variant="primary"
                        size="sm"
                        @click="setFile">Auswählen</b-button>
                </b-col>
            </b-row>
        </b-card-text>
    </b-card>
</template>

<script>
    import {eventHub} from '../../app';
    import AudioPlayButton from "../AudioPlayButton";

    export default {
        name: "FileSuggestion",

        components: {
            AudioPlayButton
        },

        data: function () {
            return {
                isActive: false
            }
        },

        props: {
            file: {
                required: true,
                type: Object
            },
        },

        methods: {
            showButton() {
                this.isActive = true;
            },
            hideButton() {
                this.isActive = false;
            },
            setFile() {
                eventHub.$emit('file:selected', this.file);
            }
        },

        created() {
            this.isActive = this.isMobile();
        }
    }
</script>

<style scoped>
    .selectButton {
        position:absolute;
        left:0;
        bottom:0;
        margin-left: 1px;
        margin-right: 1px;
        margin-bottom: 1px;
    }
</style>
