<template>
    <b-row>
            <b-col cols="12" xl="4" lg="4" class="mb-3">
                <b-row class="mb-2">
                    <b-col cols="12">
                        <h3>{{ $t('player.header_configurations') }}</h3>
                    </b-col>
                </b-row>

                <b-row v-if="!canEmbed">
                    <b-col cols="12" class="">
                        <div class="text-center alert-warning p-4 mr-4">
                            <span class="text-" v-html="$t('player.text_hint_upgrade_package', {route: '/pakete'})"></span>
                        </div>
                    </b-col>
                </b-row>

                <b-row v-if="canEmbed">
                    <b-col cols="12" sm="3" md="3" lg="3" xl="3">
                        <b-button
                            class="mt-3"
                            size="lg"
                            variant="primary"
                            v-show="!selectedConfig && !model.player_type"
                            @click="model.player_type=1"
                            v-b-popover.hover.top="$t('player.text_configurator_popover_new_configuration')"
                            :disabled="!canEmbed"
                        >{{ $t('player.button_configurator_new_config') }}</b-button>
                        <i class="mt-4 ml-4 ml-lg-2 ml-xl-4 display-4 icon-cw"
                           v-b-popover.hover.top="$t('player.text_configurator_popover_reset_configurator')"
                           v-show="selectedConfig || model.player_type"
                           @click="resetConfiguration"></i>
                    </b-col>

                    <b-col cols="12" sm="9" md="9" lg="9" xl="9" class="mt-2 mt-md-0">
                        <label
                            for="configurations"
                            class="col-md-8 col-form-label">{{$t('player.label_configurator_configurations')}} <span class="text-muted">({{ numberConfigs }}/{{ amountPlayerConfigurations }})</span></label>
                        <div class="col-md-8">
                            <b-overlay :show="isLoadingConfigs" rounded="sm">
                                <b-select
                                    v-model="selectedConfig"
                                    @change="setConfig"
                                    id="configurations"
                                    :options="configList"
                                    :disabled="!canCreatePlayerConfigurations"
                                    v-if="configList">
                                </b-select>
                            </b-overlay>
                        </div>
                    </b-col>

                    <b-col cols="12" sm="12" md="8" lg="12" xl="12" class="mt-2 mt-md-0 mt-xl-3">
                        <b-button-group
                            class="right"
                            v-show="model.uuid">
                            <b-button
                                size="sm"
                                variant="outline-secondary"
                                @click="copyConfig"
                                :disabled="cannotSaveConfigs"
                                v-b-popover.hover.bottom="$t('player.popover_link_update_config')">{{ $t('player.button_configurator_copy_configuration') }}</b-button>
                            <b-button
                                size="sm"
                                variant="outline-danger"
                                @click="deleteConfig"
                                v-b-popover.hover.bottom="$t('player.popover_link_delete_config')">{{ $t('player.text_link_delete_config') }}</b-button>
                        </b-button-group>
                    </b-col>
                </b-row>

                <hr>

                <b-row v-if="model.player_type">
                    <b-col cols="12" v-if="canEmbed">
                        <b-form>
                            <b-row class="mb-3 form-group">
                                <b-col cols="12">
                                    <label for="player-configurable-type" class="col-md-4 col-form-label">{{$t('player.label_configurator_player_configurable_type')}}</label>
                                    <div class="col-md-8">
                                        <b-select
                                                v-model="model.player_configurable_type"
                                                :options="player_types"
                                                id="player-configurable-type"
                                                @change="loadConfigurableTypeOptions"></b-select>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mb-3 form-group"
                                   v-if="model.player_configurable_type === 'channel' || model.player_configurable_type === 'show'">
                                <b-col cols="12">
                                    <label for="player-type-channel" class="col-md-4 col-form-label">{{$t('player.label_configurator_player_type_channel')}}</label>
                                    <div class="col-md-8">
                                        <b-select
                                                v-model="model.player_configurable_id"
                                                :options="channels"
                                                id="player-type-channel"
                                                v-if="channels && model.player_configurable_type === 'channel'"></b-select>
                                        <b-select
                                                v-model="model.feed_id"
                                                :options="channels"
                                                id="player-type-channel"
                                                v-if="channels && model.player_configurable_type === 'show'"
                                                @change="getShows"></b-select>
                                        <div class="spinner-border" role="status" v-if="!channels">
                                            <span class="sr-only">{{$t('package.text_loading')}}</span>
                                        </div>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mb-3 form-group" v-if="model.feed_id && model.player_configurable_type === 'show'">
                                <b-col cols="12">
                                    <label for="player-type-show" class="col-md-4 col-form-label">{{$t('player.label_configurator_player_type_show')}}</label>
                                    <div class="col-md-8">
                                        <b-select
                                                v-model="model.player_configurable_id"
                                                :options="shows"
                                                id="player-type-show"
                                                v-if="shows"></b-select>
                                        <div class="spinner-border" role="status" v-if="!shows">
                                            <span class="sr-only">{{$t('package.text_loading')}}</span>
                                        </div>
                                    </div>
                                </b-col>
                            </b-row>

                            <div v-if="playerSource || adjustConfig">
                                <b-row>
                                    <b-col lg="8" xl="8" cols="12">
                                        <b-checkbox
                                            v-model="adjustConfig">
                                            {{ $t('player.label_adjust_config') }}
                                        <span v-if="!canCreatePlayerConfigurations" class="small">({{ $t('player.text_need_package_upgrade', {package: 'Profi'}) }})</span>
                                        <span v-else-if="cannotSaveConfigs" class="small">( {{ $t('player.text_need_extra_configs') }} <i class="icon icon-info-with-circle text-blue" v-b-popover.hover.click="$t('player.text_popover_need_extra_configs')"></i>)</span>
                                        </b-checkbox>
                                    </b-col>
                                </b-row>
                            </div>

                            <div v-if="adjustConfig">

                                <b-row class="mb-3 form-group">
                                    <b-col cols="12">
                                        <label for="delay-between-audio" class="col-md-6 col-form-label">{{$t('player.label_configurator_delay_between_audio')}}</label>
                                        <div class="col-md-8">
                                            <b-form-input
                                                id="delay-between-audio"
                                                v-model="model.delay_between_audio"
                                                type="range"
                                                min="0"
                                                max="300"
                                                step="50"></b-form-input>
                                            <div class="mt-2 text-muted text-small">
                                                {{$t('player.text_configurator_delay_between_audio_in_seconds')}}: {{ delayAudioInSeconds }}
                                            </div>
                                        </div>
                                    </b-col>
                                </b-row>

                                <b-row class="mb-3 form-group">
                                    <b-col cols="12">
                                        <label for="preload" class="col-md-4 col-form-label">{{$t('player.label_configurator_preload')}}</label>
                                        <div class="col-md-8">
                                            <b-select
                                                id="preload"
                                                v-model="model.preload"
                                                v-b-popover.hover.leftbottom="$t('player.text_configurator_popover_preload_option')"
                                                :options="preloadOptions">
                                            </b-select>
                                        </div>
                                    </b-col>
                                </b-row>

                                <b-row class="mb-3 form-group" v-if="this.model.player_configurable_type === 'channel'">
                                    <b-col cols="12">
                                        <label for="order" class="col-md-4 col-form-label">{{$t('player.label_configurator_order')}}</label>
                                        <div class="col-md-8">
                                            <b-select
                                                id="order"
                                                v-model="model.order"
                                                v-b-popover.hover.leftbottom="$t('player.text_configurator_popover_order_option')"
                                                :options="orderOptions">
                                            </b-select>
                                        </div>
                                    </b-col>
                                </b-row>

                                <b-row class="mb-3 form-group">
                                    <b-col cols="6" class="ml-2">
                                        <b-checkbox
                                            v-model="model.sharing"
                                            v-b-popover.hover.leftbottom="$t('player.text_configurator_popover_sharing_option')">{{ $t('player.label_enable_sharing_options') }}</b-checkbox>
                                    </b-col>
                                </b-row>

                                <b-row class="mb-3 form-group">
                                    <b-col cols="6" class="ml-2">
                                        <b-checkbox
                                            :disabled="this.model.player_type!==1"
                                            v-model="model.show_info"
                                            v-b-popover.hover.leftbottom="$t('player.text_configurator_popover_show_detail_option')">{{ $t('player.label_enable_show_detail_option') }}</b-checkbox>
                                        <small class="text-muted" v-show="this.model.player_type!==1" v-text="$t('player.hint_feature_not_available')"></small>
                                    </b-col>
                                </b-row>

                                <b-row class="mb-3 form-group" v-if="this.model.player_configurable_type === 'channel'">
                                    <b-col cols="6" class="ml-2">
                                        <b-checkbox
                                            :disabled="this.model.player_type!==1"
                                            v-model="model.show_playlist"
                                            v-b-popover.hover.leftbottom="$t('player.text_configurator_popover_show_playlist_option')">{{ $t('player.label_show_playlist') }}</b-checkbox>
                                        <small class="text-muted" v-show="this.model.player_type!==1" v-text="$t('player.hint_feature_not_available')"></small>
                                    </b-col>
                                </b-row>

                                <b-row>
                                    <b-col cols="12">
                                        {{ $t('player.header_configurator_colors') }}
                                    </b-col>
                                </b-row>

                                <b-row>

                                    <b-col cols="12" xl="6" lg="6">
                                        <b-row class="mb-3 form-group">
                                            <b-col cols="12">
                                                <label for="text-color" class="col-md-4 col-form-label">{{$t('player.label_configurator_text_color')}}</label>
                                                <div class="col-md-8">
                                                    <b-input v-model="model.text_color" id="text-color" type="color"
                                                             :placeholder="$t('player.placeholder_configurator_text_color')"
                                                             class="form-control"></b-input>
                                                </div>
                                            </b-col>
                                        </b-row>
                                    </b-col>

                                    <b-col cols="12" xl="6" lg="6">
                                        <b-row class="mb-3 form-group">
                                            <b-col cols="12">
                                                <label for="background-color" class="col-md-4 col-form-label">{{$t('player.label_configurator_background_color')}}</label>
                                                <div class="col-md-8">
                                                    <b-input v-model="model.background_color" id="background-color"
                                                             type="color"
                                                             :placeholder="$t('player.placeholder_configurator_background_color')"
                                                             class="form-control"></b-input>
                                                </div>
                                            </b-col>
                                        </b-row>
                                    </b-col>

                                </b-row>

                                <b-row>

