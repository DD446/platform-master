<template>
    <div>
        <b-progress height="2rem" :max="max" class="mb-3" v-b-tooltip.hover.top="freeUsageText">
            <b-progress-bar variant="info" :value="values[0]" v-show="values[0] < 100">{{ used }}</b-progress-bar>
            <b-progress-bar variant="success" :value="values[1]" striped v-show="values[1] > 0">{{ free }}</b-progress-bar>
            <b-progress-bar variant="success" value="100" v-if="values[1] <= 0">Kein Speicherplatz frei</b-progress-bar>
        </b-progress>
    </div>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "Usage",
        data () {
            return {
                max: 100,
                values: [ 100, 0 ],
                timer: null,
                used: null,
                free: null,
                label: '',
            }
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.getUsage();
        },

        created() {
            eventHub.$on("usage:refresh", () => {
                this.getUsage();
            });
        },

        props: {
        },

        methods: {
            getUsage() {
                //axios.get('/podcaster/podcaster/action/getStorageUsage')
                axios.get('/stats/user/storage')
                    .then(response => {
                        this.values = response.data.percentages;

                        if (this.values[1] < 30) {
                            this.used = response.data.label;

                            if (this.values[1] <= 0.01) {
                                eventHub.$emit('show-message:error', this.$t('main.text_message_upload_blocked_insufficient_space'));
                                eventHub.$emit('upload:block');
                            }
                        } else {
                            this.free = response.data.label;
                        }
                        this.label = response.data.label;
                    })
                    .catch(error => {
                    });
            },
        },

        computed: {
            freeUsageText() {
                if (this.values[1] > 0) {
                    return this.$t('main.text_tooltip_usage_free_space', { space: this.label });
                } else {
                    return this.$t('main.text_tooltip_usage_no_free_space');
                }
            }
        }
    }
</script>
