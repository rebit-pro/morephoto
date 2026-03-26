<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue';
import { useChatScriptsStore } from '@/stores/chatScripts';
import type { ChatScriptStep, ChatScriptPayload } from '@/api/exchange';

const scripts = useChatScriptsStore();

const formDialog = ref(false);
const deleteDialog = ref(false);
const deleteTargetId = ref<number | null>(null);
const editingId = ref<number | null>(null);
const previewDialog = ref(false);
const previewSteps = ref<ChatScriptStep[]>([]);

const form = reactive<{
  name: string;
  isActive: boolean;
  steps: ChatScriptStep[];
}>({
  name: '',
  isActive: true,
  steps: [{ sort: 1, message: '', delaySeconds: 0 }],
});

const placeholders = [
  { tag: '{counterparty}', desc: 'Имя контрагента' },
  { tag: '{amount}', desc: 'Сумма сделки' },
  { tag: '{currency}', desc: 'Код валюты (USDT, BTC)' },
  { tag: '{fiat_amount}', desc: 'Сумма в фиате' },
  { tag: '{fiat_currency}', desc: 'Фиатная валюта (RUB)' },
  { tag: '{trade_id}', desc: 'Номер сделки' },
];

function formatDate(iso: string): string {
  return new Date(iso).toLocaleString('ru-RU');
}

function openCreate(): void {
  editingId.value = null;
  form.name = '';
  form.isActive = true;
  form.steps = [{ sort: 1, message: '', delaySeconds: 0 }];
  formDialog.value = true;
}

function openEdit(id: number): void {
  const script = scripts.scripts.find((s) => s.id === id);
  if (!script) return;

  editingId.value = id;
  form.name = script.name;
  form.isActive = script.isActive;
  form.steps = script.steps.map((s) => ({ ...s }));
  formDialog.value = true;
}

function addStep(): void {
  form.steps.push({
    sort: form.steps.length + 1,
    message: '',
    delaySeconds: 0,
  });
}

function removeStep(index: number): void {
  form.steps.splice(index, 1);
  form.steps.forEach((step, i) => {
    step.sort = i + 1;
  });
}

function moveStep(index: number, direction: -1 | 1): void {
  const target = index + direction;
  if (target < 0 || target >= form.steps.length) return;
  const temp = form.steps[index];
  form.steps[index] = form.steps[target];
  form.steps[target] = temp;
  form.steps.forEach((step, i) => {
    step.sort = i + 1;
  });
}

async function handleSave(): Promise<void> {
  const payload: ChatScriptPayload = {
    name: form.name,
    isActive: form.isActive,
    steps: form.steps.map((s, i) => ({
      sort: i + 1,
      message: s.message,
      delaySeconds: s.delaySeconds,
    })),
  };

  try {
    if (null !== editingId.value) {
      await scripts.updateScript(editingId.value, payload);
    } else {
      await scripts.createScript(payload);
    }
    formDialog.value = false;
  } catch {
    // ошибка обрабатывается в сторе
  }
}

function confirmDelete(id: number): void {
  deleteTargetId.value = id;
  deleteDialog.value = true;
}

async function handleDelete(): Promise<void> {
  if (null === deleteTargetId.value) return;
  try {
    await scripts.deleteScript(deleteTargetId.value);
    deleteDialog.value = false;
    deleteTargetId.value = null;
  } catch {
    // ошибка обрабатывается в сторе
  }
}

function openPreview(steps: ChatScriptStep[]): void {
  previewSteps.value = steps;
  previewDialog.value = true;
}

function renderPreview(msg: string): string {
  return msg
    .replace(/{counterparty}/g, 'Иван')
    .replace(/{amount}/g, '100')
    .replace(/{currency}/g, 'USDT')
    .replace(/{fiat_amount}/g, '9 400')
    .replace(/{fiat_currency}/g, 'RUB')
    .replace(/{trade_id}/g, '12345');
}

onMounted(async () => {
  await scripts.fetchScripts();
});
</script>

