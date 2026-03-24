<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useExchangeStore } from '@/stores/exchange';
import OrderBookTable from './components/OrderBookTable.vue';
import CurrencyPairSelector from './components/CurrencyPairSelector.vue';

const exchange = useExchangeStore();
const selectorRef = ref<InstanceType<typeof CurrencyPairSelector> | null>(null);

const bestBuyPrice = computed(() => {
  const prices = exchange.buyOrders.map((o) => parseFloat(o.price)).filter((n) => !isNaN(n));
  return 0 === prices.length ? null : Math.max(...prices);
});

const bestSellPrice = computed(() => {
  const prices = exchange.sellOrders.map((o) => parseFloat(o.price)).filter((n) => !isNaN(n));
  return 0 === prices.length ? null : Math.min(...prices);
});

const spread = computed(() => {
  if (null === bestBuyPrice.value || null === bestSellPrice.value) return null;
  return bestSellPrice.value - bestBuyPrice.value;
});

const spreadPercent = computed(() => {
  if (null === spread.value || null === bestSellPrice.value || 0 === bestSellPrice.value) return null;
  return (spread.value / bestSellPrice.value) * 100;
});

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
  <div>
    <h2 class="text-h4 mb-6">P2P Стакан</h2>

    <CurrencyPairSelector ref="selectorRef" class="mb-6" />

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
              :limit-min="selectorRef?.limitMin"
              :limit-max="selectorRef?.limitMax"
              side="buy"
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
              :limit-min="selectorRef?.limitMin"
              :limit-max="selectorRef?.limitMax"
              side="sell"
            />
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Спрэд -->
    <v-card v-if="null !== spread" rounded="md" variant="tonal" class="mt-4">
      <v-card-text class="d-flex align-center justify-center flex-wrap ga-4 py-3">
        <div class="text-center">
          <div class="text-caption text-lightText mb-1">Лучшая покупка</div>
          <div class="text-success font-weight-bold">{{ bestBuyPrice!.toFixed(2) }} ₽</div>
        </div>
        <div class="text-center">
          <div class="text-caption text-lightText mb-1">Спрэд</div>
          <div class="font-weight-bold">
            {{ spread.toFixed(2) }} ₽
            <span class="text-caption text-lightText">({{ spreadPercent!.toFixed(2) }}%)</span>
          </div>
        </div>
        <div class="text-center">
          <div class="text-caption text-lightText mb-1">Лучшая продажа</div>
          <div class="text-error font-weight-bold">{{ bestSellPrice!.toFixed(2) }} ₽</div>
        </div>
      </v-card-text>
    </v-card>

    <v-row v-if="exchange.loading" justify="center" class="mt-4">
      <v-progress-circular indeterminate color="primary" />
    </v-row>
    <v-alert v-if="exchange.error" type="error" variant="tonal" class="mt-4">{{ exchange.error }}</v-alert>
  </div>
</template>
