<script setup lang="ts">
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useWalletStore } from '@/stores/wallet';
import { useIdentityStore } from '@/stores/identity';
import { useExchangeStore } from '@/stores/exchange';

const auth = useAuthStore();
const wallet = useWalletStore();
const identity = useIdentityStore();
const exchange = useExchangeStore();

onMounted(async () => {
  await Promise.all([wallet.fetchBalances(), identity.fetchStatus(), exchange.fetchCurrencyPairs()]);
});
</script>

<template>
  <div>
    <h2 class="text-h4 mb-2">Добро пожаловать, {{ auth.user?.name ?? auth.user?.email }}</h2>
    <p class="text-lightText mb-6">Панель управления Rebit P2P</p>

    <!-- Статус подключения Bybit -->
    <v-row class="mb-6">
      <v-col cols="12" md="4">
        <v-card rounded="md">
          <v-card-text class="d-flex align-center">
            <v-avatar size="48" :color="identity.isConnected ? 'success' : 'warning'" variant="tonal" class="mr-4">
              <v-icon>{{ identity.isConnected ? 'mdi-link-variant' : 'mdi-link-variant-off' }}</v-icon>
            </v-avatar>
            <div>
              <p class="text-caption text-lightText mb-1">Bybit API</p>
              <p class="text-h6 font-weight-bold">
                {{ identity.isConnected ? 'Подключён' : 'Не подключён' }}
              </p>
              <p v-if="identity.connectionStatus?.mode" class="text-caption text-lightText">
                {{ identity.connectionStatus.mode }}
              </p>
            </div>
          </v-card-text>
          <v-card-actions v-if="!identity.isConnected">
            <v-btn variant="text" color="primary" to="/profile/api-connection" size="small">Подключить</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <!-- Балансы -->
      <v-col v-for="balance in wallet.balances" :key="balance.currency" cols="12" md="4">
        <v-card rounded="md">
          <v-card-text class="d-flex align-center">
            <v-avatar size="48" color="primary" variant="tonal" class="mr-4">
              <span class="text-body-1 font-weight-bold">{{ balance.currency }}</span>
            </v-avatar>
            <div>
              <p class="text-caption text-lightText mb-1">{{ balance.currency }}</p>
              <p class="text-h6 font-weight-bold">{{ balance.available }}</p>
              <p v-if="'0' !== balance.locked" class="text-caption text-warning">
                Заблокировано: {{ balance.locked }}
              </p>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Если балансов нет -->
      <v-col v-if="0 === wallet.balances.length && !wallet.loading" cols="12" md="4">
        <v-card rounded="md">
          <v-card-text class="text-center text-lightText pa-6">
            <v-icon size="32" class="mb-2">mdi-wallet-outline</v-icon>
            <p>Балансы пока пусты</p>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Быстрые действия -->
    <v-row>
      <v-col cols="12" sm="6" md="3">
        <v-card rounded="md" hover to="/orderbook">
          <v-card-text class="text-center pa-6">
            <v-icon size="40" color="secondary" class="mb-3">mdi-swap-horizontal-bold</v-icon>
            <p class="text-body-1 font-weight-medium">P2P Стакан</p>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" md="3">
        <v-card rounded="md" hover to="/wallet/balances">
          <v-card-text class="text-center pa-6">
            <v-icon size="40" color="primary" class="mb-3">mdi-wallet</v-icon>
            <p class="text-body-1 font-weight-medium">Балансы</p>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" md="3">
        <v-card rounded="md" hover to="/wallet/transactions">
          <v-card-text class="text-center pa-6">
            <v-icon size="40" color="info" class="mb-3">mdi-history</v-icon>
            <p class="text-body-1 font-weight-medium">Транзакции</p>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" md="3">
        <v-card rounded="md" hover to="/profile">
          <v-card-text class="text-center pa-6">
            <v-icon size="40" color="success" class="mb-3">mdi-account-circle</v-icon>
            <p class="text-body-1 font-weight-medium">Профиль</p>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>
