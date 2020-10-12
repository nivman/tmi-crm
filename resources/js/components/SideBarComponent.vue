<template>
  <div>
    <aside class="menu app-sidebar animated fastest fadeInRight">
        <ul class="menu-list" v-if="!$store.getters.customer && !$store.getters.vendor">
            <li>
                <router-link to="/" exact @click.native="hideMenu">
                    <span class="icon is-small m-l-sm">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                    </span>
                    Dashboard
                </router-link>
            </li>
            <li>
                <router-link to="/calendar" exact @click.native="hideMenu">
                    <span class="icon is-small m-l-sm">
                        <i class="fas fa-fw fa-calendar-alt"></i>
                    </span>
                    Calendar
                </router-link>
            </li>
            <side-bar-menu-component :expand="subIsActive(['/invoices', '/recurrings'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-file-invoice"></i>
                </span>
                Invoices
                <template slot="submenu">
                    <li>
                        <router-link to="/invoices" exact @click.native="hideMenu">List Invoices</router-link>
                    </li>
                    <li>
                        <router-link to="/invoices/add" @click.native="hideMenu">Add New Invoice</router-link>
                    </li>
                    <li>
                        <router-link to="/recurrings" exact @click.native="hideMenu">List Recurring Invoices</router-link>
                    </li>
                    <li>
                        <router-link to="/recurrings/add" @click.native="hideMenu">New Recurring Invoice</router-link>
                    </li>
                </template>
            </side-bar-menu-component>

            <side-bar-menu-component :expand="subIsActive(['/purchases'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-file-invoice-dollar"></i>
                </span>
                Purchases
                <template slot="submenu">
                    <li>
                        <router-link to="/purchases" exact @click.native="hideMenu">List Purchases</router-link>
                    </li>
                    <li>
                        <router-link to="/purchases/add" @click.native="hideMenu">Add New Purchase</router-link>
                    </li>
                </template>
            </side-bar-menu-component>

            <side-bar-menu-component :expand="subIsActive(['/products'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-cubes"></i>
                </span>
                Products
                <template slot="submenu">
                    <li>
                        <router-link to="/products" exact @click.native="hideMenu">List Products</router-link>
                    </li>
                    <li>
                        <router-link to="/products/add" @click.native="hideMenu">Add New Product</router-link>
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
                </template>
            </side-bar-menu-component>
          <side-bar-menu-component :expand="subIsActive(['/events'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-users"></i>
                </span>
            התקשרויות
            <template slot="submenu">
              <li>
                <router-link to="/events/list" exact @click.native="hideMenu">רשימת התקשרויות</router-link>
              </li>
              <li>
                <a
                    @click="addEvent"
                    >הוספת התקשרות
                </a>

              </li>
            </template>
          </side-bar-menu-component>
            <side-bar-menu-component :expand="subIsActive(['/vendors'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-user-friends"></i>
                </span>
                Vendors
                <template slot="submenu">
                    <li>
                        <router-link to="/vendors" exact @click.native="hideMenu">List Vendors</router-link>
                    </li>
                    <li>
                        <router-link to="/vendors/add" @click.native="hideMenu">Add New Vendor</router-link>
                    </li>
                </template>
            </side-bar-menu-component>

            <side-bar-menu-component :expand="subIsActive(['/incomes'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-money-check"></i>
                </span>
                Incomes
                <template slot="submenu">
                    <li>
                        <router-link to="/incomes" exact @click.native="hideMenu">List Incomes</router-link>
                    </li>
                    <li>
                        <router-link to="/incomes/add" @click.native="hideMenu">Add New Income</router-link>
                    </li>
                </template>
            </side-bar-menu-component>

            <side-bar-menu-component :expand="subIsActive(['/expenses'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-money-check-alt"></i>
                </span>
                Expenses
                <template slot="submenu">
                    <li>
                        <router-link to="/expenses" exact @click.native="hideMenu">List Expenses</router-link>
                    </li>
                    <li>
                        <router-link to="/expenses/add" @click.native="hideMenu">Add New Expense</router-link>
                    </li>
                </template>
            </side-bar-menu-component>

            <side-bar-menu-component :expand="subIsActive(['/payments'])">
                <span class="icon is-small m-l-sm">
                    <i class="fas fa-fw fa-dollar-sign"></i>
                </span>
                Payments
                <template slot="submenu">
                    <li>
                        <router-link to="/payments" exact @click.native="hideMenu">List Payments</router-link>
                    </li>
                    <li>
                        <router-link to="/payments/add" @click.native="hideMenu">Add New Payment</router-link>
                    </li>
                </template>
            </side-bar-menu-component>

            <span v-if="$store.getters.admin">
                <side-bar-menu-component :expand="subIsActive(['/transfers'])">
                    <span class="icon is-small m-l-sm">
                        <i class="fas fa-fw fa-exchange-alt"></i>
                    </span>
                    Transfers
                    <template slot="submenu">
                        <li>
                            <router-link to="/transfers" exact @click.native="hideMenu">List Transfers</router-link>
                        </li>
                        <li>
                            <router-link to="/transfers/add" @click.native="hideMenu">Add New Transfer</router-link>
                        </li>
                    </template>
                </side-bar-menu-component>

                <side-bar-menu-component :expand="subIsActive(['/accounts'])">
                    <span class="icon is-small m-l-sm">
                        <i class="fas fa-fw fa-swatchbook"></i>
                    </span>
                    Accounts
                    <template slot="submenu">
                        <li>
                            <router-link to="/accounts" exact @click.native="hideMenu">List Accounts</router-link>
                        </li>
                        <li>
                            <router-link to="/accounts/add" @click.native="hideMenu">Add New Account</router-link>
                        </li>
                    </template>
                </side-bar-menu-component>
            </span>

            <span v-if="$store.getters.superAdmin">
                <side-bar-menu-component :expand="subIsActive(['/users'])">
                    <span class="icon is-small m-l-sm">
                        <i class="fas fa-fw fa-users-cog"></i>
                    </span>
                    Users
                    <template slot="submenu">
                        <li>
                            <router-link to="/users" exact @click.native="hideMenu">List Users</router-link>
                        </li>
                        <li>
                            <router-link to="/users/add" @click.native="hideMenu">Add New User</router-link>
                        </li>
                    </template>
                </side-bar-menu-component>
            </span>

            <span v-if="$store.getters.admin">
                <side-bar-menu-component :expand="subIsActive(['/settings', '/taxes', '/categories'])">
                    <span class="icon is-small m-l-sm">
                        <i class="fas fa-fw fa-cogs"></i>
                    </span>
                    Settings
                    <template slot="submenu">
                        <span v-if="$store.getters.superAdmin">
                            <li>
                                <router-link class="navbar-item" to="/settings" exact @click.native="hideMenu">App Settings</router-link>
                            </li>
                            <li>
                                <router-link class="navbar-item" to="/settings/invoice_settings" @click.native="hideMenu">
                                    Invoice Settings
                                </router-link>
                            </li>
                            <li>
                                <router-link class="navbar-item" to="/settings/system" exact @click.native="hideMenu">
                                    System Settings
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/settings/email_templates" @click.native="hideMenu">Email Templates</router-link>
                            </li>
                        </span>
                        <li>
                            <router-link to="/taxes" exact @click.native="hideMenu">List Taxes</router-link>
                        </li>
                        <li>
                            <router-link to="/categories" exact @click.native="hideMenu">List Categories</router-link>
                        </li>
                        <li>
                            <router-link to="/settings/fields" @click.native="hideMenu">List Custom Fields</router-link>
                        </li>
                      <li>
                            <router-link to="/settings/statuses" @click.native="hideMenu">רשימת סטטוסים</router-link>
                        </li>
                       <li>
                            <router-link to="/settings/fields" @click.native="hideMenu"></router-link>
                        </li>
                    </template>
                </side-bar-menu-component>
            </span>
            <li v-if="$store.getters.admin">
                <router-link to="/report" exact @click.native="hideMenu">
                    <span class="icon is-small m-l-sm">
                        <i class="fas fa-fw fa-chart-line"></i>
                    </span>
                    Report
                </router-link>
            </li>
        </ul>
        <ul class="menu-list" v-if="$store.getters.customer && $store.getters.vendor">
            <li>
                <router-link to="/" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                    </span>
                    Dashboard
                </router-link>
            </li>
            <li>
                <router-link to="/invoices" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-file-invoice"></i>
                    </span>
                    Invoices
                </router-link>
            </li>
            <li>
                <router-link to="/recurrings" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-file-contract"></i>
                    </span>
                    Recurring Invoices
                </router-link>
            </li>
            <li>
                <router-link to="/purchases" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-file-invoice-dollar"></i>
                    </span>
                    Purchases
                </router-link>
            </li>
            <li>
                <router-link to="/payments" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-dollar-sign"></i>
                    </span>
                    Payments
                </router-link>
            </li>
            <li>
                <router-link to="/customer" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-file-alt"></i>
                    </span>
                    Customer Company
                </router-link>
            </li>
            <li>
                <router-link to="/vendor" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-file-alt"></i>
                    </span>
                    Vendor Company
                </router-link>
            </li>
        </ul>
        <ul class="menu-list" v-if="$store.getters.customer && !$store.getters.vendor">
            <li>
                <router-link to="/" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                    </span>
                    Dashboard
                </router-link>
            </li>
            <li>
                <router-link to="/invoices" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-file-invoice"></i>
                    </span>
                    Invoices
                </router-link>
            </li>
            <li>
                <router-link to="/recurrings" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-file-contract"></i>
                    </span>
                    Recurring Invoices
                </router-link>
            </li>
            <li>
                <router-link to="/payments" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-dollar-sign"></i>
                    </span>
                    Payments
                </router-link>
            </li>
            <li>
                <router-link to="/customer" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-file-alt"></i>
                    </span>
                    חברה

                </router-link>
            </li>
        </ul>
        <ul class="menu-list" v-if="$store.getters.vendor && !$store.getters.customer">
            <li>
                <router-link to="/" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                    </span>
                    Dashboard
                </router-link>
            </li>
            <li>
                <router-link to="/purchases" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-file-invoice-dollar"></i>
                    </span>
                    Purchases
                </router-link>
            </li>
            <li>
                <router-link to="/payments" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-dollar-sign"></i>
                    </span>
                    Payments
                </router-link>
            </li>
            <li>
                <router-link to="/vendor" exact @click.native="hideMenu">
                    <span class="icon is-small m-r-sm">
                        <i class="fas fa-fw fa-file-alt"></i>
                    </span>
                    Company
                </router-link>
            </li>
        </ul>

    </aside>
    <event-form-modal></event-form-modal>
  </div>
</template>

<script>
import SideBarMenuComponent from './helpers/SideBarMenuComponent.vue';
import EventFormModal from "./calendar/EventFormModal.vue";
export default {
    components: {
        SideBarMenuComponent,
        EventFormModal
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

        this.$modal.show("event-form-modal", { event: null });
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
