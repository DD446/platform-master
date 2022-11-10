import VueFormWizard from "vue-form-wizard/src";
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue';
import VCalendar from 'v-calendar';
window.Vue = require('vue').default;
export const eventHub = new Vue();

let VueInternationalization = require('vue-i18n').default;
import Locale from "../js/vue-i18n-locales.generated";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import VueClipboard from 'vue-clipboard2';
import Vuex from 'vuex';
import VueRouter from "vue-router";
import StatsDashboard from "./components/statistics/StatsDashboard";
import StatsPodcasterListeners from "./components/statistics/StatsPodcasterListeners";
import StatsPodcasterSubscribers from "./components/statistics/StatsPodcasterSubscribers";
import StatsPioniere from "./components/statistics/StatsPioniere";
import StatsSpotify from "./components/statistics/StatsSpotify";
// import StatsExport from "./components/statistics/StatsExport";
// import StatsComparison from "./components/statistics/StatsComparison";
// import StatsDownloads from "./components/statistics/StatsDownloads";
import ChannelDashboard from "./components/feed/ChannelDashboard";
import ChannelDetails from "./components/feed/ChannelDetails";
import ChannelCheck from "./components/feed/ChannelCheck";
import ChannelSubmit from "./components/feed/ChannelSubmit";
import ChannelPlayer from "./components/feed/ChannelPlayer";
import AddShow from "./components/show/AddShow";
import Channel from "./components/feed/Channel";
import Shows from "./components/show/Shows";
import CreateAuphonicProduction from "./components/feed/auphonic/CreateAuphonicProduction";
import ECharts from 'vue-echarts'

window.axios = require('axios');

window.axios.interceptors.request.use(request => {

    const removeAuthHeaders = request.url.includes("auphonic.com");

    if (removeAuthHeaders){
        //delete request.headers['Access-Control-Request-Headers']
        delete request.headers.common['X-CSRF-TOKEN']
        delete request.headers.common['X-Requested-With']
        //delete request.headers.common['X-Socket-ID']
        delete request.headers['X-Socket-Id']
    }

    return request;
});


window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

Vue.use(BootstrapVue);
//Vue.use(BootstrapVueIcons);
Vue.use(Vuex);
Vue.use(ECharts);

let store = new Vuex.Store({
    state: {
    },
});

// Define routes
let routes = [];

//require('summernote');

let useEcho = function() {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: process.env.MIX_PUSHER_APP_KEY ? process.env.MIX_PUSHER_APP_KEY : process.env.PUSHER_APP_KEY,
        encrypted: process.env.MIX_WS_ENCRYPTED ? process.env.MIX_WS_ENCRYPTED : false,
        disableStats: process.env.MIX_WS_DISABLE_STATISTICS ? process.env.MIX_WS_DISABLE_STATISTICS : true,
        wsHost: process.env.MIX_WS_HOST ? process.env.MIX_WS_HOST : window.location.hostname,
        wsPort: process.env.MIX_WS_PORT ? process.env.MIX_WS_PORT : 6001,
        wssPort: process.env.MIX_WSS_PORT ? process.env.MIX_WSS_PORT : process.env.MIX_WS_PORT ? process.env.MIX_WS_PORT : 6001,
        enabledTransports: ['ws', 'wss'],
        forceTLS: process.env.MIX_WS_ENCRYPTED ? process.env.MIX_WS_ENCRYPTED : false,
        cluster: process.env.MIX_PUSHER_APP_CLUSTER,
        scheme: process.env.MIX_PUSHER_APP_SCHEME ? process.env.MIX_PUSHER_APP_SCHEME : 'http',
    });
};

let home = function() {
    let StatsButton = require('./components/user/StatsButton').default;
    Vue.component('listeners', StatsButton);
    Vue.component('subscribers', StatsButton);
    //Vue.component('downloads', StatsButton);
    Vue.component('usage', require('./components/user/Usage').default);
    Vue.component('changes', require('./components/Changes').default);
    Vue.component('like-button', require('./components/LikeButton.vue').default);
    Vue.component(
        'funds',
        require('./components/funds/Funds').default,
    );
};

