<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-8 flex">
    <!-- Sidebar de Filtros -->
    <div class="w-1/4 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md h-full">
      <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
        Filtros
      </h2>

      <!-- Filtros Principales -->
      <div>
        <label class="block text-sm font-medium dark:text-gray-300">Rango de Fechas</label>
        <VueDatePicker v-model="dateRange" range :enable-time-picker="false" :dark="darkMode"
          :class="darkMode ? 'dp-custom-dark' : 'dp-custom-light'" @update:model-value="applyFilters" />
      </div>
      <MultiSelect v-model="selectedRegions" :options="filterOptions.regions" label="Regiones" :dark="darkMode"
        @update:model-value="applyFilters" />
      <MultiSelect v-model="selectedCategories" :options="filterOptions.categories" label="Categorías" :dark="darkMode"
        @update:model-value="applyFilters" />

      <!-- Filtros Secundarios -->
      <div class="mt-4">
        <SelectFilter v-model="selectedCustomerTypes" :options="filterOptions.customer_types" label="Tipo de Cliente"
          :dark="darkMode" @update:model-value="applyFilters" />
        <SelectFilter v-model="selectedPaymentMethods" :options="filterOptions.payment_methods" label="Método de Pago"
          :dark="darkMode" @update:model-value="applyFilters" />
        <SelectFilter v-model="selectedSalesPersons" :options="sortedSalesPersons" label="Vendedor" :dark="darkMode"
          @update:model-value="applyFilters" />
      </div>

      <div class="mt-4">
        <button @click="resetFilters"
          class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors w-full">
          Limpiar Filtros
        </button>
      </div>
    </div>

    <div class="w-3/4 pl-8">
      <!-- Header con toggle de modo oscuro -->
      <DashboardHeader :dark-mode="darkMode" @toggle-dark-mode="toggleDarkMode" />

      <!-- Tarjetas de KPIs -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="p-6 rounded-xl shadow-lg backdrop-blur-sm transition-all duration-300 border-l-4" :class="[
          darkMode
            ? 'bg-gray-800 border-gray-700'
            : 'bg-white border-gray-100',
          'border-blue-500',
        ]">
          <h3 class="text-sm font-medium mb-2" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">
            Ventas Totales
          </h3>
          <p class="text-3xl font-semibold" :class="darkMode ? 'text-gray-100' : 'text-gray-900'">
            ${{ formatNumber(indicators.total_sales || 0) }}
          </p>
        </div>

        <div class="p-6 rounded-xl shadow-lg backdrop-blur-sm transition-all duration-300 border-l-4" :class="[
          darkMode
            ? 'bg-gray-800 border-gray-700'
            : 'bg-white border-gray-100',
          'border-green-500',
        ]">
          <h3 class="text-sm font-medium mb-2" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">
            Venta Promedio
          </h3>
          <p class="text-3xl font-semibold" :class="darkMode ? 'text-gray-100' : 'text-gray-900'">
            ${{ formatNumber(indicators.average_sale || 0) }}
          </p>
        </div>

        <div class="p-6 rounded-xl shadow-lg backdrop-blur-sm transition-all duration-300 border-l-4" :class="[
          darkMode
            ? 'bg-gray-800 border-gray-700'
            : 'bg-white border-gray-100',
          'border-purple-500',
        ]">
          <h3 class="text-sm font-medium mb-2" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">
            Transacciones Totales
          </h3>
          <p class="text-3xl font-semibold" :class="darkMode ? 'text-gray-100' : 'text-gray-900'">
            {{
              formatNumber(
                (indicators.monthly_trend || []).reduce(
                  (acc, curr) => acc + parseFloat(curr.total || 0),
                  0
                )
              )
            }}
          </p>
        </div>
      </div>

      <!-- Gráficos -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfico de Tendencia Mensual -->
        <div class="rounded-xl p-6 transition-all duration-300" :class="darkMode
          ? 'bg-gray-800 border border-gray-700'
          : 'bg-white border border-gray-100'
          ">
          <h3 class="text-lg font-semibold mb-4" :class="darkMode ? 'text-gray-200' : 'text-gray-900'">
            Tendencia Mensual de Ventas
          </h3>
          <canvas ref="trendChart"></canvas>
        </div>

        <!-- Productos Más Vendidos -->
        <div class="rounded-xl p-6 transition-all duration-300" :class="darkMode
          ? 'bg-gray-800 border border-gray-700'
          : 'bg-white border border-gray-100'
          ">
          <h3 class="text-lg font-semibold mb-4" :class="darkMode ? 'text-gray-200' : 'text-gray-900'">
            Productos Más Vendidos
          </h3>
          <div class="space-y-4">
            <div v-for="(product, index) in indicators.product_performance" :key="product.product_name"
              class="flex items-center justify-between p-3 rounded-lg" :class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
              <div class="flex items-center w-full">
                <span class="text-sm font-medium mr-4" :class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                  #{{ index + 1 }}
                </span>
                <span class="text-sm flex-1" :class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                  {{ product.product_name }}
                </span>
                <div class="flex items-center w-1/3">
                  <div class="h-2 rounded-full mr-2 transition-all duration-500"
                    :class="darkMode ? 'bg-gray-600' : 'bg-gray-200'" :style="{
                      width: `${(product.total / maxProductValue) * 100}%`,
                      backgroundColor: getRankColor(index),
                    }"></div>
                  <span class="text-sm font-medium" :class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                    {{ formatNumber(product.total) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Gráfico de Categorías -->
        <div class="rounded-xl p-6 transition-all duration-300" :class="darkMode
          ? 'bg-gray-800 border border-gray-700'
          : 'bg-white border border-gray-100'
          ">
          <h3 class="text-lg font-semibold mb-4" :class="darkMode ? 'text-gray-200' : 'text-gray-900'">
            Ventas por Categoría
          </h3>
          <canvas ref="categoryChart"></canvas>
        </div>

        <!-- Gráfico de Métodos de Pago -->
        <div class="rounded-xl p-6 transition-all duration-300" :class="darkMode
          ? 'bg-gray-800 border border-gray-700'
          : 'bg-white border border-gray-100'
          ">
          <h3 class="text-lg font-semibold mb-4" :class="darkMode ? 'text-gray-200' : 'text-gray-900'">
            Distribución de Pagos
          </h3>
          <canvas ref="paymentChart"></canvas>
        </div>

        <!-- Gráfico Regional -->
        <div class="rounded-xl p-6 transition-all duration-300 lg:col-span-2" :class="darkMode
          ? 'bg-gray-800 border border-gray-700'
          : 'bg-white border border-gray-100'
          ">
          <h3 class="text-lg font-semibold mb-4" :class="darkMode ? 'text-gray-200' : 'text-gray-900'">
            Distribución Regional
          </h3>
          <canvas ref="regionalChart"></canvas>
        </div>
      </div>

      <!-- Gráficos Secundarios -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
        <!-- Gráfico de Clientes -->
        <div class="rounded-xl p-6 transition-all duration-300" :class="darkMode
          ? 'bg-gray-800 border border-gray-700'
          : 'bg-white border border-gray-100'
          ">
          <h3 class="text-lg font-semibold mb-4" :class="darkMode ? 'text-gray-200' : 'text-gray-900'">
            Tipos de Clientes
          </h3>
          <canvas ref="customerChart"></canvas>
        </div>

        <!-- Top Vendedores -->
        <div class="rounded-xl p-6 transition-all duration-300 lg:col-span-2" :class="darkMode
          ? 'bg-gray-800 border border-gray-700'
          : 'bg-white border border-gray-100'
          ">
          <h3 class="text-lg font-semibold mb-4" :class="darkMode ? 'text-gray-200' : 'text-gray-900'">
            Top Vendedores
          </h3>
          <div class="space-y-4">
            <div v-for="(seller, index) in topSellers" :key="seller.sales_person"
              class="flex items-center justify-between p-3 rounded-lg" :class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
              <div class="flex items-center w-full">
                <span class="text-sm font-medium mr-4" :class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                  #{{ index + 1 }}
                </span>
                <span class="text-sm flex-1" :class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                  {{ seller.sales_person }}
                </span>
                <div class="flex items-center w-1/3">
                  <div class="h-2 rounded-full mr-2 transition-all duration-500"
                    :class="darkMode ? 'bg-gray-600' : 'bg-gray-200'" :style="{
                      width: `${(seller.total / maxSalesValue) * 100}%`,
                      backgroundColor: getRankColor(index),
                    }"></div>
                  <span class="text-sm font-medium" :class="darkMode ? 'text-gray-300' : 'text-gray-600'">
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
        <div class="rounded-xl p-6 transition-all duration-300 lg:col-span-2" :class="darkMode
          ? 'bg-gray-800 border border-gray-700'
          : 'bg-white border border-gray-100'
          ">
          <h3 class="text-lg font-semibold mb-4">
            Tendencia Diaria (Últimos 30 días)
          </h3>
          <canvas ref="dailyChart"></canvas>
        </div>

        <!-- Nuevo: Comparación Mensual Anual -->
        <div class="rounded-xl p-6 transition-all duration-300 lg:col-span-2" :class="darkMode
          ? 'bg-gray-800 border border-gray-700'
          : 'bg-white border border-gray-100'
          ">
          <h3 class="text-lg font-semibold mb-4">Comparación Mensual</h3>
          <canvas ref="comparisonChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from "chart.js/auto";
import VueDatePicker from "@vuepic/vue-datepicker";
import MultiSelect from "@/Components/MultiSelect.vue";
import SelectFilter from "@/Components/SelectFilter.vue";
import DashboardHeader from "@/Components/DashboardHeader.vue";
import ChartDataLabels from "chartjs-plugin-datalabels";
Chart.register(ChartDataLabels);

export default {
  components: { VueDatePicker, MultiSelect, SelectFilter, DashboardHeader },
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
      },
      dateRange: this.filters.date_range || null,
      selectedRegions: this.filters.regions || [],
      selectedCategories: this.filters.categories || [],
      selectedCustomerTypes: this.filters.customer_types || [],
      selectedPaymentMethods: this.filters.payment_methods || [],
      selectedSalesPersons: this.filters.sales_persons || [],
      darkMode: false,
    };
  },

  mounted() {
    this.initCharts();
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

    applyFilters() {
      this.$inertia.get(
        route("sales.index"),
        {
          date_range: this.dateRange,
          regions: this.selectedRegions,
          categories: this.selectedCategories,
          customer_types: this.selectedCustomerTypes,
          payment_methods: this.selectedPaymentMethods,
          sales_persons: this.selectedSalesPersons,
        },
        {
          preserveState: true,
          replace: true,
          preserveScroll: true,
        }
      );
    },

    resetFilters() {
      this.dateRange = null;
      this.selectedRegions = [];
      this.selectedCategories = [];
      this.selectedCustomerTypes = [];
      this.selectedPaymentMethods = [];
      this.selectedSalesPersons = [];
      this.applyFilters();
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

      // Gráfico Regional
      if (this.$refs.regionalChart) {
        this.charts.regional = new Chart(this.$refs.regionalChart, {
          type: "doughnut",
          data: {
            labels: this.indicators.regional_sales.map((item) => item.region),
            datasets: [
              {
                data: this.indicators.regional_sales.map((item) => item.total),
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
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(number || 0);
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

    sortedSalesPersons() {
      const salesPersons = this.filterOptions.sales_persons || [];
      return salesPersons
        .filter((person) => typeof person === "string" && person.trim() !== "") // Filtrar cadenas válidas
        .sort((a, b) => a.localeCompare(b)); // Ordenar directamente las cadenas
    }
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