<template>
    <b-modal
        ref="adTrackingModal"
        :title="$t('feeds.title_settings_ads_tracking')"
        :ok-title="$t('feeds.title_close')"
        ok-variant="outline-secondary"
        ok-only
        lazy
        size="lg">
        <alert-container></alert-container>

        <template #modal-title>
            <div class="d-flex">
                <div>
                    <span v-text="$t('feeds.title_settings_ads_tracking')"></span>
                </div>
                <div class="pl-1">
                    <a href="https://www.podcaster.de/faq/monetarisierung-10"
                       class="d-none d-sm-block"
                       onclick="window.open(this.href, 'help','width=1300,height=1250,top=15,left=15,scrollbars=yes');return false;"
                       v-b-popover.hover.top="$t('help.popover_faq')">
                        <img src="/images1/help/hilfe-faq.png" :alt="$t('help.alt_faq')" class="">
                    </a>
                </div>
            </div>
        </template>

        <b-card no-body>
            <div class="spinner-border m-5"
                 role="status"
                 v-if="!opts">
                <span class="sr-only">{{$t('package.text_loading')}}</span>
            </div>
            <b-card-body>

                <b-row class="mt-1 mb-3 ml-lg-1" v-if="!this.canUseAds">
                    <div class="text-center alert-warning m-1 p-4">
                        <b-card-text v-html="$t('feeds.text_hint_upgrade_package_for_ads', {route: '/pakete'})"></b-card-text>
                    </div>
                </b-row>

                <b-row>
                    <b-form-radio-group
                        id="btn-radios-2"
                        v-model="selected"
                        :options="getChoices()"
                        buttons
                        button-variant="outline-primary"
                        size="lg"
                        name="radio-btn-outline"
                    ></b-form-radio-group>
                </b-row>

                <div v-show="selected === 'audiotakes'">
                    <div v-show="isAvailable('audiotakes')">
                        <b-row>
                            <b-col cols="3" class="mt-4">
                                <a href="https://www.podcastpioniere.de" target="_blank">
                                    <img src="/images1/podcast-pioniere-250px.png" alt="Podcast Pioniere" class="img img-fluid mr-4">
                                </a>
                            </b-col>
                            <b-col cols="9">
                                <b-row class="mt-4">
                                    <b-col sm="12">
                                        <b-form-checkbox
                                            id="checkbox-audiotakes"
                                            v-model="opts.audiotakes"
                                            name="audiotakes"
                                            value="1"
                                            unchecked-value="0"
                                            v-b-popover.hover.bottomleft="$t('feeds.hover_audiotakes_save')"
                                            switch
                                        >
                                            <span v-show="!opts['audiotakes'] || opts['audiotakes'] == 0">{{ $t('feeds.label_integration_active') }}</span>
                                            <span v-show="opts['audiotakes'] == 1">{{ $t('feeds.label_integration_inactive') }}</span>
                                        </b-form-checkbox>
                                    </b-col>
                                </b-row>
                                <b-row class="mt-3" v-show="opts['audiotakes_id']">
                                    <b-col sm="12">
                                        <label>{{ $t('feeds.label_audiotakes_id', {title: feedId}) }}</label>
                                    </b-col>
                                    <b-col sm="12">
                                            <b-input
                                                v-model="opts['audiotakes_id']"
                                                readonly
                                            ></b-input>
                                    </b-col>
                                </b-row>
                                <b-row class="mt-3">
                                    <b-col sm="12">
                                        <b-button
                                            size="lg"
                                            variant="primary"
                                            :disabled="!opts['audiotakes'] || !opts['audiotakes_id']"
                                            @click="saveSettings('audiotakes', {state: opts['audiotakes'], id: opts['audiotakes_id']})"
                                            v-b-popover.hover.right="$t('feeds.hover_audiotakes_save')">{{ $t('feeds.text_button_save') }}
                                        </b-button>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>
                    </div>
                    <div v-show="!isAvailable('audiotakes')" class="text-info mt-4">
                        <b-row>
                            <b-col cols="3">
                                <a href="https://www.podcastpioniere.de" target="_blank">
                                    <img src="/images1/podcast-pioniere-250px.png" alt="Podcast Pioniere" class="img img-fluid mr-4">
                                </a>
                            </b-col>
                            <b-col cols="9">

                                <p>
                                    Verdiene jetzt Geld mit Deinem Podcast! Die Werbevermarktung über die <span class="font-weight-bolder">Podcast Pioniere</span> befindet sich ab sofort in der offenen Beta-Phase.
                                </p>
                                <p>
                                    <b-button href="/vermarktung" variant="success">Monetarisiere Deinen Podcast</b-button>
                                </p>
                            </b-col>
                        </b-row>
                    </div>
                </div>

                <div v-show="selected == 'chartable'">
                    <div v-show="isAvailable('chartable')">
                        <b-row class="mt-4">
                            <b-col sm="12">
                                <b-form-checkbox
                                    id="checkbox-chartable"
                                    v-model="opts.chartable"
                                    name="chartable"
                                    value="1"
                                    unchecked-value="0"
                                    v-b-popover.hover.bottomleft="$t('feeds.hover_chartable_save')"
                                    switch
                                >
                                    <span v-show="!opts['chartable'] || opts['chartable'] == 0">{{ $t('feeds.label_integration_active') }}</span>
                                    <span v-show="opts['chartable'] == 1">{{ $t('feeds.label_integration_inactive') }}</span>
                                </b-form-checkbox>
                            </b-col>
                        </b-row>
                        <b-row class="mt-3">
                            <b-col sm="12">
                                <label>{{ $t('feeds.label_chartable_id') }}</label>
                            </b-col>
                            <b-col sm="12">
                                <b-input-group prepend="https://chtbl.com/track/" append="/https://...">
                                    <b-input
                                        v-model="opts['chartable_id']"
                                        :placeholder="$t('feeds.placeholder_chartable_id')"
                                        autofocus
                                        required
                                    ></b-input>
                                </b-input-group>
                            </b-col>
                        </b-row>
                        <b-row class="mt-3">
                            <b-col sm="12">
                                <b-button
                                    size="lg"
                                    variant="primary"
                                    :disabled="!opts['chartable_id']"
                                    @click="saveSettings('chartable', {state: opts['chartable'], id: opts['chartable_id']})"
                                    v-b-popover.hover.right="$t('feeds.hover_chartable_save')">{{
                                    $t('feeds.text_button_save') }}
                                </b-button>
                            </b-col>
                        </b-row>
                    </div>
                    <div v-show="!isAvailable('chartable')" class="text-warning mt-4">
                        <span
                            v-text="$t('feeds.text_hint_activation_not_possible', {type: 'Chartable', service: getSelected(true)})"></span>
                    </div>
                </div>

                <div v-show="selected == 'podtrac'">
                    <div v-show="isAvailable('podtrac')">
                        <b-row class="mt-4">
                            <b-col sm="12">
                                <b-form-checkbox
                                    id="checkbox-podtrac"
                                    v-model="opts.podtrac"
                                    name="podtrac"
                                    value="1"
                                    unchecked-value="0"
                                    v-b-popover.hover.bottomleft="$t('feeds.hover_podtrac_save')"
                                    switch
                                >
                                    <span v-show="!opts['podtrac'] || opts['podtrac'] == 0">{{ $t('feeds.label_integration_active') }}</span>
                                    <span v-show="opts['podtrac'] == 1">{{ $t('feeds.label_integration_inactive') }}</span>
                                </b-form-checkbox>
                            </b-col>
                        </b-row>
                        <b-row class="mt-3">
                            <b-col sm="12">
                                <b-overlay :show="isLoading" rounded="sm">
                                    <b-button
                                        size="lg"
                                        variant="primary"
                                        @click="saveSettings('podtrac', opts['podtrac'])"
                                        v-b-popover.hover.right="$t('feeds.hover_podtrac_save')">
                                                {{ $t('feeds.text_button_save') }}
                                    </b-button>
                                </b-overlay>
                            </b-col>
                        </b-row>
                    </div>
                    <div v-show="!isAvailable('podtrac')" class="text-warning mt-4">
                        <span
                            v-text="$t('feeds.text_hint_activation_not_possible', {type: 'Podtrac', service: getSelected(true)})"></span>
                    </div>
                </div>

                <div v-show="selected == 'rms'" v-if="opts['rms']">
                    <b-row class="mt-4">
                        <b-col sm="12">
                            <b-form-checkbox
                                id="checkbox-rms"
                                v-model="opts.rms"
                                name="rms"
                                value="1"
                                unchecked-value="0"
                                v-b-popover.hover.bottomleft="$t('feeds.hover_rms_save')"
                                switch
                                v-show="isAvailable('rms')"
                            >
                                <span v-show="!opts['rms'] || opts['rms'] == 0">{{ $t('feeds.label_integration_active') }}</span>
                                <span v-show="opts['rms'] == 1">{{ $t('feeds.label_integration_inactive') }}</span>
                            </b-form-checkbox>
                            <b-card-text v-show="!isAvailable('rms')" class="text-warning">
                                <span v-text="$t('feeds.text_hint_activation_not_possible', {type: 'RMS', service: getSelected(true)})"></span>
                            </b-card-text>
                        </b-col>
                    </b-row>
                    <b-row class="mt-3">
                        <b-col sm="12">
                            <label>{{ $t('feeds.label_rms_id') }}</label>
                        </b-col>
                        <b-col sm="12">
                            <b-input-group :prepend="ownUrlHint">
                                <b-input
                                    v-model="opts['rms_id']"
                                    :placeholder="$t('feeds.placeholder_rms_id')"
                                    autofocus
                                    required
                                    v-b-popover.hover="$t('feeds.hover_rms_id')"
                                ></b-input>
                            </b-input-group>
                        </b-col>
                    </b-row>
                    <b-row class="mt-3">
                        <b-col sm="12">
                            <b-button
                                size="lg"
                                variant="primary"
                                :disabled="!opts['rms_id']"
                                @click="saveSettings('rms', {state: opts['rms'], id: opts['rms_id']})"
                                v-b-popover.hover.right="$t('feeds.hover_rms_save')">{{
                                $t('feeds.text_button_save') }}
                            </b-button>
                        </b-col>
                    </b-row>
                </div>

                <div v-show="selected == 'podcorn'">
                    <b-row class="mt-4">
                        <b-col sm="12">
                            <b-form-checkbox
                                id="checkbox-podcorn"
                                v-model="opts.podcorn"
                                name="podcorn"
                                value="1"
                                unchecked-value="0"
                                v-b-popover.hover.bottomleft="$t('feeds.hover_podcorn_save')"
                                switch
                                v-show="isAvailable('podcorn')"
                            >
                                <span v-show="!opts['podcorn'] || opts['podcorn'] == 0">{{ $t('feeds.label_integration_active') }}</span>
                                <span v-show="opts['podcorn'] == 1">{{ $t('feeds.label_integration_inactive') }}</span>
                            </b-form-checkbox>
                            <b-card-text v-show="!isAvailable('podcorn')" class="text-warning">
                                <span v-text="$t('feeds.text_hint_activation_not_possible', {type: 'Podcorn', service: getSelected(true)})"></span>
                            </b-card-text>
                        </b-col>
                    </b-row>
                    <b-row class="mt-3">
                        <b-col sm="12">
                            <b-overlay :show="isLoading" rounded="sm">
                                <b-button
                                    size="lg"
                                    variant="primary"
                                    @click="saveSettings('podcorn', opts['podcorn'])"
                                    v-b-popover.hover.right="$t('feeds.hover_podcorn_save')">{{
                                        $t('feeds.text_button_save')
                                    }}
                                </b-button>
                            </b-overlay>
                        </b-col>
                    </b-row>
                </div>

                <div v-show="selected == 'custom'">
                    <b-row class="mt-4">
                        <b-col sm="12">
                            <b-form-checkbox
                                id="checkbox-custom"
                                v-model="opts.custom"
                                name="custom"
                                value="1"
                                unchecked-value="0"
                                v-b-popover.hover.bottomleft="$t('feeds.hover_custom_save')"
                                switch
                                v-show="isAvailable('custom')"
                            >
                                <span v-show="!opts['custom'] || opts['custom'] == 0">{{ $t('feeds.label_integration_active') }}</span>
                                <span v-show="opts['custom'] == 1">{{ $t('feeds.label_integration_inactive') }}</span>
                            </b-form-checkbox>
                            <b-card-text v-show="!isAvailable('custom')" class="text-warning">
                                <span v-text="$t('feeds.text_hint_activation_not_possible', {type: 'custom', service: getSelected(true)})"></span>
                            </b-card-text>
                        </b-col>
                    </b-row>
                    <b-row class="mt-3">
                        <b-col sm="12">
                            <b-overlay :show="isLoading" rounded="sm">
                                <b-button
                                    size="lg"
                                    variant="primary"
                                    @click="saveSettings('custom', opts['custom'])"
                                    v-b-popover.hover.right="$t('feeds.hover_custom_save')">{{
                                        $t('feeds.text_button_save')
                                    }}
                                </b-button>
                            </b-overlay>
                        </b-col>
                    </b-row>
                </div>
            </b-card-body>
        </b-card>
    </b-modal>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "AdTrackingModal",

        data() {
            return {
                selected: 'audiotakes',
                opts: false,
                canUseAds: false,
                canUseAdsCustom: false,
                canUseAudiotakes: false,
                isLoading: false,
                ownUrlHint: ''
            }
        },

        props: {
            feedId: String,
        },

        methods: {
            show() {
                this.getSettings();
                this.$refs.adTrackingModal.show();
            },
            changeStateAds(value) {
                this.saveSettings('ads', value);
            },
            changeStateSelected(type, value) {
                this.saveSettings(type, value);
            },
            getSettings() {
                axios.get('/feed/' + this.feedId + '/settings')
                    .then((response) => {
                        this.opts = response.data;

                        //this.canUseTracking = this.opts.can.tracking;
                        this.canUseAds = this.opts.can.ads;

                        if (this.opts.chartable == 1 && !this.opts.chartable_id) {
                            this.opts.chartable = 0;
                        }

                        if (this.opts.rms == 1 && !this.opts.rms_id) {
                            this.opts.rms = 0;
                        }

                        //this.selected = this.getSelected();
                    })
                    .catch(error => {
                        this.showError(error);
                    });
            },
            saveSettings(type, value) {
                this.isLoading = true;
                axios.put('/feed/' + this.feedId + '/settings/' + type, {data: value})
                    .then((response) => {
                        //eventHub.$emit('show-message:success', response.data.message);
                        this.showMessage(response);
                        this.getSettings();
                    })
                    .catch(error => {
                        this.opts[type] = 0;
                        this.showError(error);
                    })
                    .then(() => {
                        this.isLoading = false;
                    });
            },
            getSelected(formatted) {
                formatted = formatted || false;
                if (this.opts.audiotakes == 1) {
                    if (formatted) return 'Podcast Pioniere';
                    return 'audiotakes';
                }
                if (this.opts.chartable == 1) {
                    if (formatted) return 'Chartable';
                    return 'chartable';
                }
                if (this.opts.podtrac == 1) {
                    if (formatted) return 'Podtrac';
                    return 'podtrac';
                }
                if (this.opts.rms == 1) {
                    if (formatted) return 'RMS';
                    return 'rms';
                }
                if (this.opts.podcorn == 1) {
                    if (formatted) return 'Podcorn';
                    return 'podcorn';
                }
                return null;
            },
            getChoices() {
                return [
                    { text: 'Podcast Pioniere', value: 'audiotakes' },
                    { text: 'Chartable', value: 'chartable', disabled: !this.canUseAds },
                    { text: 'Podtrac', value: 'podtrac', disabled: !this.canUseAds },
                    /*{ text: 'RMS', value: 'rms', disabled: !this.canUseAds },*/
                    { text: 'Podcorn', value: 'podcorn', disabled: !this.canUseAds },
                    { text: 'Individuell', value: 'custom', disabled: !this.canUseAdsCustom }
                ];
            },
            isAvailable(type) {

                var selected = this.getSelected();

                if (type === 'audiotakes') {
                    return this.opts.audiotakes_id || this.canUseAudiotakes;
                }

                if (!selected) return true;

                if (type != selected) return false;

                return true;
            },
            processing(service) {
                switch (service) {
                    case 'audiotakes':
                        return true;
                    case 'chartable':
                        return true;
                    case 'podtrac':
                        return true;
                    case 'podcorn':
                        return true;
                    case 'rms':
                        return true;
                }
            },
            signAudiotakesContract() {
                this.isLoading = true;
            }
        },

        computed: {
        },

        mounted() {
            eventHub.$on("ad-tracking-modal:show:" + this.feedId, () => {
                this.show();
            });
            this.ownUrlHint = 'https://rmsi-podcast.de/' + this.feedId + '/media/...?awCollectionId=';
        },

        created() {
        }
    }
</script>

<style scoped>
</style>
