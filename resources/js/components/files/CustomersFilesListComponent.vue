<template>
  <div class="modal is-active">
    <div class="modal-background"></div>
    <div class="modal-card animated fastest zoomIn">
      <header class="modal-card-head is-radius-top">

        <p class="modal-card-title">
        רשימת קבצים
        </p>

        <button type="button" class="delete" @click="goBack"></button>

      </header>
      <section class="modal-card-body is-radius-bottom">
        <table  ref="filesTable" class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
          <thead style="  text-align: center;">
          <tr>
            <th>שם</th>
            <th>תאריך העלה</th>
            <th>מחיקה</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="file in files" :key="file.name">
            <td>
              <button class="button is-link" @click="downloadFile(file.id, file.name)">{{file.name}}</button>

            </td>
            <td>
              <date-format-component :dateTime="file.created_at"></date-format-component>
            </td>
            <td style="  text-align: center;">
              <button class="delete" @click="deleteRecord(file.id)"></button>
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
  props:['customerId'],
  data() {
    return {
      showNewContactForm: false,
      files: [],
      title : ''
    };
  },
  created() {

    this.$http
        .get(`app/files/customers/${this.customerId}`)
        .then(res => {

          this.files = res.data;
        })
        .catch(err =>
            this.$event.fire("appError", err.response)
        );

  },
  methods: {
    goBack() {
      this.$emit("showFiles", true);
    },
    downloadFile(id, name) {
      axios
          .get(`app/files/download?file=${id}&customer=${this.customerId}`, {
            responseType: 'arraybuffer'
          })
          .then(response => {

            let blob = new Blob([response.data], { type: 'application/text'}),
            url = window.URL.createObjectURL(blob)
            var anchor = document.createElement("a");
            anchor.download = name;
            anchor.href = url;
            anchor.click();


          })

    },
    deleteFile(id) {

      this.$http
          .get(`app/files/delete/${id}`)
          .then(res => {

            if (res.status === 204) {
              const index = this.files.findIndex(file => file.id === id)
              if (~index){
                this.files.splice(index, 1)
              }
              this.notify(
                  "success",
                  "קובץ נמחק "
              );
            }

          })
          .catch(err =>
              this.$event.fire("appError", err.response)
          );
    },
    deleteRecord(id) {
      this.$modal.show("dialog", {
        title: "למחוק " + name + "!",
        text: "הפעולה תמחק את הרשומה ללא אפשרות שיחזור.",
        buttons: [
          {
            title: "כן למחוק",
            class: "button is-danger is-radiusless is-radius-bottom-left",
            handler: () => {
              this.deleteFile(id)
              this.$modal.hide("dialog");
            }
          },
          {title: "ביטול", class: "button is-warning is-radiusless is-radius-bottom-right"}
        ]
      });
    }
  },
  components: { DateFormatComponent},
};
</script>
<style scoped>
.button-add{
  color: rgba(10, 10, 10, 0.2);

  border: none;
  border-radius: 290486px;
  cursor: pointer;
  pointer-events: auto;
  display: inline-block;
  flex-grow: 0;
  flex-shrink: 0;
  font-size: 20px;
  height: 20px;
  max-height: 20px;
  max-width: 20px;
  min-height: 20px;
  min-width: 20px;
  outline: none;
  position: relative;
  width: 20px;
  margin-left: 5px;

}

.delete::before {
  height: 63%;
  width: 16%;
}

.modal-close::after,
.delete::after {
  height: 16%;
  width: 63%;
}
</style>