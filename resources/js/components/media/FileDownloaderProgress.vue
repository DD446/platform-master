<template>
    <div>
        <vue-circle
                :progress="0"
                :size="120"
                :reverse="true"
                line-cap="round"
                :fill="fill"
                empty-fill="rgba(0, 0, 0, .1)"
                :animation-start-value="0.0"
                :start-angle="180"
                insert-mode="append"
                :thickness="3"
                :show-percent="true"
                @vue-circle-progress="progress"
                @vue-circle-end="progress_end">
            <p>Fortschritt</p>
        </vue-circle>
    </div>
</template>

<script>
    import VueCircle from 'vue2-circle-progress';

    export default {
        name: 'FileDownloaderProgress',

        components: {
            VueCircle
        },
        data(){
            return{
                fill : { gradient: ["blue"] },
            }
        },
        methods:{
            progress(event, progress, stepValue) {
                //console.log(stepValue);
            },
            progress_end(event) {
                //console.log("Circle progress end");
            }
        }
    }
</script>

<style scoped>

</style>
