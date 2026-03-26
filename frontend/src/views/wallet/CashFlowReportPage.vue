<script setup lang="ts">
import { ref } from 'vue';

const dateFrom = ref('');
const dateTo = ref('');
const currency = ref('');
</script>

<template>
  <div>
    <h2 class="text-h4 mb-6">Обороты денежных средств</h2>

    <!-- Фильтры-заглушки -->
    <v-card rounded="md" class="mb-4">
      <v-card-text>
        <v-row dense>
          <v-col cols="12" sm="3">
            <v-text-field
              v-model="dateFrom"
              label="Период с"
              type="date"
              variant="outlined"
              density="compact"
              hide-details
              disabled
            />
          </v-col>
          <v-col cols="12" sm="3">
            <v-text-field
              v-model="dateTo"
              label="Период по"
              type="date"
              variant="outlined"
              density="compact"
              hide-details
              disabled
            />
          </v-col>
          <v-col cols="12" sm="3">
            <v-select
              v-model="currency"
              :items="['USDT', 'BTC', 'ETH', 'RUB']"
              label="Валюта"
              variant="outlined"
              density="compact"
              hide-details
              disabled
            />
          </v-col>
          <v-col cols="12" sm="3" class="d-flex align-center">
            <v-btn variant="outlined" size="small" disabled prepend-icon="mdi-filter">
              Применить
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Таблица-заглушка -->
    <v-card rounded="md">
      <v-table density="comfortable">
        <thead>
          <tr>
            <th>Период</th>
            <th class="text-right">Входящий оборот</th>
            <th class="text-right">Исходящий оборот</th>
            <th class="text-right">Сальдо</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="4" class="text-center pa-12">
              <v-icon size="64" color="grey-lighten-1" class="mb-4">mdi-chart-line</v-icon>
              <div class="text-h6 text-lightText mb-2">В разработке</div>
              <div class="text-body-2 text-lightText">
                Отчёт формируется на основании транзакций.<br />
                Данные будут доступны в следующей версии.
              </div>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>

    <!-- Информационное сообщение -->
    <v-alert type="info" variant="tonal" class="mt-4">
      <strong>Планируемые данные:</strong> отчёт будет показывать входящие и исходящие обороты
      с группировкой по периодам (день, неделя, месяц) на основании транзакций из раздела
      «Транзакции».
    </v-alert>
  </div>
</template>
