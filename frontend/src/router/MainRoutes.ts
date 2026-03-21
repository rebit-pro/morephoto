const MainRoutes = {
  path: '/main',
  meta: {
    requiresAuth: true
  },
  redirect: '/dashboard',
  component: () => import('@/layouts/full/FullLayout.vue'),
  children: [
    {
      name: 'Dashboard',
      path: '/dashboard',
      component: () => import('@/views/dashboard/DashboardPage.vue')
    },
    {
      name: 'OrderBook',
      path: '/orderbook',
      component: () => import('@/views/exchange/OrderBookPage.vue')
    },
    {
      name: 'Profile',
      path: '/profile',
      component: () => import('@/views/profile/ProfilePage.vue')
    },
    {
      name: 'ApiConnection',
      path: '/profile/api-connection',
      component: () => import('@/views/profile/ApiConnectionPage.vue')
    },
    {
      name: 'Balances',
      path: '/wallet/balances',
      component: () => import('@/views/wallet/BalancesPage.vue')
    },
    {
      name: 'Transactions',
      path: '/wallet/transactions',
      component: () => import('@/views/wallet/TransactionsPage.vue')
    }
  ]
};

export default MainRoutes;
