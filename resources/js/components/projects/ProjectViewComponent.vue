<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <div class="modal-card is-medium animated fastest zoomIn">
      <header class="modal-card-head is-radius-top">
        <p class="modal-card-title">פרטי הפרוייקט</p>
        <button type="button" class="delete" @click="$router.go(-1)"></button>
      </header>
      <section class="modal-card-body is-radius-bottom">
        <loading v-if="loading"></loading>
        <div v-else>

          <table
              class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
            <tbody>
            <tr>
              <td>לקוח</td>
              <td>{{ project.customer ? project.customer.name : ''}}</td>
            </tr>
            <tr>
              <td>נושא</td>
              <td>{{ project.name }}</td>
            </tr>
            <tr>
              <td>מחיר</td>
              <td>{{ project.price }}</td>
            </tr>
            <tr>
              <td>הוצאות</td>
              <td>{{ project.expenses }}</td>
            </tr>
            <tr>
              <td>אחוז מהעבודה עד כה</td>
              <td>{{ percentageDone }}</td>
            </tr>
            <tr>
              <td>סוג</td>
              <td>{{ type }}</td>
            </tr>
            <tr>
              <td>תאריך התחלה</td>
              <td>
                <date-format-component :date=" project.start_date"></date-format-component>
              </td>
            </tr>
            <tr>
              <td>תאריך סיום</td>
              <td>
                <date-format-component :date=" project.end_date"></date-format-component>
              </td>
            </tr>

            </tbody>
          </table>
        </div>
      </section>
    </div>
    <button class="modal-close is-large is-hidden-mobile" aria-label="close" @click="$router.go(-1)"></button>
  </div>
</template>

<script>
import DateFormatComponent from '../helpers/DateFormatComponent'

export default {
  data () {
    return {
      loading: true,
      project: null,
      type: '',
      percentageDone: ''
    }
  },
  created () {
    this.$http
        .get(`app/projects/${this.$route.params.id}`)
        .then(res => {

          this.project = res.data.project;
          this.type = res.data.type[0].name;
          this.loading = false
          this.percentageDone = res.data.percentageDone + '%'
        })
        .catch(err => {
          this.$event.fire('appError', err.response)
        })

  },
  methods: {
    showTaxes (taxes) {
      return taxes.map(tax => '<span class="tag">' + tax.name + '</span>').join(' ')
    },
    bar: function(tasksTime, price) {
     console.log(tasksTime)
     return  this.$root.$refs.ProjectListComponent.percentageCalculation(tasksTime, price);
    }
  },
  components: { DateFormatComponent },
}
</script>
