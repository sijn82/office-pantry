
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
// import vSelect from 'vue-select';
// Vue.component('v-select', vSelect);

import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue);

import { ValidationProvider, extend } from 'vee-validate';
// Register it globally
Vue.component('ValidationProvider', ValidationProvider);

import 'bootstrap';
import { store } from './vuex/store';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

 Vue.component(
     'passport-clients',
     require('./components/passport/Clients.vue').default
 );

 Vue.component(
     'passport-authorized-clients',
     require('./components/passport/AuthorizedClients.vue').default
 );

 Vue.component(
     'passport-personal-access-tokens',
     require('./components/passport/PersonalAccessTokens.vue').default
 );

 // Temporary file
Vue.component('import-test-file', require('./components/ImportTestFile.vue').default);


Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('picklists', require('./components/Picklists.vue').default);
Vue.component('routes', require('./components/Routes.vue').default);
Vue.component('update-picklist-n-routes', require('./components/UpdatePicklistNRoutes.vue').default);
Vue.component('update-week-start', require('./components/UpdateWeekStart.vue').default);
Vue.component('import-fod-file', require('./components/ImportFodFile.vue').default);
Vue.component('import-snacks-n-drinks-file', require('./components/ImportSnacksNDrinksFile.vue').default);
Vue.component('import-rejigged-routes-file', require('./components/ImportRejiggedRoutesFile.vue').default);
// Vue.component('add-new-company', require('./components/AddNewCompany.vue')); // This is now outdated and replaced, going to use new component.
Vue.component('process-snacks-into-templates', require('./components/ProcessSnacksIntoTemplates.vue').default);
Vue.component('berry-picklist', require('./components/BerryPicklist.vue').default);

// Company User Dashboard Information

Vue.component('company-data', require('./components/customer/CompanyData.vue').default);
Vue.component('drink-orders', require('./components/customer/DrinkOrders.vue').default);
Vue.component('fruit-orders', require('./components/customer/FruitOrders.vue').default);
// Vue.component('add-new-fruitbox', require('./components/customer/AddNewFruitbox.vue')); // Changed file name to old but we don't need it right now anyway.
Vue.component('milk-orders', require('./components/customer/MilkOrders.vue').default);
Vue.component('other-orders', require('./components/customer/OtherOrders.vue').default);
Vue.component('snack-orders', require('./components/customer/SnackOrders.vue').default);

// Office User Dashboard Information

// Fruitbox Related
Vue.component('fruitbox', require('./components/admin/fruit/FruitBox.vue').default);
Vue.component('fruit-orders-admin', require('./components/admin/fruit/FruitOrdersAdmin.vue').default);
Vue.component('add-new-fruitbox', require('./components/admin/fruit/AddNewFruitbox.vue').default);
// Fruitbox Archive Related
Vue.component('archived-fruitbox', require('./components/admin/fruit/ArchivedFruitBox.vue').default);
Vue.component('archived-fruit-orders-admin', require('./components/admin/fruit/ArchivedFruitOrdersAdmin.vue').default);
// Milkbox Related
Vue.component('milkbox', require('./components/admin/milk/MilkBox.vue').default);
Vue.component('milk-orders-admin', require('./components/admin/milk/MilkOrdersAdmin.vue').default);
Vue.component('add-new-milkbox', require('./components/admin/milk/AddNewMilkbox.vue').default);
// Milkbox Archive Related
Vue.component('archived-milkbox', require('./components/admin/milk/ArchivedMilkBox.vue').default);
Vue.component('archived-milk-orders-admin', require('./components/admin/milk/ArchivedMilkOrdersAdmin.vue').default);
// Fruit Partner Related
Vue.component('add-new-fruitpartner', require('./components/admin/fruit/AddNewFruitPartner.vue').default);
Vue.component('fruitpartners-admin', require('./components/admin/fruit/FruitPartnersAdmin.vue').default);
Vue.component('fruitpartner', require('./components/admin/fruit/FruitPartner.vue').default);
// Route Related
Vue.component('company-route', require('./components/admin/routes/CompanyRoute.vue').default);
Vue.component('routes-admin', require('./components/admin/routes/RoutesAdmin.vue').default);
Vue.component('assigned-route', require('./components/admin/routes/AssignedRoute.vue').default);
Vue.component('assigned-routes-list', require('./components/admin/routes/AssignedRoutesList.vue').default);
Vue.component('add-new-assigned-route', require('./components/admin/routes/AddNewAssignedRoute.vue').default);
// Snackbox Related
Vue.component('snackbox-item', require('./components/admin/snacks/SnackBoxItem.vue').default);
Vue.component('snackbox', require('./components/admin/snacks/SnackBox.vue').default);
Vue.component('snackboxes-admin', require('./components/admin/snacks/SnackBoxesAdmin.vue').default);
Vue.component('add-new-snackbox', require('./components/admin/snacks/AddNewSnackbox.vue').default);
Vue.component('mass-update-snackbox', require('./components/admin/snacks/MassUpdateSnackBox.vue').default);
// Snackbox Archive Related
Vue.component('archived-snackbox-item', require('./components/admin/snacks/ArchivedSnackBoxItem.vue').default);
Vue.component('archived-snackbox', require('./components/admin/snacks/ArchivedSnackBox.vue').default);
Vue.component('archived-snackboxes-admin', require('./components/admin/snacks/ArchivedSnackBoxesAdmin.vue').default);
// Drinkbox Related
Vue.component('drinkbox-item', require('./components/admin/drinks/DrinkBoxItem.vue').default);
Vue.component('drinkbox', require('./components/admin/drinks/DrinkBox.vue').default);
Vue.component('drink-orders-admin', require('./components/admin/drinks/DrinkOrdersAdmin.vue').default);
Vue.component('add-new-drinkbox', require('./components/admin/drinks/AddNewDrinkBox.vue').default);
// Drinkbox Archive Related
Vue.component('archived-drinkbox-item', require('./components/admin/drinks/ArchivedDrinkBoxItem.vue').default);
Vue.component('archived-drinkbox', require('./components/admin/drinks/ArchivedDrinkBox.vue').default);
Vue.component('archived-drink-orders-admin', require('./components/admin/drinks/ArchivedDrinkOrdersAdmin.vue').default);
// Otherbox Related
Vue.component('otherbox-item', require('./components/admin/other/OtherBoxItem.vue').default);
Vue.component('otherbox', require('./components/admin/other/OtherBox.vue').default);
Vue.component('other-orders-admin', require('./components/admin/other/OtherOrdersAdmin.vue').default);
Vue.component('add-new-otherbox', require('./components/admin/other/AddNewOtherBox.vue').default);
// Otherbox Archive related
Vue.component('archived-otherbox-item', require('./components/admin/other/ArchivedOtherBoxItem.vue').default);
Vue.component('archived-otherbox', require('./components/admin/other/ArchivedOtherBox.vue').default);
Vue.component('archived-other-orders-admin', require('./components/admin/other/ArchivedOtherOrdersAdmin.vue').default);

