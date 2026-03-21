<script setup lang="ts">
import { useCustomizerStore } from '../../../stores/customizer';
import { BellIcon, Menu2Icon } from 'vue-tabler-icons';
import NotificationDD from './NotificationDD.vue';
import ProfileDD from './ProfileDD.vue';

const customizer = useCustomizerStore();
</script>

<template>
  <v-app-bar elevation="0" height="80">
    <v-btn
      class="hidden-md-and-down text-secondary"
      color="lightsecondary"
      icon
      rounded="sm"
      variant="flat"
      size="small"
      aria-label="menu collapse"
      @click.stop="customizer.SET_MINI_SIDEBAR(!customizer.mini_sidebar)"
    >
      <Menu2Icon size="20" stroke-width="1.5" />
    </v-btn>
    <v-btn
      class="hidden-lg-and-up text-secondary ms-3"
      color="lightsecondary"
      icon
      rounded="sm"
      variant="flat"
      size="small"
      aria-label="menu collapse"
      @click.stop="customizer.SET_SIDEBAR_DRAWER"
    >
      <Menu2Icon size="20" stroke-width="1.5" />
    </v-btn>

    <v-spacer />

    <!-- Notification -->
    <v-menu :close-on-content-click="false">
      <template #activator="{ props }">
        <v-btn
          icon
          class="text-secondary mx-3"
          color="lightsecondary"
          rounded="sm"
          size="small"
          aria-label="notification icon"
          variant="flat"
          v-bind="props"
        >
          <BellIcon stroke-width="1.5" size="22" />
        </v-btn>
      </template>
      <v-sheet rounded="md" width="330" elevation="12">
        <NotificationDD />
      </v-sheet>
    </v-menu>

    <!-- User Profile -->
    <v-menu :close-on-content-click="false">
      <template #activator="{ props }">
        <v-btn class="profileBtn text-primary" color="lightprimary" variant="flat" rounded="pill" v-bind="props">
          <v-avatar size="30" color="primary" variant="tonal" class="me-2">
            <v-icon size="18">mdi-account</v-icon>
          </v-avatar>
        </v-btn>
      </template>
      <v-sheet rounded="md" width="330" elevation="12">
        <ProfileDD />
      </v-sheet>
    </v-menu>
  </v-app-bar>
</template>
