<template>
        <div>
            <h3>{{$t('teams.header_invites')}}</h3>

            <b-list-group>

                <b-list-group-item class="flex-column align-items-start" v-if="members.length===0">
                    {{$t('teams.text_no_open_invites')}}
                </b-list-group-item>

                <b-list-group-item class="flex-column align-items-start" v-for="(member,id) in members" :key="id">
                    <div class="d-flex w-100 justify-content-between">
                        <span>{{member.email}}</span>
                        <div>
                            <b-button
                                variant="light"
                                size="sm"
                                @click="resend(member)"
                            >
<!--                                <b-icon icon="envelope"></b-icon>-->
                                <svg data-v-1d715545="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="envelope" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-envelope b-icon bi"><g data-v-1d715545=""><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"></path></g></svg>
                                {{$t('teams.text_button_resend_invitation')}}
                            </b-button>
                            <b-button
                                variant="light"
                                size="sm"
                                @click="remove(member)"
                                class="mt-1 mt-sm-0"
                            >
<!--                                <b-icon icon="trash" variant="danger"></b-icon>-->
                                <svg data-v-1d715545="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="trash" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-trash b-icon bi text-danger"><g data-v-1d715545=""><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"></path><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"></path></g></svg>
                                {{$t('teams.text_button_delete_invite')}}
                            </b-button>
                        </div>
                    </div>
                </b-list-group-item>
            </b-list-group>
        </div>
</template>

<script>
    import {eventHub} from "../../app";

    export default {
        name: "MemberInvites",

        data() {
            return {
                members: [],
                loading: true
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
            getQueued() {
                axios.get('/beta/members/invite')
                    .then((response) => {
                        this.members = response.data;
                        if (this.members.length >= this.allowed) {
                            this.canAddMembers = false;
                        }
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                        // always executed
                        this.loading = false;
                    });
            },
            resend(member) {
                if (confirm(this.$t('teams.text_confirm_resend_invitation'))) {
                    axios.put('/beta/members/invite/' + member.id)
                        .then((response) => {
                            this.showMessage(response);
                        })
                        .catch(error => {
                            this.showError(error);
                        });
                }
            },
            remove(member) {
                if (confirm(this.$t('teams.text_confirm_delete_invite'))) {
                    axios.delete('/beta/members/invite/' + member.id)
                        .then((response) => {
                            this.showMessage(response);
                            this.getQueued();
                            eventHub.$emit('members:refresh');
                        })
                        .catch(error => {
                            this.showError(error);
                        });
                }
            }
        },

        mounted() {
            this.getQueued();
        },

        created() {
            eventHub.$on("invites:refresh", () => {
                this.getQueued();
            });
        }
    }
</script>

<style scoped>

</style>
