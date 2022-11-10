window.podcasterPlayerConfig = {
    default_album_art: "{{ $config->default_album_art }}",
    default_description: "",
    bindings: {
        37: 'prev',
        39: 'next',
        32: 'play_pause'
    },
    playback_speed: "{{ $config->initial_playback_speed }}",
    @if($config->debug_player)
    debug: true,
    @endif
    delay: "{{ $config->delay_between_audio }}",
    @if($config->enable_shuffle)
    shuffle_on: true,
    @endif
    @if($config->preload)
    preload: '{{$config->preload}}',
    @endif
    songs: [
        {!! $shows !!}
    ],
    callbacks: {
        stop: function() {
            @if($config->player_type == 1)
            setPlayState(Amplitude.getPlayerState());
            @endif
        },
        initialized: function () {
            @if($config->player_type == 1)
                if (Amplitude.getSongs().length <= 1) {
                    document.getElementById('next-container').style.display = 'none';
                }
                @if($config->show_playlist)
                    fillPlaylistWithSongs(Amplitude.getSongs());
                @endif
            @endif
            @if($config->player_type != 3 && ($config->player_type == 2 && $config->hide_playlist_in_singlemode
                || $config->player_configurable_type == App\Models\PlayerConfig::TYPE_SHOW))
            if (Amplitude.getSongs().length <= 1) {
                document.getElementById('menu').classList.remove('displayed-list');
                document.getElementById('list-screen').classList.remove('slide-in-left');
                document.getElementById('list-screen').classList.add('slide-out-left');
                document.getElementById('menu').style.display = 'none';
            }
            @endif
            @if($config->player_type == 3)
                if (Amplitude.getSongs().length > 0) {
                    setShownotes();
                }
            @endif
        },
        song_change: function () {
            @if($config->player_type == 3)
                setShownotes();
            @endif
        }
    }
};

@if($config->background_color || $config->text_color || $config->icon_color)
function createCSSSelector (selector, style) {
    if (!document.styleSheets) return;
    if (document.getElementsByTagName('head').length === 0) return;

    var styleSheet,mediaType;

    if (document.styleSheets.length > 0) {
        for (var i = 0, l = document.styleSheets.length; i < l; i++) {
            if (document.styleSheets[i].disabled)
                continue;
            var media = document.styleSheets[i].media;
            mediaType = typeof media;

            if (mediaType === 'string') {
                if (media === '' || (media.indexOf('screen') !== -1)) {
                    styleSheet = document.styleSheets[i];
                }
            }
            else if (mediaType === 'object') {
                if (media.mediaText === '' || (media.mediaText.indexOf('screen') !== -1)) {
                    styleSheet = document.styleSheets[i];
                }
            }

            if (typeof styleSheet !== 'undefined')
                break;
        }
    }

    if (typeof styleSheet === 'undefined') {
        var styleSheetElement = document.createElement('style');
        styleSheetElement.type = 'text/css';
        document.getElementsByTagName('head')[0].appendChild(styleSheetElement);

        for (i = 0; i < document.styleSheets.length; i++) {
            if (document.styleSheets[i].disabled) {
                continue;
            }
            styleSheet = document.styleSheets[i];
        }
        mediaType = typeof styleSheet.media;
    }
    var i = 0;
    if (mediaType === 'string') {
        for (i = 0, l = styleSheet.rules.length; i < l; i++) {
            if(styleSheet.rules[i].selectorText && styleSheet.rules[i].selectorText.toLowerCase() === selector.toLowerCase()) {
                styleSheet.rules[i].style.cssText = style;
                return;
            }
        }
        styleSheet.addRule(selector,style);
    }
    else if (mediaType === 'object') {
        var styleSheetLength = (styleSheet.cssRules) ? styleSheet.cssRules.length : 0;
        for (i = 0; i < styleSheetLength; i++) {
            if (styleSheet.cssRules[i].selectorText && styleSheet.cssRules[i].selectorText.toLowerCase() === selector.toLowerCase()) {
                styleSheet.cssRules[i].style.cssText = style;
                return;
            }
        }
        styleSheet.insertRule(selector + '{' + style + '}', styleSheetLength);
    }
}
@endif

