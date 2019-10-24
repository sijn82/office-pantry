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

// Not sure if all or none of the routes listed in this page should be included with '::middleware('auth:api')'
// but when I'm done moving the routes it'll be a breeze to do so.

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//---- Moving routes into sensible groups for namespacing and authentication ease (hopefully). -----//

// This group will hold all the Fruit/Milk/Snacks/Drink & Other Boxes as well as their archived counterparts.
Route::group([
    'namespace' => 'Boxes',
    'prefix' => 'boxes',
    //'guard' => 'office',
    // middleware currently throws a 401 unauthenticated error when turned on.

    'middleware' => [
        // 'auth:office'//,
        'auth:api'
    ]
], function () {

                        //----- Current Orders -----//

    // Fruitbox CRUD
    Route::post('fruitbox/add-new-fruitbox', 'FruitBoxController@store');
    Route::put('fruitbox/{id}', 'FruitBoxController@update');
    // Milkbox CRUD
    Route::post('milkbox/add-new-milkbox', 'MilkBoxController@store');
    Route::put('milkbox/{id}', 'MilkBoxController@update');
    // Snackbox CRUD
    Route::post('snackboxes/save', 'SnackBoxController@store');
    Route::post('snackbox/update', 'SnackBoxController@update');
    Route::post('snackbox/details', 'SnackBoxController@updateDetails');
    Route::post('snackbox/add-product', 'SnackBoxController@addProductToSnackbox');
    Route::put('snackbox/destroy/{id}', 'SnackBoxController@destroyItem');
    Route::put('snackbox/destroy-box/{id}', 'SnackBoxController@destroyBox');
    // Drinkbox CRUD
    Route::post('drinkboxes/save', 'DrinkBoxController@store');
    Route::post('drinkbox/update', 'DrinkBoxController@update');
    Route::post('drinkbox/details', 'DrinkBoxController@updateDetails');
    Route::post('drinkbox/add-product', 'DrinkBoxController@addProductToDrinkbox');
    Route::put('drinkbox/destroy/{id}', 'DrinkBoxController@destroyItem');
    Route::put('drinkbox/destroy-box/{id}', 'DrinkBoxController@destroyBox');
    // Otherbox CRUD
    Route::post('otherboxes/save', 'OtherBoxController@store');
    Route::post('otherbox/update', 'OtherBoxController@update');
    Route::post('otherbox/details', 'OtherBoxController@updateDetails');
    Route::post('otherbox/add-product', 'OtherBoxController@addProductToOtherbox');
    // Route::put('otherbox/destroy/{id}', 'OtherBoxController@destroy'); // Don't think I'm using this, going to replace it anyway.
    Route::put('otherbox/destroy/{id}', 'OtherBoxController@destroyItem');
    Route::put('otherbox/destroy-box/{id}', 'OtherBoxController@destroyBox');

                        //----- Archived Orders -----//

    // Archived Fruitbox
    Route::put('archived-fruitbox/update/{id}', 'ArchivedFruitBoxController@updateArchivedFruitBox');
    Route::put('archived-fruitbox/destroy/{id}', 'ArchivedFruitBoxController@deleteArchivedFruitBox');
    // Archived Milkbox
    Route::put('archived-milkbox/update/{id}', 'ArchivedMilkBoxController@updateArchivedMilkBox');
    Route::put('archived-milkbox/destroy/{id}', 'ArchivedMilkBoxController@deleteArchivedMilkBox');
    // Archived Snackbox
    Route::post('archived-snackbox/details', 'ArchivedSnackBoxController@updateDetails');
    Route::put('archived-snackbox/destroy/{id}', 'ArchivedSnackBoxController@destroyItem');
    Route::put('archived-snackbox/destroy-box/{id}', 'ArchivedSnackBoxController@destroyBox');
    // Archived Drinkbox
    Route::put('archived-drinkbox/destroy/{id}', 'ArchivedDrinkBoxController@destroyItem');
    Route::put('archived-drinkbox/destroy-box/{id}', 'ArchivedDrinkBoxController@destroyBox');
    // Archived Otherbox
    Route::put('archived-otherbox/destroy/{id}', 'ArchivedOtherBoxController@destroyItem');
    Route::put('archived-otherbox/destroy-box/{id}', 'ArchivedOtherBoxController@destroyBox');

    //----- Mass Update Snackboxes -----//
    Route::post('snackboxes/standard/update', 'SnackBoxController@massUpdateType');
    Route::get('types/select', 'SnackBoxController@showTypes');
    // Export Fruitbox Picklists
    Route::get('export-fruitbox-picklists', 'FruitBoxController@fruitbox_export');
    // Not 100% sure if this one is in use?
    Route::get('fruitbox-picklists', 'FruitBoxController@addRouteInfoToFruitPicklists');
    // Or this remarkably similar one :)
    Route::get('fruitbox-picklists', 'FruitBoxController@addRouteInfoToFruitPicklists');
    // And this almost certainly isn't used... In fact I'm even going to comment it out for (ever) now.
    // Route::get('importFruitBox', 'FruitBoxController@store');
    // Again, not sure exactly sure the purpose of this route, obviously it's an export but is it a picklist? Is it currently in use?
    Route::get('otherbox-export', 'OtherBoxController@download_otherboxes');


    // Old System Routes
    Route::post('upload-snackbox-product-codes', 'SnackBoxController@upload_products_and_codes');
    Route::post('upload-snackbox-orders', 'SnackBoxController@upload_snackbox_orders');
});

