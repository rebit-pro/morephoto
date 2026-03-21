<script setup lang="ts">
import { onMounted } from 'vue';
import { useWalletStore } from '@/stores/wallet';

const wallet = useWalletStore();

onMounted(async () => {
  await wallet.fetchBalances();
});
</script>

<template>
  <div>
    <h2 class="text-h4 mb-6">Балансы</h2>

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
