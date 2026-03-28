<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTradesStore } from '@/stores/trades';
import { useExchangeStore } from '@/stores/exchange';
import { usePolling } from '@/composables/usePolling';
import { useCurrencyFormat } from '@/composables/useCurrencyFormat';
import type { Trade } from '@/api/exchange';
import TradeChat from './components/TradeChat.vue';
import TradeCountdown from './components/TradeCountdown.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { isMockApiEnabled } from '@/mocks/config';

const route = useRoute();
const router = useRouter();
const trades = useTradesStore();
const exchange = useExchangeStore();
const { formatRub, formatNumber, formatDate } = useCurrencyFormat();

const tradeId = computed(() => Number(route.params.id));

const currencyPairLabel = computed(() => {
  const pairId = getTradeValue('currencyPairId');

  if (null === pairId) {
    return null;
  }

  const pair = exchange.currencyPairs.find((p) => p.id === pairId);

  return pair?.label ?? null;
});

const counterparty = computed(() => trades.counterpartyInfo);

const kycLevelLabel = computed(() => {
  if (null === counterparty.value) return '';
  return match(counterparty.value.kycLevel, {
    0: 'Нет KYC',
    1: 'Базовый (Lv.1)',
    2: 'Продвинутый (Lv.2)',
    3: 'Полный (Lv.3)',
  });
});

function match<TValue>(value: number, map: Record<number, TValue>): TValue {
  return map[value] ?? (map[0] as TValue);
}

const confirmPaymentDialog = ref(false);
const releaseDialog = ref(false);
const paymentType = ref('bank_transfer');
const paymentId = ref('');

const statusLabels: Record<string, string> = {
  pending_payment: 'Ожидание оплаты',
  payment_sent: 'Оплата отправлена',
  payment_confirmed: 'Оплата подтверждена',
  completed: 'Завершена',
  cancelled: 'Отменена',
  disputed: 'Спор'
};

const statusColors: Record<string, string> = {
  pending_payment: 'warning',
  payment_sent: 'info',
  payment_confirmed: 'primary',
  completed: 'success',
  cancelled: 'error',
  disputed: 'error'
};

function hasTrade(): boolean {
  return null !== trades.currentTrade;
}

function getTradeValue<TKey extends keyof Trade>(key: TKey): Trade[TKey] | null {
  if (null === trades.currentTrade) {
    return null;
  }

  return trades.currentTrade[key];
}

const isChatReadonly = computed(() => {
  const status = getTradeValue('status');

  if (null === status) {
    return true;
  }

  return 'completed' === status || 'cancelled' === status;
});

const canConfirmPayment = computed(() => {
  return 'pending_payment' === getTradeValue('status') && 'buy' === getTradeValue('side');
});

const canRelease = computed(() => {
  return 'payment_sent' === getTradeValue('status') && 'sell' === getTradeValue('side');
});

const canOpenAdvertisement = computed(() => {
  return null !== getTradeValue('advertisementId');
});

const showCountdown = computed(() => {
  return null !== getTradeValue('paymentDeadline') &&
    '' !== (getTradeValue('paymentDeadline') ?? '') &&
    'pending_payment' === getTradeValue('status');
});

async function loadTrade(): Promise<void> {
  await trades.fetchTradeDetail(tradeId.value);
}

async function handleConfirmPayment(): Promise<void> {
  try {
    await trades.confirmPayment(tradeId.value, {
      paymentType: paymentType.value,
      paymentId: paymentId.value
    });
    confirmPaymentDialog.value = false;
  } catch {
    // ошибка обрабатывается в сторе
  }
}

async function handleRelease(): Promise<void> {
  try {
    await trades.releaseAssets(tradeId.value);
    releaseDialog.value = false;
  } catch {
    // ошибка обрабатывается в сторе
  }
}

