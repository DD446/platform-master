<template>
    <div class="row my-4">
        <b-col class="bg-white p-3" cols="12" lg="5" offset-lg="7">
            <h3>{{ $t('approvals.header_add_approval') }}</h3>
            <b-form :action="getAction" method="post">
                <input type="hidden" :value="csrf" name="_token">
                <b-form-group :label="$t('approvals.label_services')" label-for="service-list" inline>
                    <b-select
                        id="service-list"
                        v-model="selected"
                        :options="options"
                        class="mb-3"
                        size="lg"></b-select>
                    <b-button
                        type="submit"
                        :disabled="!selected"
                        variant="primary"
                        class="right">{{ $t('approvals.button_add_approval') }}</b-button>
                </b-form-group>
            </b-form>
        </b-col>
    </div>
</template>

<script>
export default {
    name: "AddApproval",

    data() {
        return {
            options: this.getServices(),
            selected: null,
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        };
    },

    props: {
        canUseAuphonic: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        getAction() {
            switch (this.selected) {
                case 'twitter':
                    return '/dienst/' + this.selected + '/freigabe';
                default:
                    return '/podcaster/oauth/action/' + this.selected;
            }
        },
    },
    methods: {
        getServices() {
            return [
                {value: null, text: this.$t('approvals.select_list_choose_a_service')},
                {value: null, text: '------------------------------------------------'},
                {
                    value: 'auphonic', text: 'Auphonic', disabled: !this.canUseAuphonic
                },
                /*            {
                                value: 'facebook', text: 'Facebook'
                            },*/
                {
                    value: 'twitter', text: 'Twitter'
                },
                /*            {
                                value: 'youtube', text: 'YouTube'
                            }*/
            ]
        }
    },

    mounted() {
    }
}
</script>

<style scoped>

</style>