@if($config->player_type == 3)
    function setShownotes() {
        var raw = Amplitude.getActiveSongMetadata();
        document.getElementById('embed-shownotes').innerHTML = decodeHtml(raw.description);
    }
@endif

@if($config->background_color)
createCSSSelector('.global-background-color', 'background-color: {{$config->background_color}}');
createCSSSelector('.global-icon-fg-color', 'fill: {{$config->background_color}}');
@endif

@if($config->text_color)
createCSSSelector('.global-text-color', 'color: {{$config->text_color}}');
@endif

@if($config->icon_color)
createCSSSelector('.global-icon-bg-color', 'fill: {{$config->icon_color}}');
@endif

@if($config->icon_fg_color)
createCSSSelector('.global-icon-fg-color', 'fill: {{$config->background_color}}');
    {{--createCSSSelector('.global-icon-fg-color', 'fill: {{$config->icon_fg_color}}');--}}
@endif

{{--@if($config->progressbar_color)
    createCSSSelector('.progressbar-color', 'fill: {{$config->progressbar_color}}');
@endif--}}

@if($config->sharing)
var sharingLinks = {!! $sharingLinks !!};
function enableSubscriptionModule() {
    var sm = document.getElementById('subscription-module');
    var services = {
        spotify: '<svg class="w-6 h-6 mr-4 cursor-pointer inline-block" xmlns="http://www.w3.org/2000/svg" id="spotify" viewBox="0 0 100 100" y="0px" x="0px" width="100%" height="100%"><g><path class="global-icon-bg-color" d="M 77.904356,44.524865 C 62.836639,35.576947 37.983475,34.755 23.597466,39.121833 c -2.310021,0.701186 -4.752486,-0.603799 -5.449777,-2.913819 -0.701186,-2.31002 0.599904,-4.752485 2.913819,-5.453673 16.512941,-5.013482 43.960495,-4.043508 61.307068,6.252246 2.076291,1.234867 2.758,3.914958 1.527028,5.991249 -1.230971,2.076291 -3.914958,2.761896 -5.991248,1.527029 z m -0.490831,13.256318 c -1.05957,1.714012 -3.299472,2.251587 -5.013482,1.199809 -12.562925,-7.720843 -31.717,-9.960744 -46.582152,-5.449779 -1.924369,0.584323 -3.961705,-0.502516 -4.546027,-2.426883 -0.580427,-1.928263 0.506413,-3.957808 2.43078,-4.546025 16.976503,-5.149826 38.082215,-2.656718 52.511074,6.21329 1.71401,1.05178 2.251587,3.299472 1.199807,5.009588 z m -5.722461,12.726534 c -0.837527,1.379 -2.633343,1.811398 -4.004553,0.969975 -10.977463,-6.708017 -24.794732,-8.223359 -41.066152,-4.507072 -1.565984,0.358385 -3.128071,-0.623276 -3.486456,-2.189259 -0.358384,-1.569879 0.619382,-3.131965 2.189261,-3.49035 17.806241,-4.070777 33.080418,-2.317811 45.401821,5.212151 1.375105,0.83753 1.807504,2.633346 0.966079,4.004555 z M 50.254237,3.0847457 c -25.815346,0 -46.7457623,20.9304163 -46.7457623,46.7457623 0,25.819243 20.9304163,46.745762 46.7457623,46.745762 25.819243,0 46.745762,-20.926519 46.745762,-46.745762 0,-25.815346 -20.926519,-46.7457623 -46.745762,-46.7457623 z" id="path3"/></g></svg>',
        rss: '<svg class="w-6 h-6 mr-4 cursor-pointer inline-block" xmlns="http://www.w3.org/2000/svg" id="rss" xml:space="preserve" viewBox="0 0 291.319 291.319" y="0px" x="0px" width="100%" height="100%"><g><path class="global-icon-bg-color" id="path2" d="M145.659,0c80.44,0,145.66,65.219,145.66,145.66c0,80.45-65.219,145.659-145.66,145.659 S0,226.109,0,145.66C0,65.219,65.21,0,145.659,0z"/><path class="global-icon-fg-color" style="fill-opacity:1" id="path4" d="m 102.20251,168.05613 c -11.618926,0 -21.05035,9.42103 -21.05035,21.05035 0,11.62932 9.421029,21.05035 21.05035,21.05035 11.62931,0 21.05034,-9.42103 21.05034,-21.05035 0,-11.62932 -9.42218,-21.05035 -21.05034,-21.05035 z m -6.214856,-52.50922 c -8.170205,0 -14.783521,6.61332 -14.783521,14.78352 0,8.15866 6.613316,14.78353 14.783521,14.78353 27.715636,0 50.269746,22.55295 50.269746,50.26974 0,8.15866 6.61331,14.78352 14.79391,14.78352 8.14826,0 14.78352,-6.62371 14.78352,-14.78352 0,-44.02486 -35.82231,-79.83679 -79.847176,-79.83679 z m 0.28412,-52.58313 c -8.348069,0 -15.119614,6.771545 -15.119614,15.119614 0,8.34807 6.771545,15.119615 15.119614,15.119615 56.157746,0 101.853836,45.685701 101.853836,101.843441 0,8.34807 6.76115,15.11962 15.11962,15.11962 8.35846,0 15.11961,-6.78194 15.11961,-15.11962 C 228.35445,122.2122 169.09563,62.96378 96.271774,62.96378 Z"/></g></svg>',
        deezer: '<svg class="w-6 h-6 mr-4 cursor-pointer inline-block" id="deezer" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 97.75 97.75" width="100%" height="100%" xml:space="preserve"><g><path class="global-icon-bg-color" d="M48.875,0C21.883,0,0,21.882,0,48.875S21.883,97.75,48.875,97.75S97.75,75.868,97.75,48.875S75.867,0,48.875,0z M25.676,69.248H12.365v-4.033h13.311V69.248z M25.676,64.006H12.365V59.97h13.311V64.006z M25.676,58.762H12.365v-4.035h13.311 V58.762z M25.676,53.516H12.365v-4.033h13.311V53.516z M25.676,48.271H12.365v-4.034h13.311V48.271z M40.604,69.248H27.291v-4.033 h13.313V69.248z M40.604,64.006H27.291V59.97h13.313V64.006z M40.604,58.762H27.291v-4.035h13.313V58.762z M55.531,69.248H42.219 v-4.033h13.313L55.531,69.248L55.531,69.248z M55.531,64.006H42.219V59.97h13.313L55.531,64.006L55.531,64.006z M55.531,58.762 H42.219v-4.035h13.313L55.531,58.762L55.531,58.762z M55.531,53.516H42.219v-4.033h13.313L55.531,53.516L55.531,53.516z M55.531,48.271H42.219v-4.034h13.313L55.531,48.271L55.531,48.271z M55.531,43.026H42.219v-4.034h13.313L55.531,43.026 L55.531,43.026z M55.531,37.783H42.219v-4.035h13.313L55.531,37.783L55.531,37.783z M70.457,69.248H57.145v-4.033h13.313 L70.457,69.248L70.457,69.248z M70.457,64.006H57.145V59.97h13.313L70.457,64.006L70.457,64.006z M70.457,58.762H57.145v-4.035 h13.313L70.457,58.762L70.457,58.762z M70.457,53.516H57.145v-4.033h13.313L70.457,53.516L70.457,53.516z M70.457,48.271H57.145 v-4.034h13.313L70.457,48.271L70.457,48.271z M85.385,69.248H72.072v-4.033h13.312V69.248z M85.385,64.006H72.072V59.97h13.312 V64.006z M85.385,58.759H72.072v-4.034h13.312V58.759z M85.385,53.516H72.072V49.48h13.312V53.516z M85.385,48.271H72.072v-4.037 h13.312V48.271z M85.385,43.025H72.072v-4.033h13.312V43.025z M85.385,37.78H72.072v-4.033h13.312V37.78z M72.072,32.536v-4.034 h13.312v4.034H72.072z"/></g></svg>',
        homepage: '<svg class="w-6 h-6 mr-4 cursor-pointer inline-block" xmlns="http://www.w3.org/2000/svg" id="Homepage" xml:space="preserve" x="0px" y="0px" viewBox="0 0 291.319 291.319" width="100%" height="100%"><g id="g7" class="global-icon-bg-color"><g style="display:inline" id="g5"><path class="" d="m 145.659,0 c 80.44,0 145.66,65.219 145.66,145.66 0,80.45 -65.219,145.659 -145.66,145.659 C 65.218,291.319 0,226.109 0,145.66 0,65.219 65.21,0 145.659,0 Z" id="path2" /><path class="global-icon-fg-color" id="house" d="M 235.80782,122.29836 151.44138,48.353662 c -2.34091,-2.051097 -5.83815,-2.051097 -8.17857,0 L 58.896369,122.29836 c -2.576154,2.25804 -2.834216,6.17761 -0.576173,8.75475 2.258043,2.57566 6.178104,2.83422 8.754258,0.57568 l 6.532443,-5.72501 v 92.26413 c 0,3.42577 2.777144,6.20341 6.203414,6.20341 h 47.013449 41.05668 47.01294 c 3.42627,0 6.20341,-2.77764 6.20341,-6.20341 v -92.26364 l 6.53245,5.72551 c 1.17765,1.03126 2.6357,1.53845 4.08631,1.53845 1.72504,0 3.44116,-0.71513 4.66745,-2.11462 2.25953,-2.57764 2.00147,-6.49671 -0.57518,-8.75525 z m -102.78064,89.66613 v -51.10224 h 28.64935 v 51.10274 z m 75.66378,-96.88691 v 96.88691 h -34.60612 v -57.30565 c 0,-3.42577 -2.77814,-6.20342 -6.20341,-6.20342 h -41.05668 c -3.42577,0 -6.20341,2.77765 -6.20341,6.20342 v 57.30615 H 86.014221 v -96.88741 c 0,-0.0159 -0.002,-0.0308 -0.002,-0.0462 l 61.339889,-53.763724 61.34085,53.763254 c 4.9e-4,0.0164 -0.002,0.0308 -0.002,0.0466 z" /></g></g></svg>',
        podcast: '<svg class="w-6 h-6 mr-4 cursor-pointer inline-block" xmlns="http://www.w3.org/2000/svg" id="podcast" x="0px" y="0px" viewBox="0 0 400 400" width="100%" height="100%" xml:space="preserve"><path d="M 200.20118,1.832556e-6 C 310.59356,1.832556e-6 400.09866,89.503733 400.09866,199.89747 c 0,110.40609 -89.50374,199.8961 -199.89748,199.8961 C 89.807442,399.79357 0.30508444,310.30219 0.30508444,199.89747 0.30508444,89.503733 89.796464,1.832556e-6 200.20118,1.832556e-6 Z" id="circle" class="global-icon-bg-color" /><g id="g30" transform="matrix(1.2583682,0,0,1.2583682,-51.533869,-304.16253)" class="global-icon-fg-color"><path d="m 109.08202,376.73176 c 6.047,-4.234 11.231,-7.666 19.874,-7.666 11.665,0 29.523,8.123 29.523,31.807 0,27.003 -14.976,35.012 -31.252,35.012 -6.769,0 -12.242,-2.402 -18.145,-5.948 v 54.889 H 87.764017 v -114.387 h 21.318003 z m 0,39.131 c 3.311,4.003 7.487,7.553 13.823,7.553 12.817,0 14.691,-12.588 14.691,-20.256 0,-7.091 -2.45,-18.99 -14.258,-18.99 -6.193,0 -10.514,3.43 -14.258,6.978 v 24.715 z" id="path3" class="global-icon-fg-color"/><path d="m 164.03002,402.47476 c 0,-17.047 11.812,-33.409 35.431,-33.409 23.62,0 35.43,16.362 35.43,33.409 0,16.935 -11.952,33.41 -35.43,33.41 -23.475,0 -35.431,-16.475 -35.431,-33.41 m 48.969,0 c 0,-8.007 -2.018,-20.938 -13.539,-20.938 -11.523,0 -13.54,12.932 -13.54,20.938 0,8.01 2.016,20.939 13.54,20.939 11.521,0 13.539,-12.93 13.539,-20.939" id="path4" class="global-icon-fg-color"/><path d="m 292.14502,429.13376 c -6.481,4.576 -11.523,6.75 -20.45,6.75 -16.276,0 -31.254,-8.009 -31.254,-35.012 0,-23.684 17.86,-31.807 29.525,-31.807 8.642,0 13.824,3.432 19.875,7.666 v -60.427 h 21.313 v 118.207 h -17.136 z m -16.563,-46.68 c -11.811,0 -14.258,11.898 -14.258,18.991 0,7.667 1.872,20.022 14.69,20.022 6.337,0 10.512,-3.316 13.826,-7.321 v -24.712 c -3.745,-3.55 -8.064,-6.98 -14.258,-6.98" id="path6" class="global-icon-fg-color"/><path d="m 114.99202,488.70476 16.819,-13.299 c 6.496,9.427 82.732,60.224 143.288,-16.688 l 19.437,8.783 c -56.12,81.362 -149.667,54.804 -179.544,21.204" id="path22" class="global-icon-fg-color"/><path d="m 148.23802,463.00976 12.341,-9.746 c 5.41,6.212 48.488,27.255 81.136,-9.564 l 14.463,6.457 c -31.257,41.061 -85.676,33.797 -107.94,12.853" id="path24" class="global-icon-fg-color"/><path d="m 240.88702,341.54876 -12.531,10.079 c -2.737,-3.012 -42.123,-25.818 -77.299,9.906 l -14.476,-6.669 c 33.452,-39.271 81.1,-31.331 104.306,-13.316" id="path26" class="global-icon-fg-color"/><path d="m 273.76102,315.12376 -16.885,13.502 c -53.123,-41.328 -111.692,-18.782 -138.949,17.465 l -19.582003,-8.772 c 26.181003,-41.621 105.459003,-81.531 175.416003,-22.195" id="path28" class="global-icon-fg-color"/></g></svg>',
        itunes: '<svg class="w-6 h-6 mr-4 cursor-pointer inline-block" xmlns="http://www.w3.org/2000/svg" id="itunes" x="0px" y="0px" viewBox="0 0 400 400" width="100%" height="100%" xml:space="preserve"><path d="M 200.20118,1.832556e-6 C 310.59356,1.832556e-6 400.09866,89.503733 400.09866,199.89747 c 0,110.40609 -89.50374,199.8961 -199.89748,199.8961 C 89.807442,399.79357 0.30508444,310.30219 0.30508444,199.89747 0.30508444,89.503733 89.796464,1.832556e-6 200.20118,1.832556e-6 Z" id="path2" class="global-icon-bg-color" /><path class="global-icon-fg-color" d="m 188.91696,378.37656 c -13.268,-4.74387 -16.11432,-11.19256 -21.55494,-48.98042 -6.33009,-43.92523 -7.70878,-71.11352 -4.04711,-79.7266 4.86246,-11.41493 18.05634,-17.89327 36.51293,-17.96739 18.30836,-0.0741 31.62083,6.46352 36.51295,17.96739 3.67649,8.59826 2.29781,35.80137 -4.03229,79.7266 -4.29913,30.68689 -6.67106,38.44014 -12.6009,43.65839 -8.15352,7.21958 -19.71669,9.22089 -30.68686,5.33685 z M 132.24259,333.30983 C 86.434624,310.77647 57.081953,272.64764 46.408254,221.91793 43.73983,208.82782 43.295092,177.59243 45.815271,165.58452 52.486333,133.34105 65.235473,108.12444 86.434624,85.828267 116.97327,53.629276 156.25841,36.610656 199.99091,36.610656 c 43.28778,0 82.42467,16.707304 112.22208,47.942697 22.68161,23.571087 35.43075,48.520857 41.95357,81.386957 2.22369,10.94054 2.22369,40.7676 0.14824,53.072 -6.8193,38.8997 -28.46319,74.33045 -60.03955,98.13873 -11.26668,8.50931 -38.8404,23.36354 -43.28778,23.36354 -1.6307,0 -1.77895,-1.69 -1.03772,-8.52414 1.33421,-10.97019 2.66843,-13.25317 8.89475,-15.8623 9.93247,-4.15088 26.83249,-16.18844 37.2097,-26.5953 17.93774,-17.7895 31.13162,-41.06409 37.2097,-65.52465 3.85439,-15.26932 3.40965,-49.21761 -0.88948,-64.93167 -13.49037,-49.95883 -54.25796,-88.799239 -104.2168,-99.176446 -14.52809,-2.964916 -40.91583,-2.964916 -55.59217,0 C 122.01363,70.277281 80.2083,111.04488 67.45916,162.33793 c -3.409654,13.93511 -3.409654,47.8834 0,61.81851 8.450012,33.94829 30.390392,65.07991 59.15009,83.61064 5.63334,3.70614 12.45265,7.56053 15.26932,8.7465 6.22632,2.66842 7.56053,4.89211 8.7465,15.8623 0.74123,6.67106 0.59298,8.59826 -1.03772,8.59826 -1.03772,0 -8.59826,-3.26141 -16.60353,-7.1158 z m 0.59298,-60.3064 c -15.41756,-12.3044 -29.05618,-34.12618 -34.689526,-55.53288 -3.409654,-12.92703 -3.409654,-37.50619 0.148246,-50.40358 9.33949,-34.80811 34.98602,-61.78885 70.56501,-74.463869 12.15616,-4.299129 39.13689,-5.262727 54.10971,-1.956845 51.58955,11.489054 88.20626,63.152714 81.09046,114.401294 -2.81667,20.65064 -9.93247,37.60997 -22.53336,53.3685 -6.22632,7.9608 -21.3474,21.31774 -24.01582,21.31774 -0.44474,0 -0.88948,-5.04035 -0.88948,-11.17773 v -11.20738 l 7.70878,-9.19124 c 29.05618,-34.80812 26.98074,-83.43275 -4.74386,-115.33525 -12.3044,-12.423 -26.536,-19.71669 -44.91848,-23.08187 -11.85967,-2.19404 -14.37985,-2.19404 -26.83248,-0.14825 -18.90134,3.08352 -33.54803,10.40686 -46.54919,23.2746 -31.87285,31.57635 -33.94829,80.453 -4.89211,115.29077 l 7.64949,9.19124 v 11.26668 c 0,6.22632 -0.48922,11.26668 -1.09702,11.26668 -0.59299,0 -4.89212,-2.96492 -9.48774,-6.67106 z m 51.4413,-60.52876 c -13.19388,-6.13738 -20.30968,-17.71538 -20.45792,-32.8068 0,-13.56449 7.56053,-25.39451 20.60617,-32.31759 8.30175,-4.35843 22.97808,-4.35843 31.27985,0.0296 9.04299,4.69939 16.45528,13.81651 19.27195,23.33389 8.59826,29.23408 -22.38511,54.85095 -50.40356,41.76085 z" id="path12" /></svg>',
        amazon: '<svg id="amazon" class="w-6 h-6 mr-4 cursor-pointer inline-block" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve"><g id="XMLID_1_"><path class="global-icon-bg-color" d="M256,0C114.5,0,0,114.5,0,256s114.5,256,256,256s256-114.5,256-256S397.5,0,256,0z M278.3,146.2 c-4.7-7.4-14-11.2-22.3-11.2h-2.8c-14,1.9-26.1,9.3-29.8,24.2c-0.9,3.7-2.8,6.5-6.5,7.4l-39.1-3.7c-2.8-0.9-6.5-2.8-5.6-8.4 c8.4-43.8,45.6-59.6,81-61.4h8.4c19.5,0,44.7,5.6,60.5,20.5c19.5,17.7,17.7,42.8,17.7,68.9v62.4c0,18.6,7.4,27,14.9,37.2 c1.9,3.7,2.8,7.4-0.9,10.2c-7.4,6.5-22.3,19.5-30.7,26.1c-2.8,1.9-6.5,1.9-9.3,0.9c-13-11.2-14.9-15.8-23.3-26.1 c-13,14-24.2,21.4-37.2,26.1c-8.4,1.9-17.7,3.7-28.9,3.7c-33.5,0-59.6-20.5-59.6-62.4c0-32.6,17.7-54.9,42.8-65.2 c13-6.5,28.9-9.3,45.6-11.2c10.2-1.9,20.5-1.9,29.8-2.8v-5.6C283,165.7,283.9,154.5,278.3,146.2z M383.5,364.9 c-3.7,2.8-8.4,6.5-13,8.4l0,0c-32.6,20.5-75.4,33.5-112.6,34.4c-0.9,0-2.8,0-3.7,0c-58.6,0-102.4-32.6-142.4-67 c-1.9-1.9-3.7-3.7-3.7-6.5c0-1.9,0.9-3.7,1.9-5.6c1.9-0.9,3.7-1.9,5.6-1.9c1.9,0,2.8,0,4.7,0.9c42.8,23.3,84.7,47.5,136.8,47.5 c0.9,0,2.8,0,3.7,0c32.6-0.9,68.9-8.4,102.4-22.3l0,0l2.8-0.9l2.8-0.9c1.9-0.9,3.7-1.9,4.7-1.9c1.9-0.9,2.8-0.9,4.7-0.9 c4.7,0,9.3,3.7,9.3,9.3C388.2,360.3,386.3,363.1,383.5,364.9z M403.1,379.8c-1.9,0.9-3.7,1.9-5.6,1.9l0,0c-1.9,0-3.7-0.9-4.7-1.9 c-1.9-0.9-1.9-3.7-1.9-4.7c0-0.9,0-1.9,0.9-2.8c1.9-3.7,3.7-10.2,5.6-15.8c1.9-5.6,3.7-12.1,3.7-15.8v-1.9l0,0l-1.9-0.9 c-1.9,0-4.7-0.9-7.4-0.9c-3.7,0-8.4,0-13,0.9c-4.7,0.9-9.3,0.9-12.1,1.9l0,0l0,0h-0.9c-1.9,0-2.8,0-4.7-1.9 c-1.9-0.9-2.8-2.8-1.9-4.7c0-2.8,1.9-5.6,3.7-6.5c3.7-2.8,7.4-4.7,11.2-6.5l0,0l3.7-0.9l0,0l6.5-1.9l0,0c5.6-0.9,12.1-1.9,17.7-1.9 c7.4,0,14,0.9,17.7,2.8c1.9,0.9,2.8,0.9,3.7,2.8l0,0l0,0c0.9,1.9,1.9,4.7,1.9,7.4v1.9C423.6,343.5,418.9,365.8,403.1,379.8z"/><path class="global-icon-bg-color" d="M283,212.2c-10.2,0-20.5,0.9-29.8,2.8c-16.8,4.7-29.8,14.9-29.8,37.2c0,17.7,9.3,28.9,24.2,28.9 c1.9,0,3.7-0.9,5.6-0.9c9.3-1.9,17.7-8.4,22.3-17.7c8.4-14,7.4-26.1,7.4-42.8C283,219.7,283,212.2,283,212.2z"/></g></svg>',
        google: '<svg id="google" class="w-6 h-6 mr-4 cursor-pointer inline-block" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 1000 1000" xml:space="preserve"><path class="global-icon-bg-color" d="M500,10C229.4,10,10,229.4,10,500s219.4,490,490,490s490-219.4,490-490S770.6,10,500,10z M507.3,867.5c-203.1,0-367.5-164.4-367.5-367.5s164.4-367.5,367.5-367.5c99.1,0,182.2,36.2,246.1,96.1l-99.7,96.1c-27.4-26.2-75-56.7-146.4-56.7c-125.6,0-227.8,103.9-227.8,232s102.4,232,227.8,232c145.5,0,200-104.5,208.4-158.5H507.3V447.6h347c3.1,18.4,5.7,36.8,5.7,60.9C860.2,718.4,719.5,867.5,507.3,867.5L507.3,867.5z"/></svg>',
    };
    if (sm) {
        var sus = document.getElementById('subscription-services');
        Object.keys(sharingLinks).forEach(function(service){
            var url = sharingLinks[service];
            if (services[service] != undefined) {
                sus.insertAdjacentHTML('beforeend', '<a href="' + url + '" target="_blank" rel="noopener noreferrer" title="' + service + '">' + services[service] + '</a>');
            }
        });
        sm.style.display = 'flex';
    }
}
enableSubscriptionModule();
@else
    var sm = document.getElementById('subscription-module');
    if (sm) {
        sm.remove();
    }
