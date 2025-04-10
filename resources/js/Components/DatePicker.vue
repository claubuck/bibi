<template>
  <Popover class="relative w-full" v-slot="{ close }">
    <PopoverButton
      class="w-full px-4 py-2 border rounded-lg text-left"
      :class="
        darkMode
          ? 'bg-gray-800 text-white border-gray-600'
          : 'bg-white text-gray-900 border-gray-300'
      "
    >
      <span v-if="modelValue">
        {{ formatDate(modelValue.start) }} - {{ formatDate(modelValue.end) }}
      </span>
      <span v-else class="text-gray-400">Seleccionar rango</span>
    </PopoverButton>

    <Transition
      enter="transition duration-200 ease-out"
      enter-from="opacity-0 translate-y-2"
      enter-to="opacity-100 translate-y-0"
      leave="transition duration-150 ease-in"
      leave-from="opacity-100 translate-y-0"
      leave-to="opacity-0 translate-y-2"
    >
      <PopoverPanel
        class="absolute z-10 mt-2 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg w-[300px]"
      >
        <div class="flex flex-col space-y-2">
          <label class="text-sm text-gray-700 dark:text-gray-200">Desde</label>
          <input
            type="date"
            v-model="start"
            class="rounded px-2 py-1 border dark:bg-gray-700 dark:text-white"
          />

          <label class="text-sm text-gray-700 dark:text-gray-200">Hasta</label>
          <input
            type="date"
            v-model="end"
            class="rounded px-2 py-1 border dark:bg-gray-700 dark:text-white"
          />

          <button
            class="mt-2 px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
            @click="applyRange(close)"
          >
            Aplicar
          </button>
        </div>
      </PopoverPanel>
    </Transition>
  </Popover>
</template>

<script setup>
import { ref, watch } from "vue";
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
import { format } from "date-fns";

const props = defineProps({
  modelValue: Object,
  darkMode: Boolean,
});
const emit = defineEmits(["update:modelValue"]);

const start = ref(null);
const end = ref(null);

watch(
  () => props.modelValue,
  (val) => {
    start.value = val?.start || null;
    end.value = val?.end || null;
  },
  { immediate: true }
);

const formatDate = (date) => {
  if (!date) return "";
  return format(new Date(date), "dd/MM/yyyy");
};

const applyRange = (close) => {
  if (start.value && end.value) {
    emit("update:modelValue", { start: start.value, end: end.value });
    close(); // 👈 cerrar el Popover
  }
};
</script>