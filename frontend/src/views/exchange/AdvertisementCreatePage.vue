<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useExchangeStore } from '@/stores/exchange';
import { useAdvertisementsStore } from '@/stores/advertisements';
import { useChatScriptsStore } from '@/stores/chatScripts';
import type { CreateAdvertisementPayload, AdvertisementSide, PriceType } from '@/api/exchange';
import { isMockApiEnabled } from '@/mocks/config';
import UiFormCard from '@/components/shared/UiFormCard.vue';

const router = useRouter();
const exchange = useExchangeStore();
const ads = useAdvertisementsStore();
const chatScripts = useChatScriptsStore();

const formError = ref<string | null>(null);
const submitting = ref(false);

// Форма
const side = ref<AdvertisementSide>('sell');
const currencyPairId = ref<number | null>(null);
const priceType = ref<PriceType>('fixed');
const price = ref('');
const premium = ref('');
const quantity = ref('');
const minAmount = ref('');
const maxAmount = ref('');
const selectedPaymentMethodIds = ref<string[]>([]);
const paymentPeriod = ref(15);
const conditions = ref('');
const chatScriptId = ref<number | null>(null);
const createAsActive = ref(true);

const sideOptions = [
  { title: 'Продать', value: 'sell' },
  { title: 'Купить', value: 'buy' }
];

const priceTypeOptions = [
  { title: 'Фиксированная', value: 'fixed' },
  { title: 'Плавающая', value: 'floating' }
];

const paymentPeriodOptions = [
  { title: '15 минут', value: 15 },
  { title: '30 минут', value: 30 },
  { title: '60 минут', value: 60 }
];

const currencyPairOptions = computed(() =>
  exchange.currencyPairs.map((pair) => ({
    title: pair.label,
    value: pair.id
  }))
);

const paymentMethodOptions = computed(() =>
  exchange.paymentMethods.map((method) => ({
    title: method.name,
    value: method.id
  }))
);

const chatScriptOptions = computed(() => [
  { title: 'Без скрипта', value: null as number | null },
  ...chatScripts.scripts.filter((s) => s.isActive).map((s) => ({ title: s.name, value: s.id as number | null }))
]);

const isFormValid = computed(() => {
  return (
    null !== currencyPairId.value &&
    '' !== price.value &&
    '' !== quantity.value &&
    '' !== minAmount.value &&
    '' !== maxAmount.value &&
    0 < selectedPaymentMethodIds.value.length
  );
});

async function handleSubmit(): Promise<void> {
  if (!isFormValid.value || null === currencyPairId.value) return;

  formError.value = null;
  submitting.value = true;

  const payload: CreateAdvertisementPayload = {
    currencyPairId: currencyPairId.value,
    side: side.value,
    priceType: priceType.value,
    price: price.value,
    premium: '' !== premium.value ? premium.value : null,
    quantity: quantity.value,
    minAmount: minAmount.value,
    maxAmount: maxAmount.value,
    paymentMethodIds: selectedPaymentMethodIds.value,
    paymentPeriod: paymentPeriod.value,
    conditions: conditions.value,
    chatScriptId: chatScriptId.value,
    tradingPreferenceSet: {}
  };

  try {
    const advertisement = await ads.createAdvertisement(payload);

    if (!createAsActive.value) {
      await ads.toggleAdvertisement(advertisement.id, 'paused');
    }

    await router.push('/exchange/advertisements');
  } catch (e: unknown) {
    formError.value = e instanceof Error ? e.message : 'Ошибка создания объявления';
  } finally {
    submitting.value = false;
  }
}

onMounted(async () => {
  await Promise.all([exchange.fetchCurrencyPairs(), exchange.fetchPaymentMethods(), chatScripts.fetchScripts()]);

  // Установить дефолтную пару
  const firstPair = exchange.currencyPairs[0];
  if (undefined !== firstPair && null === currencyPairId.value) {
    currencyPairId.value = firstPair.id;
  }
});
</script>

