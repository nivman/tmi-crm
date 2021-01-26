<template>
    <div class="wrapper">
        <div class="columns">
            <div class="column is-one-quarter">
                <chart-card :source="pie" :params="pieParams" @monthChanged="pieParamsMonthChanged" @yearChanged="pieParamsYearChanged" />
            </div>
            <div class="column is-three-quarters">
<!--                <chart-card-->
<!--                    width="100%"-->
<!--                    :source="line"-->
<!--                    :params="lineParams"-->
<!--                    @monthChanged="lineParamsMonthChanged"-->
<!--                    @yearChanged="lineParamsYearChanged"-->
<!--                />-->

              <div class="columns">

                <div class="column">

                  <chart-card
                      :source="bar_category"
                      width="100%"
                      :params="barCategoriesParams"
                      class="bar-chart"
                      refs="test"
                      @rangeChanged = "barCategoryRangeChanged">
                  </chart-card>
                </div>
              </div>



            </div>
        </div>
        <div class="columns">

            <div class="column">

                <chart-card
                    :source="bar_project"
                    width="100%"
                    :params="barProjectsParams"
                    class="bar-chart"
                    @projectChanged = "barProjectChanged">
                </chart-card>
            </div>
        </div>
    </div>


</template>

<script>
import ChartCard from './charts/ChartCard.vue';
import * as moment from 'moment';
import 'moment/locale/he';
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
export default {
    components: { ChartCard, Multiselect},
    data() {
        return {
            project:'',
            projects:[],
            projectsIds: [],
            loading: true,
            pie: 'app/charts/pie_chart',
            bar_project: 'app/charts/category-hours-per-project-bar',
            bar_category: 'app/charts/hours-per-category-bar',
            line: 'app/charts/line_chart',
            barProjectsParams: {showProjectsList: true , projects :[]},
            barCategoriesParams: {showCategoriesList: true , rangeDate :[]},
            lineParams: { month: moment().format('MM'), year: moment().format('YYYY') },
            pieParams: { month: moment().format('MM'), year: moment().format('YYYY'), hideFilter: true },
        };
    },

    methods: {
        lineParamsMonthChanged(month) {
            this.lineParams.month = month;
        },
        lineParamsYearChanged(year) {
            this.lineParams.year = year;
        },
        pieParamsMonthChanged(month) {
            this.pieParams.month = month;
        },
        pieParamsYearChanged(year) {
            this.pieParams.year = year;
        },

        barProjectChanged(projects) {
           this.barProjectsParams.projects = projects;
        },
        barCategoryRangeChanged(dates) {

          if (dates.includes("אל")) {
            this.barCategoriesParams.rangeDate = dates;
          }
        }
    },
  created() {

    let startDate = moment(new Date()).startOf('month').format('DD/MM/YYYY');
    let endDate = moment(new Date()).endOf('month').format('DD/MM/YYYY');

    this.dateRange = `${startDate} אל ${endDate} `

    this.barCategoryRangeChanged(this.dateRange);
  }
};
</script>
