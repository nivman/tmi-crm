<template>
  <div class="panel panel-default is-centered" @refresh="get" v-if="config">
    <div class="panel-heading">
            <span v-if="!params.hideFilter">
                <button type="button" class="button is-primary is-small is-pulled-right m-l-sm" @click="download()">
                    <i class="fas fa-download"></i>
                </button>
                    <div class="select is-small is-pulled-right m-l-sm categories-time-list" v-if="params.showCategoriesList">
                          <flat-pickr
                              class="input"
                              id="end_date"
                              name="end_date"
                              enableTime="true"
                              :config="range_date_config"
                              v-model="dateRange"
                              @input="$emit('rangeChanged', dateRange)"
                              :class="{ 'is-danger': errors.has('end_date') }"
                          ></flat-pickr>
                    </div>
                <div class="select is-small is-pulled-right m-l-sm" v-if="!params.showProjectsList && !params.showCategoriesList">
                    <select @change="$emit('yearChanged', $event.target.value)">
                        <option v-for="year in years" :value="year" :key="year" :selected="params.year == year"> {{
                            שנה
                          }}</option>
                    </select>
                </div>
                <div class="select is-small is-pulled-right" v-if="params.month && !params.showProjectsList" >
                    <select @change="$emit('monthChanged', $event.target.value)">
                        <option v-for="month in months" :value="month" :key="month" :selected="params.month == month"> {{
                            חודש
                          }}</option>
                    </select>
                </div>

             <div class="is-pulled-right projects-dropdown-wrapper"  v-if="params.showProjectsList">
                            <multiselect
                                selectLabel=""
                                deselectLabel=""
                                class="multiselect-column rtl-direction projects-dropdown"
                                v-model="project"
                                @input="$emit('projectChanged', $event)"
                                :options="projects"
                                :multiple="true"
                                :option-height="104"
                                :searchable="true"
                                :allow-empty="true"
                                :close-on-select="true"
                                placeholder="בחירת פרוייקטים"
                                label="name"
                                track-by="name">
              </multiselect>
               </div>
            </span>
      {{ config.title }}
    </div>
    <div class="panel-body">
      <loading v-if="loading"></loading>
      <div class="columns">
        <div class="column has-text-centered">

          <div class="menu-bar-chart" v-if="config.type ==='bar' && params.showProjectsList">
          <div>אחוז שעות העבודה לקטגוריה מתוך כלל השעות בפרוייקט : %</div>
          <div>שעות עבודה לקטגוריה : ש</div>
          </div>
          <chart
              ref="chart"
              :data="config.data"
              :type="config.type"
              :options="config.options"
              class="has-padding-medium inline-block"
              :style="{ width: width, height: height, color: 'red' }"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from './Chart.vue'
import _times from 'lodash/times'
import { saveAs } from 'file-saver'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import * as moment from "moment";
export default {
  components: { Chart, Multiselect, flatPickr },
  props: {
    width: { default: '100%' },
    height: { default: '400px' },
    params: { type: Object, default: null },
    source: { type: String, required: true },

  },
  data () {
    return {

      dateRange: '',
      loading: false,
      config: null,
      project:'',
      projects: [],
      // range_date_config: {
      //   altInput: true,
      //   altFormat: 'd/m/Y',
      //   dateFormat: 'd/m/Y',
      //   mode: "range"
      // }
    }
  },
  created() {
    this.$http.get('app/projects/list')
        .then((res)=>{

          this.projects = res.data

        })
    let startDate = moment(new Date()).startOf('month').format('DD/MM/YYYY');
    let endDate = moment(new Date()).endOf('month').format('DD/MM/YYYY');
    this.dateRange = `${startDate} אל ${endDate}`
  },
  computed: {
    range_date_config() {
      return {
        locale: this.getLocale !== 'en' ? require(`flatpickr/dist/l10n/he.js`).default.he : 'en',
        altInput: true,
        altFormat: 'd/m/Y',
        dateFormat: 'd/m/Y',
        mode: "range"

      }
    },
    months () {
      let date = new Date()
      let months = this.params.year == date.getFullYear() ? date.getMonth() + 1 : 12
      return _times(months).map(i =>
          this.$moment()
              .month(i)
              .format('MM')
      )
    },
    years () {
      let years = []
      let date = new Date()
      let year = date.getFullYear()
      for (let y = 2017; y <= year; y++) {
        years.push(y)
      }
      return years
    },
  },
  watch: {
    params: {
      handler () {
        this.get()
      },
      deep: true,
    },
  },
  mounted () {
    this.get()
  },
  methods: {
    get () {
      if (this.$store.getters.user) {
        this.loading = true
        this.$http
            .get(this.source, { params: this.params })
            .then(({ data }) => {

                data.data.labels = Object.keys(data.data.labels).map((key) => data.data.labels[key]);
               if (data.options.projectsData) {

                let projectLabels = data.data.datasets
                let labels = Object.keys(data.options.projectsData).map((key) => data.options.projectsData[key]);

                labels.forEach(function(project, index){
                  if (project.name === projectLabels[index].label) {
                    let price = !project.price ? 'לא  נקבע' : project.price;

                    let percentageDone = !project.price ? '':  parseFloat((project.actual_time * 150) * 100 / price).toFixed(1)+'%';
                    data.data.datasets[index].label = project.name + ' ( מחיר: ' +
                        price +
                        ' ;זמן: ' +
                        parseFloat(project.actual_time) +
                        ' ;אחוז סופי: ' +
                        percentageDone +
                        ')'
                  }

                })

              }

              this.translateTerm(data)
              this.config = data

              this.loading = false
            })
            .catch(err => {
              this.loading = false
              this.$event.fire('appError', err.response)
            })
      }
    },
    translateTerm (data) {
    //TODO refactor this
      data.data.datasets.forEach((element, index) => {
        if (element.label) {
         this.translate(data.data.datasets,index)
        }
      })

      data.data.labels.forEach((element, index) => {

        switch (element) {
          case 'Invoices':
            data.data.labels[index] = 'חשבוניות'
            break
          case 'Purchases':
            data.data.labels[index] = 'רכישות'
            break
          case 'Incomes':
            data.data.labels[index] = 'הכנסות'
            break
          case 'Expenses':
            data.data.labels[index] = 'הוצאות'
            break
          default:
            // code block
        }
      })

    },
    translate(data, index) {
      //TODO refactor this
      switch (data[index].label) {
        case 'Invoices':
          data[index].label = 'חשבוניות'
          break
        case 'Purchases':
          data[index].label = 'רכישות'
          break
        case 'Incomes':
          data[index].label = 'הכנסות'
          break
        case 'Expenses':
          data[index].label = 'הוצאות'
          break
        default:

      }
    },
    download () {
      this.$refs.chart.$el.toBlob(blob => saveAs(blob, `${this.config.title}.png`))
    },
  },
}
</script>

<style>
.panel-body {
  position: relative;
}

.inline-block {
  display: inline-block !important;
}
.multiselect__content-wrapper{
  width: 150%;
  text-align: right;
}
.projects-dropdown-wrapper{
  position: relative;
  left:3%;
}
.projects-dropdown{

  text-align: right;
  bottom: 5px;
}
.menu-bar-chart{
  text-align: right;
  font-size: 12px;
  font-weight: bold;
}
#end_date + input {
  padding-right: calc(1.75em - 1px);
}

</style>
