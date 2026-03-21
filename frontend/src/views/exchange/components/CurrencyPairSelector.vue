<script setup lang="ts">
import { ref, computed } from 'vue';
import { useExchangeStore } from '@/stores/exchange';
import type { CurrencyPair } from '@/api/exchange';

const exchange = useExchangeStore();
const selectedMethods = ref<string[]>([]);

function onSelectPair(pair: CurrencyPair): void {
  exchange.selectPair(pair);
}

function toggleMethod(method: string): void {
  const idx = selectedMethods.value.indexOf(method);
  if (-1 === idx) {
    selectedMethods.value.push(method);
  } else {
    selectedMethods.value.splice(idx, 1);
  }
}

function clearFilters(): void {
  selectedMethods.value = [];
}

const isActivePair = computed(() => (pair: CurrencyPair) =>
  pair.token === exchange.selectedPair.token && pair.fiat === exchange.selectedPair.fiat
);

defineExpose({ selectedMethods });
</script>

<template>
  <v-card variant="outlined" rounded="md">
    <v-card-text>
      <!-- Валютные пары -->
      <div class="d-flex align-center ga-3 flex-wrap mb-3">
        <span class="text-body-1 font-weight-medium">Пара:</span>
        <v-chip-group mandatory>
          <v-chip
            v-for="pair in exchange.currencyPairs"
            :key="`${pair.token}-${pair.fiat}`"
            :color="isActivePair(pair) ? 'secondary' : undefined"
            :variant="isActivePair(pair) ? 'flat' : 'outlined'"
            size="default"
            @click="onSelectPair(pair)"
          >
            {{ pair.label }}
          </v-chip>
        </v-chip-group>
      </div>

      <!-- Методы оплаты -->
      <div v-if="0 < exchange.paymentMethods.length" class="d-flex align-center ga-3 flex-wrap">
        <span class="text-body-2 text-lightText">Оплата:</span>
        <v-chip
          v-for="method in exchange.paymentMethods"
          :key="method.id"
          :color="selectedMethods.includes(method.id) ? 'primary' : undefined"
          :variant="selectedMethods.includes(method.id) ? 'flat' : 'outlined'"
          size="small"
          @click="toggleMethod(method.id)"
        >
          {{ method.name }}
        </v-chip>
        <v-btn
          v-if="0 < selectedMethods.length"
          variant="text"
          size="x-small"
          color="error"
          @click="clearFilters"
        >
          Сбросить
        </v-btn>
      </div>
    </v-card-text>
  </v-card>
</template>
