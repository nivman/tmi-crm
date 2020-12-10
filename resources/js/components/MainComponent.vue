<template>
  <div>
    <top-bar-component></top-bar-component>
    <nav-bar-component v-if="$store.getters.settings.ac.navPosition == 'top' && user"></nav-bar-component>
    <div class="is-app" :class="{ topMenu: $store.getters.settings.ac.navPosition == 'top' }"  ref="container">
      <div class="container">
        <div class="is-app"
             :class="{ 'hide-side-bar': toggleSideBar || $store.getters.settings.ac.navPosition == 'top' }">
          <transition
              appear
              name="fade"
              mode="out-in"
              enter-active-class="animated faster fadeInRight"
              leave-active-class="animated fastest fadeOutLeft"
          >
            <div class="is-sidebar is-shadow">
              <side-bar-component></side-bar-component>
            </div>
          </transition>
          <div class="is-main">
            <transition
                appear
                name="fade"
                mode="out-in"
                enter-active-class="animated faster fadeInDown"
                leave-active-class="animated fastest fadeOutRight"
            >
              <router-view></router-view>
            </transition>
            <footer-component v-if="user"></footer-component>
          </div>
          <div class="is-clearfix"></div>
        </div>
      </div>
    </div>
    <login-modal></login-modal>
    <modals-container></modals-container>
    <vue-progress-bar></vue-progress-bar>
    <notifications group="app" classes="notification"/>
    <event-form-modal></event-form-modal>

  </div>
</template>

<script>
import LoginModal from '../core/LoginModal.vue';
import TopBarComponent from './TopBarComponent.vue';
import NavBarComponent from './NavBarComponent.vue';
import FooterComponent from './FooterComponent.vue';
import SideBarComponent from './SideBarComponent.vue';
import EventFormModal from "./calendar/EventFormModal.vue";
import CustomerFormComponent from "./customers/CustomerFormComponent";
import TaskFormModal from "./tasks/TaskFormModal";
import Noty from 'noty';

