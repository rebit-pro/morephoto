// src/services/api.js
import axios from 'axios'

const baseURL =
    (import.meta?.env?.VITE_ENDPOINT_URL && String(import.meta.env.VITE_ENDPOINT_URL)) ||
    (import.meta?.env?.VITE_API_BASE_URL && String(import.meta.env.VITE_API_BASE_URL)) ||
    ''

const api = axios.create({
    baseURL,
    timeout: 15000,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
})

api.defaults.withCredentials = false

/**
 * Универсальная GET-обёртка.
 * @param {string} url
 * @param {object} params
 */
export async function get(url, params = {}) {
    try {
        const resp = await api.get(url, { params })
        return resp.data
    } catch (err) {
        handleAxiosError(err)
    }
}

/**
 * Универсальная POST-обёртка.
 * Если нужно отправлять FormData, передайте второй аргумент как FormData
 * (в таком случае Content-Type будет автоматически выставлен в multipart/form-data).
 * @param {string} url
 * @param {object|FormData} data
 */
export async function post(url, data) {
    try {
        // Если передали FormData — позвольте axios установить заголовок
        const headers = (data instanceof FormData) ? { 'Content-Type': 'multipart/form-data' } : undefined
        const resp = await api.post(url, data, { headers })
        return resp.data
    } catch (err) {
        handleAxiosError(err)
    }
}

function handleAxiosError(err) {
    // Нормализуем ошибку и пробросим дальше
    if (err.response) {
        // Сервер вернул ошибку статуса
        const message = err.response.data?.message || err.response.statusText || 'Server error'
        const e = new Error(message)
        e.status = err.response.status
        e.data = err.response.data
        throw e
    } else if (err.request) {
        // Запрос ушёл, ответа нет
        const e = new Error('No response from server')
        throw e
    } else {
        // Прочая ошибка
        throw err
    }
}

export default {
    api,
    get,
    post
}
