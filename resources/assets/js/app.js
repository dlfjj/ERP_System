
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
import BootstrapVue from 'bootstrap-vue';
import $ from 'jquery';
import 'select2';


window.$ = window.jQuery = $;
Vue.use(BootstrapVue);
Vue.component(
    'test',
    require('./components/ExampleComponent.vue')
);

import 'jquery-ui/ui/widgets/datepicker.js';



const app = new Vue({
    el: '#app'
});



// add datepicker jquery
$( ".datepicker" ).datepicker({
    defaultDate: +7,
    showOtherMonths:true,
    autoSize: true,
    dateFormat: 'yy-mm-dd'
});

$('.select2').select2({
    minimumInputLength: 3
});

// $(window).unload(function(){
//     $("#dvLoading").show();
//     $('#dvLoading').fadeOut(116000);
// });
// $(window).load(function(){
//     $("#dvLoading").hide();
// })

