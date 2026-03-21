<script setup lang="ts">
import Logo from '@/layouts/full/logo/LogoMain.vue';
import AuthRegister from './authForms/AuthRegister.vue';
</script>

<template>
  <v-row class="h-screen" no-gutters>
    <v-col cols="12" class="d-flex align-center justify-center bg-lightprimary">
      <v-container>
        <v-row justify="center">
          <v-col cols="12" sm="8" md="6" lg="4" xl="3">
            <v-card elevation="0" class="loginBox">
              <v-card variant="outlined">
                <v-card-text class="pa-9">
                  <div class="text-center mb-6">
                    <Logo />
                    <h2 class="text-h4 text-secondary mt-4">Регистрация в Rebit P2P</h2>
                    <p class="text-lightText mt-2">Создайте аккаунт для торговли</p>
                  </div>
                  <AuthRegister />
                </v-card-text>
              </v-card>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-col>
  </v-row>
</template>

<style lang="scss">
.loginBox {
  max-width: 475px;
  margin: 0 auto;
}
</style>
