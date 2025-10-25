// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import PhotoOrderPage from '../pages/PhotoOrderPage.vue'

const routes = [
    { path: '/', redirect: '/photos' },
    { path: '/photos', component: { template: '<div>Откройте ссылку /photos/{code} для выбора фотографий</div>' } },
    { path: '/photos/:code', component: PhotoOrderPage, props: true }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
