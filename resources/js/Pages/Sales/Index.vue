<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-8 flex">
    <!-- Sidebar de Filtros -->
    <FilterSidebar
      :dark-mode="darkMode"
      :filter-options="filterOptions"
      :filters="filters"
    />

    <div class="w-3/4 pl-8">
      <!-- Header con toggle de modo oscuro -->
      <DashboardHeader
        :dark-mode="darkMode"
        @toggle-dark-mode="toggleDarkMode"
      />

      <!-- Tarjetas de KPIs -->
      <KpiSection :indicators="indicators" :dark-mode="darkMode" />

      <!-- Gráficos -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfico de Tendencia Mensual -->
        <div
          class="rounded-xl p-6 transition-all duration-300"
          :class="
            darkMode
              ? 'bg-gray-800 border border-gray-700'
              : 'bg-white border border-gray-100'
          "
        >
          <h3
            class="text-lg font-semibold mb-4"
            :class="darkMode ? 'text-gray-200' : 'text-gray-900'"
          >
            Tendencia Mensual de Ventas
          </h3>
          <canvas ref="trendChart"></canvas>
        </div>

        <!-- Productos Más Vendidos -->
        <div
          class="rounded-xl p-6 transition-all duration-300"
          :class="
            darkMode
              ? 'bg-gray-800 border border-gray-700'
              : 'bg-white border border-gray-100'
          "
        >
          <h3
            class="text-lg font-semibold mb-4"
            :class="darkMode ? 'text-gray-200' : 'text-gray-900'"
          >
            Productos Más Vendidos
          </h3>
          <div class="space-y-4">
            <div
              v-for="(product, index) in indicators.product_performance"
              :key="product.product_name"
              class="flex items-center justify-between p-3 rounded-lg"
              :class="darkMode ? 'bg-gray-700' : 'bg-gray-50'"
            >
              <div class="flex items-center w-full">
                <span
                  class="text-sm font-medium mr-4"
                  :class="darkMode ? 'text-gray-300' : 'text-gray-600'"
                >
                  #{{ index + 1 }}
                </span>
                <span
                  class="text-sm flex-1"
                  :class="darkMode ? 'text-gray-200' : 'text-gray-700'"
                >
                  {{ product.product_name }}
                </span>
                <div class="flex items-center w-1/3">
                  <div
                    class="h-2 rounded-full mr-2 transition-all duration-500"
                    :class="darkMode ? 'bg-gray-600' : 'bg-gray-200'"
                    :style="{
                      width: `${(product.total / maxProductValue) * 100}%`,
                      backgroundColor: getRankColor(index),
                    }"
                  ></div>
                  <span
                    class="text-sm font-medium"
                    :class="darkMode ? 'text-gray-300' : 'text-gray-600'"
                  >
                    {{ formatNumber(product.total) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Gráfico de Categorías -->
        <div
          class="rounded-xl p-6 transition-all duration-300"
          :class="
            darkMode
              ? 'bg-gray-800 border border-gray-700'
              : 'bg-white border border-gray-100'
          "
        >
          <h3
            class="text-lg font-semibold mb-4"
            :class="darkMode ? 'text-gray-200' : 'text-gray-900'"
          >
            Ventas por Categoría
          </h3>
          <canvas ref="categoryChart"></canvas>
        </div>

        <!-- Gráfico de Métodos de Pago -->
        <div
          class="rounded-xl p-6 transition-all duration-300"
          :class="
            darkMode
              ? 'bg-gray-800 border border-gray-700'
              : 'bg-white border border-gray-100'
          "
        >
          <h3
            class="text-lg font-semibold mb-4"
            :class="darkMode ? 'text-gray-200' : 'text-gray-900'"
          >
            Distribución de Pagos
          </h3>
          <canvas ref="paymentChart"></canvas>
        </div>

        <!-- Gráfico Regional -->
        <RegionalDistribution :indicators="indicators" :dark-mode="darkMode" />
      </div>

      <!-- Gráficos Secundarios -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
        <!-- Gráfico de Clientes -->
        <div
          class="rounded-xl p-6 transition-all duration-300"
          :class="
            darkMode
              ? 'bg-gray-800 border border-gray-700'
              : 'bg-white border border-gray-100'
          "
        >
          <h3
            class="text-lg font-semibold mb-4"
            :class="darkMode ? 'text-gray-200' : 'text-gray-900'"
          >
            Tipos de Clientes
          </h3>
          <canvas ref="customerChart"></canvas>
        </div>

        <!-- Top Vendedores -->
        <div
          class="rounded-xl p-6 transition-all duration-300 lg:col-span-2"
          :class="
            darkMode
              ? 'bg-gray-800 border border-gray-700'
              : 'bg-white border border-gray-100'
          "
        >
          <h3
            class="text-lg font-semibold mb-4"
            :class="darkMode ? 'text-gray-200' : 'text-gray-900'"
          >
            Top Vendedores
          </h3>
          <div class="space-y-4">
            <div
              v-for="(seller, index) in topSellers"
              :key="seller.sales_person"
              class="flex items-center justify-between p-3 rounded-lg"
              :class="darkMode ? 'bg-gray-700' : 'bg-gray-50'"
            >
              <div class="flex items-center w-full">
                <span
                  class="text-sm font-medium mr-4"
                  :class="darkMode ? 'text-gray-300' : 'text-gray-600'"
                >
                  #{{ index + 1 }}
                </span>
                <span
                  class="text-sm flex-1"
                  :class="darkMode ? 'text-gray-200' : 'text-gray-700'"
                >
                  {{ seller.sales_person }}
                </span>
                <div class="flex items-center w-1/3">
                  <div
                    class="h-2 rounded-full mr-2 transition-all duration-500"
                    :class="darkMode ? 'bg-gray-600' : 'bg-gray-200'"
                    :style="{
                      width: `${(seller.total / maxSalesValue) * 100}%`,
                      backgroundColor: getRankColor(index),
                    }"
                  ></div>
                  <span
                    class="text-sm font-medium"
                    :class="darkMode ? 'text-gray-300' : 'text-gray-600'"
                  >
                    ${{ formatNumber(seller.total) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Tercera Fila -->
      <div class="grid grid-cols-1 mt-6 lg:grid-cols-2 gap-6">
        <!-- Nuevo: Tendencia Diaria (30 días) -->
        <div
          class="rounded-xl p-6 transition-all duration-300 lg:col-span-2"
          :class="
            darkMode
              ? 'bg-gray-800 border border-gray-700'
              : 'bg-white border border-gray-100'
          "
        >
          <h3
            class="text-lg font-semibold mb-4"
            :class="darkMode ? 'text-gray-200' : 'text-gray-900'"
          >
            Tendencia Diaria (Últimos 30 días)
          </h3>
          <canvas ref="dailyChart"></canvas>
        </div>

        <!-- Nuevo: Comparación Mensual Anual -->
        <div
          class="rounded-xl p-6 transition-all duration-300 lg:col-span-2"
          :class="
            darkMode
              ? 'bg-gray-800 border border-gray-700'
              : 'bg-white border border-gray-100'
          "
        >
          <h3
            class="text-lg font-semibold mb-4"
            :class="darkMode ? 'text-gray-200' : 'text-gray-900'"
          >
            Comparación Mensual
          </h3>
          <canvas ref="comparisonChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from "chart.js/auto";
import DashboardHeader from "@/Components/DashboardHeader.vue";
import ChartDataLabels from "chartjs-plugin-datalabels";
import FilterSidebar from "@/Components/FilterSidebar.vue";
import KpiSection from "@/Components/KpiSection.vue";
import RegionalDistribution from "@/Components/RegionalDistribution.vue";
import L from "leaflet";
import "leaflet.heat";
import "leaflet/dist/leaflet.css";
Chart.register(ChartDataLabels);

export default {
  components: {
    DashboardHeader,
    FilterSidebar,
    KpiSection,
    RegionalDistribution,
  },
  props: {
    indicators: {
      type: Object,
      default: () => ({
        total_sales: 0,
        average_sale: 0,
        monthly_trend: [],
        product_performance: [],
        regional_sales: [],
        category_distribution: [],
        payment_methods: [],
        customer_types: [],
        sales_performance: [],
        daily_trend: [],
        monthly_comparison: [],
      }),
    },
    filterOptions: Object,
    filters: Object,
  },

  data() {
    return {
      charts: {
        trend: null,
        product: null,
        category: null,
        payment: null,
        regional: null,
        customer: null,
        performance: null,
        daily: null,
        comparison: null,
        map: null,
      },
      darkMode: false,
    };
  },

  mounted() {
    this.initCharts();
    this.initRegionalMap(); // 👈 llamada nueva
    if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
      this.darkMode = true;
    }
  },

  methods: {
    getRankColor(index) {
      const colors = ["#3B82F6", "#10B981", "#F59E0B", "#EF4444", "#8B5CF6"];
      return colors[index] || colors[0];
    },

    toggleDarkMode() {
      this.darkMode = !this.darkMode;
    },

    initCharts() {
      // Destruye gráficos existentes
      Object.values(this.charts).forEach((chart) => {
        if (chart) {
          chart.destroy();
        }
      });

      const textColor = this.darkMode ? "#F3F4F6" : "#111827";
      const gridColor = this.darkMode ? "#374151" : "#E5E7EB";

      const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            labels: {
              color: textColor,
              font: {
                family: "'Inter', sans-serif",
              },
            },
          },
        },
        scales: {
          x: {
            grid: { color: gridColor },
            ticks: { color: textColor },
          },
          y: {
            grid: { color: gridColor },
            ticks: { color: textColor },
          },
        },
      };

      // Gráfico de Tendencia Mensual
      if (this.$refs.trendChart) {
        this.charts.trend = new Chart(this.$refs.trendChart, {
          type: "line",
          data: {
            labels: this.indicators.monthly_trend.map((item) => {
              // Extraer mes y año del string period
              const [mes, anio] = item.period.split(" ");

              // Crear fecha usando el formato correcto
              const fecha = new Date(`${mes} 1, ${anio}`);

              // Validar fecha
              if (isNaN(fecha)) {
                console.error("Fecha inválida:", item.period);
                return item.period; // Mostrar el string original como fallback
              }

              return fecha.toLocaleDateString("es-ES", {
                month: "short",
                year: "2-digit",
              });
            }),
            datasets: [
              {
                label: "Ventas",
                data: this.indicators.monthly_trend.map((item) => item.total),
                borderColor: "#3B82F6",
                tension: 0.4,
              },
            ],
          },
          options: {
            plugins: {
              datalabels: false, // Desactiva datalabels para este gráfico
            },
          },
        });
      }

      // Ventas por Categoría (Polar Area)
      this.charts.category = new Chart(this.$refs.categoryChart, {
        type: "polarArea",
        data: {
          labels: this.indicators.category_distribution.map(
            (item) => item.product_category
          ),
          datasets: [
            {
              data: this.indicators.category_distribution.map(
                (item) => parseFloat(item.total) // Convertir a número
              ),
              backgroundColor: [
                "#3B82F6",
                "#10B981",
                "#F59E0B",
                "#EF4444",
                "#8B5CF6",
              ],
            },
          ],
        },
        options: {
          plugins: {
            datalabels: {
              formatter: (value, context) => {
                // Convertir los valores a números antes de calcular el total
                const total = context.dataset.data.reduce(
                  (acc, curr) => acc + parseFloat(curr || 0),
                  0
                );
                const percentage = ((value / total) * 100).toFixed(2);
                return `${percentage}%`;
              },
              color: "#fff",
              font: {
                weight: "bold",
              },
            },
            tooltip: {
              callbacks: {
                label: (tooltipItem) => {
                  // Convertir los valores a números antes de calcular el total
                  const total = tooltipItem.dataset.data.reduce(
                    (acc, curr) => acc + parseFloat(curr || 0),
                    0
                  );
                  const value = parseFloat(tooltipItem.raw || 0);
                  const percentage = ((value / total) * 100).toFixed(2);
                  return `${tooltipItem.label}: ${value} (${percentage}%)`;
                },
              },
            },
          },
        },
      });

      // Métodos de Pago (Pie)
      this.charts.payment = new Chart(this.$refs.paymentChart, {
        type: "pie",
        data: {
          labels: this.indicators.payment_methods.map(
            (item) => item.payment_method
          ),
          datasets: [
            {
              data: this.indicators.payment_methods.map(
                (item) => parseFloat(item.total) // Convertir a número
              ),
              backgroundColor: ["#3B82F6", "#10B981", "#F59E0B", "#EF4444"],
            },
          ],
        },
        options: {
          plugins: {
            datalabels: {
              formatter: (value, context) => {
                // Convertir los valores a números antes de calcular el total
                const total = context.dataset.data.reduce(
                  (acc, curr) => acc + parseFloat(curr || 0),
                  0
                );
                const percentage = ((value / total) * 100).toFixed(2);
                return `${percentage}%`;
              },
              color: "#fff",
              font: {
                weight: "bold",
              },
            },
            tooltip: {
              callbacks: {
                label: (tooltipItem) => {
                  // Convertir los valores a números antes de calcular el total
                  const total = tooltipItem.dataset.data.reduce(
                    (acc, curr) => acc + parseFloat(curr || 0),
                    0
                  );
                  const value = parseFloat(tooltipItem.raw || 0);
                  const percentage = ((value / total) * 100).toFixed(2);
                  return `${tooltipItem.label}: ${value} (${percentage}%)`;
                },
              },
            },
          },
        },
      });

      // Tipos de Cliente (Doughnut)
      this.charts.customer = new Chart(this.$refs.customerChart, {
        type: "doughnut",
        data: {
          labels: this.indicators.customer_types.map(
            (item) => item.customer_type
          ),
          datasets: [
            {
              data: this.indicators.customer_types.map((item) => item.count),
              backgroundColor: ["#3B82F6", "#10B981"],
            },
          ],
        },
        options: {
          plugins: {
            datalabels: false, // Desactiva datalabels para este gráfico
          },
        },
      });

      // Tendencia Diaria (Line)
      this.charts.daily = new Chart(this.$refs.dailyChart, {
        type: "line",
        data: {
          labels: this.indicators.daily_trend.map((item) =>
            new Date(item.date).toLocaleDateString("es-ES", {
              day: "2-digit",
              month: "short",
            })
          ),
          datasets: [
            {
              label: "Ventas Diarias",
              data: this.indicators.daily_trend.map((item) => item.total),
              borderColor: "#10B981",
              tension: 0.4,
              fill: true,
              backgroundColor: "rgba(16, 185, 129, 0.1)",
            },
          ],
        },
        options: {
          plugins: {
            datalabels: false, // Desactiva datalabels para este gráfico
          },
        },
      });

      // Comparación Mensual (Multi Axis)
      this.charts.comparison = new Chart(this.$refs.comparisonChart, {
        type: "line",
        data: {
          labels: this.indicators.monthly_comparison.map(
            (item) =>
              `${new Date(item.year, item.month - 1).toLocaleString("es-ES", {
                month: "short",
              })} ${item.year}`
          ),
          datasets: [
            {
              label: "Ventas",
              data: this.indicators.monthly_comparison.map(
                (item) => item.total
              ),
              borderColor: "#3B82F6",
              yAxisID: "y",
              tension: 0.4,
            },
          ],
        },
        options: {
          scales: {
            y: {
              type: "linear",
              display: true,
              position: "left",
            },
          },
          plugins: {
            datalabels: false, // Desactiva datalabels para este gráfico
          },
        },
      });
    },

    formatNumber(number) {
      return new Intl.NumberFormat("es-ES", {
        style: "decimal", // Asegura que se use el formato decimal
        minimumFractionDigits: 0, // No mostrar decimales
        maximumFractionDigits: 0, // No mostrar decimales
      }).format(Number(number) || 0); // Asegúrate de que el valor sea un número
    },
  },

  computed: {
    maxProductValue() {
      if (!this.indicators.product_performance?.length) return 1;
      return Math.max(
        ...this.indicators.product_performance.map((p) => p.total)
      );
    },

    topSellers() {
      if (!this.indicators.sales_performance) return [];
      // Ordenar y limitar a 5
      return [...this.indicators.sales_performance]
        .sort((a, b) => b.total - a.total)
        .slice(0, 5);
    },

    maxSalesValue() {
      if (!this.topSellers.length) return 1;
      return this.topSellers[0].total; // Tomar el máximo del top 5
    },

  },

  watch: {
    indicators: {
      handler() {
        this.$nextTick(() => this.initCharts());
      },
      deep: true,
    },
  },
};
</script>

<style>
@import "@vuepic/vue-datepicker/dist/main.css";
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap");

body {
  font-family: "Inter", sans-serif;
}

.dark .chartjs-grid line {
  stroke: #374151;
}

.dark .chartjs-tooltip {
  background: #1f2937 !important;
  border: 1px solid #374151 !important;
  color: #f3f4f6 !important;
}

select::-webkit-scrollbar {
  width: 8px;
}

select::-webkit-scrollbar-track {
  background-color: #f3f4f6;
  /* bg-gray-100 */
}

select.dark::-webkit-scrollbar-track {
  background-color: #1f2937;
  /* dark:bg-gray-800 */
}

select::-webkit-scrollbar-thumb {
  background-color: #d1d5db;
  /* bg-gray-300 */
  border-radius: 9999px;
  /* rounded-full */
}

select.dark::-webkit-scrollbar-thumb {
  background-color: #4b5563;
  /* dark:bg-gray-600 */
}
</style>