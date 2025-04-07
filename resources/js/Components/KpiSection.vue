<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <KpiCard
        v-for="(kpi, index) in kpis"
        :key="index"
        :title="kpi.title"
        :value="kpi.value"
        :color="kpi.color"
        :dark-mode="darkMode"
      />
    </div>
  </template>
  
  <script>
  import KpiCard from './KpiCard.vue'
  
  export default {
    components: { KpiCard },
    props: {
      indicators: {
        type: Object,
        required: true
      },
      darkMode: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      kpis() {
        return [
          {
            title: 'Ventas Totales',
            value: this.indicators.total_sales || 0,
            color: 'blue-800'
          },
          {
            title: 'Venta Promedio',
            value: this.indicators.average_sale || 0,
            color: 'green-500'
          },
          {
            title: 'Transacciones Totales',
            value: this.calculateTotalTransactions(),
            color: 'purple-500'
          }
        ]
      }
    },
    methods: {
      calculateTotalTransactions() {
        return (this.indicators.monthly_trend || []).reduce(
          (acc, curr) => acc + parseFloat(curr.total || 0),
          0
        )
      }
    }
  }
  </script>