<template>
  <div>
    <div class="d-flex align-center justify-space-between mb-6 flex-wrap ga-3">
      <h2 class="text-h4">Скрипты чата</h2>
      <v-btn color="primary" prepend-icon="mdi-plus" @click="openCreate">
        Создать скрипт
      </v-btn>
    </div>

    <v-row v-if="scripts.loading" justify="center" class="mt-8">
      <v-progress-circular indeterminate color="primary" />
    </v-row>

    <v-alert v-if="scripts.error" type="error" variant="tonal" class="mb-4">{{ scripts.error }}</v-alert>

    <v-card v-if="!scripts.loading" rounded="md">
      <v-table density="comfortable" hover>
        <thead>
          <tr>
            <th>Название</th>
            <th class="text-center">Шагов</th>
            <th>Статус</th>
            <th>Дата</th>
            <th class="text-right">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="0 === scripts.scripts.length">
            <td colspan="5" class="text-center text-lightText pa-6">
              Нет скриптов.
              <v-btn variant="text" color="primary" size="small" @click="openCreate">
                Создать первый
              </v-btn>
            </td>
          </tr>
          <tr v-for="script in scripts.scripts" :key="script.id">
            <td class="font-weight-medium">{{ script.name }}</td>
            <td class="text-center">{{ script.steps.length }}</td>
            <td>
              <v-chip size="small" variant="tonal" :color="script.isActive ? 'success' : 'grey'">
                {{ script.isActive ? 'Активен' : 'Неактивен' }}
              </v-chip>
            </td>
            <td class="text-lightText text-body-2">{{ formatDate(script.createdAt) }}</td>
            <td class="text-right">
              <v-btn
                icon="mdi-eye-outline"
                size="small"
                variant="text"
                @click="openPreview(script.steps)"
              />
              <v-btn
                icon="mdi-pencil-outline"
                size="small"
                variant="text"
                @click="openEdit(script.id)"
              />
              <v-btn
                icon="mdi-delete-outline"
                size="small"
                variant="text"
                color="error"
                @click="confirmDelete(script.id)"
              />
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>

    <!-- Диалог создания/редактирования -->
    <v-dialog v-model="formDialog" max-width="700" persistent>
      <v-card>
        <v-card-title>
          {{ null !== editingId ? 'Редактировать скрипт' : 'Создать скрипт' }}
        </v-card-title>
        <v-card-text>
          <v-text-field
            v-model="form.name"
            label="Название скрипта"
            variant="outlined"
            density="compact"
            class="mb-2"
          />
          <v-switch
            v-model="form.isActive"
            label="Активен"
            color="primary"
            density="compact"
            hide-details
            class="mb-4"
          />

          <div class="text-subtitle-2 mb-2">Шаги скрипта</div>

          <div v-for="(step, index) in form.steps" :key="index" class="mb-3 pa-3 rounded border">
            <div class="d-flex align-center justify-space-between mb-2">
              <span class="text-caption font-weight-bold">Шаг {{ index + 1 }}</span>
              <div class="d-flex ga-1">
                <v-btn
                  icon="mdi-arrow-up"
                  size="x-small"
                  variant="text"
                  :disabled="0 === index"
                  @click="moveStep(index, -1)"
                />
                <v-btn
                  icon="mdi-arrow-down"
                  size="x-small"
                  variant="text"
                  :disabled="index === form.steps.length - 1"
                  @click="moveStep(index, 1)"
                />
                <v-btn
                  icon="mdi-close"
                  size="x-small"
                  variant="text"
                  color="error"
                  :disabled="1 === form.steps.length"
                  @click="removeStep(index)"
                />
              </div>
            </div>
            <v-textarea
              v-model="step.message"
              label="Текст сообщения"
              variant="outlined"
              density="compact"
              rows="2"
              class="mb-2"
            />
            <v-text-field
              v-model.number="step.delaySeconds"
              label="Задержка (секунд)"
              variant="outlined"
              density="compact"
              type="number"
              min="0"
              max="300"
              hide-details
            />
          </div>

          <v-btn variant="outlined" size="small" prepend-icon="mdi-plus" @click="addStep" class="mb-4">
            Добавить шаг
          </v-btn>

          <!-- Подсказка по плейсхолдерам -->
          <v-expansion-panels variant="accordion" class="mt-2">
            <v-expansion-panel title="Доступные плейсхолдеры">
              <v-expansion-panel-text>
                <v-list density="compact" class="pa-0">
                  <v-list-item
                    v-for="p in placeholders"
                    :key="p.tag"
                    class="px-0"
                  >
                    <template #prepend>
                      <code class="text-primary mr-2">{{ p.tag }}</code>
                    </template>
                    <v-list-item-title class="text-body-2">{{ p.desc }}</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-expansion-panel-text>
            </v-expansion-panel>
          </v-expansion-panels>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="formDialog = false">Отмена</v-btn>
          <v-btn
            color="primary"
            :loading="scripts.actionLoading"
            :disabled="'' === form.name.trim() || form.steps.some((s) => '' === s.message.trim())"
            @click="handleSave"
          >
            Сохранить
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Диалог предпросмотра -->
    <v-dialog v-model="previewDialog" max-width="500">
      <v-card>
        <v-card-title>Предпросмотр скрипта</v-card-title>
        <v-card-text>
          <div class="pa-3 bg-grey-lighten-4 rounded" style="max-height: 400px; overflow-y: auto">
            <div v-for="(step, index) in previewSteps" :key="index" class="mb-3">
              <div class="d-flex justify-end">
                <div class="pa-3 rounded-lg bg-primary text-white" style="max-width: 80%">
                  <div class="text-body-2" style="white-space: pre-wrap">{{ renderPreview(step.message) }}</div>
                  <div class="text-caption mt-1" style="opacity: 0.7">
                    {{ 0 < step.delaySeconds ? `+${step.delaySeconds} сек` : 'Сразу' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="previewDialog = false">Закрыть</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Диалог удаления -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card>
        <v-card-title>Удалить скрипт?</v-card-title>
        <v-card-text>
          Скрипт будет удалён и отвязан от всех объявлений. Уже запущенные сделки не затрагиваются.
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="deleteDialog = false">Отмена</v-btn>
          <v-btn color="error" :loading="scripts.actionLoading" @click="handleDelete">Удалить</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<style scoped>
.border {
  border: 1px solid rgba(0, 0, 0, 0.12);
}
</style>
