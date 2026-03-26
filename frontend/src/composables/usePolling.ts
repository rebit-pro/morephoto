import { onUnmounted, ref, type Ref } from 'vue';

interface UsePollingReturn {
  isActive: Ref<boolean>;
  start: () => void;
  stop: () => void;
}

export function usePolling(callback: () => Promise<void> | void, intervalMs = 10000): UsePollingReturn {
  const isActive = ref(false);
  let timer: ReturnType<typeof setTimeout> | null = null;
  let inFlight = false;

  async function tick(): Promise<void> {
    if (inFlight) return;
    inFlight = true;
    try {
      await callback();
    } finally {
      inFlight = false;
      if (isActive.value) {
        timer = setTimeout(() => void tick(), intervalMs);
      }
    }
  }

  function start(): void {
    stop();
    isActive.value = true;
    timer = setTimeout(() => void tick(), intervalMs);
  }

  function stop(): void {
    if (null !== timer) {
      clearTimeout(timer);
      timer = null;
    }
    isActive.value = false;
  }

  onUnmounted(stop);

  return { isActive, start, stop };
}
