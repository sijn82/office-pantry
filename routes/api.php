<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Create index views for showing all of that model.
Route::get('fruitorderingdocs', 'FruitOrderingDocumentController@index');
Route::get('picklists', 'PickListsController@index');
Route::get('products', 'ProductsController@index');
Route::get('routes', 'RoutesController@index');
// Route::get('companies', 'CompanyController@index');s

// Store routes/picklists - this was used once to populate the tables with some data to work with.
Route::get('import-routing', 'RoutesController@store');
Route::get('importPicklist', 'PickListsController@store');

// Run updates to existing data.
Route::get('picklists-vs-fod', 'PickListsController@update');
// Be wary of this url, some company totals on the route list will continue to increase with each visit! i.e those with more than one box and require a grouped total.
Route::get('update-routing', 'RoutesController@update');

Route::get('rejig-routing', 'RoutesController@updateRouteAndPosition');

// Not sure this one will be working properly anymore now that I'm using grouped routing names rather than a mirror of the picklists. - Edit: Actually it looks like I was omnipotent, need to test it.
Route::get('update-picklist-with-routes', 'PickListsController@updatePicklistWithRejiggedRoutes');

// This is now defunct, will remove at some point when I clean the code up generally.
// Route::get('strip-picklists-company-name', 'PickListsController@stripu00a0FromPicklistsCompanyName');

// Import FOD data => currently this just stores fresh data each time, however this should be updating the existing entries.
Route::get('import-fod', 'FruitOrderingDocumentController@store');

// This is again a temporary solution to adding the drinks and snacks totals to routes, as it doesn't get brought in with FOD at the moment.
// Calling it store is maybe misleading as it will only update existing entries.
Route::get('import-drinks-n-snacks', 'RoutesController@storeDrinksSnacks');

Route::get('import-snackboxes', 'SnackBoxController@store');

// Import main Company data.
Route::get('import-companies', 'CompaniesController@store');
// Import additional route summary address and delivery info
Route::get('import-route-summary-delivery-info', 'CompaniesController@updateRouteSummaryAddressAndDeliveryInfo');

// To be useful at a later date when more of the process has been implimented.
Route::get('importProduct', 'ProductsController@store');
Route::get('importFruitBox', 'FruitBoxController@store');

// Export excel data for picklists and routing.
Route::get('export-picklists', 'PickListsController@export');
// Route::get('export-routing', 'RoutesController@export');
Route::get('export-routing', 'RoutesController@download');
Route::get('export-companies', 'CompaniesController@export');

// Not currently using these, but maybe (very?) soon.

// Route for export/download tabledata to .csv, .xls or .xlsx
Route::get('downloadExcel/{type}', 'MaatwebsiteController@downloadExcel');
// Route for import excel data to database.
Route::post('importExcel', 'MaatwebsiteController@importExcel');


Route::get('imports/readfile', 'ProductsController@import');
