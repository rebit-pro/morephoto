<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useIdentityStore } from '@/stores/identity';

const identity = useIdentityStore();

const apiKey = ref('');
const secretKey = ref('');
const mode = ref<'testnet' | 'mainnet'>('testnet');
const showSecret = ref(false);
const submitting = ref(false);
const verifying = ref(false);
const disconnecting = ref(false);
const errorMessage = ref<string | null>(null);
const notification = ref<{
  type: 'success' | 'info' | 'warning' | 'error';
  title: string;
  text: string;
} | null>(null);

const isConnected = computed(() => identity.isConnected);
const connectionMode = computed(() => {
  const status = identity.connectionStatus;

  if (null === status) {
    return null;
  }

  return status['mode'];
});
const connectionStatus = computed(() => {
  const status = identity.connectionStatus;

  if (null === status) {
    return null;
  }

  return status['status'];
});
const connectionModeLabel = computed(() => identity.modeLabel ?? '—');
const connectionStatusLabel = computed(() => identity.statusLabel ?? '—');
const hasConnectionMode = computed(() => null !== connectionMode.value);
const notificationType = computed(() => {
  const value = notification.value;

  if (null === value) {
    return 'info';
  }

  return value['type'];
});

const notificationTitle = computed(() => {
  const value = notification.value;

  if (null === value) {
    return '';
  }

  return value['title'];
});

const notificationText = computed(() => {
  const value = notification.value;

  if (null === value) {
    return '';
  }

  return value['text'];
});
const maskedApiKey = computed(() => {
  const status = identity.connectionStatus;

  if (null === status) {
    return '—';
  }

  return status['maskedApiKey'] ?? '—';
});
const createdAt = computed(() => {
  const status = identity.connectionStatus;

  if (null === status) {
    return null;
  }

  return status['createdAt'];
});
const verifiedAt = computed(() => {
  const status = identity.connectionStatus;

  if (null === status) {
    return null;
  }

  return status['verifiedAt'];
});
const statusChipColor = computed(() => {
  switch (connectionStatus.value) {
    case 'active':
      return 'success';
    case 'pending_verification':
      return 'info';
    case 'invalid':
      return 'warning';
    case 'revoked':
      return 'error';
    default:
      return 'secondary';
  }
});
const connectionTone = computed(() => ('mainnet' === connectionMode.value ? 'warning' : 'info'));
const connectionTitle = computed(() => ('mainnet' === connectionMode.value ? 'Подключён боевой Bybit API' : 'Подключён тестовый Bybit API'));
const connectionDescription = computed(() => (
  'mainnet' === connectionMode.value
    ? 'У вас активирован боевой ключ. Операции будут выполняться в реальной среде Bybit.'
    : 'У вас активирован тестовый ключ. Можно безопасно проверять сценарии без боевых сделок.'
));

function resetForm(resetMode = true): void {
  apiKey.value = '';
  secretKey.value = '';
  showSecret.value = false;
  errorMessage.value = null;

  if (resetMode) {
    mode.value = 'testnet';
  }
}

function formatDate(value: string | null): string {
  if (null === value || '' === value) {
    return '—';
  }

  return new Date(value).toLocaleString('ru-RU');
}

onMounted(async () => {
  await identity.fetchStatus();

  if (hasConnectionMode.value && null !== connectionMode.value) {
    mode.value = connectionMode.value;
  }
});