let faq = function() {
    Vue.component(
        'faq-like-button',
        require('./components/LikeButton.vue').default,
    );
    Vue.prototype.$openLink = function(link) {
        useShare(link);
    }
};

let feeds = function() {
    useEcho();
    Vue.use(VueClipboard);
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'channels',
        require('./components/feed/Channels').default,
    );
    Vue.component(
        'channel-count',
        require('./components/feed/ChannelCount').default,
    );
    Vue.component(
        'fileuploader',
        require('./components/media/FileUploader').default,
    );
    routes = [
        {
            path: '/', component: ChannelDashboard, name: 'Podcasts'
        },
        {
            path: '/podcast/:feedId', component: Channel, props: true, name: 'Podcast'
        },
        {
            path: '/podcast/:feedId/episode/:guid', component: AddShow, props: true, name: 'edit-show'
        },
        {
            path: '/podcast/:feedId/episode', component: AddShow, props: true, name: 'add-show'
        },
        {
            path: '/podcast/:feedId/episoden', component: Shows, props: true, name: 'Episoden'
        },
        {
            path: '/podcast/:feedId/details',
            component: ChannelDetails,
            props: true,
            name: 'Details'
        },
        {
            path: '/podcast/:feedId/status',
            component: ChannelCheck,
            props: true,
            name: 'State'
        },
        {
            path: '/podcast/:feedId/promotion',
            component: ChannelSubmit,
            props: true,
            name: 'Submit'
        },
        {
            path: '/podcast/:feedId/player',
            component: ChannelPlayer,
            props: true,
            name: 'Player'
        },
        {
            path: '/podcast/:feedId/auphonic/:preset',
            component: CreateAuphonicProduction,
            props: true,
            name: 'create-auphonic-production'
        }
    ];
};

let addshow = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'add-show',
        require('./components/show/AddShow').default,
    );
    Vue.component(
        'fileuploader',
        require('./components/media/FileUploader').default,
    );
};

let shareshow = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'share-show',
        require('./components/show/ShareShow').default,
    );
};

let shows = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'shows',
        require('./components/show/Shows').default,
    );
};

let funds = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'funds',
        require('./components/funds/Funds').default,
    );
    Vue.component(
        'bill',
        require('./components/funds/Bill').default,
    );
    Vue.component(
        'voucher',
        require('./components/funds/Voucher').default,
    );
};

let feedwizard = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'feed-form-wizard',
        require('./components/feed/wizard/FeedFormWizard').default,
    );
    Vue.use(VueFormWizard);

    store = new Vuex.Store({
        state: {
            feed_url: false,
        },
    });
};

let fileupload = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component('usage',
        require('./components/user/Usage').default,
    );
    Vue.component(
        'fileuploader',
        require('./components/media/FileUploader').default,
    );
};

let filedownloaderdropbox = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component('usage',
        require('./components/user/Usage').default,
    );
    Vue.component(
        'file-downloader-dropbox',
        require('./components/media/FileDownloaderDropbox').default,
    );
};

let replace = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'fileuploader',
        require('./components/media/FileUploader').default,
    );
};

let login = function() {
    Vue.component(
        'login-facebook',
        require('./components/login/LoginFacebook').default,
    );
};

let social = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'login-socialite',
        require('./components/login/LoginSocialite').default,
    );
};

let help = function() {
    Vue.component(
        'help-videos-slider',
        require('./components/HelpVideosSlider').default,
    );
};

let media = function() {
    VueClipboard.config.autoSetContainer = true;
    Vue.use(VueClipboard);
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'mediatable',
        require('./components/media/MediaTable').default,
    );
};

let packages = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'package-switch',
        require('./components/package/PackageSwitch').default,
    );
};

let packageextras = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'package-extras',
        require('./components/package/PackageExtras').default,
    );
};

let playerconfigurator = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.use(VueClipboard);
    Vue.component(
        'player-configurator',
        require('./components/player/PlayerConfigurator').default,
    );
};

