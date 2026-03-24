<script setup lang="ts">
import { computed, ref } from 'vue';
import { useExchangeStore } from '@/stores/exchange';
import type { CurrencyPair } from '@/api/exchange';

const exchange = useExchangeStore();
const selectedMethods = ref<string[]>([]);
const limitMin = ref<string>('');
const limitMax = ref<string>('');

const hasActiveFilters = computed(
  () => 0 < selectedMethods.value.length || '' !== limitMin.value || '' !== limitMax.value
);

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
  limitMin.value = '';
  limitMax.value = '';
}

const isActivePair = computed(
  () => (pair: CurrencyPair) =>
    pair.token === exchange.selectedPair.token && pair.fiat === exchange.selectedPair.fiat
);

defineExpose({ selectedMethods, limitMin, limitMax });
</script>

<template>
  <v-card variant="outlined" rounded="md">
    <v-card-text class="d-flex flex-column ga-4">
      <!-- Валютные пары -->
      <div class="d-flex align-center ga-3 flex-wrap">
        <span class="text-body-2 font-weight-medium filter-label">Пара:</span>
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
        <span class="text-body-2 font-weight-medium filter-label">Оплата:</span>
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
      </div>

      <!-- Фильтр по лимитам -->
      <div class="d-flex align-center ga-3 flex-wrap">
        <span class="text-body-2 font-weight-medium filter-label">Лимиты:</span>
        <v-text-field
          v-model="limitMin"
          label="От"
          type="number"
          min="0"
          density="compact"
          variant="outlined"
          hide-details
          clearable
          style="max-width: 140px"
          :suffix="exchange.selectedPair.fiat"
        />
        <v-text-field
          v-model="limitMax"
          label="До"
          type="number"
          min="0"
          density="compact"
          variant="outlined"
          hide-details
          clearable
          style="max-width: 140px"
          :suffix="exchange.selectedPair.fiat"
        />
      </div>

      <!-- Сброс -->
      <div v-if="hasActiveFilters">
        <v-btn variant="text" size="x-small" color="error" prepend-icon="mdi-close-circle" @click="clearFilters">
          Сбросить фильтры
        </v-btn>
      </div>
    </v-card-text>
  </v-card>
</template>

<style scoped>
.filter-label {
  min-width: 60px;
}
</style>
