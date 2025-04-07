<template>
    <div class="relative h-full">
      <div 
        class="rounded-xl p-6 transition-all duration-300 h-full"
        :class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-100'"
      >
        <h3 :class="darkMode ? 'text-gray-200' : 'text-gray-900'" class="text-lg font-semibold mb-4">
          Distribución Regional
        </h3>
        
        <div v-if="loading" class="h-96 flex items-center justify-center">
          <div class="animate-pulse flex space-x-4">
            <div class="rounded-full bg-blue-500 h-12 w-12"></div>
          </div>
        </div>
        
        <div v-else-if="error" class="h-96 flex items-center justify-center text-red-500">
          Error cargando el mapa
        </div>
  
        <div v-else ref="mapContainer" class="h-96 rounded-lg"></div>
      </div>
    </div>
  </template>
  
  <script>
  import { Loader } from '@googlemaps/js-api-loader';
  
  export default {
    props: {
      regionalData: {
        type: Array,
        required: true
      },
      darkMode: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        map: null,
        heatmap: null,
        loading: true,
        error: false,
        google: null,
        coordinatesCache: JSON.parse(localStorage.getItem('googleRegionCoordinates')) || {}
      };
    },
    async mounted() {
      try {
        await this.initializeGoogleMaps();
        await this.processRegions();
        this.initMap();
      } catch (error) {
        console.error('Error:', error);
        this.error = true;
      } finally {
        this.loading = false;
      }
    },
    methods: {
      async initializeGoogleMaps() {
        const loader = new Loader({
          apiKey: 'TU_API_KEY_GOOGLE', // Obtén una clave en https://console.cloud.google.com/
          version: 'weekly',
          libraries: ['visualization']
        });
  
        this.google = await loader.load();
      },
  
      async processRegions() {
        const newRegions = this.regionalData.filter(
          region => !this.coordinatesCache[region.region]
        );
  
        for (const region of newRegions) {
          await this.geocodeRegion(region.region);
        }
      },
  
      async geocodeRegion(regionName) {
        try {
          const geocoder = new this.google.maps.Geocoder();
          const response = await geocoder.geocode({ address: `${regionName}, España` });
  
          if (response.results.length > 0) {
            const location = response.results[0].geometry.location;
            this.coordinatesCache[regionName] = {
              lat: location.lat(),
              lng: location.lng()
            };
            localStorage.setItem('googleRegionCoordinates', JSON.stringify(this.coordinatesCache));
          }
        } catch (error) {
          console.error('Geocoding error:', error);
        }
      },
  
      initMap() {
        const mapOptions = {
          center: { lat: 40.4168, lng: -3.7038 },
          zoom: 5,
          disableDefaultUI: true,
          styles: this.darkMode ? this.darkStyle : []
        };
  
        this.map = new this.google.maps.Map(this.$refs.mapContainer, mapOptions);
        this.createHeatmap();
      },
  
      createHeatmap() {
        const heatmapData = this.regionalData
          .filter(region => this.coordinatesCache[region.region])
          .map(region => ({
            location: new this.google.maps.LatLng(
              this.coordinatesCache[region.region].lat,
              this.coordinatesCache[region.region].lng
            ),
            weight: region.total
          }));
  
        this.heatmap = new this.google.maps.visualization.HeatmapLayer({
          data: heatmapData,
          map: this.map,
          radius: 40,
          gradient: this.getGradient()
        });
      },
  
      getGradient() {
        return this.darkMode 
          ? ['rgba(0, 255, 255, 0)', 'rgba(0, 255, 255, 1)', 'rgba(0, 191, 255, 1)', 'rgba(0, 127, 255, 1)', 'rgba(0, 63, 255, 1)', 'rgba(0, 0, 255, 1)']
          : ['rgba(0, 255, 0, 0)', 'rgba(0, 255, 0, 1)', 'rgba(255, 255, 0, 1)', 'rgba(255, 165, 0, 1)', 'rgba(255, 0, 0, 1)'];
      },
  
      darkStyle: [
        { elementType: 'geometry', stylers: [{ color: '#242f3e' }] },
        { elementType: 'labels.text.stroke', stylers: [{ color: '#242f3e' }] },
        { elementType: 'labels.text.fill', stylers: [{ color: '#746855' }] },
        // ... (Más estilos para modo oscuro)
      ]
    },
    watch: {
      darkMode() {
        if (this.map) {
          this.map.setOptions({
            styles: this.darkMode ? this.darkStyle : []
          });
          this.heatmap.set('gradient', this.getGradient());
        }
      },
      regionalData() {
        if (this.heatmap) {
          this.createHeatmap();
        }
      }
    },
    beforeDestroy() {
      if (this.heatmap) {
        this.heatmap.setMap(null);
      }
    }
  };
  </script>