let review = function() {
    Vue.component(
        'citation',
        require('./components/review/Citation').default,
    );
};

let state = function() {
    useEcho();
    Vue.component(
        'state-check',
        require('./components/feed/StateCheck').default,
    );
    Vue.component(
        'state-check-group',
        require('./components/feed/StateCheckGroup').default,
    );
    Vue.component(
        'websocket-connection-check',
        require('./components/WebsocketConnectionCheck').default,
    );
};

let submit = function() {
    //useEcho();
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'submit',
        require('./components/feed/Submit').default,
    );
    Vue.component(
        'submit-item',
        require('./components/feed/SubmitItem').default,
    );
};

let subscribers = function() {
    Vue.component('subscribers',
        require('./components/statistics/StatsCounter').default,
    );
};

let spotify = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'spotify',
        require('./components/user/Spotify').default,
    );
};

let amazon = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'amazon',
        require('./components/user/Amazon').default,
    );
};

let deezer = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'deezer',
        require('./components/feed/submission/deezer/Deezer').default,
    );
};

let audiotakes = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'audiotakes-sign-up',
        require('./components/audiotakes/AudiotakesSignUp').default,
    );
    Vue.component(
        'audiotakes-transfer-funds',
        require('./components/audiotakes/AudiotakesTransferFunds').default,
    );
    Vue.component(
        'audiotakes-funds',
        require('./components/audiotakes/AudiotakesFunds').default,
    );
};

/*let spotifystats = function() {
    Vue.component(
        'spotify-stats',
        require('./components/statistics/SpotifyStats').default,
    );

    Vue.filter('dateOnly', function(value) {
        if (value) {
            //return moment(String(value)).format('DD.MM.YYYY');
            return value;
        }
    });
};*/

let statistics = function() {
    useEcho();
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'stats',
        require('./components/statistics/Stats').default,
    );

    Vue.use(VCalendar, {});

    routes = [
        {
            path: '/', component: StatsDashboard,
        },
        {
            path: '/podcaster/listeners', component: StatsPodcasterListeners, name: 'podcaster-listeners'
        },
        {
            path: '/podcaster/subscribers', component: StatsPodcasterSubscribers, name: 'podcaster-subscribers'
        },
        {
            path: '/pioniere', component: StatsPioniere, name: 'pioniere', props: true
        },
        {
            path: '/pioniere/audiotakes', component: StatsPioniere, name: 'audiotakes', props: true
        },
        {
            path: '/spotify', component: StatsSpotify, name: 'spotify'
        },
    ];
    Vue.filter('dateOnly', function(value) {
        if (value) {
            //return moment(String(value)).format('DD.MM.YYYY');
            let date = new Date(value);

            return date.toLocaleDateString();
        }
    });
    Vue.filter('dateTime', function(value) {
        if (value) {
            //return moment(String(value)).format('DD.MM.YYYY');
            let date = new Date(value);

            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        }
    });
};

let apps = function() {
    Vue.component(
        'passport-clients',
        require('./components/passport/Clients.vue').default,
    );
    Vue.component(
        'passport-authorized-clients',
        require('./components/passport/AuthorizedClients.vue').default,
    );
    Vue.component(
        'passport-personal-access-tokens',
        require('./components/passport/PersonalAccessTokens.vue').default,
    );
};

let billing = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'billing-address',
        require('./components/BillingAddress').default,
    );
    Vue.component(
        'multiselect',
        require('vue-multiselect').default,
    );
};

let teams = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'members',
        require('./components/teams/Members').default,
    );
    Vue.component(
        'member-invitation',
        require('./components/teams/MemberInvitation').default,
    );
    Vue.component(
        'member-invites',
        require('./components/teams/MemberInvites').default,
    );
    Vue.component(
        'project-login',
        require('./components/teams/ProjectLogin').default
    );
};

let projectlogin = function() {
    Vue.component(
        'project-login',
        require('./components/teams/ProjectLogin').default
    );
};

let profile = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'profile-address',
        require('./components/user/ProfileAddress').default
    );
    Vue.component(
        'billing-address',
        require('./components/BillingAddress').default,
    );
    Vue.component(
        'funds',
        require('./components/funds/Funds').default,
    );
};

