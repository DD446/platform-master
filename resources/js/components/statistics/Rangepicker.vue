<template>
    <div>
        <v-date-picker
                mode='range'
                v-model='myRange'
                v-on:input="getUpdatedRange"
                show-caps
                :show-day-popover=false
                is-inline
                :input-props='{ class: "form-control form-control-lg", placeholder: "Wähle einen Datumsbereich aus.", readonly: false }'
                :min-date='minDate'
                :max-date='maxDate'>
        </v-date-picker>
    </div>
</template>

<script>
    //import Vue from 'vue';
    //import VCalendar from 'v-calendar';

/*    Vue.use(VCalendar, {
        firstDayOfWeek: 2,  // Monday
    });*/

    export default {
        name: "Rangepicker",

        components: {
            //VCalendar,
        },

        data() {
            return {
                myRange: {
                    start: this.daysAgoDate(7), // Jan 16th, 2018
                    end: this.maxDate,    // Jan 19th, 2018
                },
            };
        },

        methods: {
            daysAgoDate() {
                let d = new Date();
                d.setDate(d.getDate() - 7);

                return d;
            },
            getUpdatedRange(range) {
                this.$emit("selected", range);
            }
        },

        create() {
        },

        props: {
            minDate: {
                type: Date,
                default: new Date(2018, 3, 10),
            },
            maxDate: {
                type: Date,
            },
        }
    }
</script>

<!--<style scoped>
    @import '~v-calendar/src/styles/base.css';
</style>-->
