<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { useExchangeStore } from '@/stores/exchange';
import { useAuthStore } from '@/stores/auth';
import { useIdentityStore } from '@/stores/identity';
import OrderBookTable from '@/views/exchange/components/OrderBookTable.vue';
import OrderBookAccessState from '@/views/exchange/components/OrderBookAccessState.vue';
import CurrencyPairSelector from '@/views/exchange/components/CurrencyPairSelector.vue';
import type { OrderBookEntry } from '@/api/exchange';

type OrderBookFilters = {
  selectedMethods: string[];
  limitMin: string;
  limitMax: string;
};

const exchange = useExchangeStore();
const auth = useAuthStore();
const identity = useIdentityStore();
const filters = ref<OrderBookFilters>({
  selectedMethods: [],
  limitMin: '',
  limitMax: ''
});
const selectedOrder = ref<OrderBookEntry | null>(null);
const showOrderDialog = ref(false);

const hasOrderBookAccess = computed(() => auth.isAuthenticated && identity.hasActiveConnection);
const isResolvingOrderBookAccess = computed(() => auth.isAuthenticated && identity.loading && null === identity.connectionStatus);
const orderBookConnectionStatus = computed(() => identity.connectionStatus?.['status'] ?? null);
const activeOrder = computed(() => {
  const order = selectedOrder.value;

  if (null === order) {
    return null;
  }

  return {
    ...order
  };
});
const activeOrderUsername = computed(() => {
  const order = activeOrder.value as OrderBookEntry | null;

  if (null === order) {
    return '';
  }

  return order['username'];
});

const activeOrderPrice = computed(() => {
  const order = activeOrder.value as OrderBookEntry | null;

  if (null === order) {
    return '';
  }

  return order['price'];
});

const activeOrderAmount = computed(() => {
  const order = activeOrder.value as OrderBookEntry | null;

  if (null === order) {
    return '';
  }

  return order['amount'];
});

const activeOrderMinLimit = computed(() => {
  const order = activeOrder.value as OrderBookEntry | null;

  if (null === order) {
    return '';
  }

  return order['minLimit'];
});

const activeOrderMaxLimit = computed(() => {
  const order = activeOrder.value as OrderBookEntry | null;

  if (null === order) {
    return '';
  }

  return order['maxLimit'];
});

const activeOrderCompletedTrades = computed(() => {
  const order = activeOrder.value as OrderBookEntry | null;

  if (null === order) {
    return 0;
  }

  return order['completedTrades'];
});

const activeOrderCompletionRate = computed(() => {
  const order = activeOrder.value as OrderBookEntry | null;

  if (null === order) {
    return 0;
  }

  return order['completionRate'];
});

const activeOrderPaymentMethods = computed(() => {
  const order = activeOrder.value as OrderBookEntry | null;

  if (null === order) {
    return [] as string[];
  }

  return order['paymentMethods'];
});

const activeOrderSide = computed(() => {
  const order = activeOrder.value as OrderBookEntry | null;

  if (null === order) {
    return 'buy' as const;
  }

  return order['side'];
});
const activeOrderInitial = computed(() => {
  if ('' === activeOrderUsername.value) {
    return '';
  }

  return activeOrderUsername.value.charAt(0).toUpperCase();
});

function onFiltersUpdate(nextFilters: OrderBookFilters): void {
  filters.value = nextFilters;
}

watch(
  hasOrderBookAccess,
  async (value) => {
    exchange.setOrderBookAccess(value);

    if (!value) {
      exchange.stopAutoRefresh();

      return;
    }

    await exchange.fetchOrderBook();
    exchange.startAutoRefresh();
  },
  { immediate: true }
);

function onSelectOrder(order: OrderBookEntry): void {
  selectedOrder.value = order;
  showOrderDialog.value = true;
}

onMounted(async () => {
  await Promise.all([
    exchange.fetchCurrencyPairs(),
    exchange.fetchPaymentMethods(),
    auth.isAuthenticated ? identity.fetchStatus() : Promise.resolve()
  ]);
});

onUnmounted(() => {
  exchange.stopAutoRefresh();
});
</script>