export default {
  data() {
    return {
      firstEnter: false,
      popupCustomerId: '',
      popupTaskId:''
    }
  },
  components: {
    LoginModal,
    TopBarComponent,
    NavBarComponent,
    SideBarComponent,
    FooterComponent,
    EventFormModal,
    CustomerFormComponent,
    TaskFormModal,
    Noty

  },
  computed: {
    menu() {
      return this.$store.state.sideBar;
    },
    user() {
      return this.$store.getters.user ? true : false;
    },
    toggleSideBar() {
      return this.$store.getters.user ? this.$store.state.sideBar : true;
    },
  },
  mounted() {
    this.firstEnter =  localStorage.getItem("firstEnter");

    if (!this.$store.getters.user) {
      this.$router.push({path: '/login'});
    }else{
      this.checkForUnSeenPopUps();

    }
  },
  beforeMount() {

    if (this.$store.getters.admin) {
      if (this.$route.meta.super && !this.$route.meta.admin) {
        this.$Progress.fail();
        this.notify('warn', 'You are not allowed to access the requested resource.', 'Access denied!');
        this.$router.push('/');
      }
    } else if (this.$store.getters.customer && this.$store.getters.vendor) {
      if (!this.$route.meta.customer && !this.$route.meta.vendor) {
        this.$Progress.fail();
        this.notify('warn', 'You are not allowed to access the requested resource.', 'Access denied!');
        this.$router.push('/');
      }
    } else if (this.$store.getters.customer && !this.$store.getters.vendor) {
      if (!this.$route.meta.customer) {
        this.$Progress.fail();
        this.notify('warn', 'You are not allowed to access the requested resource.', 'Access denied!');
        this.$router.push('/');
      }
    } else if (this.$store.getters.vendor && !this.$store.getters.customer) {
      if (!this.$route.meta.vendor) {
        this.$Progress.fail();
        this.notify('warn', 'You are not allowed to access the requested resource.', 'Access denied!');
        this.$router.push('/');
      }
    } else {
      if (this.$route.meta.super || this.$route.meta.admin) {
        this.$Progress.fail();
        this.notify('warn', 'You are not allowed to access the requested resource.', 'Access denied!');
        this.$router.push('/');
      }
    }
  },
  created() {
    setTimeout(() => {
      if (window.innerWidth <= 1023) {
        this.$store.commit('TOGGLE_SIDEBAR', true);
      }
    }, 5);
    this.$event.listen('logOut', () => {
      this.$http.get('/logout').then(() => {
        this.$store.commit('UPDATE_USER', null);
        this.$router.push({path: '/login'});
      });
    });
    this.$event.listen('appError', res => {
      if (res.status) {
        if (res.status == 401) {
          this.login();
        } else if (res.status == 403) {
          if (!this.$store.getters.user) {
            this.notify('warn', 'You are not allowed to access the requested resource.', 'Access denied!');
            this.$router.push({path: '/login'});
          } else {
            this.redirectBack();
          }
        } else if (res.status == 419) {
          this.refreshPage();
        } else if (res.status == 422) {
          this.displayErrors(res.data);
        } else {
          let error = res.data.error
              ? res.data.error
              : res.data.message
                  ? res.data.message
                  : 'Unknown error occurred! please try again later.';
          let nf = "We are sorry but the requested page can't be found.";
          let ua = 'Your session has been expired! Please login again';
          this.$modal.show('dialog', {
            title: res.status + ' ' + res.statusText,
            text: res.status == 401 ? ua : res.status == 404 ? nf : error,
            buttons: [
              {
                title: 'OK',
                handler: () => {
                  this.$modal.hide('dialog');
                },
              },
            ],
          });
        }
      } else {
        console.error(res);
      }
    });
    this.$event.listen('missingData', (res) => {

      this.$modal.show('dialog', {
        title: '<div style="text-align: center">' + res + '</div>',
        buttons: [
          {
            title: 'OK',
            handler: () => {
              this.$modal.hide('dialog');
            },
          },
        ],
      });
    });
  },
  watch: {
    $route(to, from) {
      if (window && to.meta.modal) {
        document.body.classList.add('modal-open');
      }
      if (window && from.meta.modal) {
        document.body.classList.remove('modal-open');
      }
    },
    isMobile: function (v) {
      this.$store.commit('TOGGLE_SIDEBAR', v);
    },
  },
  methods: {
    getUnseenLeads() {
      Echo.channel('email-rec').listen('EmailEvent', (e) => {
            this.showEmailPopup(e.email, [])
        })
    },
    taskNotification() {
      Echo.channel('task-notification').listen('TaskEvent', (e) => {

       this.showTaskPopup(e.task, [])
      })
    },
    checkForUnSeenPopUps() {

      this.$http
          .get(`app/customers/leads/unseen`)
          .then((res) => {
            let unseenLeads = res.data;
            unseenLeads.forEach((lead)=>{
                this.showEmailPopup(lead,unseenLeads)
            })
            this.getUnseenLeads();
            this.taskNotification();
          })
          .catch(err => this.$event.fire("appError", err.response))

      this.$http
          .get(`app/tasks/unseen`)
          .then((res) => {

            res.data.forEach((e)=>{
              this.showTaskPopup(e, [])
            })
          })
          .catch(err => this.$event.fire("appError", err.response))
      this.firstEnter = false

    },
    showTaskPopup(task) {
      let em = this;
      Noty.setMaxVisible(10);
      let noty = new Noty({
        type: 'info',
        // sounds:{sources : "/var/www/html/tmi/storage/app/public/sound/kitchen"},

        closeWith: ['click'],
        text: '<stronge style="font-size: 18px;font-weight: bold">יש משימה לעשות</stronge>' +
            '<p style="font-size: 14px;font-weight: bold"> כותרת: '+task.name+' </p>',
        buttons: [
          Noty.button('לצפייה במשימה', 'button', function (e) {
            em.zeroTaskNotification(task.id)
            em.$root.$router.push({path : `/tasks/edit/${task.id}`})

            noty.close()
          }, {'data-status': 'ok'}),
        ]
      }).show();
    },
    showEmailPopup(ev, unseenLeads) {

      let em = this;
      Noty.setMaxVisible(10);

      let noty = new Noty({
        type: 'success',
        // sounds:{sources : "/var/www/html/tmi/storage/app/public/sound/kitchen"},

        closeWith: ['click'],
        text: '<stronge style="font-size: 18px;font-weight: bold">ליד חדש</stronge>' +
            '<p style="font-size: 14px;font-weight: bold"> אמייל: '+ev.email+' </p>'+
            '<p style="font-size: 14px;font-weight: bold"> טלפון: '+ev.phone+' </p>',
        buttons: [
          Noty.button('לעריכת הליד', 'button', function (e) {

            em.popupCustomerId = ev.customer_id;
            var ComponentClass = Vue.extend(CustomerFormComponent)

            var instance = new ComponentClass({
              propsData: { popupCustomerId: ev.customer_id }
            })
            instance.$mount()
            em.$refs.container.appendChild(instance.$el)
            noty.close()
          }, {id: 'button1', 'data-status': 'ok'}),
        ]
      }).show();
    },
    zeroTaskNotification(taskId) {
      this.$http
          .get(`app/tasks/cancel-notification/${taskId}`)
          .then(res => {

          })
          .catch(err => this.$event.fire('appError', err.response));
    },
    login() {
      this.$modal.hide('dialog');
      this.$modal.show('login-modal');
    },
    displayErrors(err) {
      this.$modal.hide('dialog');
      let errors = err.errors ? Object.values(err.errors) : [err.message];
      this.$modal.show('dialog', {
        text: '<span class="has-text-danger">' + errors.join('<br>') + '</span>',
        buttons: [
          {
            title: 'OK',
            handler: () => {
              this.$modal.hide('dialog');
            },
          },
        ],
      });
      // this.notify('error', errors.join('<br>'));
    },
    redirectBack() {
      this.$modal.hide('dialog');
      this.notify('warn', 'You are not allowed to access the requested resource.', 'Access denied!');
      this.$router.push('/');
    },
    refreshPage() {
      this.$modal.hide('dialog');
      this.$modal.show('dialog', {
        title: '419 סטטוס לא ידוע',
        text: 'פג תוקף אסימון הדף! אנא לחצו על אישור כדי לרענן ואז נסו שוב',
        buttons: [
          {
            title: 'OK',
            handler: () => {
              this.$modal.hide('dialog');
              this.refreshToken();
            },
          },
        ],
      });
    },
    refreshToken() {
      this.$http
          .get(`app/token`)
          .then(res => {
            document.head.querySelector('meta[name="csrf-token"]').setAttribute('content', res.data.token);
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = res.data.token;
          })
          .catch(err => this.$event.fire('appError', err.response));
    },
  },
};
</script>

