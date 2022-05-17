<template>
    <tbody>
        <tr v-if="specialAtt.in_date">
            <td>{{ attDate }}</td>
            <td class="bg-dark text-white">Present</td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <input v-if="mode === 'edit'" class="intime manual form-control" :id="'spintime-'+date" type="text" v-model="specialAtt.in_time" placeholder="HH:mm:ss" autocomplete="off" v-on:change="onchangeTime(specialAtt.in_time, 'in_time', 1)" >
                <p v-else>
                    {{ specialAtt.in_time }}
                </p>
            </td>
            <td>
                <input v-if="mode === 'edit'" class="outtime manual form-control" :id="'spouttime-'+date" type="text" v-model="specialAtt.out_time" placeholder="HH:mm:ss"  autocomplete="off" v-on:change="onchangeTime(specialAtt.out_time, 'out_time', 1)">
                <p v-else>
                    {{ specialAtt.out_time }}
                </p>
            </td>
            <td>
                {{ specialAtt.ot }}
            </td>
            <td v-if="mode === 'edit'">
                <button v-if="specialAtt.editStatus === 1" class="btn btn-outline-primary btn-sm spsingle_row" type="button" @click="spSingleRow(specialAtt)">
                  <i class="ace-icon fa fa-check bigger-110"></i>
                </button>
            </td>
        </tr>
        <tr>
            <td>
                {{ attDate }}
            </td>
            <td :class="[{ 'text-white': bgColor != 'bg-default'}, bgColor]">
                
                <span v-if="card.info.as_status !== '1' && card.info.as_status !== '6' && date === card.info.as_status_date" class="label label-danger">{{ card.as_status_name }}</span>
                <span v-else>
                    <a v-if="openStatus === 0" @click="absentClickEvent(date)">{{ attStatus }}</a>
                    <p v-else>{{ attStatus }} </p>
                </span>
                
                <span v-if="late_status === 1" style="height: auto;float:right;" class="label label-warning pull-right">Late</span>

                <span v-if="remarks !== ''" style="height: auto;float:right;" class="label label-danger pull-right">{{ remarks }} </span>
                
                <span v-if="date == card.info.lastDayMonth && card.info.as_doj | moment('YYYY-MM') == card.yearMonth" style="height: auto;float:right;" class="label label-danger pull-right">Joining</span>
                
                <span v-if="Object.keys(card.generalDate).includes(date)" style="height: auto;float:right;" class="label label-warning pull-right">General</span>
                
                <span v-if="ot_day !== 0 && card.info.as_ot === '1'" style="height: auto;float:right;" class="label label-warning pull-right">OT</span>

            </td>
            <td>
                {{ floor }}
            </td>
            <td>
                {{ line }}
            </td>
            <td>
                <a v-if="mode === 'edit'" @click="shiftClickEvent(date)" data-toggle="tooltip" data-placement="top" title="" :data-original-title="shift.name">{{ shift.start_end }}</a>
                <span v-else>{{ shift.start_end }}</span>
            </td>
            <td>
                <input v-if="mode === 'edit'" class="intime manual form-control" :id="'intime-'+date" type="text" v-model="intime" placeholder="HH:mm:ss" autocomplete="off" :disabled="attDisable" v-on:change="onchangeTime(intime, 'intime',0)" >
                <p v-else>
                    {{ intime }}
                </p>
            </td>
            <td>
                <input v-if="mode === 'edit'" class="outtime manual form-control" :id="'outtime-'+date" type="text" v-model="outtime"  placeholder="HH:mm:ss"  autocomplete="off" :disabled="attDisable" v-on:change="onchangeTime(outtime, 'outtime',0)">
                <p v-else>
                    {{ outtime }}
                </p>
            </td>
            <td>
                {{ ot }}
            </td>
            <td v-if="mode === 'edit'">
                <button v-if="saveMode === true && saveLoader === false" class="btn btn-outline-primary btn-sm single_row" type="button" @click="singleRow(date)" :id="'modify-'+date" data-toggle="tooltip" data-placement="top" title="" data-original-title="Save Attendance">
                  <i class="ace-icon fa fa-check bigger-110"></i>
                </button>
                <button v-if="saveLoader === true" class="btn btn-outline-primary btn-sm" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Saving Process">
                  <i class="ace-icon fa fa-spin fa-spinner bigger-110"></i>
                </button>
                <button v-if="attDisable === false" class="btn btn-outline-success btn-sm single_date" type="button" style="visibility: hidden;width: 1px; padding: 0; margin: 0;" @click="singleSave(date)">
                  <i class="ace-icon fa fa-save bigger-110"></i>
                </button>

                <button v-if="!card.specialAttDate[date] && shift.ot_shift !== null" class="btn btn-outline-warning pull-right btn-sm" type="button" @click="extraOTClickEvent(date)" data-toggle="tooltip" data-placement="top" title="" data-original-title="Special OT">
                  <i class="ace-icon fa fa-plus bigger-110"></i>
                </button>

                <button v-if="attDisable === false" class="btn btn-outline-success pull-right btn-sm" type="button" @click="attendanceUndo(date)" data-toggle="tooltip" data-placement="top" title="" data-original-title="Attendance undo">
                  <i class="ace-icon fa fa-undo bigger-110"></i>
                </button>
            </td>
        </tr>
    </tbody>
    
