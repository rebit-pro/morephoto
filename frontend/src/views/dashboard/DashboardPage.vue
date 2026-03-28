<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { HistoryIcon, PlugConnectedIcon, WalletIcon } from 'vue-tabler-icons';
import { useAuthStore } from '@/stores/auth';
import { useWalletStore } from '@/stores/wallet';
import { useIdentityStore } from '@/stores/identity';
import { useExchangeStore } from '@/stores/exchange';
import type { OrderBookEntry } from '@/api/exchange';
import AppEmptyState from '@/components/shared/AppEmptyState.vue';

const auth = useAuthStore();
const wallet = useWalletStore();
const identity = useIdentityStore();
const exchange = useExchangeStore();
const isPageLoading = ref(true);

const txLabels: Record<string, string> = {
  deposit: 'Депозит',
  withdrawal: 'Вывод',
  trade_buy: 'Покупка',
  trade_sell: 'Продажа',
  lock: 'Блокировка',
  unlock: 'Разблокировка',
  fee: 'Комиссия'
};

const txColors: Record<string, string> = {
  deposit: 'success',
  withdrawal: 'error',
  trade_buy: 'info',
  trade_sell: 'warning',
  lock: 'grey',
  unlock: 'grey',
  fee: 'error'
};

const txIcons: Record<string, string> = {
  trade_sell: 'mdi-arrow-top-right',
  trade_buy: 'mdi-arrow-bottom-left',
  lock: 'mdi-lock-outline',
  fee: 'mdi-percent-outline'
};

const userDisplayName = computed(() => auth.user?.['name'] ?? auth.user?.['email'] ?? '');
const hasConnectionMode = computed(() => null !== identity.connectionStatus?.['mode']);
const latestTransactions = computed(() => wallet.transactions.slice(0, 4));
const balancesWithFundsCount = computed(() => wallet.balances.filter((balance) => parseAmount(balance.total) > 0).length);
const lockedBalancesCount = computed(() => wallet.balances.filter((balance) => parseAmount(balance.locked) > 0).length);
const sortedBalances = computed(() => {
  return [...wallet.balances].sort((left, right) => parseAmount(right.total) - parseAmount(left.total));
});
const bestBuyOrder = computed(() => {
  return exchange.buyOrders.reduce(
    (best, current) => {
      if (null === best || parseAmount(current.price) > parseAmount(best.price)) {
        return current;
      }

      return best;
    },
    null as OrderBookEntry | null
  );
});
const bestSellOrder = computed(() => {
  return exchange.sellOrders.reduce(
    (best, current) => {
      if (null === best || parseAmount(current.price) < parseAmount(best.price)) {
        return current;
      }

      return best;
    },
    null as OrderBookEntry | null
  );
});
const spread = computed(() => {
  if (null === bestBuyOrder.value || null === bestSellOrder.value) {
    return null;
  }

  return parseAmount(bestSellOrder.value.price) - parseAmount(bestBuyOrder.value.price);
});
const spreadPercent = computed(() => {
  if (null === spread.value || null === bestSellOrder.value) {
    return null;
  }

  const bestSellPrice = parseAmount(bestSellOrder.value.price);

  if (0 === bestSellPrice) {
    return null;
  }

  return (spread.value / bestSellPrice) * 100;
});
const lastTransactionDate = computed(() => {
  const [lastTransaction] = wallet.transactions;

  if (undefined === lastTransaction) {
    return 'Нет операций';
  }

  return formatDate(lastTransaction.createdAt);
});

function parseAmount(value: string): number {
  const parsedValue = Number.parseFloat(value);

  return Number.isNaN(parsedValue) ? 0 : parsedValue;
}

function formatAmount(value: string | number, maximumFractionDigits = 2): string {
  const parsedValue = 'number' === typeof value ? value : parseAmount(value);

  return new Intl.NumberFormat('ru-RU', {
    minimumFractionDigits: 0,
    maximumFractionDigits
  }).format(parsedValue);
}

function formatDate(value: string): string {
  return new Date(value).toLocaleString('ru-RU');
}

function txLabel(type: string): string {
  return txLabels[type] ?? type;
}

function txColor(type: string): string {
  return txColors[type] ?? 'default';
}

function txIcon(type: string): string {
  return txIcons[type] ?? 'mdi-swap-horizontal';
}

function connectionStateText(): string {
  if (identity.hasActiveConnection) {
    return 'Подключение активно';
  }

  if (identity.isConnected) {
    return 'Подключение требует внимания';
  }

  return 'API не подключён';
}

