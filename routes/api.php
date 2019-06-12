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

//Invoicing
Route::get('weekly-invoicing', 'InvoicingController@weekly_invoicing_export');
Route::get('confirm-weekly-invoicing', 'InvoicingController@confirm_weekly_invoicing');

// Office Pantry Products
Route::put('products/office-pantry-products/update/{id}', 'OfficePantryProductsController@update');
Route::get('products/office-pantry-products/show', 'OfficePantryProductsController@show'); // Might do this differently tomorrow morning.

// Archived Boxes
Route::put('archived-fruitbox/destroy/{id}', 'ArchivedFruitBoxController@deleteArchivedFruitBox');
Route::put('archived-fruitbox/update/{id}', 'ArchivedFruitBoxController@updateArchivedFruitBox');

Route::get('random', 'PreferencesController@random');

Route::put('fruitbox/{id}', 'FruitBoxController@update');
Route::post('fruitbox/add-new-fruitbox', 'FruitBoxController@store');

Route::put('milkbox/{id}', 'MilkBoxController@update');
Route::post('milkbox/add-new-milkbox', 'MilkBoxController@store');

Route::put('products/update/{id}', 'ProductsController@update');
Route::put('products/destroy/{id}', 'ProductsController@destroy');

Route::post('products/add-new-product', 'ProductsController@store');
// Route::get('products/{id}', 'OfficeDashboardController@showProduct'); // why does this break the search function, if placed (here) above the 'products/search'
Route::get('products/search', 'ProductsController@search');
Route::get('products/{id}', 'OfficeDashboardController@showProduct'); // but works fine when placed below it here?


Route::post('preferences/add-new-preference', 'PreferencesController@store');
Route::post('preferences/selected', 'PreferencesController@show');
Route::post('preferences/remove', 'PreferencesController@remove');
Route::put('preferences/{id}', 'OfficeDashboardController@destroy');

Route::get('allergies/select', 'AllergyController@showAllergies');
Route::post('allergies', 'AllergyController@addAllergy');
Route::put('allergies/{id}', 'AllergyController@destroy');

Route::get('types/select', 'SnackBoxController@showTypes');

Route::post('additional-info', 'AdditionalInfoController@addAdditionalInfo');
Route::put('additional-info/{id}', 'AdditionalInfoController@destroy');

Route::post('assigned-route/add-new-assigned-route', 'AssignedRouteController@store');
Route::post('assigned-routes/update', 'AssignedRouteController@update');
Route::get('assigned-routes/select', 'AssignedRouteController@listAssignedRoutes');
Route::put('assigned-route/{id}', 'AssignedRouteController@destroy');

Route::post('fruitpartners/add-new-fruitpartner', 'FruitPartnerController@store');
Route::get('fruit_partners/select', 'FruitPartnerController@listFruitPartners');
Route::get('fruit_partners/{id}', 'FruitPartnerController@show');

Route::put('company-route/update/{id}', 'CompanyRouteController@update');
Route::put('company-route/delete/{id}', 'CompanyRouteController@destroy');

// Update the week_start variable.
Route::post('import-week-start', 'WeekStartController@storeWeekStart');

Route::post('import-week-start-days', 'WeekStartController@storeDeliveryDays');

Route::get('fruitbox-picklists', 'FruitBoxController@addRouteInfoToFruitPicklists');

// Create index views for showing all of that model.
Route::get('fruitorderingdocs', 'FruitOrderingDocumentController@index');
Route::get('picklists', 'PickListsController@index');
// Route::get('fruitbox-picklists', 'PickListsController@fruitboxPicklists');
Route::get('products', 'ProductsController@index');
Route::get('routes', 'RoutesController@index');
Route::put('route/{id}', 'RoutesController@routeInfoUpdate');
Route::put('route/delete/{id}', 'RoutesController@destroy');
// Route::get('companies', 'CompanyController@index');

// Store routes/picklists - this was used once to populate the tables with some data to work with.
Route::get('import-routing', 'RoutesController@store');
Route::get('importPicklist', 'PickListsController@store');

// Run updates to existing data.
Route::get('picklists-vs-fod', 'PickListsController@update');
// Be wary of this url, some company totals on the route list will continue to increase with each visit! i.e those with more than one box and require a grouped total.
Route::get('update-routing', 'RoutesController@update');

Route::get('reset-routing', 'RoutesController@reset');
Route::get('reset-fod', 'FruitOrderingDocumentController@reset');

Route::post('upload-rejigged-routes-csv', 'RoutesController@uploadRejiggedRoutes');
Route::get('rejig-routing', 'RoutesController@updateRouteAndPosition');

// Not sure this one will be working properly anymore now that I'm using grouped routing names rather than a mirror of the picklists. - Edit: Actually it looks like I was omnipotent, need to test it.
Route::get('update-picklists-with-routes', 'PickListsController@updatePicklistWithRejiggedRoutes');
Route::get('reorder-seasonal-berries', 'PickListsController@reorder_seasonal_berries');
Route::get('seasonal-berries-picklists', 'PickListsController@berry_totals');
Route::get('seasonal-berries-breakdown', 'PickListsController@berry_export');


Route::get('otherbox-export', 'OtherBoxController@download_otherboxes');
// This is now defunct, will remove at some point when I clean the code up generally.
// Route::get('strip-picklists-company-name', 'PickListsController@stripu00a0FromPicklistsCompanyName');

// Import FOD data => currently this just stores fresh data each time, however this should be updating the existing entries.
Route::get('import-fod', 'FruitOrderingDocumentController@store');
// This a temporary entry to keep it away from currently used functions/urls but may get updated to import-fod.
Route::post('upload-fod-csv', 'FruitOrderingDocumentController@upload');

