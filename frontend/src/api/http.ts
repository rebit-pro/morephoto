import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import { router } from '@/router';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json'
  },
  timeout: 15000
});

// Request: подставляем JWT-токен
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (null !== token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response: обработка 401
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      const auth = useAuthStore();
      auth.clearSession();
      await router.push('/login');
    }
    return Promise.reject(error);
  }
);

export default api;
