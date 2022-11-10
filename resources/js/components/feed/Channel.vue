<template>
    <div :id="'podcast-' + feedId" class="mb-5">

        <b-overlay :show="isLoadingChannel" rounded="sm">
            <b-card no-body v-if="feed" style="max-width: 1500px;padding-right: 15px">
                <b-row no-gutters v-if="feed.type==='feed'">
                    <b-col sm="12" v-if="true">
                        <b-nav align="right">
                            <b-nav-item-dropdown
                                :id="'settings-dropdown-' + feed.id"
                                no-caret
                                no-flip
                                v-b-popover.hover.righttop="$t('feeds.hover_feed_settings')"
                            >
                                <template slot="button-content">
                                    <i class="icon-cog"> Einstellungen</i>
                                </template>
                                <b-dropdown-item
                                    :to="'/podcast/' + feed.id + '/details'"
                                    v-b-popover.hover.top="$t('feeds.title_link_settings')"
                                >{{ $t('feeds.link_details_channel') }}</b-dropdown-item>
                                <b-dropdown-item
                                    @click="adTrackingLink"
                                    v-b-popover.hover.righttop="$t('feeds.hover_settings_ads_tracking')"
                                >{{ $t('feeds.nav_settings_ads_tracking') }}</b-dropdown-item>
                                <b-dropdown-item
                                    @click="socialMediaLink"
                                    v-b-popover.hover.righttop="$t('feeds.hover_settings_social_media')"
                                >{{ $t('feeds.nav_settings_social_media') }}</b-dropdown-item>
                                <b-dropdown-item
                                    @click="defaultSettingsLink"
                                    v-b-popover.hover.righttop="$t('feeds.hover_settings_defaults')"
                                >{{ $t('feeds.nav_settings_defaults') }}</b-dropdown-item>
                                <b-dropdown-item
                                    :href="getLink('auphonicApproval')" v-if="!hasAuphonic">{{ $t('feeds.link_add_auphonic_service') }}</b-dropdown-item>
                                <b-dropdown-divider></b-dropdown-divider>
                                <b-dropdown-item
                                    @click="privacyLink"
                                    v-b-popover.hover.righttop="$t('feeds.hover_settings_privacy')"
                                >{{ $t('feeds.nav_settings_privacy') }}</b-dropdown-item>
                                <b-dropdown-item
                                    v-b-popover.hover.righttop="$t('feeds.hover_change_url')"
                                    @click.stop="editLink"
                                >{{ $t('feeds.nav_change_url') }}</b-dropdown-item>
                                <b-dropdown-item
                                    v-b-popover.hover.righttop="$t('feeds.hover_redirect_feed_url')"
                                    @click.stop="redirectLink">{{ $t('feeds.link_create_redirect') }}</b-dropdown-item>
                                <b-dropdown-divider></b-dropdown-divider>
                                <b-dropdown-item
                                    variant="danger"
                                    v-b-popover.hover.righttop="$t('feeds.hover_delete_feed')"
                                    @click="removeChannel"><i class="icon-trash mr-1"></i>{{ $t('feeds.link_delete_channel') }}</b-dropdown-item>
                            </b-nav-item-dropdown>
                        </b-nav>
                    </b-col>
                    <b-col lg="2" md="4" sm="12">
                        <div v-if="feed.links['logo']" class="m-3">
                            <div style="position:relative">
                                <div style="position:absolute;right:1em;top:1em;">
                                    <b-button
                                            variant="warning"
                                            @click="removeLogo"
                                            v-b-popover.hover.top="$t('feeds.title_remove_logo')">x</b-button>
                                </div>
                                <b-card-img
                                        :src="feed.links['logo']"
                                        class="rounded-0"></b-card-img>
                            </div>
                            <b-card-title class="mt-3">
                                {{ $t('feeds.title_logo') }}
                                <b-badge
                                        :variant="stateLogo"
                                        :id="`state-logo-` + item">{{ stateLogoTitle }}</b-badge>
                                <b-popover
                                        :target="`state-logo-` + item"
                                        triggers="hover focus click">
                                    <template slot="title">{{ $t('feeds.title_status_logo_check') }}</template>
                                    <b-list-group>
                                        <b-list-group-item v-for="(item, key) in stateLogoText" :key="key">
                                            <span v-html="item"></span>
                                        </b-list-group-item>
                                    </b-list-group>
                                </b-popover>
                                <b-button variant="outline-primary"
                                          :title="$t('feeds.title_chosen_logo')"
                                          v-b-popover.hover.top="feed.attributes.logo"
                                          @click.stop="openLink(feed.links['logo'])"><i class="icon-link"></i></b-button>
                                <b-button variant="outline-primary"
                                          :title="$t('shop.title_logo')"
                                          v-b-popover.hover.top="$t('shop.hover_logo')"
                                          @click.stop="openLink('/kontakt?type=commercial&comment=Ich möchte ein professionelles Cover für meinen Podcast erstellen lassen. Bitte nehmen Sie Kontakt auf.', 'shop', 'width=500,height=750,left=100,top=100,toolbar=0,menubar=0,location=0')"><i class="icon-shopping-cart"></i></b-button>
                            </b-card-title>
                        </div>
                        <div v-if="!feed.links['logo']" class="m-2">
                            <fileuploader
                                    maxfiles="1"
                                    maxFilesize="1.1"
                                    thumbnailWidth="225"
                                    :url="logoUploadUrl"
                                    acceptedFiles=".png,.jpg"
                                    :uploadText="$t('feeds.upload_hint_logo')"
                                    :uploadTextExtra="$t('feeds.upload_hint_logo_extra')"
                                    :feed="feed.id"
                                ></fileuploader>

                            <div class="text-center text-nowrap">
                                ---------------------------- {{ $t('or') }} ----------------------------
                            </div>

                            <div class="my-1">
                                <logo-selector :feed="feed.id" :type="'logo'"></logo-selector>
                            </div>

                            <div class="my-1">
                                <b-button variant="outline-primary"
                                          block
                                          :title="$t('shop.title_logo')"
                                          v-b-popover.hover.top="$t('shop.hover_logo')"
                                          @click.stop="openLink('/kontakt?type=commercial&comment=Ich möchte ein professionelles Cover für meinen Podcast erstellen lassen. Bitte nehmen Sie Kontakt auf.', 'shop', 'width=500,height=750,left=100,top=100,toolbar=0,menubar=0,location=0')"><i class="icon-shopping-cart"></i> Cover-Erstellung</b-button>
                            </div>

                            <div class="alert alert-warning" role="alert" style="max-height: 350px; overflow: auto">
                                <h4 class="mb-1">{{ $t('feeds.warning_no_logo' )}}</h4>
                                <p>{{ $t('feeds.warning_no_logo_text')}} </p>
                                <ul class="list-group" v-for="value in restrictions">
                                    <li class="list-group-item list-group-item-light">{{ $t('feeds.hint_logo_requirement_' + value) }}</li>
                                </ul>
                            </div>
                        </div>
                    </b-col>
                    <b-col lg="10" md="8" sm="12">
                        <b-card-body>
                            <b-card-title>
                                <editable
                                    :placeholder="$t('feeds.text_editable_placeholder_title')"
                                    :title="$t('feeds.title_rss_title')"
                                    :content="feed.attributes.title"
                                    :feed="feed.id" type="title"></editable>
                            </b-card-title>
                            <b-card-sub-title class="mb-2">
                                <editable
                                    :placeholder="$t('feeds.text_editable_placeholder_subtitle')"
                                    :title="$t('feeds.title_itunes_title')"
                                    :content="feed.attributes.subtitle"
                                    :feed="feed.id"
                                    type="subtitle"></editable></b-card-sub-title>
                            <b-card-text style="max-height: 250px;overflow: auto">
                                <editable
                                    :placeholder="$t('feeds.text_editable_placeholder_description')"
                                    :title="$t('feeds.title_rss_description')"
                                    :content="feed.attributes.description"
                                    :feed="feed.id"
                                    type="description"></editable>
                            </b-card-text>
                        </b-card-body>

                        <b-list-group>
                            <b-list-group-item>
                                <b-card-title>
                                    {{ $t('feeds.title_channel') }}
                                </b-card-title>
                                <b-form-group>
                                    <b-input-group class="mt-4">
                                        <b-form-input
                                                type="url"
                                                :value="feed.links['rss']"
                                                v-b-popover.hover.top="$t('feeds.title_link_podcast_feed')"
                                                readonly></b-form-input>
                                        <b-button variant="outline-primary"
                                                  v-b-popover.hover.top="$t('feeds.button_copy_link')"
                                                  v-clipboard:copy="feed.links['rss']"
                                                  v-clipboard:success="onCopy"
                                                  v-clipboard:error="onError"><i class="icon-clipboard"></i></b-button>
                                        <b-button variant="outline-primary"
                                                  v-b-popover.hover.top="$t('feeds.button_open_link')"
                                                  @click.stop="openLink(feed.links['rss'])"><i class="icon-link"></i></b-button>
                                    </b-input-group>
                                    <small id="feedHelp" class="form-text text-muted">{{ $t('feeds.help_feed_url') }}</small>
                                </b-form-group>
                                <b-card-body>
                                    <b-list-group horizontal="md">
