<template>
  <div class="logo">
    <RouterLink to="/" aria-label="Rebit P2P" class="text-decoration-none d-flex align-center">
      <v-icon color="primary" size="28" class="mr-2">mdi-bitcoin</v-icon>
      <span class="text-h5 font-weight-bold">Rebit P2P</span>
    </RouterLink>
  </div>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router';
</script>
