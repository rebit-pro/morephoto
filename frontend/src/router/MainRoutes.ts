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
      name: 'Trades',
      path: '/exchange/trades',
      component: () => import('@/views/exchange/TradesPage.vue')
    },
    {
      name: 'TradeDetail',
      path: '/exchange/trades/:id',
      component: () => import('@/views/exchange/TradeDetailPage.vue')
    },
    {
      name: 'Advertisements',
      path: '/exchange/advertisements',
      component: () => import('@/views/exchange/AdvertisementsPage.vue')
    },
    {
      name: 'AdvertisementCreate',
      path: '/exchange/advertisements/create',
      component: () => import('@/views/exchange/AdvertisementCreatePage.vue')
    },
    {
      name: 'ChatScripts',
      path: '/exchange/chat-scripts',
      component: () => import('@/views/exchange/ChatScriptsPage.vue')
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
    },
    {
      name: 'CashFlowReport',
      path: '/wallet/reports/cash-flow',
      component: () => import('@/views/wallet/CashFlowReportPage.vue')
    }
  ]
};

export default MainRoutes;
