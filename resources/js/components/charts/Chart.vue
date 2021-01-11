<template>
  <canvas class="chart-js"/>
</template>

<script>
import Chart from 'chart.js';
import 'chartjs-plugin-datalabels';

// Chart.defaults.global.plugins.datalabels.formatter = value => `${format(value)}m`;

Chart.scaleService.updateScaleDefaults('linear', {ticks: {min: 0}});
const types = ['line', 'bar', 'radar', 'polarArea', 'pie', 'doughnut', 'bubble'];

export default {
  name: 'Chart',
  props: {
    type: {
      type: String,
      required: true,
      validator: value => types.includes(value),
    },
    data: {
      type: Object,
      required: true,
    },
    options: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      chart: null,
    };
  },
  watch: {
    data() {
      this.update();
    },
  },
  mounted() {
    this.init();
  },
  beforeDestroy() {
    this.chart.destroy();
  },
  methods: {
    init() {

      let em = this;
      this.chart = new Chart(this.$el, {
        type: this.type,
        data: this.data,
        options: {
          tooltips: false,

          plugins: {
            datalabels: {
              anchor: 'center',
              textAlign: 'center',
              height:500,
              align: 'end',
              borderRadius: 3,
              padding: 2,
              color: 'black',
              font: {
                style: 'bold',
                lineHeight: 1,

              },
              labels: {
                title: {
                  backgroundColor: 'transparent',
                  color: 'black'
                },
                value: {
                  color: 'black',
                  backgroundColor: 'transparent',
                }
              },

              display(context) {

                const {chart} = context;
                const meta = chart.getDatasetMeta(context.datasetIndex);
                return !meta.hidden;
              },
              formatter: function (value, chart) {

                if (em.type === 'bar') {

                    let groupProjectsValues = em.groupBy()

                  groupProjectsValues =  groupProjectsValues.map(function (value){
                    return {'value' :value.total, 'name' :value.name}
                  })

                  let percentageHours = (value * 100 / groupProjectsValues[chart.datasetIndex].value).toFixed(1);
                  return  percentageHours+'%'+'\n'+'\n' + ' ש: ' + value;
                  //  let projectTotalHours = (groupProjectsValues[chart.datasetIndex].value).toFixed(1);
                  //  return  percentageHours+'%'+'\n'+' סה"כ: '+projectTotalHours+'\n' + ' שעות: ' + value;
                }

              },

            },
          },
          ...this.options,
        },
      });
    },
    groupBy() {

      let chartsNumbers = []
      this.data.datasets.forEach(function (dataset, index) {
        dataset.data.map(function (chartNumber) {
          let label = {'name': dataset.label, 'value' : parseFloat(chartNumber)};
          chartsNumbers.push(label)

        },index);
      })

      Array.prototype.groupBy = function(key) {
        return this.reduce(function (r, a, i) {
          if (!i || r[r.length - 1][0][key] !== a[key]) {
            return r.concat([[a]]);
          }
          r[r.length - 1].push(a);
          return r;
        }, []);
      };
      let projectsNameGroup = chartsNumbers.groupBy("name");
      let totalProjectsHours = []
          projectsNameGroup.forEach(function (projectNameGroup, index) {
          Array.prototype.sum = function (value, name) {
            let total = 0
            for ( var i = 0, _len = this.length; i < _len; i++ ) {
              total += this[i][value]
            }
            return {"name": name, 'total' : total}

          }
            totalProjectsHours.push(projectNameGroup.sum("value", projectNameGroup[0].name));
      })
      chartsNumbers.groupBy("name")

      return totalProjectsHours;

    },
    update() {
      if (!this.chart) {
        this.init();
        return;
      }

      this.updateDatasets();
      this.chart.data.labels = this.data.labels;
      this.chart.update();
    },
    updateDatasets() {
      if (this.datasetsStructureChanged()) {
        this.$set(this.chart.data, 'datasets', this.data.datasets);
        return;
      }

      this.chart.data.datasets.forEach((dataset, index) => {
        dataset.data = this.data.datasets[index].data;
      });
    },
    datasetsStructureChanged() {
      return (
          this.chart.data.datasets.length !== this.data.datasets.length ||
          this.chart.data.datasets.filter(({label}) => this.data.datasets.findIndex(dataset => dataset.label === label) === -1)
              .length !== 0
      );
    },
    svg() {
      return this.$el.toDataURL('image/jpg');
    },
  },
};
</script>

<style scoped>
.chart-js {
  max-width: 100%;
}
</style>
