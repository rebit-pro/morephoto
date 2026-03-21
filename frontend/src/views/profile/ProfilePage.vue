<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useWalletStore } from '@/stores/wallet';
import { useIdentityStore } from '@/stores/identity';

const auth = useAuthStore();
const wallet = useWalletStore();
const identity = useIdentityStore();

const activeTab = ref('balances');

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

onMounted(async () => {
  await Promise.all([wallet.fetchBalances(), wallet.fetchTransactions(), identity.fetchStatus()]);
});
</script>

<template>
  <div>
    <h2 class="text-h4 mb-2">Мой профиль</h2>
    <p class="text-lightText mb-6">{{ auth.user?.email }}</p>

    <v-tabs v-model="activeTab" color="secondary" class="mb-6">
      <v-tab value="balances">Балансы</v-tab>
      <v-tab value="transactions">История</v-tab>
      <v-tab value="api">Bybit API</v-tab>
    </v-tabs>

    <v-tabs-window v-model="activeTab">
      <!-- Балансы -->
      <v-tabs-window-item value="balances">
        <v-row>
          <v-col v-for="balance in wallet.balances" :key="balance.currency" cols="12" sm="6" md="4">
            <v-card rounded="md">
              <v-card-text>
                <p class="text-caption text-lightText mb-1">{{ balance.currency }}</p>
                <p class="text-h5 font-weight-bold mb-2">{{ balance.available }}</p>
                <div class="d-flex justify-space-between text-caption text-lightText">
                  <span>Заблокировано: {{ balance.locked }}</span>
                  <span>Всего: {{ balance.total }}</span>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col v-if="0 === wallet.balances.length && !wallet.loading" cols="12">
            <v-alert type="info" variant="tonal">Балансы пока пусты</v-alert>
          </v-col>
        </v-row>
      </v-tabs-window-item>

      <!-- Транзакции -->
      <v-tabs-window-item value="transactions">
        <v-card rounded="md">
          <v-table density="comfortable" hover>
            <thead>
              <tr>
                <th>Тип</th>
                <th class="text-right">Сумма</th>
                <th>Валюта</th>
                <th>Дата</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="0 === wallet.transactions.length">
                <td colspan="4" class="text-center text-lightText pa-6">Нет транзакций</td>
              </tr>
              <tr v-for="tx in wallet.transactions" :key="tx.id">
                <td>
                  <v-chip size="small" variant="tonal" :color="txColor(tx.type)">{{ txLabel(tx.type) }}</v-chip>
                </td>
                <td class="text-right font-weight-medium">{{ tx.amount }}</td>
                <td>{{ tx.currency }}</td>
                <td class="text-lightText">{{ formatDate(tx.createdAt) }}</td>
              </tr>
            </tbody>
          </v-table>
        </v-card>
      </v-tabs-window-item>

      <!-- Bybit API -->
      <v-tabs-window-item value="api">
        <v-card rounded="md">
          <v-card-text>
            <div v-if="identity.isConnected" class="text-center pa-6">
              <v-icon size="48" color="success" class="mb-3">mdi-check-circle</v-icon>
              <h3 class="text-h5 mb-2">API подключён</h3>
              <p class="text-lightText mb-1">
                Режим: <strong>{{ identity.connectionStatus?.mode }}</strong>
              </p>
              <p class="text-lightText mb-4">
                Статус: <strong>{{ identity.connectionStatus?.status }}</strong>
              </p>
              <v-btn color="error" variant="outlined" @click="identity.disconnect()">Отключить</v-btn>
            </div>
            <div v-else class="text-center pa-6">
              <v-icon size="48" color="warning" class="mb-3">mdi-link-variant-off</v-icon>
              <h3 class="text-h5 mb-2">API не подключён</h3>
              <p class="text-lightText mb-4">Подключите Bybit API для торговли</p>
              <v-btn color="primary" to="/profile/api-connection">Подключить</v-btn>
            </div>
          </v-card-text>
        </v-card>
      </v-tabs-window-item>
    </v-tabs-window>
  </div>
</template>
