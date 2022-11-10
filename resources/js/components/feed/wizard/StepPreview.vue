<template>
    <b-card
        :title="$t('feeds.header_step_preview')"
        class="mb-2">
        <b-card-text>
            {{ $t('feeds.text_step_preview') }}
        </b-card-text>

        <b-card no-body class="overflow-hidden">
            <b-row no-gutters>
                <b-col md="6">
                    <b-card-img :src="feed.logo.itunes ? '/mediathek/' + feed.logo.itunes : feed.image" alt="Logo" class="rounded-0"></b-card-img>
                </b-col>
                <b-col md="6">
                    <b-card-body :title="feed.rss.title">
                        <b-card-sub-title class="mb-2">{{ feed.rss.author }}</b-card-sub-title>
                        <b-card-text>
                            {{ feed.rss.description }}
                        </b-card-text>

                        <b-list-group flush v-for="(category,key) in feed.itunes.category_trans" :key="key">
                            <b-list-group-item>{{ category }}</b-list-group-item>
                        </b-list-group>

                        <b-card-body v-show="feed.rss.link">
                            <a :href="feed.rss.link" class="card-link">{{ $t('feeds.label_website') }}</a>
                        </b-card-body>
                    </b-card-body>
                </b-col>
            </b-row>
        </b-card>
    </b-card>
</template>

<script>
import {eventHub} from '../../../app';

export default {
    name: "StepPreview",

    data() {
        return {
            feed: this.dat,
        }
    },

    props: {
        dat: {
            type: Object,
            default: {
                itunes: {
                    category: []
                }
            }
        }
    },

    watch: {
        dat(newVal, oldVal) {
            this.feed = oldVal;
            this.feed.itunes.category = this.feed.itunes.category.filter(e => e.length);
        }
    },

    mounted() {
    }
}
</script>

<style scoped>
</style>