@endif
@if($config->show_playlist)
    function getPlaylistItem(song, index) {
        return '<div class="song amplitude-song-container amplitude-play-pause w-full flex p-4 mt-4 border-2 playlist-border-color" data-amplitude-song-index="' + index + '">'
            + '<div class="w-1/4"><img class="w-32" src="' + song.podcast_episode_cover_art_url + '" alt="Logo"></div>'
            + '<div class="w-3/4 ml-3 xs:ml-3 mt-0 xs:mt-1 flex">'
            + '<div class="w-5/6 xs:w-4/6 sm:w-4/6 md:w-4/6 h-12 overflow-hidden px-0 xs:px-1 song-meta-container">'
            // + '<span class="song-number-now-playing">'
            // + '<span class="number">' + ++index + '</span>'
            // + '<img class="now-playing h-4 w-4 fill-current" src="/simpleplayer/images/play.svg" alt="Pause" />'
            // + '</span>'
            + '<div class="song-name-container">'
            + '<span class="song-name" data-amplitude-song-info="name" data-amplitude-song-index="' + index + '">' + song.name + '</span>'
            //+ '<span class="artist" data-amplitude-song-info="artist" data-amplitude-song-index="' + index + '">' + song.artist + '</span>'
            + '</div>'
            + '</div>'
            + '<div class="xs:block xs:w-1/6 song-duration-container">'
            + '<span class="song-duration pl-1">'
            + '<span class="amplitude-duration-time" data-amplitude-song-index="' + index + '">' + song.duration + '</span>'
            + '</span>'
            + '</div>'
            + '<div class="w-1/6 p-1">'
            @if($config->show_info)
            + '<button class="info-button w-4 h-4 md:w-6 md:h-6 float-right" onclick="event.stopImmediatePropagation();showInfoButton(' + index + ');">'
            + '<svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26.458333 26.458334" id="svg8"><title>Infos</title><g id="layer1" transform="translate(0,-270.54163)"><path class="global-icon-bg-color" d="M 2.2324219,2.2304688 2.5898438,96.873047 97.767578,97.765625 97.589844,2.4082031 Z m 8.2695311,8.4277342 h 79.15625 V 89.337891 L 10.341797,88.861328 Z" transform="matrix(0.26458333,0,0,0.26458333,0,270.54163)" id="path826" /><text xml:space="preserve" class="global-icon-bg-color" id="global-info-button-text"><tspan id="tspan831" x="11.040037" y="290.44492">i</tspan></text></g></svg>'
            + '</button>'
            @endif
            + '</div>'
            + '</div>'
            + '</div>';
    }

    function fillPlaylistWithSongs(songs) {
        var list = document.getElementById('playlist');
        var i = 0;
        songs.forEach(function(song) {
            list.insertAdjacentHTML('beforeend', getPlaylistItem(song, i++));
        });
        var songList = list.getElementsByClassName('song');
        Array.from(songList).forEach(function(element) {
            element.addEventListener('click', changePlayState);
        });
        Amplitude.bindNewElements();
    }
    function enablePlaylist() {
        var pl = document.getElementById('playlist');

        if (pl) {
            pl.style.display = 'block';
        }
    }
    enablePlaylist();
@else
    var pl = document.getElementById('playlist');
    if (pl) {
        pl.remove();
    }
@endif

function setSvgListener() {
    var bs = document.querySelector("button > svg");
    if (bs) {
        bs.addEventListener("click", function(e) {
            e.stopPropagation();
            e.preventDefault();
        });
    }
}

@if($config->show_info || $config->player_type == 3)
    function decodeHtml(html) {
        var txt = document.createElement("textarea");
        txt.innerHTML = decodeURIComponent(html);
        return txt.value;
    }
@endif

@if($config->show_info)
    var ib = document.getElementById('global-info-button');
    if (ib) {
        ib.style.display = 'block';
    }

    document.getElementById('close-container').addEventListener('click', function( e ){
        document.getElementById('pop-up').classList.add('hidden');
    });
@else
    var ib = document.getElementById('global-info-button');
    if (ib) {
        ib.remove();
    }
@endif

window.onload = function () {
    setSvgListener();
}
