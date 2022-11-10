import 'vue-select/dist/vue-select.css';

Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'funds-management',
            path: '/funds-management',
            component: (require('./components/Tool').default)
        }
/*        {
            name: 'unpaid-bills',
            path: '/unpaid-bills',
            component: require('./components/UnpaidBills').default,
        },*/
    ]);

    Vue.component('v-select', require('vue-select').default);
});
