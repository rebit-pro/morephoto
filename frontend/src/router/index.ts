import { createRouter, createWebHistory } from 'vue-router';
import MainRoutes from './MainRoutes';
import PublicRoutes from './PublicRoutes';
import { useAuthStore } from '@/stores/auth';

export const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/:pathMatch(.*)*',
      component: () => import('@/views/pages/maintenance/error/Error404Page.vue'),
      meta: {
        title: 'Страница не найдена',
        description: 'Запрашиваемая страница не найдена на Rebit P2P Trader.'
      }
    },
    MainRoutes,
    PublicRoutes
  ]
});

const publicPages = ['/', '/documentation', '/login', '/register', '/error'];

router.beforeEach(async (to, _from, next) => {
  const auth = useAuthStore();
  const isPublicPage = publicPages.includes(to.path);
  const authRequired = !isPublicPage && to.matched.some((record) => true === record.meta['requiresAuth']);

  if (authRequired && !auth.isAuthenticated) {
    auth.returnUrl = to.fullPath;
    return next('/login');
  }

  if (auth.isAuthenticated && (to.path === '/login' || to.path === '/register')) {
    return next('/dashboard');
  }

  next();
});
