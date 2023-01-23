<template>

  <div>
    <heading class="mb-6">Pioniere Check Management</heading>

    <div class="wavecontainer">
      <div class="box1">
        <PlayPauseButton :ws="wavesurfer"></PlayPauseButton>
        <div class="volume">
          <input type="range" v-model="volume" min="0" max="1" step="0.01"/>
        </div>
      </div>
      <div class="box2">
        <p class="url">
          URL : {{ urlLoaded }}
        </p>
        <div ref="waveform" id="wavesurfer">
          <p class="title">
            Titel : {{ title }}
          </p>
        </div>
      </div>
    </div>
  <div class="spaceBetweenPlayerTable"></div>
    <table
        cellpadding="0"
        cellspacing="0"
        class="table w-full bg-white">
      <!--Informationen, die in einer zweidimensionalen Tabelle dargestellt werden, die aus Zeilen und Spalten von Zellen besteht, die Daten enthalten-->
      <thead><!--definiert eine Reihe von Zeilen, die den Kopf der Spalten der Tabelle definieren-->
      <tr><!--definiert eine Reihe von Zellen in einer Tabelle-->
        <th><!--definiert eine Zelle als Überschrift einer Gruppe von Tabellenzellen-->
          Logo
        </th>
        <th>
          Username
        </th>
        <th>
          neueste Folge
        </th>
        <th>
          Link
        </th>
        <th>
          Play
        </th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(user, usr_id) in users" :key="usr_id"><!--Schleifendurchlauf der Benutzer nach ID-->
        <td class="text-center">{{ user.identifier }}</td><!--definiert eine Zelle einer Tabelle, die Daten enthält-->
        <a :href="user.feedImage" rel="download" v-if="user.feedImage">
          <img :src="user.feedImage" :alt="user.feedImage" style="max-width:100px"/>
        </a>
        <td class="text-center">{{ user.username }}</td>
        <td class="text-center"><a :href="user.feedLink" v-show="user.feedLink">Link</a></td>
        <td class="text-center"><a :href="user.podcastLink" v-show="user.feedTitle" target="_blank">podcast.de</a></td>
        <td class="text-center">
          <play-pause-button :ws="wavesurfer" :class="{'playing': user.isPlaying }" @click="resetAndToggle(usr_id)">
            {{ user.isPlaying ? 'Pause' : 'Play' }}
          </play-pause-button>
        </td>

        <!--                    <td class="text-center">{{ user.contract.feedTitle }}</td>
                                <td class="text-center"><a :href="user.feedLink" v-show="user.feedLink">Link</a></td>
                                <td class="text-center"><a :href="user.podcastLink" v-show="user.feedTitle" target="_blank">podcast.de</a></td>
                                <td class="text-center">{{ user.contract.identifier }}</td>
                                <td class="text-center">{{ user.contract.first_name }} {{ user.contract.last_name }}</td>
                                <td class="text-center">{{ user.contract.email }}</td>
                                <td class="text-center">{{ user.contract.telephone }}</td>
                                <td class="text-center">{{ user.contract.feed_id }}</td>
                                <td class="text-center">{{ user.feedTitle }}</td>
                                <td class="text-center"><boolean-icon :value="user.isActive==1" width="20" height="20" /></td>
                                <td class="text-center"><a :href="user.feedLink" v-show="user.feedLink">Link</a></td>
                                <td class="text-center"><a :href="user.podcastLink" v-show="user.feedTitle" target="_blank">podcast.de</a></td>-->
      </tr>
      </tbody>
    </table>


    <!-- Informationsangebot wird auf mehrere Seiten aufgeteilt -->
    <pagination-load-more
        v-if="users"
        current-resource-count="currentResourceCount"
        :all-matching-resource-count="total"
        resource-count-label=""
        :per-page="perPage"
        :page="currentPage"
        :pages="pages"
        @load-more="loadMore"
    ></pagination-load-more>

    #{{ currentPage }}#
    #{{ users }}#

  </div>


</template>

<script>

import WaveSurfer from "wavesurfer.js";
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
            this.users = response.data.data;
            this.currentPage = response.data.current_page;
            this.perPage = response.data.per_page;
            this.total = response.data.total;
          });
    },

// Methode zur alleinigen Funktionalität
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

    this.wavesurfer = WaveSurfer.create({
      container: '.box2',
      waveColor: 'orange',
      progressColor: 'black',
      barWidth: 2,
      barRadius: 2,
      responsive: true,
    });
    this.wavesurfer.on('ready', this.wavesurfer.play.bind(this.wavesurfer));
    // this.wavesurfer.load('https://deliver.audiotakes.net/d/podcast-plattform.podcaster.de/p/podcastde-news/m/221123_NAPS_Internationale_Podcast-Markte__China_mixdown_auphonic.mp3?awCollectionId=at-grdzz&awEpisodeId=at-grdzz-5c3a7cda98f38c24bffc6a413b4a8b953c023675&origin=feed&v=16');
  },

  beforeDestroy() {
    this.wavesurfer.destroy();
  },

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
  box-shadow:  -3px -3px 2px orange;
  position: relative;
  width: 100%;
  height: 128px;
  display: flex;
  flex-wrap: nowrap;
  background: linear-gradient(rgba(0,0,0,0.5),#dbdada);
}

.box1 {
  width: 30%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.box2 {
  position: relative;
  flex-basis: 70%;
}
.url {
  position: absolute;
  padding-left: 20px;
  top: 0;
}
.title {
  position: absolute;
  padding-left: 20px;
  bottom: 0;
}

.volume {
  position: absolute;
  bottom: 0;
}
.spaceBetweenPlayerTable {
  height: 30px;
}
</style>
