<template>
    <div class="row justify-content-center mb-5">
        <div class="col">
            <div class="card card-sm">
                <div class="card-header d-flex bg-secondary justify-content-between align-items-center">
                    <div>
                        <h5>{{$t('package.header_extras_booking_form')}}</h5>
                    </div>
                </div>

                <div class="card-body m-3">
                    <b-form>
                        <b-select
                            v-model="type"
                            :options="options"
                            class="mb-3"
                            v-on:change="getSelectedItem">
                            <template v-slot:first>
                                <option :value="null" disabled>{{$t('package.text_option_extra_default')}}</option>
                            </template>
                        </b-select>

                        <b-select
                            v-model="amount"
                            :options="details"
                            v-show="type"
                            class="mb-3">
                            <template v-slot:first>
                                <option :value="null" disabled>{{$t('package.text_option_detail_default')}}</option>
                            </template>
                        </b-select>

                        <b-form-group label="">
                            <b-form-radio-group
                                id="btn-radios-2"
                                v-model="repeating"
                                :options="getOccurence()"
                                buttons
                                button-variant="outline-primary"
                                size="lg"
                                name="radio-btn-outline"
                                v-show="amount"
                            ></b-form-radio-group>
                        </b-form-group>

                        <b-button
                            variant="success"
                            @click="addExtras"
                            v-show="amount"
                        >{{$t('package.text_button_extra_order', { cost: amount })}}</b-button>
                    </b-form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {eventHub} from "../../app";

    export default {
        name: "PackageBooking",

        data() {
            return {
                type: null,
                amount: null,
                repeating: true,
                options: this.getOptions(),
                details: [],
                extrasOnce: [
                    'storage',
                    'statsexport',
                ]
            }
        },
        methods: {
            getSelectedItem: function (type) {
                let max = 100;
                let piece = 1;

                switch (type) {
                    case 'storage':
                        max = 100;
                        piece = 50;
                        break;
                    case 'playerconfiguration':
                        max = 100;
                        piece = 10;
                        break;
                    case 'statsexport':
                        max = 100;
                        piece = 5; // TODO: Pull pieces from user extra model
                        break;
                }

                let _d = [];
                for (let i = 1; i <= max; i++) {
                    let it = {
                        value: i,
                        text: this.$t('package.text_option_detail_' + type, { piece: piece*i, cost: i })
                    };
                    _d.push(it);
                }
                this.details = _d;
                this.amount = null;
            },
            resetForm() {
                this.type = null;
                this.details = [];
                this.amount = null;
                this.repeating = true;
            },
            addExtras() {
                // Create new entry and add extra for user
                axios.post('/pakete/extras', { type: this.type, amount: this.amount, repeating: this.repeating })
                    .then((response) => {
                        this.showMessage(response);
                        eventHub.$emit('refresh-package-extras');
                        this.resetForm();
                    })
                    .catch(error => {
                        this.showError(error);
                        this.resetForm();
                    });
            },
            getOccurence() {
                return [
                    { text: this.$t('package.text_option_extra_is_repeating'), value: true, disabled: false },
                    { text: this.$t('package.text_option_extra_is_once'), value: false, disabled: !this.extrasOnce.includes(this.type) },
                ];
            },
            getOptions() {
                return [
                    {
                        value: 'storage',
                        text: this.$t('package.text_option_extra_storage')
                    },
                    {
                        value: 'feed',
                        text: this.$t('package.text_option_extra_feed')
                    },
                    {
                        value: 'playerconfiguration',
                        text: this.$t('package.text_option_extra_playerconfig') + (!this.canAddPlayerConfiguration ? ' (' + this.$t('package.text_option_extra_needs_package_upgrade') + ')' : ''),
                        disabled: !this.canAddPlayerConfiguration
                    },
                    {
                        value: 'member',
                        text: this.$t('package.text_option_extra_contributor') + (!this.canAddMember ? ' (' + this.$t('package.text_option_extra_needs_package_upgrade') + ')' : ''),
                        disabled: !this.canAddMember
                    },
                    {
                        value: 'statsexport',
                        text: this.$t('package.text_option_extra_stats_export') + (!this.canAddStatsExport ? ' (' + this.$t('package.text_option_extra_needs_package_upgrade') + ')' : ''),
                        disabled: !this.canAddStatsExport
                    }
                ]
            }
        },

        props: {
            bookable: Object,
            canAddPlayerConfiguration: {
                type: Boolean,
                default: false,
            },
            canAddMember: {
                type: Boolean,
                default: false,
            },
            canAddStatsExport: {
                type: Boolean,
                default: false,
            }
        }
    }
</script>

<style scoped>

</style>
