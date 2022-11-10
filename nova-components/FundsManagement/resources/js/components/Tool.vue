<template>
    <div>
        <loading-view :loading="initialLoading">
            <heading class="mb-6">Zahlungsverwaltung</heading>

            <loading-card :loading="loading" class="card">
                <card class="bg-20 flex flex-col items-center justify-center p-6" style="min-height: 300px">
                    <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" @submit.prevent="onSubmit">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Empfänger</label>
                            <span v-if="errors.receiver_id" class="p-1 bg-danger-light" v-text="errors.receiver_id[0]"></span>

                            <v-select
                                :filterable="filterable"
                                :options="receiver"
                                v-model="model.receiver_id"
                                :reduce="receiver => receiver.code"
                                @search="performSearch"
                                @input="prefillPayer">
                                <template #search="{attributes, events}">
                                    <input
                                        autofocus
                                            class="vs__search"
                                            :required="!model.receiver_id"
                                            v-bind="attributes"
                                            v-on="events"
                                    />
                                </template>
                            </v-select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Sender</label>
                            <span v-if="errors.payer_id" class="p-1 bg-danger-light" v-text="errors.payer_id[0]"></span>
                            <v-select
                                :filterable="filterable"
                                :options="payer"
                                :reduce="payer => payer.code"
                                v-model="model.payer_id"
                                @search="performSearch">
                                <template #search="{attributes, events}">
                                    <input
                                            class="vs__search"
                                            :required="!model.payer_id"
                                            v-bind="attributes"
                                            v-on="events"
                                    />
                                </template>
                            </v-select>
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Höhe</label>
                            <input type="text"
                                   class="shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
                                   placeholder="Höhe des Buchungsbetrages, Nachkomma mit '.'"
                                   v-model="model.amount"
                                   required
                                    id="amount">

                            <select v-model="model.currency"
                                    class="block w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                                <option value="EUR" selected>Euro</option>
                                <option value="AFA">Afghanistan Afghani</option>
                                <option value="ALL">Albanian Lek</option>
                                <option value="DZD">Algerian Dinar</option>
                                <option value="ARS">Argentine Peso</option>
                                <option value="AWG">Aruba Florin</option>
                                <option value="AUD">Australian Dollar</option>
                                <option value="BSD">Bahamian Dollar</option>
                                <option value="BHD">Bahraini Dinar</option>
                                <option value="BDT">Bangladesh Taka</option>
                                <option value="BBD">Barbados Dollar</option>
                                <option value="BZD">Belize Dollar</option>
                                <option value="BMD">Bermuda Dollar</option>
                                <option value="BTN">Bhutan Ngultrum</option>
                                <option value="BOB">Bolivian Boliviano</option>
                                <option value="BWP">Botswana Pula</option>
                                <option value="BRL">Brazilian Real</option>
                                <option value="GBP">British Pound</option>
                                <option value="BND">Brunei Dollar</option>
                                <option value="BIF">Burundi Franc</option>
                                <option value="XOF">CFA Franc (BCEAO)</option>
                                <option value="XAF">CFA Franc (BEAC)</option>
                                <option value="KHR">Cambodia Riel</option>
                                <option value="CAD">Canadian Dollar</option>
                                <option value="CVE">Cape Verde Escudo</option>
                                <option value="KYD">Cayman Islands Dollar</option>
                                <option value="CLP">Chilean Peso</option>
                                <option value="NY">Chinese Yuan</option>
                                <option value="COP">Colombian Peso</option>
                                <option value="KMF">Comoros Franc</option>
                                <option value="CRC">Costa Rica Colon</option>
                                <option value="HRK">Croatian Kuna</option>
                                <option value="CUP">Cuban Peso</option>
                                <option value="CYP">Cyprus Pound</option>
                                <option value="CZK">Czech Koruna</option>
                                <option value="DKK">Danish Krone</option>
                                <option value="DJF">Dijibouti Franc</option>
                                <option value="DOP">Dominican Peso</option>
                                <option value="XCD">East Caribbean Dollar</option>
                                <option value="EGP">Egyptian Pound</option>
                                <option value="SVC">El Salvador Colon</option>
                                <option value="EEK">Estonian Kroon</option>
                                <option value="ETB">Ethiopian Birr</option>
                                <option value="FKP">Falkland Islands Pound</option>
                                <option value="GMD">Gambian Dalasi</option>
                                <option value="GHC">Ghanian Cedi</option>
                                <option value="GIP">Gibraltar Pound</option>
                                <option value="XAU">Gold Ounces</option>
                                <option value="GTQ">Guatemala Quetzal</option>
                                <option value="GNF">Guinea Franc</option>
                                <option value="GYD">Guyana Dollar</option>
                                <option value="HTG">Haiti Gourde</option>
                                <option value="HNL">Honduras Lempira</option>
                                <option value="HKD">Hong Kong Dollar</option>
                                <option value="HUF">Hungarian Forint</option>
                                <option value="ISK">Iceland Krona</option>
                                <option value="INR">Indian Rupee</option>
                                <option value="IDR">Indonesian Rupiah</option>
                                <option value="IQD">Iraqi Dinar</option>
                                <option value="ILS">Israeli Shekel</option>
                                <option value="JMD">Jamaican Dollar</option>
                                <option value="JPY">Japanese Yen</option>
                                <option value="JOD">Jordanian Dinar</option>
                                <option value="KZT">Kazakhstan Tenge</option>
                                <option value="KES">Kenyan Shilling</option>
                                <option value="KRW">Korean Won</option>
                                <option value="KWD">Kuwaiti Dinar</option>
                                <option value="LAK">Lao Kip</option>
                                <option value="LVL">Latvian Lat</option>
                                <option value="LBP">Lebanese Pound</option>
                                <option value="LSL">Lesotho Loti</option>
                                <option value="LRD">Liberian Dollar</option>
                                <option value="LYD">Libyan Dinar</option>
                                <option value="LTL">Lithuanian Lita</option>
                                <option value="MOP">Macau Pataca</option>
                                <option value="MKD">Macedonian Denar</option>
                                <option value="MGF">Malagasy Franc</option>
                                <option value="MWK">Malawi Kwacha</option>
                                <option value="MYR">Malaysian Ringgit</option>
                                <option value="MVR">Maldives Rufiyaa</option>
                                <option value="MTL">Maltese Lira</option>
                                <option value="MRO">Mauritania Ougulya</option>
                                <option value="MUR">Mauritius Rupee</option>
                                <option value="MXN">Mexican Peso</option>
                                <option value="MDL">Moldovan Leu</option>
                                <option value="MNT">Mongolian Tugrik</option>
                                <option value="MAD">Moroccan Dirham</option>
                                <option value="MZM">Mozambique Metical</option>
                                <option value="MMK">Myanmar Kyat</option>
                                <option value="NAD">Namibian Dollar</option>
                                <option value="NPR">Nepalese Rupee</option>
                                <option value="ANG">Neth Antilles Guilder</option>
                                <option value="NZD">New Zealand Dollar</option>
                                <option value="NIO">Nicaragua Cordoba</option>
                                <option value="NGN">Nigerian Naira</option>
                                <option value="KPW">North Korean Won</option>
                                <option value="NOK">Norwegian Krone</option>
                                <option value="OMR">Omani Rial</option>
                                <option value="XPF">Pacific Franc</option>
                                <option value="PKR">Pakistani Rupee</option>
                                <option value="XPD">Palladium Ounces</option>
                                <option value="PAB">Panama Balboa</option>
                                <option value="PGK">Papua New Guinea Kina</option>
                                <option value="PYG">Paraguayan Guarani</option>
                                <option value="PEN">Peruvian Nuevo Sol</option>
                                <option value="PHP">Philippine Peso</option>
                                <option value="XPT">Platinum Ounces</option>
                                <option value="PLN">Polish Zloty</option>
                                <option value="QAR">Qatar Rial</option>
                                <option value="ROL">Romanian Leu</option>
                                <option value="RUB">Russian Rouble</option>
                                <option value="WST">Samoa Tala</option>
                                <option value="STD">Sao Tome Dobra</option>
                                <option value="SAR">Saudi Arabian Riyal</option>
                                <option value="SCR">Seychelles Rupee</option>
                                <option value="SLL">Sierra Leone Leone</option>
                                <option value="XAG">Silver Ounces</option>
                                <option value="SGD">Singapore Dollar</option>
                                <option value="SKK">Slovak Koruna</option>
                                <option value="SIT">Slovenian Tolar</option>
                                <option value="SBD">Solomon Islands Dollar</option>
                                <option value="SOS">Somali Shilling</option>
                                <option value="ZAR">South African Rand</option>
                                <option value="LKR">Sri Lanka Rupee</option>
                                <option value="SHP">St Helena Pound</option>
                                <option value="SDD">Sudanese Dinar</option>
                                <option value="SRG">Surinam Guilder</option>
                                <option value="SZL">Swaziland Lilageni</option>
                                <option value="SEK">Swedish Krona</option>
                                <option value="TRY">Turkey Lira</option>
                                <option value="CHF">Swiss Franc</option>
                                <option value="SYP">Syrian Pound</option>
                                <option value="TWD">Taiwan Dollar</option>
                                <option value="TZS">Tanzanian Shilling</option>
                                <option value="THB">Thai Baht</option>
                                <option value="TOP">Tonga Pa'anga</option>
                                <option value="TTD">Trinidad&amp;Tobago Dollar</option>
                                <option value="TND">Tunisian Dinar</option>
                                <option value="TRL">Turkish Lira</option>
                                <option value="USD">U.S. Dollar</option>
                                <option value="AED">UAE Dirham</option>
                                <option value="UGX">Ugandan Shilling</option>
                                <option value="UAH">Ukraine Hryvnia</option>
                                <option value="UYU">Uruguayan New Peso</option>
                                <option value="VUV">Vanuatu Vatu</option>
                                <option value="VEB">Venezuelan Bolivar</option>
                                <option value="VND">Vietnam Dong</option>
                                <option value="YER">Yemen Riyal</option>
                                <option value="YUM">Yugoslav Dinar</option>
                                <option value="ZMK">Zambian Kwacha</option>
                                <option value="ZWD">Zimbabwe Dollar</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="payment-method" class="block text-gray-700 text-sm font-bold mb-2">Zahlart</label>
                            <select
                                    id="payment-method"
                                    v-model="model.payment_method"
                                    required
                                    class="block w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                                <option value=""></option>
                                <option value="1">Voucher</option>
                                <option value="2">Local Bank</option>
                                <option value="3">Foreign Bank</option>
                                <option value="4">PayPal</option>
                                <option value="5">Bill</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Erstattungsfähig</label>
                            <input type="radio" v-model="model.refundable" value="0"> Nein
                            <input type="radio" v-model="model.refundable" value="1" checked="checked"> Ja
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Bezahlt</label>
                            <input type="radio" v-model="model.is_paid" value="0"> Nein
                            <input type="radio" v-model="model.is_paid" value="1" checked="checked"> Ja
                        </div>

                        <div class="mb-4">
                            <label for="payment-comment" class="block text-gray-700 text-sm font-bold mb-2">Kommentar</label>
                            <textarea v-model="model.comment" rows="10" cols="35"  class="shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="payment-comment"></textarea>
                        </div>

                        <button  class="btn btn-default btn-primary" type="submit">
                            Zahlung buchen
                        </button>
                    </form>
                </card>
            </loading-card>
        </loading-view>
    </div>
