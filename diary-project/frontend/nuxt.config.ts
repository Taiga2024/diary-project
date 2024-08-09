// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-04-03',
  devtools: { enabled: true },
  runtimeConfig: {
    public: {
      apiUrl: '',
    }
  },
  vite: {
    server: {
      watch: {
        usePolling: true,
      },
    },
  },
  css: ['ress'],
})
