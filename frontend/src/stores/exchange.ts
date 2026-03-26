import { defineStore } from 'pinia';
import { ref } from 'vue';
import { exchangeApi, type OrderBookEntry, type CurrencyPair, type PaymentMethod } from '@/api/exchange';

export const useExchangeStore = defineStore('exchange', () => {
  const buyOrders = ref<OrderBookEntry[]>([]);
  const sellOrders = ref<OrderBookEntry[]>([]);
  const currencyPairs = ref<CurrencyPair[]>([]);
  const paymentMethods = ref<PaymentMethod[]>([]);
  const selectedPair = ref<CurrencyPair>({ token: 'USDT', fiat: 'RUB', label: 'USDT / RUB' });
  const loading = ref(false);
  const error = ref<string | null>(null);
  const hasOrderBookAccess = ref(false);

  let refreshTimer: ReturnType<typeof setInterval> | null = null;

  function clearOrderBook(): void {
    buyOrders.value = [];
    sellOrders.value = [];
    loading.value = false;
    error.value = null;
  }

  function setOrderBookAccess(value: boolean): void {
    hasOrderBookAccess.value = value;

    if (!value) {
      stopAutoRefresh();
      clearOrderBook();
    }
  }

  async function fetchOrderBook(): Promise<void> {
    if (!hasOrderBookAccess.value) {
      clearOrderBook();

      return;
    }

    loading.value = true;
    error.value = null;
    try {
      const data = await exchangeApi.getOrderBook(selectedPair.value.token, selectedPair.value.fiat);
      buyOrders.value = data.buy;
      sellOrders.value = data.sell;
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Ошибка загрузки стакана';
    } finally {
      loading.value = false;
    }
  }

  async function fetchCurrencyPairs(): Promise<void> {
    try {
      currencyPairs.value = await exchangeApi.getCurrencyPairs();
    } catch {
      // Используем дефолтный список при ошибке
      currencyPairs.value = [
        { token: 'USDT', fiat: 'RUB', label: 'USDT / RUB' },
        { token: 'BTC', fiat: 'RUB', label: 'BTC / RUB' },
        { token: 'ETH', fiat: 'RUB', label: 'ETH / RUB' }
      ];
    }
  }

  async function fetchPaymentMethods(): Promise<void> {
    try {
      paymentMethods.value = await exchangeApi.getPaymentMethods();
    } catch {
      paymentMethods.value = [];
    }
  }

  function selectPair(pair: CurrencyPair): void {
    selectedPair.value = pair;

    if (hasOrderBookAccess.value) {
      void fetchOrderBook();
    }
  }

  function startAutoRefresh(intervalMs = 10000): void {
    stopAutoRefresh();

    if (!hasOrderBookAccess.value) {
      return;
    }

    refreshTimer = setInterval(() => {
      void fetchOrderBook();
    }, intervalMs);
  }

  function stopAutoRefresh(): void {
    if (null !== refreshTimer) {
      clearInterval(refreshTimer);
      refreshTimer = null;
    }
  }

  return {
    buyOrders,
    sellOrders,
    currencyPairs,
    paymentMethods,
    selectedPair,
    loading,
    error,
    hasOrderBookAccess,
    clearOrderBook,
    setOrderBookAccess,
    fetchOrderBook,
    fetchCurrencyPairs,
    fetchPaymentMethods,
    selectPair,
    startAutoRefresh,
    stopAutoRefresh
  };
});
