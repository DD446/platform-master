Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'pioniere-check-management',
      path: '/pioniere-check-management',
      component: require('./components/Tool').default,
    },
  ])
})