// test api url to check what encoding was used
Route::post('upload-test-csv', 'FruitOrderingDocumentController@csv_tester');

Route::get('process-csv', 'FruitOrderingDocumentController@create');

// Store the snacks n drinks csv uploaded from import-file (url) vue component.
Route::post('upload-snacks-n-drinks-csv', 'RoutesController@storeSnacksNDrinksCSV');
// This is again a temporary solution to adding the drinks and snacks totals to routes, as it doesn't get brought in with FOD at the moment.
Route::get('import-drinks-n-snacks', 'RoutesController@addDrinksNSnacksToRoute');

// Use this to store a snackbox attached to a single company id
Route::post('snackboxes/save', 'SnackBoxController@store');

// Use this to update all the existing standard snackboxes.
Route::post('snackboxes/standard/update', 'SnackBoxController@massUpdateType');
// Update company specific snackbox
Route::post('snackbox/update', 'SnackBoxController@update');
Route::post('snackbox/details', 'SnackBoxController@updateDetails');
Route::post('snackbox/add-product', 'SnackBoxController@addProductToSnackbox');
Route::put('snackbox/destroy/{id}', 'SnackBoxController@destroyItem');
Route::put('snackbox/destroy-box/{id}', 'SnackBoxController@destroyBox');
// Update company specific drinkbox
Route::post('drinkboxes/save', 'DrinkBoxController@store');
Route::post('drinkbox/update', 'DrinkBoxController@update');
Route::post('drinkbox/details', 'DrinkBoxController@updateDetails');
Route::post('drinkbox/add-product', 'DrinkBoxController@addProductToDrinkbox');
// Route::put('drinkbox/destroy/{id}', 'DrinkBoxController@destroy'); // Don't think I'm using this, going to replace it anyway.
Route::put('drinkbox/destroy/{id}', 'DrinkBoxController@destroyItem');
Route::put('drinkbox/destroy-box/{id}', 'DrinkBoxController@destroyBox');
// Update company specific otherbox
Route::post('otherboxes/save', 'OtherBoxController@store');
Route::post('otherbox/update', 'OtherBoxController@update');
Route::post('otherbox/details', 'OtherBoxController@updateDetails');
Route::post('otherbox/add-product', 'OtherBoxController@addProductToOtherbox');
// Route::put('otherbox/destroy/{id}', 'OtherBoxController@destroy'); // Don't think I'm using this, going to replace it anyway.
Route::put('otherbox/destroy/{id}', 'OtherBoxController@destroyItem');
Route::put('otherbox/destroy-box/{id}', 'OtherBoxController@destroyBox');

Route::post('upload-snackbox-product-codes', 'SnackBoxController@upload_products_and_codes');
Route::post('upload-snackbox-orders', 'SnackBoxController@upload_snackbox_orders');


Route::get('orders', 'OrderController@index');
Route::get('process-orders', 'OrderController@addOrdersToRoutes');
Route::get('carbon', 'OrderController@advanceNextOrderDeliveryDate');

// Route::get('/SnackBoxController/{$snd_OP_singleBoxes_chunks}', 'SnackBoxController@auto_process_snackboxes');

Route::get('week-start/select', 'WeekStartController@showAndSet');

Route::get('cron-data/select', 'OrderController@showCronData');
Route::post('cron-data/update', 'OrderController@updateCronData');


// Import main Company data.
Route::get('import-companies', 'CompaniesController@store');
// Import additional route summary address and delivery info
Route::get('import-route-summary-delivery-info', 'CompaniesController@updateRouteSummaryAddressAndDeliveryInfo');
//

// Route::post('companies/add-new-company', 'CompaniesController@create'); Old Add Company Path
Route::post('company-details/add-new-company', 'CompanyDetailsController@store');
Route::put('company-details/update/{company_details_id}', 'CompanyDetailsController@update');
// Route::get('companies/search', 'CompaniesController@search');
Route::get('companies/search', 'CompanyDetailsController@search');
Route::post('companies/selected', 'OfficeDashboardController@showSelectedCompanyData');
Route::get('companies/selected', 'OfficeDashboardController@showSelectedCompanyData');
Route::get('companies/{company}', 'OfficeDashboardController@show');

// To be useful at a later date when more of the process has been implimented.
Route::get('importProduct', 'ProductsController@storeCSV');
Route::get('importFruitBox', 'FruitBoxController@store');

// New system exporting functions, now moved to their new homes but still partially incomplete (DO NOT EXPECT THEM TO RUN RIGHT NOW!)
Route::get('export-fruitbox-picklists', 'FruitBoxController@fruitbox_export');
Route::get('export-routing', 'CompanyRouteController@download_new_routes');
Route::get('export-routing-override', 'CompanyRouteController@download_new_routes_override');

// Export excel data for picklists and routing.
Route::get('export-picklists', 'PickListsController@export');
Route::get('export-picklists-full', 'PickListsController@full_export')->middleware('auth:office');
// Route::get('export-routing', 'RoutesController@export');

Route::get('export-companies', 'CompaniesController@export')->middleware('auth:office');

// Not currently using these, but maybe (very?) soon. <--- Going to assume they found a new home?  Yeah, that'll be it... Sure.

// Route for export/download tabledata to .csv, .xls or .xlsx
Route::get('downloadExcel/{type}', 'MaatwebsiteController@downloadExcel');
// Route for import excel data to database.
Route::post('importExcel', 'MaatwebsiteController@importExcel');


Route::get('imports/readfile', 'ProductsController@import');
