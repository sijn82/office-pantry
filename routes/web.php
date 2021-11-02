<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
// Route::post('exporting', 'CompanyRouteController@import')->name('import-rejigged-routes');

// If I need more of these I will group them but for now it's just this solo entry.
Route::post('exporting', 'Company\CompanyRouteController@import')->name('import-rejigged-routes'); // <-- Interesting url though, I've also used it again for something else further down.

// I'm pretty sure these are all examples of when I wanted to create a specific url with a new blade template to add vue components too.
// I'm also pretty sure they only look like this and have a singular purpose because
// a) it worked! (and)
// b) I hadn't/haven't spent the time to realise and maximise their use... yet.

// Main homepage for guest/office/warehouse login - no authentication on route and end point for logged out users
// Route::get('/', function () {
//     return view('welcome');
// });

Route::view('/', 'welcome');

// I think was auto generated?  Or I'm going in a different direction, either way I can probably remove it at a later date.
// Actually this will serve the customer (after logging in) with a landing page featuring their listed company.... I think?
Route::get('/home', 'HomeController@showCompany')->name('home')->middleware('auth');

// Register for office or warehouse login
// Get the registration form
// DELETED
// Post the submitted form details
// DELETED
// Get the login form
Route::get('/login/office', 'Auth\LoginController@showOfficeLoginForm')->name('login.office');
Route::get('/login/warehouse', 'Auth\LoginController@showWarehouseLoginForm')->name('login.warehouse');
// Post the submitted form details
Route::post('/login/office', 'Auth\LoginController@officeLogin');
Route::post('/login/warehouse', 'Auth\LoginController@warehouseLogin');
// If Login details matched the corresponding db table info, redirect the user to their dashboard to carry out tasks or, just hangout.
Route::view('/office', 'office')->middleware('auth:office')->name('office');
Route::view('/warehouse', 'warehouse')->middleware('auth:warehouse')->name('warehouse');

// Passport authentication
Route::view('passport', 'passport')->name('passport');

// View jobs and their completion success status

// Only those with office authentication can access these routes.
// All routes contained here now have the prefix 'office' to clearly indicate the login access needed to visit the url.
Route::group(['middleware' => ['web','auth:office'], 'prefix' => 'office'], function () {

    // Calling this jobs because I'd originally planned to use/show queued jobs but now it makes more sense to call it fruit partner exports.
    // Will sort this when it's fully working.
    Route::view('fruit-partner-deliveries/export', 'fruit-partner-deliveries.export');
    // Route::get('fruit-partner-deliveries/export', 'OfficePantry\FruitPartnerController@fruitPartnersList');

    // Add a new company
    Route::view('companies/new', 'new-company')->name('company.new');
    // View the current 3rd Party products i.e Cranberry Smokey Almonds and Corn
    Route::view('products', 'products')->name('products');
    // View the current Office Pantry products i.e fruitbox discount prices etc.
    Route::view('office-pantry-products', 'office-pantry-products')->name('office-pantry-products');
    // Accessing the invoicing and invoice confirm buttons to run invoicing
    Route::view('invoicing', 'invoicing')->name('invoicing');
    // Access the cron page, to view and edit existing cron jobs.
    Route::view('cron', 'cron')->name('cron');
    // Access fruit partners currently just to add new ones but will include viewing/editing existing fruit partners
    Route::view('fruit-partners', 'fruit-partners')->name('fruit-partners');
    // Access office pantry delivery routes, add/view/edit/delete entries
    Route::view('assigned-routes', 'assigned-routes')->name('assigned-routes');
    // Mass update, empty & archive snackboxes.
    Route::view('snackboxes/massupdate', 'snackbox-creation')->name('snackboxes.massupdate');

    Route::view('exporting-processes', 'exporting')->name('exporting-processes');

    Route::view('importing', 'imports.import')->name('import-new-system-data');
    Route::post('importing-company-details', 'ImportController@importCompanyDetails')->name('import-company-details');
    Route::post('importing-fruit-partners', 'ImportController@importFruitPartners')->name('import-fruit-partners');

    // Subgroup in group, these all need the namespace 'Boxes' applied to find the new location of the controller files.

    Route::group(['namespace' => 'Boxes'], function () {

        //----- Old system Processing and Export for Snackboxes -----//
        Route::get('auto_process_snackboxes', 'SnackBoxController@auto_process_snackboxes');
        //----- End of Old system Processing and Export for Snackboxes -----//

        // Not sure what this is used for, whether it's just the old system or to be reused?
        Route::get('snackboxes-multi-company', 'SnackBoxController@auto_process_snackboxes');

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

    }); // End of (subgroup) Route::group(['middleware' => 'web', 'namespace' => 'Boxes'], function ()


}); // Route::group(['middleware' => ['web','auth:office'], 'prefix' => 'office'], function ()

