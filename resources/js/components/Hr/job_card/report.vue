<template>
	<div>
		<div class="panel w-100">
          <div class="panel-body">
            <div class="col-12">
              <div class="pageload" v-if="loader==true">
                <div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>
              </div>
              <div v-else class="job-card-section">
                <button class="btn btn-outline-success btn-sm re_generate" type="button" style="visibility: hidden;width: 1px; padding: 0; margin: 0;" id="re_generate" @click="reGenerate()">
                  <i class="ace-icon fa fa-save bigger-110"></i>
                </button>
                <div class="iq-card">
                    <div class="iq-card-header d-flex mb-0 pt-3 customize-header">
                       <div class="iq-header-title w-100">
                          <div class="row">
                            <div class="col-1">
                                <div class="action-section">
                                    <h4 class="card-title capitalize inline">
                                        <button type="button" onClick="printDiv('result-data-section')" class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Job Card">
                                           <i class="fa fa-print"></i>
                                        </button>
                                        
                                    </h4>
                                </div>
                            </div>
                            <div class="col-9 p-0 text-center">
                                <ul class="color-bar m-0 p-0">
                                    <li><span class="color-label bg-default"></span><span class="lib-label"> Present </span></li>
                                    <li><span class="color-label bg-danger"></span><span class="lib-label"> Absent </span></li>
                                    <li><span class="color-label bg-warning"></span><span class="lib-label"> Weekend </span></li>
                                    <li><span class="color-label bg-primary"></span><span class="lib-label"> Leave</span></li>
                                    <li><span class="color-label bg-success"></span><span class="lib-label"> Outside</span></li>
                                    <li><span class="color-label bg-dark"></span><span class="lib-label"> Extra OT</span></li>
                                </ul>
                                 
                            </div>
                            
                            <div class="col-2 pl-0">
                              <div class="text-right" >
                                <h4 class="inline">
                                    <a class="btn view prev_btn p-0" v-if="viewtype === 'show'" @click="changeMonth('prev')" >
                                      <i class="las la-chevron-left"></i>
                                    </a>
                                    
                                    <a class="btn view next_btn p-0" v-if="viewtype === 'show' && params.max_month !== jobcard.info.yearMonth" @click="changeMonth('next')" >
                                      <i class="las la-chevron-right"></i>
                                    </a>
                                </h4>
                                <h4 class="card-title capitalize inline" v-if="jobcard.lock === 0 && jobcard.info.as_status === '1'">
                                    <router-link :to="{ name: 'job-card-edit', params: { id: jobcard.info.associate_id, month:jobcard.info.yearMonth } }">
                                        <a v-if="viewtype === 'show'" class="btn view list_view no-padding" id="jobcard-edit" @click="generate()">
                                          <i class="fa fa-edit bigger-120"></i>
                                        </a>
                                    </router-link>
                                    <router-link :to="{ name: 'jobcard', params: { id: jobcard.info.associate_id, month:jobcard.info.yearMonth } }">
                                        <a v-if="viewtype === 'edit'" class="btn view list_view no-padding" id="jobcard-show" @click="generate()">
                                          <i class="fa fa-eye bigger-120"></i>
                                        </a>
                                    </router-link>
                                </h4>
                              </div>
                              
                            </div>
                          </div>
                       </div>
                    </div>
                    <div class="iq-card-body p-0">
                        
                        <div class="result-data-section" id="result-data-section">
                            <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:0px auto;">
                                <div class="page-header mb-0" id="brand-head" style="text-align: center;border-bottom:1px double #666;">
                                    <h4 style="margin:4px 10px;" class="mb-0">
                                      {{ jobcard.unit[jobcard.info.as_unit_id].hr_unit_name }}
                                    </h4>
                                    <h5 style="margin:4px 10px" class="mt-0">Job Card Report</h5>
                                    <h5 style="margin:4px 10px">For the month of <b class="f-16" id="result-head">{{ jobcard.info.yearMonth | moment("MMMM, Y") }} </b></h5>
                                </div>
                                <table class="table mb-0" style="width:100%;" cellpadding="5">
                                    <tr>
                                        <th style="width:40%; text-center:left;">
                                           <p style="margin-left: 10px;">ID  # <b v-html="jobcard.info.associate_id"></b> </p>
                                           <p style="margin-left: 10px;">Name : {{ jobcard.info.as_name }}</p>
                                           <p style="margin-left: 10px;">DOJ : {{ jobcard.info.as_doj | moment("DD-MM-YYYY") }}</p>
                                        </th>
                                        <th style="width:40%; text-center:left;">
                                            <p>Oracle ID : {{ jobcard.info.as_oracle_code }} </p>
                                            <p>Section : {{ jobcard.section[jobcard.info.as_section_id].hr_section_name }} </p>
                                            <p>Designation : {{ jobcard.designation[jobcard.info.as_designation_id].hr_designation_name }} </p>
                                        </th>
                                        <th style=" text-center:left;">
                                           <p>Total Present : {{ jobcard.info.totalPresent }} </p>
                                           <p>Total Absent : {{ jobcard.info.totalAbsent }}</p>
                                           
                                           <p v-if="jobcard.info.as_ot === '1'">Total OT : {{ $helpers.numberToTimeClockFormat(jobcard.info.otHour) }}</p>
                                           
                                        </th>
                                    </tr>
                                </table>
                                <cardShow v-if="viewtype === 'show'" :params="params" :jobcard="jobcard"></cardShow>
                                <cardEdit v-if="viewtype === 'edit'" :params="params" :jobcard="jobcard"></cardEdit>
                                
                            </div>
                        </div> 

                    </div>
                </div>
                <div class="overlay"></div>
              </div>
            </div>
          </div>
        </div>
	</div>
