
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import vSelect from 'vue-select';
Vue.component('v-select', vSelect);


import 'bootstrap';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('products', require('./components/Products.vue'));
Vue.component('companies', require('./components/Companies.vue'));
Vue.component('picklists', require('./components/Picklists.vue'));
Vue.component('routes', require('./components/Routes.vue'));
Vue.component('update-picklist-n-routes', require('./components/UpdatePicklistNRoutes.vue'));
Vue.component('import-file', require('./components/ImportFile.vue'));

const app = new Vue({
    el: '#app'
});