// Generated by make:auth command I think.
// dd($url);
Auth::routes();

//----- Old System Routes -----//

    // This is the old system url for downloading all the important stuff,
    // it's been replaced in the new system but was arguably the most important url in the past.
    Route::view('import-file', 'import-file')->middleware('auth:office')->name('import.file');
    // Another important link from the old system; this was used to import products and snackbox orders before offering links to download all the good stuff, picklists etc.
    Route::view('import-products', 'process-snackboxes')->middleware('auth:office')->name('import.products');

    // old system week start added to importing/exporting processes.
    // Turns out (after wasting time working out why the route kept becoming unnamed) I've bypassed this by pulling the details from $store.  It also explains why it was working without binding (:) the prop name!
    //Route::get('import-file', 'OfficePantry\WeekStartController@show')->middleware('auth:office');

    // new system week start added to exporting page <-- Not sure I really need to pass it like this though? Going to do it a little differently this time.
    Route::get('exporting', 'OfficePantry\WeekStartController@showAndSet')->name('exporting')->middleware('auth:office');
    // looks like we still need one of these to pull the templating elements in.


//----- Things I don't think I need anymore, commenting out during further testing but will be removed during the great cleanup of 2019! -----//

// The view hasn't been found and I already though this was an unused url, so commenting out for now.
// Could and should delete when I can clean up the file properly of outdated ideas/comments.
// Route::get('import-csv', function () {
//     return view('importCSV');
// });

// No longer used to add preferences, this component is now used in the company search specific (dashboard) feed.
// Route::get('preferences', ['as' => 'preferences', function () {
//     return view('preferences');
// }]);

// This doesn't look like it's in current use either, commmenting out for now, we'll see if it's needed.
// Route::get('routes', function () {
//     return view('routes');
// });

// I have also have a file called new-berry-picklists in the same export folder, so I'm going to assume this has been replaced.
// Commenting out for now.
// Route::get('berry-picklists', function () {
//     return view('exports.berry-picklists');
// });

// Route::get('companies/new', ['as' => 'new-company', function () {
//     return view('new-company');
// }]);

// Old url before it's purpose changed to mass updating only. Rewritten route as view('snackboxes/massupdate') etc.
// Route::get('snackboxes/new', ['as' => 'snackboxes', function () {
//     return view('snackbox-creation');
// }]);

// Route::get('import-products', 'SnackBoxController@upload_products_and_codes')->name('import-products')->middleware('auth:office');
// Using out of date controllers to show all routes, no longer needed, wasn't even really used in the past either.
// Commenting out.
// Route::get('display-routes', 'RoutesController@index');
// Same as above.
// Route::get('companies', 'CompaniesController@index');

// Route::get('snackboxes-multi-company', 'SnackBoxController@index_OP');

// Route::get('snackboxes-multi-company', 'SnackBoxController@auto_process_snackboxes'); <-- Moved into group below.

// Route::get('products', 'ProductsController@index');

// Route::get('/import', 'ImportController@getImport')->name('import');
// Route::post('/import_parse', 'ImportController@parseImport')->name('import_parse');
// Route::post('/import_process', 'ImportController@processImport')->name('import_process');

