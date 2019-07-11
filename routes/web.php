<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::post('exporting', 'CompanyRouteController@import')->name('import-rejigged-routes');

Route::get('/', function () {
    return view('welcome');
});
Route::get('import-csv', function () {
    return view('importCSV');
});
Route::get('products', ['as' => 'products', function () {
    return view('products');
}]);
Route::get('preferences', ['as' => 'preferences', function () {
    return view('preferences');
}]);
Route::get('routes', function () {
    return view('routes');
});
Route::get('berry-picklists', function () {
    return view('exports.berry-picklists');
});
Route::get('import-file', function () {
    return view('import-file');
});
Route::get('import-products', ['as' => 'import-products', function () {
    return view('process-snackboxes');
}]);
Route::get('companies/new', ['as' => 'new-company', function () {
    return view('new-company');
}]);
Route::get('snackboxes/new', ['as' => 'snackboxes', function () {
    return view('snackbox-creation');
}]);
Route::get('fruit-partners', ['as' => 'fruit-partners', function () {
    return view('fruit-partners');
}]);
Route::get('assigned-routes', ['as' => 'assigned-routes', function () {
    return view('assigned-routes');
}]);
Route::get('office-pantry-products', ['as' => 'office-pantry-products', function () {
    return view('office-pantry-products');
}]);
Route::get('invoicing', ['as' => 'invoicing', function () {
    return view('invoicing');
}]);
Route::get('cron', ['as' => 'cron', function () {
    return view('cron');
}]);

// old system week start added to importing/exporting processes.
Route::get('import-file', 'WeekStartController@show')->name('import-file')->middleware('auth:office');
// new system week start added to exporting page <-- Not sure I really need to pass it like this though? Going to do it a little differently this time.
Route::get('exporting', 'WeekStartController@showAndSet')->name('exporting')->middleware('auth:office');
// l;ooks like we still need one of these to pull the templating elements in.
Route::get('exporting', ['as' => 'exporting', function () {
    return view('exporting');
}]);


// Route::get('import-products', 'SnackBoxController@upload_products_and_codes')->name('import-products')->middleware('auth:office');
Route::get('display-routes', 'RoutesController@index');
// Route::get('snackboxes-multi-company', 'SnackBoxController@index_OP');
Route::get('snackboxes-multi-company', 'SnackBoxController@auto_process_snackboxes');

Route::get('companies', 'CompaniesController@index');

