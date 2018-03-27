
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//we load the components into the app vue element
Vue.component('appointment', require('./components/AppointmentComponent.vue'));
Vue.component('hours', require('./components/HoursComponent.vue'));

const app = new Vue({
    el: '#app'
});
