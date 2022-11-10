<template>
    <div>
        <v-date-picker
                v-model="range"
                mode="date"
                :masks="masks"
                is-range
            >
                <template v-slot="{ inputValue, inputEvents, isDragging }">
                    <div class="flex flex-col sm:flex-row justify-start items-center">
                        <div class="relative flex-grow">
                            <b-dropdown
                                :disabled="readonly"
                                :no-caret="readonly"
                                variant="outline-primary"
                                ref="dropdown"
                            >
                                <b-dropdown-form>
                                    <b-row>
                                        <b-col cols="12">
                                            <b-select
                                                v-model="selected"
                                                :options="options"
                                                size="sm"
                                                @change="updateDateSelection"
                                            ></b-select>
                                        </b-col>
                                    </b-row>
                                    <b-row class="mt-2">
                                        <b-col cols="4">
                                            <b-form-input
                                                :readonly="selected !== 'custom'"
                                                @click="setIsCustom"
                                                size="sm"
                                                :value="inputValue.start"
                                            ></b-form-input>
                                        </b-col>
                                        <b-col cols="1">
                                            <div class="text-center">
                                                -
                                            </div>
                                        </b-col>
                                        <b-col cols="4">
                                            <b-form-input
                                                :readonly="selected !== 'custom'"
                                                @click="setIsCustom"
                                                size="sm"
                                                :value="inputValue.end"
                                            ></b-form-input>
                                        </b-col>
                                    </b-row>
                                    <b-row class="mt-2">
                                        <b-col cols="12">
                                            <v-date-picker
                                                :max-date='new Date()'
                                                :columns="$screens({ sm: 1, md: 1, lg: 2 }, 1)"
                                                v-model="range"
                                                is-range
                                                @input="closeDropdown"
                                                color="gray"></v-date-picker>
                                        </b-col>
                                    </b-row>
                                </b-dropdown-form>
                                <span v-on="inputEvents.start">
<!--                                    <i class="icon-popup" id="l-i-p"></i>-->
                                </span>
                                <template #button-content>
                                    {{ inputValue.start + ' - ' + inputValue.end }}
                                </template>
                            </b-dropdown>
                        </div>
                    </div>
                </template>
            </v-date-picker>
        </div>
</template>

<script>
import {eventHub} from "../../app";

