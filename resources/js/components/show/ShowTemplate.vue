<template>
    <div>
        <b-dropdown
            :text="$t('feeds.link_create_show')">
            <b-dropdown-item></b-dropdown-item>
            <b-dropdown-divider></b-dropdown-divider>
            <b-dropdown-item @click="this.$bvModal.show('create-show-template-modal')">{{ $t('feeds.add_show_template') }}</b-dropdown-item>
        </b-dropdown>
        <create-show-template></create-show-template>
    </div>
</template>

<script>
import CreateShowTemplate from "./CreateShowTemplate";
export default {
    name: "ShowTemplate",
    components: {CreateShowTemplate},
    data() {
        return {
            templates: [],
        }
    }
}
</script>

<style scoped>

</style>
