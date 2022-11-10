<template>
    <b-container>

        <h2 class="display-5">{{$t('teams.header_my_members')}}
<!--            <span class="font-weight-light text-muted" v-if="!loading">({{memberCount.usedWithQueued}}/{{memberCount.total}})</span>-->
        </h2>

        <div class="text-center" v-show="loading">
            <div class="spinner-grow m-5 h-1" role="status">
                <span class="sr-only">{{ $t("teams.is_loading") }}</span>
            </div>
        </div>

        <b-list-group>
            <b-list-group-item class="flex-column align-items-start" v-if="members.length===0 && !loading">
                <div v-if="!canAddMembers" class="text-center alert-warning m-1 p-4">
                    <span v-html="$t('teams.text_info_missing_feature', {route: '/pakete'})"></span>
                </div>
                <div v-else>
                    {{$t('teams.text_info_no_members')}}
                </div>
            </b-list-group-item>

            <b-list-group-item class="flex-column align-items-start" v-for="(member,id) in members" :key="id">
                <div class="d-flex w-100 justify-content-between">
                    <span>{{member.name}} &lt;{{member.email}}&gt;</span>
                    <div>
                        <b-button
                            variant="light"
                            size="sm"
                            @click="remove(member)"
                            class="mt-1 mt-sm-0"
                        >
<!--                            <b-icon icon="trash" variant="danger"></b-icon>-->
                            <svg data-v-1d715545="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="trash" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-trash b-icon bi text-danger"><g data-v-1d715545=""><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"></path><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"></path></g></svg>
                            {{$t('teams.text_button_delete_member')}}
                        </b-button>
                    </div>
                </div>
            </b-list-group-item>
        </b-list-group>

        <div class="row mt-3">
            <div class="col-12">
                <member-invites :allowed="memberCount" :can-add-members="canAddMembers"></member-invites>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <member-invitation :allowed="memberCount" :can-add-members="canAddMembers"></member-invitation>
            </div>
        </div>

    </b-container>
</template>

<script>
    import {eventHub} from "../../app";

    export default {
        name: "Members",

        data() {
            return {
                members: [],
                loading: true,
                memberCount: {},
            }
        },

        props: {
            allowed: {
                type: Object,
                default: {
                    'included': 0,
                    'extra': 0,
                    'total': 0,
                    'used': 0,
                    'usedWithQueued': 0,
                }
            },
            canAddMembers: {
                type: Boolean,
                default: false
            },
        },

        methods: {
            getMembers() {
                axios.get('/beta/members')
                    .then((response) => {
                        this.members = response.data;
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                        // always executed
                        this.loading = false;
                    });
            },
            getMemberCount() {
                axios.get('/beta/member/count')
                    .then((response) => {
                        this.memberCount = response.data;
                    });
            },
            remove(member) {
                if (confirm(this.$t('teams.text_confirm_delete_member'))) {
                    axios.delete('/beta/members/' + member.id)
                        .then((response) => {
                            this.showMessage(response);
                            this.getMembers();
                            eventHub.$emit('members:refresh');
                        })
                        .catch(error => {
                            this.showError(error);
                        });
                }
            }
        },

        mounted() {
            this.getMembers();
            this.memberCount = this.allowed;
        },

        created() {
            eventHub.$on("members:refresh", () => {
                this.getMemberCount();
            });
        }
    }
</script>

<style scoped>

</style>
