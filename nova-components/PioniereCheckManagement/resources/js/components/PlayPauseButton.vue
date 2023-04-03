<template>
  <div>
    <button  @click="play()" v-if="playback === 'paused'">
      <img v-bind:src="imageSrcPlay" alt="Play">
    </button>
    <button  @click="pause" v-if="playback === 'playing'">
      <img v-bind:src="imageSrcPause" alt="Pause">
    </button>
  </div>
</template>

<script>
import WaveSurfer from "wavesurfer.js";
import eventHub from "./EventHub";

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
    entry: {
      type: Object,
      required: true
    },
    ws: null
  },

  methods: {

    play() {
      this.ws
          .load(this.entry.url);
      this.playback = 'playing'
      this.$emit('play', {url: this.entry.url});
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
      console.log(eventHub)
      this.$on('all-paused', () => {
        console.log('ALL PAUSED');
        //this.pause();
      });
    },
  },


}
</script>

<style scoped>

</style>
