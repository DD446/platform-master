<template>
    <div>
    <b-table
        class="mt-2"
        responsive
        show-empty
        stacked="md"
        :items="items"
        :fields="fields"
        :busy="isLoading"
        :empty-text="$t('stats.subscribers_empty_text')"
        hover
        striped
        sort-by="date"
        sort-desc
        sort-direction="desc"
        :per-page="perPage"
        :current-page="currentPage"
        ></b-table>
        <b-pagination
            v-model="currentPage"
            :total-rows="rows"
            :per-page="perPage"
            align="fill"
            size="sm"
            class="my-0"
        ></b-pagination>
    </div>
</template>

<script>
export default {
    name: "SubscriberTable",

    data () {
        return {
            perPage: 10,
            currentPage: 1,
            fields: [
                {
                    key: 'date',
                    label: this.$t('stats.key_date'),
                    formatter: (value, key, item) => {
                        return (new Date(parseInt(value))).toLocaleDateString();
                    },
                    sortable: true,
                    //sortByFormatted: true,
                    // filterByFormatted: true
                },
                {
                    key: 'hits', label: this.$t('stats.key_hits'), sortable: true, class: 'text-center'
                }
            ]
        }
    },

    mounted() {
    },

    props: {
        items: {
            type: Array,
            required: true
        },
        isLoading: {
            type: Boolean,
            default: true
        }
    },

    computed: {
        rows() {
            return this.items.length
        }
    }
}
</script>

<style scoped>

</style>