<!--                                    <b-col cols="12" xl="6" lg="6">
                                        <b-row class="mb-3 form-group">
                                            <b-col cols="12">
                                                <label for="icon-fg-color" class="col-md-8 col-form-label">{{$t('player.label_configurator_icon_text_color')}}</label>
                                                <div class="col-md-8">
                                                    <b-input v-model="model.icon_fg_color" id="icon-fg-color" type="color"
                                                             :placeholder="$t('player.placeholder_configurator_icon_text_color')"
                                                             class="form-control"></b-input>
                                                </div>
                                            </b-col>
                                        </b-row>
                                    </b-col>-->

                                    <b-col cols="12" xl="6" lg="6">
                                        <b-row class="mb-3 form-group">
                                            <b-col cols="12">
                                                <label for="icon-color" class="col-md-8 col-form-label">{{$t('player.label_configurator_icon_color')}}</label>
                                                <div class="col-md-8">
                                                    <b-input v-model="model.icon_color" id="icon-color" type="color"
                                                             :placeholder="$t('player.placeholder_configurator_icon_color')"
                                                             class="form-control"></b-input>
                                                </div>
                                            </b-col>
                                        </b-row>
                                    </b-col>

                                </b-row>

                                <b-row>
                                    <b-col cols="12" xl="11" lg="11" class="mb-5">
                                        <b-checkbox
                                            id="use-custom-styles"
                                            v-model="use_custom_styles"
                                            name="use-custom-styles"
                                            :disabled="!canUseCustomPlayerStyles"
                                            value=true
                                            v-b-popover.hover.right="$t('player.text_popover_css_editor')"
                                            unchecked-value=false>
                                            {{ $t('player.label_css_editor') }}
                                            <span v-if="!canUseCustomPlayerStyles" class="small">({{ $t('player.text_need_package_upgrade', {package: 'Maxi'}) }})</span>
                                        </b-checkbox>
                                        <prism-editor
                                            class="custom-styles-editor h-300"
                                            v-model="model.custom_styles"
                                            :code="model.custom_styles"
                                            :highlight="highlighter"
                                            line-numbers
                                            v-show="use_custom_styles"></prism-editor>
                                    </b-col>
                                </b-row>
