<template>
  <div
      class="w-1/4 p-6 rounded-xl shadow-md h-full"
      :class="darkMode ? 'dark:bg-gray-800' : 'bg-white'"
    >
      <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
        Filtros
      </h2> 
    <VueDatePicker
      v-model="localFilters.date_range"
      range
      :enable-time-picker="false"
      :dark="darkMode"
      @update:model-value="applyFilters"
    />
    <MultiSelect
      v-model="localFilters.regions"
      :options="filterOptions.regions"
      label="Regiones"
      :dark="darkMode"
      @update:model-value="applyFilters"
    />
    <MultiSelect
      v-model="localFilters.categories"
      :options="filterOptions.categories"
      label="Categorías"
      :dark="darkMode"
      @update:model-value="applyFilters"
    />
    <div class="mt-4">
      <SelectFilter
        v-model="localFilters.customer_types"
        :options="filterOptions.customer_types"
        label="Tipo de Cliente"
        :dark="darkMode"
        @update:model-value="applyFilters"
      />
      <SelectFilter
        v-model="localFilters.payment_methods"
        :options="filterOptions.payment_methods"
        label="Método de Pago"
        :dark="darkMode"
        @update:model-value="applyFilters"
      />
      <SelectFilter
        v-model="localFilters.sales_persons"
        :options="sortedSalesPersons"
        label="Vendedor"
        :dark="darkMode"
        @update:model-value="applyFilters"
      />
    </div>
    <div class="mt-4">
      <button
        @click="resetFilters"
        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors w-full"
      >
        Limpiar Filtros
      </button>
    </div>
  </div>
</template>

<script>
import MultiSelect from "@/Components/MultiSelect.vue";
import SelectFilter from "@/Components/SelectFilter.vue";
import VueDatePicker from "@vuepic/vue-datepicker";

export default {
  components: {
    MultiSelect,
    SelectFilter,
    VueDatePicker,
  },
  props: {
    darkMode: Boolean,
    filterOptions: Object,
    filters: Object,
  },
  data() {
    return {
      localFilters: { ...this.filters }, // Copia local de los filtros
    };
  },
  methods: {
    resetFilters() {
      this.localFilters = {
        date_range: null,
        regions: [],
        categories: [],
        customer_types: [],
        payment_methods: [],
        sales_persons: [],
      };
      this.applyFilters();
    },

    applyFilters() {
      console.log("Aplicando filtros:", this.localFilters);
      this.$inertia.get(
        route("sales.index"),
        {
          date_range: this.localFilters.date_range,
          regions: this.localFilters.regions,
          categories: this.localFilters.categories,
          customer_types: this.localFilters.customer_types,
          payment_methods: this.localFilters.payment_methods,
          sales_persons: this.localFilters.sales_persons,
        },
        {
          preserveState: true,
          replace: true,
          preserveScroll: true,
        }
      );
    },
  },

  computed: {
    sortedSalesPersons() {
      const salesPersons = this.filterOptions.sales_persons || [];
      return salesPersons
        .filter((person) => typeof person === "string" && person.trim() !== "") // Filtrar cadenas válidas
        .sort((a, b) => a.localeCompare(b)); // Ordenar directamente las cadenas
    },
  },
};
</script>