<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { Form } from 'vee-validate';

const show1 = ref(false);
const password = ref('');
const email = ref('');

// Email validation rules
const emailRules = ref([
  (v: string) => '' !== v.trim() || 'Введите email',
  (v: string) => /.+@.+\..+/.test(v.trim()) || 'Некорректный email'
]);
// Password validation rules
const passwordRules = ref([
  (v: string) => '' !== v || 'Введите пароль',
  (v: string) => v.length >= 6 || 'Минимум 6 символов'
]);

/* eslint-disable @typescript-eslint/no-explicit-any */
function validate(_values: any, { setErrors }: any) {
  const authStore = useAuthStore();
  return authStore.login(email.value.trim(), password.value).catch((error) => {
    const message = error?.response?.data?.message ?? 'Ошибка авторизации';
    setErrors({ apiError: message });
  });
}
</script>

<template>
  <Form @submit="validate" class="mt-5 loginForm" v-slot="{ errors, isSubmitting }">
    <v-text-field
      v-model="email"
      :rules="emailRules"
      label="Email"
      class="mb-4"
      required
      density="comfortable"
      hide-details="auto"
      variant="outlined"
      color="primary"
    />
    <v-text-field
      v-model="password"
      :rules="passwordRules"
      label="Пароль"
      required
      density="comfortable"
      variant="outlined"
      color="primary"
      hide-details="auto"
      :append-inner-icon="show1 ? '$eye' : '$eyeOff'"
      :type="show1 ? 'text' : 'password'"
      @click:append-inner="show1 = !show1"
      class="pwdInput"
    />

    <v-btn color="secondary" :loading="isSubmitting" block class="mt-6" variant="flat" size="large" type="submit">
      Войти
    </v-btn>

    <v-alert v-if="errors.apiError" color="error" class="mt-4" variant="tonal">
      {{ errors.apiError }}
    </v-alert>
  </Form>

  <div class="mt-5 text-center">
    <v-divider class="mb-3" />
    <span class="text-lightText">Нет аккаунта?</span>
    <router-link to="/register" class="text-primary text-decoration-none ml-1 font-weight-medium">
      Зарегистрироваться
    </router-link>
  </div>
</template>

<style lang="scss">

.loginForm {
  .v-text-field .v-field--active input {
    font-weight: 500;
  }
}
</style>
