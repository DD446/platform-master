<template>
  <div>
    <button  @click="play()" v-if="playback === 'paused'">
      <img v-bind:src="imageSrcPlay" alt="Play">
    </button>
    <button  @click="pause()" v-if="playback === 'playing'">
      <img v-bind:src="imageSrcPause" alt="Pause">
    </button>
<!--    <button :class="playState" @click="changeState"></button>-->
  </div>
</template>

<script>
import WaveSurfer from "wavesurfer.js";

export default {
  name: "PlayPauseButton",

  data() {
    return {
      playback: 'paused',
      imageSrcPlay: 'https://www.podcast.de/images/svg/html5player_play.svg',
      imageSrcPause: 'https://www.podcast.de/images/svg/html5player_pause.svg',
    }
  },

  props: {
    ws: null
  },

  methods: {

    play() {
      this.ws
          .load('https://deliver.audiotakes.net/d/podcast-plattform.podcaster.de/p/podcastde-news/m/221123_NAPS_Internationale_Podcast-Markte__China_mixdown_auphonic.mp3?awCollectionId=at-grdzz&awEpisodeId=at-grdzz-5c3a7cda98f38c24bffc6a413b4a8b953c023675&origin=feed&v=16');
      this.playback = 'playing'
    },

    // erstellt die Pausefunktion
    pause() {
      this.ws.pause();
      this.playback = 'paused'
    },


    // wechselt beim Draufklicken zwischen Play- und Pausesymbol
    changeImage() {
      if (this.imageSrcPlay === 'https://www.podcast.de/images/svg/html5player_play.svg') {
        this.imageSrcPlay = this.imageSrcPause;
      } else {
        this.imageSrcPlay = 'https://www.podcast.de/images/svg/html5player_play.svg';
        pause();
      }
    },

    mounted() {
    },
  },


}
</script>

<style scoped>

</style>