function openCurrentAdvertisement(): void {
  const advertisementId = getTradeValue('advertisementId');

  if ('number' !== typeof advertisementId) {
    return;
  }

  void router.push({
    path: '/exchange/advertisements',
    query: {
      highlight: String(advertisementId)
    }
  });
}

function openBybitTradePage(): void {
  window.open('https://www.bybit.com/fiat/trade/otc', '_blank', 'noopener,noreferrer');
}

const polling = usePolling(loadTrade, 10000);

function reinitialize(): void {
  polling.stop();
  trades.clearCurrentTrade();
  confirmPaymentDialog.value = false;
  releaseDialog.value = false;
}

watch(tradeId, async (newId, oldId) => {
  if (newId === oldId) return;
  reinitialize();
  await loadTrade();
  trades.fetchCounterpartyInfo(newId);
  polling.start();
});

onMounted(async () => {
  await Promise.all([loadTrade(), exchange.fetchCurrencyPairs()]);
  trades.fetchCounterpartyInfo(tradeId.value);
  polling.start();
});

onUnmounted(() => {
  polling.stop();
  trades.clearCurrentTrade();
});
</script>

<template>
  <div>
    <v-btn variant="text" class="mb-4" prepend-icon="mdi-arrow-left" @click="router.push('/exchange/trades')"> К списку сделок </v-btn>

    <v-row v-if="trades.loading && !hasTrade()" justify="center" class="mt-8">
      <v-progress-circular indeterminate color="primary" />
    </v-row>

    <v-alert v-if="trades.error" type="error" variant="tonal" class="mb-4">{{ trades.error }}</v-alert>

    <v-alert v-if="isMockApiEnabled && hasTrade()" type="info" variant="tonal" class="mb-4">
      В mock-режиме в этом окне доступны и детали сделки, и чат. После успешной оплаты используйте действие
      <strong>«Отпустить средства»</strong>.
    </v-alert>

    <template v-if="null !== trades.currentTrade">
      <div class="d-flex align-center justify-space-between mb-6 flex-wrap ga-3">
        <h2 class="text-h4 font-weight-bold">Сделка #{{ trades.currentTrade['id'] }}</h2>
        <v-chip :color="statusColors[trades.currentTrade['status']] ?? 'default'" variant="tonal" size="default">
          {{ statusLabels[trades.currentTrade['status']] ?? trades.currentTrade['status'] }}
        </v-chip>
      </div>

      <v-row>
        <!-- Контрагент -->
        <v-col cols="12" md="7">
          <!-- Профиль контрагента -->
          <UiParentCard
            v-if="null !== counterparty"
            title="Контрагент"
            subtitle="Профиль на Bybit"
            icon="mdi-account-box-outline"
            color="secondary"
            class="mb-4"
          >
            <v-card-text class="pa-5">
              <div class="d-flex align-center ga-3 mb-4">
                <v-avatar size="48" color="secondary" variant="tonal">
                  <span class="text-h6 font-weight-bold">{{ counterparty.nickName.charAt(0).toUpperCase() }}</span>
                </v-avatar>
                <div>
                  <div class="text-subtitle-1 font-weight-bold">{{ counterparty.nickName }}</div>
                  <div class="d-flex align-center ga-2">
                    <v-chip :color="counterparty.isOnline ? 'success' : 'grey'" variant="tonal" size="x-small">
                      {{ counterparty.isOnline ? 'Онлайн' : 'Офлайн' }}
                    </v-chip>
                    <v-chip v-if="0 < counterparty.vipLevel" color="warning" variant="tonal" size="x-small">
                      VIP {{ counterparty.vipLevel }}
                    </v-chip>
                  </div>
                </div>
              </div>

              <v-list density="compact" class="pa-0">
                <v-list-item v-if="'' !== counterparty.realName">
                  <template #prepend><v-icon size="18" color="secondary">mdi-card-account-details-outline</v-icon></template>
                  <v-list-item-title class="text-body-2">ФИО</v-list-item-title>
                  <template #append>
                    <span class="font-weight-medium text-body-2">{{ counterparty.realName }}</span>
                  </template>
                </v-list-item>

                <v-list-item>
                  <template #prepend><v-icon size="18" color="secondary">mdi-shield-check-outline</v-icon></template>
                  <v-list-item-title class="text-body-2">KYC</v-list-item-title>
                  <template #append>
                    <v-chip size="x-small" variant="tonal" :color="2 <= counterparty.kycLevel ? 'success' : 'warning'">
                      {{ kycLevelLabel }}
                    </v-chip>
                  </template>
                </v-list-item>

                <v-list-item v-if="'' !== counterparty.kycCountryCode">
                  <template #prepend><v-icon size="18" color="secondary">mdi-earth</v-icon></template>
                  <v-list-item-title class="text-body-2">Страна KYC</v-list-item-title>
                  <template #append>
                    <span class="font-weight-medium text-body-2">{{ counterparty.kycCountryCode }}</span>
                  </template>
                </v-list-item>

                <v-list-item v-if="'' !== counterparty.email">
                  <template #prepend><v-icon size="18" color="secondary">mdi-email-outline</v-icon></template>
                  <v-list-item-title class="text-body-2">Email</v-list-item-title>
                  <template #append>
                    <span class="text-body-2 text-medium-emphasis">{{ counterparty.email }}</span>
                  </template>
                </v-list-item>

                <v-list-item v-if="'' !== counterparty.mobile">
                  <template #prepend><v-icon size="18" color="secondary">mdi-phone-outline</v-icon></template>
                  <v-list-item-title class="text-body-2">Телефон</v-list-item-title>
                  <template #append>
                    <span class="text-body-2 text-medium-emphasis">{{ counterparty.mobile }}</span>
                  </template>
                </v-list-item>
              </v-list>

              <v-divider class="my-3" />

              <div class="text-caption font-weight-bold text-medium-emphasis mb-2">СТАТИСТИКА СДЕЛОК</div>
              <div class="d-flex flex-wrap ga-2">
                <v-chip size="small" variant="tonal" color="default" prepend-icon="mdi-check-all">{{ counterparty.totalFinishCount }} сделок</v-chip>
                <v-chip size="small" variant="tonal" color="success" prepend-icon="mdi-arrow-down-bold">{{ counterparty.totalFinishBuyCount }} покупок</v-chip>
                <v-chip size="small" variant="tonal" color="error" prepend-icon="mdi-arrow-up-bold">{{ counterparty.totalFinishSellCount }} продаж</v-chip>
                <v-chip size="small" variant="tonal" color="info" prepend-icon="mdi-calendar-month">{{ counterparty.recentRate }}% за 30д</v-chip>
              </div>

              <v-divider class="my-3" />

              <div class="text-caption font-weight-bold text-medium-emphasis mb-2">РЕЙТИНГ И СКОРОСТЬ</div>
              <v-list density="compact" class="pa-0">
                <v-list-item>
                  <template #prepend><v-icon size="18" color="success">mdi-thumb-up-outline</v-icon></template>
                  <v-list-item-title class="text-body-2">Положительные отзывы</v-list-item-title>
                  <template #append>
                    <span class="font-weight-medium text-body-2">{{ counterparty.goodAppraiseCount }} ({{ counterparty.goodAppraiseRate }}%)</span>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="18" color="error">mdi-thumb-down-outline</v-icon></template>
                  <v-list-item-title class="text-body-2">Отрицательные отзывы</v-list-item-title>
                  <template #append>
                    <span class="font-weight-medium text-body-2">{{ counterparty.badAppraiseCount }}</span>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="18" color="info">mdi-timer-outline</v-icon></template>
                  <v-list-item-title class="text-body-2">Ср. время оплаты</v-list-item-title>
                  <template #append>
                    <span class="text-body-2">{{ counterparty.averageTransferTime }} мин</span>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="18" color="info">mdi-clock-fast</v-icon></template>
                  <v-list-item-title class="text-body-2">Ср. время выпуска</v-list-item-title>
                  <template #append>
                    <span class="text-body-2">{{ counterparty.averageReleaseTime }} мин</span>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="18" color="secondary">mdi-calendar-clock</v-icon></template>
                  <v-list-item-title class="text-body-2">Аккаунт создан</v-list-item-title>
                  <template #append>
                    <span class="text-body-2">{{ counterparty.accountCreateDays }} дн. назад</span>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </UiParentCard>
        </v-col>

        <!-- Информация + Таймер + Действия -->
        <v-col cols="12" md="5">
          <UiParentCard title="Информация" subtitle="Детали текущей сделки" icon="mdi-information-outline" color="primary">
            <v-card-text class="pa-5">
              <v-list density="compact" class="pa-0">
                <v-list-item>
                  <template #prepend><v-icon size="20" color="primary">mdi-account</v-icon></template>
                  <v-list-item-title>Контрагент</v-list-item-title>
                  <template #append>
                    <span class="font-weight-medium">{{ trades.currentTrade['counterpartyName'] }}</span>
                  </template>
                </v-list-item>
                <v-list-item v-if="null !== currencyPairLabel">
                  <template #prepend><v-icon size="20" color="primary">mdi-swap-horizontal-circle-outline</v-icon></template>
                  <v-list-item-title>Валютная пара</v-list-item-title>
                  <template #append>
                    <v-chip size="small" variant="tonal" color="secondary">{{ currencyPairLabel }}</v-chip>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="20" color="primary">mdi-swap-horizontal</v-icon></template>
                  <v-list-item-title>Направление</v-list-item-title>
                  <template #append>
                    <v-chip size="small" variant="tonal" :color="'buy' === trades.currentTrade['side'] ? 'success' : 'error'">
                      {{ 'buy' === trades.currentTrade['side'] ? 'Покупка' : 'Продажа' }}
                    </v-chip>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="20" color="primary">mdi-currency-rub</v-icon></template>
                  <v-list-item-title>Цена</v-list-item-title>
                  <template #append>
                    <span class="font-weight-bold">{{ formatRub(trades.currentTrade['price']) }}</span>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="20" color="primary">mdi-bitcoin</v-icon></template>
                  <v-list-item-title>Количество</v-list-item-title>
                  <template #append>
                    <span class="font-weight-medium">{{ formatNumber(trades.currentTrade['quantity'], 8) }}</span>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="20" color="primary">mdi-cash</v-icon></template>
                  <v-list-item-title>Сумма (фиат)</v-list-item-title>
                  <template #append>
                    <span class="font-weight-bold text-h6">{{ formatRub(trades.currentTrade['fiatAmount']) }}</span>
                  </template>
                </v-list-item>
                <v-list-item v-if="0 < trades.currentTrade['fee']">
                  <template #prepend><v-icon size="20" color="primary">mdi-percent</v-icon></template>
                  <v-list-item-title>Комиссия</v-list-item-title>
                  <template #append>
                    <span>{{ formatNumber(trades.currentTrade['fee']) }}</span>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="20" color="primary">mdi-calendar</v-icon></template>
                  <v-list-item-title>Создана</v-list-item-title>
                  <template #append>
                    <span class="text-body-2 text-medium-emphasis">{{ formatDate(trades.currentTrade['createdAt']) }}</span>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </UiParentCard>

          <!-- Таймер обратного отсчёта -->
          <TradeCountdown
            v-if="showCountdown"
            :deadline="trades.currentTrade['paymentDeadline']!"
            :total-seconds="900"
            class="mt-4"
          />


          <!-- Действия -->
          <v-card class="trade-actions mt-4" rounded="lg">
            <v-card-text class="pa-5">
              <div class="d-flex flex-column ga-3">
                <v-btn
                  v-if="canConfirmPayment"
                  color="secondary"
                  variant="flat"
                  block
                  size="large"
                  rounded="lg"
                  elevation="2"
                  :loading="trades.actionLoading"
                  prepend-icon="mdi-check-circle-outline"
                  @click="confirmPaymentDialog = true"
                >
                  Я оплатил
                </v-btn>

                <v-btn
                  v-if="canRelease"
                  color="success"
                  variant="flat"
                  block
                  size="large"
                  rounded="lg"
                  elevation="2"
                  :loading="trades.actionLoading"
                  prepend-icon="mdi-check-all"
                  @click="releaseDialog = true"
                >
                  Отпустить средства
                </v-btn>

                <v-btn
                  v-if="canOpenAdvertisement"
                  variant="outlined"
                  block
                  size="large"
                  rounded="lg"
                  prepend-icon="mdi-bullhorn-outline"
                  @click="openCurrentAdvertisement"
                >
                  Открыть объявление
                </v-btn>

                <v-btn
                  variant="outlined"
                  block
                  size="large"
                  rounded="lg"
                  prepend-icon="mdi-open-in-new"
                  @click="openBybitTradePage"
                >
                  Перейти на Bybit
                </v-btn>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Чат на всю ширину -->
      <v-row class="mt-2">
        <v-col cols="12">
          <TradeChat :trade-id="tradeId" :readonly="isChatReadonly" />
        </v-col>
      </v-row>
    </template>

    <!-- Диалог подтверждения оплаты -->
    <v-dialog v-model="confirmPaymentDialog" max-width="500">
      <v-card rounded="lg">
        <v-card-item class="px-6 py-5">
          <template #prepend>
            <v-avatar size="48" color="primary" variant="tonal">
              <v-icon>mdi-check-circle-outline</v-icon>
            </v-avatar>
          </template>
          <v-card-title class="text-h6 font-weight-bold">Подтвердить оплату</v-card-title>
          <v-card-subtitle class="text-wrap">Убедитесь, что вы совершили перевод перед подтверждением.</v-card-subtitle>
        </v-card-item>
        <v-divider />
        <v-card-text class="px-6 py-5">
          <v-text-field v-model="paymentType" label="Тип оплаты" variant="outlined" density="compact" class="mb-3" />
          <v-text-field v-model="paymentId" label="ID платежа (опционально)" variant="outlined" density="compact" />
        </v-card-text>
        <v-divider />
        <v-card-actions class="px-6 py-4">
          <v-spacer />
          <v-btn variant="text" @click="confirmPaymentDialog = false">Отмена</v-btn>
          <v-btn color="secondary" variant="flat" rounded="lg" :loading="trades.actionLoading" @click="handleConfirmPayment"> Подтвердить </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Диалог подтверждения получения -->
    <v-dialog v-model="releaseDialog" max-width="500">
      <v-card rounded="lg">
        <v-card-item class="px-6 py-5">
          <template #prepend>
            <v-avatar size="48" color="success" variant="tonal">
              <v-icon>mdi-check-all</v-icon>
            </v-avatar>
          </template>
          <v-card-title class="text-h6 font-weight-bold">Отпустить средства</v-card-title>
          <v-card-subtitle class="text-wrap">Криптовалюта будет передана покупателю.</v-card-subtitle>
        </v-card-item>
        <v-divider />
        <v-card-text class="px-6 py-5">
          <v-alert type="warning" variant="tonal">
            Подтвердите только после получения оплаты! Это действие нельзя отменить.
          </v-alert>
        </v-card-text>
        <v-divider />
        <v-card-actions class="px-6 py-4">
          <v-spacer />
          <v-btn variant="text" @click="releaseDialog = false">Отмена</v-btn>
          <v-btn color="success" variant="flat" rounded="lg" :loading="trades.actionLoading" @click="handleRelease"> Подтвердить </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<style scoped lang="scss">
.trade-actions {
  border: 1px solid rgba(15, 23, 42, 0.08);
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
}
</style>
