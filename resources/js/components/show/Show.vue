<template>
    <b-media
        tag="li"
        class="mb-3"
        @mouseover="showActions"
        @mouseout="hideActions">
<!--        v-b-popover.left.hover.html="{title: show.attributes.itunes.subtitle, content: show.attributes.description}"-->
        <template v-slot:aside>
            <div v-show="!isSmallScreen">
                <b-img-lazy
                    left
                    width="125px"
                    height="125px"
                    class="mr-3"
                    style="border: 1px solid #dee2e6"
                    :alt="$t('shows.image_placeholder')"
                    :src="show.attributes.logo"
                    v-if="show.attributes.logo"></b-img-lazy>
                <!--        <show-logo-picker
                                    show-logo-upload-url="'/upload/show/logo'"
                                    v-if="!show.attributes.logo"></show-logo-picker>-->
                <div class="mr-3" style="position: relative;width: 125px" v-if="!show.attributes.logo">
                    <b-link :href="'/podcasts#/podcast/' + feedId + '/episode/' + show.attributes.guid">
                        <b-img
                            thumbnail
                            fluid
                            class="mr-3"
                            blank
                            blank-color="#fff"
                            width="125px"
                            height="125px"
                            style="position: absolute;top: 0;left: 0"
                            :alt="$t('shows.image_missing_placeholder')"
                        ></b-img>
                        <b-button
                            variant="warning"
                            style="position: absolute;top: 35px;left: 10px"
                            >Kein Bild</b-button>
<!--                        v-b-popover.hover.top="$t('shows.text_popover_set_show_logo')"-->
                    </b-link>
                </div>
            </div>
        </template>

        <b-media-body class="ml-1">
            <h5 class="mt-0 mb-1">
                <editable
                    :placeholder="$t('shows.text_editable_placeholder_title')"
                    :title="$t('shows.text_editable_header_show_title')"
                    :content="show.attributes.title"
                    :feed="feedId"
                    :guid="show.attributes.guid"
                    type="showTitle"></editable>
            </h5>
            <b-nav>
                <b-nav-item disabled>
                    {{ publishState }} <span v-show="show.attributes.is_published !== '0'">{{ show.attributes.publish_date_formatted }}</span>
                </b-nav-item>
            </b-nav>
    <!--        <b-row>
                <b-col xl="2" lg="3" md="12">
                    <b-form-checkbox
                        v-model="isDraft"
                        @change="changeState"
                        button-variant="secondary"> {{ $t('shows.text_label_draft') }}
                    </b-form-checkbox>
                </b-col>
                <b-col xl="6" lg="6" md="12">
                    <span v-show="show.attributes.is_published !== '0'">{{ publishState }}{{ show.attributes.publish_date_formatted }}</span>
                </b-col>
    &lt;!&ndash;                    <b-col xl="4" lg="4">
                    <b-form-datepicker
                        v-model="publishDate"
                        today-button
                        start-weekday="1"
                        value-as-date
                        locale="de"></b-form-datepicker>
                </b-col>
                <b-col xl="4" lg="4">
                    <b-form-timepicker
                        v-model="publishDate"
                        locale="de"></b-form-timepicker>
                </b-col>&ndash;&gt;
            </b-row>-->
            <b-nav>
                <b-nav-item
                        v-if="!show.attributes.enclosure_url">
                        <b-button
                                :to="'/podcast/' + feedId + '/episode/' + show.attributes.guid"
                                variant="warning"
                                size="sm"
                                v-b-popover.hover.top.blur="{title: $t('shows.title_enclosure_is_missing'), content: $t('shows.content_enclosure_is_missing')}">{{ $t('shows.enclosure_is_missing') }}</b-button>
                </b-nav-item>

                <b-nav-item v-if="isAudio && !isSmallScreen">
                    <audio-play-button
                        :url="this.show.attributes.enclosure_url"
                        :filename="this.show.attributes.file"
                        :duration="this.show.attributes.duration_formatted"></audio-play-button>
                </b-nav-item>

                <b-nav-item v-if="isVideo && !isSmallScreen">
                    <video-player-button
                        :url="this.show.attributes.enclosure_url"></video-player-button>
                </b-nav-item>

                <b-media
                    vertical-align="center"
                    v-show="displayActions">
                    <template v-slot:aside>
                        <b-button-group class="show-actions" :vertical="isSmallScreen">
                            <b-button
                                variant="outline-secondary"
                                :to="'/podcast/' + feedId + '/episode/' + show.attributes.guid"
                            ><i class="icon-pencil" v-show="!isSmallScreen"></i> {{ $t('shows.edit_show') }}</b-button>
