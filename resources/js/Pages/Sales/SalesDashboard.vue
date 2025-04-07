<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-8 flex">
    <FilterSidebar
      :filter-options="filterOptions"
      :initial-filters="filters"
      :dark-mode="darkMode"
      @filter-changed="applyFilters"
      @reset-filters="resetFilters"
    />

    <div class="w-3/4 pl-8">
      <DashboardHeader
        :dark-mode="darkMode"
        @toggle-dark-mode="toggleDarkMode"
      />

      <KpiSection :indicators="indicators" :dark-mode="darkMode" />

      <DashboardCharts
        :indicators="indicators"
        :dark-mode="darkMode"
        class="mt-8"
      />
    </div>
  </div>
</template>
  
  <script>
import FilterSidebar from "@/Components/FilterSidebar.vue";
import DashboardHeader from "@/Components/DashboardHeader.vue";
import KpiSection from "@/Components/KpiSection.vue";
import DashboardCharts from "@/Components/DashboardCharts.vue";

export default {
  components: {
    FilterSidebar,
    DashboardHeader,
    KpiSection,
    DashboardCharts,
  },
  props: {
    indicators: Object,
    filterOptions: Object,
    filters: {
      type: Object,
      default: () => ({
        date_range: null,
        regions: [],
        categories: [],
        customer_types: [],
        payment_methods: [],
        sales_persons: [],
      }),
    },
  },
  data() {
    return {
      darkMode: false,
    };
  },
  methods: {
    toggleDarkMode() {
      this.darkMode = !this.darkMode;
    },
    applyFilters(filters) {
      const normalized = this.normalizeFilters(filters);

      // Verificar si hay cambios reales
      if (JSON.stringify(normalized) === JSON.stringify(this.filters)) return;

      this.$inertia.get(route("sales.index"), normalized, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
        onSuccess: () => {
          this.$nextTick(() => {
            // Forzar actualización de gráficos
            if (this.$refs.chartsComponent) {
              this.$refs.chartsComponent.initCharts();
            }
          });
        },
      });
    },
    normalizeFilters(filters) {
      return Object.fromEntries(
        Object.entries(filters).filter(([_, v]) => {
          if (Array.isArray(v)) return v.length > 0;
          return v !== null && v !== undefined;
        })
      );
    },

    resetFilters() {
      this.$inertia.get(
        route("sales.index"),
        {},
        {
          preserveState: false,
          replace: true,
        }
      );
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
  background-color: #f3f4f6; /* bg-gray-100 */
}

select.dark::-webkit-scrollbar-track {
  background-color: #1f2937; /* dark:bg-gray-800 */
}

select::-webkit-scrollbar-thumb {
  background-color: #d1d5db; /* bg-gray-300 */
  border-radius: 9999px; /* rounded-full */
}

select.dark::-webkit-scrollbar-thumb {
  background-color: #4b5563; /* dark:bg-gray-600 */
}
</style>