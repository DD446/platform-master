<template>
    <div>
        <b-card
            tag="article"
            style="max-width: 20rem;"
            class="mb-2 text-center"
        >
            <b-card-title>
                <div class="spinner-grow m-3" role="status" v-show="isLoading">
                    <span class="sr-only">{{$t('package.text_loading')}}</span>
                </div>
                <small :class="change()" v-if="count['change']">{{ count['change'] }}</small>
                <small v-else>&nbsp;</small>
                <h2>{{ count['now'] }}</h2>
            </b-card-title>
            <b-card-text>
                {{ title }}
            </b-card-text>
            <b-card-text v-show="subtitle">
                <span class="font-weight-lighter small">{{ subtitle }}</span>
            </b-card-text>
        </b-card>
    </div>
</template>

<script>
    import {eventHub} from "../../app";

    export default {
        name: "StatsCounter",

        data() {
            return {
                count: {'now': '-', 'prev': '-', 'change': '-'},
                isLoading: true,
                source: this.$route.query.s ? this.$route.query.s : null,
            }
        },

        mounted() {
            this.getCount();

            eventHub.$on('change', (type, params) => {
                if (this.$route.name && this.$route.name.startsWith("podcaster-") || !this.$route.name) {

                    if (params != this.source) {
                        if (type === 'source') {
                            this.source = params;
                            this.getCount();
                        }
                    }
                }
            });
        },

        props: {
            action: String,
            title: String,
            subtitle: null
        },

        methods: {
            getCount() {
                this.isLoading = true;
                let url = this.action;

                if (this.source) {
                    url += '&s=' + this.source;
                }

                axios.get(url)
                    .then(response => {
                        this.count = response.data;
                        this.isLoading = false;
                    })
                    .catch(error => {
                    }).then(() => {
                        this.isLoading = false;
                    });
            },
            change() {
                if (typeof this.count !== 'undefined' && typeof this.count['change'] !== 'undefined') {
                    if (this.count['change'] === '-') {
                        return 'neutral';
                    } else if(this.count['change'] < 0) {
                        return 'negative';
                    } else {
                        return 'positive';
                    }
                } else {
                    return 'hidden';
                }
            }
        },
    }
</script>

<style scoped>
.positive {
    color: green;
}
.positive::before {
    content: '+';
}
.positive::after {
    content: '%';
}
.negative {
    color: red;
}
/*.negative::before {
    content: '';
}*/
.negative::after {
    content: '%';
}
.neutral {
    visibility: hidden;
}
</style>
