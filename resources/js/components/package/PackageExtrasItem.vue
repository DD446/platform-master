<template>
    <div class="media align-items-center">
<!--                            <a href="#" class="mr-4">
                                <img alt="Image" src="assets/img/graphic-product-sidekick-thumb.jpg" class="rounded avatar avatar-lg" />
                            </a>-->
        <div class="media-body">
            <div class="d-flex justify-content-between mb-2">
                <div>
                    <a href="#" class="mb-1">
                        <h4>{{this.item.extras_description}}</h4>
                    </a>
                    <span v-if="this.item.is_repeating">
                        {{$t('package.text_extra_is_repeating')}}
                    </span>
                    <span v-if="!this.item.is_repeating && ![2, 5].includes(this.item.extras_type)">
                        {{$t('package.text_extra_is_ending', { end: this.item.date_end_formatted })}}
                    </span>
                </div>
                <div v-if="this.item.is_repeating">
                    <b-link @click.prevent="cancelExtra()"><i class="icon-circle-with-minus text-danger"></i> {{$t('package.link_cancel_extra')}}</b-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {eventHub} from "../../app";

    export default {
        name: "PackageExtrasItem",

        props: {
            item: Object
        },

        methods: {
            cancelExtra(id) {
                if (confirm(this.$t('package.text_confirm_cancel_extra'))) {
                    axios.put('/pakete/extras/' + this.item.extras_id)
                        .then((response) => {
                            eventHub.$emit('show-message:success', response.data);
                            this.item.is_repeating = false;
                        })
                        .catch(error => {
                            eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
                        });
                }
            }
        }
    }
</script>

<style scoped>

</style>
