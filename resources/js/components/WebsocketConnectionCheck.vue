<template>
    <b-alert
        dismissible
        :show="show"
        :variant="getVariant">
        <p>{{ stateHint }}</p>
    </b-alert>
</template>

<script>
export default {
    name: "WebsocketConnectionCheck",

    data() {
        return {
            show: true,
            state: 'connecting',
            stateHint: this.$t('feeds.ws_state_hint_connecting'),
        }
    },

    methods: {
        connect() {
            window.Echo.connector.pusher.connection.bind('state_change', (o) => {
                this.state = o.current;
            });
        },
    },

    mounted() {
        this.connect();
        this.state = window.Echo.connector.pusher.connection.state;
    },

    computed: {
        getVariant() {
            let variant = 'warning';
            switch (this.state) {
                case 'unavailable':
                    variant = 'danger';
                    break;
                case 'connecting':
                    variant = 'warning';
                    break;
                case 'connected':
                    variant = 'success';
                    this.show = false;
                    break;
            }
            this.stateHint = this.$t('feeds.ws_state_hint_' + this.state);

            return variant;
        }
    },

}
</script>

<style scoped>

</style>
