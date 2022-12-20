<template>
    <div>
        <heading class="mb-6">Pioniere Check Management</heading>

      {{ dat }}
    </div>
</template>

<script>
export default {
  data() {
    return {
      dat: null,
      currentPage: 1,
      initialLoading: false,
    }
  },
  methods: {
    getUsers(page) {
      Nova.request()
          .get('/nova-vendor/pioniere-check-management/users?page=' + page)
          .then(response => {
              this.dat = response.data;
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