// Route::get('products', 'ProductsController@index');
Route::group(['middleware' => 'web'], function () {

    //----- Old system Processing and Export for Snackboxes -----//
        Route::get('auto_process_snackboxes', 'SnackBoxController@auto_process_snackboxes');
    //----- End of Old system Processing and Export for Snackboxes -----//

    //----- Snackbox Exports for Companies Receiving 1 Box Only -----//
        // These are for exporting the full week of orders
        Route::get('export-snackbox-weekly-op-multicompany', 'SnackBoxController@download_snackbox_weekly_op_multicompany')->name('SnackboxOPMultiCompany');
        Route::get('export-snackbox-weekly-dpd-multicompany', 'SnackBoxController@download_snackbox_weekly_dpd_multicompany')->name('SnackboxDPDMultiCompany');
        Route::get('export-snackbox-weekly-apc-multicompany', 'SnackBoxController@download_snackbox_weekly_apc_multicompany')->name('SnackboxAPCMultiCompany');
        // Whereas these will only export the selected days
        Route::get('export-snackbox-op-multicompany', 'SnackBoxController@download_snackbox_op_multicompany')->name('SnackboxOPMultiCompany');
        Route::get('export-snackbox-dpd-multicompany', 'SnackBoxController@download_snackbox_dpd_multicompany')->name('SnackboxDPDMultiCompany');
        Route::get('export-snackbox-apc-multicompany', 'SnackBoxController@download_snackbox_apc_multicompany')->name('SnackboxAPCMultiCompany');
    //----- End of Snackbox Exports for Companies Receiving 1 Box Only -----//

    //----- Snackbox Exports for Companies Receiving Multiple Boxes -----//
        // These are for exporting the full week of orders
        Route::get('export-snackbox-weekly-op-singlecompany', 'SnackBoxController@download_snackbox_weekly_op_singlecompany');
        Route::get('export-snackbox-weekly-dpd-singlecompany', 'SnackBoxController@download_snackbox_weekly_dpd_singlecompany');
        Route::get('export-snackbox-weekly-apc-singlecompany', 'SnackBoxController@download_snackbox_weekly_apc_singlecompany');
        // Whereas these will only export the selected days
        Route::get('export-snackbox-op-singlecompany', 'SnackBoxController@download_snackbox_op_singlecompany');
        Route::get('export-snackbox-dpd-singlecompany', 'SnackBoxController@download_snackbox_dpd_singlecompany');
        Route::get('export-snackbox-apc-singlecompany', 'SnackBoxController@download_snackbox_apc_singlecompany');
    //----- End of Snackbox Exports for Companies Receiving Multiple Boxes -----//

    //----- Unique Snackboxes for special items such as peanut butter, himalayan salt and cereal etc -----//
        // Companies receiving two or more boxes with the same contents
        Route::get('export-snackbox-unique-op-singlecompany', 'SnackBoxController@download_snackbox_unique_op_singlecompany');
        Route::get('export-snackbox-unique-dpd-singlecompany', 'SnackBoxController@download_snackbox_unique_dpd_singlecompany');
        Route::get('export-snackbox-unique-apc-singlecompany', 'SnackBoxController@download_snackbox_unique_apc_singlecompany');
        // Companies just receiving one box of any snackbox_id, with type 'unique'.
        Route::get('export-snackbox-unique-op-multicompany', 'SnackBoxController@download_snackbox_unique_op_multicompany');
        Route::get('export-snackbox-unique-dpd-multicompany', 'SnackBoxController@download_snackbox_unique_dpd_multicompany');
        Route::get('export-snackbox-unique-apc-multicompany', 'SnackBoxController@download_snackbox_unique_apc_multicompany');

    //----- End of Unique Snackboxes for special items such as peanut butter, himalayan salt and cereal etc -----//

    //----- These Unique routes were previously used to handle drink orders, now fulfilled by the drinkbox controller -----//
        // Route::get('export-snackbox-op-unique', 'SnackBoxController@download_snackbox_op_unique')->name('SnackboxOPUnique');
        // Route::get('export-snackbox-dpd-unique', 'SnackBoxController@download_snackbox_dpd_unique')->name('SnackboxDPDUnique');
        // Route::get('export-snackbox-apc-unique', 'SnackBoxController@download_snackbox_apc_unique')->name('SnackboxAPCUnique');
    //----- Unique was previously used to handle drink orders, now fulfilled by the drinkbox controller -----//

    //----- Otherbox Exports -----//
        Route::get('export-otherbox-op-multicompany', 'OtherBoxController@download_otherbox_op_multicompany');
        Route::get('export-otherbox-checklist-op', 'OtherBoxController@download_otherbox_checklist_op');
        Route::get('export-otherbox-checklist-op-weekly-total', 'OtherBoxController@download_otherbox_checklist_weekly_total_op');
    //----- End of Otherbox Exports -----//

    //----- These handle the exporting of Wholesale Items for Snackboxes & Drinkboxes* (* - Always Wholesale) -----//
        // Selected Delivery Day Exports
        Route::get('export-wholesale-drinkbox-op-multicompany', 'DrinkBoxController@download_drinkbox_wholesale_op_multicompany');
        Route::get('export-wholesale-snackbox-op-singlecompany', 'SnackBoxController@download_snackbox_wholesale_op_singlecompany')->name('SnackboxWholesaleOPSingleCompany');
        // Weekly Export
        Route::get('export-wholesale-weekly-drinkbox-op-multicompany', 'DrinkBoxController@download_drinkbox_wholesale_weekly_op_multicompany');
        Route::get('export-wholesale-weekly-snackbox-op-singlecompany', 'SnackBoxController@download_snackbox_wholesale_weekly_op_singlecompany');
    //----- End of exporting Wholesale Items for Snackboxes & Drinkboxes* (* - Always Wholesale) -----//

    //----- Finally, hopefully, maybe? Mystery Special Items -----//

        // As they could be any of the main product groups, I'm going to resolve processing for these in the orders controller.

        // Selected Delivery Day Exports
        Route::get('export-monthly-special-op', 'MonthlySpecialController@download_monthly_special_op');
        Route::get('export-monthly-special-dpd', 'MonthlySpecialController@download_monthly_special_dpd');
        Route::get('export-monthly-special-apc', 'MonthlySpecialController@download_monthly_special_apc');
        // Weekly Exports
        Route::get('export-monthly-special-op-weekly', 'MonthlySpecialController@download_monthly_special_op_weekly');
        Route::get('export-monthly-special-dpd-weekly', 'MonthlySpecialController@download_monthly_special_dpd_weekly');
        Route::get('export-monthly-special-apc-weekly', 'MonthlySpecialController@download_monthly_special_apc_weekly');

    //----- End of Finally, hopefully, maybe? Mystery Special Items -----//

    //----- Run the empty and archive snackboxes -----//

        // Strip out orders from snackboxes, archive previous contents.
        Route::get('snackboxes/archive-and-empty', 'SnackBoxController@archiveAndEmptySnackBoxes');
        // Same for drinks
        Route::get('drinkboxes/archive-and-empty', 'DrinkBoxController@archiveAndEmptyDrinkBoxes');
        // And otherbox
        Route::get('otherboxes/archive-and-empty', 'OtherBoxController@archiveAndEmptyOtherBoxes');
    
    //----- End of Run the empty and archive snackboxes -----//
});

// Route::get('/import', 'ImportController@getImport')->name('import');
// Route::post('/import_parse', 'ImportController@parseImport')->name('import_parse');
// Route::post('/import_process', 'ImportController@processImport')->name('import_process');

Auth::routes();


// I think was auto generated?  Or I'm going in a different direction, either way I can probably remove it at a later date.
Route::get('/home', 'HomeController@showCompany')->name('home')->middleware('auth');

Route::view('/', 'welcome');


Route::get('/login/office', 'Auth\LoginController@showOfficeLoginForm')->name('login/office');
Route::get('/login/warehouse', 'Auth\LoginController@showWarehouseLoginForm')->name('login/warehouse');
Route::get('/register/office', 'Auth\RegisterController@showOfficeRegisterForm')->name('register/office');
Route::get('/register/warehouse', 'Auth\RegisterController@showWarehouseRegisterForm')->middleware('auth:office')->name('register/warehouse');

Route::post('/login/office', 'Auth\LoginController@officeLogin');
Route::post('/login/warehouse', 'Auth\LoginController@warehouseLogin');
Route::post('/register/office', 'Auth\RegisterController@createOffice');
Route::post('/register/warehouse', 'Auth\RegisterController@createWarehouse');

// Route::view('/home', 'home')->middleware('auth');
Route::view('/office', 'office')->name('office')->middleware('auth:office');
Route::view('/warehouse', 'warehouse')->middleware('auth:warehouse');
