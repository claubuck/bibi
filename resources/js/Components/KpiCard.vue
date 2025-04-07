<template>
    <div
      class="p-6 rounded-xl shadow-lg backdrop-blur-sm transition-all duration-300 border-l-4"
      :class="[darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100', borderColor]"
    >
      <h3 :class="[darkMode ? 'text-gray-400' : 'text-gray-500', 'text-sm font-medium mb-2']">
        {{ title }}
      </h3>
      <p :class="[darkMode ? 'text-gray-100' : 'text-gray-900', 'text-3xl font-semibold']">
        {{ formattedValue }}
      </p>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      title: String,
      value: [Number, String],
      color: {
        type: String,
        default: 'blue-500'
      },
      darkMode: Boolean,
      isCurrency: {
        type: Boolean,
        default: true
      }
    },
    computed: {
      borderColor() {
        // Mapeo de clases completas de Tailwind
        const colors = {
          'blue-500': 'border-blue-500',
          'green-500': 'border-green-500',
          'purple-500': 'border-purple-500',
          // Agregar más colores si es necesario
        };
        return colors[this.color] || 'border-blue-500';
      },
      formattedValue() {
        return this.isCurrency 
          ? `$${this.formatNumber(this.value)}`
          : this.formatNumber(this.value);
      }
    },
    methods: {
      formatNumber(number) {
        return new Intl.NumberFormat('es-ES', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        }).format(number || 0);
      }
    }
  };
  </script>