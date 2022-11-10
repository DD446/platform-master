<template>
    <b-overlay :show="isLoading" rounded="sm">
        <template #overlay>
            <div class="text-center">
                <b-spinner type="grow"></b-spinner>
                <p class="p-4">
                    <span v-text="statusString" class="m-4"></span>
                </p>
            </div>
        </template>
        <b-container>
            <validation-observer ref="observer" v-slot="{ handleSubmit }">
                <b-form @submit.stop.prevent="handleSubmit(onSubmit)">
                    <b-row>
                        <b-col cols="12" lg="6">
                            <b-form-group
                                id="input-group-1"
                                :label="$t('auphonic.preset_title')"
                                label-for="input-1"
                                :description="''"
                            >
                                <validation-provider
                                    name="Title"
                                    :rules="{ required: true, min: 5 }"
                                    v-slot="validationContext"
                                >
                                    <b-form-input
                                        id="input-1"
                                        v-model="form.title"
                                        type="text"
                                        :placeholder="$t('auphonic.preset_title_placeholder')"
                                        required
                                    ></b-form-input>
                                </validation-provider>
                            </b-form-group>

                            <b-form-group
                                id="input-group-2"
                                :label="$t('auphonic.preset_subtitle')"
                                label-for="input-2"
                                :description="''"
                            >
                                <validation-provider
                                    name="Subtitle"
                                    :rules="{ max: 255 }"
                                    v-slot="validationContext"
                                >
                                    <b-form-input
                                        id="input-2"
                                        v-model="form.subtitle"
                                        type="text"
                                        maxlength="255"
                                        :placeholder="$t('auphonic.preset_subtitle_placeholder')"
                                    ></b-form-input>
                                </validation-provider>
                            </b-form-group>

                            <b-form-group
                                id="input-group-3"
                                :label="$t('auphonic.preset_publisher')"
                                label-for="input-3"
                                :description="''"
                            >
                                <b-form-input
                                    id="input-3"
                                    v-model="form.publisher"
                                    type="text"
                                    :placeholder="$t('auphonic.preset_publisher_placeholder')"
                                ></b-form-input>
                            </b-form-group>

                            <b-form-group
                                id="input-group-4"
                                :label="$t('auphonic.preset_artist')"
                                label-for="input-4"
                                :description="''"
                            >
                                <b-form-input
                                    id="input-4"
                                    v-model="form.artist"
                                    type="text"
                                    :placeholder="$t('auphonic.preset_artist_placeholder')"
                                ></b-form-input>
                            </b-form-group>

                            <b-form-group
                                id="input-group-8"
                                :label="$t('auphonic.preset_track')"
                                label-for="input-8"
                                :description="''"
                            >
                                <b-form-input
                                    id="input-4"
                                    v-model="form.track"
                                    type="number"
                                    :placeholder="$t('auphonic.preset_track_placeholder')"
                                ></b-form-input>
                            </b-form-group>

                            <b-form-group
                                id="input-group-5"
                                :label="$t('auphonic.preset_link')"
                                label-for="input-5"
                                :description="''"
                            >
                                <b-form-input
                                    id="input-5"
                                    v-model="form.url"
                                    type="url"
                                    :placeholder="$t('auphonic.preset_link_placeholder')"
                                ></b-form-input>
                            </b-form-group>

                            <b-form-group
                                id="input-group-6"
                                :label="$t('auphonic.preset_license')"
                                label-for="input-6"
                                :description="''"
                            >
                                <b-form-input
                                    id="input-6"
                                    v-model="form.license"
                                    type="text"
                                    :placeholder="$t('auphonic.preset_license_placeholder')"
                                ></b-form-input>
                            </b-form-group>

                            <b-form-group
                                id="input-group-7"
                                :label="$t('auphonic.preset_license_url')"
                                label-for="input-7"
                                :description="''"
                            >
                                <b-form-input
                                    id="input-7"
                                    v-model="form.license_url"
                                    type="url"
                                    :placeholder="$t('auphonic.preset_license_url_placeholder')"
                                ></b-form-input>
                            </b-form-group>

                        </b-col>
                        <b-col cols="12" lg="6">
                            <b-form-group
                                id="input-group-7"
                                :label="$t('auphonic.preset_description')"
                                label-for="input-7"
                                :description="''"
                            >
                                <validation-provider
                                vid="summary"
                                :rules="{ required: false, max: 4000 }"
                                v-slot="validationContext"
                                :name="$t('shows.text_label_description_show')"
                            >
                                <div id="editor">
                                    <div class="menubar-wrapper">
                                        <editor-menu-bar
                                            :editor="editor"
                                            @hide="hideLinkMenu"
                                            v-slot="{ commands, isActive, getMarkAttrs, menu }">
                                            <div class="menubar position-relative">
                                                <div class="menuitem">
                                                    <b-button
                                                        :class="{ 'active': isActive.bold() }"
                                                        variant="outline-secondary"
                                                        v-b-popover.hover.top="$t('shows.editor_popover_bold')"
                                                        @click="commands.bold">
                                                        <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type bold" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-bold b-icon bi"><g data-v-c6b02ba4=""><path d="M8.21 13c2.106 0 3.412-1.087 3.412-2.823 0-1.306-.984-2.283-2.324-2.386v-.055a2.176 2.176 0 0 0 1.852-2.14c0-1.51-1.162-2.46-3.014-2.46H3.843V13H8.21zM5.908 4.674h1.696c.963 0 1.517.451 1.517 1.244 0 .834-.629 1.32-1.73 1.32H5.908V4.673zm0 6.788V8.598h1.73c1.217 0 1.88.492 1.88 1.415 0 .943-.643 1.449-1.832 1.449H5.907z"></path></g></svg>
                                                    </b-button>

                                                    <b-button
                                                        :class="{ 'active': isActive.italic() }"
                                                        variant="outline-secondary"
                                                        v-b-popover.hover.top="$t('shows.editor_popover_italic')"
                                                        @click="commands.italic"
                                                    >
                                                        <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type italic" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-italic b-icon bi"><g data-v-c6b02ba4=""><path d="M7.991 11.674L9.53 4.455c.123-.595.246-.71 1.347-.807l.11-.52H7.211l-.11.52c1.06.096 1.128.212 1.005.807L6.57 11.674c-.123.595-.246.71-1.346.806l-.11.52h3.774l.11-.52c-1.06-.095-1.129-.211-1.006-.806z"></path></g></svg>
                                                    </b-button>

                                                    <b-button
                                                        :class="{ 'active': isActive.underline() }"
                                                        variant="outline-secondary"
                                                        v-b-popover.hover.top="$t('shows.editor_popover_underline')"
                                                        @click="commands.underline"
                                                    >
                                                        <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type underline" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-underline b-icon bi"><g data-v-c6b02ba4=""><path d="M5.313 3.136h-1.23V9.54c0 2.105 1.47 3.623 3.917 3.623s3.917-1.518 3.917-3.623V3.136h-1.23v6.323c0 1.49-.978 2.57-2.687 2.57-1.709 0-2.687-1.08-2.687-2.57V3.136zM12.5 15h-9v-1h9v1z"></path></g></svg>
                                                    </b-button>

                                                    <b-button
                                                        :class="{ 'active': isActive.strike() }"
                                                        variant="outline-secondary"
                                                        v-b-popover.hover.top="$t('shows.editor_popover_strike')"
                                                        @click="commands.strike"
                                                    >
                                                        <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type strikethrough" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-strikethrough b-icon bi"><g data-v-c6b02ba4=""><path d="M6.333 5.686c0 .31.083.581.27.814H5.166a2.776 2.776 0 0 1-.099-.76c0-1.627 1.436-2.768 3.48-2.768 1.969 0 3.39 1.175 3.445 2.85h-1.23c-.11-1.08-.964-1.743-2.25-1.743-1.23 0-2.18.602-2.18 1.607zm2.194 7.478c-2.153 0-3.589-1.107-3.705-2.81h1.23c.144 1.06 1.129 1.703 2.544 1.703 1.34 0 2.31-.705 2.31-1.675 0-.827-.547-1.374-1.914-1.675L8.046 8.5H1v-1h14v1h-3.504c.468.437.675.994.675 1.697 0 1.826-1.436 2.967-3.644 2.967z"></path></g></svg>
                                                    </b-button>

                                                    <b-button
                                                        :class="{ 'active': isActive.link() }"
                                                        variant="outline-secondary"
                                                        v-b-popover.hover.top="$t('shows.editor_popover_link')"
                                                        @click="showLinkMenu(getMarkAttrs('link'))"
                                                    >
                                                        <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="link" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-link b-icon bi"><g data-v-c6b02ba4=""><path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9c-.086 0-.17.01-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z"></path><path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4.02 4.02 0 0 1-.82 1H12a3 3 0 1 0 0-6H9z"></path></g></svg>
                                                    </b-button>

                                                    <b-button
                                                        :class="{ 'active': isActive.heading({ level: 1 })  }"
                                                        variant="outline-secondary"
                                                        v-b-popover.hover.top="$t('shows.editor_popover_h1')"
                                                        @click="commands.heading({ level: 1 }) "
                                                    >
                                                        <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type h1" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-h1 b-icon bi"><g data-v-c6b02ba4=""><path d="M8.637 13V3.669H7.379V7.62H2.758V3.67H1.5V13h1.258V8.728h4.62V13h1.259zm5.329 0V3.669h-1.244L10.5 5.316v1.265l2.16-1.565h.062V13h1.244z"></path></g></svg>
                                                    </b-button>

                                                    <b-button
                                                        :class="{ 'active': isActive.heading({ level: 2 })  }"
                                                        variant="outline-secondary"
                                                        v-b-popover.hover.top="$t('shows.editor_popover_h2')"
                                                        @click="commands.heading({ level: 2 }) "
                                                    >
                                                        <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type h2" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-h2 b-icon bi"><g data-v-c6b02ba4=""><path d="M7.638 13V3.669H6.38V7.62H1.759V3.67H.5V13h1.258V8.728h4.62V13h1.259zm3.022-6.733v-.048c0-.889.63-1.668 1.716-1.668.957 0 1.675.608 1.675 1.572 0 .855-.554 1.504-1.067 2.085l-3.513 3.999V13H15.5v-1.094h-4.245v-.075l2.481-2.844c.875-.998 1.586-1.784 1.586-2.953 0-1.463-1.155-2.556-2.919-2.556-1.941 0-2.966 1.326-2.966 2.74v.049h1.223z"></path></g></svg>
                                                    </b-button>

                                                    <b-button
                                                        :class="{ 'active': isActive.heading({ level: 3 })  }"
                                                        variant="outline-secondary"
                                                        v-b-popover.hover.top="$t('shows.editor_popover_h3')"
                                                        @click="commands.heading({ level: 3 }) "
                                                    >
                                                        <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type h3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-h3 b-icon bi"><g data-v-c6b02ba4=""><path d="M7.637 13V3.669H6.379V7.62H1.758V3.67H.5V13h1.258V8.728h4.62V13h1.259zm3.625-4.272h1.018c1.142 0 1.935.67 1.949 1.674.013 1.005-.78 1.737-2.01 1.73-1.08-.007-1.853-.588-1.935-1.32H9.108c.069 1.327 1.224 2.386 3.083 2.386 1.935 0 3.343-1.155 3.309-2.789-.027-1.51-1.251-2.16-2.037-2.249v-.068c.704-.123 1.764-.91 1.723-2.229-.035-1.353-1.176-2.4-2.954-2.385-1.873.006-2.857 1.162-2.898 2.358h1.196c.062-.69.711-1.299 1.696-1.299.998 0 1.695.622 1.695 1.525.007.922-.718 1.592-1.695 1.592h-.964v1.074z"></path></g></svg>
                                                    </b-button>

                                                    <b-button
                                                        :class="{ 'active': isActive.bullet_list() }"
                                                        variant="outline-secondary"
                                                        v-b-popover.hover.top="$t('shows.editor_popover_bullet_list')"
                                                        @click="commands.bullet_list"
                                                    >
                                                        <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="list ul" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-list-ul b-icon bi"><g data-v-c6b02ba4=""><path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm-3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"></path></g></svg>
                                                    </b-button>

                                                    <b-button
                                                        :class="{ 'active': isActive.ordered_list() }"
                                                        variant="outline-secondary"
                                                        v-b-popover.hover.top="$t('shows.editor_popover_ordered_list')"
                                                        @click="commands.ordered_list"
                                                    >
                                                        <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="list ol" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-list-ol b-icon bi"><g data-v-c6b02ba4=""><path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"></path><path d="M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 0 1-.492.594v.033a.615.615 0 0 1 .569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 0 0-.342.338v.041zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635V5z"></path></g></svg>
                                                    </b-button>

                                                    <b-button
                                                        variant="outline-secondary"
                                                        v-b-popover.hover.top="$t('shows.editor_popover_undo')"
                                                        @click="commands.undo"
                                                    >
                                                        <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="arrow counterclockwise" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-arrow-counterclockwise b-icon bi"><g data-v-c6b02ba4=""><path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"></path><path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"></path></g></svg>
                                                    </b-button>

                                                    <b-button
                                                        variant="outline-secondary"
                                                        v-b-popover.hover.top="$t('shows.editor_popover_redo')"
                                                        @click="commands.redo"
                                                    >
                                                        <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="arrow clockwise" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-arrow-clockwise b-icon bi"><g data-v-c6b02ba4=""><path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"></path><path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"></path></g></svg>
                                                    </b-button>

                                                    <b-button
                                                        variant="outline-secondary"
                                                        v-b-popover.hover.top="$t('shows.editor_popover_sourcecode')"
                                                        @click.stop="switchSourceView"
                                                    >
                                                        <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="code" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-code b-icon bi"><g data-v-c6b02ba4=""><path d="M5.854 4.854a.5.5 0 1 0-.708-.708l-3.5 3.5a.5.5 0 0 0 0 .708l3.5 3.5a.5.5 0 0 0 .708-.708L2.707 8l3.147-3.146zm4.292 0a.5.5 0 0 1 .708-.708l3.5 3.5a.5.5 0 0 1 0 .708l-3.5 3.5a.5.5 0 0 1-.708-.708L13.293 8l-3.147-3.146z"></path></g></svg>
                                                        {{ sourceCodeLabel }}
                                                    </b-button>

                                                    <div class="menuseparator"></div>

                                                </div>

                                                <div class="shadow p-3 mb-5 bg-white rounded position-absolute" v-show="linkMenuIsActive">
                                                    <b-form
                                                        @submit.prevent="setLinkUrl(commands.link, linkUrl)">
                                                        <b-row>
                                                            <b-col cols="12">
                                                                <b-button-close
                                                                    class="float-right"
                                                                    v-b-popover.hover="$t('shows.text_popover_add_link_close_button')"
                                                                    @click="setLinkUrl(commands.link, null)"></b-button-close>
                                                            </b-col>
                                                        </b-row>
                                                        <b-row>
                                                            <b-col cols="12">
                                                                <h5>
                                                                    {{ $t('shows.header_add_link') }}
                                                                </h5>
                                                            </b-col>
                                                        </b-row>
                                                        <b-row>
                                                            <b-col cols="12" class="mt-2">
                                                                <b-input v-model="linkUrl"
                                                                         placeholder="https://" ref="linkInput"
                                                                         @keydown.esc="hideLinkMenu"/>
                                                            </b-col>
                                                        </b-row>
                                                        <b-row>
                                                            <b-col cols="2" offset="8" class="mt-2">
                                                                <b-button
                                                                    variant="primary"
                                                                    @click="setLinkUrl(commands.link, linkUrl)">{{ $t('shows.button_add_link') }}</b-button>
                                                            </b-col>
                                                        </b-row>
                                                    </b-form>
                                                </div>
                                            </div>
                                        </editor-menu-bar>
                                        <b-overlay :show="isLoadingDefaults" rounded="sm">
                                            <editor-content
                                                v-show="!showSource"

                                                :editor="editor"
                                            />
                                            <b-textarea id="summary" :state="getValidationState(validationContext)"
                                                        v-show="showSource"
                                                        rows="10"
                                                        :placeholder="$t('shows.text_placeholder_description_show')"
                                                        v-model="form.summary"
                                                        name="summary"></b-textarea>
                                        </b-overlay>
                                    </div>
                                </div>
                                <b-form-invalid-feedback
                                    style="display:block"
                                    id="input-description-live-feedback">{{ validationContext.errors[0] }}
                                </b-form-invalid-feedback>
                            </validation-provider>
                            </b-form-group>

                            <b-form-group
                                id="input-group-9"
                                :label="$t('auphonic.preset_genre')"
                                label-for="input-9"
                                :description="''"
                            >
                                <b-form-input
                                    id="input-9"
                                    v-model="form.genre"
                                    type="text"
                                    :placeholder="$t('auphonic.preset_genre_placeholder')"
                                ></b-form-input>
                            </b-form-group>

                            <b-form-group
                                id="input-group-10"
                                :label="$t('auphonic.preset_year')"
                                label-for="input-10"
                                :description="''"
                            >
                                <b-form-input
                                    id="input-10"
                                    v-model="form.year"
                                    type="number"
                                    min="1900"
                                    :max="maxDate"
                                    :placeholder="$t('auphonic.preset_year_placeholder')"
                                ></b-form-input>
                            </b-form-group>

                            <b-form-group
                                id="input-group-11"
                                :label="$t('auphonic.preset_tags')"
                                label-for="input-11"
                                :description="''"
                            >
                                <b-form-tags
                                    id="input-11"
                                    separator=",;"
                                    tag-variant="primary"
                                    tag-pills
                                    remove-on-delete
                                    v-model="form.tags"
                                    :placeholder="$t('auphonic.preset_tags_placeholder')"
                                ></b-form-tags>
        <!--                        <b-form-text id="tags-remove-on-delete-help" class="mt-2">
                                    Press <kbd>Backspace</kbd> to remove the last tag entered
                                </b-form-text>-->
                            </b-form-group>

