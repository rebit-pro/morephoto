import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { identityApi, type ApiConnectionStatus } from '@/api/identity';

export const useIdentityStore = defineStore('identity', () => {
  const connectionStatus = ref<ApiConnectionStatus | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const isConnected = computed(() => true === connectionStatus.value?.connected);

  async function fetchStatus(): Promise<void> {
    loading.value = true;
    error.value = null;
    try {
      connectionStatus.value = await identityApi.status();
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Ошибка получения статуса';
    } finally {
      loading.value = false;
    }
  }

  async function connect(apiKey: string, secretKey: string, mode: 'testnet' | 'mainnet'): Promise<void> {
    loading.value = true;
    error.value = null;
    try {
      connectionStatus.value = await identityApi.connect({ apiKey, secretKey, mode });
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Ошибка подключения API';
      throw e;
    } finally {
      loading.value = false;
    }
  }

  async function disconnect(): Promise<void> {
    loading.value = true;
    error.value = null;
    try {
      await identityApi.disconnect();
      connectionStatus.value = { connected: false, mode: null, status: null };
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Ошибка отключения';
    } finally {
      loading.value = false;
    }
  }

  async function verify(): Promise<void> {
    loading.value = true;
    error.value = null;
    try {
      connectionStatus.value = await identityApi.verify();
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Ошибка верификации';
    } finally {
      loading.value = false;
    }
  }

  return {
    connectionStatus,
    loading,
    error,
    isConnected,
    fetchStatus,
    connect,
    disconnect,
    verify
  };
});