<!--
                                <b-row>

                                    <b-col cols="12" xl="6" lg="6">
                                        <b-row class="mb-3 form-group">
                                            <b-col cols="12">
                                                <label for="progressbar-color" class="col-md-8 col-form-label">{{$t('player.label_configurator_progressbar_color')}}</label>
                                                <div class="col-md-8">
                                                    <b-input v-model="model.progressbar_color"
                                                             id="progressbar-color"
                                                             type="color"
                                                             class="form-control"></b-input>
                                                </div>
                                            </b-col>
                                        </b-row>
                                    </b-col>

                                    <b-col cols="12" xl="6" lg="6">
                                        <b-row class="mb-3 form-group">
                                            <b-col cols="12">
                                                <label for="progressbar-buffer-color" class="col-md-8 col-form-label">{{$t('player.label_configurator_progressbar_buffer_color')}}</label>
                                                <div class="col-md-8">
                                                    <b-input v-model="model.progressbar_buffer_color"
                                                             id="progressbar-buffer-color"
                                                             type="color"
                                                             class="form-control"></b-input>
                                                </div>
                                            </b-col>
                                        </b-row>
                                    </b-col>

                                </b-row>-->


                                <!--                        <b-row class="mb-3">
                                                            <label for="initial-playback-speed">{{$t('player.label_configurator_initial_playback_speed')}}</label>
                                                            <b-form-input id="initial-playback-speed" v-model="model.initial_playback_speed" type="range" min="0.6" max="2" step="0.1"></b-form-input>
                                                            <div class="mt-2">{{$t('player.text_configurator_initial_playback_speed')}}: {{ model.initial_playback_speed }}</div>
                                                        </b-row>-->

                                <b-row class="mb-3 form-group">
                                    <b-col cols="12">
                                        <label for="title" class="col-md-8 col-form-label">{{$t('player.label_configurator_title')}}</label>
                                        <div class="col-md-8">
                                            <b-input v-model="model.title" required id="title"
                                                     :placeholder="$t('player.placeholder_configurator_title')"
                                                     class="form-control"></b-input>
                                        </div>
                                    </b-col>
                                </b-row>

                                <b-row class="mt-2">
                                    <b-col cols="12">
                                        <b-button
                                            variant="primary"
                                            @click="saveConfig"
                                            :disabled="cannotSaveConfigs || !canCreatePlayerConfigurations"
                                            v-show="!model.uuid">{{ $t('player.button_configurator_save_configuration') }}</b-button>
                                        <b-button-group v-show="model.uuid">
                                            <b-button
                                                variant="primary"
                                                @click="updateConfig"
                                                :disabled="cannotSaveConfigs || !canCreatePlayerConfigurations"
                                                v-b-popover.hover.top="$t('player.text_configurator_popover_button_update_configuration')">{{ $t('player.button_configurator_update_configuration') }}</b-button>
                                        </b-button-group>

                                    </b-col>
                                </b-row>
                            </div>
                        </b-form>
                    </b-col>
                </b-row>
            </b-col>

            <b-col cols="12" xl="4" lg="5" class="mb-3">
                    <b-row class="mb-3">
                        <b-col cols="12">
                            <h4>Vorschau</h4>
                        </b-col>
                    </b-row>

                    <b-row class="mb-3 form-group" v-if="playerSource">
                        <b-col cols="12">
                            <label for="players" class="col-md-4 col-form-label">{{$t('player.label_configurator_players')}}</label>
                            <div class="col-md-8">
                                <b-select
                                    v-model="model.player_type"
                                    :options="players"
                                    id="players"
                                ></b-select>
                            </div>
                        </b-col>
                    </b-row>

                    <b-row>
                        <b-col cols="12">
                            <iframe
                                class="player-frame"
                                :src="playerSource"
                                v-if="playerSource"
                            ></iframe>
                            <span v-if="!playerSource" v-text="$t('player.text_no_preview')"></span>
                        </b-col>
                    </b-row>
            </b-col>

            <b-col cols="11" xl="3" lg="2">

                <b-row class="mb-3">
                    <b-col cols="12">
                        <h4>{{ $t('player.header_embed_code') }}</h4>
                    </b-col>
                </b-row>

                <div v-if="playerSource">
                    <b-row>
                        <b-col cols="1" class="mr-3 mb-1">
                            <b-button variant="outline-primary"
                                      v-b-popover.hover.top="$t('player.button_copy_embed_code')"
                                      v-clipboard:copy="playerEmbed"
                                      v-clipboard:success="onCopy"
                                      v-clipboard:error="onCopyError"><i class="icon-clipboard"></i></b-button>
                        </b-col>
                        <b-col cols="8">
                            <h5>{{ $t('player.header_embed_code_iframe') }}</h5>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col cols="12">
                            <b-textarea
                                    readonly
                                    v-model="playerEmbed"
                                    rows="7"
                                    max-rows="10"
                                    @focus="$event.target.select()"
                                    v-b-popover.hover.top="$t('player.popover_embed_code')"
                            ></b-textarea>
                        </b-col>
                    </b-row>

                    <b-row class="pt-3 pb-3">
                        <b-col>
                            <b-link @click="toggleDimensionContainer = !toggleDimensionContainer">{{$t('player.header_change_embed_dimensions')}}</b-link>
                        </b-col>
                    </b-row>
                    <div class="card p-3" v-show="toggleDimensionContainer">
                        <b-row>
                            <b-col cols="12">
                                <b-row>
                                    <b-col cols="12" lg="4">
                                        <label for="width" class="mr-2">Breite</label>
                                    </b-col>
                                    <b-col cols="12" lg="8">
                                        <b-input-group>
                                            <template v-slot:append>
                                                <b-select
                                                    :options="unitOpts"
                                                    v-model="iWidthUnit"
                                                >
                                                </b-select>
                                            </template>
                                            <b-input v-model="iWidth" type="number"></b-input>
                                        </b-input-group>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col cols="6">
                                <b-row>