// Aside from boxes which got their own group these are the controllers which handle company specific data.
// I could maybe put everything in here, and add a further subgroup of 'boxes' but for now this is an improvement on what I had.

Route::group([
    'namespace' => 'Company',
    'prefix' => 'company',
    //'middleware' => ['auth:api','auth:office']
], function () {

    Route::post('additional-info', 'AdditionalInfoController@addAdditionalInfo');
    Route::put('additional-info/{id}', 'AdditionalInfoController@destroy');

    Route::get('allergies/select', 'AllergyController@showAllergies');
    Route::post('allergies', 'AllergyController@addAllergy');
    Route::put('allergies/{id}', 'AllergyController@destroy');

    Route::post('company-details/add-new-company', 'CompanyDetailsController@store');
    Route::put('company-details/update/{company_details_id}', 'CompanyDetailsController@update');
    Route::get('companies/search', 'CompanyDetailsController@search');

    // New system exporting functions, now moved to their new homes but still partially incomplete (DO NOT EXPECT THEM TO RUN RIGHT NOW!)
    // Route::get('export-fruitbox-picklists', 'FruitBoxController@fruitbox_export');

    // The next 3 routes should really go into their own group I think, using a more bespoke controller, until I do that, they'll just have to live here for now.
    Route::get('export-routing', 'CompanyRouteController@download_new_routes');
    Route::get('export-routing-override', 'CompanyRouteController@download_new_routes_override');
    //Import Rejigged Routes (New System)
    Route::post('import-rejigged-routes', 'CompanyRouteController@import');

    // Company route CRUD - note that the autogeneration of routes means create isn't necessary and I never seem to use read in the default way.
    Route::put('company-route/update/{id}', 'CompanyRouteController@update');
    Route::put('company-route/delete/{id}', 'CompanyRouteController@destroy');

    Route::post('preferences/add-new-preference', 'PreferencesController@store');
    Route::post('preferences/selected', 'PreferencesController@show');
    Route::post('preferences/remove', 'PreferencesController@remove');
    // I'm pretty sure this was just used to test the random selection of a new product id,
    // but I'll double check that later, for now it's fine to keep.
    Route::get('random', 'PreferencesController@random');
});


