<template>
    <b-modal
            ref="confirmDialog"
            :title="title"
            ok-title="Löschen"
            cancel-title="Abbrechen"
            ok-variant="danger"
            @ok="onConfirm"
        >
        <p>{{ question }}</p>
    </b-modal>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "confirm-dialog",

        data() {
            return {
                item: {},
                baseUrl: '/mediathek/',
                title: 'Löschen'
            }
        },

        methods: {
            show() {
                this.$refs.confirmDialog.show();
            },
            onConfirm() {
                if(Array.isArray(this.item)) {
                    let l = this.item.length;
                    for (const i of this.item) {
                        this.deleteItem(i.id, (--l === 0));
                    }
                } else {
                    this.deleteItem(this.item.id);
                }
            },
            deleteItem(id, last = true) {
                axios.delete(this.baseUrl + id)
                    .then((response) => {
                            //eventHub.$emit('media-table:remove-item', this.item);
                            if (last) {
                                eventHub.$emit('show-message:success', response.data.message);
                                eventHub.$emit('media-table:refresh');
                            }
                        },
                        (error) => {
                            eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
                        })
                    .catch(error => {
                        eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
                    });
            },
        },

        mounted() {
            eventHub.$on("confirm-dialog:show", item => {
                // Treat an array with a single item like a single item
                if(Array.isArray(item) && item.length === 1) {
                    item = item[0];
                }
                this.item = item;
                this.show();
            });
        },

        computed: {
            question() {
                // Resets title
                this.title = 'Datei löschen';

                if(Array.isArray(this.item)) {
                    let l = this.item.length;
                    this.title = l +  ' Dateien löschen';
                    return "Willst Du die " + l + " Dateien wirklich löschen?";
                } else {
                    return 'Willst Du die Datei `' + this.item.name + '` wirklich löschen?';
                }
            }
        }
    }
</script>

<style scoped>

</style>