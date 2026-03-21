<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useIdentityStore } from '@/stores/identity';
import { useRouter } from 'vue-router';

const identity = useIdentityStore();
const router = useRouter();

const apiKey = ref('');
const secretKey = ref('');
const mode = ref<'testnet' | 'mainnet'>('testnet');
const showSecret = ref(false);
const submitting = ref(false);
const errorMessage = ref<string | null>(null);

onMounted(async () => {
  await identity.fetchStatus();
});

async function onSubmit(): Promise<void> {
  if ('' === apiKey.value.trim() || '' === secretKey.value.trim()) {
    errorMessage.value = 'Заполните все поля';
    return;
  }

  submitting.value = true;
  errorMessage.value = null;

  try {
    await identity.connect(apiKey.value.trim(), secretKey.value.trim(), mode.value);
    await router.push('/profile');
  } catch (e: unknown) {
    if (e instanceof Error) {
      errorMessage.value = e.message;
    }
    if (null !== identity.error) {
      errorMessage.value = identity.error;
    }
  } finally {
    submitting.value = false;
  }
}
</script>

<template>
  <div>
    <h2 class="text-h4 mb-2">Подключение Bybit API</h2>
    <p class="text-lightText mb-6">Введите ключи API для начала торговли</p>

    <!-- Если уже подключён -->
    <v-card v-if="identity.isConnected" rounded="md" class="mb-6">
      <v-card-text class="text-center pa-8">
        <v-icon size="48" color="success" class="mb-3">mdi-check-circle</v-icon>
        <h3 class="text-h5 mb-2">API уже подключён</h3>
        <p class="text-lightText mb-1">
          Режим: <strong>{{ identity.connectionStatus?.mode }}</strong>
        </p>
        <p class="text-lightText mb-4">
          Статус: <strong>{{ identity.connectionStatus?.status }}</strong>
        </p>
        <div class="d-flex justify-center ga-3">
          <v-btn color="info" variant="outlined" @click="identity.verify()" :loading="identity.loading">
            Проверить
          </v-btn>
          <v-btn color="error" variant="outlined" @click="identity.disconnect()" :loading="identity.loading">
            Отключить
          </v-btn>
        </div>
      </v-card-text>
    </v-card>

    <!-- Форма подключения -->
    <v-card v-else rounded="md">
      <v-card-text class="pa-6">
        <v-form @submit.prevent="onSubmit">
          <v-select
            v-model="mode"
            :items="[
              { title: 'Testnet (тестовая)', value: 'testnet' },
              { title: 'Mainnet (боевая)', value: 'mainnet' }
            ]"
            label="Режим"
            variant="outlined"
            density="comfortable"
            color="primary"
            class="mb-4"
          />

          <v-text-field
            v-model="apiKey"
            label="API Key"
            variant="outlined"
            density="comfortable"
            color="primary"
            class="mb-4"
            hide-details="auto"
          />

          <v-text-field
            v-model="secretKey"
            label="Secret Key"
            variant="outlined"
            density="comfortable"
            color="primary"
            class="mb-4"
            hide-details="auto"
            :append-inner-icon="showSecret ? '$eye' : '$eyeOff'"
            :type="showSecret ? 'text' : 'password'"
            @click:append-inner="showSecret = !showSecret"
          />

          <v-alert v-if="null !== errorMessage" type="error" variant="tonal" class="mb-4">
            {{ errorMessage }}
          </v-alert>

          <v-alert type="warning" variant="tonal" class="mb-4">
            <strong>Важно:</strong> Создайте API-ключ с правами только на чтение и торговлю.
            Не давайте права на вывод средств.
          </v-alert>

          <v-btn color="secondary" :loading="submitting" block variant="flat" size="large" type="submit">
            Подключить API
          </v-btn>
        </v-form>
      </v-card-text>
    </v-card>
  </div>
</template>