<!--                            <b-form-group
                                id="input-group-location"
                                :label="$t('auphonic.preset_location')"
                                label-for="input-location"
                                :description="''"
                            >
                                <b-row>
                                    <b-col cols="6">
                                        <b-form-input
                                            id="input-location"
                                            v-model="form.location.latitude"
                                            :placeholder="$t('auphonic.preset_latitude_placeholder')"
                                        ></b-form-input>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-form-input
                                            id="input-location-longitude"
                                            v-model="form.location.longitude"
                                            :placeholder="$t('auphonic.preset_longitude_placeholder')"
                                        ></b-form-input>
                                    </b-col>
                                </b-row>
                            </b-form-group>-->
                        </b-col>
                    </b-row>
                    <b-row class="mt-2 mt-lg-4">
                        <b-col cols="12" lg="6">
                            <b-form-group
                                id="input-group-12"
                                :label="$t('auphonic.preset_file')"
                                label-for="input-12"
                                :description="''"
                            >
                                <validation-provider
                                    name="File"
                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-file
                                        accept="audio/*, video/*"
                                        required
                                        size="lg"
                                        id="input-12"
                                        v-model="file"
                                        :placeholder="$t('auphonic.preset_file_placeholder')"
                                        :state="getValidationState(validationContext)"
                                        aria-describedby="input-12-live-feedback"
                                    ></b-form-file>
                                    <b-form-invalid-feedback id="input-12-live-feedback">
                                        {{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </validation-provider>
                            </b-form-group>
                        </b-col>
                        <b-col cols="12" lg="6">
                            <b-form-group
                                id="input-group-13"
                                :label="$t('auphonic.preset_cover')"
                                label-for="input-13"
                                :description="''"
                            >
                                <validation-provider
                                    name="Image"
                                    :rules="{}"
                                    v-slot="validationContext"
                                >
                                    <b-form-file
                                        accept="image/*"
                                        size="lg"
                                        id="input-13"
                                        v-model="image"
                                        :placeholder="$t('auphonic.preset_cover_placeholder')"
                                        :state="getValidationState(validationContext)"
                                        aria-describedby="input-13-live-feedback"
                                    ></b-form-file>
                                    <b-form-invalid-feedback id="input-13-live-feedback">
                                        {{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </validation-provider>
                            </b-form-group>
                        </b-col>
                    </b-row>

<!--                    <b-row class="mt-2 mt-lg-3">
                        <b-col cols="12">
                            <b-form-group
                                id="input-group-14"
                                label-for="input-14"
                                :description="''"
                            >
                                <validation-provider
                                    name="Chapters"
                                    :rules="{}"
                                    v-slot="validationContext"
                                >
                                    <b-form-checkbox
                                        id="append-chapters"
                                        v-model="form.append_chapters"
                                        name="append_chapters"
                                    >
                                        {{ $t('auphonic.preset_chapters') }}
                                    </b-form-checkbox>
                                    <b-form-invalid-feedback id="input-12-live-feedback">
                                        {{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                </validation-provider>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-row class="mt-1">
                        <b-col cols="12">
                            <chaptermark-list
                                v-model="form.chapters"
                                v-if="form.append_chapters"></chaptermark-list>
                        </b-col>
                    </b-row>-->

                    <b-row class="mt-2 mt-lg-4 mb-5">
                        <b-col cols="12">
                            <div class="float-right">
                                <b-button variant="link" class="mr-3" @click="resetForm()">{{ $t('auphonic.button_reset') }}</b-button>
                                <b-button type="submit" variant="primary" size="lg">{{ $t('auphonic.button_submit') }}</b-button>
                            </div>
                        </b-col>
                    </b-row>
                </b-form>
            </validation-observer>
        </b-container>
    </b-overlay>
</template>

<script>
import { Editor, EditorContent, EditorMenuBar } from 'tiptap';
import FileSelector from "../../media/FileSelector";
import {ValidationObserver, ValidationProvider} from "vee-validate";
import * as rules from "vee-validate/dist/rules";
import FileUploader from "../../media/FileUploader";
import {
    Blockquote,
    Bold, BulletList,
    HardBreak,
    Heading,
    History,
    HorizontalRule,
    Italic,
    Link, ListItem, OrderedList,
    Strike,
    Underline
} from "tiptap-extensions";
import {eventHub} from "../../../app";
import ChaptermarkList from "./ChaptermarkList";

//const CSRF_TOKEN = window.axios.defaults.headers.common['X-CSRF-TOKEN'];
let thisYear = new Date().getFullYear();

export default {
    name: "CreateAuphonicProduction",

    components: {
        ChaptermarkList,
        EditorContent,
        EditorMenuBar,
        ValidationProvider,
        ValidationObserver,
        rules,
    },

    data() {
        return {
            isLoading: true,
            form: {
                preset: null,
                feed_id: null,
                title: null,
                subtitle: null,
                summary: null,
                publisher: null,
                track: null,
                append_chapters: false,
                artist: null,
                url: null,
                license: null,
                license_url: null,
                genre: null,
                year: null,
                tags: [],
                chapters: {},
                location: {}
            },
            file: null,
            image: null,
            editor: new Editor({
                content: '',
                extensions: [
                    new Bold(),
                    new Italic(),
                    new Underline(),
                    new Strike(),
                    new Link(),
                    new HardBreak(),
                    new Heading({
                        levels: [1, 2, 3, 4],
                    }),
                    new Blockquote(),
                    new History(),
                    new HorizontalRule(),
                    new OrderedList(),
                    new BulletList(),
                    new ListItem()
                ],
                onUpdate: ({ getHTML }) => {
                    this.emitAfterOnUpdate = true;
                    let html = getHTML();
                    if (html === '<p></p>') {
                        html = '';
                    }
                    this.$emit("editor:input", html);
                },
            }),
            isLoadingDefaults: false,
            showSource: false,
            linkUrl: null,
            linkMenuIsActive: false,
            statusString: this.$t('auphonic.is_loading_data'),
            maxDate: thisYear++,
        }
    },

    props: {
        feedId: {
            type: String,
            required: true
        },
        preset: {
            type: String,
            required: true
        },
        hasAuphonic: {
            type: [Boolean, String],
            required: true,
            default: false
        }
    },

    mounted() {
        this.getPresetData();
        this.emitPageInfo();

        this.$on("editor:input", content => {
            this.form.summary = content;
        });
    },

    methods: {
        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },
        resetForm() {
            this.getPresetData();
            this.file = null;
            this.$nextTick(() => {
                this.$refs.observer.reset();
            });
        },
        onSubmit() {
            this.isLoading = true;

            axios.post('/api/auphonic/presets', this.form)
                .then(response => {
                    this.statusString = response.data.status_string;

                    let file = new FormData;
                    file.append('input_file', this.file);

                    if (this.image) {
                        file.append('image', this.image);
                    }

                    const config = {
                        headers: {
                            'Content-Type': `multipart/form-data; boundary=${file._boundary}`,
                            Authorization: 'Bearer ' + response.data.token
                        }
                    }

                    let url = 'https://auphonic.com/api/production/{uuid}/upload.json'.replace('{uuid}', response.data.uuid);
                    axios.post(
                        url,
                        file,
                        config
                    ).then(_response => {
                        this.statusString = _response.data.data.status_string;
                        let url = 'https://auphonic.com/api/production/{uuid}/start.json'.replace('{uuid}', _response.data.data.uuid);
                        const config = {
                            headers: {
                                Authorization: 'Bearer ' + response.data.token
                            }
                        }
                        axios.post(
                            url,
                            {},
                            config
                        ).then(__response => {
                            this.statusString = this.$t('auphonic.status_progress_hint') + __response.data.data.status_string;

                            Echo.private('auphonic.production.' + __response.data.data.uuid)
                                .listen('\\App\\Events\\AuphonicProductionEvent', (o) => {
                                    this.statusString = this.$t('auphonic.status_progress_hint') + o.webhookCall.payload.status_string;

                                    if (o.webhookCall.payload.status == "3") {
                                        this.statusString = this.$t('auphonic.status_transferring_finished_file');

                                        let url = 'api/auphonic/presets/{preset}'.replace('{preset}', this.preset);
                                        axios.put(url, { uuid: __response.data.data.uuid, feed_id: this.feedId })
                                            .then(response => {
                                                window.location = '/podcasts#/podcast/' + this.feedId + '/episode?media=' + response.data.id;
                                            })
                                            .catch(error => {
                                                // handle error
                                                this.showError(error);
                                            }).then(() => {
                                                this.isLoading = false;
                                            });
                                    }
                                });
                        })
                            .catch(error => {
                                this.showError(error);
                            }).then(() => {
                            });
                    })
                    .catch(error => {
                        this.isLoading = false;
                        this.showError(error);
                    }).then(() => {
                    });
                }).catch(error => {
                    // handle error
                    this.isLoading = false;
                    this.showError(error);
                }).then(() => {
                });
        },
        getPresetData() {
            this.isLoading = true;
            axios.get('/api/auphonic/presets/' + this.preset + '?feed_id=' + this.feedId)
                .then(response => {
                    this.form = response.data;
                    this.form.preset = this.preset;
                    this.form.feed_id = this.feedId;
                    this.editor.setContent(this.form.summary);
                    // TOOD
                    this.form.append_chapters = false;
                })
                .catch(error => {
                    // handle error
                    this.showError(error);
                }).then(() => {
                    this.isLoading = false;
                    this.emitPageInfo();
                });
        },
        showLinkMenu(attrs) {
            this.linkUrl = attrs.href
            this.linkMenuIsActive = true
            this.$nextTick(() => {
                this.$refs.linkInput.focus()
            })
        },
        hideLinkMenu() {
            this.linkUrl = null
            this.linkMenuIsActive = false
        },
        setLinkUrl(command, url) {
            command({ href: url })
            this.hideLinkMenu()
        },
        switchSourceView() {
            this.showSource = !this.showSource;

            if (!this.showSource) {
                this.editor.setContent(this.form.summary);
            }
        },
        emitPageInfo() {
            let items = [{
                text: this.feedId,
                href: '#/podcasts/' + this.feedId,
            },{
                text: this.$t('auphonic.nav_production')
            }];
            let page = {
                header: this.$t('auphonic.page_header_create_production'),
                subheader: this.$t('auphonic.page_subheader_create_production'),
            }
            eventHub.$emit('podcasts:page:infos', items, page);
        }
    },

    computed: {
        sourceCodeLabel() {
            return (this.showSource ? this.$t('shows.label_wysiwyg') : this.$t('shows.label_source_code'));
        }
    },

    beforeDestroy() {
        // Always destroy your editor instance when it's no longer needed
        if (this.editor) this.editor.destroy();
    }
}
</script>

<style scoped>
#editor, .editor {
    background: white;
    color: black;
    background-clip: padding-box;
    border-radius: 4px;
    border: 2px solid rgba(0, 0, 0, 0.2);
    padding: 5px 0;
    margin-bottom: 23px;
}
.menubar {
    border-top-left-radius: inherit;
    border-top-right-radius: inherit;
    position: relative;
    min-height: 1em;
    color: #666;
    padding: 1px 6px;
    top: 0;
    left: 0;
    right: 0;
    border-bottom: 1px solid silver;
    background: white;
    z-index: 10;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    overflow: visible;
}
.menubar .menuseparator {
    border-right: 1px solid #ddd;
    margin-right: 3px;
}
.menuitem {
    margin-right: 3px;
    margin-bottom: 5px;
    display: inline-block;
}
.menuitem .icon {
    display: inline-block;
    line-height: .8;
    vertical-align: -2px;
    padding: 2px 8px;
    cursor: pointer;
}

.ProseMirror-prompt {
    background: white;
    padding: 5px 10px 5px 15px;
    border: 1px solid silver;
    position: fixed;
    border-radius: 3px;
    z-index: 11;
    box-shadow: -.5px 2px 5px rgba(0, 0, 0, .2);
}
</style>

<style>
.ProseMirror {
    padding: 4px 8px 4px 14px;
    margin-top: 1em;
    line-height: 1.2;
    min-height: 180px;
    outline: none;
    word-wrap: break-word;
    white-space: pre-wrap;
    -webkit-font-variant-ligatures: none;
    font-variant-ligatures: none;
    position: relative;
    max-height: 450px;
    overflow: auto;
}

.custom-file-input:lang(en) ~ .custom-file-label::after {
    content: 'Browse';
}
.custom-file-input:lang(de) ~ .custom-file-label::after {
    content: 'Auswählen';
}
</style>
