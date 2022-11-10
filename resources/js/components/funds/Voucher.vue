<template>
    <div>
        <alert-container></alert-container>
        <b-form @submit.prevent="onSubmit">
            <div class="row">
                <div class="col-lg-6 col-md-5 col-sm-12">
                    <b-form-group
                            :description="$t('accounting.description_voucher_field')"
                            label-for="vouchercode"
                    >
                        <b-input
                            required
                            id="vouchercode"
                            type="text"
                            :placeholder="$t('accounting.placeholder_voucher_field')"
                            v-model="code"
                        ></b-input>
                    </b-form-group>
                </div>

                <div class="col-lg-6 col-md-7 col-sm-12">
                    <b-button
                            type="submit"
                            variant="primary">{{ $t('accounting.button_voucher_submit') }}
                    </b-button>
                </div>
            </div>
        </b-form>
    </div>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "Voucher",

        data() {
            return {
                code: '',
                isLoading: false,
            }
        },

        methods: {
            onSubmit() {
                // '/podcaster/podcaster/action/redeemVoucher/?frmVoucherCode=' + this.code
                this.isLoading = true;
                let url = '/voucher/' + this.code;

                axios.put(url)
                    .then((response) => {
                        this.showMessage(response);
                    })
                    .catch(error => {
                        this.showError(error);
                    })
                    .then(() => {
                        this.code = '';
                        this.isLoading = false;
                    });
            },
        }
    }
</script>
