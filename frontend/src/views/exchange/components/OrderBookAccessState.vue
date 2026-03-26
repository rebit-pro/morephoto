<script setup lang="ts">
import { computed } from 'vue';
import type { ApiConnectionStatus } from '@/api/identity';

type ActionButton = {
  text: string;
  to: string;
  color: string;
  variant: 'flat' | 'outlined';
  icon: string;
};

type AccessStateContent = {
  icon: string;
  title: string;
  description: string;
  actions: ActionButton[];
  steps: string[];
};

const props = defineProps<{
  isAuthenticated: boolean;
  connectionStatus?: ApiConnectionStatus['status'];
}>();

const guestActions: ActionButton[] = [
  {
    text: 'Войти',
    to: '/login',
    color: 'secondary',
    variant: 'flat',
    icon: 'mdi-login'
  },
  {
    text: 'Зарегистрироваться',
    to: '/register',
    color: 'primary',
    variant: 'outlined',
    icon: 'mdi-account-plus-outline'
  }
];

const bybitActions: ActionButton[] = [
  {
    text: 'Подключить Bybit API',
    to: '/profile/api-connection',
    color: 'secondary',
    variant: 'flat',
    icon: 'mdi-key-chain-variant'
  }
];

const content = computed<AccessStateContent>(() => {
  if (!props.isAuthenticated) {
    return {
      icon: 'mdi-shield-lock-outline',
      title: 'Авторизуйтесь, чтобы смотреть стаканы',
      description: 'Стаканы P2P доступны только после входа в аккаунт и подключения Bybit API. Авторизуйтесь или зарегистрируйтесь, затем добавьте ключи в профиле.',
      actions: guestActions,
      steps: ['Войти или зарегистрироваться', 'Подключить API-ключи Bybit', 'Вернуться к стаканам и выбрать торговую пару']
    };
  }

  switch (props.connectionStatus) {
    case 'invalid':
      return {
        icon: 'mdi-alert-circle-outline',
        title: 'Проверьте Bybit API-ключи',
        description: 'Подключение к Bybit найдено, но ключи не прошли проверку. Обновите API Key и Secret Key в профиле, чтобы снова открыть доступ к стаканам.',
        actions: bybitActions,
        steps: ['Открыть настройки Bybit API', 'Проверить права ключа и Secret Key', 'Сохранить новые ключи и повторить проверку']
      };
    case 'revoked':
      return {
        icon: 'mdi-link-variant-off',
        title: 'Доступ к Bybit API отозван',
        description: 'Bybit больше не принимает сохранённые ключи. Подключите API повторно, чтобы загрузить стаканы и включить автообновление.',
        actions: bybitActions,
        steps: ['Подключить новый Bybit API-ключ', 'Проверить режим mainnet или testnet', 'Вернуться к стаканам после успешного подключения']
      };
    case 'pending_verification':
      return {
        icon: 'mdi-timer-sand',
        title: 'Подключение Bybit API ожидает проверки',
        description: 'Ключи сохранены, но подключение ещё не подтверждено. Завершите проверку в профиле, после чего стаканы станут доступны.',
        actions: bybitActions,
        steps: ['Открыть страницу подключения API', 'Запустить или повторить проверку ключей', 'Дождаться статуса active и вернуться к стаканам']
      };
    default:
      return {
        icon: 'mdi-key-chain-variant',
        title: 'Подключите API-ключи Bybit',
        description: 'Для вывода стаканов P2P необходимо подключить Bybit API в профиле. После подключения ключей таблица ордеров и автообновление станут доступны.',
        actions: bybitActions,
        steps: ['Открыть профиль и подключить Bybit API', 'Убедиться, что статус подключения active', 'Вернуться к стаканам и выбрать торговую пару']
      };
  }
});
</script>

<template>
  <v-card rounded="xl" elevation="0" class="orderbook-access-state">
    <v-card-text class="pa-8 pa-md-10 text-center">
      <v-avatar size="72" color="lightsecondary" class="mb-5">
        <v-icon size="36" color="secondary">
          {{ content.icon }}
        </v-icon>
      </v-avatar>

      <h3 class="text-h5 font-weight-bold mb-3">
        {{ content.title }}
      </h3>

      <p class="text-body-1 text-lightText mb-6 mx-auto orderbook-access-state__text">
        {{ content.description }}
      </p>

      <div class="d-flex flex-wrap justify-center ga-3 mb-6">
        <v-btn
          v-for="action in content.actions"
          :key="action.text"
          :to="action.to"
          :color="action.color"
          :variant="action.variant"
          rounded="lg"
          size="large"
          :prepend-icon="action.icon"
        >
          {{ action.text }}
        </v-btn>
      </div>

      <v-alert color="info" variant="tonal" class="text-left">
        <div class="font-weight-medium mb-1">Что нужно сделать</div>
        <ul class="pl-5 mb-0">
          <li v-for="step in content.steps" :key="step">{{ step }}</li>
        </ul>
      </v-alert>
    </v-card-text>
  </v-card>
</template>

<style scoped>
.orderbook-access-state {
  border: 1px solid rgba(103, 80, 164, 0.12);
  background: linear-gradient(180deg, #ffffff 0%, #f7f3ff 100%);
}

.orderbook-access-state__text {
  max-width: 680px;
}
</style>