// Product Related
Vue.component('product', require('./components/admin/products/Product.vue').default);
Vue.component('products-list', require('./components/admin/products/ProductsList.vue').default);
Vue.component('add-new-product', require('./components/admin/products/AddNewProduct.vue').default);
Vue.component('select-product', require('./components/admin/products/SelectProduct.vue').default);
// Office Pantry Products Related
Vue.component('add-new-office-pantry-product', require('./components/admin/products/AddNewOfficePantryProduct.vue').default);
Vue.component('office-pantry-product-list', require('./components/admin/products/OfficePantryProductList.vue').default);
Vue.component('office-pantry-product', require('./components/admin/products/OfficePantryProduct.vue').default);
// Preference & Allergy Related
Vue.component('preference', require('./components/admin/preferences/Preference.vue').default);
Vue.component('preferences', require('./components/admin/preferences/Preferences.vue').default);
Vue.component('preferences-admin', require('./components/admin/preferences/PreferencesAdmin.vue').default);
Vue.component('add-new-preference', require('./components/admin/preferences/AddNewPreference.vue').default);
Vue.component('allergy', require('./components/admin/preferences/Allergy.vue').default);
Vue.component('additional-info', require('./components/admin/preferences/AdditionalInfo.vue').default);
// Company Related
Vue.component('add-new-company', require('./components/admin/company/AddNewCompany.vue').default);
Vue.component('company-details-admin', require('./components/admin/company/CompanyDetailsAdmin.vue').default);
Vue.component('search-companies', require('./components/admin/SearchCompanies.vue').default);
Vue.component('select-company', require('./components/admin/SelectCompany.vue').default);
// System Related
Vue.component('exporting', require('./components/admin/Exporting.vue').default);
Vue.component('invoice-options', require('./components/admin/InvoiceOptions.vue').default);
Vue.component('cron-list', require('./components/admin/CronList.vue').default);
Vue.component('jobs', require('./components/admin/Jobs.vue').default);

// This works which is great but I should move this somewhere else, maybe somewhere I can add further directives and their associated functions?
function debounce(fn, delay = 300) {
	var timeoutID = null;

    return function () {
		clearTimeout(timeoutID);

        var args = arguments;
        var that = this;

        timeoutID = setTimeout(function () {
        	fn.apply(that, args);
        }, delay);
    }
};

// We can add it globally (like now) or locally!
Vue.directive('debounce', (el, binding) => {
	if (binding.value !== binding.oldValue) {
		// window.debounce is our global function what we defined at the very top!
		el.oninput = debounce(ev => {
			el.dispatchEvent(new Event('change'));
		}, parseInt(binding.value) || 300);
	}
});

const app = new Vue({
    el: '#app',
    store,
    fruitboxes: [],
});
