<template>

  <div>
    <heading class="mb-6">Pioniere Check Management</heading>

    <div class="wavecontainer">
      <div class="box2">
        <div ref="waveform" id="wavesurfer">
        </div>
      </div>
    </div>
    <div class="mt-3">
      <div class="">
        <div class="volume">
          <input type="range" v-model="volume" min="0" max="1" step="0.01"/>
        </div>
      </div>
      <div class="flex-row mt-3">
        <div class="flex-col mt-3">
          <p class="title">
            Titel : {{ title }}
          </p>
        </div>
        <div class="flex-col mt-3">
          <p class="url">
            URL : <a :href="url" target="_blank">{{ url }}</a>
          </p>
        </div>
      </div>
    </div>
    <table
        cellpadding="0"
        cellspacing="0"
        class="table w-full bg-white mt-8">
      <!--Informationen, die in einer zweidimensionalen Tabelle dargestellt werden, die aus Zeilen und Spalten von Zellen besteht, die Daten enthalten-->
      <thead><!--definiert eine Reihe von Zeilen, die den Kopf der Spalten der Tabelle definieren-->
      <tr><!--definiert eine Reihe von Zellen in einer Tabelle-->
<!--        <th>
          Logo
        </th>-->
        <th>
          Username
        </th>
        <th>
          neueste Folge
        </th>
        <th>
          RSS-Feed
        </th>
        <th>
          Play
        </th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(user, usr_id) in users" :key="usr_id"><!--Schleifendurchlauf der Benutzer nach ID-->
<!--        <td class="text-center">{{ user.identifier }}</td>--><!--definiert eine Zelle einer Tabelle, die Daten enthält-->

        <td class="text-center">{{ user.username }}</td>
        <td class="text-center">{{ user.newestEntry.title }}</td>
        <td class="text-center"><a :href="user.podcastLink" v-show="user.feedTitle" target="_blank">{{ user.feedLink }}</a></td>
        <td class="text-center">
          <play-pause-button
              :url="url"
              :ws="wavesurfer"
              :entry="user.newestEntry"
              :class="{'playing': user.isPlaying }" @click="resetAndToggle(usr_id)">
            {{ user.isPlaying ? 'Pause' : 'Play' }}
          </play-pause-button>
        </td>

      </tr>
      </tbody>
    </table>


  </div>


</template>

<script>
export const eventHub = new Vue();

import  WaveSurfer from "wavesurfer.js";
import PlayPauseButton from "./PlayPauseButton";

export default {

  name: 'MyComponent',

  components: {PlayPauseButton},

  data() {
    return {
      url: '',
      wavesurfer: null,
      wavesurferReady: false,
      urlLoaded: null,
      title: null,
      users: null,
      currentPage: 1,
      initialLoading: false,
      volume: 1,
      perPage: 10,
      total: 0,
      newestEntry: null
    }
  },

  // lässt die Volumenscrollbar funktionieren
  watch: {
    volume(newValue) {
      this.wavesurfer.setVolume(newValue);
    }
  },

  methods: {
    getUsers(page) {
      Nova.request()
          .get('/nova-vendor/pioniere-check-management/users?page=' + page)
          .then(response => {
            this.users = response.data;
            this.currentPage = response.data.current_page;
            this.perPage = response.data.per_page;
            this.total = response.data.total;

            //this.wavesurfer.load(this.users[0].newestEntry.url);
            this.title = this.users[0].newestEntry.title;
            this.url = this.users[0].newestEntry.url;
          });
    },

    // Methode zur alleinigen Funktionalität des PPButton
    resetAndToggle(usr_id) {
      this.users.forEach(user => {
        user.isPlaying = false;
      });
      this.users[usr_id].isPlaying = true;
    },

    loadMore() {
      this.getUsers(++this.currentPage);
    },

    // implementiert die Volume-Bar
    onVolumeChange(volume) {
      this.wavesurfer.volume();
    },

  },

  mounted() {
    this.getUsers(this.currentPage);
    this.initialLoading = false;

    // Instanzen erstellen des Wavesurfers
    this.wavesurfer = WaveSurfer.create({
      container: '.box2',
      waveColor: 'orange',
      progressColor: 'black',
      barWidth: 2,
      barRadius: 2,
      responsive: true,
    });

    // lädt den wavesurfer nach ersten klick auf PPButton
    this.wavesurfer.on('ready', this.wavesurfer.play.bind(this.wavesurfer));
    this.wavesurfer.on('pause', () => { this.$emit('all-paused') });
    //this.wavesurfer.on('pause', this.$emit('all-paused'));
    this.$on('play', (url) => {
      this.url = url;
    });

  },

  // Methode, die Ereignisse, Elemente entfernt und Web-Audio-Knoten trennt
  beforeDestroy() {
    this.wavesurfer.destroy();
  },

  // Methode für MetaInfo im Browser-Tab
  metaInfo() {
    return {
      title: 'PioniereCheckManagement',
    }
  }
}
</script>

<style>

.wavecontainer {
  border-radius: 10px;
  box-shadow: -3px -3px 2px orange;
  position: relative;
  width: 100%;
  display: flex;
  flex-wrap: nowrap;
  background: linear-gradient(rgba(0, 0, 0, 0.5), #dbdada);
}

.box1 {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.box2 {
  position: relative;
  flex-basis: 100%;
}

.url {
  padding-left: 20px;
}

.title {
  padding-left: 20px;
}

.volume {
  padding-left: 20px;
}

</style>
