<template>
    <b-overlay :show="isLoading">
        <b-select
            v-model="selected"
            :options="options"
            class="mb-3"
            :disabled="isLoading"></b-select>
    </b-overlay>
</template>

<script>
import {eventHub} from "../../app";

export default {
    name: "StatsSourceSelect",

    data() {
        return {
            selected: this.$route.query.s ? this.$route.query.s : null,
            options: [
                { value: null, text: this.defaultValueLabel },
            ],
            isLoading: true
        }
    },

    methods: {
        getSources() {
            axios.get('/api/stats/sources')
                .then(response => {
                    let opts = response.data;
                    let _this = this;

                    opts.forEach(function(element) {
                        _this.options.push({
                            value: element.feed_id, text: element.rss.title
                        })
                    });
                })
                .catch(error => {
                    // handle error
                    this.showError(error);
                }).then(() => {
                    this.isLoading = false;
                }
            );
        }
    },

    props: {
        defaultValueLabel: {
            type: String,
            default: function() { return this.$t('stats.text_option_all_sources'); },
        }
    },

    watch: {
        selected(to,from) {
            const query = Object.assign({}, this.$route.query);
            query.s = to;
            this.$router.push({ query });
            eventHub.$emit('change', 'source', to);
        }
    },

    mounted() {
        this.getSources();
    }
}
</script>

<style scoped>

</style>
