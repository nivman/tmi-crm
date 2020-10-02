import VueRouter from "vue-router";

import LoginComponent from "./components/LoginComponent.vue";
import NotFoundComponent from "./components/helpers/NotFoundComponent.vue";
import ParentRouteOutletComponent from "./components/ParentRouteOutletComponent.vue";

import DashboardComponent from "./components/DashboardComponent.vue";
import CalendarComponent from "./components/calendar/CalendarComponent.vue";
import AccountFormComponent from "./components/accounts/AccountFormComponent.vue";
import AccountListComponent from "./components/accounts/AccountListComponent.vue";
import ExpenseFormComponent from "./components/expenses/ExpenseFormComponent.vue";
import ExpenseListComponent from "./components/expenses/ExpenseListComponent.vue";
import ExpenseViewComponent from "./components/expenses/ExpenseViewComponent.vue";
import InvoiceFormComponent from "./components/invoices/InvoiceFormComponent.vue";
import InvoiceListComponent from "./components/invoices/InvoiceListComponent.vue";
import InvoiceViewComponent from "./components/invoices/InvoiceViewComponent.vue";
import ProductFormComponent from "./components/products/ProductFormComponent.vue";
import ProductListComponent from "./components/products/ProductListComponent.vue";
import ProductViewComponent from "./components/products/ProductViewComponent.vue";
import PurchaseFormComponent from "./components/purchases/PurchaseFormComponent.vue";
import PurchaseListComponent from "./components/purchases/PurchaseListComponent.vue";
import CategoryFormComponent from "./components/categories/CategoryFormComponent.vue";
import CategoryListComponent from "./components/categories/CategoryListComponent.vue";
import RecurringInvoiceListComponent from "./components/invoices/RecurringInvoiceListComponent.vue";
import RecurringInvoiceFormComponent from "./components/invoices/RecurringInvoiceFormComponent.vue";

import TransactionsComponent from "./components/accounts/TransactionsComponent.vue";
import VendorTransactionsComponent from "./components/vendors/TransactionsComponent.vue";
import CustomerTransactionsComponent from "./components/customers/TransactionsComponent.vue";

import ProfileComponent from "./components/users/ProfileComponent.vue";
import UserFormComponent from "./components/users/UserFormComponent.vue";
import UserListComponent from "./components/users/UserListComponent.vue";
import IncomeFormComponent from "./components/incomes/IncomeFormComponent.vue";
import IncomeListComponent from "./components/incomes/IncomeListComponent.vue";
import IncomeViewComponent from "./components/incomes/IncomeViewComponent.vue";
import VendorFormComponent from "./components/vendors/VendorFormComponent.vue";
import VendorListComponent from "./components/vendors/VendorListComponent.vue";
import VendorViewComponent from "./components/vendors/VendorViewComponent.vue";
import PaymentFormComponent from "./components/payments/PaymentFormComponent.vue";
import PaymentListComponent from "./components/payments/PaymentListComponent.vue";
import PaymentViewComponent from "./components/payments/PaymentViewComponent.vue";
import ChangePasswordComponent from "./components/users/ChangePasswordComponent.vue";
import CustomerFormComponent from "./components/customers/CustomerFormComponent.vue";
import CustomerListComponent from "./components/customers/CustomerListComponent.vue";
import CustomerViewComponent from "./components/customers/CustomerViewComponent.vue";

import TaxListComponent from "./components/taxes/TaxListComponent.vue";
import TaxFormComponent from "./components/taxes/TaxFormComponent.vue";
import TransferListComponent from "./components/transfers/TransferListComponent.vue";
import TransferFormComponent from "./components/transfers/TransferFormComponent.vue";
import CustomFieldsComponent from "./components/settings/CustomFieldsComponent.vue";

import AppSettingsComponent from "./components/settings/AppSettingsComponent.vue";
import SystemSettingsComponent from "./components/settings/SystemSettingsComponent.vue";
import EmailTemplatesComponent from "./components/settings/EmailTemplatesComponent.vue";
import CustomFieldFormComponent from "./components/settings/CustomFieldFormComponent.vue";
import InvoiceSettingsComponent from "./components/settings/InvoiceSettingsComponent.vue";

