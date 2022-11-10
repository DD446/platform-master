<template>
    <div class="m-1 mr-2 mb-4 p-4" style="background-color: #f0f3f4; border-radius: 1em">
        <validation-observer ref="observer" v-slot="{ invalid, valid, validated, passes, reset }">
            <b-overlay :show="showOverlay" rounded="lg">
                <b-form ref="form" @submit.stop.prevent="passes(onSubmit)" method="post">
                <b-row class="add-show">
                    <b-col cols="12" xl="6" class="show-form">
                        <b-row>
                            <b-col cols="12">
                                <b-row class="mb-3">
                                    <b-col cols="11">
                                        <label for="title">{{$t('shows.text_label_title_show')}}
                                            <span class="text-muted" :title="$t('shows.text_title_field_required')">*</span>
                                        </label>
                                    </b-col>
                                    <b-col cols="12" lg="11">
                                        <b-row>
                                            <b-col cols="11">
                                                <validation-provider
                                                    vid="title"
                                                    :rules="{ required: true, min: 1, max: 255 }"
                                                    v-slot="validationContext"
                                                    :name="$t('shows.text_label_title_show')"
                                                >
                                                    <b-overlay :show="isLoadingDefaults" rounded="sm">
                                                        <b-input
                                                            size="lg"
                                                            type="text"
                                                            maxlength="255"
                                                            autofocus
                                                            required
                                                            id="title"
                                                            :placeholder="$t('shows.text_placeholder_title_show')"
                                                            v-model="model.title"
                                                            :state="getValidationState(validationContext)"
                                                            name="title"></b-input>
                                                    </b-overlay>
                                                    <b-form-invalid-feedback
                                                        id="input-title-live-feedback">{{ validationContext.errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </validation-provider>
                                            </b-col>
                                            <b-col cols="1">
                                                <i class="icon icon-info-with-circle text-blue"
                                                   v-b-popover.hover="$t('shows.text_popover_title_show')"></i>
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                </b-row>

                                <b-row class="mb-3">
                                    <b-col cols="12">
                                        <label for="description">{{$t('shows.text_label_description_show')}}
                                            <span class="text-muted" :title="$t('shows.text_title_field_required')">*</span></label>
                                    </b-col>
                                    <b-col cols="12" lg="11">
                                        <b-row>
                                            <b-col cols="11">

                                                <validation-provider
                                                    vid="description"
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
<!--                                                                            <b-icon-type-bold></b-icon-type-bold>-->
                                                                            <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type bold" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-bold b-icon bi"><g data-v-c6b02ba4=""><path d="M8.21 13c2.106 0 3.412-1.087 3.412-2.823 0-1.306-.984-2.283-2.324-2.386v-.055a2.176 2.176 0 0 0 1.852-2.14c0-1.51-1.162-2.46-3.014-2.46H3.843V13H8.21zM5.908 4.674h1.696c.963 0 1.517.451 1.517 1.244 0 .834-.629 1.32-1.73 1.32H5.908V4.673zm0 6.788V8.598h1.73c1.217 0 1.88.492 1.88 1.415 0 .943-.643 1.449-1.832 1.449H5.907z"></path></g></svg>
                                                                        </b-button>

                                                                        <b-button
                                                                            :class="{ 'active': isActive.italic() }"
                                                                            variant="outline-secondary"
                                                                            v-b-popover.hover.top="$t('shows.editor_popover_italic')"
                                                                            @click="commands.italic"
                                                                        >
<!--                                                                            <b-icon-type-italic></b-icon-type-italic>-->
                                                                            <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type italic" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-italic b-icon bi"><g data-v-c6b02ba4=""><path d="M7.991 11.674L9.53 4.455c.123-.595.246-.71 1.347-.807l.11-.52H7.211l-.11.52c1.06.096 1.128.212 1.005.807L6.57 11.674c-.123.595-.246.71-1.346.806l-.11.52h3.774l.11-.52c-1.06-.095-1.129-.211-1.006-.806z"></path></g></svg>
                                                                        </b-button>

                                                                        <b-button
                                                                            :class="{ 'active': isActive.underline() }"
                                                                            variant="outline-secondary"
                                                                            v-b-popover.hover.top="$t('shows.editor_popover_underline')"
                                                                            @click="commands.underline"
                                                                        >
<!--                                                                            <b-icon-type-underline></b-icon-type-underline>-->
                                                                            <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type underline" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-underline b-icon bi"><g data-v-c6b02ba4=""><path d="M5.313 3.136h-1.23V9.54c0 2.105 1.47 3.623 3.917 3.623s3.917-1.518 3.917-3.623V3.136h-1.23v6.323c0 1.49-.978 2.57-2.687 2.57-1.709 0-2.687-1.08-2.687-2.57V3.136zM12.5 15h-9v-1h9v1z"></path></g></svg>
                                                                        </b-button>

                                                                        <b-button
                                                                            :class="{ 'active': isActive.strike() }"
                                                                            variant="outline-secondary"
                                                                            v-b-popover.hover.top="$t('shows.editor_popover_strike')"
                                                                            @click="commands.strike"
                                                                        >
<!--                                                                            <b-icon-type-strikethrough></b-icon-type-strikethrough>-->
                                                                            <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type strikethrough" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-strikethrough b-icon bi"><g data-v-c6b02ba4=""><path d="M6.333 5.686c0 .31.083.581.27.814H5.166a2.776 2.776 0 0 1-.099-.76c0-1.627 1.436-2.768 3.48-2.768 1.969 0 3.39 1.175 3.445 2.85h-1.23c-.11-1.08-.964-1.743-2.25-1.743-1.23 0-2.18.602-2.18 1.607zm2.194 7.478c-2.153 0-3.589-1.107-3.705-2.81h1.23c.144 1.06 1.129 1.703 2.544 1.703 1.34 0 2.31-.705 2.31-1.675 0-.827-.547-1.374-1.914-1.675L8.046 8.5H1v-1h14v1h-3.504c.468.437.675.994.675 1.697 0 1.826-1.436 2.967-3.644 2.967z"></path></g></svg>
                                                                        </b-button>

                                                                        <b-button
                                                                            :class="{ 'active': isActive.link() }"
                                                                            variant="outline-secondary"
                                                                            v-b-popover.hover.top="$t('shows.editor_popover_link')"
                                                                            @click="showLinkMenu(getMarkAttrs('link'))"
                                                                        >
<!--                                                                            <b-icon-link></b-icon-link>-->
                                                                            <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="link" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-link b-icon bi"><g data-v-c6b02ba4=""><path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9c-.086 0-.17.01-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z"></path><path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4.02 4.02 0 0 1-.82 1H12a3 3 0 1 0 0-6H9z"></path></g></svg>
                                                                        </b-button>

                                                                        <b-button
                                                                            :class="{ 'active': isActive.heading({ level: 1 })  }"
                                                                            variant="outline-secondary"
                                                                            v-b-popover.hover.top="$t('shows.editor_popover_h1')"
                                                                            @click="commands.heading({ level: 1 }) "
                                                                        >
<!--                                                                            <b-icon-type-h1></b-icon-type-h1>-->
                                                                            <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type h1" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-h1 b-icon bi"><g data-v-c6b02ba4=""><path d="M8.637 13V3.669H7.379V7.62H2.758V3.67H1.5V13h1.258V8.728h4.62V13h1.259zm5.329 0V3.669h-1.244L10.5 5.316v1.265l2.16-1.565h.062V13h1.244z"></path></g></svg>
                                                                        </b-button>

                                                                        <b-button
                                                                            :class="{ 'active': isActive.heading({ level: 2 })  }"
                                                                            variant="outline-secondary"
                                                                            v-b-popover.hover.top="$t('shows.editor_popover_h2')"
                                                                            @click="commands.heading({ level: 2 }) "
                                                                        >
<!--                                                                            <b-icon-type-h2></b-icon-type-h2>-->
                                                                            <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type h2" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-h2 b-icon bi"><g data-v-c6b02ba4=""><path d="M7.638 13V3.669H6.38V7.62H1.759V3.67H.5V13h1.258V8.728h4.62V13h1.259zm3.022-6.733v-.048c0-.889.63-1.668 1.716-1.668.957 0 1.675.608 1.675 1.572 0 .855-.554 1.504-1.067 2.085l-3.513 3.999V13H15.5v-1.094h-4.245v-.075l2.481-2.844c.875-.998 1.586-1.784 1.586-2.953 0-1.463-1.155-2.556-2.919-2.556-1.941 0-2.966 1.326-2.966 2.74v.049h1.223z"></path></g></svg>
                                                                        </b-button>

                                                                        <b-button
                                                                            :class="{ 'active': isActive.heading({ level: 3 })  }"
                                                                            variant="outline-secondary"
                                                                            v-b-popover.hover.top="$t('shows.editor_popover_h3')"
                                                                            @click="commands.heading({ level: 3 }) "
                                                                        >
<!--                                                                            <b-icon-type-h3></b-icon-type-h3>-->
                                                                            <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="type h3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-type-h3 b-icon bi"><g data-v-c6b02ba4=""><path d="M7.637 13V3.669H6.379V7.62H1.758V3.67H.5V13h1.258V8.728h4.62V13h1.259zm3.625-4.272h1.018c1.142 0 1.935.67 1.949 1.674.013 1.005-.78 1.737-2.01 1.73-1.08-.007-1.853-.588-1.935-1.32H9.108c.069 1.327 1.224 2.386 3.083 2.386 1.935 0 3.343-1.155 3.309-2.789-.027-1.51-1.251-2.16-2.037-2.249v-.068c.704-.123 1.764-.91 1.723-2.229-.035-1.353-1.176-2.4-2.954-2.385-1.873.006-2.857 1.162-2.898 2.358h1.196c.062-.69.711-1.299 1.696-1.299.998 0 1.695.622 1.695 1.525.007.922-.718 1.592-1.695 1.592h-.964v1.074z"></path></g></svg>
                                                                        </b-button>

                                                                        <b-button
                                                                            :class="{ 'active': isActive.bullet_list() }"
                                                                            variant="outline-secondary"
                                                                            v-b-popover.hover.top="$t('shows.editor_popover_bullet_list')"
                                                                            @click="commands.bullet_list"
                                                                        >
<!--                                                                            <b-icon-list-ul></b-icon-list-ul>-->
                                                                            <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="list ul" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-list-ul b-icon bi"><g data-v-c6b02ba4=""><path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm-3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"></path></g></svg>
                                                                        </b-button>

                                                                        <b-button
                                                                            :class="{ 'active': isActive.ordered_list() }"
                                                                            variant="outline-secondary"
                                                                            v-b-popover.hover.top="$t('shows.editor_popover_ordered_list')"
                                                                            @click="commands.ordered_list"
                                                                        >
<!--                                                                            <b-icon-list-ol></b-icon-list-ol>-->
                                                                            <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="list ol" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-list-ol b-icon bi"><g data-v-c6b02ba4=""><path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"></path><path d="M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 0 1-.492.594v.033a.615.615 0 0 1 .569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 0 0-.342.338v.041zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635V5z"></path></g></svg>
                                                                        </b-button>

                                                                        <b-button
                                                                            variant="outline-secondary"
                                                                            v-b-popover.hover.top="$t('shows.editor_popover_undo')"
                                                                            @click="commands.undo"
                                                                        >
<!--                                                                            <b-icon-arrow-counterclockwise></b-icon-arrow-counterclockwise>-->
                                                                            <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="arrow counterclockwise" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-arrow-counterclockwise b-icon bi"><g data-v-c6b02ba4=""><path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"></path><path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"></path></g></svg>
                                                                        </b-button>

                                                                        <b-button
                                                                            variant="outline-secondary"
                                                                            v-b-popover.hover.top="$t('shows.editor_popover_redo')"
                                                                            @click="commands.redo"
                                                                        >
<!--                                                                            <b-icon-arrow-clockwise></b-icon-arrow-clockwise>-->
                                                                            <svg data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="arrow clockwise" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-arrow-clockwise b-icon bi"><g data-v-c6b02ba4=""><path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"></path><path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"></path></g></svg>
                                                                        </b-button>

                                                                        <b-button
                                                                            variant="outline-secondary"
                                                                            v-b-popover.hover.top="$t('shows.editor_popover_sourcecode')"
                                                                            @click.stop="switchSourceView"
                                                                        >
<!--                                                                            <b-icon-code></b-icon-code>-->
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
                                                                <b-textarea id="description" :state="getValidationState(validationContext)"
                                                                    v-show="showSource"
                                                                    rows="10"
                                                                    :placeholder="$t('shows.text_placeholder_description_show')"
                                                                    v-model="model.description"
                                                                    name="description"></b-textarea>
                                                            </b-overlay>
                                                        </div>
                                                    </div>
                                                    <b-form-invalid-feedback
                                                        style="display:block"
                                                        id="input-description-live-feedback">{{ validationContext.errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </validation-provider>
                                            </b-col>
                                            <b-col cols="1">
                                                <i class="icon icon-info-with-circle text-blue"
                                                   v-b-popover.hover.html="$t('shows.text_popover_description_show')"></i>
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                </b-row>

                                <b-row class="mb-3">
                                    <b-col cols="11">
                                        <label for="author">{{$t('shows.text_label_author_show')}}
                                            <span class="text-muted" :title="$t('shows.text_title_field_required')">*</span></label>
                                    </b-col>
                                    <b-col cols="12" lg="11">
                                        <b-row>
                                            <b-col cols="11">
                                                <validation-provider
                                                    vid="author"
                                                    :rules="{ required: true, max: 255 }"
                                                    v-slot="validationContext"
                                                    :name="$t('shows.text_label_author_show')"
                                                >
                                                    <b-overlay :show="isLoadingDefaults" rounded="sm">
                                                        <b-input
                                                            type="text"
                                                            maxlength="255"
                                                            id="author"
                                                            required
                                                            :placeholder="$t('shows.text_placeholder_author_show')"
                                                            v-model="model.author"
                                                            :state="getValidationState(validationContext)"
                                                            name="author"></b-input>
                                                    </b-overlay>
                                                    <b-form-invalid-feedback
                                                        id="input-author-live-feedback">{{ validationContext.errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </validation-provider>
                                            </b-col>
                                            <b-col cols="1">
                                                <i class="icon icon-info-with-circle text-blue"
                                                   v-b-popover.hover="$t('shows.text_popover_author_show')"></i>
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                </b-row>

                                <b-row class="mb-3">
                                    <b-col cols="12">
                                        <label for="show_media">{{$t('shows.text_label_media_show')}}</label>
                                    </b-col>
                                    <b-col cols="12" lg="11">
                                        <b-row v-show="!model.show_media">
                                            <b-col cols="11" md="9">
                                                <b-tabs content-class="mt-3">
                                                <b-tab :title="$t('shows.header_upload_file')">
                                                    <fileuploader
                                                        maxfiles="1"
                                                        acceptedFiles=".mp3,.aac,.m4a,.mov,.mp4,.pdf"
                                                        :guid="model.guid"
                                                        :feed="model.feed_id"
                                                        url="/media/upload/chunks"
                                                        :uploadText="$t('shows.upload_hint_show_media')"
                                                        :uploadTextExtra="$t('shows.upload_hint_extra_show_media')"></fileuploader>
                                                </b-tab>
                                                <b-tab :title="$t('shows.header_select_file')">
                                                    <b-col cols="11" class="mt-2">
                                                        <validation-provider
                                                            vid="show_media"
                                                            :rules="{ required: false }"
                                                            v-slot="validationContext"
                                                            :name="$t('shows.text_label_media_show')"
                                                        >
                                                            <file-selector
                                                                :placeholder="$t('shows.text_placeholder_show_media_file_selector')"
                                                                :selectLabel="$t('shows.text_selectLabel_show_media_file_selector')"
                                                                :deselectLabel="$t('shows.text_deselectLabel_show_media_file_selector')"
                                                                :selectedLabel="$t('shows.text_selectedLabel_show_media_file_selector')"
                                                                filter="type:enclosure"
                                                                limit="-1"
                                                            ></file-selector>
                                                            <b-form-invalid-feedback
                                                                id="input-show-media-live-feedback">{{ validationContext.errors[0] }}
                                                            </b-form-invalid-feedback>
                                                        </validation-provider>
                                                    </b-col>
                                                </b-tab>
                                                <b-tab :title="$t('shows.text_label_show_suggestions')">
                                                    <b-spinner
                                                        type="grow"
                                                        :label="$t('shows.label_loading_suggestions')"
                                                        v-show="isLoadingFileSuggestions"></b-spinner>

                                                    <b-row style="max-height: 165px;overflow: auto">
                                                        <b-col cols="12" v-for="file in fileSuggestions" :key="file.id">
                                                            <file-suggestion :file="file"></file-suggestion>
                                                        </b-col>
                                                    </b-row>
                                                </b-tab>
                                            </b-tabs>
                                            </b-col>
                                            <b-col cols="1">
                                                <i class="icon icon-info-with-circle text-blue"
                                                   v-b-popover.hover.html="$t('shows.text_popover_media_show')"></i>
                                            </b-col>
                                        </b-row>
                                        <b-overlay :show="isLoadingMedia" rounded="sm">
                                        <b-row v-if="model.show_media">
                                                <b-col cols="11" class="position-relative">
                                                    <b-button
                                                        variant="warning"
                                                        size="sm"
                                                        style="position:absolute;top:0;right:0"
                                                        v-b-popover.hover="$t('shows.text_popover_image_remove')"
                                                        @click="$emit('vdropzone-reset');resetSelectedFile()">&times;</b-button>
                                                </b-col>
                                                <b-col cols="10" lg="8" offset-lg="3">
                                                    <audio-play-button
                                                        v-if="media.type === 'audio'"
                                                        size="md"
                                                        :tooltip-title="$t('shows.popover_audioplayer_tooltip_title')"
                                                        :tooltip-content="$t('shows.popover_audioplayer_tooltip_content')"
                                                        :url="media.intern"
                                                        :duration="model.itunes.duration"
                                                        :filename="media.name"></audio-play-button>

                                                    <video-player-button
                                                        v-if="media.type === 'video'"
                                                        :url="media.intern"></video-player-button>
                                                </b-col>
                                                <b-col cols="12" lg="8" offset-lg="3" class="pt-2" style="word-wrap: break-word">
                                                    <b-input-group :append="media.size" size="sm">
                                                        <b-input readonly disabled :value="media.name" size="sm"></b-input>
                                                    </b-input-group>
                                                </b-col>
                                        </b-row>
                                        </b-overlay>
                                        <b-row v-if="showMetaDataHint">
                                            <b-col cols="12" class="pt-3">
                                                <b-spinner
                                                    type="grow"
                                                    :label="$t('shows.label_loading_suggestions')"
                                                    v-show="isLoadingMetaData"></b-spinner>
                                                <b-link @click="">{{ $t('shows.link_found_metadata') }}</b-link>
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                </b-row>

                                <b-row class="mb-3">
                                        <b-col cols="12" lg="11">
                                            <label for="link">{{ $t('shows.text_label_website_link') }}</label>
                                        </b-col>
                                        <b-col cols="12" lg="11">
                                            <b-row>
                                                <b-col cols="11">
                                                    <validation-provider
                                                        :name="$t('shows.text_label_website_link')"
                                                        vid="link"
                                                        :rules="{ required: false, min: 11, regex: /^(http:\/\/|https:\/\/)?[a-z0-9]+([\-.]{1}[a-z0-9]+)*\.[a-z]{2,}(:[0-9]{1,5})?(\/.*)?$/ }"
                                                        v-slot="validationContext"
                                                    >
                                                        <b-input
                                                            v-model="model.link"
                                                            :placeholder="$t('shows.text_placeholder_website_link')"
                                                            type="url"
                                                            id="link"
                                                            :state="getValidationState(validationContext)"
                                                            aria-describedby="input-link-live-feedback"
                                                        ></b-input>
                                                        <b-form-invalid-feedback
                                                            id="input-link-live-feedback">{{ validationContext.errors[0] }}
                                                        </b-form-invalid-feedback>
                                                    </validation-provider>
                                                </b-col>
                                                <b-col cols="1">
                                                    <i class="icon icon-info-with-circle text-blue"
                                                       v-b-popover.hover="$t('shows.text_popover_website_link')"></i>
                                                </b-col>
                                            </b-row>
                                        </b-col>
                                    </b-row>

                                <b-row class="mb-3">
                                        <b-col cols="12" lg="11">
                                            <label for="copyright">{{ $t('shows.text_label_copyright') }}</label>
                                        </b-col>
                                        <b-col cols="12" lg="11">
                                            <b-row>
                                                <b-col cols="11">
                                                    <validation-provider
                                                        :name="$t('shows.text_label_copyright')"
                                                        vid="copyright"
                                                        :rules="{ required: false }"
                                                        v-slot="validationContext"
                                                    >
                                                        <b-input
                                                            v-model="model.copyright"
                                                            :placeholder="$t('shows.text_placeholder_copyright')"
                                                            type="text"
                                                            name="copyright"
                                                            id="copyright"
                                                            :state="getValidationState(validationContext)"
                                                            aria-describedby="input-copyright-live-feedback"
                                                        ></b-input>
                                                        <b-form-invalid-feedback
                                                            id="input-copyright-live-feedback">{{ validationContext.errors[0] }}
                                                        </b-form-invalid-feedback>
                                                    </validation-provider>
                                                </b-col>
                                                <b-col cols="1">
                                                    <i class="icon icon-info-with-circle text-blue"
                                                       v-b-popover.hover="$t('shows.text_popover_copyright')"></i>
                                                </b-col>
                                            </b-row>
                                        </b-col>
                                    </b-row>

                                <b-row class="mb-3">
                                        <b-col sm="12" lg="11">
                                            <label for="guid">{{ $t('shows.text_label_guid') }}</label>
                                        </b-col>
                                        <b-col sm="12" lg="11">
                                            <b-row>
                                                <b-col cols="10">
                                                    <validation-provider
                                                        :name="$t('shows.text_label_guid')"
                                                        vid="guid"
                                                        :rules="{ required: false }"
                                                        v-slot="validationContext"
                                                    >
                                                        <b-overlay :show="isRenewingGuid" rounded="sm">
                                                            <b-input
                                                                v-model="model.guid"
                                                                :placeholder="$t('shows.text_placeholder_guid')"
                                                                type="text"
                                                                name="guid"
                                                                id="guid"
                                                                :state="getValidationState(validationContext)"
                                                                aria-describedby="input-guid-live-feedback"
                                                            ></b-input>
                                                        </b-overlay>
                                                        <b-form-invalid-feedback
                                                            id="input-guid-live-feedback">{{ validationContext.errors[0] }}
                                                        </b-form-invalid-feedback>
                                                    </validation-provider>
                                                </b-col>
                                                <b-col cols="1">
<!--                                                    <b-icon icon="arrow-repeat"
                                                            v-b-popover.hover.right="$t('shows.text_popover_renew_guid')"
                                                            @click="renewGuid"></b-icon>-->
                                                    <svg
                                                        v-b-popover.hover.right="$t('shows.text_popover_renew_guid')"
                                                        @click="renewGuid"
                                                        data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="arrow repeat" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-arrow-repeat b-icon bi"><g data-v-c6b02ba4=""><path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"></path><path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"></path></g></svg>
                                                </b-col>
                                                <b-col cols="1">
                                                    <i class="icon icon-info-with-circle text-blue"
                                                       v-b-popover.hover="$t('shows.text_popover_guid')"></i>
                                                </b-col>
                                            </b-row>
                                        </b-col>
                                    </b-row>
                            </b-col>
                        </b-row>
                    </b-col>

                    <b-col cols="12" xl="6" class="show-form">

                        <b-row class="mb-3">
                            <b-col cols="12">
                                <label>
                                    {{$t('shows.text_label_show_image')}}
                                </label>
                            </b-col>

                            <b-col cols="12" sm="5" md="5" lg="4" offset-lg="3" xl="4" v-if="logo">
                                <b-row>
                                    <b-col cols="12" style="position:relative">
                                        <b-button
                                            variant="warning"
                                            style="position:absolute;top:1em;right:2em"
                                            size="sm"
                                            v-b-popover.hover="$t('shows.text_popover_image_remove')"
                                            @click="$emit('vdropzone-reset');model.itunes.logo=null;logo=null;isvisible=true">&times;</b-button>
                                        <b-img
                                            thumbnail
                                            fluid
                                            :src="logo.intern"
                                            :alt="logo.name">
                                        </b-img>
                                    </b-col>
                                    <b-col cols="12" class="m-1">
                                        <b-input-group :append="logo.size" size="sm">
                                            <b-input readonly disabled :value="logo.name" size="sm"></b-input>
                                        </b-input-group>
                                    </b-col>
                                </b-row>
                            </b-col>

                            <b-col cols="12" v-if="!logo">
                                <b-row>
                                    <b-col cols="11" md="9">
                                        <b-tabs content-class="mt-3">
                                            <b-tab :title="$t('shows.header_upload_image')">
                                                <fileuploader
                                                    v-show="!model.itunes.logo"
                                                    maxfiles="1"
                                                    maxFilesize="1.1"
                                                    thumbnailWidth="225"
                                                    url="/beta/show/logo"
                                                    :feed="model.feed_id"
                                                    :guid="model.guid"
                                                    usage="logo"
                                                    acceptedFiles=".png,.jpg"
                                                    :uploadText="$t('shows.text_upload_hint_logo')"
                                                    :uploadTextExtra="$t('shows.text_upload_hint_logo_extra')">
                                                </fileuploader>
                                            </b-tab>
                                            <b-tab :title="$t('shows.header_select_image')">
                                                <validation-provider
                                                    vid="show_logos"
                                                    :rules="{ required: false }"
                                                    v-slot="validationContext"
                                                    :name="$t('shows.text_label_media_show')"
                                                >
                                                    <logo-selector
                                                        :placeholder="$t('shows.placeholder_select_image')"
                                                        :feed="model.feed_id"></logo-selector>
                                                    <b-form-invalid-feedback
                                                        id="input-show-logos-live-feedback">{{ validationContext.errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </validation-provider>
                                            </b-tab>
                                            <b-tab :title="$t('shows.text_label_show_suggestions')">
                                                <b-row>
                                                    <b-col cols="10" style="max-height:400px;overflow:auto">
                                                        <div v-show="isLoadingImageSuggestions" style="margin:15px">
                                                            <b-skeleton-img></b-skeleton-img>
                                                        </div>
                                                        <div v-for="image in imageSuggestions" :key="image.id">
                                                            <image-suggestion :image="image"></image-suggestion>
                                                        </div>
                                                        <div v-for="logo in logoSuggestions" :key="logo.id">
                                                            <image-suggestion :image="logo"></image-suggestion>
                                                        </div>
                                                    </b-col>
                                                </b-row>
                                            </b-tab>
                                        </b-tabs>
                                    </b-col>
                                    <b-col cols="1">
                                        <i class="icon icon-info-with-circle text-blue"
                                           v-b-popover.hover="$t('shows.text_popover_image_show')"></i>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>

                        <hr>

                        <h3>Itunes</h3>

                        <b-row class="mb-3">
                            <b-col sm="3">
                                <label for="title_itunes">{{$t('shows.text_label_title_show_itunes')}}</label>
                            </b-col>
                            <b-col sm="8">
                                <b-row>
                                    <b-col cols="11">
                                        <validation-provider
                                            vid="title_itunes"
                                            :rules="{ required: false, max: 255/*, is_not: model.title*/ }"
                                            v-slot="validationContext"
                                            :name="$t('shows.text_label_title_show_itunes')"
                                        >
                                            <b-input
                                                type="text"
                                                maxlength="255"
                                                id="title_itunes"
                                                :placeholder="$t('shows.text_placeholder_title_show_itunes')"
                                                v-model="model.itunes.title"
                                                :state="getValidationState(validationContext)"
                                                name="title_itunes"></b-input>
                                            <b-form-invalid-feedback
                                                id="input-title-itunes-live-feedback">{{ validationContext.errors[0] }}
                                            </b-form-invalid-feedback>
                                        </validation-provider>
                                    </b-col>
                                    <b-col cols="1">
                                        <i class="icon icon-info-with-circle text-blue"
                                           v-b-popover.hover="$t('shows.text_popover_title_show_itunes')"></i>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>

                        <b-row class="mb-3">
                            <b-col sm="3">
                                <label for="subtitle_itunes">{{$t('shows.text_label_subtitle_show_itunes')}}</label>
                            </b-col>
                            <b-col sm="8">
                                <b-row>
                                    <b-col cols="11">
                                        <validation-provider
                                            vid="subtitle_itunes"
                                            :rules="{ required: false, max: 255 }"
                                            v-slot="validationContext"
                                            :name="$t('shows.text_label_subtitle_show_itunes')"
                                        >
                                            <b-input
                                                type="text"
                                                maxlength="255"
                                                id="subtitle_itunes"
                                                :placeholder="$t('shows.text_placeholder_subtitle_show_itunes')"
                                                v-model="model.itunes.subtitle"
                                                :state="getValidationState(validationContext)"
                                                name="subtitle_itunes"></b-input>
                                            <b-form-invalid-feedback
                                                id="input-subtitle-itunes-live-feedback">{{ validationContext.errors[0] }}
                                            </b-form-invalid-feedback>
                                        </validation-provider>
                                    </b-col>
                                    <b-col cols="1">
                                        <i class="icon icon-info-with-circle text-blue"
                                           v-b-popover.hover="$t('shows.text_popover_subtitle_show_itunes')"></i>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>

                        <b-row class="mb-3">
                            <b-col sm="3">
                                <label for="itunessummary">{{ $t('shows.text_label_itunes_summary') }}</label>
                            </b-col>
                            <b-col sm="8">
                                <b-row>
                                    <b-col cols="11">
                                        <validation-provider
                                            :name="$t('shows.text_label_itunes_summary')"
                                            vid="itunessummary"
                                            :rules="{ required: false }"
                                            v-slot="validationContext"
                                        >
                                            <b-textarea
                                                v-model="model.itunes.summary"
                                                :placeholder="$t('shows.text_placeholder_itunes_summary')"
                                                name="itunessummary"
                                                id="itunessummary"
                                                rows="4"
                                                :state="getValidationState(validationContext)"
                                                aria-describedby="input-itunes-summary-live-feedback"
                                            ></b-textarea>
                                            <b-form-invalid-feedback
                                                id="input-itunes-summary-live-feedback">{{ validationContext.errors[0] }}
                                            </b-form-invalid-feedback>
                                        </validation-provider>
                                    </b-col>
                                    <b-col cols="1">
                                        <i class="icon icon-info-with-circle text-blue"
                                           v-b-popover.hover="$t('shows.text_popover_itunes_summary')"></i>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>

                        <b-row class="mb-3">
                            <b-col sm="3">
                                <label for="author">{{$t('shows.text_label_author_show')}}</label>
                            </b-col>
                            <b-col sm="8">
                                <b-row>
                                    <b-col cols="11">
                                        <validation-provider
                                            vid="author"
                                            :rules="{ required: false, max: 255 }"
                                            v-slot="validationContext"
                                            :name="$t('shows.text_label_author_show')"
                                        >
                                            <b-input
                                                type="text"
                                                maxlength="255"
                                                id="author"
                                                :placeholder="$t('shows.text_placeholder_author_show')"
                                                v-model="model.itunes.author"
                                                :state="getValidationState(validationContext)"
                                                name="author"></b-input>
                                            <b-form-invalid-feedback
                                                id="input-author-live-feedback">{{ validationContext.errors[0] }}
                                            </b-form-invalid-feedback>
                                        </validation-provider>
                                    </b-col>
                                    <b-col cols="1">
                                        <i class="icon icon-info-with-circle text-blue"
                                           v-b-popover.hover="$t('shows.text_popover_author_show')"></i>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>

                        <b-row class="mb-3">
                            <b-col cols="12" sm="12" md="12" lg="12" xl="12">
                                <b-row>
                                    <b-col sm="3">
                                        <label for="itunes_episode_type">{{$t('shows.text_label_itunes_episode_type')}}</label>
                                    </b-col>
                                    <b-col sm="8">
                                        <b-row>
                                            <b-col cols="11">
                                                <validation-provider
                                                    :name="$t('shows.text_label_itunes_episode_type')"
                                                    vid="itunes_episode_type"
                                                    :rules="{ required: true/*, oneOf:full,trailer,bonus*/ }"
                                                    v-slot="validationContext"
                                                >
                                                    <b-select
                                                        required
                                                        id="itunes_episode_type"
                                                        v-model="model.itunes.episodeType"
                                                        :options="episodeTypes"
                                                        :state="getValidationState(validationContext)"
                                                        name="episodeType"></b-select>
                                                    <b-form-invalid-feedback
                                                        id="input-episode-type-live-feedback">{{
                                                        validationContext.errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </validation-provider>
                                            </b-col>
                                            <b-col cols="1">
                                                <i class="icon icon-info-with-circle text-blue"
                                                   v-b-popover.hover="$t('shows.text_popover_itunes_episode_type')"></i>
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>
                        <b-row class="mb-3">
                            <b-col cols="12" sm="12" lg="6">
                                <b-row>
                                    <b-col cols="12" sm="3" lg="6">
                                        <label for="itunes_season">{{$t('shows.text_label_itunes_season')}}</label>
                                    </b-col>
                                    <b-col cols="11" sm="4" lg="5">
                                        <b-select
                                            id="itunes_season"
                                            v-model="model.itunes.season"
                                            :options="seasons"
                                            class=""
                                            name="season"></b-select>
                                    </b-col>
                                    <b-col cols="1" >
                                        <i class="icon icon-info-with-circle text-blue"
                                           v-b-popover.hover="$t('shows.text_popover_itunes_season')"></i>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col cols="12" sm="12" lg="5" offset-xl="1" class="mt-3 mt-sm-3 mt-lg-0">
                                <b-row>
                                    <b-col cols="12" sm="3" lg="3">
                                        <label for="itunes_episode">{{$t('shows.text_label_itunes_episode')}}</label>
                                    </b-col>
                                    <b-col cols="11" sm="4" lg="5">
                                        <b-select
                                            id="itunes_episode"
                                            v-model="model.itunes.episode"
                                            :options="episodes"
                                            name="episode"></b-select>
                                    </b-col>
                                    <b-col cols="1">
                                        <i class="icon icon-info-with-circle text-blue"
                                           v-b-popover.hover="$t('shows.text_popover_itunes_episode')"></i>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>

                        <b-row class="mb-3">
                            <b-col cols="12" sm="12" md="12" lg="12" xl="12">
                                <b-row>
                                    <b-col sm="3">
                                        <label for="itunes_episode_duration">{{$t('shows.text_label_itunes_episode_duration')}}</label>
                                    </b-col>
                                    <b-col sm="7" md="4">
                                        <b-row>
                                            <b-col cols="10" sm="4" md="8">
                                                <validation-provider
                                                    :name="$t('shows.text_label_itunes_episode_duration')"
                                                    vid="itunes_episode_duration"
                                                    :rules="{ required: false }"
                                                    v-slot="validationContext"
                                                >
                                                    <b-overlay :show="isRenewingDuration" rounded="sm">
                                                        <b-input
                                                            id="itunes_episode_duration"
                                                            v-model="model.itunes.duration"
                                                            :state="getValidationState(validationContext)"
                                                            :disabled="!model.show_media"
                                                            :placeholder="$t('shows.text_placeholder_itunes_episode_duration')"
                                                            name="episodeDuration"></b-input>
                                                    </b-overlay>
                                                    <b-form-invalid-feedback
                                                        id="input-episode-duration-live-feedback">{{ validationContext.errors[0] }}
                                                    </b-form-invalid-feedback>
                                                </validation-provider>
                                            </b-col>
                                            <b-col cols="1">
<!--                                                <b-icon
                                                    icon="arrow-repeat"
                                                    v-b-popover.hover.right="$t('shows.text_popover_renew_duration')"
                                                    v-show="model.show_media" @click="renewDuration"></b-icon>-->
                                                <svg
                                                    v-b-popover.hover.right="$t('shows.text_popover_renew_duration')"
                                                    v-show="model.show_media"
                                                    @click="renewDuration"
                                                    data-v-c6b02ba4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="arrow repeat" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-arrow-repeat b-icon bi" style=""><g data-v-c6b02ba4=""><path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"></path><path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"></path></g></svg>
                                            </b-col>
                                            <b-col cols="1">
                                                <i class="icon icon-info-with-circle text-blue"
                                                   v-b-popover.hover="$t('shows.text_popover_itunes_duration')"></i>
                                            </b-col>
                                        </b-row>
                                    </b-col>
                                    <b-col cols="12" sm="12" md="4" class="mt-4 mt-md-1 mt-lg-0">
                                        <b-form-checkbox
                                            id="checkbox-explicit"
                                            v-model="model.itunes.explicit"
                                            name="explicit"
                                            value="1"
                                            unchecked-value="0">
                                            {{ $t('shows.text_label_itunes_explicit') }}
                                        </b-form-checkbox>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>
                        <!--
                                                <b-row class="mb-3">
                                                    <b-col cols="12" sm="12" lg="3" offset-lg="3">
                                                        <b-form-checkbox
                                                            id="checkbox-explicit"
                                                            v-model="model.itunes.explicit"
                                                            name="explicit"
                                                            value="1"
                                                           hecked-value="0">
                                                            {{ $t('shows.text_label_itunes_explicit') }}
                                                        </b-form-checkbox>
                                                    </b-col>

                                                    <b-col cols="12" sm="12" lg="3" offset-lg="1">
                                                        <b-form-checkbox
                                                            id="checkbox-cc"
                                                            v-model="model.itunes.isclosedcaptioned"
                                                            name="cc"
                                                            value="yes"
                                                            unchecked-value="">
                                                            {{ $t('shows.text_label_itunes_is_closed_captioned') }}
                                                        </b-form-checkbox>
                                                    </b-col>
                                                </b-row>-->
                    </b-col>
                </b-row>

                <hr>

                <b-row class="mb-3">
                    <b-col cols="12" class="mb-3">
                        <b-row>
                            <b-col cols="12" xl="3">
                                <label for="publishingDate">{{$t('shows.text_label_publishing_date_show')}}
                                    <span class="text-muted" :title="$t('shows.text_title_field_required')">*</span></label>
                            </b-col>
                            <b-col cols="11" xl="5" class="mb-2">
                                <b-datepicker
                                    id="publishingDate"
                                    v-model="model.publishing_date"
                                    :min="minDate"
                                    :max="maxDate"
                                    required
                                    :locale="locale"
                                    v-bind="labels[locale] || {}"
                                ><!--TODO: I18N--></b-datepicker>
                            </b-col>
                            <b-col cols="11" xl="2">
                                <b-form-timepicker
                                    v-model="model.publishing_time"
                                    :placeholder="$t('shows.text_placeholder_show_publishing_time')"
                                    now-button
                                    required
                                    :label-close-button="$t('shows.text_label_close_button')"
                                    :label-now-button="$t('shows.text_label_now_button')"
                                    :label-no-time-selected="$t('shows.label_no_time_selected')"
                                    locale="de"></b-form-timepicker>
                            </b-col>
                        </b-row>
                    </b-col>
                    <b-col cols="12">
                        <b-row>
                            <b-col cols="12" xl="3">
                                <label for="show-state">{{$t('shows.text_label_state_show')}}
                                    <span class="text-muted" :title="$t('shows.text_title_field_required')">*</span></label>
                            </b-col>
                            <b-col cols="12" xl="8">
                                <b-row>
                                    <b-col cols="11" lg="5">
                                        <b-form-group v-slot="{ ariaDescribedby }">
                                            <b-form-radio-group
                                                id="radio-group-1"
                                                v-model="model.is_public"
                                                :options="getStates"
                                                size="lg"
                                                :aria-describedby="$t('shows.aria_state_of_show')"
                                                name="radio-options"
                                            ></b-form-radio-group>
                                        </b-form-group>
                                    </b-col>
                                    <b-col cols="1">
                                        <i class="icon icon-info-with-circle text-blue"
                                           v-b-popover.hover="$t('shows.text_popover_state_show')"></i>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>

                <b-row>
                    <b-col cols="12">
                        <div class="float-right">
                            <b-overlay :show="isLoading" rounded="sm">
                                <b-link
                                    class="mr-2"
                                    :to="'/podcast/' + feedId">{{ $t('shows.link_cancel') }}</b-link>
                                <b-button
                                    variant="outline-secondary"
                                    v-b-toggle.sidebar-1>{{$t('shows.text_button_preview')}}
                                </b-button>
<!--                                <b-dropdown
                                    variant="primary"
                                    :disabled="isLoading"
                                    split
                                    @click="submit"
                                    :text="$t('shows.text_button_add_show')"
                                    class="m-2">
                                    <b-dropdown-item href="#" @click.stop="saveAndEdit" :title="$t('shows.title_button_save_and_edit_show')">{{$t('shows.text_button_save_and_edit_show')}}</b-dropdown-item>
                                    <b-dropdown-item href="#" @click.stop="addAndShare" :title="$t('shows.title_button_add_and_share_show')">{{$t('shows.text_button_add_and_share_show')}}</b-dropdown-item>
                                    <b-dropdown-item href="#" :title="$t('shows.title_button_add_and_create_show')">{{$t('shows.text_button_add_and_create_show')}}</b-dropdown-item>
&lt;!&ndash;                                    <b-dropdown-item href="#" :title="$t('shows.title_button_add_and_duplicate_show')">{{$t('shows.text_button_add_and_duplicate_show')}}</b-dropdown-item>&ndash;&gt;
                                </b-dropdown>-->
                                <b-button
                                    variant="primary"
                                    :disabled="isLoading"
                                    type="submit">{{saveButtonLabel()}}
                                </b-button>
                            </b-overlay>
                        </div>
                    </b-col>
                </b-row>
            </b-form>
            </b-overlay>
        </validation-observer>

        <b-sidebar id="sidebar-1" title="Vorschau" shadow right width="40em">
            <div class="px-3 py-2">
                <b-card :img-src="logo.intern" :img-alt="logo.name">
                    <b-card-body>
                        <b-card-header>
                            <span class="float-right font-weight-light" v-html="model.itunes.author"></span>
                            <b-card-title>{{ model.title }}</b-card-title>
                            <b-card-sub-title v-show="model.itunes.subtitle">{{ model.itunes.subtitle }}</b-card-sub-title>
                        </b-card-header>

                        <b-card-body v-show="media">
                                <audio-play-button
                                    v-if="this.media && this.media.type === 'audio'"
                                    size="md"
                                    :tooltip-title="$t('shows.popover_audioplayer_tooltip_title')"
                                    :tooltip-content="$t('shows.popover_audioplayer_tooltip_content')"
                                    :url="this.media.intern"
                                    :filename="this.media.name"
                                    :duration="model.itunes.duration"></audio-play-button>

                                <video-player-button
                                    v-if="this.media && this.media.type === 'video'"
                                    :url="this.media.intern"></video-player-button>
                        </b-card-body>

                        <b-card-body v-show="model.description">
                            <b-card-text>
                                <span v-html="model.description"></span>
                            </b-card-text>
                        </b-card-body>

                        <b-card-body v-show="this.model.link">
                            <b-link :href="this.model.link" class="card-link">{{ $t('shows.text_link_to_website') }}</b-link>
                        </b-card-body>

                        <b-card-footer v-show="this.model.copyright" class="text-muted text-monospace">
                            <span v-html="model.copyright"></span>
                        </b-card-footer>
                    </b-card-body>
                </b-card>
            </div>
        </b-sidebar>
    </div>
</template>

<script>
    import {eventHub} from '../../app';
    import { ValidationProvider, ValidationObserver, extend, localize } from 'vee-validate';
    import * as rules from "vee-validate/dist/rules";
    import de from 'vee-validate/dist/locale/de.json';
    import FileSelector from "../media/FileSelector";
    import LogoSelector from "../media/LogoSelector";
    import FileSuggestion from "./FileSuggestion";
    import ImageSuggestion from "./ImageSuggestion";
    import AudioPlayButton from "../AudioPlayButton";
    import VideoPlayerButton from "../VideoPlayerButton";
    import { Editor, EditorContent, EditorMenuBar } from 'tiptap';
    import { Bold, Italic, Strike, Underline, Link, HardBreak, Heading, History, HorizontalRule, OrderedList, BulletList, ListItem, Blockquote } from 'tiptap-extensions';
    import FileUploader from "../media/FileUploader";
    localize('de', de);
    Object.keys(rules).forEach(rule => {
        extend(rule, rules[rule]);
    });
    function getMonth(n){++n; return n<10? '0'+n:''+n;}

    const check = function(model, msg) {
        if (model.is_public !== 0 && !model.show_media) {
            return confirm(msg);
        }
        return true;
    }

    export default {
        name: "AddShow",

        components: {
            FileSelector,
            LogoSelector,
            FileSuggestion,
            ImageSuggestion,
            ValidationProvider,
            ValidationObserver,
            rules,
            EditorContent,
            EditorMenuBar,
            FileUploader,
            AudioPlayButton,
            VideoPlayerButton,
        },

        props: {
            feeds: {
                required: true,
                type: Array
            },
            feedId: {
                required: true,
                type: String
            },
            guid: {
                required: false,
                type: String,
                default: 'pod-' + Math.random().toString(16).slice(2) + Math.random().toString(16).slice(2)
            },
            canSchedulePosts: {
                default: false,
                type: Boolean
            },
            countShows: {
                default: 1,
                type: Number
            }
        },

        data () {
            const now = new Date();
            const today = now.getFullYear() + '-' + getMonth(now.getMonth()) + '-' + now.getDate();
            const timeNow = now.getHours() + ':' + now.getMinutes() + ':00';
            // 15th two months prior
            const minDate = new Date(2004, 1, 1);
            // 15th in two months
            const maxDate = now;

            if (this.canSchedulePosts) {
                maxDate.setMonth(maxDate.getMonth() + 54);
            }

            let episodeTypeFull = this.$t('shows.text_option_episode_type_full');
            let episodeTypeTrailer = this.$t('shows.text_option_episode_type_trailer');
            let episodeTypeBonus = this.$t('shows.text_option_episode_type_bonus');

            let seasons = [];
            seasons.push({ value: null, text: '-' });
            for (let i = 1; i <= 100; i++) {
                seasons.push({ value: i, text: '' + i });
            }

            return {
                model: {
                    feed_id: null,
                    guid: null,
                    title: null,
                    description: null,
                    itunes: {
                        title: null,
                        subtitle: null,
                        summary: null,
                        explicit: 0,
                        episodeType: 'full',
                        season: null,
                        logo: null,
                        duration: null,
                        isclosedcaptioned: null,
                        author: null,
                    },
                    show_media: null,
                    //author: this.feed.rss.email + ' (' + this.feed.rss.author + ')',
                    author: null,
                    publishing_date: today,
                    publishing_time: timeNow,
                    is_public: 1,
                    link: null,
                },
                minDate: minDate,
                maxDate: maxDate,
                medias: [],
                episodeTypes: [
                    { value: 'full', text: episodeTypeFull },
                    { value: 'trailer', text: episodeTypeTrailer },
                    { value: 'bonus', text: episodeTypeBonus },
                ],
                seasons: seasons,
                episodes: [],
                isLoading: false,
                isRenewingGuid: false,
                isRenewingDuration: false,
                fileSuggestions: [],
                imageSuggestions: [],
                logoSuggestions: [],
                logo: false,
                media: false,
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
                isLoadingFileSuggestions: false,
                isLoadingImageSuggestions: true,
                isLoadingDefaults: false,
                isLoadingMedia: false,
                emitAfterOnUpdate: false,
                fsvisible: true,
                isvisible: true,
                linkUrl: null,
                linkMenuIsActive: false,
                locale: 'de',
                labels: {
                    de: {
                        labelPrevDecade: 'Vorheriges Jahrzehnt',
                        labelPrevYear: 'Vorheriges Jahr',
                        labelPrevMonth: 'Vorheriger Monat',
                        labelCurrentMonth: 'Aktueller Monat',
                        labelNextMonth: 'Nächster Monat',
                        labelNextYear: 'Nächstes Jahr',
                        labelNextDecade: 'Nächstes Jahrzehnt',
                        labelToday: 'Heute',
                        labelSelected: 'Ausgewähltes Datum',
                        labelNoDateSelected: 'Kein Datum gewählt',
                        labelCalendar: 'Kalender',
                        labelNav: 'Kalendernavigation',
                        labelHelp: 'Mit den Pfeiltasten durch den Kalender navigieren'
                    }
                },
                showMetaDataHint: false,
                isLoadingMetaData: false,
                showOverlay: false,
                showSource: false,
                feed: null,
                numberShows: 0,
            }
        },

        mounted() {
            this.model.feed_id = this.feedId;
            this.model.guid = this.guid;
            this.numberShows = this.countShows;
            this.init();
        },

        methods: {
            submit : function() {
                this.onSubmit();
            },
            saveAndEdit : function() {
                this.onSubmit();
            },
            addAndShare : function() {
                let goto = '/beta/podcast/' + this.feedId + '/episode/' + this.model.guid + '/share';
                this.onSubmit(goto);
            },
            getValidationState({ dirty, validated, valid = null }) {
                return dirty || validated ? valid : null;
            },
            fillItunesTitle() {
                if (!this.model.itunes.title) {
                    this.model.itunes.title = this.model.title;
                }
            },
            onSubmit(goto) {
                window.scrollTo(0,275);
                let _goto = goto || '/beta/podcast/' + this.feedId + '/episoden';

                if (!this.model.description) {
                    this.showSource = false;
                    this.$refs.observer.setErrors({ description: [ this.$t('shows.error_message_fill_description') ] });
                    return false;
                }
                if (check(this.model, this.$t('shows.confirm_publishing'))) {
                    this.isLoading = this.showOverlay = true;
                    if (this.isEditing) {
                        axios.put('/api/feed/' + this.feedId + '/show/' + this.guid, this.model)
                            .then((response) => {
                                this.showMessage(response);
                                window.location.href = _goto;
                            })
                            .catch((error) => {
                                this.$refs.observer.setErrors(error.response.data.errors);
                            }).then(() => {
                                this.isLoading = false;
                            });
                    } else {
                        axios.post('/beta/shows', this.model)
                            .then((response) => {
                                this.showMessage(response);
                                window.location.href = _goto;
                            })
                            .catch((error) => {
                                this.$refs.observer.setErrors(error.response.data.errors);
                            }).then(() => {
                            this.isLoading = false;
                        });
                    }
                }
            },
            maxShows() {
                return this.numberShows + 1;
            },
            renewGuid() {
                this.isRenewingGuid = true;
                axios.get('/beta/show/guid')
                    .then((response) => {
                        this.model.guid = response.data;
                    })
                    .catch((error) => {
                        this.$refs.observer.setErrors(error.response.data.errors);
                    }).then(() => {
                        this.isRenewingGuid = false;
                    });
            },
            renewDuration() {
                this.isRenewingDuration = true;
                axios.post('/beta/show/duration', { id: this.model.show_media })
                    .then((response) => {
                        this.model.itunes.duration = response.data;
                    })
                    .catch((error) => {
                        this.$refs.observer.setErrors(error.response.data.errors);
                    }).then(() => {
                        this.isRenewingDuration = false;
                    });
            },
            getMetaData() {
                this.isLoadingMetaData = true;
                axios.post('/beta/show/metadata', { id: this.model.show_media })
                    .then((response) => {
                        if (response.data) {
                            this.showMetaDataHint = true;
                        }
                    })
                    .catch((error) => {
                        this.$refs.observer.setErrors(error.response.data.errors);
                    }).then(() => {
                        this.isLoadingMetaData = false;
                    });
            },
            init() {
                // If this (sub)page is called from parent
                // feeds is (pre-)filled
                // but if the page is called directly
                // we have the fallback in watch
                // and have to wait til feeds gets filled from its parent
                if (this.feeds && this.feeds.length > 0) {
                    this.updatePageInfo();
                }

                if (this.$route.query.media) {
                    this.getFile(this.$route.query.media, 'media');
                }

                if (this.isEditing) {
                    this.getShow(this.guid);
                } else {
                    this.isLoadingDefaults = true;
                    this.getDefaults();
                }
                this.getFileSuggestions();
                this.getImageSuggestions();
                eventHub.$on("show:image:set", image => {
                    this.logo = image;
                    this.model.itunes.logo = image.id;
                    this.isvisible = false;
                });
                eventHub.$on("file:selected", option => {
                    if (typeof option !== 'undefined'
                        && typeof option.type !== 'undefined'
                        && option.type !== 'logo') {
                        this.media = option;
                        this.model.show_media = option.id;
                        this.renewDuration();
                        //this.getMetaData();
                        eventHub.$emit('fileselector:changed', option);
                    }
                });
                this.$on("editor:input", content => {
                    this.model.description = content;
                });
                eventHub.$on("update:content:" + this.feedId, (type, content) => {
                    if (type === 'showlogo') {
                        eventHub.$emit('show:image:set', content);
                    }
                });
            },
            getDefaults() {
                axios.get('/api/feeds/' + this.feedId + '/defaults')
                    .then((response) => {
                        let defaults = response.data;
                        this.model.title = defaults.default_title;
                        this.model.description = defaults.default_description;
                        this.model.author = defaults.default_author;
                    })
                    .catch((error) => {
                    }).then(() => {
                        this.isLoadingDefaults = false;
                    });
            },
            getFileSuggestions() {
                this.isLoadingFileSuggestions = true;
                axios.get('/beta/suggestion/files?limit=5')
                    .then((response) => {
                        this.fileSuggestions = response.data.items;
                    })
                    .catch((error) => {
                    }).then(() => {
                        this.isLoadingFileSuggestions = false;
                    });
            },
            getImageSuggestions() {
                this.isLoadingImageSuggestions = true;
                axios.get('/beta/suggestion/images?feedId=' + this.feedId)
                    .then((response) => {
                        this.imageSuggestions = response.data.images.items;
                        this.logoSuggestions = response.data.logos.items;
                    })
                    .catch((error) => {
                    }).then(() => {
                        this.isLoadingImageSuggestions = false;
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
            resetSelectedFile() {
                this.model.show_media=null;
                this.model.itunes.duration=null;
                this.media=null;
                eventHub.$emit('fileselector:reset');
            },
            switchSourceView() {
                this.showSource = !this.showSource;

                if (!this.showSource) {
                    this.editor.setContent(this.model.description);
                }
            },
            emitPageInfo() {
                let items = [{
                    text: this.feed.attributes.title,
                    href: '#/podcast/' + this.feedId,
                },{
                    text: this.$t('nav.shows'),
                    href: '#/podcast/' + this.feedId + '/episoden',
                }];

                let page = {
                    header: this.$t('shows.header_add_show'),
                    subheader: this.$t('shows.subheader_add_show', {title: this.feed.attributes.title}),
                }

                if (this.isEditing) {
                    items.push({
                        text: this.$t('nav.shows_edit', {title: this.model.title}),
                        href: '#/podcast/' + this.feedId + '/episode/' + this.guid,
                    });

                    page = {
                        header: this.$t('shows.header_edit_show'),
                        subheader: this.$t('shows.subheader_edit_show', {'title': this.model.title, 'feed': this.feed.attributes.title}),
                    };
                } else {
                    items.push({
                        text: this.$t('nav.shows_add'),
                        href: '#/podcast/' + this.feedId + '/episode',
                    });
                }
                eventHub.$emit('podcasts:page:infos', items, page);
            },
            getFeedFromFeeds() {
                let _feedId = this.feedId;
                return this.feeds.filter(function (feed) {
                    return feed.id === _feedId;
                })
            },
            updatePageInfo() {
                this.feed = this.getFeedFromFeeds().shift();
                this.numberShows = this.feed.shows_count;
                this.emitPageInfo();
            },
            getShow(guid) {
                axios.get('/api/feed/' + this.feedId + '/show/' + guid)
                    .then((response) => {
                        let sm = response.data.data.attributes.show_media;
                        response.data.data.attributes.show_media = null;
                        this.$data.model = response.data.data.attributes;
                        this.$data.model.feed_id = response.data.data.feed_id;
                        this.editor.setContent(this.model.description);

                        if (sm) {
                            this.isLoadingMedia = true;
                            this.getFile(sm, 'media');
                        }

                        if (response.data.data.attributes.itunes.logo) {
                            this.getFile(response.data.data.attributes.itunes.logo);
                        }
                    })
                    .catch((error) => {
                        this.showError(error);
                    }).then(() => {
                });
            },
            getFile(id, type) {
                axios.get('/api/media/' + id)
                    .then((response) => {
                        response.data.intern = window.location.protocol + '//' + window.location.hostname + '/mediathek/' + id;
                        if (type === 'media') {
                            this.media = response.data;
                            this.$data.model.show_media = id;
                        } else {
                            this.logo = response.data;
                        }
                    })
                    .catch((error) => {
                        this.showError(error);
                    }).then(() => {
                        if (type === 'media') {
                            this.isLoadingMedia = false;
                        }
                });
            },
            saveButtonLabel() {
                if (this.isEditing) {
                    return this.$t('shows.text_button_update_show');
                } else {
                    return this.$t('shows.text_button_add_show');
                }
            },
        },

        watch: {
            model: {
                handler(val){
                    if (this.model.is_public !== 0) {
                        var _date = new Date();
                        _date.setSeconds(0);
                        _date.setMilliseconds(0);
                        var date = new Date(val.publishing_date + ' ' + val.publishing_time);

                        if (this.canSchedulePosts && this.model.is_public !== 0) {
                            if (date > _date) {
                                this.model.is_public = 2;
                            }
                        }

                        if  (date < _date) {
                            this.model.is_public = -1;
                        }
                    }
                },
                deep: true
            },
            feeds: function() {
                this.updatePageInfo();
            },
            numberShows: function() {
                let episodes = [];
                episodes.push({ value: null, text: '-' });
                for (let i = 1; i <= this.maxShows(); i++) {
                    episodes.push({ value: i, text: '' + i });
                }
                this.episodes = episodes;
            }
        },

        computed: {
            getStates() {
                let draft = this.$t('shows.text_option_state_draft');
                let published = this.$t('shows.text_option_state_published');
                let backdated = this.$t('shows.text_option_state_backdated');
                let scheduled = this.$t('shows.text_option_state_scheduled');

                if (!this.canSchedulePosts) {
                    scheduled += ' ' + this.$t('shows.text_option_scheduled_upgrade_package_hint');
                }

                switch (this.model.is_public) {
                    case -1:
                        return [
                            {value: 0, text: draft},
                            {value: -1, text: backdated},
                        ];
                    case 2:
                        return [
                            {value: 0, text: draft},
                            {value: 2, text: scheduled},
                        ];
                    default:
                    case 0:
                    case 1:
                        return [
                            {value: 0, text: draft},
                            {value: 1, text: published},
                        ];
                }
            },
            sourceCodeLabel() {
                return (this.showSource ? this.$t('shows.label_wysiwyg') : this.$t('shows.label_source_code'));
            },
            isEditing() {
                return this.$route.name === 'edit-show';
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
</style>

<!--
There is an @update event on the editor component where you can grab the current HTML.
Then you could add a toggle button and render this HTML in a textarea.
And on change you could set the new content with this.$refs.editor.setContent('<div>your html</div>')
-->
