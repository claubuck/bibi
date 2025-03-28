<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Sales Analytics Dashboard</h1>
      
      <!-- KPI Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
          <h3 class="text-gray-500 text-sm font-medium">Total Sales</h3>
          <p class="mt-2 text-3xl font-semibold text-gray-900">${{ formatNumber(indicators.total_sales) }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
          <h3 class="text-gray-500 text-sm font-medium">Average Sale</h3>
          <p class="mt-2 text-3xl font-semibold text-gray-900">${{ formatNumber(indicators.average_sale) }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-purple-500">
          <h3 class="text-gray-500 text-sm font-medium">Total Transactions</h3>
          <p class="mt-2 text-3xl font-semibold text-gray-900">{{ formatNumber(indicators.monthly_trend.reduce((acc, curr) => acc + curr.total, 0)) }}</p>
        </div>
      </div>

      <!-- Charts -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm">
          <h3 class="text-lg font-semibold mb-4">Monthly Sales Trend</h3>
          <canvas ref="trendChart"></canvas>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm">
          <h3 class="text-lg font-semibold mb-4">Top Products</h3>
          <canvas ref="productChart"></canvas>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm lg:col-span-2">
          <h3 class="text-lg font-semibold mb-4">Regional Distribution</h3>
          <canvas ref="regionalChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto';

export default {
  data() {
    return {
      indicators: {
        total_sales: 0,
        average_sale: 0,
        monthly_trend: [],
        product_performance: [],
        regional_sales: []
      },
      charts: {
        trend: null,
        product: null,
        regional: null
      }
    }
  },

  async mounted() {
    await this.fetchData();
    this.initCharts();
  },

  methods: {
    async fetchData() {
      const response = await axios.get('/api/sales');
      this.indicators = response.data;
    },

    initCharts() {
      // Destroy existing charts
      Object.values(this.charts).forEach(chart => chart?.destroy());

      // Monthly Trend Chart (Line)
      this.charts.trend = new Chart(this.$refs.trendChart, {
        type: 'line',
        data: {
          labels: this.indicators.monthly_trend.map(item => `Month ${item.month}`),
          datasets: [{
            label: 'Sales',
            data: this.indicators.monthly_trend.map(item => item.total),
            borderColor: '#3B82F6',
            tension: 0.4,
            fill: false
          }]
        }
      });

      // Product Performance (Bar)
      this.charts.product = new Chart(this.$refs.productChart, {
        type: 'bar',
        data: {
          labels: this.indicators.product_performance.map(item => item.product_name),
          datasets: [{
            label: 'Sales',
            data: this.indicators.product_performance.map(item => item.total),
            backgroundColor: '#10B981'
          }]
        }
      });

      // Regional Distribution (Doughnut)
      this.charts.regional = new Chart(this.$refs.regionalChart, {
        type: 'doughnut',
        data: {
          labels: this.indicators.regional_sales.map(item => item.region),
          datasets: [{
            data: this.indicators.regional_sales.map(item => item.total),
            backgroundColor: [
              '#3B82F6',
              '#10B981',
              '#F59E0B',
              '#EF4444',
              '#8B5CF6'
            ]
          }]
        }
      });
    },

    formatNumber(number) {
      return new Intl.NumberFormat().format(number.toFixed(2));
    }
  }
}
</script>