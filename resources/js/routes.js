import VueRouter from "vue-router";

import LoginComponent from "./components/LoginComponent.vue";
import DashboardComponent from "./components/DashboardComponent.vue";
import ProfileComponent from "./components/users/ProfileComponent.vue";
import NotFoundComponent from "./components/helpers/NotFoundComponent.vue";

const router = new VueRouter({
    mode: "history",
    routes: [
        {
            path: "/",
            component: DashboardComponent,
            meta: { title: "Dashboard", admin: false, customer: true, vendor: true }
        },
        {
            path: "/calendar",
            component: () => import(/* webpackChunkName: "components" */ "./components/calendar/CalendarComponent.vue"),
            meta: { title: "Calendar", admin: false }
        },
        {
            path: "/profile",
            component: ProfileComponent,
            meta: { title: "Profile", admin: false, customer: true, vendor: true }
        },
        {
            name: "profile",
            path: "/profile/:username",
            component: ProfileComponent,
            meta: { title: "Profile", admin: true }
        },
        {
            path: "/change_password",
            component: () => import(/* webpackChunkName: "components" */ "./components/users/ChangePasswordComponent.vue"),
            meta: { title: "Change Password", admin: false, customer: true, vendor: true }
        },
        {
            path: "/invoices",
            component: () => import(/* webpackChunkName: "components" */ "./components/invoices/InvoiceListComponent.vue"),
            meta: { title: "Invoices", admin: false, customer: true }
        },
        {
            path: "/invoices/add",
            component: () => import(/* webpackChunkName: "components" */ "./components/invoices/InvoiceFormComponent.vue"),
            meta: { title: "Add New Invoice", admin: false }
        },
        {
            path: "/invoices/edit/:id",
            component: () => import(/* webpackChunkName: "components" */ "./components/invoices/InvoiceFormComponent.vue"),
            meta: { title: "Edit Invoice", admin: true }
        },
        // {
        //     path: "/invoices/:id",
        //     component: () => import(/* webpackChunkName: "components" */ "./components/invoices/InvoiceViewComponent.vue"),
        //     meta: { title: "Invoice Details", admin: false }
        // },
        {
            path: "/recurrings",
            component: () => import(/* webpackChunkName: "components" */ "./components/invoices/RecurringInvoiceListComponent.vue"),
            meta: { title: "Recurring Invoices", admin: false, customer: true }
        },
        {
            path: "/recurrings/add",
            component: () => import(/* webpackChunkName: "components" */ "./components/invoices/RecurringInvoiceFormComponent.vue"),
            meta: { title: "Add New Recurring Invoice", save: true, admin: false }
        },
        {
            path: "/recurrings/edit/:id",
            component: () => import(/* webpackChunkName: "components" */ "./components/invoices/RecurringInvoiceFormComponent.vue"),
            meta: { title: "Edit Recurring Invoice", save: true, admin: true }
        },
        {
            path: "/purchases",
            component: () => import(/* webpackChunkName: "components" */ "./components/purchases/PurchaseListComponent.vue"),
            meta: { title: "Purchases", admin: false, vendor: true }
        },
        {
            path: "/purchases/add",
            component: () => import(/* webpackChunkName: "components" */ "./components/purchases/PurchaseFormComponent.vue"),
            meta: { title: "Add New Purchase", admin: false }
        },
        {
            path: "/purchases/edit/:id",
            component: () => import(/* webpackChunkName: "components" */ "./components/purchases/PurchaseFormComponent.vue"),
            meta: { title: "Edit Purchase", admin: true }
        },
        {
            path: "/payments",
            component: () => import(/* webpackChunkName: "components" */ "./components/payments/PaymentListComponent.vue"),
            meta: { title: "Payments", admin: false, customer: true, vendor: true },
            children: [
                {
                    path: "add",
                    component: () => import(/* webpackChunkName: "components" */ "./components/payments/PaymentFormComponent.vue"),
                    meta: { title: "Add Payment", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/payments/PaymentFormComponent.vue"),
                    meta: { title: "Edit Payment", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/payments/PaymentViewComponent.vue"),
                    meta: { title: "View Payment", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/products",
            component: () => import(/* webpackChunkName: "components" */ "./components/products/ProductListComponent.vue"),
            meta: { title: "Products", admin: false },
            children: [
                {
                    path: "add",
                    component: () => import(/* webpackChunkName: "components" */ "./components/products/ProductFormComponent.vue"),
                    meta: { title: "Add Product", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/products/ProductFormComponent.vue"),
                    meta: { title: "Edit Product", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/products/ProductViewComponent.vue"),
                    meta: { title: "View Product", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/events/eventsList",
            component: () => import(/* webpackChunkName: "components" */ "./components/events/EventsListComponent.vue"),
            meta: { title: "Events", admin: false },
            children: [
                // {
                //     path: "/events/add",
                //     component: () => import(/* webpackChunkName: "components" */ "./components/calendar/EventFormModal.vue"),
                //     meta: { title: "Add Event", admin: false, modal: true }
                // },
                {
                    path: "edit/:id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/events/EventsListComponent.vue"),
                    meta: { title: "Edit Events", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/products/ProductViewComponent.vue"),
                    meta: { title: "View Product", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/calendar-tasks",
            component: () => import("./components/tasks/TaskFormModal.vue"),
            meta: { title: "Tasks", admin: false },
            children: [
                {
                    name: 'calendar-task',
                    path: "edit/:id",
                    component: () => import("./components/tasks/TaskFormModal.vue"),
                    meta: { title: "Edit Task From Calender", admin: true, modal: true }
                },

            ]
        },
        {
            path: "/tasks",
            component: () => import(/* webpackChunkName: "components" */ "./components/tasks/TaskListComponent.vue"),
            meta: { title: "Tasks", admin: false },
            children: [
                {
                    path: "add",
                    component: () => import(/* webpackChunkName: "components" */ "./components/tasks/TaskFormModal.vue"),
                    meta: { title: "Add Task", admin: false, modal: true }
                },
                {
                    path: "customer/add/:id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/tasks/TaskFormModal.vue"),
                    meta: { title: "Add Task", admin: false, modal: true }
                },
                {
                    path: "project/add/:id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/tasks/TaskFormModal.vue"),
                    meta: { title: "Add Task", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/tasks/TaskFormModal.vue"),
                    meta: { title: "Edit Task", admin: true, modal: true }
                },
                {
                    path: "edit/email/:id",
                    name: "notification-task",
                    component: () => import(/* webpackChunkName: "components" */ "./components/tasks/TaskFormModal.vue"),
                    meta: { title: "Edit Task", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/tasks/TaskViewComponent.vue"),
                    meta: { title: "View Task", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/projects",
            component: () => import("./components/projects/ProjectListComponent.vue"),
            meta: { title: "Projects", admin: false },
            children: [
                {
                    path: "add",
                    component: () => import("./components/projects/ProjectFormModal.vue"),
                    meta: { title: "Add Project", admin: false, modal: true }
                },
                {
                    path: "customer/add/:id",
                    component: () => import("./components/projects/ProjectFormModal.vue"),
                    meta: { title: "Add Project", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    component: () => import("./components/projects/ProjectFormModal.vue"),
                    meta: { title: "Edit Project", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: () => import("./components/projects/ProjectViewComponent.vue"),
                    meta: { title: "View Project", admin: false, modal: true }
                },

            ]
        },
        {
            path: "/projects/tasks/:id",
            component: () => import(/* webpackChunkName: "components" */ "./components/tasks/TaskListComponent.vue"),
            meta: { title: "Task with projects", admin: true }
        },
        {
            path: "/categories",
            component: () => import(/* webpackChunkName: "components" */ "./components/categories/CategoryListComponent.vue"),
            meta: { title: "Categories", admin: true },
            children: [
                {
                    path: "add",
                    component: () => import(/* webpackChunkName: "components" */ "./components/categories/CategoryFormComponent.vue"),
                    meta: { title: "Add Category", admin: true }
                },
                {
                    path: "edit/:id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/categories/CategoryFormComponent.vue"),
                    meta: { title: "Edit Category", admin: true }
                }
            ]
        },
        {
            path: "/customer",
            component: () => import(/* webpackChunkName: "components" */ "./components/customers/CustomerComponent.vue"),
            meta: { title: "Customer Company Details", admin: false, customer: true }
        },
        {
            path: "/customer/contact/:id",
            name: 'contact',
            component: () => import(/* webpackChunkName: "components" */ "./components/customers/CustomerViewComponent.vue"),
            meta: { title: "View Customer", admin: false, modal: true }
        },
        {
            path: "/customers",
            component: () => import(/* webpackChunkName: "components" */ "./components/customers/CustomerListComponent.vue"),
            meta: { title: "Customers", admin: false },
            children: [
                {
                    path: "add",
                    name: 'customer',
                    component: () => import(/* webpackChunkName: "components" */ "./components/customers/CustomerFormComponent.vue"),
                    meta: { title: "Add Customer", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    name: 'customer',
                    component: () => import(/* webpackChunkName: "components" */ "./components/customers/CustomerFormComponent.vue"),
                    meta: { title: "Edit Customer", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/customers/CustomerViewComponent.vue"),
                    meta: { title: "View Customer", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/contacts",
            component: () => import(/* webpackChunkName: "components" */ "./components/customers/CustomerListComponent.vue"),
            meta: { title: "Customers", admin: false },
            children: [
                {
                    path: "add",
                    name: 'contact',
                    component: () => import(/* webpackChunkName: "components" */ "./components/customers/ContactFormComponent.vue"),
                    meta: { title: "Add Contacts", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    name: 'contact',
                    component: () => import(/* webpackChunkName: "components" */ "./components/customers/ContactFormComponent.vue"),
                    meta: { title: "Edit Customer", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/customers/CustomerViewComponent.vue"),
                    meta: { title: "View Customer", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/leads",
            component: () => import(/* webpackChunkName: "components" */ "./components/leads/LeadListComponent.vue"),
            meta: { title: "Leads", admin: false },
            children: [
                {
                    path: "add",
                    name: 'lead',
                    component: () => import(/* webpackChunkName: "components" */ "./components/customers/CustomerFormComponent.vue"),
                    meta: { title: "Add Lead", admin: false, modal: true,  lead: true}
                },
                {
                    path: "edit/:id",
                    name: 'lead',
                    component: () => import(/* webpackChunkName: "components" */ "./components/customers/CustomerFormComponent.vue"),
                    meta: { title: "Edit Lead", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/customers/CustomerViewComponent.vue"),
                    meta: { title: "View Lead", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/customers/transactions/:id",
            component: () => import(/* webpackChunkName: "components" */ "./components/customers/TransactionsComponent.vue"),
            meta: { title: "List Customer Transactions", admin: true }
        },
        {
            path: "/customers/tasks/:id",
            component: () => import(/* webpackChunkName: "components" */ "./components/tasks/TaskListComponent.vue"),
            meta: { title: "Tasks Customer ", admin: true }
        },
        {
            path: "/incomes",
            component: () => import(/* webpackChunkName: "components" */ "./components/incomes/IncomeListComponent.vue"),
            meta: { title: "Income", admin: false },
            children: [
                {
                    path: "add",
                    component: () => import(/* webpackChunkName: "components" */ "./components/incomes/IncomeFormComponent.vue"),
                    meta: { title: "Add Income", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/incomes/IncomeFormComponent.vue"),
                    meta: { title: "Edit Income", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/incomes/IncomeViewComponent.vue"),
                    meta: { title: "View Income", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/notes",
            component: () => import( /* webpackChunkName: "components" */ "./components/notes/NotesListComponent.vue"),
            meta: { title: "Note", admin: false },
            children: [
                {
                    path: "add",
                    component: () => import(/* webpackChunkName: "components" */ "./components/notes/NotesFormComponent.vue"),
                    meta: { title: "הוספת פתק", admin: true, modal: true }
                },
                {
                    path: "edit/:id",
                    component: () =>
                        import(/* webpackChunkName: "components" */ "./components/notes/NotesFormComponent.vue"),
                    meta: {
                        title: "עריכת  פתק",
                        admin: true,
                        modal: true
                    }
                }
            ]
        },
        {
            path: "/expenses",
            component: () => import(/* webpackChunkName: "components" */ "./components/expenses/ExpenseListComponent.vue"),
            meta: { title: "Expenses", admin: false },
            children: [
                {
                    path: "add",
                    component: () => import(/* webpackChunkName: "components" */ "./components/expenses/ExpenseFormComponent.vue"),
                    meta: { title: "Add Expense", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/expenses/ExpenseFormComponent.vue"),
                    meta: { title: "Edit Expense", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/expenses/ExpenseViewComponent.vue"),
                    meta: { title: "View Expense", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/accounts",
            component: () => import(/* webpackChunkName: "components" */ "./components/accounts/AccountListComponent.vue"),
            meta: { title: "Accounts", admin: true },
            children: [
                {
                    path: "add",
                    component: () => import(/* webpackChunkName: "components" */ "./components/accounts/AccountFormComponent.vue"),
                    meta: { title: "Add Account", admin: true, modal: true }
                },
                {
                    path: "edit/:id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/accounts/AccountFormComponent.vue"),
                    meta: { title: "Edit Account", admin: true, modal: true }
                }
            ]
        },
        // {
        //     path: "/transactions",
        //     component: () => import(/* webpackChunkName: "components" */ "./components/accounts/TransactionsComponent.vue"),
        //     meta: { title: "List All Transactions", admin: true }
        // },
        {
            path: "/accounts/transactions/:id",
            component: () => import(/* webpackChunkName: "components" */ "./components/accounts/TransactionsComponent.vue"),
            meta: { title: "List Account Transactions", admin: true }
        },
        {
            path: "/vendor",
            component: () => import(/* webpackChunkName: "components" */ "./components/vendors/VendorComponent.vue"),
            meta: { title: "Vendor Company Details", admin: false, vendor: true }
        },
        {
            path: "/vendors",
            component: () => import(/* webpackChunkName: "components" */ "./components/vendors/VendorListComponent.vue"),
            meta: { title: "Vendors", admin: false },
            children: [
                {
                    path: "add",
                    component: () => import(/* webpackChunkName: "components" */ "./components/vendors/VendorFormComponent.vue"),
                    meta: { title: "Add Vendor", admin: false, modal: true }
                },
                {
                    path: "edit/:id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/vendors/VendorFormComponent.vue"),
                    meta: { title: "Edit Vendor", admin: true, modal: true }
                },
                {
                    path: ":id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/vendors/VendorViewComponent.vue"),
                    meta: { title: "View Vendor", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/vendors/transactions/:id",
            component: () => import(/* webpackChunkName: "components" */ "./components/vendors/TransactionsComponent.vue"),
            meta: { title: "List Customer Transactions", admin: true }
        },
        {
            path: "/users",
            component: () => import(/* webpackChunkName: "components" */ "./components/users/UserListComponent.vue"),
            meta: { title: "Users", admin: false, super: true },
            children: [
                {
                    path: "add",
                    component: () => import(/* webpackChunkName: "components" */ "./components/users/UserFormComponent.vue"),
                    meta: { title: "Add User", admin: false, super: true, modal: true }
                },
                {
                    path: "edit/:username",
                    component: () => import(/* webpackChunkName: "components" */ "./components/users/UserFormComponent.vue"),
                    meta: { title: "Edit User", admin: false, super: true, modal: true }
                }
            ]
        },
        {
            path: "/transfers",
            component: () => import(/* webpackChunkName: "components" */ "./components/transfers/TransferListComponent.vue"),
            meta: { title: "Transfers", admin: true },
            children: [
                {
                    path: "add",
                    component: () => import(/* webpackChunkName: "components" */ "./components/transfers/TransferFormComponent.vue"),
                    meta: { title: "Add Transfer", admin: true, modal: true }
                },
                {
                    path: "edit/:id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/transfers/TransferFormComponent.vue"),
                    meta: { title: "Edit Transfer", admin: true, modal: true }
                }
            ]
        },
        {
            path: "/taxes",
            component: () => import(/* webpackChunkName: "components" */ "./components/taxes/TaxListComponent.vue"),
            meta: { title: "Taxes", admin: true },
            children: [
                {
                    path: "add",
                    component: () => import(/* webpackChunkName: "components" */ "./components/taxes/TaxFormComponent.vue"),
                    meta: { title: "Add Tax", admin: true, modal: true }
                },
                {
                    path: "edit/:id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/taxes/TaxFormComponent.vue"),
                    meta: { title: "Edit Tax", admin: true, modal: true }
                }
            ]
        },
        {
            path: "/settings",
            component: () => import(/* webpackChunkName: "components" */ "./components/ParentRouteOutletComponent.vue"),
            meta: { title: "Settings", admin: true },
            children: [
                {
                    path: "/",
                    component: () => import(/* webpackChunkName: "components" */ "./components/settings/AppSettingsComponent.vue"),
                    meta: { title: "Application Settings", admin: false, super: true }
                },
                {
                    path: "system",
                    component: () => import(/* webpackChunkName: "components" */ "./components/settings/SystemSettingsComponent.vue"),
                    meta: { title: "System Settings", admin: false, super: true }
                },
                {
                    path: "email_settings",
                    component: () => import(/* webpackChunkName: "components" */ "./components/settings/EmailSettingsComponent.vue"),
                    meta: { title: "System Settings", admin: false, super: true }
                },
                {
                    path: "email_templates",
                    component: () => import(/* webpackChunkName: "components" */ "./components/settings/EmailTemplatesComponent.vue"),
                    meta: { title: "Email Templates", admin: false, super: true }
                },
                {
                    path: "invoice_settings",
                    component: () => import(/* webpackChunkName: "components" */ "./components/settings/InvoiceSettingsComponent.vue"),
                    meta: { title: "Invoice Settings", admin: false, super: true }
                },
                {
                    path: "statuses",
                    component: () => import(/* webpackChunkName: "components" */ "./components/settings/StatusComponent.vue"),
                    meta: { title: "Statuses", admin: false, super: true }
                },
                {
                    path: "statuses",
                    component: () => import(/* webpackChunkName: "components" */ "./components/settings/StatusFormComponent.vue"),
                    meta: { title: "Statuses", admin: true },
                    children: [
                        {
                            path: "add",
                            component: () =>
                                import(/* webpackChunkName: "components" */ "./components/settings/StatusFormComponent.vue"),
                            meta: {
                                title: "הוספת סטטוס",
                                admin: true,
                                modal: true
                            }
                        },
                        {
                            path: "edit/:id",
                            component: () =>
                                import(/* webpackChunkName: "components" */ "./components/settings/StatusFormComponent.vue"),
                            meta: {
                                title: "עריכת סטטוס",
                                admin: true,
                                modal: true
                            }
                        }
                    ]
                },
                {
                    path: "arrival-source",
                    component: () => import("./components/settings/SourcesOfArrivalListComponent.vue"),
                    meta: { title: "arrivalSource", admin: false, super: true }
                },
                {
                    path: "arrival-source",
                    component: () => import("./components/settings/SourcesOfArrivalFormComponent.vue"),
                    meta: { title: "arrivalSource", admin: true },
                    children: [
                        {
                            path: "add",
                            component: () =>
                                import(/* webpackChunkName: "components" */ "./components/settings/SourcesOfArrivalFormComponent.vue"),
                            meta: {
                                title: "הוספת מקור הגעה",
                                admin: true,
                                modal: true
                            }
                        },
                        {
                            path: "edit/:id",
                            component: () =>
                                import(/* webpackChunkName: "components" */ "./components/settings/SourcesOfArrivalFormComponent.vue"),
                            meta: {
                                title: "עריכת מקור הגעה",
                                admin: true,
                                modal: true
                            }
                        }
                    ]
                },
                {
                    path: "notes-categories",
                    component: () => import("./components/settings/NotesCategoriesListComponent.vue"),
                    meta: { title: "notesCategories", admin: false, super: true }
                },
                {
                    path: "notes-categories",
                    component: () => import("./components/settings/NotesCategoriesFormComponent.vue"),
                    meta: { title: "notesCategories", admin: true },
                    children: [
                        {
                            path: "add",
                            component: () =>
                                import(/* webpackChunkName: "components" */ "./components/settings/NotesCategoriesFormComponent.vue"),
                            meta: {
                                title: "הוספת תגיות לפתקים",
                                admin: true,
                                modal: true
                            }
                        },
                        {
                            path: "edit/:id",
                            component: () =>
                                import(/* webpackChunkName: "components" */ "./components/settings/NotesCategoriesFormComponent.vue"),
                            meta: {
                                title: "עריכת תגיות לפתקים",
                                admin: true,
                                modal: true
                            }
                        }
                    ]
                },
                {
                    path: "fields",
                    component: () => import(/* webpackChunkName: "components" */ "./components/settings/CustomFieldsComponent.vue"),
                    meta: { title: "Custom Fields", admin: true },
                    children: [
                        {
                            path: "add",
                            component: () =>
                                import(/* webpackChunkName: "components" */ "./components/settings/CustomFieldFormComponent.vue"),
                            meta: {
                                title: "Add Custom Field",
                                admin: true,
                                modal: true
                            }
                        },
                        {
                            path: "edit/:id",
                            component: () =>
                                import(/* webpackChunkName: "components" */ "./components/settings/CustomFieldFormComponent.vue"),
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
            path: "/logs",
            component: () => import(/* webpackChunkName: "components" */ "./components/utilities/LogListComponent.vue"),
            meta: { title: "Activity Logs", admin: false, super: true },
            children: [
                {
                    path: ":id",
                    component: () => import(/* webpackChunkName: "components" */ "./components/utilities/LogViewComponent.vue"),
                    meta: { title: "Activity Log", admin: false, modal: true }
                }
            ]
        },
        {
            path: "/report",
            component: () => import(/* webpackChunkName: "components" */ "./components/reports/ReportComponent.vue"),
            meta: { title: "Report", admin: true }
        },
        {
            path: "/login",
            component: LoginComponent,
            meta: { title: "Login", admin: false, customer: true, vendor: true }
        },
        {
            path: "*",
            component: NotFoundComponent,
            meta: { title: "Page Not Found", admin: false, customer: true, vendor: true }
        }
    ],
    linkActiveClass: "is-active",
    scrollBehavior: () => ({ y: 0 })
});

export default router;
