<template>
  <div class="panel panel-default is-centered" @refresh="get" v-if="config">
    <div class="panel-heading">
            <span v-if="!params.hideFilter">
                <button type="button" class="button is-primary is-small is-pulled-right m-l-sm" @click="download()">
                    <i class="fas fa-download"></i>
                </button>
                <div class="select is-small is-pulled-right m-l-sm" v-if="!params.showProjectsList">
                    <select @change="$emit('yearChanged', $event.target.value)">
                        <option v-for="year in years" :value="year" :key="year" :selected="params.year == year"> {{
                            year
                          }}</option>
                    </select>
                </div>
                <div class="select is-small is-pulled-right" v-if="params.month && !params.showProjectsList" >
                    <select @change="$emit('monthChanged', $event.target.value)">
                        <option v-for="month in months" :value="month" :key="month" :selected="params.month == month"> {{
                            month
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

          <div class="menu-bar-chart" v-if="config.type ==='bar'">
          <div>אחוז שעות העבודה לקטגוריה מתוך כלל השעות בפרוייקט : %</div>
          <div>סה"כ שעות לפרוייקט : סה"כ </div>
          <div>שעות עבודה לקטגוריה : שעות</div>
          </div>
          <chart
              ref="chart"
              :data="config.data"
              :type="config.type"
              :options="config.options"
              class="has-padding-medium inline-block"
              :style="{ width: width, height: height }"
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
export default {
  components: { Chart, Multiselect },
  props: {
    width: { default: '100%' },
    height: { default: '400px' },
    params: { type: Object, default: null },
    source: { type: String, required: true },

  },
  data () {
    return {
      loading: false,
      config: null,
      project:'',
      projects: []
    }
  },
  created() {
    this.$http.get('app/projects/list')
        .then((res)=>{

          this.projects = res.data

        })
  },
  computed: {
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
             let labelsType = Object.prototype.toString.call(data.data.labels)

             // if (labelsType === '[object Object]') {
                data.data.labels = Object.keys(data.data.labels).map((key) => data.data.labels[key]);
            //  }



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

<style lang="scss" scoped>
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
</style>
