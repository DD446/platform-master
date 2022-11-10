<template>
    <form action="/faq/s" method="get" class="card card-sm">
        <input type="hidden" name="q" :value="faqSearch">
        <div class="card-body row no-gutters align-items-center">
            <div class="col-auto">
                <i class="icon-magnifying-glass h4 text-body"></i>
            </div>
            <!--end of col-->
            <div class="col">
                <!-- Begin custom suggestion slot -->
                <template slot="suggestion123" slot-scope="{ data, htmlText }">
                    <div class="d-flex align-items-center">
                        <!-- Note: the v-html binding is used, as htmlText contains
                             the suggestion text highlighted with <strong> tags -->
                        <span class="ml-4" v-html="htmlText"></span>
                    </div>
                </template>
                <vue-typeahead-bootstrap
                    input-class="form-control form-control-lg form-control-borderless"
                    :data="entries"
                    v-model="faqSearch"
                    size="lg"
                    showAllResults
                    :serializer="item => item.attributes.question"
                    :placeholder="$t('faq.placeholder_search')"
                    @hit="selectedEntry = $event"
                    highlight-class="text-dark"
                    text-variant="body"
                ></vue-typeahead-bootstrap>
            </div>
            <!--end of col-->
            <div class="col-auto">
                <button class="btn btn-lg btn-success" type="submit">{{$t('faq.button_search')}}</button>
            </div>
            <!--end of col-->
        </div>
    </form>
</template>

<script>
import VueTypeaheadBootstrap from "vue-typeahead-bootstrap";

const API_URL = window.location.protocol + '//' + window.location.host + '/api/faq/search?q=:query';

export default {
    name: "SearchBar",

    components: {
        'vue-typeahead-bootstrap': VueTypeaheadBootstrap
    },

    data() {
        return {
            entries: [],
            faqSearch: '',
            selectedEntry: null
        }
    },

    methods: {
        async getResults(query) {
            const res = await fetch(API_URL.replace(':query', query));
            const suggestions = await res.json();
            this.entries = suggestions.data;
        },
    },

    watch: {
        faqSearch: _.debounce(function(q) { this.getResults(q) }, 500),

        selectedEntry(e) {
            window.location = e.link;
        }
    }
}
</script>

<style scoped>

</style>