</template>

<script>

    import moment from 'moment'
    import VueRouter from 'vue-router';
    import cardShow from './Show';
    import cardEdit from './Edit';
    const router = new VueRouter({
        mode: 'history',
        routes: [
          {
            path: '/hr/operation/job_card?associate=:id&month_year=:month',
            name: 'jobcard',
            component: cardShow,
            props: true
          },
          {
            path: '/hr/operation/job_card?associate=:id&month_year=:month&view=edit',
            name: 'job-card-edit',
            component: cardEdit,
            props: true
          },
        ],
        // linkActiveClass:'active'
    });
    const originalPush = VueRouter.prototype.push
    // Rewrite the push method on the prototype and handle the error message uniformly
    VueRouter.prototype.push = function push(location) {
      return originalPush.call(this, location).catch(err => err)
    }
    export default {
        name: "Job-Card-Report",
        data() {
          return {
            fields: {
                month_year:'',
                associate:'',
            },
            associates:[],
            errors: {},
            jobcard: [],
            loader:true
          }
        },
        props:['params', 'viewtype'],
        router,
        computed: {
          maxMonth(){
            const current = new Date();
            const month = ((current.getMonth()+1) < 10 ? '0' : '') + (current.getMonth()+1);
            return current.getFullYear()+'-'+month;
          }
        },
        components:{
          cardShow, cardEdit
        },
        methods:{
            jobCardPreview(){
              let data = this.params;
              axios.get('/hr/reports/job-card-report-data', {
                  params: {
                    month_year:this.params.month_year,
                    associate:this.params.associate.associate_id
                  }
              })
              .then(response => {
                  console.log(response.data)
                  this.jobcard = response.data;
                  if(response.data.lock === 1 && this.viewtype === 'edit'){
                    $.notify('This monthly activity close', 'error');
                    this.$router.push({ name: 'jobcard', params: { id: this.jobcard.info.associate_id, month: this.jobcard.info.yearMonth } });
                    // this.viewtype = 'show';
                    this.$emit('viewtype', 'show');
                  }
                  setTimeout(() => this.loader = false, 200);
              })
              .catch(error => {
                  console.log(error)
                  this.errors = true;
              })
            },
            generate(){
                if(this.viewtype === 'show'){
                    this.$emit('viewtype', 'edit');
                }else{
                    this.$emit('viewtype', 'show');
                }
            },
            reGenerate(){
                this.loader = true;
                this.jobCardPreview()
            },
            changeMonth(type){
                let curMonth = this.jobcard.info.yearMonth;
                if(type === 'next'){
                    curMonth = moment(curMonth).add(1, 'months').format("YYYY-MM");
                }else{
                    curMonth = moment(curMonth).subtract(1, 'months').format("YYYY-MM");
                }
                this.$emit('monthYear', curMonth);
            }
        },
        mounted(){
          this.loader = true;
          this.jobCardPreview()
        }
    }
</script>
<style>
    .customize-header{
        min-height: 50px !important;
    }
</style>