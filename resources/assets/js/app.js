
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

import 'bootstrap';
import { store } from './vuex/store';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

 Vue.component(
     'passport-clients',
     require('./components/passport/Clients.vue')
 );

 Vue.component(
     'passport-authorized-clients',
     require('./components/passport/AuthorizedClients.vue')
 );

 Vue.component(
     'passport-personal-access-tokens',
     require('./components/passport/PersonalAccessTokens.vue')
 );

 // Temporary file
Vue.component('import-test-file', require('./components/ImportTestFile.vue'));


Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('picklists', require('./components/Picklists.vue'));
Vue.component('routes', require('./components/Routes.vue'));
Vue.component('update-picklist-n-routes', require('./components/UpdatePicklistNRoutes.vue'));
Vue.component('update-week-start', require('./components/UpdateWeekStart.vue'));
Vue.component('import-fod-file', require('./components/ImportFodFile.vue'));
Vue.component('import-snacks-n-drinks-file', require('./components/ImportSnacksNDrinksFile.vue'));
Vue.component('import-rejigged-routes-file', require('./components/ImportRejiggedRoutesFile.vue'));
// Vue.component('add-new-company', require('./components/AddNewCompany.vue')); // This is now outdated and replaced, going to use new component.
Vue.component('process-snacks-into-templates', require('./components/ProcessSnacksIntoTemplates.vue'));
Vue.component('berry-picklist', require('./components/BerryPicklist.vue'));

// Company User Dashboard Information

Vue.component('company-data', require('./components/customer/CompanyData.vue'));
Vue.component('drink-orders', require('./components/customer/DrinkOrders.vue'));
Vue.component('fruit-orders', require('./components/customer/FruitOrders.vue'));
// Vue.component('add-new-fruitbox', require('./components/customer/AddNewFruitbox.vue')); // Changed file name to old but we don't need it right now anyway.
Vue.component('milk-orders', require('./components/customer/MilkOrders.vue'));
Vue.component('other-orders', require('./components/customer/OtherOrders.vue'));
Vue.component('snack-orders', require('./components/customer/SnackOrders.vue'));

// Office User Dashboard Information

// Fruitbox Related
Vue.component('fruitbox', require('./components/admin/fruit/FruitBox.vue'));
Vue.component('fruit-orders-admin', require('./components/admin/fruit/FruitOrdersAdmin.vue'));
Vue.component('add-new-fruitbox', require('./components/admin/fruit/AddNewFruitbox.vue'));
Vue.component('add-new-fruitpartner', require('./components/admin/fruit/AddNewFruitPartner.vue'));
// Fruitbox Archive Related
Vue.component('archived-fruitbox', require('./components/admin/fruit/ArchivedFruitBox.vue'));
Vue.component('archived-fruit-orders-admin', require('./components/admin/fruit/ArchivedFruitOrdersAdmin.vue'));
// Milkbox Related
Vue.component('milkbox', require('./components/admin/milk/MilkBox.vue'));
Vue.component('milk-orders-admin', require('./components/admin/milk/MilkOrdersAdmin.vue'));
Vue.component('add-new-milkbox', require('./components/admin/milk/AddNewMilkbox.vue'));
// Route Related
Vue.component('company-route', require('./components/admin/routes/CompanyRoute.vue'));
Vue.component('routes-admin', require('./components/admin/routes/RoutesAdmin.vue'));
Vue.component('assigned-route', require('./components/admin/routes/AssignedRoute.vue'));
Vue.component('assigned-routes-list', require('./components/admin/routes/AssignedRoutesList.vue'));
Vue.component('add-new-assigned-route', require('./components/admin/routes/AddNewAssignedRoute.vue'));
// Snackbox Related
Vue.component('snackbox-item', require('./components/admin/snacks/SnackBoxItem.vue'));
Vue.component('snackbox', require('./components/admin/snacks/SnackBox.vue'));
Vue.component('snackboxes-admin', require('./components/admin/snacks/SnackBoxesAdmin.vue'));
Vue.component('add-new-snackbox', require('./components/admin/snacks/AddNewSnackbox.vue'));
// Drinkbox Related
Vue.component('drinkbox-item', require('./components/admin/drinks/DrinkBoxItem.vue'));
Vue.component('drinkbox', require('./components/admin/drinks/DrinkBox.vue'));
Vue.component('drink-orders-admin', require('./components/admin/drinks/DrinkOrdersAdmin.vue'));
Vue.component('add-new-drinkbox', require('./components/admin/drinks/AddNewDrinkBox.vue'));
// Otherbox Related
Vue.component('otherbox-item', require('./components/admin/other/OtherBoxItem.vue'));
Vue.component('otherbox', require('./components/admin/other/OtherBox.vue'));
Vue.component('other-orders-admin', require('./components/admin/other/OtherOrdersAdmin.vue'));
Vue.component('add-new-otherbox', require('./components/admin/other/AddNewOtherBox.vue'));
// Product Related
Vue.component('product', require('./components/admin/products/Product.vue'));
Vue.component('products-list', require('./components/admin/products/ProductsList.vue'));
Vue.component('add-new-product', require('./components/admin/products/AddNewProduct.vue'));
Vue.component('select-product', require('./components/admin/products/SelectProduct.vue'));
// Office Pantry Products Related
Vue.component('office-pantry-product-list', require('./components/admin/products/OfficePantryProductList.vue'));
Vue.component('office-pantry-product', require('./components/admin/products/OfficePantryProduct.vue'));
// Preference & Allergy Related
Vue.component('preference', require('./components/admin/preferences/Preference.vue'));
Vue.component('preferences', require('./components/admin/preferences/Preferences.vue'));
Vue.component('add-new-preference', require('./components/admin/preferences/AddNewPreference.vue'));
Vue.component('allergy', require('./components/admin/preferences/Allergy.vue'));
Vue.component('additional-info', require('./components/admin/preferences/AdditionalInfo.vue'));
// Company Related
Vue.component('add-new-company', require('./components/admin/company/AddNewCompany.vue'));
Vue.component('company-details-admin', require('./components/admin/company/CompanyDetailsAdmin.vue'));
Vue.component('search-companies', require('./components/admin/SearchCompanies.vue'));
Vue.component('select-company', require('./components/admin/SelectCompany.vue'));
// System Related
Vue.component('exporting', require('./components/admin/Exporting.vue'));
Vue.component('invoice-options', require('./components/admin/InvoiceOptions.vue'));

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
