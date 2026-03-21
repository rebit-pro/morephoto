<script setup lang="ts">
import { useExchangeStore } from '@/stores/exchange';
import type { CurrencyPair } from '@/api/exchange';

const exchange = useExchangeStore();

function onSelect(pair: CurrencyPair): void {
  exchange.selectPair(pair);
}
</script>

<template>
  <v-card variant="outlined" rounded="md">
    <v-card-text class="d-flex align-center ga-3 flex-wrap">
      <span class="text-body-1 font-weight-medium">Валютная пара:</span>
      <v-chip-group mandatory>
        <v-chip
          v-for="pair in exchange.currencyPairs"
          :key="`${pair.token}-${pair.fiat}`"
          :color="pair.token === exchange.selectedPair.token && pair.fiat === exchange.selectedPair.fiat ? 'secondary' : undefined"
          :variant="pair.token === exchange.selectedPair.token && pair.fiat === exchange.selectedPair.fiat ? 'flat' : 'outlined'"
          size="default"
          @click="onSelect(pair)"
        >
          {{ pair.label }}
        </v-chip>
      </v-chip-group>
    </v-card-text>
  </v-card>
</template>
