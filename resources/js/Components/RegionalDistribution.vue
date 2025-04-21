<template>
  <div
    class="rounded-xl p-6 transition-all duration-300 lg:col-span-2"
    :class="
      darkMode
        ? 'bg-gray-800 border border-gray-700'
        : 'bg-white border border-gray-100'
    "
  >
    <h3
      class="text-lg font-semibold mb-4"
      :class="darkMode ? 'text-gray-200' : 'text-gray-900'"
    >
      Distribución Regional
    </h3>
    <div id="regionalMap" class="h-96 w-full rounded-lg"></div>

    <div class="mt-4">
      <h4
        class="text-lg font-semibold"
        :class="darkMode ? 'text-gray-200' : 'text-gray-900'"
      >
        Región con Mayor Crecimiento
      </h4>
      <span
        class="text-md font-bold"
        :class="darkMode ? 'text-green-400' : 'text-green-600'"
      >
        {{ topRegionWithGrowth.region }}
        (+{{ topRegionWithGrowth.growth }}%)
      </span>
    </div>
  </div>
</template>

<script>
import L from "leaflet";
import "leaflet.heat";
import "leaflet/dist/leaflet.css";

export default {
  props: {
    indicators: {
      type: Object,
      required: true,
    },
    darkMode: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      map: null,
    };
  },
  computed: {
    topRegionWithGrowth() {
      if (!this.indicators.regional_sales?.length) {
        return { region: "Sin datos", growth: 0 };
      }
      return this.indicators.regional_sales
        .map((region) => ({
          ...region,
          growth: (
            ((region.total - (region.previous_total || 0)) /
              (region.previous_total || 1)) *
            100
          ).toFixed(2),
        }))
        .sort((a, b) => b.growth - a.growth)[0]; // Tomar la región con mayor crecimiento
    },
  },
  mounted() {
    this.initRegionalMap();
  },
  methods: {
    initRegionalMap() {
      if (this.map) {
        this.map.remove(); // Si ya existe, eliminar el mapa anterior
      }

      // Crear el mapa centrado
      this.map = L.map("regionalMap").setView([39.8283, -98.5795], 4); // Coordenadas del centro geográfico aproximado de EE.UU.

      // Capa base de mapa
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution:
          '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      }).addTo(this.map);

      // Filtrar datos válidos y crear datos de calor
      const heatData = this.indicators.regional_sales
        .filter((region) => region.lat && region.lng && region.total) // Filtrar datos válidos
        .map((region) => [region.lat, region.lng, region.total]); // Crear array de [lat, lng, intensidad]

      // Verificar si hay datos válidos antes de agregar la capa de calor
      if (heatData.length > 0) {
        L.heatLayer(heatData, {
          radius: 25,
          blur: 15,
          maxZoom: 10,
        }).addTo(this.map);
      } else {
        console.warn("No hay datos válidos para mostrar en el mapa.");
      }
    },
  },
};
</script>

<style>
@import "leaflet/dist/leaflet.css";
</style>