<template>
    <div>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <div class="row">
                    <b-col lg="2" offset-lg="8" cols="12" class="pb-4 text-right">
                        <stats-daterange-picker></stats-daterange-picker>
                    </b-col>
                    <b-col lg="2" cols="10" offset="2" offset-lg="0">
                        <stats-pioniere-source-select></stats-pioniere-source-select>
                    </b-col>
                </div>
                <div class="row">
                    <b-col lg="5" offset-lg="7" cols="12">
                        <show-selector></show-selector>
                    </b-col>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <stats-shows></stats-shows>
            </div>
        </div>
    </div>
</template>

<script>
import StatsPioniereSourceSelect from "./StatsPioniereSourceSelect";
import StatsDaterangePicker from "./StatsDaterangePicker";
import {eventHub} from "../../app";
import StatsShows from "./StatsShows";
import ShowSelector from "../ShowSelector";

export default {
    name: "StatsPioniereHosting",

    components: {
        StatsShows,
        StatsPioniereSourceSelect,
        StatsDaterangePicker,
        ShowSelector
    },
}
</script>

<style scoped>

</style>