// Essentially this is a group of system functions where, less concerned with specific orders,
// these are the bits that enable the generation/processing/delivery of company orders.
Route::group([
    'namespace' => 'OfficePantry',
    'prefix' => 'office-pantry',
    //'middleware' => 'auth:office'
], function () {
    // Assigned routes i.e our own delivery routes.
    Route::post('assigned-route/add-new-assigned-route', 'AssignedRouteController@store');
    Route::post('assigned-routes/update', 'AssignedRouteController@update');
    Route::get('assigned-routes/select', 'AssignedRouteController@listAssignedRoutes');
    Route::put('assigned-route/{id}', 'AssignedRouteController@destroy');
    // Fruit Partner Products i.e 1 x fruitbox = £20, 2 x fruitbox = 18.50 etc.
    Route::post('fruit-partners/add-new-fruitpartner', 'FruitPartnerController@store');
    Route::get('fruit-partners/select', 'FruitPartnerController@listFruitPartners');
    Route::get('fruit_partners/{id}', 'FruitPartnerController@show'); // <-- not sure where this is used, will edit when I stumble across it or take a proper look.
    Route::put('fruit-partners/update/{id}', 'FruitPartnerController@update');
    Route::put('fruit-partners/destroy/{id}', 'FruitPartnerController@destroy');
    // This might get moved to an import/export group later on.
    Route::get('export-fruitpartner-deliveries', 'FruitPartnerController@groupOrdersByFruitPartner');
    // Office Pantry Products
    Route::put('office-pantry-products/update/{id}', 'OfficePantryProductsController@update');
    Route::get('office-pantry-products/show', 'OfficePantryProductsController@show'); // Might do this differently tomorrow morning.
    //Invoicing
    Route::get('weekly-invoicing', 'InvoicingController@weekly_invoicing_export');
    Route::get('confirm-weekly-invoicing', 'InvoicingController@confirm_weekly_invoicing');
    // I think was just used to grab a few products once... will comment it out for now.
    //Route::get('imports/readfile', 'ProductsController@import');
    // Products (generic) i.e Cranberry Smokey ALmonds & Corn.
    Route::put('products/update/{id}', 'ProductsController@update');
    Route::put('products/destroy/{id}', 'ProductsController@destroy');

    Route::post('products/add-new-product', 'ProductsController@store');
    // Route::get('products/{id}', 'OfficeDashboardController@showProduct'); // why does this break the search function, if placed (here) above the 'products/search'
    Route::get('products/search', 'ProductsController@search');
    Route::get('products', 'ProductsController@index');

    // To be useful at a later date when more of the process has been implimented.
    //Route::get('importProduct', 'ProductsController@storeCSV');

    // Update the week_start variable.
    Route::post('import-week-start', 'WeekStartController@storeWeekStart');
    Route::post('import-week-start-days', 'WeekStartController@storeDeliveryDays');
    Route::get('week-start/select', 'WeekStartController@showAndSet');

    Route::get('orders', 'OrderController@index');
    Route::get('process-orders', 'OrderController@addOrdersToRoutes');
    Route::get('carbon', 'OrderController@advanceNextOrderDeliveryDate');
    Route::get('cron-data/select', 'OrderController@showCronData');
    Route::post('cron-data/update', 'OrderController@updateCronData');

    //----- Temporary hack to advance orders without worrying about whether the boxes have been invoiced, or to make archives - fruit & milk only! -----//
    Route::get('fudge-order-advancement', 'OrderController@fudgeOrderAdvancement');
});

// Routes still looking for a folder to call home.

Route::post('companies/selected', 'OfficeDashboardController@showSelectedCompanyData');
Route::get('companies/selected', 'OfficeDashboardController@showSelectedCompanyData');
Route::get('companies/{company}', 'OfficeDashboardController@show')->middleware('auth:api'); //

Route::get('products/{id}', 'OfficeDashboardController@showProduct'); // but works fine when placed below it here? <-- that was before, now I've moved it again what will happen?
Route::put('preferences/{id}', 'OfficeDashboardController@destroy');

//----- Cleaning up the surplus! -----//

// Fruit Ordering Document (FOD)

