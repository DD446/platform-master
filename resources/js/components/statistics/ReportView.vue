<template>
    <div style="max-width: 75px">
        <multiselect
            v-model="value"
            track-by="value"
            :allow-empty="false"
            :searchable="false"
            :deselect-label="deselectLabel"
            :select-label="selectLabel"
            :selected-label="selectedLabel"
            :preselect-first="true"
            @select="onChange"
            :options="options">
            <template slot="singleLabel" slot-scope="props">
                <div class="justify-content-center">
                    <i :class="'fa fa-fw fa-' + props.option.img"></i>
                    <p class="mt-1">
                        <span class="small">{{ props.option.title }}</span>
                    </p>
                </div>
            </template>
            <template slot="option" slot-scope="props">
                <i :class="'fa fa-fw fa-' + props.option.img"></i>
                <p class="mt-1">
                    <span class="small">{{ props.option.title }}</span>
                </p>
            </template>
        </multiselect>
    </div>
</template>
<script>
import Multiselect from "vue-multiselect";
import {eventHub} from "../../app";

export default {
    name: 'report-view',

    components: {
        Multiselect
    },

    data() {
        return {
            placeholder: this.$t('stats.placeholder_report_view'),
            selectLabel: this.$t('stats.text_file_selector_report_view_select_label'),
            selectedLabel: this.$t('stats.text_file_selector_report_view_selected_label'),
            deselectLabel: this.$t('stats.text_file_selector_report_view_deselect_label'),
            value: null,
            options: [
                {
                    value: 'chart',
                    title: 'Chart',
                    img: 'bar-chart',
                },
                {
                    value: 'table',
                    title: 'Tabelle',
                    img: 'table',
                }
            ]
        }
    },

    methods: {
        onChange (value) {
            eventHub.$emit('reportview:changed', value.value);
        }
    }
}
</script>