<!--                                    <b-col cols="12" lg="4">
                                        <label for="height" class="mr-2">Höhe</label>
                                    </b-col>
                                    <b-col cols="12" md="8">
                                        <b-input-group>
                                            <template v-slot:append>
                                                <b-select
                                                    :options="unitOpts"
                                                    v-model="iHeightUnit"
                                                >
                                                </b-select>
                                            </template>
                                            <b-input v-model="iHeight" type="number"></b-input>
                                        </b-input-group>
                                    </b-col>-->
                                </b-row>
                            </b-col>
                        </b-row>

                        <b-row>
                            <b-col cols="12" class="pt-3">
                                <b-row>
                                    <b-col cols="12" lg="4">
                                        <label for="min-width" class="mr-2">Minimale Breite</label>
                                    </b-col>
                                    <b-col cols="12" lg="8">
                                        <b-input-group>
                                            <template v-slot:append>
                                                <b-select
                                                    :options="unitOpts"
                                                    v-model="minWidthUnit"
                                                >
                                                </b-select>
                                            </template>
                                            <b-input v-model="minWidth" min="275" type="number"></b-input>
                                        </b-input-group>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col cols="12" class="pt-3">
                                <b-row>
                                    <b-col cols="12" lg="4">
                                        <label for="min-height" class="mr-2">Minimale Höhe</label>
                                    </b-col>
                                    <b-col cols="12" lg="8">
                                        <b-input-group>
                                            <template v-slot:append>
                                                <b-select
                                                    :options="unitOpts"
                                                    v-model="minHeightUnit"
                                                >
                                                </b-select>
                                            </template>
                                            <b-input v-model="minHeight" min="475" type="number"></b-input>
                                        </b-input-group>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>

                        <b-row>
                            <b-col cols="12" class="pt-3">
                                <b-row>
                                    <b-col cols="12" lg="4">
                                        <label for="max-width" class="mr-2">Maximale Breite</label>
                                    </b-col>
                                    <b-col cols="12" lg="8">
                                        <b-input-group>
                                            <template v-slot:append>
                                                <b-select
                                                    :options="unitOpts"
                                                    v-model="maxWidthUnit"
                                                >
                                                </b-select>
                                            </template>
                                            <b-input id="max-width" v-model="maxWidth" type="number"></b-input>
                                        </b-input-group>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col cols="12" class="pt-3">
                                <b-row>
                                    <b-col cols="12" lg="4">
                                        <label for="max-height" class="mr-2">Maximale Höhe</label>
                                    </b-col>
                                    <b-col cols="12" lg="8">
                                        <b-input-group>
                                            <template v-slot:append>
                                                <b-select
                                                    :options="unitOpts"
                                                    v-model="maxHeightUnit"
                                                >
                                                </b-select>
                                            </template>
                                            <b-input v-model="maxHeight" type="number"></b-input>
                                        </b-input-group>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>

                        <b-row>
                            <b-col cols="12" class="pt-3">
                                <small class="text-muted">{{ $t('player.hint_settings_iframe') }}</small>
                            </b-col>
                        </b-row>
                    </div>
                </div>

                <b-row v-if="!playerSource">
                    <b-col cols="12">
                        <span v-text="$t('player.text_no_embed')"></span>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