// Create index views for showing all of that model.
Route::get('fruitorderingdocs', 'FruitOrderingDocumentController@index');
Route::get('reset-fod', 'FruitOrderingDocumentController@reset');
// Import FOD data => currently this just stores fresh data each time, however this should be updating the existing entries.
Route::get('import-fod', 'FruitOrderingDocumentController@store');
// This a temporary entry to keep it away from currently used functions/urls but may get updated to import-fod.
Route::post('upload-fod-csv', 'FruitOrderingDocumentController@upload');
// test api url to check what encoding was used
Route::post('upload-test-csv', 'FruitOrderingDocumentController@csv_tester');
// Interesting but I doubt very much this is used for anything, even in the old system.
Route::get('process-csv', 'FruitOrderingDocumentController@create');

// Picklists

Route::get('picklists', 'PickListsController@index');
Route::get('importPicklist', 'PickListsController@store');
// Run updates to existing data.
Route::get('picklists-vs-fod', 'PickListsController@update');
// Not sure this one will be working properly anymore now that I'm using grouped routing names rather than a mirror of the picklists. - Edit: Actually it looks like I was omnipotent, need to test it.
Route::get('update-picklists-with-routes', 'PickListsController@updatePicklistWithRejiggedRoutes');
Route::get('reorder-seasonal-berries', 'PickListsController@reorder_seasonal_berries');
Route::get('seasonal-berries-picklists', 'PickListsController@berry_totals');
Route::get('seasonal-berries-breakdown', 'PickListsController@berry_export');
// Export excel data for picklists and routing.
Route::get('export-picklists', 'PickListsController@export');
Route::get('export-picklists-full', 'PickListsController@full_export')->middleware('auth:office');

// Routes (Old)

Route::get('routes', 'RoutesController@index');
Route::put('route/{id}', 'RoutesController@routeInfoUpdate');
Route::put('route/delete/{id}', 'RoutesController@destroy');
Route::get('import-routing', 'RoutesController@store');
Route::get('update-routing', 'RoutesController@update');
// Route::get('update-routing', 'RoutesController@update'); <-- Did I declare this twice? Dummy.
Route::get('reset-routing', 'RoutesController@reset');
Route::post('upload-rejigged-routes-csv', 'RoutesController@uploadRejiggedRoutes');
Route::get('rejig-routing', 'RoutesController@updateRouteAndPosition');
// Store the snacks n drinks csv uploaded from import-file (url) vue component.
Route::post('upload-snacks-n-drinks-csv', 'RoutesController@storeSnacksNDrinksCSV');
// This is again a temporary solution to adding the drinks and snacks totals to routes, as it doesn't get brought in with FOD at the moment.
Route::get('import-drinks-n-snacks', 'RoutesController@addDrinksNSnacksToRoute');

// Company (Old)

// Store the snacks n drinks csv uploaded from import-file (url) vue component.
Route::post('upload-snacks-n-drinks-csv', 'RoutesController@storeSnacksNDrinksCSV');
// This is again a temporary solution to adding the drinks and snacks totals to routes, as it doesn't get brought in with FOD at the moment.
Route::get('import-drinks-n-snacks', 'RoutesController@addDrinksNSnacksToRoute');
// Import main Company data.
Route::get('import-companies', 'CompaniesController@store');
// Import additional route summary address and delivery info
Route::get('import-route-summary-delivery-info', 'CompaniesController@updateRouteSummaryAddressAndDeliveryInfo');
Route::get('export-companies', 'CompaniesController@export')->middleware('auth:office');

//----- Pretty sure I never used these, almost certainly superfluous now. -----//

// Route for export/download tabledata to .csv, .xls or .xlsx
Route::get('downloadExcel/{type}', 'MaatwebsiteController@downloadExcel');
// Route for import excel data to database.
Route::post('importExcel', 'MaatwebsiteController@importExcel');

//----- Old entries commented out but otherwise as they were, to be removed if the transition of routes is (comparatively) seamless -----//

// //Import Rejigged Routes (New System)
// Route::post('import-rejigged-routes', 'CompanyRouteController@import');

// //Invoicing
// Route::get('weekly-invoicing', 'InvoicingController@weekly_invoicing_export');
// Route::get('confirm-weekly-invoicing', 'InvoicingController@confirm_weekly_invoicing');

