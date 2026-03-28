<script setup lang="ts">
import { ref, watch, nextTick, onMounted, onUnmounted, computed } from 'vue';
import { exchangeApi, type ChatContentType, type ChatMessage } from '@/api/exchange';
import { useTradesStore } from '@/stores/trades';
import { usePolling } from '@/composables/usePolling';
import { isMockApiEnabled } from '@/mocks/config';

interface SelectedAttachment {
  file: File;
  fileName: string;
  contentType: ChatContentType;
  previewUrl: string | null;
}

const props = defineProps<{
  tradeId: number;
  readonly: boolean;
}>();

const trades = useTradesStore();
const messageText = ref('');
const sending = ref(false);
const chatContainer = ref<HTMLDivElement | null>(null);
const selectedAttachment = ref<SelectedAttachment | null>(null);

const senderLabels: Record<string, string> = {
  user: 'Вы',
  system: 'Система',
  script: 'Автосообщение'
};

const canSendMessage = computed(() => {
  return '' !== messageText.value.trim() || null !== selectedAttachment.value;
});

const hasSelectedAttachment = computed(() => null !== selectedAttachment.value);

function getSelectedAttachmentName(): string {
  if (null === selectedAttachment.value) {
    return '';
  }

  return selectedAttachment.value['fileName'];
}

function getSelectedAttachmentUrl(): string {
  if (null === selectedAttachment.value || null === selectedAttachment.value['previewUrl']) {
    return '';
  }

  return selectedAttachment.value['previewUrl'];
}

function hasSelectedImageAttachment(): boolean {
  if (null === selectedAttachment.value) {
    return false;
  }

  return 'pic' === selectedAttachment.value['contentType'] && null !== selectedAttachment.value['previewUrl'];
}

function formatTime(iso: string): string {
  return new Date(iso).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
}

function isMediaMessage(contentType: ChatContentType): boolean {
  return 'pic' === contentType || 'pdf' === contentType || 'video' === contentType;
}

function isImageMessage(message: ChatMessage): boolean {
  return 'pic' === message.contentType && 'string' === typeof message.fileUrl && '' !== message.fileUrl;
}

function resolveFileContentType(file: File): ChatContentType {
  if (file.type.startsWith('image/')) {
    return 'pic';
  }

  if ('application/pdf' === file.type) {
    return 'pdf';
  }

  if (file.type.startsWith('video/')) {
    return 'video';
  }

  return 'str';
}

function clearAttachment(): void {
  const previewUrl = selectedAttachment.value?.['previewUrl'] ?? null;

  if (null !== previewUrl) {
    URL.revokeObjectURL(previewUrl);
  }

  selectedAttachment.value = null;
}

async function handleFileSelected(event: Event): Promise<void> {
  const input = event.target as HTMLInputElement;
  const file = input.files?.[0];

  if (undefined === file) {
    return;
  }

  clearAttachment();

  const contentType = resolveFileContentType(file);
  selectedAttachment.value = {
    file,
    fileName: file.name,
    contentType,
    previewUrl: 'pic' === contentType ? URL.createObjectURL(file) : null
  };
  input.value = '';
}

async function scrollToBottom(): Promise<void> {
  await nextTick();
  const container = chatContainer.value;

  if (null !== container) {
    const element = container as HTMLElement;
    element.scrollTop = element.scrollHeight;
  }
}

async function sendMessage(): Promise<void> {
  const text = messageText.value.trim();
  const attachment = selectedAttachment.value;

  if (!canSendMessage.value || sending.value) {
    return;
  }

  sending.value = true;

  try {
    if ('' !== text) {
      await trades.sendMessage(props.tradeId, {
        tradeId: props.tradeId,
        message: text,
        contentType: 'str',
        fileName: null
      });
    }

    if (null !== attachment) {
      const uploadedFile = await exchangeApi.uploadTradeChatFile(props.tradeId, attachment['file']);

      await trades.sendMessage(props.tradeId, {
        tradeId: props.tradeId,
        message: uploadedFile.fileUrl,
        contentType: uploadedFile.contentType,
        fileName: uploadedFile.fileName
      });
    }

    messageText.value = '';
    clearAttachment();
    await scrollToBottom();
  } finally {
    sending.value = false;
  }
}

async function loadChat(): Promise<void> {
  await trades.fetchChatHistory(props.tradeId);
}

const polling = usePolling(loadChat, 5000);

watch(
  () => trades.chatMessages.length,
  () => {
    void scrollToBottom();
  }
);

onMounted(async () => {
  await loadChat();
  await scrollToBottom();
  polling.start();
});

onUnmounted(() => {
  polling.stop();
  clearAttachment();
});
</script>