<!--                                        <b-list-group-item>
                                            <b-link :to="'/podcast/' + feed.id + '/details'" v-b-popover.hover.top="$t('feeds.title_link_settings')">{{ $t('feeds.link_details_channel') }}</b-link>
                                        </b-list-group-item>-->
                                        <b-list-group-item>
                                            <b-link :to="'/podcast/' + feed.id + '/status'" v-b-popover.hover.top="$t('feeds.title_link_state_check')">{{ $t('feeds.link_status') }}
                                            </b-link>
                                            <b-badge :variant="state" :id="`state-feed-` + item">{{ stateTitle }}</b-badge>
                                            <b-popover :target="`state-feed-` + item" triggers="hover focus click">
                                                <template slot="title">{{ $t('feeds.title_status_check') }}</template>
                                                <div v-html="stateTextTitle"></div>
                                                <b-list-group>
                                                    <b-list-group-item v-for="(item, key) in stateText" :key="key">
                                                        <span v-html="item"></span>
                                                    </b-list-group-item>
                                                </b-list-group>
                                            </b-popover>
                                        </b-list-group-item>
                                        <b-list-group-item>
                                            <b-link
                                                :to="'/podcast/' + feedId + '/promotion'"
                                                v-b-popover.hover.top="$t('feeds.title_link_submit')">{{ $t('feeds.link_submit_tool') }}</b-link>
                                        </b-list-group-item>
                                        <b-list-group-item>
                                            <b-link
                                                :to="'/podcast/' + feedId + '/player'"
                                                v-b-popover.hover.top="$t('feeds.title_link_embed')"
                                            >{{$t('feeds.link_embed')}}</b-link>
                                        </b-list-group-item>
