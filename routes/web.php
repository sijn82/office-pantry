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


Route::get('display-routes', 'RoutesController@index');
Route::get('snackboxes-multi-company', 'SnackBoxController@index_OP');

// Route::get('companies', 'CompaniesController@index');
// Route::get('products', 'ProductsController@index');

Route::get('/import', 'ImportController@getImport')->name('import');
Route::post('/import_parse', 'ImportController@parseImport')->name('import_parse');
Route::post('/import_process', 'ImportController@processImport')->name('import_process');
