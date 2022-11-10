<template>
    <b-button :variant="variant" :title="title" v-b-tooltip.hover.top>
        <img :src="loader" style="height: 20px" class="pr-1" v-if="count == null" :alt="info" aria-hidden="true">
        {{ info }}
    </b-button>
</template>

<script>
    export default {
        name: "StatsButton",

        data() {
            return {
                count: null,
                variant: 'secondary',
            }
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.getCount();
        },

        computed: {
            info() {
                if (this.count) {
                    return this.count['now'] + ' ' + this.text;
                }
            }
        },

        created() {
        },

        props: {
            text: String,
            title: String,
            loader: String,
            action: String,
        },

        methods: {
            getCount() {
                axios.get(this.action)
                    .then(response => {
                        this.count = response.data;
                        this.variant = 'outline-secondary';
                    })
                    .catch(error => {
                        this.count = '-';
                    });
            },
        },
    }
</script>

<style scoped>

</style>