<style lang="scss">
@import '../../sass/variables';

.noty-button {
  width: 100%;
}

.notification {
  padding: 10px;
  color: #ffffff;
  font-size: 14px;
  margin: 10px 10px 0 0;
  background: hsl(204, 86%, 53%);
  border-radius: 3px;
  border-left: 4px solid darken(hsl(204, 86%, 53%), 10%);

  &.warn {
    color: hsl(0, 0%, 29%);
    background: hsl(48, 100%, 67%);
    border-left-color: darken(hsl(48, 100%, 67%), 20%);
  }

  &.error {
    background: hsl(348, 100%, 61%);
    border-left-color: darken(hsl(348, 100%, 61%), 20%);
  }

  &.success {
    background: hsl(141, 71%, 48%);
    border-left-color: darken(hsl(141, 71%, 48%), 10%);
  }
}

.is-responsive,
.table-responsive {
  @include mobile {
    & {
      width: 100%;
      display: block;
      border-spacing: 0;
      position: relative;
      overflow-x: scroll;
      border-collapse: collapse;

      td:empty:before {
        content: '\00a0';
      }

      th,
      td {
        margin: 0;
        border: 0;
        vertical-align: top;
        min-height: 38px !important;
        text-align: left !important;

        div {
          text-align: left !important;
        }

        .buttons.has-addons {
          padding: 0 0.75em;
          justify-content: flex-start !important;
        }
      }

      th:last-child {
        padding: 0.5em 0.75em !important;
      }

      thead {
        border-right: solid 2px $white-ter;
        display: block;
        float: left;
        padding-right: 5px;

        tr span:first-child {
          margin-right: 0;
        }

        th,
        td {
          background-color: $white !important;
        }
      }

      tr {
        display: block;
        padding: 0 10px 0 0;

        th::before {
          content: '\00a0';
        }
      }

      tbody {
        display: block;
        width: auto;
        position: relative;
        // overflow-x: auto !important;
        white-space: nowrap;
        margin-left: 120px;
      }

      tr {
        display: inline-block;
        vertical-align: top;
      }

      th {
        display: block;
        text-align: right;

        span:last-child {
          margin-right: -20px;
        }
      }

      td {
        display: block;
        min-height: 1.25em;
        text-align: left;
      }
    }
  }
}

