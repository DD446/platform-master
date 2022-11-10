<template>
    <div class="row justify-content-center mb-5">
        <div class="col">
            <div class="card card-sm">
                <div class="card-header d-flex bg-secondary justify-content-between align-items-center">
                    <div>
                        <h5>{{$t('package.header_extras_list')}}</h5>
                    </div>
                </div>

                <div class="card-body m-3 align-items-center" v-show="!items">
                    <b-spinner type="border" :label="$t('package.text_package_extras_booked_loading')"></b-spinner>
                </div>

                <b-list-group class="list-group-flush">
                    <b-list-group-item v-for="(item,key) of items" :key="key">
                        <package-extras-item :item="item"></package-extras-item>
                    </b-list-group-item>

                    <b-list-group-item v-if="items && items.length < 1">
                        <span class="text-info font-weight-bold">{{$t('package.text_info_no_booked_extras')}}</span>
                    </b-list-group-item>
                </b-list-group>

                <div class="card-footer" v-show="items && items.length > 0">
                    <span class="text-muted font-italic">{{$t('package.text_info_extras_cancellation')}}</span>
                    <span v-html="$t('package.text_info_extras_storage')" class="text-muted font-italic"></span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PackageExtrasItem from "./PackageExtrasItem";
    import {eventHub} from "../../app";
    export default {
        name: "PackageExtrasBooked",

        data() {
            return {
                items: null,
            }
        },
        methods: {
            getExtras() {
                axios.get('/pakete/extras')
                    .then((response) => {
                        this.items = response.data;
                    })
                    .catch(error => {
                        this.items = [];
                    });
            }
        },

        created() {
            this.getExtras();
            eventHub.$on('refresh-package-extras', () => {
                this.getExtras();
            });
        },

        components: {PackageExtrasItem}
    }
</script>

<style scoped>

</style>
