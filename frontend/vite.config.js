import { defineConfig, loadEnv } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/

const env = loadEnv("development", process.cwd(), 'VITE');

export default defineConfig({
  plugins: [react()],
  server: {
    host: env.VITE_HOSTNAME || "127.0.0.1",
    port: Number(env.VITE_PORT_FRONT) || 5173
  }
})
