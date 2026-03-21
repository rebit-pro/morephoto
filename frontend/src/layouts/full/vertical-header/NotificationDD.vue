<script setup lang="ts">
import { notification } from './data';
</script>

<template>
  <div class="pa-4">
    <div class="d-flex align-center justify-space-between mb-3">
      <h6 class="text-subtitle-1">Уведомления</h6>
    </div>
  </div>
  <v-divider />
  <v-list class="py-0" lines="two">
    <template v-for="(item, i) in notification" :key="i">
      <v-list-item color="secondary" class="no-spacer">
        <template #prepend>
          <v-avatar size="40" variant="flat" :color="`light${item.color}`" :class="`me-3 py-2 text-${item.color}`">
            <v-icon size="20">mdi-{{ item.icon }}</v-icon>
          </v-avatar>
        </template>
        <h6 class="text-subtitle-1">{{ item.title }}</h6>
        <p class="text-subtitle-2 text-medium-emphasis mt-1">{{ item.desc }}</p>
      </v-list-item>
      <v-divider v-if="i < notification.length - 1" />
    </template>

    <v-list-item v-if="0 === notification.length" class="text-center text-lightText pa-6">
      Нет уведомлений
    </v-list-item>
  </v-list>
</template>