</template>

<script>

    function initialState(playerTypes, players, preloadOpts, orderOpts) {
        return {
            model: {
                player_type: 1,
                title: null,
                delay_between_audio: 0,
                initial_playback_speed: 1.0,
                hide_playlist_in_singlemode: true,
                show_playlist: false,
                show_info: false,
                enable_shuffle: false,
                player_configurable_id: null,
                player_configurable_type: null,
                feed_id: null,
                id: null,
                uuid: null,
                sharing: true,
                background_color: "#ffffff",
                text_color: '#000000',
                icon_color: '#d80056',
                icon_fg_color: '#ffffff',
                progressbar_color: '#d80056',
                progressbar_buffer_color: '#ffffff',
                styles: null,
                custom_styles: null,
                preload: 'metadata',
                order: 'date_desc'
            },
            player_types: playerTypes,
            players: players,
            channels: null,
            shows: null,
            selectedConfig: null,
            adjustConfig: false,
            configList: [],
            preloadOptions: preloadOpts,
            orderOptions: orderOpts,
            unitOpts: [
                {value: '%', text: '%'},
                {value: 'px', text: 'px', selected: true},
            ],
            errors: {},
            refresh_id: Math.round(new Date().getTime() / 1000),
            use_custom_styles: false,
            iWidth: 100,
            iWidthUnit: '%',
            iHeight: 475,
            iHeightUnit: 'px',
            minWidth: 275,
            minWidthUnit: 'px',
            minHeight: 525,
            minHeightUnit: 'px',
            maxWidth: 475,
            maxWidthUnit: 'px',
            maxHeight: 800,
            maxHeightUnit: 'px',
            toggleDimensionContainer: false,
            isLoadingConfigs: false
        }
    }

    import {eventHub} from '../../app';
    // import "prismjs";
    // import "prismjs/themes/prism.css";
    // import "prismjs/components/prism-css.js"
    // import "vue-prism-editor/dist/VuePrismEditor.css";
    // import PrismEditor from 'vue-prism-editor';

    import { PrismEditor } from 'vue-prism-editor';
    import "vue-prism-editor/dist/prismeditor.min.css";
    // import highlighting library (you can use any library you want just return html string)
    import { highlight, languages } from 'prismjs/components/prism-core';
    import 'prismjs/components/prism-css';
    import 'prismjs/components/prism-css-extras';
    import 'prismjs/themes/prism-tomorrow.css'; // import syntax highlighting styles

    export default {
        name: "PlayerConfigurator",

        components: {
            PrismEditor
        },

        props: {
            username: {
                type: String,
                required: true
            },
            url: {
                type: String,
                required: true
            },
            route: {
                type: String,
                required: false,
                default: ''
            },
            canEmbed: {
                type: [Boolean, String],
                required: false,
                default: false
            },
            canCreatePlayerConfigurations: {
                type: [Boolean, String],
                required: false,
                default: false
            },
            canUseCustomPlayerStyles: {
                type: [Boolean, String],
                required: false,
                default: false
            },
            amountPlayerConfigurations: {
                type: [Number, String],
                required: false,
                default: 0
            },
        },

        data() {
            return initialState(this.getPlayerTypes(), this.getPlayers(), this.getPreloadOpts(), this.getOrderOpts());
        },

        computed: {
            delayAudioInSeconds() {
                return this.model.delay_between_audio/100;
            },
            playerSource() {
                if (!this.model.player_configurable_type || !this.model.player_configurable_id && !this.model.uuid) return false;

                if (this.adjustConfig && !this.model.uuid) {
                    return false;
                }

                let url = this.url;

                if (this.model.uuid) {
                    url += '/' + this.getPlayerUrl() + '/?id=' + this.model.uuid;
                } else {
                    url += '/' + this.getPlayerUrl() + '/?id=' + this.model.player_configurable_type + '~' + this.username + '~' + (this.model.feed_id ? this.model.feed_id + '~' : '') + this.model.player_configurable_id;
                }
                url += '&v=' + this.refresh_id;

                if (this.use_custom_styles) {
                    url += '&c=1';
                }

                return url;
            },
            playerEmbed() {
                if (!this.playerSource) return false;

/*                let minHeight = 475;

                if (this.model.sharing) {
                    minHeight = 675;
                }*/

                let iframe = '<iframe style="';

                if (this.iWidth) {
                    iframe += 'width:' + this.iWidth + this.iWidthUnit + ';';
                }

                if (this.minWidth) {
                    iframe += 'min-width:' + this.minWidth + this.minWidthUnit + ';';
                }

                if (this.minHeight) {
                    iframe += 'min-height:' + this.minHeight + this.minHeightUnit + ';';
                }

                if (this.maxWidth) {
                    iframe += 'max-width:' + this.maxWidth + this.maxWidthUnit + ';';
                }

                if (this.maxHeight) {
                    iframe += 'max-height:' + this.maxHeight + this.maxHeightUnit + ';';
                }

                iframe += 'border:0;overflow:hidden" src="' + this.playerSource + '"></iframe>';

                return iframe;
            },
            numberConfigs() {
                if (!this.canCreatePlayerConfigurations) return 0;
                if (this.configList.length === 3) {
                    if (!this.configList[2].value) return 0;
                }
                return this.configList.length - 2;
            },
            cannotSaveConfigs() {
                return this.numberConfigs >= this.amountPlayerConfigurations /*this.canCreatePlayerConfigurations ||*/ ;
            }
        },

        methods: {
            resetConfiguration: function() {
                Object.assign(this.$data, initialState(this.getPlayerTypes(), this.getPlayers(), this.getPreloadOpts(), this.getOrderOpts()));
                this.loadConfigs();
            },
            onCopy: function (e) {
                window.scrollTo(0,275);
                eventHub.$emit('show-message:success', this.$t('player.copy_success_message', {message:  e.text}));
            },
            onCopyError: function (e) {
                window.scrollTo(0,275);
                eventHub.$emit('show-message:error', this.$t('player.copy_error_message'));
            },
            getPlayerUrl() {
                switch (this.model.player_type) {
                    case 1:
                        return 'simpleplayer';
                    case 2:
                        return 'podcasterplayer';
                    case 3:
                        return 'webplayer';
                    case 4:
                        return 'whiteplayer';
                }
            },
            getShows(feedId) {
                axios.get('/api/feed/' + feedId + '/shows?sortBy=lastUpdate&sortDesc=true')
                    .then((response) => {
                        let shows = response.data.data;
                        let _shows = [];
                        _shows.push({ value: null, text: this.$t('player.text_configurator_type_select_item'), disabled: true });
                        let draft = this.$t('player.text_configurator_draft');
                        let planned = this.$t('player.text_configurator_planned');
                        shows.forEach(function(show) {
                            if (!show.attributes.enclosure_url) return;
                            let showTitle = show.attributes.title;

                            if (show.attributes.is_published === 0) {
                                showTitle += ' (' + draft + ')';
                            } else if (show.attributes.is_published === 2) {
                                showTitle += ' (' + planned + ', ' + show.attributes.publish_date_formatted + ')';
                            } else {
                                showTitle += ' (' + show.attributes.publish_date_formatted + ')';
                            }
                            let _show = {
                                value: show.id, text: showTitle
                            };
                            _shows.push(_show);
                        });
                        this.shows = _shows;
                    })
                    .catch(error => {
                        this.showError(error);
                    });
            },
            resetToDefaults() {
                this.model.player_configurable_id = null;
                this.shows = null;
                this.model.feed_id = null;
                this.model.custom_styles = null;
                this.adjustConfig = false;
                this.use_custom_styles = false;
            },
            loadConfigurableTypeOptions() {
                this.resetToDefaults();
                this.getFeeds();
            },
            getFeeds() {
                axios.get('/api/feeds')
                    .then((response) => {
                        let feeds = response.data.data;
                        let _channels = [];
                        _channels.push({ value: null, text: this.$t('player.text_configurator_type_select_item'), disabled: true });
                        _channels.push({ value: null, text: this.$t('player.text_configurator_type_select_separator'), disabled: true });
                        if (feeds.length > 0) {
                            feeds.forEach(function (feed) {
                                let _feed = {
                                    value: feed.id, text: feed.attributes.title
                                };
                                _channels.push(_feed);
                            });
                        } else {
                            let _feed = {
                                value: null, text: this.$t('player.message_no_channel_entry'), disabled: true
                            };
                            _channels.push(_feed);
                        }
                        this.channels = _channels;
                    })
                    .catch(error => {
                        this.showError(error);
                    });
            },
            loadConfigs() {
                if (this.canCreatePlayerConfigurations) {
                    this.isLoadingConfigs = true;
                    axios.get('/player/config')
                        .then((response) => {
                            let configs = response.data;
                            let _configs = [];
                            _configs.push({
                                value: null,
                                text: this.$t('player.text_configurator_type_select_item'),
                                disabled: true
                            });
                            _configs.push({
                                value: null,
                                text: this.$t('player.text_configurator_type_select_separator'),
                                disabled: true
                            });

                            if (configs.length > 0) {
                                configs.forEach(function (config) {
                                    let _config = {
                                        value: config.id, text: config.title, data: config
                                    };
                                    _configs.push(_config);
                                });
                            } else {
                                let _config = {
                                    value: null, text: this.$t('player.message_no_config_entry'), disabled: true
                                };
                                _configs.push(_config);
                            }
                            this.configList = _configs;
                        })
                        .catch(error => {
                            this.showError(error);
                        }).then(() => {
                            // always executed
                            this.isLoadingConfigs = false;
                        });
                } else {
                    let _configs = [];
                    _configs.push({
                        value: null,
                        text: this.$t('player.text_need_package_upgrade', {package: 'Profi'}),
                        disabled: true
                    });
                    this.configList = _configs;
                }
            },
            saveConfig() {
                window.scrollTo(0,275);
                if (!this.model.feed_id) {
                    this.model.feed_id = this.model.player_configurable_id;
                }
                axios.post('/player/config', this.model)
                    .then(response => {
                        eventHub.$emit('show-message:success', response.data.success);
                        this.model.id = response.data.config.id;
                        this.model.uuid = response.data.config.uuid;
                        let _config = {value: this.model.id, text: this.model.title, data: this.model};
                        this.$data.configList.push(_config);
                        this.setConfig(this.model.id);
                    })
                    .catch(error => {
                        this.showError(error);
                    });
            },
            updateConfig() {
                window.scrollTo(0,275);
                if (!this.model.feed_id) {
                    this.model.feed_id = this.model.player_configurable_id;
                }
                axios.put('/player/config/' + this.model.id, this.model)
                    .then(response => {
                        eventHub.$emit('show-message:success', response.data.success);
                        let id = this.model.id;
                        let index = this.configList.findIndex(function(config) {
                            return config.value === id;
                        });
                        let _config = {
                            value: this.model.id, text: this.model.title, data: this.model
                        };
                        Object.assign(this.$data.configList[index], _config);
                        this.refresh_id = Math.round(new Date().getTime() / 1000);
                    })
                    .catch(error => {
                        this.showError(error);
                    });
            },
            copyConfig() {
                window.scrollTo(0,275);
                axios.post('/player/config/' + this.model.id + '/copy')
                    .then(response => {
                        eventHub.$emit('show-message:success', response.data.success);
                        var o = {};
                        o.data = response.data.copy;
                        o.text = response.data.copy.title;
                        o.value = response.data.copy.id;
                        this.configList.push(o);
                        this.setConfig(response.data.copy.id);
                        this.refresh_id = Math.round(new Date().getTime() / 1000);
                    })
                    .catch(error => {
                        this.showError(error);
                    });
            },
            deleteConfig() {
                window.scrollTo(0,275);
                if (confirm(this.$t('player.text_confirmation_delete_configuration', { name: this.model.title }))) {
                    axios.delete('/player/config/' + this.model.id)
                        .then(response => {
                            eventHub.$emit('show-message:success', response.data);
                            this.configList = this.configList.filter(e => e.value !== this.model.id);
                            this.resetConfiguration();
                        })
                        .catch(error => {
                            this.showError(error);
                        });
                }
            },
            setConfig(id) {
                let config = this.configList.find(function(config) {
                    return config.value === id;
                });
                this.model = config.data;
                this.adjustConfig = true;
                this.selectedConfig = id;

                if (this.model.custom_styles) {
                    this.use_custom_styles = true;
                } else {
                    this.use_custom_styles = false;
                }
            },
            getPlayers() {
                return [
                    {value: 1, text: this.$t('player.text_player_type_1')},
                    {value: 3, text: this.$t('player.text_player_type_3')},
                    {value: 4, text: this.$t('player.text_player_type_4')},
                    /*{value: 2, text: this.$t('player.text_player_type_2'), disabled: true},*/
                ];
            },
            getPlayerTypes() {
                return [
                    {value: null, text: this.$t('player.text_configurator_type_select_item'), disabled: true},
                    {value: null, text: this.$t('player.text_configurator_type_select_separator'), disabled: true},
                    {value: 'channel', text: this.$t('player.text_configurator_type_channel')},
                    {value: 'show', text: this.$t('player.text_configurator_type_show')},
/*                    {
                        value: 'direct',
                        text: this.$t('player.text_configurator_type_direct') + ' [noch nicht verfügbar]',
                        disabled: true
                    }*/
                ];
            },
            getPreloadOpts() {
                return [
                    {
                        value: 'none',
                        text: this.$t('player.text_option_preload_none')
                    },
                    {
                        value: 'metadata',
                        text: this.$t('player.text_option_preload_metadata')
                    },
                    {
                        value: 'audio',
                        text: this.$t('player.text_option_preload_audio')
                    }
                ];
            },
            getOrderOpts() {
                return [
                    {
                        value: 'date_desc',
                        text: this.$t('player.text_option_order_date_desc'),
                    },
                    {
                        value: 'date_asc',
                        text: this.$t('player.text_option_order_date_asc')
                    },
                    {
                        value: 'itunes_desc',
                        text: this.$t('player.text_option_order_itunes_desc')
                    },
                    {
                        value: 'itunes_asc',
                        text: this.$t('player.text_option_order_itunes_asc')
                    },
                ];
            },
            highlighter(code) {
                return highlight(code, languages.css); // languages.<insert language> to return html with markup
            },
        },

        mounted() {
            this.loadConfigs();
        },

        watch: {
            model: function(val) {
                this.getFeeds();
                if (val.player_configurable_type === 'show') {
                    this.getShows(val.feed_id);
                }
            },
            adjustConfig: function(val) {
                if (!val) {
                    this.use_custom_styles = false;
                }
            }
        },

        filters: {
            appendToUrl: (url, postfix) => {
                if (postfix) {
                    return `${url}&${postfix}`;
                }
                return `${url}`;
            }
        }
    }
</script>

<style scoped>
    .player-frame {
        border: 1px solid black;
        min-width: 275px;
        width: 100%;
        height: 700px;
        overflow: hidden;
        background-color: #f8f8fc;
        opacity: 0.8;
        background-size: 10px 10px;
        background-image: repeating-linear-gradient(45deg, #bbbccc 0, #bbbccc 1px, #f8f8fc 0, #f8f8fc 50%);
    }

   .custom-styles-editor {
       background: #606060;
       color: #fff;
       font-family: Fira code, Fira Mono, Consolas, Menlo, Courier, monospace;
       font-size: 14px;
       line-height: 1.5;
       padding: 5px;
   }

   .prism-editor__textarea:focus {
       outline: none;
   }

   .height-300 {
       height: 300px;
   }
</style>
