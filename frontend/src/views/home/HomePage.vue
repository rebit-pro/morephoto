<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useExchangeStore } from '@/stores/exchange';
import { useAuthStore } from '@/stores/auth';
import OrderBookTable from '@/views/exchange/components/OrderBookTable.vue';
import CurrencyPairSelector from '@/views/exchange/components/CurrencyPairSelector.vue';
import type { OrderBookEntry } from '@/api/exchange';

const exchange = useExchangeStore();
const auth = useAuthStore();
const selectorRef = ref<InstanceType<typeof CurrencyPairSelector> | null>(null);
const selectedOrder = ref<OrderBookEntry | null>(null);
const showOrderDialog = ref(false);

function onSelectOrder(order: OrderBookEntry): void {
  selectedOrder.value = order;
  showOrderDialog.value = true;
}

onMounted(async () => {
  await Promise.all([
    exchange.fetchCurrencyPairs(),
    exchange.fetchPaymentMethods()
  ]);
  await exchange.fetchOrderBook();
  exchange.startAutoRefresh();
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
        <v-btn v-if="!auth.isAuthenticated" color="secondary" size="large" rounded="lg" to="/login">
          Начать торговлю
        </v-btn>
        <v-btn v-else color="secondary" size="large" rounded="lg" to="/dashboard">
          Перейти в кабинет
        </v-btn>
      </v-col>
    </v-row>

    <!-- Валютная пара + фильтры -->
    <v-row justify="center" class="mb-4">
      <v-col cols="12">
        <CurrencyPairSelector ref="selectorRef" />
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
              :filter-methods="selectorRef?.selectedMethods"
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
              :filter-methods="selectorRef?.selectedMethods"
              side="sell"
              @select="onSelectOrder"
            />
          </v-card-text>
        </v-card>
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
      <v-card v-if="null !== selectedOrder" rounded="md">
        <v-card-title class="d-flex align-center">
          <v-avatar size="32" color="lightsecondary" class="mr-3">
            <span class="text-caption">{{ selectedOrder.username.charAt(0).toUpperCase() }}</span>
          </v-avatar>
          {{ selectedOrder.username }}
        </v-card-title>
        <v-card-text>
          <v-list density="compact" class="bg-transparent">
            <v-list-item>
              <template #prepend><span class="text-lightText mr-3">Цена:</span></template>
              <v-list-item-title class="font-weight-bold">{{ selectedOrder.price }} ₽</v-list-item-title>
            </v-list-item>
            <v-list-item>
              <template #prepend><span class="text-lightText mr-3">Доступно:</span></template>
              <v-list-item-title>{{ selectedOrder.amount }}</v-list-item-title>
            </v-list-item>
            <v-list-item>
              <template #prepend><span class="text-lightText mr-3">Лимиты:</span></template>
              <v-list-item-title>{{ selectedOrder.minLimit }} – {{ selectedOrder.maxLimit }} ₽</v-list-item-title>
            </v-list-item>
            <v-list-item>
              <template #prepend><span class="text-lightText mr-3">Сделок:</span></template>
              <v-list-item-title>{{ selectedOrder.completedTrades }} ({{ selectedOrder.completionRate }}%)</v-list-item-title>
            </v-list-item>
            <v-list-item>
              <template #prepend><span class="text-lightText mr-3">Оплата:</span></template>
              <v-list-item-title>
                <v-chip
                  v-for="m in selectedOrder.paymentMethods"
                  :key="m"
                  size="x-small"
                  variant="tonal"
                  color="primary"
                  class="mr-1"
                >
                  {{ m }}
                </v-chip>
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-card-text>
        <v-card-actions class="pa-4 pt-0">
          <v-spacer />
          <v-btn variant="text" @click="showOrderDialog = false">Закрыть</v-btn>
          <v-btn
            :color="'buy' === selectedOrder.side ? 'success' : 'error'"
            variant="flat"
            disabled
          >
            {{ 'buy' === selectedOrder.side ? 'Купить' : 'Продать' }} (скоро)
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>
