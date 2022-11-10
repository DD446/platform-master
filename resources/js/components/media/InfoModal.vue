<template>
    <b-modal
        ref="infoModal"
        :title="$t('mediamanager.file_details')"
        :ok-only="true"
        :ok-title="$t('mediamanager.close_modal')"
        @hide="resetModal"
        lazy>
        <b-card
            :title="item.name"
            tag="article"
            :img-src="imageSrc"
        >
            <b-card-body>

                <b-card-text v-if="audioSrc">
                    <audio-play-button
                        hide-tooltip
                        size="sm"
                        :url="this.item.intern"
                        :filename="this.item.name"
                        :duration="this.details.duration"></audio-play-button>
                </b-card-text>

                <b-card-text v-if="videoSrc">
                    <video-player-button :url="this.item.intern"></video-player-button>
                </b-card-text>

                <b-card-text v-if="textSrc">
                    <b-embed
                        type="embed"
                        :src="this.item.intern"
                        allowfullscreen
                    ></b-embed>
                </b-card-text>

                <b-card-text v-if="pdfSrc">
                    <object :data="this.item.intern" type="application/pdf" style="min-height:250px;width:100%">
                        <embed
                            :src="this.item.intern"
                            type="application/pdf" />
                            <!--<p>This browser does not support PDFs. Please download the PDF to view it: <a href="http://yoursite.com/the.pdf">Download PDF</a>.</p>-->
                    </object>

                </b-card-text>

                <b-card-text>
                    {{ $t('mediamanager.label_file_size') }}: {{ item.size }}
                </b-card-text>

                <b-card-text>
                    {{ $t('mediamanager.label_created_date_time') }}: {{ item['created_date'] }}, {{ item['created_time'] }}
                </b-card-text>

                <b-spinner v-show="!details"></b-spinner>

                <b-card-text v-show="details">
                    {{ $t('mediamanager.label_mime_type') }}: {{ details.mime }}
                </b-card-text>

                <b-card-text>
                    {{ details.info }}
                </b-card-text>

                <b-card-text v-if="imageSrc">
                    {{ $t('mediamanager.label_image_width') }}: {{ details.width }}px
                    <br>
                    {{ $t('mediamanager.label_image_height') }}: {{ details.height }}px
                    <br>
                    {{ $t('mediamanager.label_image_suitable_as_logo') }}: {{ details.isLogo ? $t('mediamanager.label_image_suitable_as_logo_yes') : $t('mediamanager.label_image_suitable_as_logo_no') }}
                </b-card-text>
            </b-card-body>
        </b-card>
    </b-modal>
</template>

<script>
    import {eventHub} from '../../app';
    import AudioPlayButton from "../AudioPlayButton";
    import VideoPlayerButton from "../VideoPlayerButton";

    function initialState() {
        return {
            item: '',
            details: false,
            audioSrc: null,
            imageSrc: null,
            videoSrc: null,
            textSrc: null,
            pdfSrc: null,
        }
    }

    export default {

        name: "info-modal",
        components: {
            AudioPlayButton,
            VideoPlayerButton
        },
        data() {
            return initialState();
        },

        computed: {
        },

        methods: {
            getInfo() {
                axios.get('/api/media/' + this.item.id)
                    .then((response) => {
                        this.details = response.data;
                        var mime = this.details.mime;

                        if (mime.startsWith('image')) {
                            this.imageSrc = this.item.intern;
                        }

                        if (mime.startsWith('audio')) {
                            this.audioSrc = this.item.intern;
                        }

                        if (mime.startsWith('video')) {
                            this.videoSrc = this.item.intern;
                        }

                        if (mime.startsWith('text/plain')) {
                            this.textSrc = this.item.intern;
                        }

                        if (mime.startsWith('application/pdf')) {
                            this.pdfSrc = this.item.intern;
                        }
                    })
                    .catch(error => {
                        this.details = {};
                        this.showError(error);
                    });
            },
            show() {
                this.$refs.infoModal.show();
                this.getInfo();
            },
            resetModal() {
/*                this.modalInfo.title = '';
                this.modalInfo.content = '';*/
                Object.assign(this.$data, initialState());
                eventHub.$emit('player:stop:all');
            },
        },

        created() {
        },

        mounted() {
            eventHub.$on("info-modal:show", item => {
                this.item = item;
                this.show();
            });
        },
    }
</script>
