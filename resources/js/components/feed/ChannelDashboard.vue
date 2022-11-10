<template>
    <div class="container">
        <ul class="row feature-list feature-list-sm">
            <li class="col-12 col-md-6 col-lg-4" v-for="feed of feeds" :key="feed.id">
                <b-card>
                    <b-link :to="'podcast/' + feed.id">
                        <b-overlay :show="feed.type=='redirect'">
                            <img class="card-img-top" v-if="feed.links.logo" :src="feed.links.logo" :alt="$t('feeds.alt_logo')">
                            <b-img class="img img-fluid" blank height="306" blank-color="#ccc" v-if="!feed.links.logo" :alt="$t('feeds.alt_no_logo')"></b-img>
                            <template #overlay>
                                <div class="text-center">
                                    <i class="icon-arrow-long-right"></i>
                                    <p>Weiterleitung</p>
                                </div>
                            </template>
                        </b-overlay>
                    </b-link>
                    <b-card-body>
                        <b-link :to="'podcast/' + feed.id">
                            <h4 class="card-title">{{ feed.attributes.title }}</h4>
<!--                            <p class="card-text text-body feed-description" v-show="feed.type!='redirect'">
                                {{ feed.attributes.description }}
                            </p>-->
                        </b-link>
                    </b-card-body>
                    <div class="card-footer card-footer-borderless d-flex justify-content-between">
                        <div class="text-small">
                            <ul class="list-inline">
                                <li class="list-inline-item" v-show="feed.type!='redirect'">
                                    <i class="icon-beamed-note mr-1"></i> {{ feed.shows_count }}
                                </li>
                            </ul>
                        </div>
                        <div class="dropup" v-if="feed.type!='redirect'">
                            <b-dropdown :id="'dropdown-' + feed.feed_id" text="..." class="m-md-2" no-caret size="sm">
                                <b-dropdown-item :to="'/podcast/' + feed.id">{{ $t('nav.feed_overview') }}</b-dropdown-item>
                                <b-dropdown-item :to="'/podcast/' + feed.id + '/episode'">{{ $t('feeds.link_create_show') }}</b-dropdown-item>
<!--                                <b-dropdown-item
                                    @click.stop="auphonicLink(feed.id)"
                                    v-if="hasAuphonic">{{ $t('feeds.link_create_auphonic_production') }}</b-dropdown-item>-->
                                <b-dropdown-item :to="'/podcast/' + feed.id + '/details'">{{ $t('feeds.link_feed_details') }}</b-dropdown-item>
                                <b-dropdown-item :to="'/podcast/' + feed.id + '/episoden'">{{ $t('nav.shows') }}</b-dropdown-item>
                                <b-dropdown-item :to="'/podcast/' + feed.id + '/player'">{{ $t('nav.link_player') }}</b-dropdown-item>
                                <b-dropdown-item :to="'/podcast/' + feed.id + '/status'">{{ $t('nav.link_check') }}</b-dropdown-item>
                                <b-dropdown-item :to="'/podcast/' + feed.id + '/promotion'">{{ $t('nav.link_submission') }}</b-dropdown-item>
                            </b-dropdown>
                        </div>
                        <div class="dropup" v-else>
                            <b-dropdown :id="'dropdown-' + feed.feed_id" text="..." class="m-md-2" no-caret size="sm">
                                <b-dropdown-item :to="'/podcast/' + feed.id">{{ $t('nav.feed_overview') }}</b-dropdown-item>
                            </b-dropdown>
                        </div>
                    </div>
                </b-card>
            </li>
            <!--end of col-->
        </ul>
        <!--end of row-->
    </div>
</template>

<script>
export default {
    name: "ChannelDashboard",

    props: {
        feeds: {
            type: Array,
            required: true
        }
    }
}
</script>

<style scoped>

</style>
