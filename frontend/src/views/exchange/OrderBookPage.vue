<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';
import { useExchangeStore } from '@/stores/exchange';
import OrderBookTable from './components/OrderBookTable.vue';
import CurrencyPairSelector from './components/CurrencyPairSelector.vue';

const exchange = useExchangeStore();

onMounted(async () => {
  await exchange.fetchCurrencyPairs();
  await exchange.fetchPaymentMethods();
  await exchange.fetchOrderBook();
  exchange.startAutoRefresh();
});

onUnmounted(() => {
  exchange.stopAutoRefresh();
});
</script>

<template>
  <div>
    <h2 class="text-h4 mb-6">P2P Стакан</h2>

    <CurrencyPairSelector class="mb-6" />

    <v-row>
      <v-col cols="12" md="6">
        <v-card rounded="md">
          <v-card-title class="text-success d-flex align-center">
            <v-icon class="mr-2">mdi-arrow-down-bold</v-icon>
            Покупка (Buy)
          </v-card-title>
          <v-card-text class="pa-0">
            <OrderBookTable :orders="exchange.buyOrders" side="buy" />
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
            <OrderBookTable :orders="exchange.sellOrders" side="sell" />
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row v-if="exchange.loading" justify="center" class="mt-4">
      <v-progress-circular indeterminate color="primary" />
    </v-row>
    <v-alert v-if="exchange.error" type="error" variant="tonal" class="mt-4">{{ exchange.error }}</v-alert>
  </div>
</template>