.input-filter {
  border: 0;
  background-color: transparent;

  &:focus {
    box-shadow: none;
  }
}

.table {
  th,
  td {
    vertical-align: middle;
  }

  thead th,
  thead th,
  .is-active th,
  .is-active td {
    background-color: #f5f5f5;

    &.filter {
      padding: 0;
    }
  }

  &.is-rounded {
    border-spacing: 0;
    border-collapse: inherit;

    th,
    td {
      vertical-align: middle !important;
    }

    thead tr:first-child th:first-child,
    tbody:first-child tr:first-child td:first-child {
      border-top-left-radius: $radius;
    }

    thead tr:first-child th:last-child,
    tbody:first-child tr:first-child td:last-child {
      border-top-right-radius: $radius;
    }

    thead + tbody tr:last-child td:first-child,
    tfoot tr:last-child td:first-child {
      border-bottom-left-radius: $radius;
    }

    thead + tbody tr:last-child td:last-child,
    tfoot tr:last-child td:last-child {
      border-bottom-right-radius: $radius;
    }

    thead + tbody tr td,
    tfoot + tbody tr td,
    tfoot tr td,
    tbody + tbody tr td,
    tr + tr td {
      border-top: 0 !important;
    }

    tr th + th,
    tr td + td {
      border-left: 0 !important;
    }
  }
}

.table-body-br .table.is-rounded {
  tbody tr:last-child td:first-child {
    border-bottom-left-radius: 0;
  }

  tbody tr:last-child td:last-child {
    border-bottom-right-radius: 0;
  }
}

.table-head-br .table.is-rounded {
  thead tr:first-child th:first-child {
    border-top-left-radius: 0;
  }

  thead tr:first-child th:last-child {
    border-top-right-radius: 0;
  }
}

table.is-rounded-body {
  tbody tr:first-child td:first-child {
    border-top-left-radius: $radius;
  }

  tbody tr:first-child td:last-child {
    border-top-right-radius: $radius;
  }

  tbody tr:last-child td:first-child {
    border-bottom-left-radius: $radius;
  }

  tbody tr:last-child td:last-child {
    border-bottom-right-radius: $radius;
  }
}

.pagination.is-gapless {
  margin: 0 !important;

  li + li .pagination-link {
    border-left-width: 0;
  }

  .pagination-link {
    margin: 0;
    border-radius: 0;
    border-color: $grey-lighter;

    &:hover {
      background-color: $blue;
      border-color: $blue;
      color: $white;
    }
  }

  .pagination-list {
    li:first-child .pagination-link {
      border-radius: $radius 0 0 $radius;
    }

    li:last-child .pagination-link {
      border-radius: 0 $radius $radius 0;
    }
  }
}

