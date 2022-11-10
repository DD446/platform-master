Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'pioniere-management',
      path: '/pioniere-management',
      component: require('./components/Tool').default,
    },
  ])
})