// // Office Pantry Products
// Route::put('products/office-pantry-products/update/{id}', 'OfficePantryProductsController@update');
// Route::get('products/office-pantry-products/show', 'OfficePantryProductsController@show'); // Might do this differently tomorrow morning.

// Archived Boxes
// Route::put('archived-fruitbox/destroy/{id}', 'ArchivedFruitBoxController@deleteArchivedFruitBox');
// Route::put('archived-fruitbox/update/{id}', 'ArchivedFruitBoxController@updateArchivedFruitBox');

// Route::get('random', 'PreferencesController@random');

// Route::put('fruitbox/{id}', 'FruitBoxController@update');
// Route::post('fruitbox/add-new-fruitbox', 'FruitBoxController@store');
//
// Route::put('milkbox/{id}', 'MilkBoxController@update');
// Route::post('milkbox/add-new-milkbox', 'MilkBoxController@store');

// Route::put('products/update/{id}', 'ProductsController@update');
// Route::put('products/destroy/{id}', 'ProductsController@destroy');
//
// Route::post('products/add-new-product', 'ProductsController@store');
// // Route::get('products/{id}', 'OfficeDashboardController@showProduct'); // why does this break the search function, if placed (here) above the 'products/search'
// Route::get('products/search', 'ProductsController@search');
// Route::get('products/{id}', 'OfficeDashboardController@showProduct'); // but works fine when placed below it here?


// Route::post('preferences/add-new-preference', 'PreferencesController@store');
// Route::post('preferences/selected', 'PreferencesController@show');
// Route::post('preferences/remove', 'PreferencesController@remove');
// Route::put('preferences/{id}', 'OfficeDashboardController@destroy');

// Route::get('allergies/select', 'AllergyController@showAllergies');
// Route::post('allergies', 'AllergyController@addAllergy');
// Route::put('allergies/{id}', 'AllergyController@destroy');

// Route::get('types/select', 'SnackBoxController@showTypes');

// Route::post('additional-info', 'AdditionalInfoController@addAdditionalInfo');
// Route::put('additional-info/{id}', 'AdditionalInfoController@destroy');

// Route::post('assigned-route/add-new-assigned-route', 'AssignedRouteController@store');
// Route::post('assigned-routes/update', 'AssignedRouteController@update');
// Route::get('assigned-routes/select', 'AssignedRouteController@listAssignedRoutes');
// Route::put('assigned-route/{id}', 'AssignedRouteController@destroy');

// Route::post('fruitpartners/add-new-fruitpartner', 'FruitPartnerController@store');
// Route::get('fruit_partners/select', 'FruitPartnerController@listFruitPartners');
// Route::get('fruit_partners/{id}', 'FruitPartnerController@show');
// Route::get('export-fruitpartner-deliveries', 'FruitPartnerController@groupOrdersByFruitPartner');

// Route::put('company-route/update/{id}', 'CompanyRouteController@update');
// Route::put('company-route/delete/{id}', 'CompanyRouteController@destroy');

// // Update the week_start variable.
// Route::post('import-week-start', 'WeekStartController@storeWeekStart');
//
// Route::post('import-week-start-days', 'WeekStartController@storeDeliveryDays');

// Route::get('fruitbox-picklists', 'FruitBoxController@addRouteInfoToFruitPicklists');

// Route::get('picklists', 'PickListsController@index');
// Route::get('fruitbox-picklists', 'PickListsController@fruitboxPicklists');
// Route::get('products', 'ProductsController@index');
// Route::get('routes', 'RoutesController@index');
// Route::put('route/{id}', 'RoutesController@routeInfoUpdate');
// Route::put('route/delete/{id}', 'RoutesController@destroy');
// Route::get('companies', 'CompanyController@index');

// Store routes/picklists - this was used once to populate the tables with some data to work with.
// Route::get('import-routing', 'RoutesController@store');
// Route::get('importPicklist', 'PickListsController@store');