.VueTables {
  width: 100%;

  div:first-child div:first-child {
    display: flex;
    flex-direction: row-reverse;
    justify-content: space-between;

    div {
      flex-direction: row !important;
    }
  }

  label {
    line-height: 36px;
    float: left;
    margin-bottom: 0 !important;
    margin-right: 0.5rem;
    font-weight: normal;
  }

  .field {
    min-width: 50%;
    margin-bottom: 0;

    &:first-child {
      justify-content: flex-end !important;
    }
  }

  .VueTables__sortable {
    .VueTables__sort-icon {
      margin-top: 0.25rem;
    }
  }

  .VueTables__table .form-group {
    margin-bottom: 0.5rem;
  }

  .VueTables__dropdown-pagination {
    float: right;
    margin-left: 0.5rem;
    @include mobile {
      & {
        float: none;
        text-align: center;
      }
    }
  }

  @include mobile {
    & {
      font-size: 0.9rem;

      .label {
        font-size: 0.9rem;
      }

      .field {
        min-width: auto !important;
        // justify-content: space-between;
      }

      .VueTables__search .input {
        width: 100px;
        font-size: 0.9rem;
        padding: $control-padding-vertical $control-padding-horizontal;
      }

      .VueTables__limit-field label {
        display: none;
      }
    }
  }

  .VueTables__pagination-wrapper {
    float: right;

    .VueTables__dropdown-pagination {
      min-width: 100px !important;
      @include mobile {
        & {
          float: none !important;
          min-width: 100% !important;

          .VueTables__dropdown-pagination {
            min-width: 100% !important;
          }
        }
      }
    }
  }

  .VuePagination {
    display: block;
    min-height: 38px;
    margin: 0 !important;
    @include mobile {
      & {
        .pagination-list {
          display: none;
        }
      }
    }

    li + li .pagination-link {
      border-left-width: 0;
    }

    .pagination-link {
      margin: 0;
      border-radius: 0;
      border-color: $grey-lighter;

      &:hover {
        background-color: $blue;
        border-color: $blue;
        color: $white;
      }
    }

    .pagination-list {
      li:first-child .pagination-link {
        border-radius: $radius 0 0 $radius;
      }

      li:last-child .pagination-link {
        border-radius: 0 $radius $radius 0;
      }
    }

    @include mobile {
      li {
        flex-grow: 0;
      }
      .pagination-link {
        min-width: 1.75rem !important;
        height: 1.75rem;
      }
      .VuePagination__pagination,
      .VuePagination__count {
        float: none !important;
        margin-left: auto;
        margin-right: auto;
      }
      .VuePagination__count {
        width: 100%;
        text-align: left !important;
      }
    }

    .VuePagination__count {
      margin-top: 0.45rem;
      float: left;
      margin-bottom: 0;
    }

    .VuePagination__pagination {
      float: right;
      margin-bottom: 0;
    }

    .pagination-link.disabled {
      background-color: $light;
      color: $grey-light;
      cursor: default;

      &:hover {
        border-color: $grey-lighter;
        box-shadow: none;
      }
    }

    .pagination-link.active,
    .pagination-link.is-current {
      background-color: $blue;
      border-color: $blue;
      color: $white;
      cursor: default;

      &:hover {
        box-shadow: none;
      }
    }
  }
}

.tooltip {
  position: relative;
  display: inline-block;
  height: 100%;

  .tooltip-text {
    visibility: hidden;
    background-color: rgba(0, 0, 0, 0.9);
    color: #fff;
    text-align: center;
    padding: 5px 10px;
    border-radius: 3px;
    position: absolute;
    bottom: 120%;
    z-index: 999;
    font-weight: bold;
    font-size: 13px;

    &::after {
      content: ' ';
      top: 100%;
      left: 50%;
      margin-left: -5px;
      border-width: 5px;
      position: absolute;
      border-style: solid;
      border-color: rgba(0, 0, 0, 0.9) transparent transparent transparent;
    }

    &.bottom {
      bottom: auto;
      top: 100%;

      &::after {
        border-width: 0;
      }
    }

    &.left {
      bottom: auto;
      top: -12px;
      right: 110%;

      &::after {
        border-width: 0;
      }
    }
  }

  &:hover .tooltip-text {
    visibility: visible;
  }
}

@media print {
  html,
  body,
  .wrapper {
    margin: 0 !important;
    padding: 0 !important;
  }
  .navbar,
  .is-sidebar,
  footer {
    display: none !important;
  }
  .is-main {
    margin: 0 !important;
    width: 100% !important;
    box-shadow: none !important;

    .print {
      .print-none {
        display: none !important;
      }

      .p-m-none {
        margin: 0 !important;
      }

      .p-p-none {
        padding: 0 !important;
      }

      .p-b-none {
        border: 0 !important;
      }
    }

    * {
      border-color: #ddd !important;
    }
  }
}

.no-caret::after {
  display: none;
}
</style>
