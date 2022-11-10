<template>
    <div>
        <loading-view :loading="initialLoading">
            <heading class="mb-6">Pioniere Management</heading>

            <div class="flex-no-shrink ml-auto pb-5">
                <a href="/nova-vendor/pioniere-management/rms" class="btn btn-default btn-primary">RMS-Matching (CSV)</a>
                <a href="/nova-vendor/pioniere-management/matching" class="btn btn-default btn-primary">Adserver-Matching (CSV)</a>
                <a href="/nova-vendor/pioniere-management/listing" class="btn btn-default btn-primary">Publisher-List (CSV)</a>
                <a href="/nova-vendor/pioniere-management/spotify" class="btn btn-default btn-primary">Spotify Details (CSV)</a>
                <a href="/nova-vendor/pioniere-management/spotify/short" class="btn btn-default btn-primary">Spotify Passthrough-Export (CSV)</a>
            </div>

            <div class="pt-5">
                <table
                    cellpadding="0"
                    cellspacing="0"
                    class="table w-full bg-white">
                    <thead>
                        <tr>
                            <th>
                                Logo
                            </th>
                            <th>
                                audio<em>takes</em>-ID
                            </th>
                            <th>
                                Name
                            </th>
                            <th>
                                E-Mail
                            </th>
                            <th>
                                Telefon
                            </th>
                            <th>
                                Feed-ID
                            </th>
                            <th>
                                Podcast-Titel
                            </th>
                            <th>
                                Aktiv
                            </th>
                            <th>
                                RSS-Feed
                            </th>
                            <th>
                                podcast.de
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user) in users" :key="id">
                            <td>
                                <a :href="user.feedImage" rel="download" v-if="user.feedImage">
                                    <img :src="user.feedImage" :alt="user.feedImage" style="max-width:100px" />
                                </a>
                            </td>
                            <td class="text-center">{{ user.contract.identifier }}</td>
                            <td class="text-center">{{ user.contract.first_name }} {{ user.contract.last_name }}</td>
                            <td class="text-center">{{ user.contract.email }}</td>
                            <td class="text-center">{{ user.contract.telephone }}</td>
                            <td class="text-center">{{ user.contract.feed_id }}</td>
                            <td class="text-center">{{ user.feedTitle }}</td>
                            <td class="text-center"><boolean-icon :value="user.isActive==1" width="20" height="20" /></td>
                            <td class="text-center"><a :href="user.feedLink" v-show="user.feedLink">Link</a></td>
                            <td class="text-center"><a :href="user.podcastLink" v-show="user.feedTitle" target="_blank">podcast.de</a></td>
                        </tr>
                    </tbody>
                </table>
<!--                <pagination-links
                    v-if="users"
                    :resources="users"
                    :resource-name="resourceName"
                    :resource-response="resourceResponse"
                    @previous="selectPreviousPage"
                    @next="selectNextPage">
                </pagination-links>-->
                <pagination-load-more
                    v-if="users"
                    current-resource-count="currentResourceCount"
                    :all-matching-resource-count="total"
                    resource-count-label=""
                    :per-page="perPage"
                    :page="currentPage"
                    :pages="pages"
                    @load-more="loadMore"
                ></pagination-load-more>
            </div>

        </loading-view>
    </div>
</template>

<script>
import {eventHub} from "../../../../../resources/js/app";

function initialState (){
    return {
        users: [],
        total: 0,
        perPage: 10,
        currentPage: 1,
        pages: 0,
        currentResourceCount: 10,
        errors: {},
        initialLoading: true,
        loading: false,
    }
}
import { Paginatable, PerPageable } from 'laravel-nova';

export default {
    mixins: [Paginatable, PerPageable],

    metaInfo() {
        return {
          title: 'Pioniere Management',
        }
    },

    data() {
        return initialState();
    },

    methods: {
        getUsers(page) {
            Nova.request()
                .get('/nova-vendor/pioniere-management/users?page=' + page)
                .then(response => {
/*                    if (this.users.length < 1) {
                        this.users  = response.data.users;
                    } else {
                        this.users.concat(response.data.users);
                    }*/
                    let _users = this.users;
                    //_users.concat(response.data.users);
                    [].push.apply(_users, response.data.users);
                    this.$set(this.users, _users);
                    this.total = response.data.total;
                    this.perPage =response.data.perPage;
                    this.pages = response.data.total / response.data.perPage;
                    this.currentPage = response.data.currentPage;
                    this.currentResourceCount = response.data.currentPage * response.data.perPage;
                    this.loading = false;
                });
        },
        loadMore() {
            this.getUsers(++this.currentPage);
        }
    },

    mounted() {
        this.getUsers(this.currentPage);
        this.initialLoading = false;
    },
}
</script>

<style>
/* Scoped Styles */
</style>