function connectionStateColor(): string {
  if (identity.hasActiveConnection) {
    return 'success';
  }

  if (identity.isConnected) {
    return 'warning';
  }

  return 'error';
}

async function loadDashboard(): Promise<void> {
  isPageLoading.value = true;

  try {
    await identity.fetchStatus();
    exchange.setOrderBookAccess(identity.hasActiveConnection);

    const tasks: Array<Promise<void>> = [
      wallet.fetchBalances(),
      wallet.fetchTransactions({ limit: 4, offset: 0 }),
      exchange.fetchCurrencyPairs()
    ];

    if (identity.hasActiveConnection) {
      tasks.push(exchange.fetchOrderBook());
    }

    await Promise.allSettled(tasks);
  } finally {
    isPageLoading.value = false;
  }
}

onMounted(async () => {
  await loadDashboard();
});
</script>

<template>
  <div class="dashboard-page">
    <v-card class="dashboard-hero mb-6" rounded="xl">
      <v-card-text class="pa-6 pa-md-8">
        <v-row align="center">
          <v-col cols="12" md="8">
            <div class="d-flex align-center flex-wrap ga-3 mb-4">
              <v-chip :color="connectionStateColor()" variant="tonal" size="small">
                {{ connectionStateText() }}
              </v-chip>
              <v-chip v-if="hasConnectionMode" color="primary" variant="tonal" size="small">
                {{ identity.modeLabel }}
              </v-chip>
              <v-chip color="secondary" variant="tonal" size="small"> Пара: {{ exchange.selectedPair.label }} </v-chip>
            </div>

            <h1 class="text-h4 text-md-h3 font-weight-bold mb-2">Добро пожаловать, {{ userDisplayName }}</h1>
            <p class="text-body-1 text-medium-emphasis mb-0 dashboard-hero__subtitle">
              Следите за текущим состоянием балансов, последними операциями и лучшими предложениями рынка в одном месте.
            </p>
          </v-col>

          <v-col cols="12" md="4">
            <div class="d-flex flex-column ga-3 dashboard-hero__actions">
              <v-btn color="primary" size="large" prepend-icon="mdi-swap-horizontal-bold" to="/orderbook"> Открыть P2P стакан </v-btn>
              <v-btn variant="outlined" size="large" prepend-icon="mdi-link-variant" to="/profile/api-connection">
                Настроить Bybit API
              </v-btn>
            </div>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-alert v-if="identity.error" type="error" variant="tonal" class="mb-4">{{ identity.error }}</v-alert>
    <v-alert v-if="wallet.error" type="error" variant="tonal" class="mb-4">{{ wallet.error }}</v-alert>
    <v-alert v-if="exchange.error" type="error" variant="tonal" class="mb-4">{{ exchange.error }}</v-alert>

    <v-row v-if="isPageLoading" justify="center" class="mt-8">
      <v-progress-circular indeterminate color="primary" size="56" />
    </v-row>

    <template v-else>
      <v-row class="mb-2">
        <v-col cols="12" sm="6" xl="3">
          <v-card class="dashboard-card dashboard-metric-card" rounded="xl">
            <v-card-text class="pa-5">
              <div class="d-flex align-center justify-space-between mb-3">
                <span class="text-body-2 text-medium-emphasis">Статус Bybit API</span>
                <v-avatar size="42" :color="connectionStateColor()" variant="tonal">
                  <v-icon>{{ identity.hasActiveConnection ? 'mdi-shield-check-outline' : 'mdi-link-variant-off' }}</v-icon>
                </v-avatar>
              </div>
              <div class="text-h5 font-weight-bold mb-1">{{ identity.statusLabel ?? connectionStateText() }}</div>
              <div class="text-body-2 text-medium-emphasis">
                {{ hasConnectionMode ? `Режим: ${identity.modeLabel}` : 'Подключите API для торговли и синхронизации данных' }}
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" xl="3">
          <v-card class="dashboard-card dashboard-metric-card" rounded="xl">
            <v-card-text class="pa-5">
              <div class="d-flex align-center justify-space-between mb-3">
                <span class="text-body-2 text-medium-emphasis">Балансы с активами</span>
                <v-avatar size="42" color="primary" variant="tonal">
                  <v-icon>mdi-wallet-outline</v-icon>
                </v-avatar>
              </div>
              <div class="text-h5 font-weight-bold mb-1">{{ balancesWithFundsCount }}</div>
              <div class="text-body-2 text-medium-emphasis">Всего валют в кабинете: {{ wallet.balances.length }}</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" xl="3">
          <v-card class="dashboard-card dashboard-metric-card" rounded="xl">
            <v-card-text class="pa-5">
              <div class="d-flex align-center justify-space-between mb-3">
                <span class="text-body-2 text-medium-emphasis">Заблокированные позиции</span>
                <v-avatar size="42" color="warning" variant="tonal">
                  <v-icon>mdi-lock-outline</v-icon>
                </v-avatar>
              </div>
              <div class="text-h5 font-weight-bold mb-1">{{ lockedBalancesCount }}</div>
              <div class="text-body-2 text-medium-emphasis">Показывает, по скольким валютам есть заблокированные средства</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" xl="3">
          <v-card class="dashboard-card dashboard-metric-card" rounded="xl">
            <v-card-text class="pa-5">
              <div class="d-flex align-center justify-space-between mb-3">
                <span class="text-body-2 text-medium-emphasis">Последняя активность</span>
                <v-avatar size="42" color="info" variant="tonal">
                  <v-icon>mdi-history</v-icon>
                </v-avatar>
              </div>
              <div class="text-h6 font-weight-bold mb-1">{{ lastTransactionDate }}</div>
              <div class="text-body-2 text-medium-emphasis">Последняя транзакция или обновление истории операций</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" xl="8">
          <v-card class="dashboard-card h-100" rounded="xl">
            <v-card-item class="px-5 pt-5 pb-2">
              <template #prepend>
                <v-avatar size="42" color="primary" variant="tonal">
                  <v-icon>mdi-wallet</v-icon>
                </v-avatar>
              </template>
              <v-card-title class="text-h6 font-weight-bold">Текущее состояние балансов</v-card-title>
              <v-card-subtitle>Ключевые валюты и доступные средства на вашем аккаунте</v-card-subtitle>
              <template #append>
                <v-btn variant="text" color="primary" to="/wallet/balances">Все балансы</v-btn>
              </template>
            </v-card-item>

            <v-card-text class="pa-5 pt-3">
              <v-row v-if="0 < sortedBalances.length">
                <v-col v-for="balance in sortedBalances" :key="balance.currency" cols="12" md="6">
                  <v-sheet class="dashboard-mini-card pa-4" rounded="xl">
                    <div class="d-flex align-start justify-space-between ga-3 mb-4">
                      <div class="d-flex align-center ga-3">
                        <v-avatar size="44" color="primary" variant="tonal">
                          <span class="text-body-2 font-weight-bold">{{ balance.currency }}</span>
                        </v-avatar>
                        <div>
                          <div class="text-subtitle-1 font-weight-bold">{{ balance.currency }}</div>
                          <div class="text-body-2 text-medium-emphasis">Всего: {{ formatAmount(balance.total, 8) }}</div>
                        </div>
                      </div>

                      <v-chip :color="parseAmount(balance.locked) > 0 ? 'warning' : 'success'" variant="tonal" size="small">
                        {{ parseAmount(balance.locked) > 0 ? 'Есть блокировка' : 'Доступно' }}
                      </v-chip>
                    </div>

                    <div class="d-flex justify-space-between ga-3 mb-2">
                      <div>
                        <div class="text-caption text-medium-emphasis">Доступно</div>
                        <div class="text-h6 font-weight-bold">{{ formatAmount(balance.available, 8) }}</div>
                      </div>
                      <div class="text-right">
                        <div class="text-caption text-medium-emphasis">Заблокировано</div>
                        <div class="text-subtitle-1 font-weight-medium">{{ formatAmount(balance.locked, 8) }}</div>
                      </div>
                    </div>
                  </v-sheet>
                </v-col>
              </v-row>

              <AppEmptyState
                v-else
                :icon="WalletIcon"
                tone="primary"
                title="Балансы пока пусты"
                description="Подключите Bybit API, чтобы видеть текущее состояние средств и синхронизацию кошелька прямо на дашборде."
                compact
              >
                <template #actions>
                  <div class="d-flex justify-center">
                    <v-btn color="primary" variant="outlined" to="/profile/api-connection">
                      <template #prepend>
                        <PlugConnectedIcon :size="18" stroke-width="1.75" />
                      </template>
                      Подключить Bybit API
                    </v-btn>
                  </div>
                </template>
              </AppEmptyState>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" xl="4">
          <div class="d-flex flex-column ga-6 h-100">
            <v-card class="dashboard-card" rounded="xl">
              <v-card-item class="px-5 pt-5 pb-2">
                <template #prepend>
                  <v-avatar size="42" color="secondary" variant="tonal">
                    <v-icon>mdi-trending-up</v-icon>
                  </v-avatar>
                </template>
                <v-card-title class="text-h6 font-weight-bold">Выгодная покупка и продажа</v-card-title>
                <v-card-subtitle>Лучшие цены по паре {{ exchange.selectedPair.label }}</v-card-subtitle>
              </v-card-item>

              <v-card-text class="pa-5 pt-3">
                <template v-if="identity.hasActiveConnection && null !== bestBuyOrder && null !== bestSellOrder">
                  <v-sheet class="dashboard-market-card dashboard-market-card--buy pa-4 mb-4" rounded="xl">
                    <div class="d-flex align-center justify-space-between mb-2">
                      <span class="text-body-2 font-weight-medium">Лучшая покупка</span>
                      <v-chip color="success" variant="tonal" size="small">Buy</v-chip>
                    </div>
                    <div class="text-h5 font-weight-bold mb-1">{{ formatAmount(bestBuyOrder.price) }} ₽</div>
                    <div class="text-body-2 text-medium-emphasis mb-2">{{ bestBuyOrder.username }}</div>
                    <div class="text-caption text-medium-emphasis">
                      Лимит: {{ formatAmount(bestBuyOrder.minLimit) }} — {{ formatAmount(bestBuyOrder.maxLimit) }} ₽
                    </div>
                  </v-sheet>

                  <v-sheet class="dashboard-market-card dashboard-market-card--sell pa-4 mb-4" rounded="xl">
                    <div class="d-flex align-center justify-space-between mb-2">
                      <span class="text-body-2 font-weight-medium">Лучшая продажа</span>
                      <v-chip color="error" variant="tonal" size="small">Sell</v-chip>
                    </div>
                    <div class="text-h5 font-weight-bold mb-1">{{ formatAmount(bestSellOrder.price) }} ₽</div>
                    <div class="text-body-2 text-medium-emphasis mb-2">{{ bestSellOrder.username }}</div>
                    <div class="text-caption text-medium-emphasis">
                      Лимит: {{ formatAmount(bestSellOrder.minLimit) }} — {{ formatAmount(bestSellOrder.maxLimit) }} ₽
                    </div>
                  </v-sheet>

                  <v-alert v-if="null !== spread && null !== spreadPercent" type="info" variant="tonal">
                    Спрэд: <strong>{{ formatAmount(spread) }} ₽</strong>
                    <span class="text-medium-emphasis"> ({{ formatAmount(spreadPercent) }}%)</span>
                  </v-alert>
                </template>

                <v-alert
                  v-else-if="!identity.hasActiveConnection"
                  type="warning"
                  variant="tonal"
                  text="Подключите активный Bybit API, чтобы видеть лучшие предложения рынка на дашборде."
                />

                <v-alert v-else type="info" variant="tonal" text="Для выбранной пары пока нет данных по лучшим предложениям." />
              </v-card-text>
            </v-card>

            <v-card class="dashboard-card flex-grow-1" rounded="xl">
              <v-card-item class="px-5 pt-5 pb-2">
                <template #prepend>
                  <v-avatar size="42" color="info" variant="tonal">
                    <v-icon>mdi-bell-outline</v-icon>
                  </v-avatar>
                </template>
                <v-card-title class="text-h6 font-weight-bold">Важная информация</v-card-title>
                <v-card-subtitle>То, на что стоит обратить внимание прямо сейчас</v-card-subtitle>
              </v-card-item>

              <v-card-text class="pa-5 pt-3 d-flex flex-column ga-3">
                <v-alert
                  v-if="!identity.hasActiveConnection"
                  type="warning"
                  variant="tonal"
                  title="Подключение требует действия"
                  text="Без активного Bybit API часть функций кабинета и рынок P2P будут недоступны."
                />

                <v-alert
                  v-else
                  type="success"
                  variant="tonal"
                  title="API подключён"
                  :text="`Можно работать со стаканом и актуальными данными по паре ${exchange.selectedPair.label}.`"
                />

                <v-alert
                  v-if="0 < lockedBalancesCount"
                  type="info"
                  variant="tonal"
                  :text="`Есть ${lockedBalancesCount} ${1 === lockedBalancesCount ? 'позиция' : 'позиции'} с заблокированными средствами. Проверьте активные объявления и сделки.`"
                />

                <v-alert
                  v-if="0 === latestTransactions.length"
                  type="info"
                  variant="tonal"
                  text="Сделок пока не было. Когда появятся первые операции, они отобразятся в истории ниже."
                />
              </v-card-text>
            </v-card>
          </div>
        </v-col>
      </v-row>

      <v-row class="mt-1">
        <v-col cols="12" lg="8">
          <v-card class="dashboard-card h-100" rounded="xl">
            <v-card-item class="px-5 pt-5 pb-2">
              <template #prepend>
                <v-avatar size="42" color="info" variant="tonal">
                  <v-icon>mdi-history</v-icon>
                </v-avatar>
              </template>
              <v-card-title class="text-h6 font-weight-bold">Последние транзакции</v-card-title>
              <v-card-subtitle>Последние изменения по балансу и торговым операциям</v-card-subtitle>
              <template #append>
                <v-btn variant="text" color="primary" to="/wallet/transactions">Вся история</v-btn>
              </template>
            </v-card-item>

            <v-card-text class="pa-0">
              <v-list v-if="0 < latestTransactions.length" lines="two">
                <v-list-item v-for="tx in latestTransactions" :key="tx.id" class="px-5 py-3">
                  <template #prepend>
                    <v-avatar size="40" :color="txColor(tx.type)" variant="tonal">
                      <v-icon>
                        {{ txIcon(tx.type) }}
                      </v-icon>
                    </v-avatar>
                  </template>

                  <v-list-item-title class="d-flex align-center flex-wrap ga-2">
                    <span class="font-weight-medium">{{ txLabel(tx.type) }}</span>
                    <v-chip size="x-small" variant="tonal" :color="txColor(tx.type)">{{ tx.currency }}</v-chip>
                  </v-list-item-title>

                  <v-list-item-subtitle>
                    {{ formatDate(tx.createdAt) }}
                    <span v-if="null !== tx.tradeId"> · Сделка #{{ tx.tradeId }}</span>
                  </v-list-item-subtitle>

                  <template #append>
                    <div class="text-right">
                      <div class="font-weight-bold">{{ formatAmount(tx.amount, 8) }}</div>
                      <div class="text-caption text-medium-emphasis">{{ tx.currency }}</div>
                    </div>
                  </template>
                </v-list-item>
              </v-list>

              <AppEmptyState
                v-else
                :icon="HistoryIcon"
                tone="info"
                title="Сделок пока не было"
                description="Когда появятся первые транзакции, они сразу отобразятся в этом блоке и будут доступны в полной истории операций."
                compact
              />
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" lg="4">
          <v-card class="dashboard-card h-100" rounded="xl">
            <v-card-item class="px-5 pt-5 pb-2">
              <template #prepend>
                <v-avatar size="42" color="success" variant="tonal">
                  <v-icon>mdi-lightning-bolt-outline</v-icon>
                </v-avatar>
              </template>
              <v-card-title class="text-h6 font-weight-bold">Быстрые действия</v-card-title>
              <v-card-subtitle>Часто используемые разделы кабинета</v-card-subtitle>
            </v-card-item>

            <v-card-text class="pa-5 pt-3">
              <div class="d-flex flex-column ga-3">
                <v-btn color="primary" variant="flat" block prepend-icon="mdi-swap-horizontal-bold" to="/orderbook">
                  Перейти в стакан
                </v-btn>
                <v-btn color="secondary" variant="tonal" block prepend-icon="mdi-wallet-outline" to="/wallet/balances">
                  Открыть балансы
                </v-btn>
                <v-btn color="info" variant="tonal" block prepend-icon="mdi-history" to="/wallet/transactions">
                  Посмотреть транзакции
                </v-btn>
                <v-btn color="default" variant="outlined" block prepend-icon="mdi-account-circle-outline" to="/profile">
                  Мой профиль
                </v-btn>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </template>
  </div>
</template>

<style scoped lang="scss">
.dashboard-page {
  display: flex;
  flex-direction: column;
}

.dashboard-hero {
  border: 1px solid rgba(var(--v-theme-primary), 0.12);
  background:
    radial-gradient(circle at top right, rgba(var(--v-theme-secondary), 0.12), transparent 35%),
    linear-gradient(135deg, rgba(var(--v-theme-primary), 0.12), rgba(var(--v-theme-surface), 1));
  overflow: hidden;
}

.dashboard-hero__subtitle {
  max-width: 640px;
}

.dashboard-hero__actions {
  max-width: 320px;
  margin-left: auto;
}

.dashboard-card {
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
}

.dashboard-metric-card {
  height: 100%;
}

.dashboard-mini-card {
  border: 1px solid rgba(var(--v-theme-on-surface), 0.07);
  background: rgba(var(--v-theme-surface), 0.9);
}

.dashboard-market-card {
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.dashboard-market-card--buy {
  background: rgba(var(--v-theme-success), 0.06);
}

.dashboard-market-card--sell {
  background: rgba(var(--v-theme-error), 0.05);
}

@media (max-width: 959px) {
  .dashboard-hero__actions {
    max-width: 100%;
    margin-left: 0;
  }
}
</style>
