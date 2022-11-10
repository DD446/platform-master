<template>
  <div>
      <div class="d-flex justify-content-between">
          <div>
              <h3 class="mb-1">{{ header }}</h3>
              <span class="font-weight-light text-sm">Abrufe vom {{ range.start.toLocaleDateString() }} - {{ range.end.toLocaleDateString() }}</span>
          </div>
          <div>
              <b-button variant="outline-secondary" class="right-auto">Export</b-button>
          </div>
      </div>
      <b-table></b-table>
  </div>
</template>

<script>
export default {
    name: "StatsCountry",

    data () {
        return {
            items: [],
            isLoading: true,
            pageSize: 5,
            pageNumber: 1,
            range: {
                start: (d => new Date(d.setDate(d.getDate()-8)) )(new Date),
                end: (d => new Date(d.setDate(d.getDate()-1)) )(new Date),
            },
        }
    },

    props: {
        header: {
            type: String
        },
        action: {
            type: String,
            default: '/'
        },
        canExport: false
    },

    methods: {
        getCount() {
            this.isLoading = true;
            axios.get(this.action + '?df=' + this.range.start.toISOString() + '&dt=' + this.range.end.toISOString() + '&page[size]=' + this.pageSize + '&page[number]=' + this.pageNumber)
                .then(response => {
                    this.items = response.data;
                })
                .catch(error => {
                    eventHub.$emit('show-message:error', error);
                }).then(() => {
                this.isLoading = false;
            });
        },
    },

    mounted() {
        this.getCount();

        eventHub.$on('change', (type, params) => {
            if (type === 'date') {
                this.range.start = params.start;
                this.range.end = params.end;
                this.getCount();
            }
        });
    },

    watch: {
    },
}
</script>

<style scoped>

</style>
