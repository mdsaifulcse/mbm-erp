<template>
	<div>
        <div class="pageload" v-if="loader==true">
            <div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>
        </div>
		<table v-else class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;font-size:13px;display: block;overflow-x: auto;white-space: nowrap;" cellpadding="2" cellspacing="0" border="1" align="center">
            <thead>
                <tr>
                    <th width="10%">Date</th>
                    <th width="20%">Attendance Status</th>
                    <th width="10%">Floor</th>
                    <th width="8%">Line</th>
                    <th width="10%">Shift</th>
                    <th width="12%">In Time</th>
                    <th width="10%">Out Time</th>
                    <th width="10%">OT Hour</th>
                </tr>
            </thead>
            <template v-if="jobcard.info.totalDay === 0">
                <tbody>
                    <tr>
                        <td colspan="8" class="text-center">
                            <span v-if="jobcard.info.as_status !== '1' && jobcard.info.as_status !== '6'">This employee has {{ jobcard.as_status_name }} on {{ jobcard.info.as_status_date | moment('DD-MMMM-YYYY') }}</span>
                            <span v-else>No Record Found!</span>
                        </td>
                    </tr>
                </tbody>
            </template>
            <template v-else v-for="(i) in range(jobcard.info.firstDayMonth, jobcard.info.lastDayMonth)">
              <viewSingleCard :card="jobcard" :date="i"  :mode="'show'"></viewSingleCard>
            </template>
            <tfoot style="border-top:2px double #999">
                <tr>
                    <th style="text-align:right">Total present</th>
                    <th>{{ jobcard.info.totalPresent }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th style="text-align:right">Total Over Time</th>
                    <th>
                      <b v-if="jobcard.info.as_ot === '1'">{{ $helpers.numberToTimeClockFormat(jobcard.info.otHour) }}</b>
                      <b v-else>-</b>
                    </th>
                </tr>
            </tfoot>
        </table>
	</div>
</template>
<script>
    import moment from 'moment'
    import viewSingleCard from './ViewSingleCard';
    
    export default {
        name: "job-card-show",
        data() {
          return {
            fields: {
                month_year:'',
                associate:'',
            },
            associates:[],
            errors: {},
            //jobcard: [],
            loader:true
          }
        },
        props:['params', 'jobcard'],
        components:{
          viewSingleCard
        },
        methods:{
            
            range : function (startDate, endDate) {
              var now = startDate, dates = [];
      
              while(moment(startDate) <= moment(endDate)){
                dates.push(startDate);
                startDate = moment(startDate).add(1, 'days').format("YYYY-MM-DD");
              }
              return dates;
            },
        },
        mounted(){
          this.loader = true;
          setTimeout(() => this.loader = false, 200);
        }
    }
</script>