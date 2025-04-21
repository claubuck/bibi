<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

const salesData = {
  labels: ["Ene", "Feb", "Mar", "Abr", "May"],
  datasets: [
    {
      label: "Ventas",
      data: [65, 59, 80, 81, 56],
      backgroundColor: "rgba(99, 102, 241, 0.2)",
      borderColor: "rgb(99, 102, 241)",
      tension: 0.4,
      borderWidth: 2,
    },
  ],
};

const costsData = {
  labels: ["Ene", "Feb", "Mar", "Abr", "May"],
  datasets: [
    {
      label: "Costos",
      data: [28, 48, 40, 19, 36],
      backgroundColor: "rgba(239, 68, 68, 0.2)",
      borderColor: "rgb(239, 68, 68)",
      tension: 0.4,
      borderWidth: 2,
    },
  ],
};

onMounted(() => {
  new Chart(document.getElementById("salesChart"), {
    type: "line",
    data: salesData,
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { grid: { color: "rgba(255,255,255,0.1)" } } },
    },
  });

  new Chart(document.getElementById("costsChart"), {
    type: "line",
    data: costsData,
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { grid: { color: "rgba(255,255,255,0.1)" } } },
    },
  });
});
</script>

<template>
  <Head title="Dashboard" />
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-200 dark:from-gray-900 dark:to-gray-800">
    <div class="relative flex flex-col min-h-screen">
      <!-- Header -->
      <header class="w-full bg-white dark:bg-gray-900 shadow p-6 mb-6">
        <nav class="flex items-center justify-between max-w-7xl mx-auto">
          <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
            Business Dashboard
          </div>
        </nav>
      </header>

      <!-- Main Content -->
      <main class="w-full max-w-7xl mx-auto px-6 pb-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Ventas -->
          <Link :href="route('sales.index')" class="group bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white group-hover:text-indigo-600 transition-colors">
              Resumen de Ventas
            </h2>
            <div class="h-64">
              <canvas id="salesChart"></canvas>
            </div>
            <div class="mt-4 text-gray-600 dark:text-gray-300">
              <p class="text-lg font-medium">
                Total este mes: 
                <span class="font-bold text-indigo-600">$24,500</span>
              </p>
              <span class="inline-flex items-center mt-2 px-2 py-1 text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                ↑ 12% respecto al mes anterior
              </span>
            </div>
          </Link>

          <!-- Costos -->
          <Link :href="route('sales.index')" class="group bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white group-hover:text-red-600 transition-colors">
              Resumen de Costos
            </h2>
            <div class="h-64">
              <canvas id="costsChart"></canvas>
            </div>
            <div class="mt-4 text-gray-600 dark:text-gray-300">
              <p class="text-lg font-medium">
                Total este mes: 
                <span class="font-bold text-red-600">$15,200</span>
              </p>
              <span class="inline-flex items-center mt-2 px-2 py-1 text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                ↓ 8% respecto al mes anterior
              </span>
            </div>
          </Link>
        </div>
      </main>
    </div>
  </div>
</template>
