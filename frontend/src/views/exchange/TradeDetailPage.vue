<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTradesStore } from '@/stores/trades';
import { usePolling } from '@/composables/usePolling';
import TradeChat from './components/TradeChat.vue';

const route = useRoute();
const router = useRouter();
const trades = useTradesStore();

const tradeId = computed(() => Number(route.params.id));

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
  disputed: 'Спор',
};

const statusColors: Record<string, string> = {
  pending_payment: 'warning',
  payment_sent: 'info',
  payment_confirmed: 'primary',
  completed: 'success',
  cancelled: 'error',
  disputed: 'error',
};

const trade = computed(() => trades.currentTrade);

const isChatReadonly = computed(() => {
  if (!trade.value) return true;
  return 'completed' === trade.value.status || 'cancelled' === trade.value.status;
});

const canConfirmPayment = computed(() => {
  return trade.value && 'pending_payment' === trade.value.status && 'buy' === trade.value.side;
});

const canRelease = computed(() => {
  return trade.value && 'payment_sent' === trade.value.status && 'sell' === trade.value.side;
});

const timeRemaining = ref('');
let countdownTimer: ReturnType<typeof setInterval> | null = null;

function updateCountdown(): void {
  if (!trade.value?.paymentDeadline) {
    timeRemaining.value = '';
    return;
  }
  const deadline = new Date(trade.value.paymentDeadline).getTime();
  const now = Date.now();
  const diff = deadline - now;

  if (diff <= 0) {
    timeRemaining.value = 'Время вышло';
    return;
  }

  const minutes = Math.floor(diff / 60000);
  const seconds = Math.floor((diff % 60000) / 1000);
  timeRemaining.value = `${minutes}:${String(seconds).padStart(2, '0')}`;
}

function formatDate(iso: string): string {
  return new Date(iso).toLocaleString('ru-RU');
}

async function loadTrade(): Promise<void> {
  await trades.fetchTradeDetail(tradeId.value);
}