// // Run updates to existing data.
// Route::get('picklists-vs-fod', 'PickListsController@update');
// Be wary of this url, some company totals on the route list will continue to increase with each visit! i.e those with more than one box and require a grouped total.
// Route::get('update-routing', 'RoutesController@update');
// Route::get('reset-routing', 'RoutesController@reset');
// Route::get('reset-fod', 'FruitOrderingDocumentController@reset');

// Route::post('upload-rejigged-routes-csv', 'RoutesController@uploadRejiggedRoutes');
// Route::get('rejig-routing', 'RoutesController@updateRouteAndPosition');

// // Not sure this one will be working properly anymore now that I'm using grouped routing names rather than a mirror of the picklists. - Edit: Actually it looks like I was omnipotent, need to test it.
// Route::get('update-picklists-with-routes', 'PickListsController@updatePicklistWithRejiggedRoutes');
// Route::get('reorder-seasonal-berries', 'PickListsController@reorder_seasonal_berries');
// Route::get('seasonal-berries-picklists', 'PickListsController@berry_totals');
// Route::get('seasonal-berries-breakdown', 'PickListsController@berry_export');


// Route::get('otherbox-export', 'OtherBoxController@download_otherboxes');
// This is now defunct, will remove at some point when I clean the code up generally.
// Route::get('strip-picklists-company-name', 'PickListsController@stripu00a0FromPicklistsCompanyName');

// // Import FOD data => currently this just stores fresh data each time, however this should be updating the existing entries.
// Route::get('import-fod', 'FruitOrderingDocumentController@store');
// // This a temporary entry to keep it away from currently used functions/urls but may get updated to import-fod.
// Route::post('upload-fod-csv', 'FruitOrderingDocumentController@upload');

// // test api url to check what encoding was used
// Route::post('upload-test-csv', 'FruitOrderingDocumentController@csv_tester');
//
// Route::get('process-csv', 'FruitOrderingDocumentController@create');

// // Store the snacks n drinks csv uploaded from import-file (url) vue component.
// Route::post('upload-snacks-n-drinks-csv', 'RoutesController@storeSnacksNDrinksCSV');
// // This is again a temporary solution to adding the drinks and snacks totals to routes, as it doesn't get brought in with FOD at the moment.
// Route::get('import-drinks-n-snacks', 'RoutesController@addDrinksNSnacksToRoute');

// Use this to store a snackbox attached to a single company id
// Route::post('snackboxes/save', 'SnackBoxController@store');

// Use this to update all the existing standard snackboxes.
// Route::post('snackboxes/standard/update', 'SnackBoxController@massUpdateType');
// // Update company specific snackbox
// Route::post('snackbox/update', 'SnackBoxController@update');
// Route::post('snackbox/details', 'SnackBoxController@updateDetails');
// Route::post('snackbox/add-product', 'SnackBoxController@addProductToSnackbox');
// Route::put('snackbox/destroy/{id}', 'SnackBoxController@destroyItem');
// Route::put('snackbox/destroy-box/{id}', 'SnackBoxController@destroyBox');
// Update company specific snackbox archive
// Route::put('archived-snackbox/destroy/{id}', 'ArchivedSnackBoxController@destroyItem');
// Route::put('archived-snackbox/destroy-box/{id}', 'ArchivedSnackBoxController@destroyBox');

// // Update company specific drinkbox
// Route::post('drinkboxes/save', 'DrinkBoxController@store');
// Route::post('drinkbox/update', 'DrinkBoxController@update');
// Route::post('drinkbox/details', 'DrinkBoxController@updateDetails');
// Route::post('drinkbox/add-product', 'DrinkBoxController@addProductToDrinkbox');
// // Route::put('drinkbox/destroy/{id}', 'DrinkBoxController@destroy'); // Don't think I'm using this, going to replace it anyway.
// Route::put('drinkbox/destroy/{id}', 'DrinkBoxController@destroyItem');
// Route::put('drinkbox/destroy-box/{id}', 'DrinkBoxController@destroyBox');
// Update company specific drinkbox archive
// Route::put('archived-drinkbox/destroy/{id}', 'ArchivedDrinkBoxController@destroyItem');
// Route::put('archived-drinkbox/destroy-box/{id}', 'ArchivedDrinkBoxController@destroyBox');

