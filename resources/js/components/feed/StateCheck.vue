<template>
    <b-container>
        <section>
            <h3>{{ $t('feeds.header_validator_channel') }}</h3>
            <state-check-group type="Channel" :feed="feed" :uuid="uuid"></state-check-group>
        </section>

        <section>
            <h3>{{ $t('feeds.header_validator_channel_image') }}</h3>
            <state-check-group type="Logo" :feed="feed" :uuid="uuid"></state-check-group>
        </section>

        <section>
            <h3>{{ $t('feeds.header_validator_show') }}</h3>
            <p class="font-weight-light text-muted">{{ $t('feeds.subheader_validator_show') }}</p>
            <state-check-group type="Show" :feed="feed" :uuid="uuid"></state-check-group>
        </section>

        <section>
            <h3>{{ $t('feeds.header_validator_show_enclosure') }}</h3>
            <p class="font-weight-light text-muted">{{ $t('feeds.subheader_validator_show_enclosure') }}</p>
            <state-check-group type="Enclosure" :feed="feed" :uuid="uuid"></state-check-group>
        </section>
    </b-container>
</template>

<script>
    import StateCheckGroup from "./StateCheckGroup";

    export default {
        name: "state-check",

        components: {
            StateCheckGroup
        },

        props: {
            feed: String,
            uuid: String,
        },

    }
</script>

<style scoped>

</style>
