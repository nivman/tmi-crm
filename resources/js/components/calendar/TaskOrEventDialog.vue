<template>
  <modal
      name="task-or-event-dialog"
      height="auto"
      :scrollable="true"
      width="300"
      :adaptive="true"
      transition="custom"
      @before-open="beforeOpen">

  </modal>
</template>

<script>
import TaskFormModal from "../tasks/TaskFormModal";

export default {
  props:['calendarDates'],
  data() {
    return {
      showTask: false

    }
  },
methods: {
  beforeOpen(calendarDates) {
    this.$modal.show("dialog", {
      title: "?האם להוסיף משימה או התקשרות",
      class: "right-title",
      buttons: [
        {
          title: "התקשרות",
          class: "button is-danger is-radiusless is-radius-bottom-left",
          handler: () => {

            this.$modal.hide("dialog");
            this.$modal.hide("task-or-event-dialog");
            this.$modal.show("event-form-modal", calendarDates);
          }
        },
        {
          title: "משימה",
          class: "button is-warning is-radiusless is-radius-bottom-right",
          handler: () => {
            console.log(calendarDates)
            let startDate = calendarDates.params.startStr;
            let endDate = calendarDates.params.endStr;
            this.$modal.hide("dialog");
            this.$modal.hide("task-or-event-dialog");
            this.$router.push({path: `/tasks/add?calendarDates=calendarDates&startDate=${startDate}&endDate=${endDate}`})
          }
        }
      ]
    });

  },

},
  components: {TaskFormModal},
}
</script>

<style scoped>
.pull-right{
  text-align: right;
}
</style>

