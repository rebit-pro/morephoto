<script setup lang="ts">
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { useTradesStore } from '@/stores/trades';
import { usePolling } from '@/composables/usePolling';
import type { ChatContentType } from '@/api/exchange';

const props = defineProps<{
  tradeId: number;
  readonly: boolean;
}>();

const trades = useTradesStore();
const messageText = ref('');
const sending = ref(false);
const chatContainer = ref<HTMLElement | null>(null);

const senderLabels: Record<string, string> = {
  user: 'Вы',
  system: 'Система',
  script: 'Автосообщение',
};


function formatTime(iso: string): string {
  return new Date(iso).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
}

function isMediaMessage(contentType: ChatContentType): boolean {
  return 'pic' === contentType || 'pdf' === contentType || 'video' === contentType;
}

async function scrollToBottom(): Promise<void> {
  await nextTick();
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
  }
}

async function sendMessage(): Promise<void> {
  const text = messageText.value.trim();
  if ('' === text || sending.value) return;

  sending.value = true;
  try {
    await trades.sendMessage(props.tradeId, {
      tradeId: props.tradeId,
      message: text,
      contentType: 'str',
      fileName: null,
    });
    messageText.value = '';
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
  },
);

onMounted(async () => {
  await loadChat();
  await scrollToBottom();
  polling.start();
});

onUnmounted(() => {
  polling.stop();
});
</script>

<template>
  <v-card rounded="md">
    <v-card-title class="d-flex align-center">
      <v-icon class="mr-2">mdi-chat-outline</v-icon>
      Чат сделки
    </v-card-title>

    <v-divider />

    <!-- Сообщения -->
    <div ref="chatContainer" class="chat-messages pa-4" style="height: 400px; overflow-y: auto">
      <v-row v-if="trades.chatLoading && 0 === trades.chatMessages.length" justify="center">
        <v-progress-circular indeterminate color="primary" size="24" />
      </v-row>

      <div v-if="0 === trades.chatMessages.length && !trades.chatLoading" class="text-center text-lightText pa-6">
        Сообщений пока нет
      </div>

      <div v-for="msg in trades.chatMessages" :key="msg.id" class="mb-3">
        <div
          class="d-flex"
          :class="'user' === msg.senderType ? 'justify-end' : 'justify-start'"
        >
          <div
            class="chat-bubble pa-3 rounded-lg"
            :class="{
              'bg-primary text-white': 'user' === msg.senderType,
              'bg-grey-lighten-3': 'system' === msg.senderType,
              'bg-blue-lighten-4': 'script' === msg.senderType,
            }"
            style="max-width: 70%"
          >
            <div class="text-caption font-weight-bold mb-1" :class="'user' === msg.senderType ? 'text-white' : ''">
              {{ senderLabels[msg.senderType] ?? msg.senderType }}
            </div>

            <template v-if="isMediaMessage(msg.contentType)">
              <v-chip size="small" variant="tonal" prepend-icon="mdi-attachment">
                {{ msg.fileName ?? msg.contentType }}
              </v-chip>
              <div class="text-body-2 mt-1">{{ msg.message }}</div>
            </template>
            <template v-else>
              <div class="text-body-2" style="white-space: pre-wrap">{{ msg.message }}</div>
            </template>

            <div
              class="text-caption mt-1"
              :class="'user' === msg.senderType ? 'text-white' : 'text-lightText'"
              style="opacity: 0.7"
            >
              {{ formatTime(msg.createdAt) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <v-divider />

    <!-- Баннер "чат закрыт" -->
    <v-alert v-if="readonly" type="info" variant="tonal" class="ma-2" density="compact">
      Сделка завершена / отменена. Чат закрыт.
    </v-alert>

    <!-- Поле ввода -->
    <v-card-actions v-if="!readonly" class="pa-3">
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
        :disabled="'' === messageText.trim()"
        @click="sendMessage"
      />
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
</style>
