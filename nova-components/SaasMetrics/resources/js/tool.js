Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'SaasMetrics',
      path: '/SaasMetrics',
      component: require('./components/Tool').default,
    },
  ])
})
