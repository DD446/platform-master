<template>
    <b-overlay
        spinner-small
        :show="isUpdating"
    >
        <div
            style="min-height: 1.5em;white-space: break-spaces;max-height: 10em"
            contenteditable="true"
            ref="editContainer"
            @blur="update"
            :title="title"
            @click="clearPlaceholder"
            :content="text"
        >
<!--            v-b-popover.hover.left="$t('feeds.title_editable')"-->
            <span :class="placeholderClass" v-if="!text" v-html="placeholder"></span>{{ text }}</div>
        <div class="text-danger small" v-show="hasError">{{lengthText}}/{{lengthMax}} {{$t('feeds.text_editable_length_hint_chars')}}</div>
    </b-overlay>
</template>

<script>
    import {eventHub} from '../app';

    export default {
        name: "Editable",

        data() {
            return {
                placeholderClass: 'text-warning',
                isUpdating: false,
                hasError: false,
                text: '',
            }
        },

        props:[
            'title',
            'content',
            'feed',
            'type',
            'placeholder',
            'guid'
        ],

        mounted: function() {
            if (this.content) {
                this.text = this.content;
            }

            if (['title', 'description', 'showTitle'].includes(this.type)) {
                this.placeholderClass = 'text-danger';
            }
            eventHub.$on('content:update:finished:' + this.feed, () => {
                this.isUpdating = false;
            });
        },

        methods: {
            update: function(event) {
                this.text = event.target.innerText;
                let txt = this.text;
                let singleLine = ['title', 'subtitle', 'showTitle'];

                if (singleLine.includes(this.type)) {
                    // Strip line breaks
                    txt = txt.replace(/\n/g, ' ');
                }

                if (['title', 'description', 'showTitle'].includes(this.type)) {
                    if (txt.length < 1) {
                        this.showError({message: this.$t('feeds.text_error_entity_required')});
                        this.hasError = true;
                        return false;
                    }
                }

                if (singleLine.includes(this.type)) {
                    if (txt.length > 255) {
                        this.showError({message: this.$t('feeds.text_error_entity_too_long', {max: 255})});
                        this.hasError = true;
                        return false;
                    }
                }

                if (['description'].includes(this.type)) {
                    if (txt.length > 4000) {
                        this.showError({message: this.$t('feeds.text_error_entity_too_long', {max: 4000})});
                        this.hasError = true;
                        return false;
                    }
                }

                eventHub.$emit('update:content:' + this.feed, this.type, txt, this.guid);
                this.isUpdating = true;
            },
            clearPlaceholder: function(event) {
                if (!this.text) {
                    event.target.innerText = '';
                    this.placeholderClass = '';
                }
                this.hasError = false;
            }
        },

        computed: {
            lengthText() {
                return this.text.length;
            },
            lengthMax() {
                switch (this.type) {
                    case 'description':
                        return 4000;
                    default:
                        return 255;
                }
            }
        },

        watch: {
        }
    }
</script>

<style scoped>

</style>
