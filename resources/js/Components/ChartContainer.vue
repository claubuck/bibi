<template>
    <div
      class="rounded-xl p-6 transition-all duration-300 relative"
      :class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-100'"
    >
      <h3 :class="darkMode ? 'text-gray-200' : 'text-gray-900'" class="text-lg font-semibold mb-4">
        {{ title }}
      </h3>
      <div class="relative h-64"> <!-- Contenedor con altura fija -->
        <canvas ref="chartCanvas"></canvas>
      </div>
    </div>
  </template>
  
  <script>
  import Chart from 'chart.js/auto';
  
  export default {
    props: {
      title: String,
      chartData: Object,
      chartType: String,
      chartOptions: Object,
      darkMode: Boolean
    },
    data() {
      return {
        chartInstance: null
      };
    },
    watch: {
      chartData: {
        deep: true,
        handler() {
          this.updateChart();
        }
      },
      darkMode() {
        this.updateChartTheme();
      }
    },
    mounted() {
      this.initChart();
    },
    beforeUnmount() {
      this.destroyChart();
    },
    methods: {
      initChart() {
        this.destroyChart();
        this.chartInstance = new Chart(this.$refs.chartCanvas, {
          type: this.chartType,
          data: this.chartData,
          options: this.getChartOptions()
        });
      },
      updateChart() {
        if (this.chartInstance) {
          this.chartInstance.data = this.chartData;
          this.chartInstance.options = this.getChartOptions(); // Actualizar opciones también
          this.chartInstance.update();
        }
      },
      updateChartTheme() {
        this.updateChart(); // Reutilizar la misma lógica de actualización
      },
      getChartOptions() {
        const textColor = this.darkMode ? '#F3F4F6' : '#111827';
        const gridColor = this.darkMode ? '#374151' : '#E5E7EB';
  
        return {
          responsive: true,
          maintainAspectRatio: false, // Importante mantener en false
          aspectRatio: 2, // Ratio base
          plugins: {
            legend: {
              labels: { 
                color: textColor,
                boxWidth: 12,
                padding: 20
              }
            }
          },
          scales: {
            x: { 
              grid: { 
                color: gridColor,
                drawTicks: false
              }, 
              ticks: { 
                color: textColor,
                maxRotation: 0,
                autoSkipPadding: 20
              } 
            },
            y: { 
              grid: { 
                color: gridColor,
                drawTicks: false
              }, 
              ticks: { 
                color: textColor,
                padding: 10
              } 
            }
          },
          ...this.chartOptions
        };
      },
      destroyChart() {
        if (this.chartInstance) {
          this.chartInstance.destroy();
          this.chartInstance = null;
        }
      }
    }
  };
  </script>