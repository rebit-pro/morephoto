<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useWalletStore } from '@/stores/wallet';

const wallet = useWalletStore();
const typeFilter = ref<string | undefined>(undefined);

const typeOptions = [
  { title: 'Все', value: undefined },
  { title: 'Депозит', value: 'deposit' },
  { title: 'Вывод', value: 'withdrawal' },
  { title: 'Покупка', value: 'trade_buy' },
  { title: 'Продажа', value: 'trade_sell' },
  { title: 'Блокировка', value: 'lock' },
  { title: 'Разблокировка', value: 'unlock' },
  { title: 'Комиссия', value: 'fee' }
];

const txLabels: Record<string, string> = {
  deposit: 'Депозит',
  withdrawal: 'Вывод',
  trade_buy: 'Покупка',
  trade_sell: 'Продажа',
  lock: 'Блокировка',
  unlock: 'Разблокировка',
  fee: 'Комиссия'
};

const txColors: Record<string, string> = {
  deposit: 'success',
  withdrawal: 'error',
  trade_buy: 'info',
  trade_sell: 'warning',
  lock: 'grey',
  unlock: 'grey',
  fee: 'error'
};

function txLabel(type: string): string {
  return txLabels[type] ?? type;
}

function txColor(type: string): string {
  return txColors[type] ?? 'default';
}

function formatDate(iso: string): string {
  return new Date(iso).toLocaleString('ru-RU');
}

async function loadTransactions(): Promise<void> {
  await wallet.fetchTransactions({ type: typeFilter.value });
}

onMounted(async () => {
  await loadTransactions();
});
</script>

<template>
  <div>
    <div class="d-flex align-center justify-space-between mb-6">
      <h2 class="text-h4">Транзакции</h2>
      <v-select
        v-model="typeFilter"
        :items="typeOptions"
        item-title="title"
        item-value="value"
        label="Тип"
        variant="outlined"
        density="compact"
        hide-details
        style="max-width: 200px"
        @update:model-value="loadTransactions"
      />
    </div>

    <v-row v-if="wallet.loading" justify="center" class="mt-8">
      <v-progress-circular indeterminate color="primary" />
    </v-row>

    <v-alert v-if="wallet.error" type="error" variant="tonal" class="mb-4">{{ wallet.error }}</v-alert>

    <v-card v-if="!wallet.loading" rounded="md">
      <v-table density="comfortable" hover>
        <thead>
          <tr>
            <th>Тип</th>
            <th class="text-right">Сумма</th>
            <th>Валюта</th>
            <th>Сделка</th>
            <th>Дата</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="0 === wallet.transactions.length">
            <td colspan="5" class="text-center text-lightText pa-6">Нет транзакций</td>
          </tr>
          <tr v-for="tx in wallet.transactions" :key="tx.id">
            <td>
              <v-chip size="small" variant="tonal" :color="txColor(tx.type)">{{ txLabel(tx.type) }}</v-chip>
            </td>
            <td class="text-right font-weight-medium">{{ tx.amount }}</td>
            <td>{{ tx.currency }}</td>
            <td class="text-lightText">{{ tx.tradeId ?? '—' }}</td>
            <td class="text-lightText">{{ formatDate(tx.createdAt) }}</td>
          </tr>
        </tbody>
      </v-table>
    </v-card>
  </div>
</template>
