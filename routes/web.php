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
    Route::get('auto_process_snackboxes', 'SnackBoxController@auto_process_snackboxes');

    Route::get('export-snackbox-op-multicompany', 'SnackBoxController@download_snackbox_op_multicompany')->name('SnackboxOPMultiCompany');
    Route::get('export-snackbox-dpd-multicompany', 'SnackBoxController@download_snackbox_dpd_multicompany')->name('SnackboxDPDMultiCompany');
    Route::get('export-snackbox-apc-multicompany', 'SnackBoxController@download_snackbox_apc_multicompany')->name('SnackboxAPCMultiCompany');

    Route::get('export-snackbox-op-singlecompany', 'SnackBoxController@download_snackbox_op_singlecompany')->name('SnackboxOPSingleCompany');
    Route::get('export-snackbox-dpd-singlecompany', 'SnackBoxController@download_snackbox_dpd_singlecompany')->name('SnackboxDPDSingleCompany');
    Route::get('export-snackbox-apc-singlecompany', 'SnackBoxController@download_snackbox_apc_singlecompany')->name('SnackboxAPCSingleCompany');

    Route::get('export-snackbox-op-unique', 'SnackBoxController@download_snackbox_op_unique')->name('SnackboxOPUnique');
    Route::get('export-snackbox-dpd-unique', 'SnackBoxController@download_snackbox_dpd_unique')->name('SnackboxDPDUnique');
    Route::get('export-snackbox-apc-unique', 'SnackBoxController@download_snackbox_apc_unique')->name('SnackboxAPCUnique');

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
Route::get('/register/office', 'Auth\RegisterController@showOfficeRegisterForm');
Route::get('/register/warehouse', 'Auth\RegisterController@showWarehouseRegisterForm')->middleware('auth:office');

Route::post('/login/office', 'Auth\LoginController@officeLogin');
Route::post('/login/warehouse', 'Auth\LoginController@warehouseLogin');
Route::post('/register/office', 'Auth\RegisterController@createOffice');
Route::post('/register/warehouse', 'Auth\RegisterController@createWarehouse');

// Route::view('/home', 'home')->middleware('auth');
Route::view('/office', 'office')->name('office')->middleware('auth:office');
Route::view('/warehouse', 'warehouse')->middleware('auth:warehouse');