<template>
  <div>
    <v-btn variant="text" class="mb-4" prepend-icon="mdi-arrow-left" @click="router.push('/exchange/advertisements')">
      К объявлениям
    </v-btn>

    <h2 class="text-h4 font-weight-bold mb-6">Создать объявление</h2>

    <v-alert v-if="isMockApiEnabled" type="info" variant="tonal" class="mb-4">
      В mock-режиме активное объявление автоматически получает новую сделку через несколько секунд. Если выбрать сценарий с QR/файлом,
      первый шаг отправится в чат сделки автоматически.
    </v-alert>

    <v-alert v-if="formError" type="error" variant="tonal" class="mb-4">{{ formError }}</v-alert>
    <v-alert v-if="ads.error" type="error" variant="tonal" class="mb-4">{{ ads.error }}</v-alert>

    <UiFormCard
      title="Параметры объявления"
      description="Заполните все обязательные поля для создания объявления на P2P рынке"
      icon="mdi-bullhorn-outline"
      color="primary"
    >
        <v-row>
          <!-- Направление -->
          <v-col cols="12" sm="6">
            <v-btn-toggle v-model="side" mandatory color="primary" class="mb-4" density="compact">
              <v-btn v-for="opt in sideOptions" :key="opt.value" :value="opt.value">
                {{ opt.title }}
              </v-btn>
            </v-btn-toggle>
          </v-col>
        </v-row>

        <v-row>
          <!-- Валютная пара -->
          <v-col cols="12" sm="6">
            <v-select
              v-model="currencyPairId"
              :items="currencyPairOptions"
              item-title="title"
              item-value="value"
              label="Валютная пара *"
              variant="outlined"
              density="compact"
            />
          </v-col>

          <!-- Тип цены -->
          <v-col cols="12" sm="6">
            <v-select
              v-model="priceType"
              :items="priceTypeOptions"
              item-title="title"
              item-value="value"
              label="Тип цены"
              variant="outlined"
              density="compact"
            />
          </v-col>
        </v-row>

        <v-row>
          <!-- Цена -->
          <v-col cols="12" sm="6">
            <v-text-field v-model="price" label="Цена *" variant="outlined" density="compact" type="number" step="0.01" />
          </v-col>

          <!-- Премиум -->
          <v-col v-if="'floating' === priceType" cols="12" sm="6">
            <v-text-field v-model="premium" label="Премиум (%)" variant="outlined" density="compact" type="number" step="0.01" />
          </v-col>
        </v-row>

        <v-row>
          <!-- Количество -->
          <v-col cols="12" sm="4">
            <v-text-field v-model="quantity" label="Количество *" variant="outlined" density="compact" type="number" step="0.01" />
          </v-col>

          <!-- Мин сумма -->
          <v-col cols="12" sm="4">
            <v-text-field v-model="minAmount" label="Мин. сумма (₽) *" variant="outlined" density="compact" type="number" />
          </v-col>

          <!-- Макс сумма -->
          <v-col cols="12" sm="4">
            <v-text-field v-model="maxAmount" label="Макс. сумма (₽) *" variant="outlined" density="compact" type="number" />
          </v-col>
        </v-row>

        <v-row>
          <!-- Методы оплаты -->
          <v-col cols="12" sm="6">
            <v-select
              v-model="selectedPaymentMethodIds"
              :items="paymentMethodOptions"
              item-title="title"
              item-value="value"
              label="Методы оплаты *"
              variant="outlined"
              density="compact"
              multiple
              chips
              closable-chips
            />
          </v-col>

          <!-- Время на оплату -->
          <v-col cols="12" sm="6">
            <v-select
              v-model="paymentPeriod"
              :items="paymentPeriodOptions"
              item-title="title"
              item-value="value"
              label="Время на оплату"
              variant="outlined"
              density="compact"
            />
          </v-col>
        </v-row>

        <v-row>
          <!-- Скрипт чата -->
          <v-col cols="12" sm="6">
            <v-select
              v-model="chatScriptId"
              :items="chatScriptOptions"
              item-title="title"
              item-value="value"
              label="Скрипт автосообщений"
              variant="outlined"
              density="compact"
            />
          </v-col>
          <v-col cols="12" sm="6">
            <v-switch v-model="createAsActive" color="success" inset label="Включить объявление сразу после создания" hide-details />
          </v-col>
        </v-row>

        <v-row>
          <!-- Условия -->
          <v-col cols="12">
            <v-textarea v-model="conditions" label="Условия сделки" variant="outlined" density="compact" rows="3" />
          </v-col>
        </v-row>

      <template #actions>
        <v-spacer />
        <v-btn variant="text" @click="router.push('/exchange/advertisements')">Отмена</v-btn>
        <v-btn color="secondary" variant="flat" rounded="lg" size="large" :loading="submitting" :disabled="!isFormValid" @click="handleSubmit"> Создать объявление </v-btn>
      </template>
    </UiFormCard>
  </div>
</template>
