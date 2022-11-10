<template>
    <div class="col-12 col-md-6">
        <b-card>
            <b-card-body>
                <b-media>
                    <b-media-body class="d-flex align-items-center">
                        <b-link class="mr-3" :href="urlByService">
                            <i :class="`socicon-` + this.approval.service" style="font-size: 3em;color:#000000"></i>
                        </b-link>
                        <div>
                                <h5 class="capital-first">
                                    {{ this.approval.service }}
                                </h5>
                            <!-- <small class="text-muted"><i class="icon-location"></i> San Francisco, USA</small>-->
                        </div>
                    </b-media-body>
                </b-media>
            </b-card-body>
            <b-card-footer class="d-flex justify-content-between align-items-center">
                <b-link :href="urlForUser" class="" target="_blank" rel="noreferrer noopener">{{ username }}</b-link>
                <b-button v-b-tooltip.hover.top="$t('approvals.remove_approval')" variant="outline-danger" @click.stop="removeApproval"><i class="icon-trash"></i></b-button>
            </b-card-footer>
        </b-card>
    </div>
</template>

<script>
import {eventHub} from "../../app";

export default {
    name: "ApprovedService",

    props: {
        approval: Object,
    },

    computed: {
        urlByService() {
            switch (this.approval.service) {
                case 'twitter':
                    return 'https://twitter.com/';
                case 'auphonic':
                    return 'https://auphonic.com';
                case 'facebook':
                    return 'https://facebook.com';
                case 'youtube':
                    return 'https://www.youtube.com';
                default:
                    return '#';
            }
        },
        urlForUser() {
            switch (this.approval.service) {
                case 'twitter':
                    return 'https://twitter.com/' + this.approval.screen_name;
                case 'auphonic':
                    return 'https://auphonic.com';
                case 'facebook':
                    return 'https://facebook.com';
                case 'youtube':
                    return 'https://www.youtube.com';
                default:
                    return '#';
            }
        },
        username() {
            switch (this.approval.service) {
                case 'auphonic':
                case 'youtube':
                    return this.approval.screen_name;
                default:
                    return '@' + this.approval.screen_name;
            }
        }
    },

    methods: {
        removeApproval() {
            if (confirm(this.$t('approvals.confirm_delete_approval'))) {
                window.scrollTo(0,275);
                axios.delete('/api/approvals/' + this.approval.id)
                    .then((response) => {
                            eventHub.$emit('show-message:success', response.data);

                            this.$destroy();
                            this.$el.parentNode.removeChild(this.$el);
                        },
                        (error) => {
                            eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
                        })
                    .catch(error => {
                        eventHub.$emit('show-message:error', error.response.data.message ? error.response.data.message : error.toString());
                    });
            }
        }
    },
}
</script>

<style scoped>
.capital-first { text-transform: capitalize; }



</style>
