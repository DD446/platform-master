<template>
    <b-container>
        <b-row>
            <b-col cols="12">
                <b-alert
                        :variant="avariant"
                        :show="dismissCountDown"
                        dismissible
                        fade
                        dismiss-label="X"
                        @dismissed="dismissCountDown=0"
                        @dismiss-count-down="countDownChanged"
                ><span v-html="alertText"></span></b-alert>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
    import {eventHub} from '../app';

    export default {
        name: "AlertContainer",

        data() {
            return {
                avariant: 'success',
                atext: null,
                dismissSecs: 10,
                dismissCountDown: 0,
            }
        },

        methods: {
            countDownChanged(dismissCountDown) {
                this.dismissCountDown = dismissCountDown
            },
            showAlert(msg, variant) {
                this.dismissCountDown = this.dismissSecs;
                this.alertText = msg;
                this.avariant = variant || 'success';
            },
        },

        computed: {
            alertText: {
                get() {
                    return this.atext;
                },
                set(msg) {
                    if(typeof msg === 'object') {
                        let _alert;
                        if (Object.entries(msg).length > 0) {
                            _alert = '<ul style="list-style-type:none">';
                            for (let [key, value] of Object.entries(msg)) {
                                _alert += '<li>' + value + '</li>';
                            }
                            _alert += '</ul>';
                        }
                        this.atext = _alert;
                    } else if (Array.isArray(msg) && Array.length > 1) {
                        let _alert = '<ul style="list-style-type:none">';
                        msg.forEach(function(element) {
                            _alert += '<li>' + element + '</li>';
                        });
                        _alert += '</ul>';
                        this.atext = _alert;
                    } else {
                        if (Array.isArray(msg)) {
                            msg = msg[0];
                        }
                        this.atext = msg;
                    }
                }
            }
        },

        created() {
            eventHub.$on("show-message:success", message => {
                this.showAlert(message);
            });
            eventHub.$on("show-message:error", message => {
                this.showAlert(message, 'danger');
            });
        },
    }
</script>

<style scoped>

</style>
