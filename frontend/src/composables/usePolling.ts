import { onUnmounted, ref, type Ref } from 'vue';

interface UsePollingReturn {
  isActive: Ref<boolean>;
  start: () => void;
  stop: () => void;
}

export function usePolling(callback: () => Promise<void> | void, intervalMs = 10000): UsePollingReturn {
  const isActive = ref(false);
  let timer: ReturnType<typeof setInterval> | null = null;

  function start(): void {
    stop();
    isActive.value = true;
    timer = setInterval(() => {
      void callback();
    }, intervalMs);
  }

  function stop(): void {
    if (null !== timer) {
      clearInterval(timer);
      timer = null;
    }
    isActive.value = false;
  }

  onUnmounted(stop);

  return { isActive, start, stop };
}
