<template>
  <div class="relative min-w-[200px]">
    <label class="block text-sm font-medium mb-2 dark:text-gray-300">
      {{ label }}
    </label>
    <select v-model="selected"
      class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:text-gray-100"
      @change="$emit('update:modelValue', selected)">
      <option value="">Todos</option>
      <option v-for="option in options" :key="option" :value="option" class="dark:bg-gray-800 dark:text-gray-100">
        {{ option }}
      </option>
    </select>
  </div>
</template>

<script>
export default {
  props: ["modelValue", "options", "label"],
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