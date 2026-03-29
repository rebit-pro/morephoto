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
      component: () => import('@/views/dashboard/DashboardPage.vue'),
      meta: {
        title: 'Дашборд',
        description: 'Обзор торговой активности, балансов и последних сделок в Rebit P2P Trader.'
      }
    },
    {
      name: 'OrderBook',
      path: '/orderbook',
      component: () => import('@/views/exchange/OrderBookPage.vue'),
      meta: {
        title: 'Стакан заявок',
        description: 'Книга ордеров — актуальные заявки на покупку и продажу криптовалюты.'
      }
    },
    {
      name: 'Trades',
      path: '/exchange/trades',
      component: () => import('@/views/exchange/TradesPage.vue'),
      meta: {
        title: 'Сделки',
        description: 'Список всех ваших P2P-сделок: активные, завершённые и отменённые.'
      }
    },
    {
      name: 'TradeDetail',
      path: '/exchange/trades/:id',
      component: () => import('@/views/exchange/TradeDetailPage.vue'),
      meta: {
        title: 'Детали сделки',
        description: 'Подробная информация о P2P-сделке, чат с контрагентом и статус оплаты.'
      }
    },
    {
      name: 'Advertisements',
      path: '/exchange/advertisements',
      component: () => import('@/views/exchange/AdvertisementsPage.vue'),
      meta: {
        title: 'Мои объявления',
        description: 'Управление вашими объявлениями на покупку и продажу криптовалюты.'
      }
    },
    {
      name: 'AdvertisementCreate',
      path: '/exchange/advertisements/create',
      component: () => import('@/views/exchange/AdvertisementCreatePage.vue'),
      meta: {
        title: 'Создать объявление',
        description: 'Размещение нового объявления на покупку или продажу криптовалюты на P2P-платформе.'
      }
    },
    {
      name: 'ChatScripts',
      path: '/exchange/chat-scripts',
      component: () => import('@/views/exchange/ChatScriptsPage.vue'),
      meta: {
        title: 'Шаблоны сообщений',
        description: 'Управление шаблонами быстрых сообщений для чата в P2P-сделках.'
      }
    },
    {
      name: 'Profile',
      path: '/profile',
      component: () => import('@/views/profile/ProfilePage.vue'),
      meta: {
        title: 'Профиль',
        description: 'Настройки вашего профиля, контактные данные и персональные параметры.'
      }
    },
    {
      name: 'ApiConnection',
      path: '/profile/api-connection',
      component: () => import('@/views/profile/ApiConnectionPage.vue'),
      meta: {
        title: 'API-подключение',
        description: 'Настройка подключения к Bybit API для автоматической синхронизации балансов.'
      }
    },
    {
      name: 'Balances',
      path: '/wallet/balances',
      component: () => import('@/views/wallet/BalancesPage.vue'),
      meta: {
        title: 'Балансы',
        description: 'Доступные и заблокированные средства по всем валютам на вашем аккаунте.'
      }
    },
    {
      name: 'Transactions',
      path: '/wallet/transactions',
      component: () => import('@/views/wallet/TransactionsPage.vue'),
      meta: {
        title: 'Транзакции',
        description: 'Полная история транзакций: пополнения, списания, блокировки и разблокировки.'
      }
    },
    {
      name: 'CashFlowReport',
      path: '/wallet/reports/cash-flow',
      component: () => import('@/views/wallet/CashFlowReportPage.vue'),
      meta: {
        title: 'Отчёт по движению средств',
        description: 'Аналитический отчёт по движению средств: доходы, расходы и итоговое сальдо.'
      }
    }
  ]
};

export default MainRoutes;
