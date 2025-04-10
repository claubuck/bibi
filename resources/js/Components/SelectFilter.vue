<template>
  <div class="relative min-w-[180px]">
    <label
      class="block text-sm font-medium mb-2"
      :class="dark ? 'text-gray-300' : 'text-gray-700'"
    >
      {{ label }}
    </label>
    <select
      v-model="selected"
      class="w-full border rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
      :class="
        dark
          ? 'bg-gray-800 border-gray-600 text-gray-100 focus:ring-blue-600'
          : 'bg-white border-gray-300 text-gray-900 focus:ring-blue-500'
      "
      @change="$emit('update:modelValue', selected)"
    >
      <option value="">Todos</option>
      <option
        v-for="option in options"
        :key="option"
        :value="option"
        class="dark:bg-gray-800 dark:text-gray-100"
      >
        {{ option }}
      </option>
    </select>
  </div>
</template>
  
  <script>
export default {
  props: ["modelValue", "options", "label", "dark"],
  emits: ["update:modelValue"],
  data() {
    return {
      selected: this.modelValue,
    };
  },
  watch: {
    modelValue(newVal) {
      this.selected = newVal;
    },
  },
};
</script>