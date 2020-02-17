<template>
  <div class="example">
    <apexchart type=bar height=150 :options="chartOptions" :series="series"></apexchart>
  </div>
</template>
<script>
import VueApexCharts from 'vue-apexcharts';

Vue.component('apexchart', VueApexCharts)

export default {
  name: 'workingchart',
  data: function() {
    return {
      series: [{
        name: '所定時間',
        data: [134.25]
      }, {
        name: '残業時間',
        data: [36.0]
      }, {
        name: '深夜残業時間',
        data: [10.0]
      }],
      chartOptions: {
        chart: {
          stacked: true,
        },
        plotOptions: {
          bar: {
            horizontal: false,
          },

        },
        stroke: {
          width: 1,
          colors: ['#fff']
        },

        title: {
          text: ''
        },
        xaxis: {
          categories: ['2019年10月', '2019年11月'],
          labels: {
            formatter: function (val) {
              return val + "時間"
            }
          }
        },
        yaxis: {
          title: {
            text: undefined
          },

        },
        tooltip: {
          y: {
            formatter: function (val) {
              return val + "時間"
            }
          }
        },
        fill: {
          opacity: 1

        },

        legend: {
          position: 'top',
          horizontalAlign: 'left',
          offsetX: 40
        }
      }
    }
  }
}
</script>