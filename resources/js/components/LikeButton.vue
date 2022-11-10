<template>
    <b-button variant="outline-primary" @click="plusOne"><i :class="thumbSide"></i> {{ likeCount }}</b-button>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                likeCount: 0,
                likeType: "like",
                thumbSide: "icon-thumbs-up",
                pushed: false
            };
        },

        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        created() {
            this.likeCount = this.likes;
            this.likeType = this.type ? this.type : this.likeType;

            if (this.likeType === 'dislike') {
                this.thumbSide = 'icon-thumbs-down';
            }
        },

        methods: {
            prepareComponent() {
            },

            plusOne() {
                if (!this.pushed) {
                    axios.post(this.url + '/' + this.likeType + '/' + this.id)
                        .then(response => {
                            this.likeCount = response.data;
                            this.pushed = true;
                        });

                    if (this.likeType === 'dislike') {
                        window.open('/feedback?type=' + this.feedbackType + '&entity=' + this.id,'feature_changes','width=500,height=750,left=100,top=100,toolbar=0,menubar=0,location=0');
                    }
                }
            }
        },

        props: {
            likes: Number,
            id: {
                type: Number,
                default: ''
            },
            type: String,
            url: {
                type: String,
                default: '/faq'
            },
            feedbackType: {
                type: String,
                default: "1"
            }
        }
    }
</script>

<style>
</style>
