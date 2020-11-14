<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <div class="modal-card is-medium animated fastest zoomIn">
            <header class="modal-card-head is-radius-top">
              <template v-if="customer">
                <p class="modal-card-title">פרטי לקוח</p>
                <span class="control tooltip">
<!--               <a @click="statusHistory"  class="button is-success is-small">-->
                  <!--                    <i class="fas fa-history"></i>-->
                  <!--                    <span class="tooltip-text bottom">היסטוריית סטטוסים</span>-->
                  <!--  </a>-->
            </span>
                <span class="control tooltip">
                  <a @click="addEvent"  class="button is-info is-small">
                    <i class="fas fa-comment-dots"></i>
                    <span class="tooltip-text bottom"> התקשרות חדשה</span>
                  </a>
            </span>
                <span class="control tooltip">
                  <a @click="addTask"  class="button is-success is-small">
                    <i class="fas fa-thumbtack"></i>
                    <span class="tooltip-text bottom">  משימה חדשה</span>
                  </a>
            </span>
                <button type="button" class="delete" @click="$router.go(-1)"></button>
              </template>
            </header>
            <section class="modal-card-body is-radius-bottom">
                <loading v-if="loading"></loading>
                <div v-if="!customer">אוי לאיש הקשר הזה אין לקוח זה כנראה באג!</div>
                <div v-else>
                    <table class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
                        <tbody>
                            <tr>
                                <td>שם</td>
                                <td>{{ customer.name }}</td>
                            </tr>
                            <tr>
                                <td>חברה</td>
                                <td>{{ customer.company }}</td>
                            </tr>
                            <tr>
                                <td>אימייל</td>
                                <td>{{ customer.email }}</td>
                            </tr>
                            <tr>
                                <td>טלפון</td>
                                <td>{{ customer.phone }}</td>
                            </tr>
                            <tr>
                                <td>כתובת</td>
                                <td>{{ customer.address }}</td>
                            </tr>
                            <tr>
                                <td>סטטוס</td>
                                <td>

                                  <div class="has-text-centered"
                                       :style="{background: customer.status.length > 0 ? customer.status[0].color: ''}">
                                    {{ customer.status.length > 0 ?customer.status[0].name : ''}}
                                  </div>

                                </td>
                            </tr>

                            <template v-if="customer.attributes">
                                <tr v-for="attr in customer.attributes" :key="attr.slug">
                                    <td>{{ attr.name }}</td>
                                    <td v-if="attr.type == 'datetime' && customer[attr.slug]">
                                        {{ customer[attr.slug]['date'] | formatDate($store.state.settings.ac.dateformat + ' HH:mm') }}
                                    </td>
                                    <td v-else>{{ customer[attr.slug] }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <button class="modal-close is-large is-hidden-mobile" aria-label="close" @click="$router.go(-1)"></button>
      <div v-if="show">
        <task-form-modal :cusId=customerId modal="customers"></task-form-modal>
      </div>

    </div>

</template>

<script>
import TaskFormModal from "../tasks/TaskFormModal";
export default {
    data() {
        return {
            show:false,
            loading: true,
            customer: null,
            customerId: null
        };
    },
    created() {
      this.customerId = this.$route.params.id

      let route = 'app/customers/';
      if(this.$route.name === 'contact') {
         route = 'app/customer/contact/';
      }
        this.$http
            .get(route+`${this.$route.params.id}`)
            .then(res => {
             if( res.data.customer) {
               this.customer = res.data.customer;

             }
              this.loading = false;
            })
            .catch(err => {
                this.$event.fire('appError', err.response);
            });
    },
    methods: {
      addEvent() {

        this.$modal.show("event-form-modal", {customerId: this.$route.params.id});
      },
      addTask() {
        this.show = true;
      },
    },
  components: {
    TaskFormModal
  }
};
</script>