<template>
  <v-card rounded="md">
    <v-card-title class="d-flex align-center">
      <v-icon class="mr-2">mdi-chat-outline</v-icon>
      Чат сделки
    </v-card-title>

    <v-divider />

    <div ref="chatContainer" class="chat-messages pa-4" style="height: 400px; overflow-y: auto">
      <v-row v-if="trades.chatLoading && 0 === trades.chatMessages.length" justify="center">
        <v-progress-circular indeterminate color="primary" size="24" />
      </v-row>

      <div v-if="0 === trades.chatMessages.length && !trades.chatLoading" class="text-center text-lightText pa-6">Сообщений пока нет</div>

      <div v-for="msg in trades.chatMessages" :key="msg.id" class="mb-3">
        <div class="d-flex" :class="'user' === msg.senderType ? 'justify-end' : 'justify-start'">
          <div
            class="chat-bubble pa-3 rounded-lg"
            :class="{
              'bg-primary text-white': 'user' === msg.senderType,
              'bg-grey-lighten-3': 'system' === msg.senderType,
              'bg-blue-lighten-4': 'script' === msg.senderType
            }"
            style="max-width: 70%"
          >
            <div class="text-caption font-weight-bold mb-1" :class="'user' === msg.senderType ? 'text-white' : ''">
              {{ senderLabels[msg.senderType] ?? msg.senderType }}
            </div>

            <template v-if="isMediaMessage(msg.contentType)">
              <img
                v-if="isImageMessage(msg)"
                :src="msg.fileUrl ?? undefined"
                :alt="msg.fileName ?? 'attachment'"
                class="trade-chat__image mb-2"
              />
              <a
                v-else-if="msg.fileUrl"
                :href="msg.fileUrl"
                :download="msg.fileName ?? undefined"
                target="_blank"
                rel="noopener noreferrer"
                class="trade-chat__attachment-link"
              >
                <v-chip size="small" variant="tonal" prepend-icon="mdi-attachment">
                  {{ msg.fileName ?? msg.contentType }}
                </v-chip>
              </a>
              <v-chip v-else size="small" variant="tonal" prepend-icon="mdi-attachment">
                {{ msg.fileName ?? msg.contentType }}
              </v-chip>
              <div v-if="'' !== msg.message" class="text-body-2 mt-1" style="white-space: pre-wrap">{{ msg.message }}</div>
            </template>
            <template v-else>
              <div class="text-body-2" style="white-space: pre-wrap">{{ msg.message }}</div>
            </template>

            <div class="text-caption mt-1" :class="'user' === msg.senderType ? 'text-white' : 'text-lightText'" style="opacity: 0.7">
              {{ formatTime(msg.createdAt) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <v-divider />

    <v-alert v-if="readonly" type="info" variant="tonal" class="ma-2" density="compact"> Сделка завершена / отменена. Чат закрыт. </v-alert>

    <v-card-actions v-if="!readonly" class="pa-3 flex-column align-stretch ga-3">
      <div class="d-flex flex-wrap align-center ga-3">
        <label class="d-inline-flex">
          <input class="d-none" type="file" accept="image/*,application/pdf,video/*" @change="handleFileSelected" />
          <v-btn size="small" variant="outlined" prepend-icon="mdi-paperclip" tag="span">
            {{ hasSelectedAttachment ? 'Заменить файл' : 'Прикрепить файл' }}
          </v-btn>
        </label>
        <v-chip v-if="hasSelectedAttachment" size="small" color="primary" variant="tonal">
          {{ getSelectedAttachmentName() }}
        </v-chip>
        <v-btn v-if="hasSelectedAttachment" size="small" variant="text" color="error" prepend-icon="mdi-close" @click="clearAttachment">
          Удалить
        </v-btn>
      </div>

      <v-alert v-if="!isMockApiEnabled" type="info" variant="tonal" density="compact">
        Текст и вложение отправляются отдельными сообщениями: сначала комментарий, затем файл, загруженный в Bybit.
      </v-alert>

      <div v-if="hasSelectedImageAttachment()" class="trade-chat__attachment-preview">
        <img :src="getSelectedAttachmentUrl()" :alt="getSelectedAttachmentName()" class="trade-chat__image" />
      </div>

      <div class="d-flex align-center">
        <v-text-field
          v-model="messageText"
          placeholder="Введите сообщение..."
          variant="outlined"
          density="compact"
          hide-details
          :disabled="sending"
          @keydown.enter.exact.prevent="sendMessage"
        />
        <v-btn
          icon="mdi-send"
          color="primary"
          variant="flat"
          size="small"
          class="ml-2"
          :loading="sending"
          :disabled="!canSendMessage"
          @click="sendMessage"
        />
      </div>
    </v-card-actions>
  </v-card>
</template>

<style scoped>
.chat-messages {
  scroll-behavior: smooth;
}

.chat-bubble {
  word-break: break-word;
}

.trade-chat__image {
  display: block;
  max-width: 220px;
  max-height: 220px;
  border-radius: 12px;
  object-fit: cover;
}

.trade-chat__attachment-link {
  text-decoration: none;
}

.trade-chat__attachment-preview {
  display: flex;
}
</style>
