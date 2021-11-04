
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

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('products', require('./components/Products.vue').default);
Vue.component('companies', require('./components/Companies.vue').default);
Vue.component('picklists', require('./components/Picklists.vue').default);
Vue.component('routes', require('./components/Routes.vue').default);
Vue.component('update-picklist-n-routes', require('./components/UpdatePicklistNRoutes.vue').default);
Vue.component('update-week-start', require('./components/UpdateWeekStart.vue').default);
Vue.component('import-fod-file', require('./components/ImportFodFile.vue').default);
Vue.component('import-snacks-n-drinks-file', require('./components/ImportSnacksNDrinksFile.vue').default);
Vue.component('import-rejigged-routes-file', require('./components/ImportRejiggedRoutesFile.vue').default);
Vue.component('add-new-company', require('./components/AddNewCompany.vue').default);
Vue.component('process-snacks-into-templates', require('./components/ProcessSnacksIntoTemplates.vue').default);
Vue.component('berry-picklist', require('./components/BerryPicklist.vue').default);

const app = new Vue({
    el: '#app'
});
