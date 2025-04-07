<template>
  <div class="w-1/4 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md h-full">
    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
      Filtros
    </h2>

    <div>
      <label class="block text-sm font-medium dark:text-gray-300"
        >Rango de Fechas</label
      >
      <VueDatePicker
        v-model="localFilters.date_range"
        range
        :enable-time-picker="false"
        :dark="darkMode"
        :class="darkMode ? 'dp-custom-dark' : 'dp-custom-light'"
      />
    </div>

    <MultiSelect
      v-model="localFilters.regions"
      :options="filterOptions.regions"
      label="Regiones"
    />
    <MultiSelect
      v-model="localFilters.categories"
      :options="filterOptions.categories"
      label="Categorías"
    />

    <div class="mt-4">
      <SelectFilter
        v-model="localFilters.customer_types"
        :options="filterOptions.customer_types"
        label="Tipo de Cliente"
      />
      <SelectFilter
        v-model="localFilters.payment_methods"
        :options="filterOptions.payment_methods"
        label="Método de Pago"
      />
      <SelectFilter
        v-model="localFilters.sales_persons"
        :options="filterOptions.sales_persons"
        label="Vendedor"
      />
    </div>

    <button
      @click="$emit('reset-filters')"
      class="mt-4 px-4 py-2 w-full bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
    >
      Limpiar Filtros
    </button>
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
    filterOptions: Object,
    initialFilters: Object,
    darkMode: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    const defaultFilters = {
      date_range: null,
      regions: [],
      categories: [],
      customer_types: [],
      payment_methods: [],
      sales_persons: [],
    };

    return {
      defaultFilters, // <-- Guardar como referencia
      localFilters: JSON.parse(
        JSON.stringify({
          ...defaultFilters,
          ...this.initialFilters,
        })
      ),
      isUpdatingFromParent: false,
    };
  },
  watch: {
    initialFilters: {
      handler(newVal) {
        if (this.isEqual(newVal, this.localFilters)) return;

        this.isUpdatingFromParent = true;
        this.localFilters = JSON.parse(
          JSON.stringify({
            ...this.defaultFilters,
            ...newVal,
          })
        );
        this.$nextTick(() => (this.isUpdatingFromParent = false));
      },
      deep: true,
      immediate: true,
    },
  },
  watch: {
    localFilters: {
      handler: _.debounce(function (newVal, oldVal) {
        if (!this.isUpdatingFromParent && !this.isEqual(newVal, oldVal)) {
          this.$emit("filter-changed", newVal);
        }
      }, 500),
      deep: true,
    },
  },

  methods: {
    isEqual(a, b) {
      return JSON.stringify(a) === JSON.stringify(b);
    },
  },
};
</script>