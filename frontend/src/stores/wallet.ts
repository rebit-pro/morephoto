import { defineStore } from 'pinia';
import { ref } from 'vue';
import { walletApi, type Balance, type Transaction, type TransactionFilters } from '@/api/wallet';

export const useWalletStore = defineStore('wallet', () => {
  const balances = ref<Balance[]>([]);
  const transactions = ref<Transaction[]>([]);
  const transactionsTotal = ref(0);
  const loading = ref(false);
  const syncing = ref(false);
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

  async function syncBalances(): Promise<void> {
    syncing.value = true;
    error.value = null;
    try {
      balances.value = await walletApi.syncBalances();
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Ошибка синхронизации балансов';
    } finally {
      syncing.value = false;
    }
  }

  async function fetchTransactions(params?: TransactionFilters): Promise<void> {
    loading.value = true;
    error.value = null;
    try {
      const result = await walletApi.getTransactions(params);
      transactions.value = result.transactions;
      transactionsTotal.value = result.total;
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Ошибка загрузки транзакций';
    } finally {
      loading.value = false;
    }
  }

  async function exportTransactions(params?: Omit<TransactionFilters, 'limit' | 'offset'>): Promise<void> {
    try {
      const blob = await walletApi.exportTransactions(params);
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `transactions_${new Date().toISOString().slice(0, 10)}.csv`;
      a.click();
      URL.revokeObjectURL(url);
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Ошибка экспорта транзакций';
    }
  }

  return {
    balances,
    transactions,
    transactionsTotal,
    loading,
    syncing,
    error,
    fetchBalances,
    syncBalances,
    fetchTransactions,
    exportTransactions,
  };
});
