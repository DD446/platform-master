<template>
    <div>
        <heading class="mb-6">Pioniere Check Management</heading>

      <table
          cellpadding="0"
          cellspacing="0"
          class="table w-full bg-white"><!--Informationen, die in einer zweidimensionalen Tabelle dargestellt werden, die aus Zeilen und Spalten von Zellen besteht, die Daten enthalten-->
        <thead><!--definiert eine Reihe von Zeilen, die den Kopf der Spalten der Tabelle definieren-->
        <tr><!--definiert eine Reihe von Zellen in einer Tabelle-->
          <th><!--definiert eine Zelle als Überschrift einer Gruppe von Tabellenzellen-->
            Username
          </th>
<!--          <th>
            audio<em>takes</em>-ID
          </th>
          <th>
            Name
          </th>
          <th>
            E-Mail
          </th>
          <th>
            Telefon
          </th>
          <th>
            Feed-ID
          </th>
          <th>
            Podcast-Titel
          </th>
          <th>
            Aktiv
          </th>
          <th>
            RSS-Feed
          </th>
          <th>
            podcast.de
          </th>-->
        </tr>
        </thead>
        <tbody>
        <tr v-for="(user, usr_id) in users" :key="usr_id">
          <td><!--definiert eine Zelle einer Tabelle, die Daten enthält-->
            {{ user.username }}
<!--            <a :href="user.feedImage" rel="download" v-if="user.feedImage">
              <img :src="user.feedImage" :alt="user.feedImage" style="max-width:100px" />
            </a>-->
          </td>
<!--          <td class="text-center">{{ user.contract.identifier }}</td>
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

      #{{ users }}#

    </div>
</template>

<script>
export default {
  data() {
    return {
      users: null,
      currentPage: 1,
      initialLoading: false,
    }
  },
  methods: {
    getUsers(page) {
      Nova.request()
          .get('/nova-vendor/pioniere-check-management/users?page=' + page)
          .then(response => {
              this.users = response.data.users;
              this.currentPage = response.data.currentPage;
          });
    },
    loadMore() {
      this.getUsers(++this.currentPage);
    }
  },

    mounted() {
      this.getUsers(this.currentPage);
      this.initialLoading = false;
    },
    metaInfo() {
        return {
          title: 'PioniereCheckManagement',
        }
    }
}
</script>

<style>
/* Scoped Styles */
</style>
