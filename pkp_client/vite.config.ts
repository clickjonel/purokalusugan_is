import path from 'node:path'
import tailwindcss from '@tailwindcss/vite'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    vue(),
    tailwindcss()
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  server:{
    host: '0.0.0.0',
    port: 9002,
  }
  // build: {
  //   // outDir: path.resolve(__dirname, '../pkp_api/public/pkp/'), // Laravel's public folder
  //   emptyOutDir: true,
  //   target: 'esnext',
  // },
  // root: '.',
  // publicDir: 'public',
  // base: 'https://{base_url_outside_world}/pkp/',
})
