<template>
	<div>
		<div class="panel w-100">
      <div class="panel-body">
        <div class="offset-1 col-10">
          <div class="pageload" v-if="loader==true">
            <div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>
          </div>
          <div v-else class="job-card-section">
            <div class="iq-card">
                <div class="iq-card-header d-flex mb-0">
                   <div class="iq-header-title w-100">
                      <div class="row">
                        <div class="col-3">
                            <div class="action-section">
                                <h4 class="card-title capitalize inline">
                                    <button type="button" onClick="printDiv('result-data-section')" class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Job Card">
                                       <i class="fa fa-print"></i>
                                    </button>
                                    
                                </h4>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                          <h4 class="card-title capitalize inline">
                            <a class="btn view prev_btn" >
                              <i class="las la-chevron-left"></i>
                            </a>
                            
                            <b class="f-16" id="result-head">{{ jobcard.info.yearMonth | moment("MMMM, Y") }} </b>
                            <a class="btn view next_btn" >
                              <i class="las la-chevron-right"></i>
                            </a> 
                          </h4>
                        </div>
                        <div class="col-3">
                        
                          <div class="text-right" v-if="jobcard.lock == 0 && jobcard.info.as_status == 1">
                            <h4 class="card-title capitalize inline">
                            <a href='' class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Manual Edit Job Card">
                              <i class="fa fa-edit bigger-120"></i>
                            </a>
                            </h4>
                          </div>
                          
                        </div>
                      </div>
                   </div>
                </div>
                <div class="iq-card-body p-0">
                    <ul class="color-bar">
                        <li><span class="color-label bg-default"></span><span class="lib-label"> Present </span></li>
                        <li><span class="color-label bg-danger"></span><span class="lib-label"> Absent </span></li>
                        <li><span class="color-label bg-warning"></span><span class="lib-label"> Weekend </span></li>
                        <li><span class="color-label bg-primary"></span><span class="lib-label"> Leave</span></li>
                        <li><span class="color-label bg-success"></span><span class="lib-label"> Outside</span></li>
                        <li><span class="color-label bg-dark"></span><span class="lib-label"> Extra OT</span></li>
                    </ul>
                    <div class="result-data-section" id="result-data-section">
                        <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:0px auto;">
                            <div class="page-header" id="brand-head" style="border-bottom:2px double #666; text-align: center;">
                                <h3 style="margin:4px 10px">
                                  {{ jobcard.unit[jobcard.info.as_unit_id].hr_unit_name }}
                                </h3>
                                <h5 style="margin:4px 10px">Job Card Report</h5>
                                <h5 style="margin:4px 10px">For the month of {{ jobcard.info.yearMonth | moment("MMMM , Y") }}</h5>
                            </div>
                            <table class="table" style="width:100%;border:1px solid #ccc;margin-bottom:0;padding:10px 0px;font-size:14px;text-align:left" cellpadding="5">
                                <tr>
                                    <th style="width:40%">
                                       <p style="margin-left: 10px;">ID  # <b v-html="jobcard.info.associate_id"></b> </p>
                                       <p style="margin-left: 10px;">Name : {{ jobcard.info.as_name }}</p>
                                       <p style="margin-left: 10px;">DOJ : {{ jobcard.info.as_doj | moment("DD-MM-YYYY") }}</p>
                                    </th>
                                    <th>
                                        <p>Oracle ID : {{ jobcard.info.as_oracle_code }} </p>
                                        <p>Section : {{ jobcard.section[jobcard.info.as_section_id].hr_section_name }} </p>
                                        <p>Designation : {{ jobcard.designation[jobcard.info.as_designation_id].hr_designation_name }} </p>
                                    </th>
                                    <th>
                                       <p>Total Present : {{ jobcard.info.totalPresent }} </p>
                                       <p>Total Absent : {{ jobcard.info.totalAbsent }}</p>
                                       
                                       <p v-if="jobcard.info.as_ot==1">Total OT : {{ jobcard.info.otHour }}</p>
                                       
                                    </th>
                                </tr>
                            </table>

                            <table class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;font-size:13px;display: block;overflow-x: auto;white-space: nowrap;" cellpadding="2" cellspacing="0" border="1" align="center">
                                <thead>
                                    <tr>
                                        <th width="10%">Date</th>
                                        <th width="20%">Attendance Status</th>
                                        <th width="10%">Floor</th>
                                        <th width="10%">Line</th>
                                        <th width="10%">In Time</th>
                                        <th width="10%">Out Time</th>
                                        <th width="10%">OT Hour</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="jobcard.info.totalDay === 0">
                                        <td colspan="7" class="text-center">No Record Found!</td>
                                    </tr>
                                    <template v-else v-for="(i) in range(jobcard.info.firstDayMonth, jobcard.info.lastDayMonth)">
                                      <tr v-if="jobcard.specialAttDate[i]">
                                        <td>{{ i }}</td>
                                        <td class="bg-dark text-white">Present</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                          <p v-if="jobcard.specialAttDate[i].in_time != '' && jobcard.specialAttDate[i].in_time != null">
                                            {{ jobcard.specialAttDate[i].in_time | moment('H:mm') }}
                                          </p>
                                        </td>
                                        <td>
                                          <p v-if="jobcard.specialAttDate[i].out_time != ''">
                                            {{ jobcard.specialAttDate[i].out_time | moment('H:mm') }}
                                          </p>
                                        </td>
                                        <td>
                                            {{ jobcard.specialAttDate[i].ot_hour }}
                                        </td>
                                      </tr>
                                      <viewSingleCard :card="jobcard" :date="i"></viewSingleCard>
                                    </template>
                                </tbody>
                                <tfoot style="border-top:2px double #999">
                                    <tr>
                                        <th style="text-align:right">Total present</th>
                                        <th>{{ jobcard.info.totalPresent }}</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align:right">Total Over Time</th>
                                        <th>
                                          <b v-if="jobcard.info.as_ot == 1"></b>
                                          <b v-else>-</b>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
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
    // import moment from 'moment'
    import View from './View';
    import VueRouter from 'vue-router';
    import viewSingleCard from './ViewSingleCard';
    const router = new VueRouter({
        mode: 'history',
        routes: [
          {
            path: '/hr/operation/job_card?associate=:id&month_year=:month',
            name: 'jobcard',
            component: View,
            props: true
          },
        ],
        linkActiveClass:'active'
    });
    const originalPush = VueRouter.prototype.push
    // Rewrite the push method on the prototype and handle the error message uniformly
    VueRouter.prototype.push = function push(location) {
      return originalPush.call(this, location).catch(err => err)
    }
    export default {
        name: "Job-Card-View",
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
        props:['params'],
        router,
        computed: {
          maxMonth(){
            const current = new Date();
            const month = ((current.getMonth()+1) < 10 ? '0' : '') + (current.getMonth()+1);
            return current.getFullYear()+'-'+month;
          }
        },
        components:{
          viewSingleCard
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
                  
                  setTimeout(() => this.loader = false, 200);
              })
              .catch(error => {
                  console.log(error)
                  this.errors = true;
              })
            },
            range : function (startDate, endDate) {
              var now = startDate, dates = [];
      
              while(moment(startDate) <= moment(endDate)){
                dates.push(startDate);
                startDate = moment(startDate).add(1, 'days').format("YYYY-MM-DD");
              }
              return dates;
            }
        },
        mounted(){
          this.loader = true;
          this.jobCardPreview()
        }
    }
</script>