<!--                                        <b-list-group-item>
                                            <b-nav>
                                                <b-nav-item-dropdown
                                                        id="podcast-nav-dropdown"
                                                        :text="$t('feeds.nav_actions')"
                                                        toggle-class="p-0"
                                                >
                                                    <b-dropdown-item :to="'/podcast/' + feedId + '/episode'">{{ $t('feeds.link_create_show') }}</b-dropdown-item>
                                                </b-nav-item-dropdown>
                                            </b-nav>
                                        </b-list-group-item>-->
                                    </b-list-group>
                                </b-card-body>
                            </b-list-group-item>
                            <b-list-group-item v-if="!feed.is_importing">
                                <b-card-title>
                                    <b-row>
                                        <b-col xl="3" lg="3" md="12" sm="12">
                                            {{ $t('feeds.title_shows') }}
                                            <b-button
                                                size="sm"
                                                :href="getLink('shows', feed.id)"
                                                      v-b-popover.hover.top="$t('feeds.title_link_shows')">
                                                {{ feed.shows_count }}
                                            </b-button>
                                        </b-col>
                                        <b-col xl="2" lg="2" md="5" sm="5" offset-lg="3" class="mt-sm-3">
                                            <b-select
                                                @change="showsFilter=statusSelected"
                                                v-model="statusSelected"
                                                :options="statusOpts"></b-select>
                                        </b-col>
                                        <b-col xl="4" lg="6" md="7" sm="7" class="mt-sm-3" v-show="shows || showsFilter">
                                            <b-input-group>
                                                <b-input
                                                    type="search"
                                                    v-model="showsFilter"
                                                    :placeholder="$t('shows.placeholder_filter_shows')"></b-input>
                                                <b-input-group-append>
                                                    <b-button
                                                        @click="clearFilter"
                                                        variant="secondary"
                                                        :disabled="!showsFilter">x</b-button>
                                                </b-input-group-append>
                                            </b-input-group>
                                        </b-col>
                                    </b-row>
                                </b-card-title>
                                <b-card-body>
                                    <b-spinner type="grow" :label="$t('shows.shows_loading')" v-show="isLoadingShows"></b-spinner>
                                    <ul class="list-unstyled">
                                        <show v-for="show in shows" :key="show.attributes.guid" :show="show" :feedId="feed.id"></show>

                                        <li v-show="showsFilter && !shows.length">
                                            <b-alert show variant="info">{{ $t('shows.text_no_shows_for_filtered_results') }}</b-alert>
                                        </li>
                                        <li v-if="!shows || shows.length < 1 && !isLoadingShows">
                                            <b-button
                                                :to="'/podcast/' + feedId + '/episode'"
                                                variant="info"
                                                v-b-popover.hover.top="$t('feeds.title_create_first_show')">{{ $t('shows.button_create_first_show') }}</b-button>
                                        </li>
                                    </ul>
                                    <b-list-group horizontal="md">
                                        <b-list-group-item>
                                            <b-link :to="'/podcast/' + feed.id + '/episoden'" v-b-popover.hover.top="$t('feeds.popover_all_shows')">{{ $t('feeds.link_all_shows') }}</b-link>
                                        </b-list-group-item>
                                        <b-list-group-item>
                                            <b-link :to="'/podcast/' + feed.id + '/episode'" v-b-popover.hover.top="$t('feeds.title_create_show')">{{ $t('feeds.link_create_show') }}</b-link>
                                        </b-list-group-item>
                                        <b-list-group-item v-if="hasAuphonic">
                                            <b-link @click.stop="auphonicLink">{{ $t('feeds.link_create_auphonic_production') }}</b-link>
                                        </b-list-group-item>
                                        <b-list-group-item>
                                            <b-link :id="'infos-shows-feed-' + feed.id" v-b-tooltip.hover.bottom="$t('feeds.title_infos')">Infos</b-link>
                                            <b-popover
                                                    :target="`infos-shows-feed-${feed.id}`"
                                                    :container="`infos-shows-feed-container-${feed.id}`"
                                                    triggers="click blur"
                                                    placement="top"
                                                    @show="getShowInfos"><div id="'infos-shows-feed-container-' + feed.id">
                                                    <b-spinner v-if="isLoadingInfos"></b-spinner>
                                                    <b-list-group>
                                                        <b-list-group-item
                                                            v-for="(info, key) in infos"
                                                            v-bind:key="key">{{ $t('feeds.' + key, { count: info }) }}</b-list-group-item>
                                                    </b-list-group>
                                                </div></b-popover>
                                        </b-list-group-item>
                                    </b-list-group>
                                </b-card-body>
                            </b-list-group-item>
                            <b-list-group-item v-if="feed.is_importing">
                                <show-import :feed-id="feedId"></show-import>
                            </b-list-group-item>
                            <b-list-group-item v-if="!feed.links['web_original']">
                                <b-card-title>
                                    {{ $t('feeds.title_blog') }}
                                </b-card-title>
                                <b-input-group class="mt-4" v-if="feed.links['web']">
                                    <b-form-input
                                            type="url"
                                            :value="feed.links['web']"
                                            readonly
                                            v-b-popover.hover.top="$t('feeds.title_link_website')"></b-form-input>
                                    <b-button variant="outline-primary"
                                              v-b-popover.hover.top="$t('feeds.button_copy_link')"
                                              v-clipboard:copy="feed.links['web']"
                                              v-clipboard:success="onCopy"
                                              v-clipboard:error="onError"><i class="icon-clipboard"></i></b-button>
                                    <b-button variant="outline-primary"
                                              v-b-popover.hover.top="$t('feeds.button_open_link')"
                                              @click.stop="openLink(feed.links['web'])"><i class="icon-link"></i></b-button>
                                </b-input-group>

                                <b-card-body v-if="!feed.links['web']">
                                    <b-list-group horizontal>
                                        <b-list-group-item>
                                            <b-link
                                                @click.prevent="addBlog"
                                                v-b-popover.hover.top="$t('feeds.text_hover_add_blog')">{{$t('feeds.button_text_add_blog')}}</b-link>
                                        </b-list-group-item>
                                    </b-list-group>
                                </b-card-body>

                                <b-card-body v-if="feed.links['web']">

                                    <b-list-group horizontal>
                                        <b-list-group-item>
                                            <b-link
                                                @click.stop="blogBackendLoginForm"
                                                v-b-popover.hover.top="$t('feeds.text_hover_blog_backend_login')">{{$t('feeds.link_blog_backend_login')}}</b-link>
                                        </b-list-group-item>

                                        <b-list-group-item>
                                            <b-nav>
                                                <b-nav-item-dropdown
                                                        id="blog-nav-dropdown"
                                                        :text="$t('feeds.nav_actions')"
                                                        toggle-class="p-0">
                                                    <b-dropdown-item
                                                        v-b-popover.hover.top="$t('feeds.text_hover_unset_blog')"
                                                        @click.prevent="unsetBlog">{{ $t('feeds.link_remove_blog') }}</b-dropdown-item>
    <!--                                                <b-dropdown-item
                                                        v-b-popover.hover.top="$t('feeds.text_hover_add_redirect_blog')"
                                                        :href="getLink('redirectBlog', feed.id)">{{ $t('feeds.link_add_redirect_blog') }}</b-dropdown-item>-->
                                                    <b-dropdown-item
                                                        v-b-popover.hover.top="$t('feeds.text_hover_add_redirect_blog')"
                                                        @click.stop="redirectWebsite">{{ $t('feeds.link_add_redirect_blog') }}</b-dropdown-item>
                                                    <b-dropdown-divider></b-dropdown-divider>
                                                    <b-dropdown-item
                                                        variant="danger"
                                                        @click.prevent="removeBlog"><i class="icon-trash"></i> {{$t('feeds.text_delete_blog')}}</b-dropdown-item>
                                                </b-nav-item-dropdown>
                                            </b-nav>
                                        </b-list-group-item>
                                    </b-list-group>
                                </b-card-body>
                            </b-list-group-item>

                            <b-list-group-item v-if="feed.links['web_original']">

                                <b-card-title>
                                    {{ $t('feeds.title_blog') }}
                                </b-card-title>

                                <b-row class="mt-4">
                                    <b-col lg="4" sm="12">
                                        <b-form-input
                                            type="url"
                                            :value="feed.links['web_original']"
                                            v-b-popover.hover.top="$t('feeds.title_link_website')"
                                            readonly></b-form-input>
                                    </b-col>
                                    <b-col lg="2" sm="12">
                                        <p class="text-center">
                                            <i class="icon-arrow-long-right"></i><br>
                                            <span class="text-small">{{$t('feeds.text_blog_redirected')}}</span>
                                        </p>
                                    </b-col>
                                    <b-col lg="6" sm="12">
                                        <b-input-group>
                                            <b-form-input
                                                type="url"
                                                :value="feed.links['web']"
                                                v-b-popover.hover.top="$t('feeds.title_link_website_redirect')"
                                                readonly></b-form-input>

                                            <b-input-group-append>
                                                <b-button variant="outline-primary"
                                                          v-b-popover.hover.top="$t('feeds.button_copy_link')"
                                                          v-clipboard:copy="feed.links['web']"
                                                          v-clipboard:success="onCopy"
                                                          v-clipboard:error="onError"><i class="icon-clipboard"></i></b-button>
                                                <b-button variant="outline-primary"
                                                          v-b-popover.hover.top="$t('feeds.button_open_link')"
                                                          @click.stop="openLink(feed.links['web'])"><i class="icon-link"></i></b-button>
                                                <b-button variant="outline-primary"
                                                          v-b-popover.hover.top="$t('feeds.text_hover_delete_blog_redirect')"
                                                          @click.prevent="removeRedirect('blog')"><i class="icon-trash"></i></b-button>
                                            </b-input-group-append>
                                        </b-input-group>
                                    </b-col>
                                </b-row>
                            </b-list-group-item>
                        </b-list-group>

                        <b-card-footer>
                        </b-card-footer>
                    </b-col>
                </b-row>

                <b-row no-gutters v-if="feed.type=='redirect'">
                    <b-col cols="12">
                        <b-card-body>
                            <b-card-title>{{feed.attributes.title}}</b-card-title>
                        </b-card-body>

                        <b-list-group>
                            <b-list-group-item>
                                <b-card-title>
                                    {{ $t('feeds.title_channel') }}
                                </b-card-title>

                                <b-row class="mt-4">
                                    <b-col xl="3" lg="3" sm="12">
                                        <b-form-input
                                            type="url"
                                            aria-describedby="feedHelp"
                                            :value="feed.links['rss_original']"
                                            v-b-popover.hover.top="$t('feeds.title_link_podcast_feed')"
                                            readonly></b-form-input>
                                    </b-col>
                                    <b-col xl="1" sm="12">
                                        <p class="text-center">
                                            <i class="icon-arrow-long-right"></i><br>
                                            <span class="text-small text-nowrap">{{$t('feeds.text_feed_redirected')}}</span>
                                        </p>
                                    </b-col>
                                    <b-col xl="8" lg="8" sm="12">
                                        <b-input-group>
                                            <b-form-input
                                                type="url"
                                                :value="feed.links['rss']"
                                                v-b-popover.hover.top="$t('feeds.title_link_podcast_feed_redirect')"
                                                readonly></b-form-input>

                                            <b-input-group-append>
                                                <b-button variant="outline-primary"
                                                          v-b-popover.hover.top="$t('feeds.button_copy_link')"
                                                          v-clipboard:copy="feed.links['rss']"
                                                          v-clipboard:success="onCopy"
                                                          v-clipboard:error="onError"><i class="icon-clipboard"></i></b-button>
                                                <b-button variant="outline-primary"
                                                          v-b-popover.hover.top="$t('feeds.button_open_link')"
                                                          @click.stop="openLink(feed.links['rss'])"><i class="icon-link"></i>
                                                </b-button>
                                                <b-button variant="outline-primary"
                                                          v-b-popover.hover.top="$t('feeds.text_hover_delete_feed_redirect')"
                                                          @click.prevent="removeRedirect('rss')"><i class="icon-trash"></i>
                                                </b-button>
                                            </b-input-group-append>
                                        </b-input-group>
                                    </b-col>
                                </b-row>

                            </b-list-group-item>
                        </b-list-group>

                        <b-list-group
                            v-if="feed.links['web']">
                            <b-list-group-item v-if="!feed.links['web_original']">
                                <b-card-title>
                                    {{ $t('feeds.title_blog') }}
                                </b-card-title>
                                <b-input-group class="mt-4" v-if="feed.links['web']">
                                    <b-form-input
                                        type="url"
                                        :value="feed.links['web']"
                                        readonly
                                        v-b-popover.hover.top="$t('feeds.title_link_website')"></b-form-input>
                                    <b-button variant="outline-primary"
                                              v-b-popover.hover.top="$t('feeds.button_copy_link')"
                                              v-clipboard:copy="feed.links['web']"
                                              v-clipboard:success="onCopy"
                                              v-clipboard:error="onError"><i class="icon-clipboard"></i></b-button>
                                    <b-button variant="outline-primary"
                                              v-b-popover.hover.top="$t('feeds.button_open_link')"
                                              @click.stop="openLink(feed.links['web'])"><i class="icon-link"></i></b-button>
                                    <b-button @click.prevent="removeBlog"
                                              variant="outline-primary"
                                              class="text-danger"
                                              v-b-popover.hover.top="$t('feeds.button_hover_remove_blog')"><i class="icon-trash"></i></b-button>
                                </b-input-group>
                                <b-button
                                    variant="outline-primary"
                                    @click.prevent="addBlog"
                                    v-if="!feed.links['web']">{{$t('feeds.button_text_add_blog')}}</b-button>
                            </b-list-group-item>

                            <b-list-group-item v-if="feed.links['web_original']">
                                <b-card-title>
                                    {{ $t('feeds.title_blog') }}
                                </b-card-title>
                                <b-row class="mt-4">
                                    <b-col xl="3" lg="3" sm="12">
                                        <b-form-input
                                            type="url"
                                            :value="feed.links['web_original']"
                                            v-b-popover.hover.top="$t('feeds.title_link_website')"
                                            readonly></b-form-input>
                                    </b-col>
                                    <b-col xl="1" sm="12">
                                        <p class="text-center">
                                            <i class="icon-arrow-long-right"></i><br>
                                            <span class="text-small">{{$t('feeds.text_blog_redirected')}}</span>
                                        </p>
                                    </b-col>
                                    <b-col xl="8" lg="8" sm="12">
                                        <b-input-group>
                                            <b-form-input
                                                type="url"
                                                :value="feed.links['web']"
                                                v-b-popover.hover.top="$t('feeds.title_link_website_redirect')"
                                                readonly></b-form-input>

                                            <b-input-group-append>
                                                <b-button variant="outline-primary"
                                                          v-b-popover.hover.top="$t('feeds.button_copy_link')"
                                                          v-clipboard:copy="feed.links['web']"
                                                          v-clipboard:success="onCopy"
                                                          v-clipboard:error="onError"><i class="icon-clipboard"></i></b-button>
                                                <b-button variant="outline-primary"
                                                          v-b-popover.hover.top="$t('feeds.button_open_link')"
                                                          @click.stop="openLink(feed.links['web'])"><i class="icon-link"></i></b-button>
                                                <b-button variant="outline-primary"
                                                          v-b-popover.hover.top="$t('feeds.text_hover_delete_blog_redirect')"
                                                          @click.prevent="removeRedirect('blog')"><i class="icon-trash"></i></b-button>
                                            </b-input-group-append>
                                        </b-input-group>
                                    </b-col>
                                </b-row>
                            </b-list-group-item>
                        </b-list-group>
                    </b-col>
                </b-row>
            </b-card>
        </b-overlay>
        <edit-link-modal
            id="edit-link-modal"
            :feed-id="feedId"
            :custom-domain-feature=this.customDomainFeature
        ></edit-link-modal>
        <redirect-link-modal :feed-id="feedId"></redirect-link-modal>
        <ad-tracking-modal :feed-id="feedId"></ad-tracking-modal>
        <social-media-modal :feed-id="feedId"></social-media-modal>
        <default-settings-modal :feed-id="feedId"></default-settings-modal>
        <privacy-modal :feed-id="feedId"></privacy-modal>
        <auphonic-modal :feed-id="feedId"></auphonic-modal>
        <blog-backend-modal
            v-if="feed"
            :feed-id="feedId"
            :subdomain="feed.links['web']" :logged-on-user="username"></blog-backend-modal>
    </div>
