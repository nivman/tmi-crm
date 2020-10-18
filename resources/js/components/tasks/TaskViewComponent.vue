<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <div class="modal-card is-medium animated fastest zoomIn">
      <header class="modal-card-head is-radius-top">
        <p class="modal-card-title">Product Details</p>
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
              <td>{{ task.customer ? task.customer.name : ''}}</td>
            </tr>
            <tr>
              <td>נושא</td>
              <td>{{ task.name }}</td>
            </tr>
            <tr>
              <td>תאריך יצירה</td>
              <td>
                <date-format-component :date=" task.created_at"></date-format-component>
              </td>
            </tr>
            <tr>
              <td>תאריך התחלה</td>
              <td>
                <date-format-component :date=" task.start_date"></date-format-component>
              </td>
            </tr>
            <tr>
              <td>תאריך סיום</td>
              <td>
                <date-format-component :date=" task.end_date"></date-format-component>
              </td>
            </tr>
            <tr>
              <td>סטטוס</td>
              <td>
                <div class="has-text-centered"
                     :style="{background: task.status.length > 0 ? task.status[0].color: ''}">
                  {{ task.status.length > 0 ? task.status[0].name : ''}}
                </div>
              </td>
            </tr>
            <tr>
              <td>עדיפות</td>
              <td>
                <div class="has-text-centered"
                     :style="{background: task.priority.length > 0 ? task.priority[0].color: ''}">
                  {{ task.priority.length > 0 ? task.priority[0].name : ''}}
                </div>
              </td>
            </tr>
            <tr>
              <td>תוכן</td>
              <td>
                {{task.details}}
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
      task: null,
    }
  },
  created () {
    this.$http
        .get(`app/tasks/${this.$route.params.id}`)
        .then(res => {
          this.task = res.data.task
          this.loading = false
        })
        .catch(err => {
          this.$event.fire('appError', err.response)
        })
  },
  methods: {
    showTaxes (taxes) {
      return taxes.map(tax => '<span class="tag">' + tax.name + '</span>').join(' ')
    },
  },
  components: { DateFormatComponent },
}
</script>
