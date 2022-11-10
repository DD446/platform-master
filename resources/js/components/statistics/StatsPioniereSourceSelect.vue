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
    name: "StatsPioniereSourceSelect",

    data() {
        return {
            selected: this.$route.query.i ? this.$route.query.i : null,
            options: [
                { value: null, text: this.$t('stats.text_option_all_podcasts') },
            ],
            isLoading: true
        }
    },

    methods: {
        getSources() {
            this.isLoading = true;
            axios.get('/api/stats/pioniere/contracts')
                .then(response => {
                    let opts = response.data;
                    let _this = this;

                    Object.values(opts).forEach(function(element) {
                        _this.options.push({
                            value: element.identifier, text: element.title
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

    watch: {
        selected(to,from) {
            const query = Object.assign({}, this.$route.query);
            query.i = to;
            this.$router.push({ query });
            eventHub.$emit('change', 'psource', to);
        }
    },

    mounted() {
        this.getSources();
    }
}
</script>

<style scoped>

</style>
