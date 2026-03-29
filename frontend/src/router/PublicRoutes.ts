const PublicRoutes = {
  path: '/',
  component: () => import('@/layouts/blank/BlankLayout.vue'),
  meta: {
    requiresAuth: false
  },
  children: [
    {
      name: 'Home',
      path: '/',
      component: () => import('@/views/home/HomePage.vue'),
      meta: {
        title: 'Главная',
        description: 'Rebit P2P Trader — единое рабочее пространство для P2P-сделок, балансов, сигналов безопасности и отчётов по прибыли.'
      }
    },
    {
      name: 'Documentation',
      path: '/documentation',
      component: () => import('@/views/docs/DocumentationPage.vue'),
      meta: {
        title: 'Документация',
        description: 'Руководство пользователя Rebit P2P Trader: получение Bybit API-ключа, подключение и обзор разделов приложения.'
      }
    },
    {
      name: 'Login',
      path: '/login',
      component: () => import('@/views/authentication/LoginPage.vue'),
      meta: {
        title: 'Вход',
        description: 'Войдите в аккаунт Rebit P2P Trader для доступа к торговле и управлению балансами.'
      }
    },
    {
      name: 'Register',
      path: '/register',
      component: () => import('@/views/authentication/RegisterPage.vue'),
      meta: {
        title: 'Регистрация',
        description: 'Создайте аккаунт Rebit P2P Trader и начните торговать криптовалютой на P2P-платформе.'
      }
    },
    {
      name: 'Error 404',
      path: '/error',
      component: () => import('@/views/pages/maintenance/error/Error404Page.vue'),
      meta: {
        title: 'Страница не найдена',
        description: 'Запрашиваемая страница не найдена.'
      }
    }
  ]
};

export default PublicRoutes;
