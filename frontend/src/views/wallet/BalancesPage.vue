<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useWalletStore } from '@/stores/wallet';

const wallet = useWalletStore();

const lastSyncedAt = computed(() => {
  const synced = wallet.balances.find((b) => null !== b.syncedAt);
  if (!synced?.syncedAt) return null;
  return new Date(synced.syncedAt).toLocaleString('ru-RU');
});

onMounted(async () => {
  await wallet.fetchBalances();
});

async function handleSync(): Promise<void> {
  await wallet.syncBalances();
}
</script>

<template>
  <div>
    <div class="d-flex align-center justify-space-between mb-6">
      <h2 class="text-h4">Балансы</h2>
      <div class="d-flex align-center ga-3">
        <span v-if="lastSyncedAt" class="text-caption text-lightText">
          Синхронизировано: {{ lastSyncedAt }}
        </span>
        <v-btn
          color="primary"
          variant="outlined"
          size="small"
          :loading="wallet.syncing"
          prepend-icon="mdi-sync"
          @click="handleSync"
        >
          Синхронизировать
        </v-btn>
      </div>
    </div>

    <v-row v-if="wallet.loading" justify="center" class="mt-8">
      <v-progress-circular indeterminate color="primary" />
    </v-row>

    <v-alert v-if="wallet.error" type="error" variant="tonal" class="mb-4">{{ wallet.error }}</v-alert>

    <v-row v-if="!wallet.loading">
      <v-col v-for="balance in wallet.balances" :key="balance.currency" cols="12" sm="6" md="4">
        <v-card rounded="md">
          <v-card-text>
            <div class="d-flex align-center mb-3">
              <v-avatar size="40" color="primary" variant="tonal" class="mr-3">
                <span class="text-body-1 font-weight-bold">{{ balance.currency }}</span>
              </v-avatar>
              <h3 class="text-h6">{{ balance.currency }}</h3>
            </div>
            <div class="mb-2">
              <p class="text-caption text-lightText">Доступно</p>
              <p class="text-h5 font-weight-bold">{{ balance.available }}</p>
            </div>
            <v-divider class="my-2" />
            <div class="d-flex justify-space-between text-body-2 text-lightText">
              <span>Заблокировано: {{ balance.locked }}</span>
              <span>Всего: {{ balance.total }}</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col v-if="0 === wallet.balances.length" cols="12">
        <v-card rounded="md">
          <v-card-text class="text-center pa-8 text-lightText">
            <v-icon size="48" class="mb-3">mdi-wallet-outline</v-icon>
            <p class="text-h6">Балансы пока пусты</p>
            <p class="text-body-2">Подключите Bybit API для отображения балансов</p>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>
