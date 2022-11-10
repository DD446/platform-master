<template>
    <b-row>
        <b-col cols="12" sm="4" md="3" lg="3" xl="2" v-for="(service,key) in services" :key="key"
               style="min-width: 235px">
            <b-card class="text-center">
                <div class="sprite-container">
                    <div :class="key + '-icon'"></div>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ service.name }}</h5>
                    <div class="spinner-grow m-3" role="status" v-show="isLoading">
                        <span class="sr-only">{{ $t('package.text_loading') }}</span>
                    </div>
                    <div class="card-text display-4" v-show="!isLoading">
                        {{ service.count }}
                    </div>
                </div>
            </b-card>
        </b-col>
    </b-row>
</template>
<script>
export default {
    name: 'stats-subscribers-external',

    data() {
        return {
            isLoading: true,
            services: {
                'podcast': {
                    name: 'podcast.de',
                    count: 0
                },
                'overcast': {
                    name: 'Overcast',
                    count: 0
                },
                'playerfm': {
                    name: 'player.fm',
                    count: 0
                },
                'ucast': {
                    name: 'UCast',
                    count: 0
                },
                'breaker': {
                    name: 'Breaker',
                    count: 0
                },
                'feedbin': {
                    name: 'Feedbin',
                    count: 0
                },
                'g2reader': {
                    name: 'G2reader',
                    count: 0
                },
                'bloglovin': {
                    name: 'Bloglovin\'',
                    count: 0
                },
                'instacast': {
                    name: 'Instacast Core',
                    count: 0
                },
                'newsify': {
                    name: 'Newsify',
                    count: 0
                },
                'feedly': {
                    name: 'Feedly',
                    count: 0
                },
            },
        }
    },

    mounted() {
        this.getCount();
    },

    props: {
        action: {
            type: String,
            default: "/api/stats/subscribers/services",
            required: false
        }
    },

    methods: {
        getCount() {
            axios.get(this.action)
                .then(response => {
                    let data = response.data;
                    let self = this;
                    data.forEach(function(e) {
                        self.$data.services[e.user_agent.toLowerCase()].count = e.subscribers;
                    })
                })
                .catch(error => {
                }).then(() => {
                this.isLoading = false;
            });
        }
    }
}
</script>
