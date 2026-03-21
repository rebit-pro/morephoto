<script setup lang="ts">
const menuItems = [
  { title: 'P2P Стакан', to: '/orderbook' },
  { title: 'Балансы', to: '/wallet/balances' },
  { title: 'Транзакции', to: '/wallet/transactions' },
  { title: 'Профиль', to: '/profile' },
  { title: 'Подключение Bybit', to: '/profile/api-connection' }
];
</script>

<template>
  <div>
    <v-list density="compact" class="overflow-hidden pa-4">
      <v-row no-gutters>
        <v-col v-for="(item, i) in menuItems" :key="i" cols="6" sm="4">
          <v-list-item :to="item.to" rounded="md" color="secondary" class="no-spacer">
            <template #prepend>
              <v-icon class="text-8 me-2" icon="$circle" />
            </template>
            <v-list-item-title class="text-subtitle-1 font-weight-regular">
              {{ item.title }}
            </v-list-item-title>
          </v-list-item>
        </v-col>
      </v-row>
    </v-list>
  </div>
</template>