<!--                            <b-button
                                variant="outline-secondary"
                                :href="'/podcast/' + feedId + '/episoden/#edit/' + show.attributes.guid"
                            >{{ $t('shows.edit_show') }} (alt)</b-button>-->
                            <b-button variant="outline-secondary" @click.prevent="confirmCopy"><i class="icon-copy" v-show="!isSmallScreen"></i> {{ $t('shows.copy_show') }}</b-button>
                            <b-button
                                :disabled="show.attributes.is_published==0 || show.attributes.is_published==2"
                                variant="outline-secondary"
                                :href="'/beta/podcast/' + feedId + '/episode/' + show.attributes.guid + '/share'"
                            ><i class="icon-share"></i> {{ $t('shows.text_label_action_share') }}</b-button>
                            <b-button @click.prevent="confirmDelete" variant="outline-danger"><i class="icon-trash" v-show="!isSmallScreen"></i> {{ $t('shows.delete_show') }}</b-button>
                        </b-button-group>
                    </template>
                </b-media>
            </b-nav>
        </b-media-body>
    </b-media>
</template>

<script>
    /*import ShowLogoPicker from "./ShowLogoPicker";*/
    import {eventHub} from '../../app';
    import Editable from "../Editable";
    import AudioPlayButton from "../AudioPlayButton";
    import VideoPlayerButton from "../VideoPlayerButton";

    export default {
        name: "Show",

        components: {
            /*ShowLogoPicker*/
            Editable,
            AudioPlayButton,
            VideoPlayerButton,
        },

        data() {
            return {
                audio: null,
                isPlaying: false,
                displayActions: false,
                isSmallScreen: false,
                //isDraft: (this.show.attributes.is_published == '0'),
                //publishDate: (new Date(this.show.attributes.publish_date * 1000)).toString()
            }
        },

        props: {
            feedId: String,
            show: Object,
        },

        created() {
            this.displayActions = this.isMobile();
            window.addEventListener('resize', this.mq);
            this.mq();
        },

        methods: {
            changeState(e) {
                if (!e) {
                    this.show.attributes.is_published = "0";
                } else if (this.show.attributes.publish_date > Date.now()/1000) {
                    this.show.attributes.is_published = "2";
                } else {
                    this.show.attributes.is_published = "1";
                }
            },
            play(url) {
                // TODO: Emit to stop all other running players

                if (!this.audio) {
                    this.audio = new Audio(url);
                }

                if (this.audio) {
                    if (!this.isPlaying) {
                        this.audio.play();
                        this.isPlaying = true;
                    } else {
                        this.audio.pause();
                        this.isPlaying = false;
                    }
                }
            },
            showActions() {
                this.displayActions = true;
            },
            hideActions() {
                this.displayActions = false;
            },
            mq () {
                this.isSmallScreen = window.matchMedia('(max-width: 400px)').matches;
            },
            confirmCopy() {
                if(confirm(this.$t('shows.confirm_copy_show', {title: this.show.attributes.title}))) {
                    window.scrollTo(0,275);
                    axios.post( '/api/feed/' + this.feedId + '/show/' + this.show.id + '/copy')
                        .then((response) => {
                            eventHub.$emit('show-message:success', response.data);
                            eventHub.$emit('update:shows:' + this.feedId);
                        })
                        .catch(error => {
                            this.showError(error);
                        });
                }
            },
            confirmDelete() {
                if(confirm(this.$t('shows.confirm_delete_show', {title: this.show.attributes.title}))) {
                    window.scrollTo(0,275);
                    axios.delete(  '/api/feed/' + this.feedId + '/show/' + this.show.id)
                        .then((response) => {
                            eventHub.$emit('show-message:success', response.data);
                            eventHub.$emit('update:shows:' + this.feedId);
                        })
                        .catch(error => {
                            this.showError(error);
                        });
                }
            },
            getLink(type, id) {
                switch (type) {
                }
            }
        },

        computed: {
            publishState() {
                let state;

                switch (this.show.attributes.is_published) {
                    case 2:
                    case "2":
                        state = this.$t('shows.status_is_scheduled');
                        break;
                    case 1:
                    case "1":
                    case -1:
                    case "-1":
                        state = this.$t('shows.status_is_published');
                        break;
                    default:
                        state = this.$t('shows.status_is_draft');
                }

                return state;
            },
            playPauseState() {
                if (this.isPlaying) {
                    return 'icon-controller-paus';
                } else {
                    return 'icon-controller-play';
                }
            },
            isAudio() {
                return this.show.attributes.type === 'audio';
            },
            isVideo() {
                return this.show.attributes.type === 'video';
            }
        },

        filters: {
            truncate: function (text, length, suffix) {
                if (text.length > length) {
                    return text.substring(0, length) + suffix;
                } else {
                    return text;
                }
            },
        },

        beforeDestroy() {
            window.removeEventListener('resize', this.mq)
        }
    }
</script>

<style scoped>
    .collapsed > .when-opened,
    :not(.collapsed) > .when-closed {
        display: none;
    }
</style>
