<template>
    <div>
        <b-button
            v-on:click="play"
            variant="outline-success"
            pill
            :size="this.size"
        >
            <!--            v-b-popover.hover.top="!!this.hideTooltip || getToolTip"-->
            <i :class="playPauseState"></i></b-button> <span v-text="this.duration" class="text-muted ml-2"></span>
    </div>
</template>

<script>
    import {eventHub} from "../app";

    export default {
        name: "AudioPlayButton",

        components: {
        },

        data() {
            return {
                audio: null,
                isPlaying: false,
            }
        },

        props: {
            url: {
                type: String,
                required: true
            },
            filename: {
                type: String,
                required: true
            },
            duration: {
                type: String,
            },
            hideTooltip: {
                type: Boolean,
                default: false
            },
            tooltipTitle: {
                type: String,
                default: function() { return this.$t('shows.title_audio_is_available'); },
            },
            tooltipContent: {
                type: String,
                default: function() { return this.$t('shows.content_audio_is_available', { file: this.filename }); },
            },
            size: {
                type: String,
                default: 'sm'
            }
        },

        created() {
        },

        mounted() {
            eventHub.$on("player:stop:all", () => {
                if (this.isPlaying) {
                    this.pause();
                }
            });
        },

        methods: {
            play() {

                if (this.isPlaying) {
                    this.pause();
                    return true;
                }

                eventHub.$emit('player:stop:all');

                if (!this.audio) {
                    this.audio = new Audio(this.url);
                }

                if (this.audio) {
                    if (!this.isPlaying) {

                        this.audio.onended = (event) => {
                            this.isPlaying = false;
                        };

                        this.audio.play();
                        this.isPlaying = true;
                    }
                }
            },
            pause() {
                this.audio.pause();
                this.isPlaying = false;
            }
        },

        computed: {
            playPauseState() {
                if (this.isPlaying) {
                    return 'icon-controller-paus';
                } else {
                    return 'icon-controller-play';
                }
            },
            getToolTip() {
                let title = this.tooltipTitle;
                let content = this.tooltipContent;
                return {title: title, content: content};
            },
        },

        beforeDestroy() {
        }
    }
</script>

<style scoped>

</style>
