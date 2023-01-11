<template>

    <div>
      <heading class="mb-6">Pioniere Check Management</heading>

          <div ref="waveform" id="wavesurfer-container">
            <p>Episode</p>
          </div>
          <input type="range" v-model="volume" min="0" max="1" step="0.01" />

      <p class="">
        URL : {{ urlLoaded }}
      </p>

      <table
          cellpadding="0"
          cellspacing="0"
          class="table w-full bg-white"><!--Informationen, die in einer zweidimensionalen Tabelle dargestellt werden, die aus Zeilen und Spalten von Zellen besteht, die Daten enthalten-->
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
              <img :src="user.feedImage" :alt="user.feedImage" style="max-width:100px" />
            </a>
          <td class="text-center">{{ user.username }}</td>
          <td class="text-center"><a :href="user.feedLink" v-show="user.feedLink">Link</a></td>
          <td class="text-center"><a :href="user.podcastLink" v-show="user.feedTitle" target="_blank">podcast.de</a></td>
          <td class="text-center">
            <play-pause-button :ws="wavesurfer"></play-pause-button>
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
      users: null,
      currentPage: 1,
      initialLoading: false,
      volume: 1,
      perPage: 10,
      total: 0
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

    loadMore() {
      this.getUsers(++this.currentPage);
    },

    // implementiert die Volume-Bar
    onVolumeChange(volume) {
      this.wavesurfer.volume();
    },

    // wechselt beim Draufklicken zwischen Play- und Pausesymbol
    changeImage() {
      if(this.imageSrcPlay === 'https://www.podcast.de/images/svg/html5player_play.svg') {
        this.imageSrcPlay = this.imageSrcPause;
      }
      else {
        this.imageSrcPlay = 'https://www.podcast.de/images/svg/html5player_play.svg';
        pause();
      }
    },

  },

  mounted() {
    this.getUsers(this.currentPage);
    this.initialLoading = false;

    this.wavesurfer = WaveSurfer.create({
      container: this.$refs.waveform,
      waveColor: 'orange',
      progressColor: 'black'
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
/*#wavesurfer-container {
  position: relative;
}
#playPause-button {
  position: absolute;
  top: 50%;
  left: 0;
  transform: translateY(-50%);
}*/
</style>
