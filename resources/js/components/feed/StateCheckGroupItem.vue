<template>
    <b-list-group-item class="d-flex justify-content-between align-items-center">
        {{ $t('feeds.' + item) }}
        <b-badge :variant="status" pill v-show="!isLoading" :id="item" class="p-1">
            <i :class="statusIcon"></i>
            <b-popover :target="item" triggers="hover focus click">
                <template slot="title">{{ $t('feeds.title_test_result') }}</template>
                <div v-html="statusText"></div>
            </b-popover>
        </b-badge>
        <b-spinner :label="$t('feeds.text_loading_data')" v-show="isLoading" />
    </b-list-group-item>
</template>

<script>
    export default {
        name: "state-check-group-item",

        data() {
            return  {
                status: null,
                msg: null,
                isLoading: true,
            }
        },

        props: {
            item: String,
            type: String,
            uuid: String,
        },

        methods: {
            run(item, type, uuid) {

                const eventName ='FeedValidator' + type + item.charAt(0).toUpperCase() + item.slice(1);

                Echo.private('state.check.' + uuid)
                    .listen('\\App\\Events\\' + eventName, (e) => {
                        this.status = e.state;
                        this.msg = e.message;
                        this.isLoading = false;
                    });
            }
        },

        computed: {
            statusIcon() {
                switch (this.status) {
                    case 'success' :
                        return 'icon-check';
                    case 'warning' :
                        return 'icon-bell';
                    case 'danger' :
                        return 'icon-flag';
                }
            },

            statusText() {
                switch (this.status) {
                    case 'success' :
                        return this.$t('feeds.test_succeeded');
                    case 'warning' :
                    case 'danger' :
                        return this.msg;
                }
            },
        },

        created() {
            this.run(this.item, this.type, this.uuid);
        },

    }
</script>

<style scoped>

</style>
