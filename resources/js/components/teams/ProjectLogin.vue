<template>
    <b-container>
        <h2 class="display-5">{{$t('teams.header_my_invitations')}}</h2>

        <div class="text-center" v-show="loading">
            <div class="spinner-grow m-5 h-1" role="status">
                <span class="sr-only">{{ $t("teams.is_loading") }}</span>
            </div>
        </div>

        <b-list-group>
            <b-list-group-item class="flex-column align-items-start" v-if="projects.length===0 && !loading">
                {{$t('teams.text_info_no_projects')}}
            </b-list-group-item>

            <b-list-group-item class="flex-column align-items-start" v-for="(project,id) in projects" :key="id">
                <div class="d-flex w-100 justify-content-between">
                    <span>{{project.name}} &lt;{{project.email}}&gt;</span>
                    <div>
                        <b-button
                            variant="primary"
                            size="sm"
                            :href="'/beta/member/login/' + project.id"
                            class="mt-1 mt-sm-0"
                        >
                            {{$t('teams.text_button_login_to_project')}}
                        </b-button>
                    </div>
                </div>
            </b-list-group-item>
        </b-list-group>
    </b-container>
</template>

<script>
    export default {
        name: "ProjectLogin",

        data() {
            return {
                projects: [],
                loading: true
            }
        },

        methods: {
            getProjects() {
                axios.get('/beta/member/projects')
                    .then((response) => {
                        this.projects = response.data;
                    })
                    .catch(error => {
                        this.showError(error);
                    }).then(() => {
                    // always executed
                    this.loading = false;
                });
            },

            loginToProject(id) {

            }
        },

        mounted() {
            this.getProjects();
        }
    }
</script>

<style scoped>

</style>
