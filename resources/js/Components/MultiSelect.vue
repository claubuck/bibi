<template>
    <div class="relative min-w-[180px]">
      <label class="block text-sm font-medium mb-2 dark:text-gray-300">
        {{ label }}
      </label>
      <Listbox v-model="selected" @change="$emit('update:modelValue', selected)">
        <div class="relative">
          <!-- Botón del Listbox -->
          <ListboxButton
            class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg py-2 px-3 text-sm text-left focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:text-gray-100"
          >
            <span class="block truncate">
              {{ selected || 'Todos' }}
            </span>
            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
              <svg
                class="w-5 h-5 text-gray-400"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </span>
          </ListboxButton>
  
          <!-- Opciones del Listbox -->
          <Transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <ListboxOptions
              class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 py-1 text-sm overflow-auto focus:outline-none"
            >
              <ListboxOption
                v-for="option in options"
                :key="option"
                :value="option"
                class="cursor-pointer select-none relative py-2 pl-10 pr-4 hover:bg-gray-100 dark:hover:bg-gray-700"
              >
                <span
                  :class="{
                    'font-medium': selected === option,
                    'font-normal': selected !== option,
                  }"
                  class="block truncate dark:text-gray-100"
                >
                  {{ option }}
                </span>
              </ListboxOption>
            </ListboxOptions>
          </Transition>
        </div>
      </Listbox>
    </div>
  </template>
  
  <script>
  import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from "@headlessui/vue";
  
  export default {
    components: { Listbox, ListboxButton, ListboxOptions, ListboxOption, Transition: null },
    props: ["modelValue", "options", "label"],
    emits: ["update:modelValue"],
    data() {
      return {
        selected: this.modelValue || "",
      };
    },
    watch: {
      modelValue(newVal) {
        this.selected = newVal;
      },
    },
  };
  </script>
  
  <style>
  /* Opcional: Estilos personalizados para el Listbox */
  </style>