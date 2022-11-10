<template>
    <div class="position-relative" style="margin:15px 15px 0 15px"
         @mouseenter="showButton"
         @mouseleave="hideButton">
        <b-img-lazy
            :id="'popover-target-' + image.id"
            thumbnail
            fluid-grow
            @dblclick="setImage"
            :src="image.intern"
            :title="image.name"
            :alt="image.name"></b-img-lazy>
            <b-button
                v-show="isActive"
                size="sm"
                variant="primary"
                @click="setImage"
                class="imageSelectButton">Auswählen</b-button>
<!--        <b-popover :target="'popover-target-' + image.id" triggers="hover" placement="top">
            <template v-slot:title>Popover Title</template>
            I am popover
        </b-popover>-->
    </div>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "ImageSuggestion",

        data: function () {
            return {
                isActive: false
            }
        },

        props: {
            image: {
                required: true,
                type: Object
            },
        },

        methods: {
            showButton() {
                this.isActive = true;
            },
            hideButton() {
                this.isActive = false;
            },
            setImage() {
                eventHub.$emit('show:image:set', this.image);
            }
        },

        created() {
            this.isActive = this.isMobile();
        }
    }
</script>

<style scoped>
    .imageSelectButton {
        position:absolute;
        right:0.5rem;
        bottom:0.5rem;
    }
</style>
