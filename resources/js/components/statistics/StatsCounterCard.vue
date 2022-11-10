<template>
    <b-card
        tag="article"
        style="max-width: 20rem;"
        class="mb-2 text-center"
    >
        <b-card-title>
<!--            <small :class="change()" v-if="count['change']">{{ count['change'] }}</small>
            <small v-else>&nbsp;</small>-->
            <h2>{{ count['now'] }}</h2>
        </b-card-title>
        <b-card-text>
            {{ title }}
        </b-card-text>
        <b-card-text v-show="subtitle">
            <span class="font-weight-lighter small">{{ subtitle }}</span>
        </b-card-text>
    </b-card>
</template>

<script>
export default {
    name: "StatsCounterCard",

    data() {
        return {
            isLoading: true,
        }
    },

    mounted() {
    },

    props: {
        count: {
            type: Object,
            default: {'now': '-', 'prev': '-', 'change': '-'},
        },
        title: {
            type: String,
        },
        subtitle: {
            type: String,
            default: null
        }
    }
}
</script>

<style scoped>

</style>
