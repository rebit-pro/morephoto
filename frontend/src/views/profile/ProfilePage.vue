<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useWalletStore } from '@/stores/wallet';
import { useIdentityStore } from '@/stores/identity';

const auth = useAuthStore();
const wallet = useWalletStore();
const identity = useIdentityStore();

const activeTab = ref('balances');
const userEmail = computed(() => auth.user?.['email'] ?? '');

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

function identityStatusColor(): string {
  const status = identity.connectionStatus;

  if (null === status) {
    return 'warning';
  }

  return 'active' === status['status'] ? 'success' : 'warning';
}

async function refreshBalances(): Promise<void> {
  await wallet.fetchBalances();
}

async function refreshTransactions(): Promise<void> {
  await wallet.fetchTransactions();
}

onMounted(async () => {
  await Promise.all([wallet.fetchBalances(), wallet.fetchTransactions(), identity.fetchStatus()]);
});
</script>

<template>
  <div>
    <h2 class="text-h4 mb-2">Мой профиль</h2>
    <p class="text-lightText mb-6">{{ userEmail }}</p>

    <v-tabs v-model="activeTab" color="secondary" class="mb-6">
      <v-tab value="balances">Балансы</v-tab>
      <v-tab value="transactions">История</v-tab>
      <v-tab value="api">Bybit API</v-tab>
    </v-tabs>

    <v-tabs-window v-model="activeTab">
      <!-- Балансы -->
      <v-tabs-window-item value="balances">
        <div class="d-flex align-center justify-space-between mb-4">
          <span class="text-body-1 text-lightText">Ваши балансы на Bybit</span>
          <v-btn variant="text" color="primary" size="small" :loading="wallet.loading" prepend-icon="mdi-refresh" @click="refreshBalances">
            Обновить
          </v-btn>
        </div>

        <v-alert v-if="wallet.error" type="error" variant="tonal" class="mb-4">{{ wallet.error }}</v-alert>

        <v-row>
          <v-col v-for="balance in wallet.balances" :key="balance.currency" cols="12" sm="6" md="4">
            <v-card rounded="md">
              <v-card-text>
                <div class="d-flex align-center mb-3">
                  <v-avatar size="40" color="primary" variant="tonal" class="mr-3">
                    <span class="text-body-1 font-weight-bold">{{ balance.currency }}</span>
                  </v-avatar>
                  <h3 class="text-h6">{{ balance.currency }}</h3>
                </div>
                <p class="text-caption text-lightText">Доступно</p>
                <p class="text-h5 font-weight-bold mb-2">{{ balance.available }}</p>
                <v-divider class="my-2" />
                <div class="d-flex justify-space-between text-caption text-lightText">
                  <span>Заблокировано: {{ balance.locked }}</span>
                  <span>Всего: {{ balance.total }}</span>
                </div>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col v-if="0 === wallet.balances.length && !wallet.loading" cols="12">
            <v-card rounded="md">
              <v-card-text class="text-center pa-8 text-lightText">
                <v-icon size="48" class="mb-3">mdi-wallet-outline</v-icon>
                <p class="text-h6 mb-1">Балансы пока пусты</p>
                <p v-if="!identity.isConnected" class="text-body-2">
                  <router-link to="/profile/api-connection" class="text-primary">Подключите Bybit API</router-link>
                  для отображения балансов
                </p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-tabs-window-item>

      <!-- Транзакции -->
      <v-tabs-window-item value="transactions">
        <div class="d-flex align-center justify-space-between mb-4">
          <span class="text-body-1 text-lightText">История операций</span>
          <v-btn
            variant="text"
            color="primary"
            size="small"
            :loading="wallet.loading"
            prepend-icon="mdi-refresh"
            @click="refreshTransactions"
          >
            Обновить
          </v-btn>
        </div>

        <v-alert v-if="wallet.error" type="error" variant="tonal" class="mb-4">{{ wallet.error }}</v-alert>

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
              <tr v-if="0 === wallet.transactions.length && !wallet.loading">
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
        <v-alert v-if="identity.error" type="error" variant="tonal" class="mb-4">{{ identity.error }}</v-alert>

        <v-card rounded="md">
          <v-card-text>
            <!-- Подключён -->
            <div v-if="identity.isConnected" class="text-center pa-6">
              <v-icon size="48" color="success" class="mb-3">mdi-check-circle</v-icon>
              <h3 class="text-h5 mb-4">API подключён</h3>

              <v-row justify="center" class="mb-4">
                <v-col cols="auto">
                  <v-chip color="info" variant="tonal" size="default"> Режим: {{ identity.modeLabel ?? '—' }} </v-chip>
                </v-col>
                <v-col cols="auto">
                  <v-chip :color="identityStatusColor()" variant="tonal" size="default">
                    {{ identity.statusLabel ?? '—' }}
                  </v-chip>
                </v-col>
              </v-row>

              <div class="d-flex justify-center ga-3">
                <v-btn
                  color="info"
                  variant="outlined"
                  prepend-icon="mdi-shield-check"
                  :loading="identity.loading"
                  @click="identity.verify()"
                >
                  Проверить
                </v-btn>
                <v-btn
                  color="error"
                  variant="outlined"
                  prepend-icon="mdi-link-variant-off"
                  :loading="identity.loading"
                  @click="identity.disconnect()"
                >
                  Отключить
                </v-btn>
              </div>
            </div>

            <!-- Не подключён -->
            <div v-else class="text-center pa-6">
              <v-icon size="48" color="warning" class="mb-3">mdi-link-variant-off</v-icon>
              <h3 class="text-h5 mb-2">API не подключён</h3>
              <p class="text-lightText mb-4">Подключите Bybit API для начала торговли</p>
              <v-btn color="primary" prepend-icon="mdi-link-variant-plus" to="/profile/api-connection"> Подключить </v-btn>
            </div>
          </v-card-text>
        </v-card>
      </v-tabs-window-item>
    </v-tabs-window>
  </div>
</template>
