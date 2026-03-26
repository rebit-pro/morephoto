<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useWalletStore } from '@/stores/wallet';
import type { TransactionFilters } from '@/api/wallet';

const wallet = useWalletStore();

const typeFilter = ref<string | undefined>(undefined);
const dateFrom = ref('');
const dateTo = ref('');
const currentPage = ref(1);
const itemsPerPage = 50;

const typeOptions = [
  { title: 'Все', value: undefined },
  { title: 'Депозит', value: 'deposit' },
  { title: 'Вывод', value: 'withdrawal' },
  { title: 'Покупка', value: 'trade_buy' },
  { title: 'Продажа', value: 'trade_sell' },
  { title: 'Блокировка', value: 'lock' },
  { title: 'Разблокировка', value: 'unlock' },
  { title: 'Комиссия', value: 'fee' },
];

const txLabels: Record<string, string> = {
  deposit: 'Депозит',
  withdrawal: 'Вывод',
  trade_buy: 'Покупка',
  trade_sell: 'Продажа',
  lock: 'Блокировка',
  unlock: 'Разблокировка',
  fee: 'Комиссия',
};

const txColors: Record<string, string> = {
  deposit: 'success',
  withdrawal: 'error',
  trade_buy: 'info',
  trade_sell: 'warning',
  lock: 'grey',
  unlock: 'grey',
  fee: 'error',
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

const totalPages = computed(() => Math.max(1, Math.ceil(wallet.transactionsTotal / itemsPerPage)));

function buildParams(): TransactionFilters {
  const params: TransactionFilters = {
    limit: itemsPerPage,
    offset: (currentPage.value - 1) * itemsPerPage,
  };
  if (typeFilter.value) params.type = typeFilter.value;
  if ('' !== dateFrom.value) params.dateFrom = dateFrom.value;
  if ('' !== dateTo.value) params.dateTo = dateTo.value;
  return params;
}

async function loadTransactions(): Promise<void> {
  await wallet.fetchTransactions(buildParams());
}

function onFilterChange(): void {
  if (1 === currentPage.value) {
    void loadTransactions();
  } else {
    currentPage.value = 1;
  }
}

async function handleExport(): Promise<void> {
  const params: TransactionFilters = {};
  if (typeFilter.value) params.type = typeFilter.value;
  if ('' !== dateFrom.value) params.dateFrom = dateFrom.value;
  if ('' !== dateTo.value) params.dateTo = dateTo.value;
  await wallet.exportTransactions(params);
}

watch(currentPage, () => {
  void loadTransactions();
});

onMounted(async () => {
  await loadTransactions();
});
</script>

<template>
  <div>
    <div class="d-flex align-center justify-space-between mb-6 flex-wrap ga-3">
      <h2 class="text-h4">Транзакции</h2>
      <v-btn color="primary" variant="outlined" size="small" prepend-icon="mdi-download" @click="handleExport">
        Экспорт
      </v-btn>
    </div>

    <!-- Фильтры -->
    <v-card rounded="md" class="mb-4">
      <v-card-text>
        <v-row dense>
          <v-col cols="12" sm="3">
            <v-select
              v-model="typeFilter"
              :items="typeOptions"
              item-title="title"
              item-value="value"
              label="Тип"
              variant="outlined"
              density="compact"
              hide-details
              @update:model-value="onFilterChange"
            />
          </v-col>
          <v-col cols="12" sm="3">
            <v-text-field
              v-model="dateFrom"
              label="Дата с"
              type="date"
              variant="outlined"
              density="compact"
              hide-details
              @change="onFilterChange"
            />
          </v-col>
          <v-col cols="12" sm="3">
            <v-text-field
              v-model="dateTo"
              label="Дата по"
              type="date"
              variant="outlined"
              density="compact"
              hide-details
              @change="onFilterChange"
            />
          </v-col>
          <v-col cols="12" sm="3" class="d-flex align-center">
            <v-btn variant="text" size="small" @click="typeFilter = undefined; dateFrom = ''; dateTo = ''; onFilterChange();">
              Сбросить
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

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

      <!-- Пагинация -->
      <v-card-actions v-if="totalPages > 1" class="justify-center">
        <v-pagination v-model="currentPage" :length="totalPages" :total-visible="7" density="compact" />
      </v-card-actions>
    </v-card>
  </div>
</template>
