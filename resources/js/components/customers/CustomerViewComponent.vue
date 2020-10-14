<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <div class="modal-card is-medium animated fastest zoomIn">
            <header class="modal-card-head is-radius-top">
              <template v-if="customer">
                <p class="modal-card-title">פרטי לקוח</p>
                <button type="button" class="delete" @click="$router.go(-1)"></button>
              <p class="control tooltip">
                <a
                    @click="addEvent"
                    class="fas fa-comment-dots is-small button is-info">
                  <span class="tooltip-text bottom"> התקשרות חדשה</span>

                </a>
              </p>
              </template>
            </header>
            <section class="modal-card-body is-radius-bottom">
                <loading v-if="loading"></loading>
                <div v-if="!customer">אוי לאיש הקשר הזה אין לקוח זה כנראה באג!</div>
                <div v-else>
                    <table class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
                        <tbody>
                            <tr>
                                <td>Name</td>
                                <td>{{ customer.name }}</td>
                            </tr>
                            <tr>
                                <td>Company</td>
                                <td>{{ customer.company }}</td>
                            </tr>
                            <tr>
                                <td>email</td>
                                <td>{{ customer.email }}</td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td>{{ customer.phone }}</td>
                            </tr>
                            <tr>
                                <td>Address</td>
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
    </div>
</template>

<script>
export default {
    data() {
        return {
            loading: true,
            customer: null,
        };
    },
    created() {

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
    },
};
</script>