let approvals = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'approvals',
        require('./components/user/Approvals').default
    );
};

let contactus = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'contactus',
        require('./components/contactus/ContactForm').default
    );
};

let order = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'order',
        require('./components/user/Order').default
    );
};

let newslettersignupform = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'newsletter-signup-form',
        require('./components/newsletter/NewsletterSignupForm').default
    );
};

/*let feeddetails = function() {
    Vue.component(
        'alert-container',
        require('./components/AlertContainer').default,
    );
    Vue.component(
        'feed-details',
        require('./components/feed/ChannelDetails').default
    );
};*/

let nav = document.getElementById('nav');

if (nav) {
    new Vue({
        el: '#nav',
        methods: {
            logout() {
                document.getElementById('logout-form').submit();
                return false;
            },
        },
        data() {
            return {
                showMenu: false,
            }
        }
    });
}

Vue.mixin({
    methods: {
        isMobile: function() {
            let check = false;
            (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
            return check;
        },
        showMessage(response) {
            eventHub.$emit('show-message:success', !response.data ? response : response?.data?.message ? response?.data?.message : response?.data ? response.data : response ? response : null);
        },
        showError(error) {
            eventHub.$emit('show-message:error', error?.response?.data?.errors ? error?.response?.data?.errors : error?.response?.data?.message ? error?.response?.data?.message : error?.message ? error.message : error.toString());
        },
        getRoute(type) {
            const query = Object.assign({}, this.$route.query);

            return '/' + type + '?' +
                Object.keys(query)
                    .map(key => {
                        return (
                            encodeURIComponent(key) + '=' + encodeURIComponent(query[key])
                        )
                    })
                    .join('&')
        }
    }
});

let sb = document.getElementById('searchbar');
if (sb) {
    Vue.component(
        'faq-search-bar',
        require('./components/faq/SearchBar').default,
    );
}

let el = document.getElementById('app');

if (el) {
    let type = el.getAttribute('data-type');
    switch (type) {
            case 'amazon' :
                amazon();
                break;
            case 'deezer' :
                deezer();
                break;
            case 'audiotakes' :
                audiotakes();
                break;
            case 'home' :
                home();
                break;
            case 'faq' :
                faq();
                break;
            case 'feeds' :
                Vue.use(VueRouter);
                feeds();
                break;
            /*        case 'feeddetails' :
                            feeddetails();
                        break;*/
            case 'funds' :
                funds();
                break;
            case 'feedwizard' :
                feedwizard();
                break;
            case 'fileupload' :
                fileupload();
                break;
            case 'filedownloaderdropbox' :
                filedownloaderdropbox();
                break;
            case 'replace' :
                replace();
                break;
            case 'help' :
                help();
                break;
            case 'login' :
                login();
                break;
            case 'media' :
                media();
                break;
            case 'packages' :
                packages();
                break;
            case 'packageextras' :
                packageextras();
                break;
            case 'playerconfigurator' :
                playerconfigurator();
                break;
            case 'review' :
                review();
                break;
            case 'submit' :
                submit();
                break;
            case 'subscribers' :
                subscribers();
                break;
            case 'spotifystats' :
                spotifystats();
                break;
            case 'spotify' :
                spotify();
                break;
            case 'social' :
                social();
                break;
            case 'addshow' :
                addshow();
                break;
            case 'shareshow' :
                shareshow();
                break;
            case 'shows' :
                shows();
                break;
            case 'state' :
                state();
                break;
            case 'statistics' :
                Vue.use(VueRouter);
                statistics();
                break;
            case 'apps' :
                apps();
                break;
            case 'billing' :
                billing();
                break;
            case 'teams' :
                teams();
                break;
            case 'projectlogin' :
                projectlogin();
                break;
            case 'profile' :
                profile();
                break;
            case 'approvals' :
                approvals();
                break;
            case 'contactus' :
                contactus();
                break;
            case 'order' :
                order();
                break;
            case 'newslettersignupform' :
                newslettersignupform();
                break;
        }


    //Vue.prototype.$scrollToTop = () => window.scrollTo(0,0);

    if (type === 'media') {
        window.media = app;
    }
}

if (el || sb) {
    Vue.use(VueInternationalization);
    const lang = document.documentElement.lang.substr(0, 2);
    const dateTimeFormats = {
        'de': {
            time: {
                hour: 'numeric', minute: 'numeric', hour12: false
            }
        },
        'en-US': {
            time: {
                hour: 'numeric', minute: 'numeric', hour12: true
            },
            short: {
                year: 'numeric', month: 'short', day: 'numeric'
            },
            long: {
                year: 'numeric', month: 'short', day: 'numeric',
                weekday: 'short', hour: 'numeric', minute: 'numeric'
            }
        },
    };
    const i18n = new VueInternationalization({
        locale: lang,
        messages: Locale,
        dateTimeFormats
    });
    // Create the router instance and pass the `routes` option
    const router = new VueRouter({
        routes, // short for `routes: routes`
        scrollBehavior (to, from, savedPosition) {
            if (savedPosition) {
                return savedPosition
            } else if (to.hash) {
                return { selector: to.hash }
            } else {
                return { x: 0, y: 0 };
            }
        }
    });

    if (el) {
        const app = new Vue({
            el: '#app',
            i18n,
            store,
            router
        });
    }

    if (sb) {
        const app = new Vue({
            el: '#searchbar',
            i18n,
        });
    }
}


/**
 * @fileoverview array_merge and supporting functions.
 * Designed to mimic PHP's array_merge function, however
 * this function also takes into account that objects are
 * used in place of associative arrays in JavaScript.
 * @author Andrew Noyes noyesa@gmail.com
 */

(function () {
    /**
     * Returns the class name of object.
     * @param object {Object}
     * @returns Class name of object
     * @type String
     */
    var getClass = function (object) {
        return Object.prototype.toString.call(object).slice(8, -1);
    };

    /**
     * Returns true of obj is a collection.
     * @param obj {Object}
     * @returns True if object is a collection
     * @type {bool}
     */
    var isValidCollection = function (obj) {
        try {
            return (
                typeof obj !== "undefined" &&  // Element exists
                getClass(obj) !== "String" &&  // weed out strings for length check
                typeof obj.length !== "undefined" &&  // Is an indexed element
                !obj.tagName &&  // Element is not an HTML node
                !obj.alert &&  // Is not window
                typeof obj[0] !== "undefined"  // Has at least one element
            );
        } catch (e) {
            return false;
        }
    };

    /**
     * Merges an array with an array-like object or
     * two objects.
     * @param arr1 {Array|Object} Array that arr2 will be merged into
     * @param arr2 {Array|NodeList|Object} Array-like object or Object to merge into arr1
     * @returns Merged array
     * @type {Array|Object}
     */
    window.array_merge = function (arr1, arr2) {
        // Variable declarations
        var arr1Class, arr2Class, i, il;

        // Save class names for arguments
        arr1Class = getClass(arr1);
        arr2Class = getClass(arr2);

        if (arr1Class === "Array" && isValidCollection(arr2)) {  // Array-like merge
            if (arr2Class === "Array") {
                arr1 = arr1.concat(arr2);
            } else {  // Collections like NodeList lack concat method
                for (i = 0, il = arr2.length; i < il; i++) {
                    arr1.push(arr2[i]);
                }
            }
        } else if (arr1Class === "Object" && arr1Class === arr2Class) {  // Object merge
            for (i in arr2) {
                if (i in arr1) {
                    if (getClass(arr1[i]) === getClass(arr2[i])) {  // If properties are same type
                        if (typeof arr1[i] === "object") {  // And both are objects
                            arr1[i] = array_merge(arr1[i], arr2[i]);  // Merge them
                        } else {
                            arr1[i] = arr2[i];  // Otherwise, replace current
                        }
                    }
                } else {
                    arr1[i] = arr2[i];  // Add new property to arr1
                }
            }
        }
        return arr1;
    };

})();