// // Update company specific otherbox
// Route::post('otherboxes/save', 'OtherBoxController@store');
// Route::post('otherbox/update', 'OtherBoxController@update');
// Route::post('otherbox/details', 'OtherBoxController@updateDetails');
// Route::post('otherbox/add-product', 'OtherBoxController@addProductToOtherbox');
// // Route::put('otherbox/destroy/{id}', 'OtherBoxController@destroy'); // Don't think I'm using this, going to replace it anyway.
// Route::put('otherbox/destroy/{id}', 'OtherBoxController@destroyItem');
// Route::put('otherbox/destroy-box/{id}', 'OtherBoxController@destroyBox');
// Update company specific otherbox archive
// Route::put('archived-otherbox/destroy/{id}', 'ArchivedOtherBoxController@destroyItem');
// Route::put('archived-otherbox/destroy-box/{id}', 'ArchivedOtherBoxController@destroyBox');

// Route::post('upload-snackbox-product-codes', 'SnackBoxController@upload_products_and_codes');
// Route::post('upload-snackbox-orders', 'SnackBoxController@upload_snackbox_orders');


// Route::get('orders', 'OrderController@index');
// Route::get('process-orders', 'OrderController@addOrdersToRoutes');
// Route::get('carbon', 'OrderController@advanceNextOrderDeliveryDate');
// Route::get('cron-data/select', 'OrderController@showCronData');
// Route::post('cron-data/update', 'OrderController@updateCronData');

// Route::get('/SnackBoxController/{$snd_OP_singleBoxes_chunks}', 'SnackBoxController@auto_process_snackboxes');

// Route::get('week-start/select', 'WeekStartController@showAndSet');


// // Import main Company data.
// Route::get('import-companies', 'CompaniesController@store');
// // Import additional route summary address and delivery info
// Route::get('import-route-summary-delivery-info', 'CompaniesController@updateRouteSummaryAddressAndDeliveryInfo');
//

// Route::post('companies/add-new-company', 'CompaniesController@create'); Old Add Company Path
// Route::post('company-details/add-new-company', 'CompanyDetailsController@store');
// Route::put('company-details/update/{company_details_id}', 'CompanyDetailsController@update');
// // Route::get('companies/search', 'CompaniesController@search');
// Route::get('companies/search', 'CompanyDetailsController@search');
// Route::post('companies/selected', 'OfficeDashboardController@showSelectedCompanyData');
// Route::get('companies/selected', 'OfficeDashboardController@showSelectedCompanyData');
// Route::get('companies/{company}', 'OfficeDashboardController@show');

// // To be useful at a later date when more of the process has been implimented.
// Route::get('importProduct', 'ProductsController@storeCSV');
// Route::get('importFruitBox', 'FruitBoxController@store');

// New system exporting functions, now moved to their new homes but still partially incomplete (DO NOT EXPECT THEM TO RUN RIGHT NOW!)
// Route::get('export-fruitbox-picklists', 'FruitBoxController@fruitbox_export');
// Route::get('export-routing', 'CompanyRouteController@download_new_routes');
// Route::get('export-routing-override', 'CompanyRouteController@download_new_routes_override');

// // Export excel data for picklists and routing.
// Route::get('export-picklists', 'PickListsController@export');
// Route::get('export-picklists-full', 'PickListsController@full_export')->middleware('auth:office');
// Route::get('export-routing', 'RoutesController@export');

// Route::get('export-companies', 'CompaniesController@export')->middleware('auth:office');

// Not currently using these, but maybe (very?) soon. <--- Going to assume they found a new home?  Yeah, that'll be it... Sure.

// // Route for export/download tabledata to .csv, .xls or .xlsx
// Route::get('downloadExcel/{type}', 'MaatwebsiteController@downloadExcel');
// // Route for import excel data to database.
// Route::post('importExcel', 'MaatwebsiteController@importExcel');


// Route::get('imports/readfile', 'ProductsController@import');
