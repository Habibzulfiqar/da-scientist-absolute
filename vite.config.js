import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0',
    cors: true,
    strictPort: true,
    port: 5173,
    hmr: {
      protocol: 'ws',
    }
  },
  build: {
    outDir: 'assets/dist',
    assetsDir: '',
    rollupOptions: {
      input: 'src/app.jsx',
      output: {
        entryFileNames: 'app.js',
        assetFileNames: 'app.[ext]'
      }
    }
  }
});
