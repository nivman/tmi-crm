<template>
    <nav class="navbar is-mini is-link animated fastest fadeInDown">
        <div class="container">
            <div id="mainMenuTop" class="navbar-menu" :class="{ 'is-active': !menu }">
                <div class="navbar-start" v-if="!$store.getters.customer && !$store.getters.vendor">
                    <router-link class="navbar-item" to="/" exact @click.native="hideMenu">
                        <i class="fas fa-home is-hidden-touch" />
                        <span class="is-hidden-desktop">Dashboard</span>
                    </router-link>
                    <router-link class="navbar-item" to="/calendar" exact @click.native="hideMenu">
                        <i class="fas fa-calendar is-hidden-touch" />
                        <span class="is-hidden-desktop">Calendar</span>
                    </router-link>
                    <router-link v-if="$store.getters.admin" class="navbar-item" to="/report" exact @click.native="hideMenu">
                        <i class="fas fa-chart-line is-hidden-touch" />
                        <span class="is-hidden-desktop">Report</span>
                    </router-link>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" :class="{ 'is-active': subIsActive(['/invoices', '/recurrings']) }">Invoices</a>
                        <div class="navbar-dropdown">
                            <router-link class="navbar-item" to="/invoices" exact @click.native="hideMenu">List Invoices</router-link>
                            <router-link class="navbar-item" to="/invoices/add" @click.native="hideMenu">Add New Invoice</router-link>
                            <hr class="navbar-divider" />
                            <router-link class="navbar-item" to="/recurrings" exact @click.native="hideMenu">
                                List Recurring Invoices
                            </router-link>
                            <router-link class="navbar-item" to="/recurrings/add" @click.native="hideMenu">
                                Add New Recurring Invoice
                            </router-link>
                        </div>
                    </div>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" :class="{ 'is-active': subIsActive(['/purchases']) }">Purchases</a>
                        <div class="navbar-dropdown">
                            <router-link class="navbar-item" to="/purchases" exact @click.native="hideMenu">List Purchases</router-link>
                            <router-link class="navbar-item" to="/purchases/add" @click.native="hideMenu">Add New Purchase</router-link>
                        </div>
                    </div>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" :class="{ 'is-active': subIsActive(['/payments']) }">Payments</a>
                        <div class="navbar-dropdown">
                            <router-link class="navbar-item" to="/payments" exact @click.native="hideMenu">List Payments</router-link>
                            <router-link class="navbar-item" to="/payments/add" @click.native="hideMenu">Add New Payment</router-link>
                        </div>
                    </div>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" :class="{ 'is-active': subIsActive(['/products']) }">Products</a>
                        <div class="navbar-dropdown">
                            <router-link class="navbar-item" to="/products" exact @click.native="hideMenu">List Products</router-link>
                            <router-link class="navbar-item" to="/products/add" @click.native="hideMenu">Add New Product</router-link>
                        </div>
                    </div>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" :class="{ 'is-active': subIsActive(['/customers', '/vendors', '/users']) }">People</a>
                        <div class="navbar-dropdown">
                            <router-link class="navbar-item" to="/customers" exact @click.native="hideMenu">List Customers</router-link>
                            <router-link class="navbar-item" to="/customers/add" @click.native="hideMenu">Add New Customer</router-link>
                            <hr class="navbar-divider" />
                            <router-link class="navbar-item" to="/vendors" exact @click.native="hideMenu">List Vendors</router-link>
                            <router-link class="navbar-item" to="/vendors/add" @click.native="hideMenu">Add New Vendor</router-link>
                            <span v-if="$store.getters.superAdmin">
                                <!-- <hr class="navbar-divider">
                                <router-link class="navbar-item" to="/sellers" exact @click.native="hideMenu">List Sellers</router-link>
                                <router-link class="navbar-item" to="/sellers/add" @click.native="hideMenu">Add New Seller</router-link> -->
                                <hr class="navbar-divider" />
                                <router-link class="navbar-item" to="/users" exact @click.native="hideMenu">List Users</router-link>
                                <router-link class="navbar-item" to="/users/add" @click.native="hideMenu">Add New User</router-link>
                            </span>
                        </div>
                    </div>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" :class="{ 'is-active': subIsActive(['/expenses', '/incomes', '/transfers']) }">Accounting</a>
                        <div class="navbar-dropdown">
                            <router-link class="navbar-item" to="/expenses" exact @click.native="hideMenu">List Expenses</router-link>
                            <router-link class="navbar-item" to="/expenses/add" @click.native="hideMenu">Add New Expense</router-link>
                            <hr class="navbar-divider" />
                            <router-link class="navbar-item" to="/incomes" exact @click.native="hideMenu">List Incomes</router-link>
                            <router-link class="navbar-item" to="/incomes/add" @click.native="hideMenu">Add New Income</router-link>
                            <span v-if="$store.getters.admin">
                                <hr class="navbar-divider" />
                                <router-link class="navbar-item" to="/transfers" exact @click.native="hideMenu">List Transfers</router-link>
                                <router-link class="navbar-item" to="/transfers/add" @click.native="hideMenu">Add New Transfer</router-link>
                            </span>
                        </div>
                    </div>
                    <div class="navbar-item has-dropdown is-hoverable" v-if="$store.getters.admin">
                        <a
                            class="navbar-link"
                            :class="{ 'is-active': subIsActive(['/settings', '/categories', '/taxes', '/accounts', '/transactions']) }"
                            >Settings</a
                        >
                        <div class="navbar-dropdown">
                            <span v-if="$store.getters.superAdmin">
                                <router-link class="navbar-item" to="/settings" exact @click.native="hideMenu">App Settings</router-link>
                                <router-link class="navbar-item" to="/settings/invoice_settings" @click.native="hideMenu">
                                    Invoice Settings
                                </router-link>
                                <router-link class="navbar-item" to="/settings/system" exact @click.native="hideMenu">
                                    System Settings
                                </router-link>
                                <hr class="navbar-divider" />
                            </span>
                            <!-- <router-link class="navbar-item" to="/accounts/add" @click.native="hideMenu">Add New Account</router-link> -->
                            <!-- <router-link exact class="navbar-item" to="/transactions" @click.native="hideMenu">All Transactions</router-link> -->
                            <!-- <hr class="navbar-divider"> -->
                            <router-link class="navbar-item" to="/taxes" exact @click.native="hideMenu">List Taxes</router-link>
                            <router-link class="navbar-item" to="/accounts" exact @click.native="hideMenu">List Accounts</router-link>
                            <router-link class="navbar-item" to="/categories" exact @click.native="hideMenu">List Categories</router-link>
                            <router-link class="navbar-item" to="/settings/fields" @click.native="hideMenu">List Custom Fields</router-link>
                            <span v-if="$store.getters.superAdmin">
                                <hr class="navbar-divider" />
                                <router-link class="navbar-item" to="/settings/email_templates" @click.native="hideMenu"
                                    >Email Templates</router-link
                                >
                            </span>
                        </div>
                    </div>
                </div>
                <div class="navbar-start" v-if="$store.getters.customer && $store.getters.vendor">
                    <router-link class="navbar-item" to="/" exact @click.native="hideMenu">
                        <i class="fas fa-home is-hidden-touch" />
                        <span class="is-hidden-desktop">Dashboard</span>
                    </router-link>
                    <router-link class="navbar-item" to="/invoices" exact @click.native="hideMenu">
                        Invoices
                    </router-link>
                    <router-link class="navbar-item" to="/recurrings" exact @click.native="hideMenu">
                        Recurring Invoices
                    </router-link>
                    <router-link class="navbar-item" to="/purchases" exact @click.native="hideMenu">
                        Purchases
                    </router-link>
                    <router-link class="navbar-item" to="/payments" exact @click.native="hideMenu">
                        Payments
                    </router-link>
                    <router-link class="navbar-item" to="/customer" exact @click.native="hideMenu">
                        Customer Company
                    </router-link>
                    <router-link class="navbar-item" to="/vendor" exact @click.native="hideMenu">
                        Vendor Company
                    </router-link>
                </div>
                <div class="navbar-start" v-if="$store.getters.customer && !$store.getters.vendor">
                    <router-link class="navbar-item" to="/" exact @click.native="hideMenu">
                        <i class="fas fa-home is-hidden-touch" />
                        <span class="is-hidden-desktop">Dashboard</span>
                    </router-link>
                    <router-link class="navbar-item" to="/invoices" exact @click.native="hideMenu">
                        Invoices
                    </router-link>
                    <router-link class="navbar-item" to="/recurrings" exact @click.native="hideMenu">
                        Recurring Invoices
                    </router-link>
                    <router-link class="navbar-item" to="/payments" exact @click.native="hideMenu">
                        Payments
                    </router-link>
                    <router-link class="navbar-item" to="/customer" exact @click.native="hideMenu">
                        Company
                    </router-link>
                </div>
                <div class="navbar-start" v-if="$store.getters.vendor && !$store.getters.customer">
                    <router-link class="navbar-item" to="/" exact @click.native="hideMenu">
                        <i class="fas fa-home is-hidden-touch" />
                        <span class="is-hidden-desktop">Dashboard</span>
                    </router-link>
                    <router-link class="navbar-item" to="/purchases" exact @click.native="hideMenu">
                        Purchases
                    </router-link>
                    <router-link class="navbar-item" to="/payments" exact @click.native="hideMenu">
                        Payments
                    </router-link>
                    <router-link class="navbar-item" to="/vendor" exact @click.native="hideMenu">
                        Company
                    </router-link>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
export default {
    computed: {
        menu() {
            return this.$store.state.sideBar;
        },
        name() {
            return this.$store.getters.user ? this.$store.getters.user.name : '';
        },
        user_name() {
            return this.$store.getters.user ? this.$store.getters.user.username : '';
        },
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
        logout() {
            this.$event.fire('logOut');
        },
        subIsActive(input) {
            const paths = Array.isArray(input) ? input : [input];
            return paths.some(path => {
                return this.$route.path.indexOf(path) === 0;
            });
        },
    },
};
</script>
