require('./bootstrap');

window.Vue = require('vue');

// register global components
import vSelect from 'vue-select'

Vue.component('v-select', vSelect)
import 'vue-select/dist/vue-select.css'; 

import helpers from './resources/helpers'

const plugin = {
  install () {
    Vue.helpers = helpers
    Vue.prototype.$helpers = helpers
  }
}

Vue.use(plugin);

window.moment = require('moment');
Vue.use(require('vue-moment'));

import Popper from 'vue-popperjs';
import 'vue-popperjs/dist/vue-popper.css';
Vue.component('popper', Popper);
// import mixin from './plugins/mixin' 
//Vue.component('create-shift', require('./components/hr/shift/ShiftCreate.vue').default);

// import  hr js components
require('./resources/hr');

const app = new Vue({
    el: '#app',
    http: {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    } 
});

