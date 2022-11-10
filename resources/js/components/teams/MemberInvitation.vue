<template>
    <b-container>
        <b-row>
            <b-col cols="12">
                <b-button
                    variant="primary"
                    :disabled="!canAddMembers || allowed.usedWithQueued >= allowed.total"
                    v-b-toggle.collapse-email>
<!--                    <b-icon :icon="visible ? 'dash' : 'plus'"></b-icon>-->
                    <svg
                        v-show="!visible"
                        data-v-2267c81e="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="plus" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-plus b-icon bi"><g data-v-2267c81e=""><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"></path></g></svg>
                    <svg
                        v-show="visible"
                        data-v-62f4eaf4="" viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="dash" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-dash b-icon bi"><g data-v-62f4eaf4=""><path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"></path></g></svg>
                    {{ $t('teams.text_invite_member') }}
                </b-button>

                <div v-show="allowed.usedWithQueued >= allowed.total" class="text-center alert-warning m-2 p-2">
                    <span class="" v-html="$t('teams.text_info_no_members_left', {route: '/pakete/extras'})"></span>
                </div>
            </b-col>

            <b-col cols="12" class="mt-3">
                <b-collapse id="collapse-email" v-model="visible">

                    <b-alert show variant="warning">
<!--                        <b-icon icon="alert-circle-fill" class="h3 align-middle" variant="danger"></b-icon>-->
                        {{$t('teams.text_warning_members')}}
                    </b-alert>

                    <b-form @submit.prevent="onSubmit">
                        <b-row>
                            <b-col xl="4" lg="4" md="6" sm="6">
                                <b-input
                                    autofocus
                                    required
                                    type="email"
                                    name="email"
                                    v-model="model.email"
                                    :placeholder="$t('teams.text_placeholder_member_email')"></b-input>
                            </b-col>
                            <b-col xl="3" lg="4" md="6" sm="6" class="mt-2 mt-sm-0">
                                <b-button
                                    type="submit"
                                    variant="primary"
                                >{{$t('teams.text_button_send_member_invitation')}}
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-form>
                </b-collapse>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
    import {eventHub} from "../../app";

    export default {
        name: "MemberInvitation",

        data () {
            return {
                model: {
                    email: null,
                },
                visible: false
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
            onSubmit() {
                this.errors = [];
                window.scrollTo(0,275);

                if (confirm(this.$t('teams.text_confirm_invite_member'))) {
                    axios.post('/beta/members/invite', this.model)
                        .then(response => {
                            this.showMessage(response);
                            this.model.email = null;
                            this.visible = false;
                            eventHub.$emit('invites:refresh');
                            eventHub.$emit('members:refresh');
                        })
                        .catch(error => {
                            this.showError(error);
                        });
                }
            },

        }
    }
</script>

<style scoped>

</style>