async function handleConfirmPayment(): Promise<void> {
  try {
    await trades.confirmPayment(tradeId.value, {
      paymentType: paymentType.value,
      paymentId: paymentId.value,
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

const polling = usePolling(loadTrade, 10000);

onMounted(async () => {
  await loadTrade();
  updateCountdown();
  countdownTimer = setInterval(updateCountdown, 1000);
  polling.start();
});

onUnmounted(() => {
  polling.stop();
  if (null !== countdownTimer) {
    clearInterval(countdownTimer);
  }
  trades.clearCurrentTrade();
});
</script>

<template>
  <div>
    <v-btn variant="text" class="mb-4" prepend-icon="mdi-arrow-left" @click="router.push('/exchange/trades')">
      К списку сделок
    </v-btn>

    <v-row v-if="trades.loading && !trade" justify="center" class="mt-8">
      <v-progress-circular indeterminate color="primary" />
    </v-row>

    <v-alert v-if="trades.error" type="error" variant="tonal" class="mb-4">{{ trades.error }}</v-alert>

    <template v-if="trade">
      <div class="d-flex align-center justify-space-between mb-6 flex-wrap ga-3">
        <h2 class="text-h4">Сделка #{{ trade.id }}</h2>
        <v-chip :color="statusColors[trade.status] ?? 'default'" variant="tonal">
          {{ statusLabels[trade.status] ?? trade.status }}
        </v-chip>
      </div>

      <v-row>
        <!-- Информация о сделке -->
        <v-col cols="12" md="5">
          <v-card rounded="md" class="mb-4">
            <v-card-title>Информация</v-card-title>
            <v-card-text>
              <v-list density="compact" class="pa-0">
                <v-list-item>
                  <template #prepend><v-icon size="20">mdi-account</v-icon></template>
                  <v-list-item-title>Контрагент</v-list-item-title>
                  <template #append>
                    <span class="font-weight-medium">{{ trade.counterpartyName }}</span>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="20">mdi-swap-horizontal</v-icon></template>
                  <v-list-item-title>Направление</v-list-item-title>
                  <template #append>
                    <v-chip size="small" variant="tonal" :color="'buy' === trade.side ? 'success' : 'error'">
                      {{ 'buy' === trade.side ? 'Покупка' : 'Продажа' }}
                    </v-chip>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="20">mdi-currency-rub</v-icon></template>
                  <v-list-item-title>Цена</v-list-item-title>
                  <template #append>
                    <span class="font-weight-bold">{{ trade.price.toFixed(2) }} ₽</span>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="20">mdi-bitcoin</v-icon></template>
                  <v-list-item-title>Количество</v-list-item-title>
                  <template #append>
                    <span class="font-weight-medium">{{ trade.quantity }}</span>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="20">mdi-cash</v-icon></template>
                  <v-list-item-title>Сумма (фиат)</v-list-item-title>
                  <template #append>
                    <span class="font-weight-bold text-h6">{{ trade.fiatAmount.toFixed(2) }} ₽</span>
                  </template>
                </v-list-item>
                <v-list-item v-if="0 < trade.fee">
                  <template #prepend><v-icon size="20">mdi-percent</v-icon></template>
                  <v-list-item-title>Комиссия</v-list-item-title>
                  <template #append>
                    <span>{{ trade.fee }}</span>
                  </template>
                </v-list-item>
                <v-list-item>
                  <template #prepend><v-icon size="20">mdi-calendar</v-icon></template>
                  <v-list-item-title>Создана</v-list-item-title>
                  <template #append>
                    <span class="text-body-2 text-lightText">{{ formatDate(trade.createdAt) }}</span>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Таймер обратного отсчёта -->
          <v-card
            v-if="trade.paymentDeadline && 'pending_payment' === trade.status"
            rounded="md"
            class="mb-4"
            :color="'Время вышло' === timeRemaining ? 'error' : 'warning'"
            variant="tonal"
          >
            <v-card-text class="text-center">
              <v-icon size="32" class="mb-2">mdi-timer-outline</v-icon>
              <div class="text-body-1 font-weight-medium">Время на оплату</div>
              <div class="text-h4 font-weight-bold">{{ timeRemaining }}</div>
            </v-card-text>
          </v-card>

          <!-- Действия -->
          <v-card rounded="md" class="mb-4">
            <v-card-text>
              <div class="d-flex flex-column ga-2">
                <v-btn
                  v-if="canConfirmPayment"
                  color="primary"
                  block
                  :loading="trades.actionLoading"
                  prepend-icon="mdi-check"
                  @click="confirmPaymentDialog = true"
                >
                  Я оплатил
                </v-btn>

                <v-btn
                  v-if="canRelease"
                  color="success"
                  block
                  :loading="trades.actionLoading"
                  prepend-icon="mdi-check-all"
                  @click="releaseDialog = true"
                >
                  Подтвердить получение
                </v-btn>

                <v-btn
                  variant="outlined"
                  block
                  prepend-icon="mdi-open-in-new"
                  href="https://www.bybit.com/fiat/trade/otc"
                  target="_blank"
                >
                  Перейти на Bybit
                </v-btn>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Чат -->
        <v-col cols="12" md="7">
          <TradeChat :trade-id="tradeId" :readonly="isChatReadonly" />
        </v-col>
      </v-row>
    </template>

    <!-- Диалог подтверждения оплаты -->
    <v-dialog v-model="confirmPaymentDialog" max-width="500">
      <v-card>
        <v-card-title>Подтвердить оплату</v-card-title>
        <v-card-text>
          <p class="mb-4">Убедитесь, что вы совершили перевод перед подтверждением.</p>
          <v-text-field
            v-model="paymentType"
            label="Тип оплаты"
            variant="outlined"
            density="compact"
          />
          <v-text-field
            v-model="paymentId"
            label="ID платежа (опционально)"
            variant="outlined"
            density="compact"
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="confirmPaymentDialog = false">Отмена</v-btn>
          <v-btn color="primary" :loading="trades.actionLoading" @click="handleConfirmPayment">
            Подтвердить
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Диалог подтверждения получения -->
    <v-dialog v-model="releaseDialog" max-width="500">
      <v-card>
        <v-card-title>Подтвердить получение</v-card-title>
        <v-card-text>
          <v-alert type="warning" variant="tonal" class="mb-4">
            Подтвердите только после получения оплаты! Криптовалюта будет передана покупателю.
          </v-alert>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="releaseDialog = false">Отмена</v-btn>
          <v-btn color="success" :loading="trades.actionLoading" @click="handleRelease">
            Подтвердить
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
