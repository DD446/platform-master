<template>
    <div>
        <b-embed
            type="video"
            aspect="16by9"
            :src="this.url"
            allowfullscreen
            controls
        ></b-embed>
    </div>
</template>

<script>
export default {
    name: "VideoPlayerButton",

    data() {
        return {
            isPlaying: false,
        }
    },

    props: {
        url: {
            type: String,
            required: true
        }
    }
}
</script>

<style scoped>

</style>
