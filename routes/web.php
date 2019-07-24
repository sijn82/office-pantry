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
// Route::post('exporting', 'CompanyRouteController@import')->name('import-rejigged-routes');

// If I need more of these I will group them but for now it's just this solo entry.
Route::post('exporting', 'Company\CompanyRouteController@import')->name('import-rejigged-routes'); // <-- Interesting url though, I've also used it again for something else further down.

// I'm pretty sure these are all examples of when I wanted to create a specific url with a new blade template to add vue components too.
// I'm also pretty sure they only look like this and have a singular purpose because 
// a) it worked! (and)
// b) I hadn't/haven't spent the time to realise and maximise their use... yet.

// Main homepage for guest/office/warhouse login - no authentication on route and end point for logged out users
Route::get('/', function () {
    return view('welcome');
});

// I think was auto generated?  Or I'm going in a different direction, either way I can probably remove it at a later date.
// Actually this will serve the customer (after logging in) with a landing page featuring their listed company.... I think?
Route::get('/home', 'HomeController@showCompany')->name('home')->middleware('auth');

// Register for office or warehouse login
// Get the registration form
Route::get('/register/office', 'Auth\RegisterController@showOfficeRegisterForm')->name('register.office');
Route::get('/register/warehouse', 'Auth\RegisterController@showWarehouseRegisterForm')->middleware('auth:office')->name('register.warehouse');
// Post the submitted form details
Route::post('/register/office', 'Auth\RegisterController@createOffice');
Route::post('/register/warehouse', 'Auth\RegisterController@createWarehouse');
// Get the login form 
Route::get('/login/office', 'Auth\LoginController@showOfficeLoginForm')->name('login.office');
Route::get('/login/warehouse', 'Auth\LoginController@showWarehouseLoginForm')->name('login.warehouse');
// Post the submitted form details
Route::post('/login/office', 'Auth\LoginController@officeLogin');
Route::post('/login/warehouse', 'Auth\LoginController@warehouseLogin');
// If Login details matched the corresponding db table info, redirect the user to their dashboard to carry out tasks or, just hangout.
Route::view('/office', 'office')->middleware('auth:office')->name('office');
Route::view('/warehouse', 'warehouse')->middleware('auth:warehouse')->name('warehouse');

// Only those with office authentication can access these routes.
// All routes contained here now have the prefix 'office' to clearly indicate the login access needed to visit the url.
Route::group(['middleware' => ['web','auth:office'], 'prefix' => 'office'], function () {
    
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
    Route::get('exporting', ['as' => 'exporting', function () {
        return view('exporting');
    }]);

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
