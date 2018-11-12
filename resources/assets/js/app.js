
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

Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('products', require('./components/Products.vue'));
Vue.component('companies', require('./components/Companies.vue'));
Vue.component('picklists', require('./components/Picklists.vue'));
Vue.component('routes', require('./components/Routes.vue'));
Vue.component('update-picklist-n-routes', require('./components/UpdatePicklistNRoutes.vue'));
Vue.component('update-week-start', require('./components/UpdateWeekStart.vue'));
Vue.component('import-fod-file', require('./components/ImportFodFile.vue'));
Vue.component('import-snacks-n-drinks-file', require('./components/ImportSnacksNDrinksFile.vue'));
Vue.component('import-rejigged-routes-file', require('./components/ImportRejiggedRoutesFile.vue'));
Vue.component('add-new-company', require('./components/AddNewCompany.vue'));
Vue.component('process-snacks-into-templates', require('./components/ProcessSnacksIntoTemplates.vue'));
Vue.component('berry-picklist', require('./components/BerryPicklist.vue'));

const app = new Vue({
    el: '#app'
});
