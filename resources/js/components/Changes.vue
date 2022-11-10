<template>
    <b-list-group>
        <div class="text-center" v-show="changes.length === 0">
            <div class="spinner-grow m-5 h-1" role="status">
                <span class="sr-only">{{ $t("pat.is_loading") }}</span>
            </div>
        </div>

        <b-list-group-item class="flex-column align-items-start" v-for="(change,id) in changes" :key="id">

            <b-media>
                <b-media-body>

                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1 font-weight-bolder">{{change.title}}</h5>
                        <small class="text-muted">{{change.published}}</small>
                    </div>
                    <div class="mb-2" v-html="change.description" ref="desc"></div>

                    <like-button :likes="change.likes" :id="change.id" url="/changes" feedback-type="7"></like-button>
                    <like-button :likes="change.dislikes" :id="change.id" url="/changes" type="dislike" feedback-type="7"></like-button>
                </b-media-body>
            </b-media>
        </b-list-group-item>
    </b-list-group>
</template>

<script>
    import LikeButton from "./LikeButton";

    export default {
        name: "Changes",

        components: {
            LikeButton
        },

        data() {
            return {
                changes: []
            }
        },

        methods: {
            getChanges() {
                axios.get('/changes')
                    .then((response) => {
                        this.changes = response.data.data;
                    });
            },
        },

        mounted() {
            this.getChanges();
        },

        watch:{
            changes(val){
                if(val){
                    this.$nextTick(()=>{
                        for (let _img of this.$refs['desc'][0].getElementsByTagName("img")) {
                            _img.classList.add('img-fluid')
                        }
                    })
                }
            }
        }
    }
</script>

<style scoped>

</style>