</template>

<script>
    import moment from 'moment';
    export default {
        name: "Job-Card-View-Single",
        data() {
          return {
            moment: moment,
            attDate: this.date,
            attStatus:'Absent',
            bgColor:'bg-danger',
            attComment:'',
            openStatus:1,
            intime:'-',
            outtime:'-',
            attId:'',
            ot:'-',
            late_status:0,
            ot_day:0,
            specialAtt:{},
            remarks:'',
            floor:'',
            line:'',
            loader:true,
            attDisable:false,
            saveMode:false, 
            shift:{},
            saveLoader:false
          }
        },
        props:['card', 'date', 'mode'],
        methods:{
            timeCardPreview(){
                let data = this.card;
                let self = this;
                if(self.mode === 'edit'){
                    self.intime = '';
                    self.outtime = '';
                }
                data.lineId = data.info.as_line_id??'';
                data.floorId = data.info.as_floor_id??'';
                
                if(Object.keys(data.leaveDate).includes(self.date)){
                    self.attStatus = data.leaveDate[self.date];
                    self.bgColor = 'bg-primary';
                    self.attDisable = true;
                }else if(Object.keys(data.holidayDate).includes(self.date)){
                    self.attStatus = (data.holidayDate[self.date] == null || data.holidayDate[self.date] == '')?'Weekend':data.holidayDate[self.date];
                    self.bgColor = 'bg-warning';
                    if(Object.keys(data.otDate).includes(self.date) && self.mode === 'edit'){
                        self.attStatus = 'OT';
                        self.attDisable = false;
                    }else{
                        self.attDisable = true;
                    }
                }else if(Object.keys(data.outsideDate).includes(self.date)){
                    self.attStatus = data.outsideDate[self.date] == 'WFHOME'?'Work From Home':data.outsideDate[self.date];
                    self.bgColor = 'bg-success';
                    self.attDisable = true;
                }else if(Object.keys(data.presentDate).includes(self.date)){
                    
                    self.attStatus = "Present";
                    self.bgColor = 'bg-default';
                    self.attDisable = false;
                    self.attId = '';
                    if(data.presentDate[self.date].in_time != null && data.presentDate[self.date].in_time != '' && data.presentDate[self.date].remarks != 'DSI'){
                        
                        self.intime = moment(data.presentDate[self.date].in_time).format('HH:mm:ss');
                        self.attId = data.presentDate[self.date].id;
                    }
                    if(data.presentDate[self.date].out_time != null && data.presentDate[self.date].out_time != ''){
                        self.attId = data.presentDate[self.date].id;
                        self.outtime = moment(data.presentDate[self.date].out_time).format('HH:mm:ss');
                    }
                    
                    if(data.info.as_ot === '1'){
                        self.ot = this.$helpers.numberToTimeClockFormat(data.presentDate[self.date].ot_hour);
                    }
                    if(data.presentDate[self.date].late_status === '1'){
                        self.late_status = 1;
                    }

                    if(data.presentDate[self.date].remarks === 'HD'){
                        self.remarks = 'Half Day';
                    }

                    if(data.presentDate[self.date].line_id != null){
                        data.lineId = data.presentDate[self.date] !== undefined? data.presentDate[self.date].line_id:'';
                        if(data.lineId !== ''){
                            data.floorId = data.line[data.lineId] !== undefined?data.line[data.lineId].hr_line_floor_id:'';
                        }else{
                            data.floorId = '';
                        }
                    }
                }else{
                    self.openStatus = 0;
                    if(Object.keys(data.absentDate).includes(self.date) && self.mode === 'edit'){
                        if(data.absentDate[self.date] != null){
                            self.attComment = data.absentDate[self.date];
                            self.attStatus = self.attStatus+' ('+self.attComment+')';
                        }
                    }
                }

                if(Object.keys(data.otPresentDate).includes(self.date)){
                    self.ot_day = 1;
                }
                if(Object.keys(data.getShift).includes(self.date)){
                    self.shift.start_end = moment(self.date+' '+data.getShift[self.date].time.hr_shift_start_time).format('HH:mm')+' - '+moment(self.date+' '+data.getShift[self.date].time.hr_shift_out_time).format('HH:mm');
                    self.shift.code = data.getShift[self.date].time.hr_shift_code;
                    self.shift.name = data.getShift[self.date].time.hr_shift_name;
                    self.shift.ot_shift = data.getShift[self.date].time.ot_shift;
                }
                if(data.floor[data.floorId]){
                    self.floor = data.floor[data.floorId].hr_floor_name;
                }
                if(data.line[data.lineId]){
                    self.line = data.line[data.lineId].hr_line_name;
                }

                // special attendance
                if(Object.keys(data.specialAttDate).includes(self.date)){
                    let spInTime = '';
                    if(data.specialAttDate[self.date].in_time != null){
                        spInTime = moment(data.specialAttDate[self.date].in_time).format('HH:mm:ss');
                    }
                    let spOutTime = '';
                    if(data.specialAttDate[self.date].out_time != null){
                        spOutTime = moment(data.specialAttDate[self.date].out_time).format('HH:mm:ss');
                    }
                    self.specialAtt = {
                        id: data.specialAttDate[self.date].id,
                        as_id: data.specialAttDate[self.date].as_id,
                        in_date: self.date,
                        in_time: spInTime,
                        out_time: spOutTime,
                        ot: self.$helpers.numberToTimeClockFormat(data.specialAttDate[self.date].ot_hour),
                        hr_shift_code:data.specialAttDate[self.date].hr_shift_code,
                        editStatus:0
                    }
                }
            },
            onchangeTime(time, type, flag){
                let formatTime = moment(time.toString(),"LT").format('HH:mm:ss');
                if(flag === 0){
                    this.saveMode = true;
                    if(formatTime === 'Invalid date'){
                        $.notify('Invalid Date Format', 'error')
                        this[type] = '';
                    }else{
                        this[type] = formatTime;
                    }
                }
                if(flag === 1){
                    this.specialAtt.editStatus = 1;
                    if(formatTime === 'Invalid date'){
                        $.notify('Invalid Date Format', 'error')
                        this.specialAtt[type] = '';
                    }else{
                        this.specialAtt[type] = formatTime;
                    }
                }
                
            },
            singleRow(date){
                this.saveLoader = true;
                if($('.single_row').length > 1){
                    this.singleSave(date, '');
                }else{
                    this.singleSave(date, 'individual');
                }
            },
            singleSave(date, type=''){
                // console.log(date);
                let self = this;
                axios.post('/hr/operation/job-card-single-update', {
                    id:self.attId,
                    date:date,
                    intime:$("#intime-"+date).val(),
                    outtime:$("#outtime-"+date).val(),
                    shift_bill:self.card.getShift[date],
                    activity: self.card,
                    manual: self.saveMode,
                    type: type
                })
                .then(response => {
                    // console.log(response.data)
                    
                    if(response.data.type === 'success'){
                        this.saveMode = false;
                        if(response.data.status !== 0){
                            if(self.card.info.as_ot === '1'){
                                self.ot = self.$helpers.numberToTimeClockFormat(response.data.ot);
                            }
                            self.attStatus='Present';
                            self.bgColor='bg-default';
                            self.late_status = response.data.late;
                            self.intime = response.data.intime;
                            self.outtime = response.data.outtime;
                            if(response.data.status === 2){
                                self.ot_day = 1;
                            }
                        }else{
                            self.attStatus='Absent';
                            self.bgColor='bg-danger';
                            self.late_status = 0;
                            self.ot = '-';
                            
                        }
                        self.attId = response.data.id;
                    }
                    if(type !== '' || response.data.type !== 'success'){
                        $.notify(response.data.message, response.data.type);
                    }
                    if(type !== '' && response.data.value){
                        self.card.info.otHour = response.data.value.otCount;
                        self.card.info.totalAbsent = response.data.value.absentCount;
                        self.card.info.totalPresent = response.data.value.presentCount;
                    }
                    this.saveLoader = false;
                    
                })
                .catch(error => {
                    console.log(error)
                    self.errors = true;
                    this.saveLoader = false;
                })
            },
            spSingleRow(attData){
                let self = this;
                axios.post('/hr/operation/special-ot-update', attData)
                .then(response => {
                    console.log(response.data)
                    if(response.data.type === 'success'){
                        this.specialAtt.editStatus = 0;
                        if(self.card.info.as_ot === '1'){
                            self.specialAtt.ot = self.$helpers.numberToTimeClockFormat(response.data.ot);
                        }
                    }
                    $.notify(response.data.message, response.data.type);
                    
                    if(response.data.value){
                        self.card.info.otHour = response.data.value.otCount;
                    }
                })
                .catch(error => {
                    console.log(error)
                    self.errors = true;
                })
            },
            attendanceUndo(date){
                let self = this;
                $('.app-loader').show();
                axios.post('/hr/operation/attendance-undo-history', {
                    date:date,
                    as_id: self.card.info.as_id
                })
                .then(response => {
                    // console.log(response.data)
                    
                    if(response.data === 'success'){
                        // this.singleSave(date, 'individual');
                        $('.re_generate').click();
                        $.notify("Successfully Undo", 'success');
                    }else{
                        $.notify("Something wrong, please try again", 'error');
                    }
                    $('.app-loader').hide();
                })
                .catch(error => {
                    console.log(error)
                })
            },
            shiftClickEvent(date){
                let shiftData = {
                    shift_roaster_associate_id: this.card.info.associate_id,
                    shift_code: this.shift.code,
                    shift: this.shift.name,
                    date: date,
                    as_unit_id: this.card.info.as_unit_id,
                    as_id: this.card.info.as_id
                };
                // console.log(shiftData)
                this.$emit('modalShift', shiftData);
                $("#shiftModal").modal('show')
            },
            extraOTClickEvent(date){
                let otData = {
                    as_id: this.card.info.as_id,
                    as_unit_id: this.card.info.as_unit_id,
                    in_date: date,
                    shift: this.shift
                };
                this.$emit('modalExtraOT', otData);
                $("#extraOTModal").modal('show')
            },
            absentClickEvent(date){
                let absentData = {
                    date: date,
                    associate_id: this.card.info.associate_id,
                    hr_unit: this.card.info.as_unit_id,
                    comment: this.attComment
                };
                this.$emit('modalAbsent', absentData);
                $("#absentModal").modal('show')
            }
        },
        mounted(){

            this.timeCardPreview();
            $('[data-toggle="tooltip"]').tooltip();
            // $('.intime,.outtime,.friday').datetimepicker({
            //   format:'HH:mm:ss',
            //   allowInputToggle: false
            // });
        }
    }
</script>