// src/services/photoService.js
import api from './api'

export async function getCollectionByCode(code) {
    if (!code) throw new Error('code required')
    // По требованиям бэкенда — POST /api/v1/photo/ с body { code }
    return api.post('/api/v1/photo/', { code })
}

/**
 * Отправка заявки.
 * В нашем случае будем отправлять JSON со структурой:
 * {
 *   code: "shkola-5-21102025",
 *   items: [
 *     { src: "/upload/..jpg", sizes: { "10x15": 2, "15x21":0, "20x30":1 } },
 *     ...
 *   ],
 *   note: "..."
 * }
 */
export async function createOrder(payload) {
    if (!payload) throw new Error('payload required')
    return api.post('/api/v1/photo/order', payload)
}

export default { getCollectionByCode, createOrder }
