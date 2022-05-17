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
                    <th width="12%">Out Time</th>
                    <th width="8%">OT Hour</th>
                    <th width="10%">Action</th>
                </tr>
            </thead>
            
            <template v-if="jobcard.info.totalDay === 0">
                <tbody>
                    <tr >
                        <td colspan="9" class="text-center">No Record Found!</td>
                    </tr>
                    
                </tbody>
            </template>
            <template v-else v-for="(i) in range(jobcard.info.firstDayMonth, jobcard.info.lastDayMonth)">
              <viewSingleCard :card="jobcard" :date="i" :mode="'edit'" @modalShift="setModalShift" @modalExtraOT="setModalExtraOT" @modalAbsent="setModalAbsent"></viewSingleCard>
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
                    <td v-if="jobcard.lock === 0 && jobcard.info.as_status === '1'">
                        <button class="btn btn-outline-primary pull-right" @click="formSave()" type="button">
                          <i class="ace-icon fa fa-check bigger-110"></i> Save
                        </button>
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- load shift modal -->
        <div class="modal fade apps-modal" id="shiftModal" tabindex="-1" role="dialog" aria-labelledby="appsModalLabel" aria-hidden="true" data-backdrop="false" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="height:auto;width: 380px;left: 40%;top: 50px;background: #fff;box-shadow: rgb(71 70 70) 0px 0px 5px 2px;border-radius: 10px;min-height: 100px;margin-bottom: 50px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    <div class="content-area p-4">
                        <h4 class="font-weight-bold text-center">{{ modalShift.date }}</h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 ml-auto mr-auto">
                                <p class="font-weight-bold mb-2">  </p>
                                <div class="form-group has-float-label has-required select-search-group">
                                    <v-select v-model="modalShift.shift" :options="jobcard.getShiftData" label="hr_shift_start_time">
                                        <template  slot="option" slot-scope="option">
                                          <div style="display: flex; align-items: baseline">
                                            <p>{{ option.hr_shift_name }}</p>
                                            <em style="margin-left: 0.5rem;color:#000;font-weight:blod"
                                              >{{ option.hr_shift_start_time }} - {{ option.hr_shift_end_time }}</em
                                            >
                                          </div>
                                        </template>
                                      </v-select>
                                    
                                    <label :for="'shift-'+modalShift.date">Shift</label>
                                  </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-outline-primary" @click="changeSaveShift()"><i class="fa fa-save"></i> Change</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- load extra OT modal -->
        <div class="modal fade apps-modal" id="extraOTModal" tabindex="-1" role="dialog" aria-labelledby="appsModalLabel" aria-hidden="true" data-backdrop="false" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="height:auto;width: 380px;left: 40%;top: 50px;background: #fff;box-shadow: rgb(71 70 70) 0px 0px 5px 2px;border-radius: 10px;min-height: 100px;margin-bottom: 50px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    <div class="content-area p-4">
                        <h4 class="font-weight-bold text-center">Add Friday OT : {{ modalExtraOT.in_date }}</h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 ml-auto mr-auto">
                                <p class="font-weight-bold mb-2">  </p>
                                <div class="form-group has-float-label has-required ">
                                    <input :id="'fri_in'+modalExtraOT.in_date" type="text" placeholder="HH:mm:ss" class="manual friday form-control" autocomplete="off" v-model="modalExtraOT.in_time" v-on:change="onchangeTime(modalExtraOT.in_time, 'in_time')">
                                    <label :for="'fri_in'+modalExtraOT.in_date">In Time</label>
                                </div>
                                <div class="form-group has-float-label has-required ">
                                    <input :id="'fri_out'+modalExtraOT.in_date" type="text" placeholder="HH:mm:ss" class="manual friday form-control" v-model="modalExtraOT.out_time" autocomplete="off" v-on:change="onchangeTime(modalExtraOT.out_time, 'out_time')">
                                    <label :for="'fri_out'+modalExtraOT.in_date">Out Time</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-outline-primary" @click="saveExtraOT()"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- load absent reason modal -->
        <div class="modal fade apps-modal" id="absentModal" tabindex="-1" role="dialog" aria-labelledby="appsModalLabel" aria-hidden="true" data-backdrop="false" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="height:auto;width: 380px;left: 40%;top: 50px;background: #fff;box-shadow: rgb(71 70 70) 0px 0px 5px 2px;border-radius: 10px;min-height: 100px;margin-bottom: 50px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    <div class="content-area p-4">
                        <h4 class="font-weight-bold text-center">Absent : {{ modalAbsent.date }}</h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 ml-auto mr-auto">
                                <p class="font-weight-bold mb-2">  </p>
                                <div class="form-group has-float-label has-required ">
                                    <textarea v-model="modalAbsent.comment" class="form-control" :id="'reason'+modalAbsent.date" rows="3"></textarea>
                                    <label :for="'reason'+modalAbsent.date">Absent reason</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-outline-primary" @click="saveAbsentReason()"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</template>
