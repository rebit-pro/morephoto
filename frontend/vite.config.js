// frontend/vite.config.js
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue';
export default defineConfig({
  server: {
    host: true, // Слушать все интерфейсы
    port: 5173,
    strictPort: true,
    hmr: {
      clientPort: 5173 // Важно для Docker
    }
  },
  plugins: [vue()],
})

