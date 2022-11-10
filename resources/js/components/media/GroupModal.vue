<template>
    <b-modal
            ref="groupModal"
            title="Gruppen-Zuordnung"
            :ok-only="false"
            ok-title="Gruppe ändern"
            cancel-title="Abbrechen"
            lazy
            @ok="setGroup"
            >
        <form action="#" class="std">
            <div>
                <b-select v-model="selected" :options="options" class="mb-3" v-on:change="getSelectedItem"></b-select>
            </div>
        </form>
    </b-modal>
</template>

<script>
    import {eventHub} from '../../app';

    export default {

        name: "group-modal",

        data() {
            return {
                item: '',
                selected: null,
                options: []
            }
        },

        computed: {
        },

        methods: {
            show() {
                this.$refs.groupModal.show();
                // TODO: Sort group list after group is added and modal is shown again, reset list with _CREATE_ option
            },
            getSelectedItem: function(selected) {
                if (selected === '_CREATE_') {
                    let group = prompt("Name der neuen Gruppe");
                    if (group) {
                        //this.$delete(this.options, '_CREATE_');
                        this.$set(this.options, group, group);
                        this.selected = group;
                    }
                }
            },
            setGroup: function() {
                let data = {};
                data.id = [];
                if(Array.isArray(this.item)) {
                    for (const i of this.item) {
                        data.id.push(i.id);
                    }
                } else {
                    data.id.push(this.item.id);
                }
                data.group = this.selected;

                axios.put('/media/group', data)
                    .then(response => {
                        eventHub.$emit('show-message:success', response.data.message);
                        eventHub.$emit('media-table:refresh');
                    })
                    .catch(error => {
                        eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
                    });
            },
            getGroups: function() {
                axios.get('/media/groups')
                    .then(response => {
                        this.options = response.data;
                    })
                    .catch(error => {
                        // handle error
                        eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
                    });
            }
        },

        created() {
            this.getGroups();
        },

        mounted() {
            eventHub.$on("group-modal:show", item => {
                this.item = item;
                this.selected = item.cat;
                this.show();
            });
        },
    }
</script>

<style scoped>

</style>