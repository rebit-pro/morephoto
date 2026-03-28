<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { PlugConnectedIcon, RefreshIcon, WalletIcon } from 'vue-tabler-icons';
import { useWalletStore } from '@/stores/wallet';
import AppEmptyState from '@/components/shared/AppEmptyState.vue';

const wallet = useWalletStore();

const lastSyncedAt = computed(() => {
  const maxDate = wallet.balances.reduce((latest: string | null, b) => {
    if (null === b.syncedAt) return latest;
    if (null === latest) return b.syncedAt;
    return new Date(b.syncedAt) > new Date(latest) ? b.syncedAt : latest;
  }, null);
  if (null === maxDate) return null;
  return new Date(maxDate).toLocaleString('ru-RU');
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
        <span v-if="lastSyncedAt" class="text-caption text-lightText"> Синхронизировано: {{ lastSyncedAt }} </span>
        <v-btn color="primary" variant="outlined" size="small" :loading="wallet.syncing" @click="handleSync">
          <template #prepend>
            <RefreshIcon :size="18" stroke-width="1.75" />
          </template>
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
        <AppEmptyState
          :icon="WalletIcon"
          tone="primary"
          title="Балансы пока пусты"
          description="Подключите Bybit API и запустите синхронизацию, чтобы увидеть доступные и заблокированные средства по валютам."
        >
          <template #actions>
            <div class="d-flex justify-center">
              <v-btn color="primary" variant="outlined" to="/profile/api-connection">
                <template #prepend>
                  <PlugConnectedIcon :size="18" stroke-width="1.75" />
                </template>
                Подключить Bybit API
              </v-btn>
            </div>
          </template>
        </AppEmptyState>
      </v-col>
    </v-row>
  </div>
</template>
