<template>
    <b-container class="p-3">

<!--        <div>
            <span class="help-block">Vorschau</span>
            <b-card no-body class="overflow-hidden" style="max-width: 540px;">
                <b-row no-gutters>
                    <b-col md="6">
                        <b-card-img :src="show.logo" :alt="show.title" class="rounded-0"></b-card-img>
                    </b-col>
                    <b-col md="6">
                        <b-card-body>
                            <b-card-title>
                                <b-input v-model="title" type="text">{{ title }}</b-input>
                            </b-card-title>
                            <b-card-text>
                                <span v-html="show.description" contenteditable="true"></span>
                            </b-card-text>

                            <audio-play-button
                                v-if="show.media.type === 'audio'"
                                size="md"
                                :tooltip-title="$t('shows.popover_audioplayer_tooltip_title')"
                                :tooltip-content="$t('shows.popover_audioplayer_tooltip_content')"
                                :url="show.media.intern"
                                :filename="show.media.name"></audio-play-button>

                            <video-player-button
                                v-if="show.media.intern && show.media.type === 'video'"
                                class="mt-2"
                                :url="show.media.intern"></video-player-button>

                        </b-card-body>
&lt;!&ndash;                        <b-card-footer>
                            <b-button :href="link" variant="primary">{{ goal }}</b-button>
                        </b-card-footer>&ndash;&gt;
                    </b-col>
                </b-row>
            </b-card>
        </div>-->

        <h3>
            {{$t('shows.text_header_sharing_options')}}
        </h3>

        <b-row>
           <b-col cols="12">
               <b-form-group
                   :label="$t('shows.label_share_title')"
               >
                   <b-input type="text" v-model="title" :value="show.title"></b-input>
               </b-form-group>
            </b-col>
        </b-row>

        <b-row>
           <b-col cols="12">
               <b-form-group
                   :label="$t('shows.label_share_description')"
               >
                   <b-textarea rows="5" v-model="description"></b-textarea>
               </b-form-group>
            </b-col>
        </b-row>

        <b-row>
            <b-col cols="12" lg="6">
                <label>{{$t('shows.label_share_url')}}</label>
                    <b-select
                        @change="link=sharingOpt"
                        v-model="sharingOpt"
                        :options="sharingOpts"
                        :disabled="isLoadingSharingOpts"
                        required
                    ></b-select>
                <b-form-text id="password-help-block">
                    {{ $t('shows.help_sharing_url') }}
                </b-form-text>
            </b-col>
            <b-col cols="12" lg="6" v-show="sharingOpt">
                <label>{{ $t('shows.label_link') }}</label>
                <b-input-group>
                    <b-input
                        required
                        v-model="link"
                        placeholder="Hier Link eingeben"
                        type="url"></b-input>
                    <b-input-group-append>
                        <a :href="link" class="btn btn-outline-secondary" target="_blank" rel="noopener noreferrer" v-b-popover.hover.top="$t('feeds.button_open_link')"><i class="icon-link"></i> {{ $t('shows.link_open_url') }}</a>
                    </b-input-group-append>
                </b-input-group>
            </b-col>
        </b-row>

        <b-row class="mt-5" v-show="sharingOpt">
                    <div class="d-flex justify-content-between">
                        <div><i class="icon-share mr-1"></i> Teilen</div>

                        <ul class="list-group list-group-horizontal list-group-flush list-group-shares">
                            <li class="list-group-item">
                                <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(link)" class="social-button" title="Bei Facebook teilen" @click.prevent="useShare">
                                    <span class="socicon-facebook"></span> Facebook</a>
                            </li>
                            <li class="list-group-item">
                                <a :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent(title) + '&amp;url=' + encodeURIComponent(link) + '&amp;via=podcasterDe&amp;hashtags=podcast,podcaster'" class="social-button" title="Auf Twitter teilen" @click.prevent="useShare"><span class="socicon-twitter"></span>
                                Twitter</a>
                            </li>
                            <li class="list-group-item">
                                <a :href="'https://www.linkedin.com/shareArticle?mini=true&amp;url=' + encodeURIComponent(link) + '&amp;title=' + encodeURIComponent(title) + '&amp;summary=' + encodeURIComponent(description)" class="social-button" @click.prevent="useShare"><span class="socicon-linkedin"></span> LinkedIn</a>
                            </li>
                            <li class="list-group-item">
                                <a target="_blank" :href="'https://wa.me/?text=' + encodeURIComponent(title) + ' ' + encodeURIComponent(link)" class="social-button " title="Mit Whatsapp teilen" @click.prevent="useShare"><span class="socicon-whatsapp"></span> WhatsApp</a>

                            </li>
                            <li class="list-group-item">
                                <a target="_blank" :href="'https://pinterest.com/pin/create/link/?url=' + encodeURIComponent(link)" class="social-button " title="Mit Pinterest teilen" @click.prevent="useShare"><span class="socicon-pinterest"></span> Pinterest</a>
                            </li>
                            <li class="list-group-item">
                                <a target="_top" :href="'mailto:?subject=' + encodeURIComponent(title) + '&amp;body=' + encodeURIComponent(description) + '%0d%0a%0d%0a' + encodeURIComponent(link)" class="social-button " title="Per Mail teilen"><span class="socicon-mail"></span> Mail</a>
                            </li>
                            <li class="list-group-item">
                                <a target="_blank" :href="'https://mail.google.com/mail/?view=cm&fs=1&amp;su=' + encodeURIComponent(title) + '&amp;body=' + encodeURIComponent(description) + '%0d%0a%0d%0a' + encodeURIComponent(link)" class="social-button " title="Per GMail teilen"><span class="socicon-google"></span> Gmail</a>
                            </li>
