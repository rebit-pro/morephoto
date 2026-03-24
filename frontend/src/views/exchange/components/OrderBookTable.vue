<script setup lang="ts">
import { computed, ref } from 'vue';
import type { OrderBookEntry } from '@/api/exchange';

type SortKey = 'price' | 'amount' | 'minLimit';
type SortDir = 'asc' | 'desc';

const props = defineProps<{
  orders: OrderBookEntry[];
  side: 'buy' | 'sell';
  filterMethods?: string[];
  limitMin?: string;
  limitMax?: string;
}>();

const sortKey = ref<SortKey>('price');
const sortDir = ref<SortDir>('buy' === props.side ? 'desc' : 'asc');

function toggleSort(key: SortKey): void {
  if (sortKey.value === key) {
    sortDir.value = 'asc' === sortDir.value ? 'desc' : 'asc';
  } else {
    sortKey.value = key;
    sortDir.value = 'asc';
  }
}

function sortIcon(key: SortKey): string {
  if (sortKey.value !== key) return 'mdi-unfold-more-horizontal';
  return 'asc' === sortDir.value ? 'mdi-arrow-up' : 'mdi-arrow-down';
}

function fmt(value: string | number): string {
  const num = parseFloat(String(value));
  return isNaN(num) ? String(value) : num.toFixed(2);
}

const filteredOrders = computed(() => {
  let list = props.orders;

  if (props.filterMethods && 0 < props.filterMethods.length) {
    list = list.filter((order) => props.filterMethods!.some((m) => order.paymentMethods.includes(m)));
  }

  const minVal = props.limitMin && '' !== props.limitMin ? parseFloat(props.limitMin) : null;
  const maxVal = props.limitMax && '' !== props.limitMax ? parseFloat(props.limitMax) : null;

  if (null !== minVal && !isNaN(minVal)) {
    list = list.filter((order) => parseFloat(order.maxLimit) >= minVal);
  }

  if (null !== maxVal && !isNaN(maxVal)) {
    list = list.filter((order) => parseFloat(order.minLimit) <= maxVal);
  }

  return list;
});

const sortedOrders = computed(() => {
  const list = [...filteredOrders.value];
  const dir = 'asc' === sortDir.value ? 1 : -1;
  return list.sort((a, b) => {
    const aVal = parseFloat(a[sortKey.value]);
    const bVal = parseFloat(b[sortKey.value]);
    return (aVal - bVal) * dir;
  });
});
</script>

<template>
  <v-table density="comfortable" hover>
    <thead>
      <tr>
        <th>Трейдер</th>
        <th class="text-right sortable-col" @click="toggleSort('price')">
          Цена
          <v-icon size="14" class="ml-1">{{ sortIcon('price') }}</v-icon>
        </th>
        <th class="text-right sortable-col" @click="toggleSort('amount')">
          Доступно
          <v-icon size="14" class="ml-1">{{ sortIcon('amount') }}</v-icon>
        </th>
        <th class="text-right sortable-col" @click="toggleSort('minLimit')">
          Лимиты
          <v-icon size="14" class="ml-1">{{ sortIcon('minLimit') }}</v-icon>
        </th>
        <th>Оплата</th>
      </tr>
    </thead>
    <tbody>
      <tr v-if="0 === sortedOrders.length">
        <td colspan="5" class="text-center text-lightText pa-6">Нет предложений</td>
      </tr>
      <tr v-for="order in sortedOrders" :key="order.id">
        <td>
          <div class="d-flex align-center">
            <v-avatar size="28" color="lightsecondary" class="mr-2">
              <span class="text-caption">{{ order.username.charAt(0).toUpperCase() }}</span>
            </v-avatar>
            <div>
              <div class="text-body-2 font-weight-medium">{{ order.username }}</div>
              <div class="text-caption text-lightText">
                {{ order.completedTrades }} сделок · {{ order.completionRate }}%
              </div>
            </div>
          </div>
        </td>
        <td class="text-right">
          <span :class="'buy' === side ? 'text-success' : 'text-error'" class="font-weight-bold">
            {{ fmt(order.price) }} ₽
          </span>
        </td>
        <td class="text-right text-body-2">{{ fmt(order.amount) }}</td>
        <td class="text-right text-body-2">{{ fmt(order.minLimit) }} – {{ fmt(order.maxLimit) }} ₽</td>
        <td>
          <v-chip
            v-for="method in order.paymentMethods"
            :key="method"
            size="x-small"
            variant="tonal"
            color="primary"
            class="mr-1"
          >
            {{ method }}
          </v-chip>
        </td>
      </tr>
    </tbody>
  </v-table>
</template>

<style scoped>
.sortable-col {
  cursor: pointer;
  user-select: none;
}

.sortable-col:hover {
  opacity: 0.8;
}
</style>
