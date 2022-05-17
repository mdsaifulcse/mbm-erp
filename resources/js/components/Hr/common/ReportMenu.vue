<template>
    <div>
        <form id="filterForm">
          <div class="row">       
              <div style="width: 10%; float: left; margin-left: 15px; margin-top: 2px;">
                <div id="result-section-btn">
                  <button type="button" class="btn btn-sm btn-primary hidden-print" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                </div>
              </div>
              <div class="text-center" style="width: 47%; float: left">
                
              </div>
              <div style="width: 40%; float: left">
                <div class="row">
                  <slot name="nav-month">
                    <div class="col-3 p-0">
                        <div class="form-group has-float-label has-required ">
                          <input type="month" class="report_date form-control month-report" id="yearMonth" name="year_month" placeholder=" Month-Year" required="required" value="" max="" autocomplete="off">
                          <label for="yearMonth">Month</label>
                        </div>
                    </div>
                  </slot>
                  <div class="col-4 pr-0">
                    <div class="format">
                      <div class="form-group has-float-label select-search-group mb-0">
                        <select id="reportGroupHead" @change="reportFormat()" v-model="fields.report_format">
                          <option value="as_unit_id">Unit</option>
                          <option value="as_location">Location</option>
                          <option value="as_department_id">Department</option>
                          <option value="as_designation_id">Designation</option>
                          <option value="as_section_id">Section</option>
                          <option value="as_subsection_id">Sub Section</option>
                          <option value="as_floor_id">Floor</option>
                          <option value="as_line_id">Line</option>
                        </select>
                        <label for="reportGroupHead">Report Format</label>
                      </div>
                    </div>
                  </div>
                  <div class="col-5 pl-0">
                    <div class="text-right">
                      <a class="btn view no-padding clear-filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear Filter">
                        <i class="las la-redo-alt" style="color: #f64b4b; border-color:#be7979"></i>
                      </a>
                      <a class="btn view no-padding filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Advanced Filter" @click="advFilter()">
                        <i class="fa fa-filter"></i>
                      </a>
                      
                      
                      <router-link :to="{ name: 'filter', params: { filter: dynamicPath } }" @click.native="reportView(1)">
                        <a class="btn view grid_view no-padding" :class="fields.report_view === 1 ? 'active' : ''" data-toggle="tooltip" data-placement="top" title="" data-original-title="Summary Report View" :id="fields.report_view">
                        <i class="las la-th-large"></i>
                      </a>
                      </router-link>

                      <router-link :to="{ name: 'filter', params: { filter: dynamicPath } }" @click.native="reportView(0)">
                        <a class="btn view list_view no-padding" :class="fields.report_view === 0 ? 'active' : ''" data-toggle="tooltip" data-placement="top" title="" data-original-title="Details Report View" :id="fields.report_view">
                        <i class="las la-list-ul"></i>
                      </a>
                      </router-link>
                    </div>
                  </div>
                </div>
              </div>
          </div>
          <div class="modal right fade" id="right_modal_navbar" tabindex="-1" role="dialog" aria-labelledby="right_modal_navbar">
              <div class="modal-dialog modal-lg navbar-modal" role="document" >
                  <div class="modal-content">
                    <div class="modal-header">
                      <a class="view prev_btn" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back">
                        <i class="las la-chevron-left"></i>
                      </a>
                      <h5 class="modal-title right-modal-title text-center" id="navbar-title-right"> &nbsp; </h5>
                    
                  </div>
                  <div class="modal-body">
                    <div class="modal-content-result" id="content-result">
                      <!-- <form id="filterForm"> -->
                        <div class="filter-section">
                          <slot name="right-nav-top"></slot>
                          <div class="form-group mb-2">
                            <label for="" class="m-0 fwb"><h5>Unit <input type='checkbox' id="unit" class="unit-group group-checkbox bg-primary" checked @click='checkAll("unit")' /></h5></label>
                            <hr class="mt-2">
                            <div class="row">
                              <div class="col pr-0" v-for="unitlist in this.data.unitList">
                                  <div class="custom-control custom-checkbox custom-checkbox-color-check " v-for="(unit, index) in unitlist">
                                    <input type="checkbox" v-model="fields.units" class="custom-control-input bg-primary unit" :value="unit.hr_unit_id" :id="'unit-'+index" checked>
                                    <label class="custom-control-label" :for="'unit-'+index"> {{ unit.hr_unit_short_name }}</label>
                                  </div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group mb-2">
                            <label for="" class="m-0 fwb"><h5>Location <input type='checkbox' id="location" class="unit-group group-checkbox bg-primary" checked @click='checkAll("location")' /></h5></label>
                            <hr class="mt-2">
                            <div class="row">
                              <div class="col pr-0" v-for="locationList in this.data.locationList">
                                  <div class="custom-control custom-checkbox custom-checkbox-color-check " v-for="(location, index) in locationList">
                                    <input type="checkbox" v-model="fields.locations" class="custom-control-input bg-primary location" :value="location.hr_location_id" :id="'location-'+index" checked>
                                    <label class="custom-control-label" :for="'location-'+index"> {{ location.hr_location_short_name }}</label>
                                  </div>
                              </div>
                            </div>
                          </div>
                          <hr class="mt-2">
                          <div class="form-group has-float-label select-search-group">
                            <select v-model="fields.area" class="form-control capitalize select-search" id="area">
                                <option selected="" value="">Choose Area...</option>
                                <option :value="key" v-for="(area, index, key) in this.data.areaList">{{ area.hr_area_name }}</option>
                            </select>
                            <label for="area">Area</label>
                          </div>
                          <hr class="mt-2">
                          <div class="form-group has-float-label select-search-group">
                            <select v-model="fields.department" @change="reportFormat()" class="form-control capitalize select-search" id="department">
                                <option value="">Choose Department...</option>
                                <option :value="department.hr_department_id" v-for="department in this.data.departmentList">{{ department.hr_department_name }}</option>
                            </select>
                            <label for="department">Department</label>
                          </div>
                          <hr class="mt-2">
                          <div class="form-group has-float-label select-search-group">
                            <select v-model="fields.section" class="form-control capitalize select-search " id="section">
                                <option selected="" value="">Choose Section...</option>
                                <option :value="key" v-for="(section, index, key) in this.data.sectionList">{{ section.hr_section_name }}</option>
                            </select>
                            <label for="section">Section</label>
                          </div>
                          <hr class="mt-2">
                          <div class="form-group has-float-label select-search-group">
                            <select v-model="fields.subsection" class="form-control capitalize select-search" id="subSection">
                                <option selected="" value="">Choose Sub Section...</option>
                                <option :value="key" v-for="(subSection, index, key) in this.data.subSectionList">{{ subSection.hr_subsection_name }}</option>
                            </select>
                            <label for="subSection">Sub Section</label>
                          </div>
                          <hr class="mt-2">
                          <div class="form-group has-float-label select-search-group">
                            <select v-model="fields.floor" class="form-control capitalize select-search" id="floor_id" >
                                <option selected="" value="">Choose Floor...</option>
                                <option :value="key" v-for="(floor, index, key) in this.data.floorList">{{ floor.hr_floor_name }}</option>
                            </select>
                            <label for="floor_id">Floor</label>
                          </div>
                          <hr class="mt-2">
                          <div class="form-group has-float-label select-search-group">
                            <select v-model="fields.line" class="form-control capitalize select-search" id="line_id" >
                                <option selected="" value="">Choose Line...</option>
                                <option :value="key" v-for="(line, index, key) in this.data.lineList">{{ line.hr_line_name }}</option>
                            </select>
                            <label for="line_id">Line</label>
                          </div>
                          <hr class="mt-2">
                          <div class="form-group has-float-label select-search-group">
                            <select v-model="fields.designation" class="form-control capitalize select-search" id="designation" >
                                <option selected="" value="">Choose Designation...</option>
                                <option :value="key" v-for="(designation, index, key) in this.data.designationList">{{ designation.hr_designation_name }}</option>
                            </select>
                            <label for="designation">Designation</label>
                          </div>
                          <hr class="mt-2">
                          <div class="form-group has-float-label select-search-group">
                            <select v-model="fields.otnonot" class="form-control capitalize select-search" id="otnonot" >
                                <option selected="" value="">Choose...</option>
                                <option value="0">Non-OT</option>
                                <option value="1">OT</option>
                            </select>
                            <label for="otnonot">OT/Non-OT</label>
                          </div>
                          <hr class="mt-2">
                          
                          <div class="form-group mb-2">
                            <label for="" class="m-0 fwb"><h5>Employee Status </h5></label>
                            <hr class="mt-2">
                            <div class="row">
                              <div class="col pr-0">
                                <div class="custom-control custom-checkbox custom-checkbox-color-check ">
                                  <input type="checkbox" v-model="fields.emp_status" class="custom-control-input bg-primary sta" value="1" id="sta-1" checked >
                                  <label class="custom-control-label" for="sta-1">Active</label>
                                </div>
                                
                                <div class="custom-control custom-checkbox custom-checkbox-color-check ">
                                  <input type="checkbox" v-model="fields.emp_status" class="custom-control-input bg-primary sta" value="6" id="sta-6" >
                                  <label class="custom-control-label" for="sta-6">Maternity</label>
                                </div>
                              </div>
                              <div class="col pr-0">
                                <div class="custom-control custom-checkbox custom-checkbox-color-check ">
                                  <input type="checkbox" v-model="fields.emp_status" class="custom-control-input bg-primary sta" value="5" id="sta-5" >
                                  <label class="custom-control-label" for="sta-5">Left</label>
                                </div>
                                <div class="custom-control custom-checkbox custom-checkbox-color-check ">
                                  <input type="checkbox" v-model="fields.emp_status" class="custom-control-input bg-primary sta" value="2" id="sta-2" >
                                  <label class="custom-control-label" for="sta-2">Resign</label>
                                </div>
                                <div class="custom-control custom-checkbox custom-checkbox-color-check ">
                                  <input type="checkbox" v-model="fields.emp_status" class="custom-control-input bg-primary sta" value="3" id="sta-3" >
                                  <label class="custom-control-label" for="sta-3">Terminate</label>
                                </div>
                              </div>
                              
                            </div>
                          </div>
                          <slot name="right-nav-footer"></slot>
                          <hr class="mt-2">
                          <div class="form-inline mb-3 mt-10">
                                                    
                            <div class="custom-control custom-radio custom-control-inline">
                               <input type="radio" id="form-range" name="date_type" class="date_type custom-control-input" value="range" checked="" @click="durationType('range')">
                               <label class="custom-control-label cursor-pointer" for="form-range"> Range </label>
                            </div>
                            
                            <div class="custom-control custom-radio custom-control-inline">
                               <input type="radio" id="form-month" name="date_type" class="date_type custom-control-input" v-model="fields.month_year" value="month" @click="durationType('month')">
                               <label class="custom-control-label cursor-pointer" for="form-month"> Month </label>
                            </div>
                          </div>
                          <div id="month-form" style="display: none;">
                              <div class="form-group has-float-label has-required">
                                <input type="month" class="report_date form-control" id="month-year" v-model="fields.month_year" placeholder=" Month-Year" value=""autocomplete="off" />
                                <label for="month-year">Month</label>
                              </div>
                          </div>
                          <div class="row" id="range-form">
                            <div class="col">
                                <div class="form-group has-float-label has-required">
                                    <input type="date" class="report_date datepicker form-control" id="from_date" placeholder="Y-m-d" required="required" v-model="fields.from_date" value="" autocomplete="off" />
                                    <label for="from_date">From Date</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group has-float-label has-required">
                                    <input type="date" class="report_date datepicker form-control" id="to_date" placeholder="Y-m-d" required="required" v-model="fields.to_date" value="" autocomplete="off" />
                                    <label for="to_date">To Date</label>
                                </div>
                            </div>
                          </div>
                          <hr class="mt-2">
                          <div class="form-group">
                            <router-link :to="{ name: 'filter', params: { filter: dynamicPath } }" @click.native="filterSearch()">
                              <button class="btn btn-primary nextBtn btn-lg pull-right filterBtnSubmit" type="button" ><i class="fa fa-filter"></i> Filter</button> 
                            </router-link>
                            <!-- <button class="btn btn-primary nextBtn btn-lg pull-right filterBtnSubmit" type="button" ><i class="fa fa-filter"></i> Filter</button> -->
                          </div>

                        </div>
                      <!-- </form> -->
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </form>
    </div>
