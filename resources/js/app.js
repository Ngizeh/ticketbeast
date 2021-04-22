import Vue from "vue";

require('./bootstrap');

Vue.prototype.stripeKey = window.stripeKey;

Vue.component('ticket-checkout', require('./components/TicketCheckout.vue').default);

const app = new Vue({
    el: '#app',
});


