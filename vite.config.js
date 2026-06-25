import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: 'assets/dist',
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(__dirname, 'src/main.jsx'),
      output: {
        entryFileNames: 'app.js',
        assetFileNames: 'app.[ext]',
        chunkFileNames: '[name].js',
      },
    },
  },
});