<!--                            <li class="list-group-item"><a target="_blank" :href="'http://pinterest.com/pin/create/link/?url=' + link" class="social-button " title="Mit Pinterest teilen" @click.prevent="useShare"><span class="socicon-instagram"></span></a></li>-->
                        </ul>
                    </div>
        </b-row>

    </b-container>
</template>

<script>
    function stripHtml(html)
    {
        let tmp = document.createElement("DIV");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
    }

    import AudioPlayButton from "../AudioPlayButton";
    import VideoPlayerButton from "../VideoPlayerButton";

    export default {
        name: "ShareShow",

        components: {
            AudioPlayButton,
            VideoPlayerButton
        },

        data() {
            return {
                sharingOpt: null,
                link: 'https://',
                goal: this.$t('shows.link_text_goal'),
                isLoadingSharingOpts: false,
                title: '',
                description: ''
            }
        },

        props: {
            show: {
                type: Object
            },
            feedId: {
                type: String
            },
            sharingOpts: {
                type: Array
            }
        },

        mounted() {
            this.title = this.show.title;
            let _description = stripHtml(this.show.description);
            this.description = _description.substring(0, 100);
        },

        computed: {
        },

        methods: {
            useShare(el) {
                let link = el.currentTarget.href;
                let popupSize = {
                    width: 780,
                    height: 550
                };

                let w=window,
                    d=document,
                    e=d.documentElement,
                    g=d.getElementsByTagName('body')[0],
                    x=w.innerWidth||e.clientWidth||g.clientWidth,
                    y=w.innerHeight||e.clientHeight||g.clientHeight;

                let verticalPos = Math.floor((x - popupSize.width) / 2),
                    horisontalPos = Math.floor((y - popupSize.height) / 2);

                let popup = window.open(link, 'social',
                    'width=' + popupSize.width + ',height=' + popupSize.height +
                    ',left=' + verticalPos + ',top=' + horisontalPos +
                    ',location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1');

                if (popup) {
                    popup.focus();
                }
                return false;
            }
        }
    }
</script>

<style scoped>

</style>
