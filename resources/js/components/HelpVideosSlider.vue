<template>
    <div>
        <b-carousel
            id="carousel-1"
            :interval="4000"
            controls
            indicators
            background="#cccccc"
            img-width="1024"
            img-height="480"
            style="text-shadow: 1px 1px 2px #333;"
            @sliding-start="onSlideStart"
            @sliding-end="onSlideEnd"
        >
            <!-- Text slides with image -->
            <b-carousel-slide
                v-for="(video, key) in videos" :key="key"
            >
                <template #img>
                    <a :href="video.link"><img
                        class="d-block img-fluid w-100"
                        width="1024"
                        height="480"
                        :src="video.image"
                        :alt="video.title"
                    ></a>
                </template>
            </b-carousel-slide>
        </b-carousel>
    </div>
</template>

<script>
export default {
    name: "HelpVideosSlider",
    data() {
        return {
        }
    },
    props: {
        videos: {
            type: [Array],
            required: true,
        }
    },
    methods: {
        onSlideStart(slide) {
            this.sliding = true
        },
        onSlideEnd(slide) {
            this.sliding = false
        }
    }
}
</script>
