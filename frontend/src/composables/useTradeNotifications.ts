import { watch, ref } from 'vue';
import type { Trade } from '@/api/exchange';

const NOTIFICATION_SOUND_URL = '/sounds/new-trade.wav';
const NOTIFICATION_TITLE = 'Rebit P2P';

let audioInstance: HTMLAudioElement | null = null;

function getAudio(): HTMLAudioElement {
  if (null === audioInstance) {
    audioInstance = new Audio(NOTIFICATION_SOUND_URL);
    audioInstance.volume = 0.5;
  }

  return audioInstance;
}

function playSound(): void {
  const audio = getAudio();
  audio.currentTime = 0;
  audio.play().catch(() => {
    // браузер может блокировать autoplay до взаимодействия пользователя
  });
}

function requestNotificationPermission(): void {
  if ('undefined' === typeof Notification) {
    return;
  }

  if ('default' === Notification.permission) {
    void Notification.requestPermission();
  }
}

function showBrowserNotification(title: string, body: string): void {
  if ('undefined' === typeof Notification || 'granted' !== Notification.permission) {
    return;
  }

  new Notification(title, {
    body,
    icon: '/favicon.ico',
    tag: 'rebit-new-trade',
  });
}

/**
 * Composable для звуковых и браузерных уведомлений при появлении новых сделок.
 * Принимает getter-функцию, возвращающую массив сделок из store.
 */
export function useTradeNotifications(getTrades: () => Trade[]): void {
  const knownTradeIds = ref<Set<number>>(new Set());
  let isInitialized = false;

  requestNotificationPermission();

  watch(
    () => getTrades().map((trade) => trade.id + (true === trade.isNew ? ':new' : '')).join(','),
    () => {
      const currentTrades = getTrades();

      if (!isInitialized) {
        for (const trade of currentTrades) {
          knownTradeIds.value.add(trade.id);
        }
        isInitialized = true;

        return;
      }

      const newTrades = currentTrades.filter(
        (trade) => true === trade.isNew && !knownTradeIds.value.has(trade.id),
      );

      for (const trade of currentTrades) {
        knownTradeIds.value.add(trade.id);
      }

      if (0 < newTrades.length) {
        playSound();

        const trade = newTrades[0];
        const side = 'buy' === trade.side ? 'Покупка' : 'Продажа';
        const body =
          1 === newTrades.length
            ? `${trade.counterpartyName} · ${side} · ${trade.fiatAmount.toFixed(2)} ₽`
            : `Получено ${newTrades.length} новых сделок`;

        showBrowserNotification(NOTIFICATION_TITLE, body);
      }
    },
  );
}
