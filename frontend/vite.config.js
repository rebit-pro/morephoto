// vite.config.js
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  server: {
    host: true,
    port: 5173,
    strictPort: true,
    hmr: {
      clientPort: 5173
    },
    allowedHosts: ['morephoto.loc']
  },
  plugins: [vue()],
})
