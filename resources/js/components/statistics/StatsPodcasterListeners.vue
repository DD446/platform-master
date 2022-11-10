<template>
    <b-row>
        <b-col cols="12" lg="2">
          <stats-podcaster-side-navs></stats-podcaster-side-navs>
        </b-col>
        <b-col cols="12" lg="10">
                <h2>{{ $t('stats.title_listeners') }}</h2>

                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <div class="row">
                            <b-col lg="2" offset-lg="8" cols="12" class="pb-4 text-right">
                                <stats-daterange-picker></stats-daterange-picker>
                            </b-col>
                            <b-col lg="2" cols="10" offset="2" offset-lg="0">
                                <stats-source-select :default-value-label="this.$t('stats.text_option_all_podcasts')"></stats-source-select>
                            </b-col>
                        </div>
<!--                        <div class="row">
                            <b-col lg="5" offset-lg="7" cols="12">
                                <show-selector></show-selector>
                            </b-col>
                        </div>-->
                    </div>
                </div>

                <b-row>
                    <b-col cols="12">
                        <stats-shows></stats-shows>
                    </b-col>
                </b-row>

                <!--        <b-row class="mt-5">
                            <b-col cols="3">
                                <b-form-checkbox v-model="expertmode" name="check-button" switch @change="expertmodeChange">
                                    {{ $t('stats.label_expertmode') }}
                                </b-form-checkbox>
                            </b-col>
                        </b-row>-->
        </b-col>
    </b-row>
</template>

<script>
    import StatsPodcasterSideNavs from "./StatsPodcasterSideNav";
    import StatsShows from "./StatsShows";
    import StatsSourceSelect from "./StatsSourceSelect";
    import StatsDaterangePicker from "./StatsDaterangePicker";
    import {eventHub} from "../../app";
    import ShowSelector from "../ShowSelector";

    export default {
        name: "StatsPodcasterListeners",

        components: {
            StatsShows,
            StatsPodcasterSideNavs,
            StatsDaterangePicker,
            ShowSelector,
            StatsSourceSelect
        },

        data () {
            return {
                isLoading: true,
                expertmode: false,
                datacollection: null,
            }
        },

        props: [
            'options'
        ],
        mounted () {
            eventHub.$on('change', (type, params) => {
                console.log("CHANGE", type, params);
            });
        },
        methods: {
            getRandomInt () {
                return Math.floor(Math.random() * (50 - 5 + 1)) + 5
            },
            expertmodeChange() {
                eventHub.$emit('change', 'expertmode', this.expertmode);
            }
        }
    }
</script>
