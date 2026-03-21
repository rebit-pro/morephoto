import { defineStore } from 'pinia';
import { ref } from 'vue';
import { walletApi, type Balance, type Transaction } from '@/api/wallet';

export const useWalletStore = defineStore('wallet', () => {
  const balances = ref<Balance[]>([]);
  const transactions = ref<Transaction[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  async function fetchBalances(): Promise<void> {
    loading.value = true;
    error.value = null;
    try {
      balances.value = await walletApi.getBalances();
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Ошибка загрузки балансов';
    } finally {
      loading.value = false;
    }
  }

  async function fetchTransactions(params?: { page?: number; perPage?: number; type?: string }): Promise<void> {
    loading.value = true;
    error.value = null;
    try {
      transactions.value = await walletApi.getTransactions(params);
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Ошибка загрузки транзакций';
    } finally {
      loading.value = false;
    }
  }

  return {
    balances,
    transactions,
    loading,
    error,
    fetchBalances,
    fetchTransactions
  };
});
