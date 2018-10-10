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
Route::get('products', function () {
    return view('products');
});
Route::get('routes', function () {
    return view('routes');
});
Route::get('import-file', function () {
    return view('import-file');
});

Route::get('import-file', 'WeekStartController@show')->name('import-file');
Route::get('display-routes', 'RoutesController@index');
Route::get('snackboxes-multi-company', 'SnackBoxController@index_OP');

Route::get('companies', 'CompaniesController@index');
// Route::get('products', 'ProductsController@index');

// Route::get('/import', 'ImportController@getImport')->name('import');
// Route::post('/import_parse', 'ImportController@parseImport')->name('import_parse');
// Route::post('/import_process', 'ImportController@processImport')->name('import_process');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::view('/', 'welcome');


Route::get('/login/office', 'Auth\LoginController@showOfficeLoginForm');
Route::get('/login/warehouse', 'Auth\LoginController@showWarehouseLoginForm');
Route::get('/register/office', 'Auth\RegisterController@showOfficeRegisterForm');
Route::get('/register/warehouse', 'Auth\RegisterController@showWarehouseRegisterForm');

Route::post('/login/office', 'Auth\LoginController@officeLogin');
Route::post('/login/warehouse', 'Auth\LoginController@warehouseLogin');
Route::post('/register/office', 'Auth\RegisterController@createOffice');
Route::post('/register/warehouse', 'Auth\RegisterController@createWarehouse');

Route::view('/home', 'home')->middleware('auth');
Route::view('/office', 'office')->middleware('auth:office');
Route::view('/warehouse', 'warehouse')->middleware('auth:warehouse');
