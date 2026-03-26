import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { router } from '@/router';
import { useExchangeStore } from '@/stores/exchange';
import {
  authApi,
  type AuthUser,
  type GeeTestCaptchaPayload,
  type RequestRegistrationCodeResponse
} from '@/api/auth';

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('token'));
  const user = ref<AuthUser | null>(JSON.parse(localStorage.getItem('user') ?? 'null'));
  const returnUrl = ref<string | null>(null);

  const isAuthenticated = computed(() => null !== token.value && null !== user.value);

  function setSession(newToken: string, newUser: AuthUser): void {
    token.value = newToken;
    user.value = newUser;
    localStorage.setItem('token', newToken);
    localStorage.setItem('user', JSON.stringify(newUser));
  }

  function clearSession(): void {
    const exchange = useExchangeStore();

    token.value = null;
    user.value = null;
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    exchange.setOrderBookAccess(false);
  }

  async function login(email: string, password: string, captcha?: GeeTestCaptchaPayload): Promise<void> {
    const response = await authApi.login({ email, password, captcha });
    setSession(response.token, response.user);
    await router.push(returnUrl.value ?? '/dashboard');
    returnUrl.value = null;
  }

  async function requestRegistrationCode(email: string, password: string): Promise<RequestRegistrationCodeResponse> {
    return authApi.requestRegistrationCode({ email, password });
  }

  async function confirmRegistration(email: string, code: string): Promise<void> {
    const response = await authApi.confirmRegistration({ email, code });
    setSession(response.token, response.user);
    await router.push('/dashboard');
  }

  async function logout(): Promise<void> {
    try {
      await authApi.logout();
    } catch {
      // Даже если запрос не прошёл — чистим сессию
    }
    clearSession();
    await router.push('/login');
  }

  return {
    token,
    user,
    returnUrl,
    isAuthenticated,
    setSession,
    clearSession,
    login,
    requestRegistrationCode,
    confirmRegistration,
    logout
  };
});