</template>

<script>
    import {eventHub} from '../../app';
    import Editable from "../Editable";
    import Show from "../show/Show";
    import ShowImport from "../show/ShowImport";
    import AdTrackingModal from "./AdTrackingModal";
    import SocialMediaModal from "./SocialMediaModal";
    import EditLinkModal from "./EditLinkModal";
    import RedirectLinkModal from "./RedirectLinkModal";
    import EmbedModal from "../media/EmbedModal"
    import LogoSelector from "../media/LogoSelector";
    import BlogBackendModal from "./BlogBackendModal";
    import DefaultSettingsModal from "./DefaultSettingsModal";
    import PrivacyModal from "./PrivacyModal";
    import AuphonicModal from "./auphonic/AuphonicModal";

    export default {
        name: "channel",

        components: {
            LogoSelector,
            Editable,
            Show,
            ShowImport,
            AdTrackingModal,
            SocialMediaModal,
            EditLinkModal,
            RedirectLinkModal,
            EmbedModal,
            BlogBackendModal,
            DefaultSettingsModal,
            PrivacyModal,
            AuphonicModal
        },

        props: {
            feedId: {
                type: String,
                required: true
            },
            logoUploadUrl: {
                type: String,
                required: true
            },
            username: {
                type: String,
                required: true
            },
            canEmbed: {
                type: Boolean,
                required: true,
                default: false
            },
            hasAuphonic: {
                type: [Boolean, String],
                required: true,
                default: false
            },
            customDomainFeature: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                restrictions: [
                    'type',
                    'extension',
                    'square',
                    'dimension_min',
                    'dimension_max',
                    'size',
                    'colormodel',
                    'transparency',
                ],
                feedUrl: null,
                feed: null,
                state: 'secondary',
                stateLogo: 'secondary',
                stateText: [this.$t('feeds.text_logo_state_unknown')],
                stateLogoText: [this.$t('feeds.text_logo_state_unknown')],
                item: null,
                shows: [],
                infos: {},
                isLoadingShows: true,
                isLoadingInfos: false,
                noFilteredResult: false,
                showsFilter: null,
                isLoadingChannel: true,
                oFeed: {},
                statusOpts: [
                    { value: null, text: this.$t('shows.text_label_state_all') },
                    { value: 'status:published', text: this.$t('shows.text_label_state_published') },
                    { value: 'status:draft', text: this.$t('shows.text_label_state_draft') },
                    { value: 'status:scheduled', text: this.$t('shows.text_label_state_scheduled') },
                    { value: 'status:noenclosure', text: this.$t('shows.text_label_state_noenclosure') }
                ],
                statusSelected: null,
            }
        },

        methods: {
            getLink(type, id) {
                switch (type) {
                    case 'embed' :
                        return "/player/config/";
/*                    case 'promotion' :
                        return "/beta/podcast/anmeldung/" + id;*/
/*                    case 'state' :
                        return "/beta/podcast/state/" + id;*/
                    case 'settings' :
                        return "/podcast/" + id;
/*                    case 'redirectBlog' :
                        return "/legacy/podcasts/#addBlogRedirect/" + id;*/
                    case 'editBaseLink' :
                        return "/legacy/podcasts/#editBaseLink/" + id;
                    case 'shows' :
                        return "/beta/podcast/" + id + "/episoden/";
                    case 'createShow' :
                        return "/podcast/" + id + "/episoden/#add";
                    case 'auphonicApproval' :
                        return "/freigaben";
                    case 'auphonicProduction' :
                        return "/podcast/" + id + "/episoden/#auphonic/" + id;
                    case 'deleteChannel' :
                        return "/legacy/podcasts/#deleteChannel/" + id;
                    case 'addChannelRedirect' :
                        return "/legacy/podcasts/#addChannelRedirect/" + id;
                    case 'removeChannelRedirect' :
                        return "/legacy/podcasts/#removeChannelRedirect/" + id;
                }
            },
            onCopy: function (e) {
                eventHub.$emit('show-message:success', this.$t('feeds.copy_success_message', {message:  e.text}));
            },
            onError: function (e) {
                eventHub.$emit('show-message:error', this.$t('feeds.copy_error_message'));
            },
            openLink(link, name, params) {
                const _name = name ? name : 'neww';
                const _params = params ? params : '';
                window.open(link, _name, _params);
            },
            getFeed() {
                axios.get('/api/feeds/' + this.feedId)
                    .then((response) => {
                        this.feed = response.data;
                        this.getState();
                        this.emitPageInfo();
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                        this.isLoadingChannel = false;
                    });
            },
            removeChannel() {
                if (confirm(this.$t('feeds.confirm_message_remove_channel'))) {
                    var sign = prompt(this.$t('feeds.prompt_message_remove_channel', {feedid: this.feed.id}));

                    if (sign && sign === this.feed.id) {
                        window.scrollTo(0,275);
                        axios.delete('/api/feeds/' + this.feed.id)
                            .then((response) => {
                                eventHub.$emit('show-message:success', response.data);
                                eventHub.$emit('update:channels');
                            })
                            .catch(error => {
                                this.showError(error);
                            }).then(() => {
                                this.$router.push({ path: '/' });
                            });
                    } else {
                        this.showError({ message: this.$t('feeds.error_message_remove_channel')});
                    }
                }
            },
            removeLogo() {
                if (confirm(this.$t('feeds.confirm_message_remove_logo'))) {
                    axios.delete('/feed/logo/' + this.feed.id)
                        .then((response) => {
                            eventHub.$emit('show-message:success', response.data);
                            this.getFeed();
                        })
                        .catch(error => {
                            this.showError(error);
                        }).then(() => {
                            window.scrollTo(0,275);
                        });
                }
            },
            updateContent(type, content, guid) {
                let data = {
                    type: type,
                    content: content,
                    guid: guid
                };
                axios.put('/beta/podcast/' + this.feed.id, data)
                    .then((response) => {
                        eventHub.$emit('show-message:success', response.data);

                        if (type === 'logo'
                            || content === 'add-blog'
                            || content === 'unset-blog'
                            || content === 'remove-blog'
                            || content === 'set-redirect'
                            || content === 'change-url'
                            || content === 'remove-redirect') {
                            this.getFeed();
                        }
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                        eventHub.$emit('content:update:finished:' + this.feed.id);
                    });
            },
            getState() {
                axios.get('/beta/podcast/state/' + this.feedId)
                    .then((response) => {
                        this.state = typeof response.data.state == 'object' ? response.data.state[0] : response.data.state;

                        let message = response.data.message;
                        this.stateText = '';
                        for (let elem of ['Channel', 'Enclosure', 'Logo', 'Show']) {
                            for (let k of ['errors', 'warnings']) {
                                if (message[elem][k].length > 0) {
                                    this.stateText = message[elem][k];
                                    break;
                                }
                            }
                        }

                        this.stateLogo = response.data.stateLogo;

                        switch(this.stateLogo) {
                            case 'danger' :
                                this.stateLogoText = response.data.message.Logo.errors;
                                break;
                            case 'warning' :
                                this.stateLogoText = response.data.message.Logo.warnings;
                                break;
                            case 'success' :
                                this.stateLogoText = [this.$t('feeds.text_logo_state_success')];
                                break;
                        }
                        //let messages = response.data.message;
                    })
                    .catch(error => {
                        this.showError(error);
                    });
            },
            getItemId() {
                let itemId = 'item-' + this.feedId;

                return itemId.toLowerCase();
            },
            getShows(_filter) {
                _filter = _filter || '';
                var pageSize = 3;
                if (_filter) {
                    pageSize = 5;
                }
                axios.get('/api/feed/' + this.feedId + '/shows?sortBy=lastUpdate&sortDesc=true&page[number]=1&page[size]=' + pageSize + '&filter=' + _filter)
                    .then((response) => {
                        this.shows = response.data.data;
                        this.isLoadingShows = false;

                        if (_filter && this.shows.length < 1) {
                            this.noFilteredResult = true;
                        }
                    })
                    .catch(error => {
                        this.isLoadingShows = false;
                        this.showError(error);
                    });
            },
            getShowInfos() {
                this.isLoadingInfos = true;

                axios.get('/api/stats/shows/' + this.feedId)
                    .then((response) => {
                        this.infos = response.data;
                        this.isLoadingInfos = false;
                    })
                    .catch(error => {
                        this.isLoadingInfos = false;
                        this.showError(error);
                    });
            },
            editLink() {
                eventHub.$emit('edit-link-modal:show:' + this.feed.id);
            },
            redirectLink() {
                eventHub.$emit('redirect-link-modal:show:' + this.feed.id, 'rss');
            },
            redirectWebsite() {
                eventHub.$emit('redirect-link-modal:show:' + this.feed.id, 'blog');
            },
            adTrackingLink() {
                eventHub.$emit('ad-tracking-modal:show:' + this.feed.id);
            },
            socialMediaLink() {
                eventHub.$emit('social-media-modal:show:' + this.feed.id);
            },
            defaultSettingsLink() {
                eventHub.$emit('default-settings-modal:show:' + this.feed.id);
            },
            auphonicLink() {
                eventHub.$emit('auphonic-modal:show:' + this.feed.id);
            },
            privacyLink() {
                eventHub.$emit('privacy-modal:show:' + this.feed.id);
            },
            blogBackendLoginForm() {
                eventHub.$emit('blog-backend-modal:show:' + this.feed.id);
            },
            removeRedirect(type) {
                if (confirm(this.$t('feeds.confirm_remove_redirect_' + type, { id: this.feed.attributes.title }))) {
                    window.scrollTo(0,275);
                    this.updateContent(type, 'remove-redirect');
                }
            },
            addBlog() {
                if (confirm(this.$t('feeds.confirm_add_blog', { id: this.feed.attributes.title }))) {
                    window.scrollTo(0,275);
                    this.updateContent('blog', 'add-blog');
                }
            },
            unsetBlog() {
                if (confirm(this.$t('feeds.confirm_unset_blog', { id: this.feed.attributes.title }))) {
                    window.scrollTo(0,275);
                    this.updateContent('blog', 'unset-blog');
                }
            },
            removeBlog() {
                if (confirm(this.$t('feeds.confirm_remove_blog', { id: this.feed.attributes.title }))) {
                    window.scrollTo(0,275);
                    this.updateContent('blog', 'remove-blog');
                }
            },
            emitPageInfo() {
                let items = [{
                    text: this.feed.attributes.rss.title,
                    href: '/podcasts/' + this.feedId,
                }];
                let page = {
                    header: this.$t('feeds.page_header_podcast', { title: this.feed.attributes.rss.title }),
                    subheader: this.$t('feeds.page_subheader_podcast'),
                }
                eventHub.$emit('podcasts:page:infos', items, page);
            },
            clearFilter() {
                this.showsFilter = '';
                this.statusSelected = null;
            }
        },

        computed: {
/*            stateClass() {
                return 'badge badge-indicator badge-' + this.state;
            },*/
            stateTitle() {
                return this.$t('feeds.state_title_' + this.state);
            },
            stateTextTitle() {
                return this.$t('feeds.state_text_' + this.state);
            },
            stateLogoTitle() {
                return this.$t('feeds.state_title_' + this.stateLogo);
            },
        },

        created() {
            //this.feed = this.oFeed;
            this.getFeed();
            this.item = this.getItemId();
            this.getState();
            this.getShows();
        },

        beforeDestroy() {
            eventHub.$off("update:content:" + this.feedId);
            eventHub.$off("usage:refresh:" + this.feedId);
            eventHub.$off("update:shows:" + this.feedId);
        },

        mounted() {
            eventHub.$on("usage:refresh:" + this.feedId, () => {
                eventHub.$emit('show-message:success', this.$t('feeds.logo_upload_success_message'));
                this.isLoadingChannel = true;
                this.getFeed();
            });
            eventHub.$on("update:content:" + this.feedId, (type, content, guid) => {
                if (type === 'showlogo') {
                    return false;
                }
                if (type === 'logo') {
                    content = content.id;
                }
                this.updateContent(type, content, guid);
            });
            eventHub.$on("update:shows:" + this.feedId, () => {
                this.getShows(this.showsFilter);
            });
        },

        watch: {
            showsFilter: function(filter) {
                var _filter = '';
                if (typeof filter !== 'undefined' && filter && filter.length >= 2) {
                    _filter = filter;
                }
                this.getShows(_filter);
            },
        }
    }
</script>

<style scoped>
    .player-frame {
        border: 0;
        width: 400px;
        height: 500px;
        overflow: hidden;
    }
</style>
