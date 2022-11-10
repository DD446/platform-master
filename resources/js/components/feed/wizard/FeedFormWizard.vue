<template>
    <div>
        <alert-container></alert-container>

        <form-wizard
                :title="$t('feeds.title_feed_wizard')"
                :subtitle="$t('feeds.subtitle_feed_wizard')"
                :next-button-text="$t('feeds.title_next_button')"
                :back-button-text="$t('feeds.title_back_button')"
                :finish-button-text="$t('feeds.title_finish_button')"
                color="#d80056"
                ref="wizard"
                @on-complete="onComplete"
                @on-change="onChange"
                :hide-buttons="hideButtons"
                v-cloak>
            <tab-content v-for="tab in displayTabs"
                         v-if="!tab.hide"
                         :key="tab.title"
                         :title="tab.title"
                         :icon="tab.icon"
                         :before-change="()=>validateStep(tab.component)">
                <component
                    @on-validate="mergePartialModels"
                    :ref="'component' + tab.component"
                    :dat="finalModel"
                    :is="tab.component"></component>
            </tab-content>

            <wizard-step slot-scope="props"
                         slot="step"
                         @click.native="navigate(props.navigateToTab, props.index)"
                         :tab="props.tab"
                         :index="props.index">
            </wizard-step>
        </form-wizard>
    </div>
</template>

<script>
    import VueFormWizard from 'vue-form-wizard';
    import StepImport from './StepImport';
    import StepFeed from './StepFeed';
    import StepMediaPre from './StepMediaPre';
    import StepMedia from './StepMedia';
    import StepMeta from './StepMeta';
    import StepLogo from './StepLogo';
    import StepUrl from "./StepUrl";
    import StepPreview from "./StepPreview";
    import {eventHub} from '../../../app';

    export default {
        name: "FeedFormWizard",

        data() {
            return {
                tabs: [
                    {
                        index: 1, title: this.$t('feeds.title_step_import'), component: 'StepImport',
                    },
                    {
                        index: 2, title: this.$t('feeds.title_step_feed'), component: 'StepFeed', hide: true,
                    },
/*                    {
                        index: 3, title: this.$t('feeds.title_step_media'), component: 'StepMediaPre', hide: true,
                    },
                    {
                        index: 4, title: this.$t('feeds.title_step_media'), component: 'StepMedia', hide: true,
                    },*/
                    {
                        index: 5, title: this.$t('feeds.title_step_meta'), component: 'StepMeta', hide: false,
                    },
                    {
                        index: 6, title: this.$t('feeds.title_step_logo'), component: 'StepLogo', hide: false,
                    },
                    {
                        index: 7, title: this.$t('feeds.title_step_url'), component: 'StepUrl', hide: false,
                    },
                    {
                        index: 8, title: this.$t('feeds.title_step_preview'), component: 'StepPreview', hide: false,
                    }
                ],
                hideButtons: true,
                step: null,
                finalModel: {
                    feed_id: null,
                    feedUrl: null,
                    rss: {
                        language: 'de',
                        author: this.author,
                        email: this.email
                    },
                    logo: {
                        itunes: null
                    },
                    itunes: {
                        category: [[],[],[]]
                    },
                    domain: {},
                    domains: [],
                    canUseShortSubdomains: false,
                    username: null
                }
            }
        },

        props: {
            username: {
                type: String,
                required: true
            },
            author: {
                type: String
            },
            email: {
                type: String
            },
            localDomains: {
                type: Object,
                required: false,
            },
            canUseShortSubdomains: {
                type: Boolean,
                default: false
            }
        },

        components: {
            VueFormWizard,
            StepImport,
            StepFeed,
/*            StepMediaPre,
            StepMedia,*/
            StepMeta,
            StepLogo,
            StepUrl,
            StepPreview
        },

        mounted() {
            this.finalModel.domains = this.localDomains;
            this.finalModel.username = this.username;
            this.finalModel.canUseShortSubdomains = this.canUseShortSubdomains;

            eventHub.$on('on-validate', (model) => {
                this.mergePartialModels(model);
            });

            eventHub.$on('enable-step:feed', () => {
                this.tabs[1].hide = false;
                //this.tabs[2].hide = true;
                this.hideButtons = false;
                this.step = 'feed';
                this.nextTab();
            });

            eventHub.$on('enable-step:media', () => {
                this.tabs[2].hide = false;
                this.tabs[1].hide = true;
                this.hideButtons = false;
                this.step = 'media';
                this.nextTab();
            });

            eventHub.$on('jump-step:media', () => {
                this.tabs[2].hide = false;
                this.hideButtons = false;
                //this.nextTab();
            });

            eventHub.$on('jump-step:meta', () => {
                this.tabs[2].hide = false;
                this.hideButtons = false;
                this.nextTab();
            });
        },

        computed: {
            displayTabs: function (){
                return this.tabs.filter(function (tab){
                    return tab.hide !== true;
                })
            }
        },

        methods: {
            onComplete: function() {
                axios.post('/api/feeds', { feed_id: this.$data.finalModel.feed_id, rss: this.$data.finalModel.rss, domain: this.$data.finalModel.domain, itunes: this.$data.finalModel.itunes, logo: this.$data.finalModel.logo, feed_url: this.$data.finalModel.feedUrl })
                    .then(response => {
                            //eventHub.$emit('show-message:success', response.data);
                            window.location = '/podcasts#/podcast/' + this.$data.finalModel.feed_id;
                        },
                        (error) => {
                            this.showError(error);
                        })
                    .catch(error => {
                        this.showError(error);
                    })
                    .then(() => {
                        window.scrollTo(0,0);
                    });
            },
            onChange: function(prevIndex, nextIndex) {
                // Reset wizard to fresh start
                if (nextIndex === 0) {
                    this.tabs[2].hide = true;
                    this.tabs[1].hide = true;
                    this.hideButtons = true;
                }
            },
            navigate(navigateMethod, index){
                let wizard = this.$refs.wizard;
                if(index === wizard.activeTabIndex + 1){
                    wizard.nextTab()
                } else {
                    navigateMethod(index);
                }
            },
            nextTab() {
                let wizard = this.$refs.wizard;
                wizard.nextTab();
            },
            validateStep(name) {
                let refToValidate = this.$refs['component' + name][0];
                if (typeof refToValidate.validate !== "undefined") {
                    return refToValidate.validate();
                }
                return true;
            },
            mergePartialModels(model) {
                // merging each step model into the final model
                this.finalModel = Object.assign({},this.finalModel, model.feed);
            }
        }
    }
</script>

<style scoped>
    .vue-form-wizard .category {
        margin: 15px !important;
    }
</style>
