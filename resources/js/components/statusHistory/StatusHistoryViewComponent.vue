<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <div class="modal-card is-medium animated fastest zoomIn">
      <header class="modal-card-head is-radius-top">
        <p class="modal-card-title">
          {{title}}
        </p>

        <button type="button" class="delete" @click="goBack"></button>

      </header>
      <section class="modal-card-body is-radius-bottom">
        <table class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
          <tbody>
          <tr v-for="status in statuses" :key="status.updated_at">
            <td>
              <date-format-component :dateTime="status.updated_at "></date-format-component>
             </td>
            <td>
              <div class="has-text-centered" :style="{background: status.color}">
                {{ status.name}}
              </div>
            </td>
          </tr>
          </tbody>
        </table>
      </section>
    </div>
  </div>

</template>

<script>
import DateFormatComponent from '../helpers/DateFormatComponent'
export default {
  props:['entityType', 'entityId', 'entityName'],
  data() {
    return {
      statuses: [],
      title : ''
    };
  },
  created() {

    this.$http
        .get(`app/history/status/${this.entityType}/${this.entityId}`)
        .then(res => {
          console.log(this.entityName)
          this.setHistoryTitle(this.entityName, this.entityType)
          this.statuses = res.data;
        })
        .catch(err =>
            this.$event.fire("appError", err.response)
        );

  },
  methods: {
    goBack() {
       this.$emit("showStatusesHistory", true);
    },

    setHistoryTitle(entityName, entityType) {
      let title = 'היסטוריית סטטוסים '
      if (entityType === 'customer') {
        this.title = title + ' '+ ' ללקוח: '+ entityName  ;
      }
    },
  },
  components: { DateFormatComponent},
};
</script>
