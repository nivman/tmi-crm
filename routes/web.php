<?php

// Home
Route::view('/', 'home');
Route::view('/message', 'message');
Route::get('/data', 'AjaxController@data');
Route::get('/manifest', 'AjaxController@manifest');
Route::post('/pay/{gateway}/{hash}', 'PublicController@pay');
Route::get('/uploads/payments/{file}', 'AjaxController@file');
Route::get('/view/{act}/{hash}', 'PublicController@index')->name('order');

// PayPal Standard
Route::post('/paypal/ipn', 'PublicController@ipn');
Route::get('/paypal/{hash}', 'PublicController@paypal');
Route::get('/paypal/{hash}/completed', 'PublicController@completed');

// App
Route::prefix('app')->middleware(['auth'])->group(function () {
    Route::get('products/search', 'ProductsController@search');
    // Route::get('transactions', 'TransactionsController@index');settings

    Route::get('dashboard/vendor', 'DashboardController@vendor');
    Route::get('charts/pie_chart', 'DashboardController@pieChart');
    Route::get('charts/bar_chart', 'DashboardController@barChart');
    Route::get('charts/line_chart', 'DashboardController@lineChart');
    Route::get('dashboard/customer', 'DashboardController@customer');
    Route::post('invoices/email/{invoice}', 'InvoicesController@email');
    Route::post('payments/email/{payment}', 'PaymentsController@email');
    Route::post('purchases/email/{purchase}', 'PurchasesController@email');

    Route::get('taxes', 'TaxesController@index');
    Route::get('accounts', 'AccountsController@index');
    Route::put('users/{user}', 'UsersController@update');
    Route::get('vendors/search', 'VendorsController@search');
    Route::put('vendor/{vendor}', 'VendorsController@update');
    Route::get('customers/search', 'CustomersController@search');
    Route::post('customers-projects/{ids}', 'ProjectsController@getProjectsFormCustomersByIds');
    Route::get('customers/projects/{id}', 'ProjectsController@getCustomerProjectsList');
    Route::post('customers-contacts/{ids}', 'ContactsController@getContactsCustomerById');
    Route::put('customer/{customer}', 'CustomersController@update');
    Route::get('customer/contact/{customerId}', 'CustomersController@getCustomerByContactId');
    Route::get('contacts/search', 'ContactsController@search');
    Route::get('projects/search', 'ProjectsController@search');
    Route::post('project-customers/{ids}', 'CustomersController@getCustomersByIds');
    Route::get('customer/{id}', 'CustomersController@getCustomerById');
    Route::get('contacts/{customerId}', 'ContactsController@getContactByCustomerId');
    Route::get('contacts/details/{customerId}', 'ContactsController@getContactsDetailsByCustomerId');
    Route::get('contact/{contact}', 'ContactsController@show');
    Route::put('contacts/edit/{contact}', 'ContactsController@update');
    Route::resource('categories', 'CategoriesController')->only(['index', 'show']);
    Route::get('tasks/search', 'TasksController@search');
    Route::get('invoices/{invoice}/payments', 'InvoicesController@payments');
    Route::get('purchases/{purchase}/payments', 'PurchasesController@payments');

    Route::get('events/eventsList', 'EventsController@list');
    Route::get('events/eventsList/add', 'EventsController@list');
    Route::post('events/calender/dates', 'EventsController@updateCalendarDates');
    Route::post('tasks/calender/dates', 'TasksController@updateCalendarDates');
    Route::get('events/create', 'EventsController@eventsTypes');
  //  Route::delete('events/eventsTypes', 'EventsController@eventsTypes');
    Route::delete('events/delete/{event}', 'EventsController@destroy');
    Route::resource('events', 'EventsController');
    Route::get('tasks/details/{term}/{id}', 'TasksController@details');

    Route::get('tasks/customers/{id}', 'TasksController@getCustomer');
    Route::get('tasks/projects/{id}', 'TasksController@getProject');
    Route::get('customers/tasks/{id}', 'TasksController@getCustomerTasks');
    Route::get('customers/leads', 'CustomersController@index');
    Route::get('files/customers/{customerId}', 'FilesController@showCustomerFiles');
    Route::resource('files/show', 'FilesController');
    Route::get('files/download', 'FilesController@downloadFile');
    Route::get('files/delete/{id}', 'FilesController@deleteFile');
    Route::post('file/customers', 'CustomersController@saveCustomerFile');
    Route::post('tasks/add', 'TasksController@store');
    Route::resource('tasks', 'TasksController');

    Route::get('projects/', 'ProjectsController@edit');
    Route::post('projects/add', 'ProjectsController@store');
    Route::get('projects/search', 'ProjectsController@search');
    Route::get('projects/tasks/{id}', 'TasksController@getProjectTasks');
    Route::get('projects/events/{id}', 'EventsController@getProjectEvents');
    Route::get('customers/events/{id}', 'EventsController@getCustomersEvents');
    Route::resource('projects', 'ProjectsController');
    Route::resource('companies', 'CompaniesController')->only(['show']);
    Route::post('users/change_password', 'UsersController@changePassword');
    Route::resource('incomes', 'IncomesController')->except(['update', 'destroy']);
    Route::resource('vendors', 'VendorsController')->except(['update', 'destroy']);
    Route::resource('products', 'ProductsController')->except(['update', 'destroy']);
    Route::resource('expenses', 'ExpensesController')->except(['update', 'destroy']);
    Route::resource('invoices', 'InvoicesController')->except(['update', 'destroy']);
    Route::resource('payments', 'PaymentsController')->except(['update', 'destroy']);
    Route::resource('purchases', 'PurchasesController')->except(['update', 'destroy']);
    Route::resource('customers', 'CustomersController')->except(['update', 'destroy']);
    Route::resource('contacts', 'ContactsController')->except(['update', 'destroy']);
    Route::resource('recurrings', 'RecurringsController')->except(['update', 'destroy']);

    Route::middleware(['role:admin'])->group(function () {
        Route::post('report', 'ReportController@index');
        Route::resource('incomes', 'IncomesController')->only(['update']);
        Route::resource('vendors', 'VendorsController')->only(['update']);
        Route::resource('products', 'ProductsController')->only(['update']);
        Route::resource('expenses', 'ExpensesController')->only(['update']);
        Route::resource('invoices', 'InvoicesController')->only(['update']);
        Route::resource('payments', 'PaymentsController')->only(['update']);
        Route::resource('purchases', 'PurchasesController')->only(['update']);
        Route::resource('customers', 'CustomersController')->only(['update']);
        Route::resource('recurrings', 'RecurringsController')->only(['update']);

        Route::get('vendors/transactions/{vendor}', 'TransactionsController@vendor');
        Route::get('accounts/transactions/{account}', 'TransactionsController@account');
        Route::get('customers/transactions/{customer}', 'TransactionsController@customer');

        Route::resource('transfers', 'TransfersController')->except(['destroy']);
        Route::resource('taxes', 'TaxesController')->except(['index', 'destroy']);
        Route::resource('custom_fields', 'CustomFieldsController')->except(['destroy']);
        Route::resource('categories', 'CategoriesController')->except(['index', 'show', 'destroy']);
    });

    Route::middleware(['role:super'])->group(function () {
        Route::resource('users', 'UsersController');
        Route::get('roles', 'UsersController@roles');
        Route::get('logs', 'UtilitiesController@logs');
        Route::get('profile/{username}', 'UsersController@show');
        Route::post('settings/system/update', 'SettingsController@update');
        Route::get('settings/system', 'SettingsController@show');
        Route::post('app/system', 'SettingsController@update');
        Route::get('logs/{activity}', 'UtilitiesController@showLog');
        Route::resource('taxes', 'TaxesController')->only(['destroy']);
        Route::resource('incomes', 'IncomesController')->only(['destroy']);
        Route::resource('vendors', 'VendorsController')->only(['destroy']);
        Route::resource('accounts', 'AccountsController')->except(['index']);
        Route::resource('expenses', 'ExpensesController')->only(['destroy']);
        Route::resource('invoices', 'InvoicesController')->only(['destroy']);
        Route::resource('payments', 'PaymentsController')->only(['destroy']);
        Route::resource('products', 'ProductsController')->only(['destroy']);
        Route::resource('companies', 'CompaniesController')->only(['update']);
        Route::resource('purchases', 'PurchasesController')->only(['destroy']);
        Route::resource('customers', 'CustomersController')->only(['destroy']);
        Route::resource('transfers', 'TransfersController')->only(['destroy']);
        Route::resource('categories', 'CategoriesController')->only(['destroy']);
        Route::resource('recurrings', 'RecurringsController')->only(['destroy']);
        Route::resource('settings', 'SettingsController')->only(['index', 'store']);
        Route::resource('custom_fields', 'CustomFieldsController')->only(['destroy']);
        Route::resource('statuses', 'StatusController')->only(['index']);
        Route::post('arrival-source/create', 'SourcesOfArrivalController@create');
        Route::resource('arrival-source', 'SourcesOfArrivalController');
        Route::post('statuses/create', 'StatusController@store');
        Route::post('statuses/edit/{status}', 'StatusController@update');
        Route::delete('statuses/delete/{status}', 'StatusController@destroy');
        Route::delete('customers/delete/{customer}', 'CustomersController@destroy');
        Route::delete('customers/leads/delete/{customer}', 'CustomersController@destroy');
        Route::delete('tasks/delete/{task}', 'TasksController@destroy');
        Route::delete('expenses/delete/{expense}', 'ExpensesController@destroy');
        Route::delete('categories/delete/{category}', 'CategoriesController@destroy');
        Route::get('tasks/details', 'TasksController@details');
        Route::get('statuses/{status}', 'StatusController@edit');
        Route::get('templates/{template?}', 'EmailTemplateController@show');
        Route::put('templates/{template?}', 'EmailTemplateController@update');
        Route::get('emails', 'mail\EmailController@create');
        Route::resource('history', 'StatusHistoryController');
        Route::get('history/status/{entity_type}/{entity_id}', 'StatusHistoryController@get');

    });

    Route::get('token', 'AjaxController@token');
    Route::get('states', 'AjaxController@states');
    Route::get('countries', 'AjaxController@countries');
});

// Auth
Route::redirect('/login', '/')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::get('/password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');

// Update route
Route::middleware(['role:super'])->group(function () {
    Route::get('/update/database', 'Update\UpdateController');
});

// Redirect all others
// Auth::routes(['verify' => true]);
Route::view('/{any}', 'home')->where('any', '.*');
