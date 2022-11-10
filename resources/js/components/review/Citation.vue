<template>
    <div>
        <textarea class="form-control form-control-lg" v-model.trim="citation">{{ citation }}</textarea>
        <input type="checkbox" v-model="published" true-value="1" false-value="0" :id="id">
        <label :for="id">Veröffentlichen</label>
        <button type="submit" class="btn btn-primary right" v-on:click="saveCite">Speichern</button>
    </div>
</template>

<script>
    export default {
        name: "Citation",

        /*
         * The component's data.
         */
        data() {
            return {
                citation: '',
                published: '0',
            };
        },

        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        created() {
            this.citation = this.published_cite;

            // Prevents this.published being set to null
            if (this.is_published) {
                this.published = this.is_published;
            }
        },

        methods: {
            prepareComponent() {
            },

            saveCite() {
                axios.put('/admin/reviews/' + this.id, this.$data)
                    .then(response => {
                    });
            }
        },

        props: {
            published_cite: String,
            is_published: String,
            id: String
        }
    }
</script>

<style scoped>

</style>