</template>
<script>
    var httpBuildQuery = require('http-build-query');
    export default {
        data() {
          return {
            fields: {
              report_format:'as_unit_id',
              month_year:'',
              from_date:'',
              to_date:'',
              associate:'',
              units: {},
              locations: {},
              department:'',
              area:'',
              section:'',
              subsection:'',
              line:'',
              floor:'',
              designation:'',
              otnonot:'',
              emp_status:[1],
              report_view:1,
              durationType:1
            },
            errors: {},
          }
        },
        props:['data', 'flag', 'geturl'],
        computed: {
          dynamicPath(){
            let extraUrl = httpBuildQuery(this.fields, 'flags_');
            return extraUrl;
          }

        },
        methods:{
          advFilter(){
            $('#right_modal_navbar').modal('show');
            $('#navbar-title-right').html('Advanced Filter');
          },
          filterSearch(){
            let resultData = [];
            $('#right_modal_navbar').modal('hide');
            let data = this.fields;
            axios.get(this.geturl, { params: data })
              .then(response => {
                console.log(response.data)
              })
              .catch(error => {
                this.errors = error
              })
              .finally(() => {
                this.$emit('clicked', resultData);
              })
            
          },
          checkAll(para){
              if($('#'+para).is(':checked')){
                  $('.'+para).each(function() {
                      $(this).prop("checked", true);
                  });
              }else{
                  $('.'+para).each(function() {
                      $(this).prop("checked", false);
                  });
              }
          },
          reportView(value){
            this.fields.report_view = value;
            this.filterSearch();
          },
          reportFormat(){
            // this.fields.report_format = value;
            console.log('hi')
          },
          durationType(value){
            if(value == 'month'){
              $("#month-form").show();
              $("#range-form").hide();
              this.fields.durationType = 1;
            }else if(value == 'range'){
              $("#month-form").hide();
              $("#range-form").show();
              this.fields.durationType = 0;
            }
          }
        },
        mounted(){
          this.fields.units = this.data.units;
          this.fields.locations = this.data.locations;
          
          
        }
    }
</script>
<style>
  .single-employee-search {
    margin-top: 82px !important;
  }
  .view:hover, .view:hover{
    color: #ccc !important;
    
  }
  .grid_view{

  }
  .view i{
    font-size: 25px;
    border: 1px solid #000;
    border-radius: 3px;
    padding: 0px 3px;
  }
  .view.active i{
    background: linear-gradient(to right,#0db5c8 0,#089bab 100%);
    color: #fff;
    border-color: #089bab;
  }
  .iq-card .iq-card-header {
    margin-bottom: 10px;
    padding: 15px 15px;
    padding-bottom: 0px;
  }
  .modal-h3{
    line-height: 15px !important;
  }
  .select2-container .select2-selection--single, .month-report { height: 30px !important;}
  .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 30px !important;}
  .navbar-modal {
    width: 275px !important;
    box-shadow: -2px 0px 6px 1px;
  }
</style>