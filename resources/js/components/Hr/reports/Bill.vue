<template>
    <div>
      <div class="row">
          <div class="col">
            <form role="form" method="get" action="" id="formReport">
              <div class="iq-card" id="result-section">
                <div class="iq-card-header d-flex mb-0">
                  <div class="iq-header-title w-100">
                    
                    <report-menu :data="data" :flag="resultFlag" :geturl="geturl" @clicked="onClickFilter">
                      <!-- custom month -->
                      <template slot="nav-month">
                        <div class="col-3"></div>
                      </template>
                      <!-- nav bottom -->
                      <template slot="right-nav-footer" slot-scope="props">
                        <!-- <hr class="mt-2">
                        <input type="month" class="report_date form-control" id="month-year" v-model="month_year" placeholder="Month-Year" value="2021-05" autocomplete="off" /> -->
                        <!-- <div class="form-inline mb-3 mt-10">
                                                    
                          <div class="custom-control custom-radio custom-control-inline">
                             <input type="radio" id="form-range" name="date_type" class="date_type custom-control-input" value="range" checked="" @click="durationType('range')">
                             <label class="custom-control-label cursor-pointer" for="form-range"> Range </label>
                          </div>
                          
                          <div class="custom-control custom-radio custom-control-inline">
                             <input type="radio" id="form-month" name="date_type" class="date_type custom-control-input" value="month" @click="durationType('month')">
                             <label class="custom-control-label cursor-pointer" for="form-month"> Month </label>
                          </div>
                        </div>
                        <div id="month-form" style="display: none;">
                            <div class="form-group has-float-label has-required">
                              <input type="month" class="report_date form-control" id="month-year" v-model="props.month_year" placeholder=" Month-Year" value=""autocomplete="off" />
                              <label for="month-year">Month</label>
                            </div>
                        </div>
                        <div class="row" id="range-form">
                          <div class="col">
                              <div class="form-group has-float-label has-required">
                                  <input type="date" class="report_date datepicker form-control" id="from_date" placeholder="Y-m-d" required="required" v-model="props.from_date" value="" autocomplete="off" />
                                  <label for="from_date">From Date</label>
                              </div>
                          </div>
                          <div class="col">
                              <div class="form-group has-float-label has-required">
                                  <input type="date" class="report_date datepicker form-control" id="to_date" placeholder="Y-m-d" required="required" v-model="props.to_date" value="" autocomplete="off" />
                                  <label for="to_date">To Date</label>
                              </div>
                          </div>
                        </div> -->
                      </template>
                    </report-menu>
                  </div>
                </div>
                <div class="iq-card-body no-padding">
                  <div class="result-data" id="result-data">
                    <router-view :data="resultData"></router-view>
                  </div>
                </div>
              </div>
            </form>
          </div>
      </div>
    </div>
</template>
<script>
    import ReportMenu from './../common/ReportMenu';
    import BillReport from './BillReport';
    import VueRouter from 'vue-router';
    const router = new VueRouter({
        mode: 'history',
        routes: [
            {
              path: '/hr/reports/bill-announcement?:filter',
              name: 'filter',
              component: BillReport,
              props: true
            },
            
        ],
        linkActiveClass:'active'
    });
    export default {
        name: "Report-Bill",
        data() {
          return {
            month_year:'2021-05',
            errors: {},
            url:window.location.origin, 
            resultData:{},
            resultFlag:false,
            geturl: '/hr/reports/bill-announcement-report'
          }
        },
        props:['data'],
        router,
        components:{
          'report-menu':ReportMenu
        },
        methods:{
          onClickFilter (value) {
            this.resultData = value;
            //this.resultFlag = true
          },
          durationType(value){
            if(value == 'month'){
              $("#month-form").show();
              $("#range-form").hide();
            }else if(value == 'range'){
              $("#month-form").hide();
              $("#range-form").show();
            }
          }
        },
        mounted(){
          // console.log(this.resultFlag)
        }
    }
</script>