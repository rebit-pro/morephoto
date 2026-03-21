<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';
import { useExchangeStore } from '@/stores/exchange';
import { useAuthStore } from '@/stores/auth';
import OrderBookTable from '@/views/exchange/components/OrderBookTable.vue';
import CurrencyPairSelector from '@/views/exchange/components/CurrencyPairSelector.vue';

const exchange = useExchangeStore();
const auth = useAuthStore();

onMounted(async () => {
  await exchange.fetchCurrencyPairs();
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
        <v-btn v-else color="secondary" size="large" rounded="lg" to="/dashboard"> Перейти в кабинет </v-btn>
      </v-col>
    </v-row>

    <!-- Валютная пара -->
    <v-row justify="center" class="mb-4">
      <v-col cols="12" md="8">
        <CurrencyPairSelector />
      </v-col>
    </v-row>

    <!-- Стаканы -->
    <v-row justify="center">
      <v-col cols="12" md="6">
        <v-card rounded="md">
          <v-card-title class="text-success">
            <v-icon class="mr-2">mdi-arrow-down</v-icon>
            Покупка (Buy)
          </v-card-title>
          <v-card-text class="pa-0">
            <OrderBookTable :orders="exchange.buyOrders" side="buy" />
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="6">
        <v-card rounded="md">
          <v-card-title class="text-error">
            <v-icon class="mr-2">mdi-arrow-up</v-icon>
            Продажа (Sell)
          </v-card-title>
          <v-card-text class="pa-0">
            <OrderBookTable :orders="exchange.sellOrders" side="sell" />
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Загрузка / Ошибка -->
    <v-row v-if="exchange.loading" justify="center" class="mt-4">
      <v-progress-circular indeterminate color="primary" />
    </v-row>
    <v-row v-if="exchange.error" justify="center" class="mt-4">
      <v-col cols="12" md="8">
        <v-alert type="error" variant="tonal">{{ exchange.error }}</v-alert>
      </v-col>
    </v-row>
  </v-container>
</template>
