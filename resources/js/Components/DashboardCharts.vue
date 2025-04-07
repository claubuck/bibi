<template>
  <div>
    <!-- Primera fila de gráficos principales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Gráfico de Tendencia Mensual -->
      <ChartContainer
        v-for="chart in mainCharts"
        :key="chart.title"
        :title="chart.title"
        :chart-type="chart.type"
        :chart-data="chart.data"
        :chart-options="chart.options"
        :dark-mode="darkMode"
      />

      <!-- Ranking de Productos -->
      <div
        class="rounded-xl p-6 transition-all duration-300 h-full"
        :class="
          darkMode
            ? 'bg-gray-800 border border-gray-700'
            : 'bg-white border border-gray-100'
        "
      >
        <h3
          :class="darkMode ? 'text-gray-200' : 'text-gray-900'"
          class="text-lg font-semibold mb-4"
        >
          Productos Más Vendidos
        </h3>
        <div class="space-y-3">
          <div
            v-for="(product, index) in sortedProducts"
            :key="product.product_name"
            class="flex items-center justify-between p-3 rounded-lg transition-colors"
            :class="darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'"
          >
            <div class="flex items-center space-x-3">
              <span
                class="text-sm font-medium"
                :class="darkMode ? 'text-gray-400' : 'text-gray-500'"
              >
                #{{ index + 1 }}
              </span>
              <span :class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                {{ product.product_name }}
              </span>
            </div>
            <span
              class="text-sm font-semibold"
              :class="darkMode ? 'text-blue-400' : 'text-blue-600'"
            >
              {{ formatNumber(product.total) }} unidades
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Segunda fila de gráficos secundarios -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
      <ChartContainer
        v-for="chart in secondaryCharts"
        :key="chart.title"
        :title="chart.title"
        :chart-type="chart.type"
        :chart-data="chart.data"
        :chart-options="chart.options"
        :dark-mode="darkMode"
        :class="chart.fullWidth ? 'lg:col-span-2' : ''"
      />
    </div>

    <!-- Tercera fila de gráficos adicionales -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
      <ChartContainer
        v-for="chart in additionalCharts"
        :key="chart.title"
        :title="chart.title"
        :chart-type="chart.type"
        :chart-data="chart.data"
        :chart-options="chart.options"
        :dark-mode="darkMode"
      />
    </div>
  </div>
</template>
  
  <script>
import ChartContainer from "@/Components/ChartContainer.vue";

export default {
  components: { ChartContainer },
  props: {
    indicators: {
      type: Object,
      default: () => ({
        monthly_trend: [],
        regional_sales: [],
        category_distribution: [],
        payment_methods: [],
        customer_types: [],
        sales_performance: [],
        product_performance: [],
        daily_trend: [],
        monthly_comparison: [],
      }),
    },
    darkMode: Boolean,
  },
  computed: {
    sortedProducts() {
      return (this.indicators.product_performance || [])
        .sort((a, b) => (b.total || 0) - (a.total || 0))
        .slice(0, 5);
    },
    baseOptions() {
      const textColor = this.darkMode ? "#F3F4F6" : "#111827";
      const gridColor = this.darkMode ? "#374151" : "#E5E7EB";

      return {
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
    },
    mainCharts() {
      return [
        {
          title: "Tendencia Mensual de Ventas",
          type: "line",
          data: {
            labels:
              this.indicators.monthly_trend?.map((item) =>
                new Date(2023, item.month - 1).toLocaleString("es-ES", {
                  month: "long",
                })
              ) || [],
            datasets: [
              {
                label: "Ventas",
                data:
                  this.indicators.monthly_trend?.map((item) => item.total) ||
                  [],
                borderColor: "#3B82F6",
                tension: 0.4,
              },
            ],
          },
          options: this.baseOptions,
        },
      ];
    },
    secondaryCharts() {
      return [
        {
          title: "Distribución Regional",
          type: "doughnut",
          fullWidth: true,
          data: {
            labels:
              this.indicators.regional_sales?.map((item) => item.region) || [],
            datasets: [
              {
                data:
                  this.indicators.category_distribution?.map(
                    (item) => item.total
                  ) || [],
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
          options: this.baseOptions,
        },
        {
          title: "Ventas por Categoría",
          type: "polarArea",
          data: {
            labels:
              this.indicators.category_distribution?.map(
                (item) => item.product_category
              ) || [],
            datasets: [
              {
                data:
                  this.indicators.category_distribution?.map(
                    (item) => item.total
                  ) || [],
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
          options: this.baseOptions,
        },
        {
          title: "Distribución de Pagos",
          type: "pie",
          data: {
            labels:
              this.indicators.payment_methods?.map(
                (item) => item.payment_method
              ) || [],
            datasets: [
              {
                data:
                  this.indicators.payment_methods?.map((item) => item.total) ||
                  [],
                backgroundColor: ["#3B82F6", "#10B981", "#F59E0B", "#EF4444"],
              },
            ],
          },
          options: this.baseOptions,
        },
      ];
    },
    additionalCharts() {
      return [
        {
          title: "Tipos de Clientes",
          type: "doughnut",
          data: {
            labels:
              this.indicators.customer_types?.map(
                (item) => item.customer_type
              ) || [],
            datasets: [
              {
                data:
                  this.indicators.customer_types?.map((item) => item.count) ||
                  [],
                backgroundColor: ["#3B82F6", "#10B981"],
              },
            ],
          },
          options: this.baseOptions,
        },
        {
          title: "Top Vendedores",
          type: "bar",
          data: {
            labels:
              this.indicators.sales_performance?.map(
                (item) => item.sales_person
              ) || [],
            datasets: [
              {
                label: "Ventas Totales",
                data:
                  this.indicators.sales_performance?.map(
                    (item) => item.total
                  ) || [],
                backgroundColor: "#3B82F6",
              },
            ],
          },
          options: {
            ...this.baseOptions,
            indexAxis: "y",
          },
        },
        {
          title: "Tendencia Diaria (Últimos 30 días)",
          type: "line",
          data: {
            labels:
              this.indicators.daily_trend?.map((item) =>
                new Date(item.date).toLocaleDateString("es-ES", {
                  day: "2-digit",
                  month: "short",
                })
              ) || [],
            datasets: [
              {
                label: "Ventas Diarias",
                data:
                  this.indicators.daily_trend?.map((item) => item.total) || [],
                borderColor: "#10B981",
                tension: 0.4,
                fill: true,
                backgroundColor: "rgba(16, 185, 129, 0.1)",
              },
            ],
          },
          options: this.baseOptions,
        },
        {
          title: "Comparación Mensual",
          type: "line",
          data: {
            labels:
              this.indicators.monthly_comparison?.map((item) => {
                return item.year && item.month
                  ? `${new Date(item.year, item.month - 1).toLocaleString(
                      "es-ES",
                      { month: "short" }
                    )} ${item.year}`
                  : "";
              }) || [],
            datasets: [
              {
                label: "Ventas",
                data:
                  this.indicators.monthly_comparison?.map(
                    (item) => item.total
                  ) || [],
                borderColor: "#3B82F6",
                tension: 0.4,
              },
            ],
          },
          options: {
            ...this.baseOptions,
            scales: {
              y: {
                type: "linear",
                position: "left",
              },
            },
          },
        },
      ];
    },
  },

  methods: {
    formatNumber(number) {
      return new Intl.NumberFormat("es-ES").format(number || 0);
    },
  },
};
</script>