<template>
  <v-container class="py-8" fluid>
    <!-- Hero -->
    <v-row justify="center" class="mb-8">
      <v-col cols="12" md="8" class="text-center">
        <h1 class="text-h3 font-weight-bold mb-3">Rebit P2P</h1>
        <p class="text-h6 text-lightText mb-6">Удобная торговля криптовалютой P2P через Bybit</p>
        <v-btn v-if="!auth.isAuthenticated" color="secondary" size="large" rounded="lg" to="/login"> Начать торговлю </v-btn>
        <v-btn v-else color="secondary" size="large" rounded="lg" to="/dashboard"> Перейти в кабинет </v-btn>
      </v-col>
    </v-row>

    <template v-if="isResolvingOrderBookAccess">
      <v-row justify="center" class="mb-4 mt-2">
        <v-progress-circular indeterminate color="primary" />
      </v-row>
    </template>

    <template v-else-if="hasOrderBookAccess">
      <!-- Валютная пара + фильтры -->
      <v-row justify="center" class="mb-4">
        <v-col cols="12">
          <CurrencyPairSelector @update:filters="onFiltersUpdate" />
        </v-col>
      </v-row>

      <!-- Стаканы -->
      <v-row>
        <v-col cols="12" md="6">
          <v-card rounded="md">
            <v-card-title class="text-success d-flex align-center">
              <v-icon class="mr-2">mdi-arrow-down-bold</v-icon>
              Покупка (Buy)
            </v-card-title>
            <v-card-text class="pa-0">
              <OrderBookTable
                :orders="exchange.buyOrders"
                :filter-methods="filters.selectedMethods"
                :limit-min="filters.limitMin"
                :limit-max="filters.limitMax"
                side="buy"
                @select="onSelectOrder"
              />
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="6">
          <v-card rounded="md">
            <v-card-title class="text-error d-flex align-center">
              <v-icon class="mr-2">mdi-arrow-up-bold</v-icon>
              Продажа (Sell)
            </v-card-title>
            <v-card-text class="pa-0">
              <OrderBookTable
                :orders="exchange.sellOrders"
                :filter-methods="filters.selectedMethods"
                :limit-min="filters.limitMin"
                :limit-max="filters.limitMax"
                side="sell"
                @select="onSelectOrder"
              />
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </template>

    <v-row v-else>
      <v-col cols="12">
        <OrderBookAccessState :is-authenticated="auth.isAuthenticated" :connection-status="orderBookConnectionStatus" />
      </v-col>
    </v-row>

    <!-- Загрузка -->
    <v-row v-if="exchange.loading" justify="center" class="mt-4">
      <v-progress-circular indeterminate color="primary" />
    </v-row>

    <!-- Ошибка -->
    <v-alert v-if="exchange.error" type="error" variant="tonal" class="mt-4">
      {{ exchange.error }}
    </v-alert>

    <!-- Диалог выбора ордера (заглушка — логика сделки будет позже) -->
    <v-dialog v-model="showOrderDialog" max-width="480">
      <v-card v-if="null !== activeOrder" rounded="md">
        <v-card-title class="d-flex align-center">
          <v-avatar size="32" color="lightsecondary" class="mr-3">
            <span class="text-caption">{{ activeOrderInitial }}</span>
          </v-avatar>
          {{ activeOrderUsername }}
        </v-card-title>
        <v-card-text>
          <v-list density="compact" class="bg-transparent">
            <v-list-item>
              <template #prepend><span class="text-lightText mr-3">Цена:</span></template>
              <v-list-item-title class="font-weight-bold">{{ activeOrderPrice }} ₽</v-list-item-title>
            </v-list-item>
            <v-list-item>
              <template #prepend><span class="text-lightText mr-3">Доступно:</span></template>
              <v-list-item-title>{{ activeOrderAmount }}</v-list-item-title>
            </v-list-item>
            <v-list-item>
              <template #prepend><span class="text-lightText mr-3">Лимиты:</span></template>
              <v-list-item-title>{{ activeOrderMinLimit }} – {{ activeOrderMaxLimit }} ₽</v-list-item-title>
            </v-list-item>
            <v-list-item>
              <template #prepend><span class="text-lightText mr-3">Сделок:</span></template>
              <v-list-item-title>{{ activeOrderCompletedTrades }} ({{ activeOrderCompletionRate }}%)</v-list-item-title>
            </v-list-item>
            <v-list-item>
              <template #prepend><span class="text-lightText mr-3">Оплата:</span></template>
              <v-list-item-title>
                <v-chip v-for="m in activeOrderPaymentMethods" :key="m" size="x-small" variant="tonal" color="primary" class="mr-1">
                  {{ m }}
                </v-chip>
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card-text>
        <v-card-actions class="pa-4 pt-0">
          <v-spacer />
          <v-btn variant="text" @click="showOrderDialog = false">Закрыть</v-btn>
          <v-btn :color="'buy' === activeOrderSide ? 'success' : 'error'" variant="flat" disabled>
            {{ 'buy' === activeOrderSide ? 'Купить' : 'Продать' }} (скоро)
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>
