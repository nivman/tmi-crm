<template>
  <div>
    <aside class="menu app-sidebar animated fastest fadeInRight">
      <ul class="menu-list" v-if="!$store.getters.customer && !$store.getters.vendor">
        <li>
          <router-link to="/" exact @click.native="hideMenu">
                    <span class="icon is-small m-l-sm">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                    </span>
            עמוד הבית
          </router-link>
        </li>
        <li>
          <router-link to="/calendar" exact @click.native="hideMenu">
                    <span class="icon is-small m-l-sm">
                        <i class="fas fa-fw fa-calendar-alt"></i>
                    </span>
            יומן
          </router-link>
        </li>
        <side-bar-menu-component :expand="subIsActive(['/invoices', '/recurrings'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-file-invoice"></i>
                </span>
          חשבוניות
          <template slot="submenu">
            <li>
              <router-link to="/invoices" exact @click.native="hideMenu">רשימת חשבוניות</router-link>
            </li>
            <li>
              <router-link to="/invoices/add" @click.native="hideMenu">חשבונית חדשה</router-link>
            </li>
            <li>
              <router-link to="/recurrings" exact @click.native="hideMenu">רשימת חשבוניות חוזרות</router-link>
            </li>
            <li>
              <router-link to="/recurrings/add" @click.native="hideMenu">חשבונית חוזרת חדשה</router-link>
            </li>
          </template>
        </side-bar-menu-component>

        <side-bar-menu-component :expand="subIsActive(['/purchases'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-file-invoice-dollar"></i>
                </span>
          רכישות
          <template slot="submenu">
            <li>
              <router-link to="/purchases" exact @click.native="hideMenu">רשימת רכישות</router-link>
            </li>
            <li>
              <router-link to="/purchases/add" @click.native="hideMenu">רכישה חדשה</router-link>
            </li>
          </template>
        </side-bar-menu-component>

        <side-bar-menu-component :expand="subIsActive(['/products'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-cubes"></i>
                </span>
          מוצרים
          <template slot="submenu">
            <li>
              <router-link to="/products" exact @click.native="hideMenu">רשימת מוצרים</router-link>
            </li>
            <li>
              <router-link to="/products/add" @click.native="hideMenu">הוספת מוצר</router-link>
            </li>
          </template>
        </side-bar-menu-component>

        <side-bar-menu-component :expand="subIsActive(['/customers'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-users"></i>
                </span>
          לקוחות
          <template slot="submenu">
            <li>
              <router-link to="/customers" exact @click.native="hideMenu">רשימת לקוחות</router-link>
            </li>
            <li>
              <router-link to="/customers/add" @click.native="hideMenu">הוספת לקוח חדש</router-link>
            </li>
            <li>
              <router-link to="/contacts" @click.native="hideMenu">רשימת אנשי קשר</router-link>
            </li>
            <li>
              <router-link to="/contacts/add" @click.native="hideMenu">הוספת איש קשר חדש</router-link>
            </li>
          </template>
        </side-bar-menu-component>
        <side-bar-menu-component :expand="subIsActive(['/leads'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-person-booth"></i>
                </span>
          לידים
          <template slot="submenu">
            <li>
              <router-link to="/leads" exact @click.native="hideMenu">רשימת לידים</router-link>
            </li>
            <li>
              <router-link to="/leads/add" @click.native="hideMenu">הוספת ליד חדש</router-link>
            </li>
          </template>
        </side-bar-menu-component>
        <side-bar-menu-component :expand="subIsActive(['/events'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-handshake"></i>
                </span>
          התקשרויות
          <template slot="submenu">
            <li>
              <router-link to="/events/eventsList" exact @click.native="hideMenu">רשימת התקשרויות</router-link>
            </li>
            <li>
              <a
                  @click="addEvent"
              >יצירת התקשרות
              </a>

            </li>
          </template>
        </side-bar-menu-component>
        <side-bar-menu-component :expand="subIsActive(['/tasks'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-tasks"></i>
                </span>
          משימות
          <template slot="submenu">
            <li>
              <router-link to="/tasks" exact @click.native="hideMenu">רשימת משימות</router-link>
            </li>
            <li>
            <li>
              <router-link to="/tasks/add" @click.native="hideMenu">יצירת משימה</router-link>
            </li>
          </template>
        </side-bar-menu-component>
        <side-bar-menu-component :expand="subIsActive(['/projects'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-project-diagram"></i>
                </span>
          פרוייקטים
          <template slot="submenu">
            <li>
              <router-link to="/projects" exact @click.native="hideMenu">רשימת פרוייקטים</router-link>
            </li>
            <li>
            <li>
              <router-link to="/projects/add" @click.native="hideMenu">יצירת פרוייקט</router-link>
            </li>
            <li>
              <router-link to="/upsales" exact @click.native="hideMenu">רשימת מכירות נוספות</router-link>
            </li>
            <li>
            <li>
              <router-link to="/upsales/add" @click.native="hideMenu">יצירת מכירה נוספת</router-link>
            </li>
          </template>
        </side-bar-menu-component>
        <side-bar-menu-component :expand="subIsActive(['/notes'])">
                <span class="icon is-small m-l-sm">
                 <i class="far fa-sticky-note"></i>
                </span>
          פתקים
          <template slot="submenu">
            <li>
              <router-link to="/notes" exact @click.native="hideMenu">רשימת פתקים</router-link>
            </li>
            <li>
            <li>
              <router-link to="/notes/add" @click.native="hideMenu">יצירת פתק</router-link>
            </li>
          </template>
        </side-bar-menu-component>
        <side-bar-menu-component :expand="subIsActive(['/vendors'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-user-friends"></i>
                </span>
          ספקים
          <template slot="submenu">
            <li>
              <router-link to="/vendors" exact @click.native="hideMenu">רשימת ספקים</router-link>
            </li>
            <li>
              <router-link to="/vendors/add" @click.native="hideMenu">הכנסת ספק חדש</router-link>
            </li>
          </template>
        </side-bar-menu-component>

        <side-bar-menu-component :expand="subIsActive(['/incomes'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-money-check"></i>
                </span>
          הכנסות
          <template slot="submenu">
            <li>
              <router-link to="/incomes" exact @click.native="hideMenu">רשימת הכנסות</router-link>
            </li>
            <li>
              <router-link to="/incomes/add" @click.native="hideMenu">הוספת הכנסה</router-link>
            </li>
          </template>
        </side-bar-menu-component>

        <side-bar-menu-component :expand="subIsActive(['/expenses'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-money-check-alt"></i>
                </span>
          הוצאות
          <template slot="submenu">
            <li>
              <router-link to="/expenses" exact @click.native="hideMenu">רשימת הוצאות</router-link>
            </li>
            <li>
              <router-link to="/expenses/add" @click.native="hideMenu">הוספת הוצאה</router-link>
            </li>
          </template>
        </side-bar-menu-component>

        <side-bar-menu-component :expand="subIsActive(['/payments'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-dollar-sign"></i>
                </span>
          תשלומים
          <template slot="submenu">
            <li>
              <router-link to="/payments" exact @click.native="hideMenu">רשימת תשלומים</router-link>
            </li>
            <li>
              <router-link to="/payments/add" @click.native="hideMenu">הוספת תשלום</router-link>
            </li>
          </template>
        </side-bar-menu-component>

        <!--            <span v-if="$store.getters.admin">-->
        <!--                <side-bar-menu-component :expand="subIsActive(['/transfers'])">-->
        <!--                    <span class="icon is-small m-l-sm">-->
        <!--                        <i class="fas fa-fw fa-exchange-alt"></i>-->
        <!--                    </span>-->
        <!--                    העברות כספים-->
        <!--                    <template slot="submenu">-->
        <!--                        <li>-->
        <!--                            <router-link to="/transfers" exact @click.native="hideMenu">רשימת העברות כספים</router-link>-->
        <!--                        </li>-->
        <!--                        <li>-->
        <!--                            <router-link to="/transfers/add" @click.native="hideMenu">הוספת העברה</router-link>-->
        <!--                        </li>-->
        <!--                    </template>-->
        <!--                </side-bar-menu-component>-->

        <!--                <side-bar-menu-component :expand="subIsActive(['/accounts'])">-->
        <!--                    <span class="icon is-small m-l-sm">-->
        <!--                        <i class="fas fa-fw fa-swatchbook"></i>-->
        <!--                    </span>-->
        <!--                    חשבונות-->
        <!--                    <template slot="submenu">-->
        <!--                        <li>-->
        <!--                            <router-link to="/accounts" exact @click.native="hideMenu">רשימת חשבונות</router-link>-->
        <!--                        </li>-->
        <!--                        <li>-->
        <!--                            <router-link to="/accounts/add" @click.native="hideMenu">הוספת חשבון</router-link>-->
        <!--                        </li>-->
        <!--                    </template>-->
        <!--                </side-bar-menu-component>-->
        <!--            </span>-->

        <span v-if="$store.getters.superAdmin">
                <side-bar-menu-component :expand="subIsActive(['/users'])">
                    <span class="icon is-small m-l-sm">
                        <i class="fas fa-fw fa-users-cog"></i>
                    </span>
                    משתמשים
                    <template slot="submenu">
                        <li>
                            <router-link to="/users" exact @click.native="hideMenu">רשימת משתמשות</router-link>
                        </li>
                        <li>
                            <router-link to="/users/add" @click.native="hideMenu">הוספת משתמשת</router-link>
                        </li>
                    </template>
                </side-bar-menu-component>
            </span>


        <span v-if="$store.getters.admin">
                <side-bar-menu-component :expand="subIsActive(['/settings', '/taxes', '/categories'])">
                    <span class="icon is-small m-l-sm">
                        <i class="fas fa-fw fa-cogs"></i>
                    </span>
                    הגדרות
                    <template slot="submenu">
                        <span v-if="$store.getters.superAdmin">
                            <li>
                                <router-link class="navbar-item" to="/settings" exact @click.native="hideMenu">הגדרות אתר</router-link>
                            </li>
                            <li>
                                <router-link class="navbar-item" to="/settings/invoice_settings"
                                             @click.native="hideMenu">
                                    הגדרות חשבונית
                                </router-link>
                            </li>
                            <li>
                                <router-link class="navbar-item" to="/settings/system" exact @click.native="hideMenu">
                                    הגדרות מערכת
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/settings/email_templates" @click.native="hideMenu">
                                  הגדרות תבניות לאמיילים
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/settings/email_settings" @click.native="hideMenu">
                                  הגדרות לאמיילים
                                </router-link>
                            </li>
                        </span>
                    </template>
                        </side-bar-menu-component>
                <side-bar-menu-component :expand="subIsActive(['/settings', '/taxes', '/categories'])">
                    <span class="icon is-small m-l-sm">
                        <i class="fas fa-fw fa-cogs"></i>
                    </span>
                    רשימות שונות
                      <template slot="submenu">
                        <li>
                            <router-link to="/taxes" exact @click.native="hideMenu">רשימת מיסים</router-link>
                        </li>
                        <li>
                            <router-link to="/categories" exact @click.native="hideMenu">רשימת קטגוריות</router-link>
                        </li>
                        <li>
                            <router-link to="/settings/fields" @click.native="hideMenu">רשימת שדות דינמיים</router-link>
                        </li>
                        <li>
                            <router-link to="/settings/statuses" @click.native="hideMenu">רשימת סטטוסים</router-link>
                        </li>
                       <li>
                            <router-link to="/settings/arrival-source" @click.native="hideMenu">                            רשימת מקורות הגעה
                            </router-link>
                        </li>
                       <li>
                            <router-link to="/settings/notes-categories" @click.native="hideMenu">רשימת תגיות לפתקים </router-link>
                        </li>
                      </template>
                </side-bar-menu-component>
            </span>
        <li v-if="$store.getters.admin">
          <router-link to="/report" exact @click.native="hideMenu">
                    <span class="icon is-small m-l-sm">
                        <i class="fas fa-fw fa-chart-line"></i>
                    </span>
            דוחות
          </router-link>
        </li>
      </ul>
    </aside>

  </div>
</template>

<script>
import SideBarMenuComponent from './helpers/SideBarMenuComponent.vue';
import EventFormModal from "./calendar/EventFormModal.vue";

export default {
  components: {
    SideBarMenuComponent,

  },
  methods: {
    hideMenu() {
      if (window) {
        if (window.innerWidth <= 1023) {
          this.$store.commit('TOGGLE_SIDEBAR', true);
        }
        window.scroll(0, 0);
      }
    },
    subIsActive(input) {
      const paths = Array.isArray(input) ? input : [input];
      return paths.some(path => {
        return this.$route.path.indexOf(path) === 0;
      });
    },
    addEvent() {

      this.$modal.show("event-form-modal", {event: null});
    },

  },

};
</script>

<style lang="scss">
@import '../../sass/variables';

.app-sidebar {
  padding: 1px 0;
  height: 100% !important;
  background: #fff;

  .menu-list li {
    border-bottom: 1px solid $white-ter;

    a {
      padding: 0.65em 0.75em;
    }
  }

  .menu-list .submenu .is-active {
    background-color: whitesmoke !important;
    color: #363636;
  }
}
</style>
