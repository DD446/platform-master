<template>
    <b-modal
        ref="auphonicModal"
        :title="$t('auphonic.title_modal')"
        :ok-title="$t('auphonic.ok_title_modal')"
        :cancel-title="$t('auphonic.cancel_title_modal')"
        @ok="handleOk"
        lazy>
        <alert-container></alert-container>

        <b-overlay :show="isLoading" rounded="lg" class="p-5">
            <b-form
                    ref="form"
                    method="get"
                    @submit.stop.prevent="handleSubmit">
                <b-select
                    required
                    v-model="preset"
                    :options="presets"
                    class="mb-3"
                    v-if="hasPresets"
                    :disabled="isLoading"></b-select>
                <b-link
                    v-else
                    href="https://auphonic.com/engine/presets/"
                    target="_blank">{{ $t('auphonic.create_preset') }}</b-link>
            </b-form>
        </b-overlay>

<!--        <b-form action="/auphonic/produktion/starten" ref="nextForm" method="post">
            <input type="hidden" name="frmFeedId" :value="feedId">
            <input type="hidden" name="frmPreset" :value="preset">
        </b-form>-->
    </b-modal>
</template>

<script>
import {eventHub} from "../../../app";

export default {
    name: "AuphonicModal",

    data() {
        return {
            preset: null,
            presets: [
                { value: null, disabled: true, text: this.$t('auphonic.option_choose_preset') },
            ],
            isLoading: true,
            hasPresets: false
        }
    },

    props: {
        feedId: {
            type: String,
            required: true
        }
    },

    methods: {
        show() {
            this.$refs.auphonicModal.show();

            if (this.presets.length === 1) {
                this.getPresets();
            }
        },
        getPresets() {
            this.isLoading = true;
            axios.get('/api/auphonic/presets' + '?feed_id=' + this.feedId)
                .then((response) => {
                    let res = response.data.data || {};
                    let _this = this;
                    let first = false;

                    res.forEach(function(element) {
                        _this.presets.push({
                            value: element.id, text: element.name
                        })
                        if (!first) {
                            first = true;
                            _this.preset = element.id;
                        }
                        _this.hasPresets = true;
                    });
                }).catch(error => {
                    this.showError(error);
                }).then(() => {
                    this.isLoading = false;
                });
        },
        handleOk(bvModalEvt) {
            // Prevent modal from closing
            bvModalEvt.preventDefault();
            this.handleSubmit();
        },
        handleSubmit() {
            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return
            }
            // Hide the modal manually
            this.$nextTick(() => {
                // if (this.preset === 'bNHrTHBkyE77YoJnKHpR6B') {
                    this.$router.push({ path: '/podcast/' + this.feedId + '/auphonic/' + this.preset });
                // } else {
                //     this.$refs.nextForm.submit();
                // }
            })
        },
        checkFormValidity() {
            return this.$refs.form.checkValidity() && this.hasPresets;
        },
    },

    computed: {
    },

    mounted() {
        eventHub.$on("auphonic-modal:show:" + this.feedId, () => {
            this.show();
        });
    },
}
</script>

<style scoped>
</style>