<script>
    import moment from 'moment'
    import viewSingleCard from './ViewSingleCard';
    
    export default {
        name: "job-card-edit",
        data() {
          return {
            fields: {
                month_year:'',
                associate:'',
            },
            associates:[],
            errors: {},
            //jobcard: [],
            loader:true,
            shifts:[],
            modalShift:{},
            modalExtraOT:{},
            modalAbsent:{},
            selectedShift:''
            
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
            onchangeTime(time, type){
                let formatTime = moment(time.toString(),"LT").format('HH:mm:ss');
                if(formatTime === 'Invalid date'){
                    $.notify('Invalid Date Format', 'error')
                    this.modalExtraOT[type] = '';
                }else{
                    this.modalExtraOT[type] = formatTime;
                }
            },
            formSave(){
                if($('.single_row').length > 0){
                    $('.app-loader').show();
                    let totalRow = ($(".single_row").length) - 1;
                    $(".single_row").each((i, element) => {
                        setTimeout(() => {
                            $(element).click()
                            if(i === totalRow){
                                $('.app-loader').hide();
                            }
                        }, i * 300);
                    });
                }else{
                    let totalRow = ($(".single_date").length) - 1;
                    if($(".single_date").length > 0){
                        $('.app-loader').show();
                    }
                    $(".single_date").each((i, element) => {
                        setTimeout(() => {
                            $(element).click()
                            if(i === totalRow){
                                this.salaryProcess()
                            }
                        }, i * 300);
                    });
                }
            },
            salaryProcess(){
                setTimeout(() => {
                    let self = this;
                    axios.post('/hr/operation/individual-salary-process', {
                        info: self.jobcard.info
                    })
                    .then(response => {
                        $('.app-loader').hide();
                        if(response.data.type === 'success'){
                            self.jobcard.info.otHour = response.data.value.otCount;
                            self.jobcard.info.totalAbsent = response.data.value.absentCount;
                            self.jobcard.info.totalPresent = response.data.value.presentCount;
                        }
                        $.notify(response.data.message, response.data.type);
                    })
                    .catch(error => {
                        console.log(error)
                    })
                },400);
            },
            shiftLoad(){
                let self = this;
                axios.get('/hr/operation/job-card-unit-shift', {
                    params: {
                        as_unit_id: this.jobcard.info.as_unit_id
                    }
                })
                .then(response => {
                    // console.log(response.data)
                    if(response.data !== 'error'){
                        self.jobcard.getShiftData = response.data;
                    }else{
                        self.jobcard.getShiftData = [];
                    }
                })
                .catch(error => {
                    console.log(error)
                })
            },
            setModalShift(modalShift){
                this.modalShift = modalShift;
            },
            setModalExtraOT(modalExtraOT){
                this.modalExtraOT = modalExtraOT;
            },
            setModalAbsent(modalAbsent){
                this.modalAbsent = modalAbsent;
            },
            changeSaveShift(){
                $('.app-loader').show();
                let self = this;
                axios.post('/hr/operation/job-card-shift-change', self.modalShift)
                .then(response => {
                    console.log(response.data);
                    if(response.data.type === 'success'){
                        location.reload();
                    }else{
                        $.notify(response.data.message, response.data.type);
                    }
                    $('.app-loader').hide();
                })
                .catch(error => {
                    console.log(error)
                    self.errors = true;
                })
            },
            saveExtraOT(){
                let self = this;
                if((self.modalExtraOT.in_time && self.modalExtraOT.in_time !== '') ||  (self.modalExtraOT.out_time && self.modalExtraOT.out_time !== '')){
                    axios.post('/hr/operation/special-ot-save', self.modalExtraOT)
                    .then(response => {
                        console.log(response.data);

                        if(response.data.type === 'success'){
                            // window.reload=response.data.url
                            location.reload();
                        }else{
                            $.notify(response.data.message, response.data.type);
                        }
                        
                    })
                    .catch(error => {
                        console.log(error)
                        self.errors = true;
                    })
                }else{
                    $.notify('In time & out time empty', 'error');
                }
                
            },
            saveAbsentReason(){
                let self = this;
                if((self.modalAbsent.date !== '')){
                    axios.post('/hr/operation/job-card-absent-reason', self.modalAbsent)
                    .then(response => {
                        console.log(response.data);

                        if(response.data.type === 'success'){
                            location.reload();
                        }
                        $.notify(response.data.message, response.data.type);
                        
                    })
                    .catch(error => {
                        console.log(error)
                        self.errors = true;
                    })
                }else{
                    $.notify('Something wrong, please try again', 'error');
                }
            }
            
        },
        mounted(){
            this.loader = true;
            this.shiftLoad()
            setTimeout(() => this.loader = false, 200);
            
        }
    }
</script>