// This looks like a duplicate url trying to do the same thing as the version at the top of this file.
// Route::view('/', 'welcome');

// Route::get('/password/reset','Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');

// Route::view('/home', 'home')->middleware('auth');


Route::group([
    'namespace' => 'Boxes',
    'prefix' => 'api/boxes',
    //'guard' => 'office',
    // middleware currently throws a 401 unauthenticated error when turned on.

    'middleware' => [
        'office'//,
        // 'auth:api'
    ]
], function () {

                        //----- Current Orders -----//

    // Fruitbox CRUD
    Route::post('fruitbox/add-new-fruitbox', 'FruitBoxController@store');
    Route::put('fruitbox/{id}', 'FruitBoxController@update');
    Route::put('fruitbox/destroy/{id}', 'FruitBoxController@destroy');
    // Milkbox CRUD
    Route::post('milkbox/add-new-milkbox', 'MilkBoxController@store');
    Route::put('milkbox/{id}', 'MilkBoxController@update');
    Route::put('milkbox/destroy/{id}', 'MilkBoxController@destroy');
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
    'prefix' => 'api/company',
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
    Route::put('preferences/remove/{id}', 'PreferencesController@remove');
    // I'm pretty sure this was just used to test the random selection of a new product id,
    // but I'll double check that later, for now it's fine to keep.
    Route::get('random', 'PreferencesController@random');
});


// Essentially this is a group of system functions where, less concerned with specific orders,
// these are the bits that enable the generation/processing/delivery of company orders.
Route::group([
    'namespace' => 'OfficePantry',
    'prefix' => 'api/office-pantry',
    //'middleware' => 'auth:office'
], function () {
    // Assigned routes i.e our own delivery routes.
    Route::post('assigned-route/add-new-assigned-route', 'AssignedRouteController@store');
    Route::post('assigned-routes/update', 'AssignedRouteController@update');
    Route::get('assigned-routes/select', 'AssignedRouteController@listAssignedRoutes');
    Route::put('assigned-route/{id}', 'AssignedRouteController@destroy');
    // Fruit Partner Products i.e 1 x fruitbox = £20, 2 x fruitbox = 18.50 etc.
    Route::post('fruit-partners/add-new-fruitpartner', 'FruitPartnerController@store');
    Route::get('fruit-partners/select', 'FruitPartnerController@listFruitPartners'); // All fruitpartners including Office Pantry (for adding fruit/milk boxes)
    Route::get('fruit-partner-deliveries/export', 'FruitPartnerController@fruitPartnersList'); // Fruit Partners excluding Office Pantry for processing/exporting their orders.
    Route::get('fruit-partners-export/download-zip', 'FruitPartnerController@downloadFruitPartnerZipFile');
    Route::get('fruit_partners/{id}', 'FruitPartnerController@show'); // <-- not sure where this is used, will edit when I stumble across it or take a proper look.
    Route::put('fruit-partners/update/{id}', 'FruitPartnerController@update');
    Route::put('fruit-partners/destroy/{id}', 'FruitPartnerController@destroy');
    // This might get moved to an import/export group later on.
    Route::get('export-fruitpartner-deliveries', 'FruitPartnerController@groupOrdersByFruitPartner');
    Route::get('create-fruitpartner-export-jobs', 'FruitPartnerController@createJobsForEachFruitPartner');

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

Route::post('api/companies/selected', 'OfficeDashboardController@showSelectedCompanyData');
Route::get('api/companies/selected', 'OfficeDashboardController@showSelectedCompanyData');
Route::get('api/companies/{company}', 'OfficeDashboardController@show')->middleware('office'); //

Route::get('api/products/{id}', 'OfficeDashboardController@showProduct'); // but works fine when placed below it here? <-- that was before, now I've moved it again what will happen?
Route::put('api/preferences/{id}', 'OfficeDashboardController@destroy');
