<template>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <b-spinner
                    :label="$t('feeds.text_loading_data')"
                    class="m-5"
                    style="width: 3rem; height: 3rem;"
                    aria-hidden="true"
                    :aria-label="$t('feeds.text_loading_data')"
                    v-show="loading" />
                <div v-for="option in options">
                    <AmazonPodcastEntry
                            :feed_id="option.id"
                            :feed_title="option.name"
                            :amazon="option.amazon"
                    ></AmazonPodcastEntry>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import AmazonPodcastEntry from "./AmazonPodcastEntry";

    export default {
        name: "Amazon",

        data() {
            return {
                loading: true,
                options: [],
            }
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        created() {
        },

        methods: {
            prepareComponent() {
                axios.get('/amazon')
                    .then(response => {
                        this.options = response.data;
                    })
                    .catch(error => {
                        // handle error
                        this.showError(error);
                    }).then(() => {
                    // always executed
                    this.loading = false;
                });
            }
        },

        components: {
            AmazonPodcastEntry
        }
    }
</script>

<style scoped>

</style>
