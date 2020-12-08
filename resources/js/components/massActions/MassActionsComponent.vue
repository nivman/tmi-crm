<template>
  <div class="main-mass-action field is-grouped is-grouped-multiline">
    <div class="control mass-action-control ">
      <div class="select">

        <select class="button" v-model="selectAction" @change="setAction">
          <option value="delete">מחיקה</option>
          <option value="status">שינוי סטטוס</option>
        </select>
      </div>
      <div class="select" v-if="selectAction == 'status'">

        <select class="button" v-model="statusesAction" @change="setAction">
          <option v-for="item in statuses" :value="item.id" :key="item.id">
            {{ item.name }}
          </option>
        </select>
      </div>
    </div>
    <div class="control mass-action-control">
    <button class="button is-info" id="submit-mass-action" @click="submit">אישור</button>
    </div>
  </div>

</template>

<script>
import Velocity from "velocity-animate";

export default {
  props: [
    'entitiesIds',
    'entity'
  ],
  data() {
    return {
      selectAction: '',
      statusesAction: '',
      statuses: []

    }
  },
  methods: {
    submit() {

      if (!this.selectAction) {
        alert('לא נבחרה פעולה')
        return
      }

      this.$http
          .post('app/massActions/',
         {'entity': this.entity, 'action': this.selectAction, 'ids': this.entitiesIds, 'statusId' :this.statusesAction})
          .then(res => {
            let refreshTable = `refresh${this.entity}sTable`
            this.$event.fire(refreshTable)
            this.$emit('clicked')
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
    },
    setAction(){
      switch(this.selectAction) {
        case 'status':
          this.getEntityStatuses();
          return;
      }

    },
    getEntityStatuses(){
      this.$http
          .get(`app/massActions/status/${this.entity}` )
          .then(res => {
            console.log(res.data)
            this.statuses = res.data
            // let refreshTable = `refresh${this.entity}Table`
            // this.$event.fire(refreshTable)
          })
          .catch(err => {
            this.$event.fire('appError', err.response)
          })
      console.log(this.entity)
    },

  }
}
</script>

<style scoped>
.main-mass-action {
  background: #00b89c;
  bottom: 10px;
  width: 86%;
  z-index: 1111;
  height: 50px;
  position: fixed;
  margin-bottom: -10px;
}
.mass-action-control {
  margin: 0.30rem;
}

</style>
