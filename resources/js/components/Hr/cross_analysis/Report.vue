<template>
	<div>
		<div class="panel w-100">
            <div class="panel-body">
            	<div class="pageload" v-if="loader">
	                <div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>
	            </div>
            	<div class="row" v-else v-for="(groupSalary, key) in resultData.getSalary">
					<div class="col-sm-6">
						<table class="table table-bordered table-hover table-head" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
							<thead>
				                <tr>
				                    <th colspan="2">
				                    	<span></span>
				                    </th>
				                    <th colspan="11"></th>
				                </tr>
				                <tr>
				                    <th width="2%">SL</th>
				                    <th>Year, Month</th>
				                    <th>Join Employee</th>
				                    <th>Left Employee</th>
				                    <th>Active Employee</th>
				                    <th class="text-right">Total Salary</th>
				                </tr>
				            </thead>
				            <tbody>
				            	<tr v-for="(salary, key) in groupSalary">
				            		<td>{{ ++key }}</td>
				            		<td>{{ salary.yearmonth | moment('YYYY, MMMM') }}</td>
				            		<td>{{ salary.joinemp }}</td>
				            		<td>{{ salary.leftresignemp }}</td>
				            		<td>{{ salary.empcount }}</td>
				            		<td class="text-right">{{ salary.totalSalary }}</td>
				            	</tr>
				            </tbody>
						</table>
					</div>
					<div class="col-sm-6">
						<barChart :dataParams="groupSalary"></barChart>	
					</div>
				</div>
            </div>
        </div>
	</div>
</template>
<script>
	import moment from 'moment';
    import barChart from './BarChart';
    export default {
        name: "Cross-Analysis",
        data() {
            return {
                errors:[],
                loader: true,
                resultData:[]
            }
        },
        props:['params'],
        components:{
          barChart
        },
        computed:{

        },
        created(){
        	this.reportPreview();
        },
        methods:{
            reportPreview(){
                let data = this.params;
                axios.get('/hr/reports/employee-cross-analysis-report', {
                    params:data
                })
                .then(response => {
                    // console.log(response.data)
                    if(response.data.type === 'success'){
                        this.resultData = response.data;
                    }else{
                        this.resultData = [];
                        $.notify(response.data.message, response.data.type);
                    }
                    this.loader = false
                })
                .catch(error => {
                    console.log(error)
                    this.errors = true;
                })
            }
        },
        mounted(){

            this.reportPreview();
        }
    }
</script>