</template>

<script>
function initialState (){
    return {
        receiver: [],
        payer: [],
        amount: 0,
        model: {
            receiver_id: null,
            payer_id: null,
            amount: null,
            refundable: 1,
            is_paid: 0,
            currency: 'EUR',
            payment_method: 5,
            comment: 'Vorab auf Rechnung',
        },
        errors: {},
        initialLoading: true,
        loading: false,
        filterable: false
    }
}

export default {
    data() {
        return initialState();
    },

    methods: {
        resetWindow: function () {
            Object.assign(this.$data, initialState());
            this.loading = false;
        },
        performSearch: function(query, loading) {
            if (query.length) {
                loading(true);
                this.search(query, loading, this);
            }
        },
        search: _.debounce((query, loading, vm) => {
            Nova.request().get('/nova-vendor/funds-management/users/find?q=' + query).then(response => {
                vm.receiver = vm.payer = response.data;
            }).catch(function (error) {
                Nova.error(error);
            }).then(() => {
                loading(false);
            });
        }, 350),
        onSubmit() {
            this.loading = true;
            Nova.request().post('/nova-vendor/funds-management/payment', this.model).then(response => {
                Nova.success(response.data);
                this.resetWindow();
            }).catch(error => {
                if(error.response.data.errors !== undefined) {
                    this.errors = error.response.data.errors;
                    Nova.error(error.response.data.message);
                } else {
                    Nova.error(error);
                }
                this.loading = false;
            }).then(() => {
                this.initialLoading = false;
            });
        },
        prefillPayer() {
            if (!this.model.payer_id) {
                this.model.payer_id = this.model.receiver_id;
            }
        }
    },

    mounted() {
        this.initialLoading = false;
        this.loading = false;
    },
}
</script>

<style>
/* Scoped Styles */
</style>
