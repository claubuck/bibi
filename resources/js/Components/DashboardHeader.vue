<template>
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
        Panel de Análisis de Ventas
      </h1>
      <button
        @click="toggleDarkMode"
        class="p-2 rounded-full bg-white dark:bg-gray-800 shadow hover:shadow-lg transition-all"
      >
        <span v-if="!darkMode" class="text-gray-600">🌙</span>
        <span v-else class="text-yellow-400">☀️</span>
      </button>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      darkMode: Boolean
    },
    emits: ['toggle-dark-mode'],
    methods: {
      toggleDarkMode() {
        this.$emit('toggle-dark-mode')
      }
    }
  }
  </script>