async function onSubmit(): Promise<void> {
  if ('' === apiKey.value.trim() || '' === secretKey.value.trim()) {
    errorMessage.value = 'Заполните все поля';
    return;
  }

  submitting.value = true;
  errorMessage.value = null;
  notification.value = null;

  try {
    await identity.connect(apiKey.value.trim(), secretKey.value.trim(), mode.value);
    resetForm(false);
    notification.value = {
      type: 'success',
      title: 'Bybit API подключён',
      text: `Подключен ${identity.modeLabel ?? ('mainnet' === mode.value ? 'Mainnet' : 'Testnet')} ключ. Теперь можно пользоваться стаканами и торговыми сценариями.`,
    };
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

async function onVerify(): Promise<void> {
  verifying.value = true;
  errorMessage.value = null;
  notification.value = null;

  try {
    await identity.verify();

    if (null !== identity.error) {
      errorMessage.value = identity.error;

      return;
    }

    notification.value = {
      type: 'info',
      title: 'Проверка выполнена',
      text: `Статус подключения: ${identity.statusLabel ?? '—'}. Режим: ${identity.modeLabel ?? '—'}.`,
    };
  } catch (e: unknown) {
    if (e instanceof Error) {
      errorMessage.value = e.message;
    }

    if (null !== identity.error) {
      errorMessage.value = identity.error;
    }
  } finally {
    verifying.value = false;
  }
}

async function onDisconnect(): Promise<void> {
  disconnecting.value = true;
  errorMessage.value = null;
  notification.value = null;

  try {
    await identity.disconnect();

    if (null !== identity.error) {
      errorMessage.value = identity.error;

      return;
    }

    resetForm(true);
    notification.value = {
      type: 'info',
      title: 'Bybit API отключён',
      text: 'Ключ и секрет отвязаны. Можно подключить новый testnet или mainnet ключ.',
    };
  } catch (e: unknown) {
    if (e instanceof Error) {
      errorMessage.value = e.message;
    }

    if (null !== identity.error) {
      errorMessage.value = identity.error;
    }
  } finally {
    disconnecting.value = false;
  }
}
</script>

<template>
  <div>
    <h2 class="text-h4 mb-2">Подключение Bybit API</h2>
    <p class="text-lightText mb-6">Подключите testnet или mainnet ключ, чтобы видеть стаканы, балансы и работать с P2P.</p>

    <v-alert
      v-if="null !== notification"
      :type="notificationType"
      variant="tonal"
      class="mb-6"
      closable
      @click:close="notification = null"
    >
      <div class="font-weight-medium mb-1">{{ notificationTitle }}</div>
      <div>{{ notificationText }}</div>
    </v-alert>

    <v-row class="mb-6">
      <v-col cols="12" md="6">
        <v-card rounded="xl" variant="outlined" class="fill-height api-connection-page__hero-card">
          <v-card-text class="pa-6 pa-md-8">
            <div class="d-flex align-center mb-4">
              <v-avatar size="52" color="secondary" variant="tonal" class="mr-4">
                <v-icon size="28">mdi-key-chain-variant</v-icon>
              </v-avatar>
              <div>
                <div class="text-h6 font-weight-bold mb-1">Bybit API</div>
                <div class="text-body-2 text-lightText">Поддерживаются testnet и mainnet ключи</div>
              </div>
            </div>

            <div class="d-flex flex-wrap ga-2 mb-4">
              <v-chip color="info" variant="tonal">Testnet для безопасной отладки</v-chip>
              <v-chip color="warning" variant="tonal">Mainnet для реальных операций</v-chip>
            </div>

            <v-alert :type="isConnected ? connectionTone : 'info'" variant="tonal">
              <div class="font-weight-medium mb-1">
                {{ isConnected ? connectionTitle : 'Ключ пока не подключён' }}
              </div>
              <div>
                {{ isConnected
                  ? connectionDescription
                  : 'После подключения вы сможете видеть P2P-стаканы, статусы подключения и балансы Bybit прямо в интерфейсе.' }}
              </div>
            </v-alert>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card rounded="xl" variant="outlined" class="fill-height">
          <v-card-text class="pa-6 pa-md-8">
            <div class="text-subtitle-1 font-weight-bold mb-4">Рекомендации по ключам</div>
            <v-list density="comfortable" class="bg-transparent py-0">
              <v-list-item prepend-icon="mdi-shield-check-outline" title="Давайте только нужные права" subtitle="Достаточно чтения и торговли, без вывода средств." />
              <v-list-item prepend-icon="mdi-flask-outline" title="Для тестов используйте Testnet" subtitle="Подходит для проверки сценариев без риска для боевых средств." />
              <v-list-item prepend-icon="mdi-swap-horizontal" title="Mainnet используйте осознанно" subtitle="Все операции выполняются в реальной среде Bybit." />
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Если уже подключён -->
    <v-card v-if="isConnected" rounded="xl" class="mb-6">
      <v-card-text class="pa-6 pa-md-8">
        <div class="d-flex align-center justify-space-between flex-wrap ga-4 mb-6">
          <div class="d-flex align-center">
            <v-avatar size="56" color="success" variant="tonal" class="mr-4">
              <v-icon size="30">mdi-check-circle</v-icon>
            </v-avatar>
            <div>
              <h3 class="text-h5 mb-1">{{ connectionTitle }}</h3>
              <p class="text-lightText mb-0">{{ connectionDescription }}</p>
            </div>
          </div>

          <div class="d-flex flex-wrap ga-2">
            <v-chip :color="connectionTone" variant="tonal">{{ connectionModeLabel }}</v-chip>
            <v-chip :color="statusChipColor" variant="tonal">{{ connectionStatusLabel }}</v-chip>
          </div>
        </div>

        <v-row class="mb-4">
          <v-col cols="12" md="4">
            <div class="text-caption text-lightText mb-1">Ключ</div>
            <div class="font-weight-medium">{{ maskedApiKey }}</div>
          </v-col>
          <v-col cols="12" md="4">
            <div class="text-caption text-lightText mb-1">Подключён</div>
            <div class="font-weight-medium">{{ formatDate(createdAt) }}</div>
          </v-col>
          <v-col cols="12" md="4">
            <div class="text-caption text-lightText mb-1">Проверен</div>
            <div class="font-weight-medium">{{ formatDate(verifiedAt) }}</div>
          </v-col>
        </v-row>

        <div class="d-flex flex-wrap ga-3">
          <v-btn color="info" variant="outlined" @click="onVerify" :loading="verifying || identity.loading">
            Проверить
          </v-btn>
          <v-btn color="error" variant="outlined" @click="onDisconnect" :loading="disconnecting || identity.loading">
            Отключить
          </v-btn>
        </div>
      </v-card-text>
    </v-card>

    <!-- Форма подключения -->
    <v-card v-else rounded="xl">
      <v-card-text class="pa-6 pa-md-8">
        <div class="d-flex align-center justify-space-between flex-wrap ga-4 mb-6">
          <div>
            <h3 class="text-h5 mb-1">Подключить Bybit API</h3>
            <p class="text-lightText mb-0">Введите ключ и секрет. После подключения форма скроется, а при отключении снова станет доступна.</p>
          </div>
          <v-chip :color="'mainnet' === mode ? 'warning' : 'info'" variant="tonal" size="large">
            {{ 'mainnet' === mode ? 'Будет подключён Mainnet' : 'Будет подключён Testnet' }}
          </v-chip>
        </div>

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
            prepend-inner-icon="mdi-compare"
            hide-details="auto"
          />

          <v-text-field
            v-model="apiKey"
            label="API Key"
            variant="outlined"
            density="comfortable"
            color="primary"
            class="mb-4"
            hide-details="auto"
            prepend-inner-icon="mdi-key-variant"
            placeholder="Введите Bybit API Key"
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
            prepend-inner-icon="mdi-lock-outline"
            placeholder="Введите Bybit Secret Key"
            @click:append-inner="showSecret = !showSecret"
          />

          <v-alert v-if="null !== errorMessage" type="error" variant="tonal" class="mb-4">
            {{ errorMessage }}
          </v-alert>

          <v-alert :type="'mainnet' === mode ? 'warning' : 'info'" variant="tonal" class="mb-6">
            <strong>Важно:</strong> Создайте API-ключ с правами только на чтение и торговлю.
            {{ 'mainnet' === mode
              ? ' Вы выбрали боевой режим. Не давайте права на вывод средств и перепроверьте ограничения ключа.'
              : ' Вы выбрали тестовый режим — это безопасный вариант для первичной проверки интеграции.' }}
          </v-alert>

          <div class="d-flex flex-wrap ga-3">
            <v-btn color="secondary" :loading="submitting" variant="flat" size="large" type="submit">
            Подключить API
            </v-btn>
            <v-btn variant="text" size="large" @click="resetForm(false)">
              Очистить поля
            </v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>
  </div>
</template>

<style scoped>
.api-connection-page__hero-card {
  background: linear-gradient(180deg, rgba(103, 80, 164, 0.04) 0%, rgba(103, 80, 164, 0.08) 100%);
}
</style>
