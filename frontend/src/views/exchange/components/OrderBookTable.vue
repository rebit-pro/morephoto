<script setup lang="ts">
import type { OrderBookEntry } from '@/api/exchange';

defineProps<{
  orders: OrderBookEntry[];
  side: 'buy' | 'sell';
}>();
</script>

<template>
  <v-table density="comfortable" hover>
    <thead>
      <tr>
        <th>Трейдер</th>
        <th class="text-right">Цена</th>
        <th class="text-right">Доступно</th>
        <th class="text-right">Лимиты</th>
        <th>Оплата</th>
      </tr>
    </thead>
    <tbody>
      <tr v-if="0 === orders.length">
        <td colspan="5" class="text-center text-lightText pa-6">Нет предложений</td>
      </tr>
      <tr v-for="order in orders" :key="order.id">
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
            {{ order.price }} ₽
          </span>
        </td>
        <td class="text-right text-body-2">{{ order.amount }}</td>
        <td class="text-right text-body-2">{{ order.minLimit }} – {{ order.maxLimit }} ₽</td>
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
