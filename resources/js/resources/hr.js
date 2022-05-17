window.Vue = require('vue');
// common
// Vue.component('bar-chart', require('./../components/Hr/BarChart.vue').default);
// report
Vue.component('cross-analysis', require('./../components/Hr/cross_analysis/Index.vue').default);


Vue.component('job-card', require('./../components/Hr/job_card/Index.vue').default);
Vue.component('bill-announce', require('./../components/Hr/reports/Bill.vue').default);
Vue.component('incentive-bonus', require('./../components/Hr/payroll/Incentive.vue').default);
Vue.component('salary-disburse', require('./../components/Hr/operation/SalaryDisbursed.vue').default);
Vue.component('shift-edit', require('./../components/Hr/shift/ShiftEdit.vue').default);