export default {
    name: "StatsDaterangePicker",

    data() {
        return {
            range: {
                start: this.$route.query.df ? new Date(this.$route.query.df) : (d => new Date(d.setDate(d.getDate()-8)) )(new Date),
                end: this.$route.query.dt ? new Date(this.$route.query.dt) : (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
            },
            masks: {
                input: 'DD.MM.YYYY',
            },
            options: [
                { value: 'custom', text: this.$t('stats.opts_date_range_custom') },
                { value: 'today', text: this.$t('stats.opts_date_range_today') },
                { value: 'yesterday', text: this.$t('stats.opts_date_range_yesterday') },
                { value: 'thisweek', text: this.$t('stats.opts_date_range_thisweek') },
                { value: 'lastweek', text: this.$t('stats.opts_date_range_lastweek') },
                { value: 'thismonth', text: this.$t('stats.opts_date_range_thismonth') },
                { value: 'lastmonth', text: this.$t('stats.opts_date_range_lastmonth') },
                { value: 'last7days', text: this.$t('stats.opts_date_range_last7days') },
                { value: 'last30days', text: this.$t('stats.opts_date_range_last30days') },
                { value: 'thisQuarter', text: this.$t('stats.opts_date_range_thisQuarter') },
                /*{ value: 'lastQuarter', text: this.$t('stats.opts_date_range_lastQuarter') },*/
                { value: 'thisYear', text: this.$t('stats.opts_date_range_thisYear') },
                { value: 'lastYear', text: this.$t('stats.opts_date_range_lastYear') },
            ],
            selected: 'custom',
            // Attributes are supplied as an array
            attributes: [
                {
                    dates: this.range
                }
            ]
        };
    },

    props: {
        readonly: {
            type: Boolean,
            default: false
        }
    },

    mounted() {
        eventHub.$on('change', (type, params) => {
            if (type === 'mate') {
                this.range = {
                    start: params.start,
                    end: params.end
                };
            }
        });
    },

    methods: {
        updateDateSelection() {
            var d;
            switch (this.selected) {
                case 'today':
                    this.range = {
                        start: new Date(),
                        end: new Date(),
                    }
                    break;
                case 'yesterday':
                    this.range = {
                        start: (d => new Date(d.setDate(d.getDate()-1)))(new Date),
                        end: (d => new Date(d.setDate(d.getDate()-1)))(new Date),
                    }
                    break;
                case 'thisweek':
                    d = new Date();
                    // set to Monday of this week
                    d.setDate(d.getDate() - (d.getDay() + 6) % 7);
                    this.range = {
                        start: d,
                        end: new Date(),
                    }
                    break;
                case 'lastweek':
                    d = new Date();
                    // set to Monday of this week
                    d.setDate(d.getDate() - (d.getDay() + 6) % 7);
                    // set to previous Monday
                    d.setDate(d.getDate() - 7);
                    var sunday = new Date(d.getFullYear(), d.getMonth(), d.getDate() + 6);
                    this.range = {
                        start: d,
                        end: sunday,
                    }
                    break;
                case 'thismonth':
                    this.range = {
                        start: (d => new Date(d.getFullYear(), d.getMonth(), 1))(new Date),
                        end: new Date(),
                    }
                    break;
                case 'lastmonth':
                    this.range = {
                        start: (d => new Date(d.getFullYear(), d.getMonth()-1, 1))(new Date),
                        end: (d => new Date(d.getFullYear(), d.getMonth(), 0))(new Date),
                    }
                    break;
                case 'last7days':
                    this.range = {
                        start: (d => new Date(d.setDate(d.getDate()-8)) )(new Date),
                        end: (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
                    }
                    break;
                case 'last30days':
                    this.range = {
                        start: (d => new Date(d.setDate(d.getDate()-31)) )(new Date),
                        end: (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
                    }
                    break;
                case 'thisQuarter':
/*                    var now = new Date();
                    var quarter = Math.floor((now.getMonth() / 3));
                    var firstDate = new Date(now.getFullYear(), quarter * 3, 1);
                    var endDate = new Date(firstDate.getFullYear(), firstDate.getMonth() + 3, 0);*/
                    this.range = {
                        start: (d => new Date((new Date()).getFullYear(), Math.floor(((new Date()).getMonth() / 3)) * 3, 1))(new Date),
                        end: (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
                        //end: (d => new Date(firstDate.getFullYear(), (new Date((new Date()).getFullYear(), Math.floor(((new Date()).getMonth() / 3)) * 3, 1)).getMonth() + 3)(new Date),
                    }
                    break;
                case 'lastQuarter':
                    this.range = {
                        start: (d => new Date(d.setDate(d.getDate()-31)) )(new Date), // TODO
                        end: (d => new Date((new Date()).getFullYear(), Math.floor(((new Date()).getMonth() / 3)) * 3, 1))(new Date), // TODO: Check
                    }
                    break;
                case 'thisYear':
                    this.range = {
                        start: (d => new Date(d.getFullYear(), 0, 1))(new Date),
                        end: (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
                    }
                    break;
                case 'lastYear':
                    this.range = {
                        start: (d => new Date(d.getFullYear()-1, 0, 1))(new Date),
                        end: (d => new Date(d.getFullYear(), 0, 0, 23, 59, 59))(new Date),
                    }
                    break;
            }
            this.closeDropdown();
        },
        setIsCustom() {
            this.selected='custom';
        },
        closeDropdown() {
            this.$refs.dropdown.hide(true);
        }
    },

    watch: {
        range(to,from) {
            const query = Object.assign({}, this.$route.query);
            let m = to.start.getMonth() + 1;
            query.df = to.start.getFullYear() + '-' + m + '-' + to.start.getDate();
            m = to.end.getMonth() + 1;
            query.dt = to.end.getFullYear() + '-' + m + '-' + to.end.getDate();
            this.$router.push({ query });
            eventHub.$emit('change', 'date', to);
        }
    },
}
</script>

<style scoped>

</style>
