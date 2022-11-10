<template>
    <b-list-group>
        <state-check-group-item v-for="item of items"
                                :item="item"
                                :type="type"
                                :uuid="uuid"
                                :key="item.id"></state-check-group-item>
    </b-list-group>
</template>

<script>
    import StateCheckGroupItem from "./StateCheckGroupItem";

    export default {
        name: "state-check-group",

        components: {StateCheckGroupItem},

        data() {
            return {
                items: {}
            }
        },

        props: {
            feed: String,
            type: String,
            uuid: String,
        },

        methods: {
            getActions(feed, type) {
                axios.get('/feedvalidator/' + feed + '/actions/' + type)
                    .then(response => {
                        this.items = response.data;
                    });
            },

            run(feed, type, uuid) {
                axios.post('/feedvalidator', { feed: feed, type: type, uuid: uuid })
                    .then(response => {
                        // TODO: Show message of start
                    });
            },
        },

        created() {
            this.getActions(this.feed, this.type);
            this.run(this.feed, this.type, this.uuid);
        },

        mounted() {

        },

    }
</script>

<style scoped>

</style>