const router = new VueRouter({
    mode: "history",
    routes: [
        {
            path: "/",
            component: DashboardComponent,
            meta: { title: "Dashboard", admin: false }
        },
        {
            path: "/calendar",
            component: CalendarComponent,
            meta: { title: "Calendar", admin: false }
        },
        {
            path: "/profile",
            component: ProfileComponent,
            meta: { title: "Profile", admin: false }
        },
        {
            name: "profile",
            path: "/profile/:username",
            component: ProfileComponent,
            meta: { title: "Profile", admin: true }
        },
        {
            path: "/change_password",
            component: ChangePasswordComponent,
            meta: { title: "Change Password", admin: false }
        },
        {
            path: "/invoices",
            component: InvoiceListComponent,
            meta: { title: "Invoices", admin: false }
        },
        {
            path: "/invoices/add",
            component: InvoiceFormComponent,
            meta: { title: "Add New Invoice", admin: false }
        },
        {
            path: "/invoices/edit/:id",
            component: InvoiceFormComponent,
            meta: { title: "Edit Invoice", admin: true }
        },
        {
            path: "/invoices/:id",
            component: InvoiceViewComponent,
            meta: { title: "Invoice Details", admin: false }
        },
        {
            path: "/recurrings",
            component: RecurringInvoiceListComponent,
            meta: { title: "Recurring Invoices", admin: false }
        },
        {
            path: "/recurrings/add",
            component: RecurringInvoiceFormComponent,
            meta: { title: "Add New Recurring Invoice", admin: false }
        },
        {
            path: "/recurrings/edit/:id",
            component: RecurringInvoiceFormComponent,
            meta: { title: "Edit Recurring Invoice", admin: true }
        },
        {
            path: "/purchases",
            component: PurchaseListComponent,
            meta: { title: "Purchases", admin: false }
        },
        {
            path: "/purchases/add",
            component: PurchaseFormComponent,
            meta: { title: "Add New Purchase", admin: false }
        },
        {
            path: "/purchases/edit/:id",
            component: PurchaseFormComponent,
            meta: { title: "Edit Purchase", admin: true }
        },
        {
            path: "/payments",
            component: PaymentListComponent,
            meta: { title: "Payments", admin: false },
            children: [
                {
                    path: "add",
                    component: PaymentFormComponent,
                    meta: { title: "Add Payment", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    component: PaymentFormComponent,
                    meta: { title: "Edit Payment", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: PaymentViewComponent,
                    meta: { title: "View Payment", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/products",
            component: ProductListComponent,
            meta: { title: "Products", admin: false },
            children: [
                {
                    path: "add",
                    component: ProductFormComponent,
                    meta: { title: "Add Product", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    component: ProductFormComponent,
                    meta: { title: "Edit Product", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: ProductViewComponent,
                    meta: { title: "View Product", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/categories",
            component: CategoryListComponent,
            meta: { title: "Categories", admin: true },
            children: [
                {
                    path: "add",
                    component: CategoryFormComponent,
                    meta: { title: "Add Category", admin: true }
                },
                {
                    path: "edit/:id",
                    component: CategoryFormComponent,
                    meta: { title: "Edit Category", admin: true }
                }
            ]
        },
        {
            path: "/customers",
            component: CustomerListComponent,
            meta: { title: "Customers", admin: false },
            children: [
                {
                    path: "add",
                    component: CustomerFormComponent,
                    meta: { title: "Add Customer", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    component: CustomerFormComponent,
                    meta: { title: "Edit Customer", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: CustomerViewComponent,
                    meta: { title: "View Customer", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/customers/transactions/:id",
            component: CustomerTransactionsComponent,
            meta: { title: "List Customer Transactions", admin: true }
        },
        {
            path: "/incomes",
            component: IncomeListComponent,
            meta: { title: "Income", admin: false },
            children: [
                {
                    path: "add",
                    component: IncomeFormComponent,
                    meta: { title: "Add Income", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    component: IncomeFormComponent,
                    meta: { title: "Edit Income", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: IncomeViewComponent,
                    meta: { title: "View Income", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/expenses",
            component: ExpenseListComponent,
            meta: { title: "Expenses", admin: false },
            children: [
                {
                    path: "add",
                    component: ExpenseFormComponent,
                    meta: { title: "Add Expense", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    component: ExpenseFormComponent,
                    meta: { title: "Edit Expense", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: ExpenseViewComponent,
                    meta: { title: "View Expense", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/accounts",
            component: AccountListComponent,
            meta: { title: "Accounts", admin: true },
            children: [
                {
                    path: "add",
                    component: AccountFormComponent,
                    meta: { title: "Add Account", admin: true, modal: true }
                },
                {
                    path: "edit/:id",
                    component: AccountFormComponent,
                    meta: { title: "Edit Account", admin: true, modal: true }
                }
            ]
        },
        // {
        //     path: "/transactions",
        //     component: TransactionsComponent,
        //     meta: { title: "List All Transactions", admin: true }
        // },
        {
            path: "/accounts/transactions/:id",
            component: TransactionsComponent,
            meta: { title: "List Account Transactions", admin: true }
        },
        {
            path: "/vendors",
            component: VendorListComponent,
            meta: { title: "Vendors", admin: false },
            children: [
                {
                    path: "add",
                    component: VendorFormComponent,
                    meta: { title: "Add Vendor", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    component: VendorFormComponent,
                    meta: { title: "Edit Vendor", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: VendorViewComponent,
                    meta: { title: "View Vendor", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/vendors/transactions/:id",
            component: VendorTransactionsComponent,
            meta: { title: "List Customer Transactions", admin: true }
        },
        {
            path: "/users",
            component: UserListComponent,
            meta: { title: "Users", admin: false, super: true },
            children: [
                {
                    path: "add",
                    component: UserFormComponent,
                    meta: { title: "Add User", admin: false, super: true, modal: true }
                },
                {
                    path: "edit/:id",
                    component: UserFormComponent,
                    meta: { title: "Edit User", admin: false, super: true, modal: true }
                }
            ]
        },
        {
            path: "/transfers",
            component: TransferListComponent,
            meta: { title: "Transfers", admin: true },
            children: [
                {
                    path: "add",
                    component: TransferFormComponent,
                    meta: { title: "Add Transfer", admin: true, modal: true }
                },
                {
                    path: "edit/:id",
                    component: TransferFormComponent,
                    meta: { title: "Edit Transfer", admin: true, modal: true }
                }
            ]
        },
        {
            path: "/taxes",
            component: TaxListComponent,
            meta: { title: "Taxes", admin: true },
            children: [
                {
                    path: "add",
                    component: TaxFormComponent,
                    meta: { title: "Add Tax", admin: true, modal: true }
                },
                {
                    path: "edit/:id",
                    component: TaxFormComponent,
                    meta: { title: "Edit Tax", admin: true, modal: true }
                }
            ]
        },
        {
            path: "/settings",
            component: ParentRouteOutletComponent,
            meta: { title: "Settings", admin: true },
            children: [
                {
                    path: "/",
                    component: AppSettingsComponent,
                    meta: { title: "Application Settings", admin: false, super: true }
                },
                {
                    path: "system",
                    component: SystemSettingsComponent,
                    meta: { title: "System Settings", admin: false, super: true }
                },
                {
                    path: "email_templates",
                    component: EmailTemplatesComponent,
                    meta: { title: "Email Templates", admin: false, super: true }
                },
                {
                    path: "invoice_settings",
                    component: InvoiceSettingsComponent,
                    meta: { title: "Invoice Settings", admin: false, super: true }
                },
                {
                    path: "fields",
                    component: CustomFieldsComponent,
                    meta: { title: "Custom Fields", admin: true },
                    children: [
                        {
                            path: "add",
                            component: CustomFieldFormComponent,
                            meta: {
                                title: "Add Custom Field",
                                admin: true,
                                modal: true
                            }
                        },
                        {
                            path: "edit/:id",
                            component: CustomFieldFormComponent,
                            meta: {
                                title: "Edit Custom Field",
                                admin: true,
                                modal: true
                            }
                        }
                    ]
                }
            ]
        },
        {
            path: "/login",
            component: LoginComponent,
            meta: { title: "Login", admin: false },
            beforeEnter: (to, from, next) => {
                if (localStorage.getItem("user")) {
                    next("/");
                } else {
                    next();
                }
            }
        },
        {
            path: "*",
            component: NotFoundComponent,
            meta: { title: "Page Not Found", admin: false }
        }
    ],
    linkActiveClass: "is-active",
    scrollBehavior: () => ({ y: 0 })
});

export default router;
