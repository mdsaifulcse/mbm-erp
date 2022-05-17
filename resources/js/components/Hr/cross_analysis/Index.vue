<template>
    <div>
        <div class="iq-accordion career-style mat-style  ">
            <div class="iq-card iq-accordion-block mb-3 accordion-active">
                <div class="active-mat clearfix">
                  <div class="container-fluid">
                     <div class="row">
                        <div class="col-sm-12"><a class="accordion-title"><span class="header-title" style="line-height:1.8;"> Cross Analysis </span> </a></div>
                     </div>
                  </div>
                </div>
                <div class="accordion-details">
                    <div class="row">
                        <div class="col">
                          <form role="form" class="dataReport" id="dataReportEmp">
                            <div class="panel mb-0">
                                <div class="panel-body " >
                                    <div class="row justify-content-sm-center">
                                      <div class="col-sm-4">
                                        <div class="pr-0">
                                            <div class="form-group has-float-label has-required select-search-group">
                                                <v-select v-model="fields.type" :options="typeOption" @input="typeChange" :reduce="label => label.key" />
                                                <label  for="type"> Type </label>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="row" v-if="loadContent">
                                        <div class="col-4 pr-0">
                                            <div class="form-group has-float-label has-required select-search-group" id="load-type-wise-data">
                                                <v-select v-model="fields.category_data" :options="categoryOption" label="text" :reduce="text => text.id" @input="nameChange" />
                                                <label  for="data"> Name </label>
                                            </div>
                                        </div>
                                        <div class="col-2 pr-0">
                                            <div class="form-group has-float-label has-required select-search-group">
                                                <select v-model="fields.otnonot" id="ot-nonot" class="form-control" @input="nameChange">
                                                    <option value=""> Both </option>
                                                    <option value="1"> OT </option>
                                                    <option value="0"> Non-OT </option>
                                                </select>
                                                <label  for="ot-nonot"> OT/Non-OT </label>
                                            </div>
                                        </div>
                                        <div class="col-2 pr-0">
                                            <div class="form-group has-float-label has-required select-search-group">
                                                <input type="month" class="form-control" id="month-from" v-model="fields.month_from" placeholder=" Month-Year" required="required" :max="maxMonth" @input="nameChange" />
                                                <label  for="month-from"> Month From </label>
                                            </div>
                                        </div>
                                        <div class="col-2 pr-0">
                                            <div class="form-group has-float-label has-required select-search-group">
                                                <input type="month" class="form-control" id="month-to" v-model="fields.month_to" placeholder=" Month-Year" required="required" :max="maxMonth" @input="nameChange" />
                                                <label  for="month-to"> Month To </label>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <router-link :to="{ name: 'report', query:{fields}  }">
                                                <button type="button" class="btn btn-primary btn-sm" @click="generate()"><i class="fa fa-save"></i> Generate</button>
                                            </router-link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                      </div>
                  </div>
               </div>
            </div>
        </div>

        <div class="page-content content-show" v-if="loadReport">
            <router-view :params="fields"></router-view>
        </div>
    </div>
</template>
<script>
    import moment from 'moment'
    import VueRouter from 'vue-router';
    import Report from './Report';
    const router = new VueRouter({
        mode: 'history',
        routes: [
          {
            path: '/hr/reports/employee-cross-analysis',
            name: 'report',
            component: Report,
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
        name: "Cross-Analysis",
        data() {
            return {
                errors:[],
                fields: {
                    otnonot: '',
                    category_data:''
                },
                typeOption:[
                    {
                        key: 'as_unit_id',
                        label: 'Unit'
                    },{
                        key: 'as_location',
                        label: 'Location'
                    },{
                        key: 'as_department_id',
                        label: 'Department'
                    },{
                        key: 'as_designation_id',
                        label: 'Designation'
                    },{
                        key: 'as_section_id',
                        label: 'Section'
                    },{
                        key: 'as_subsection_id',
                        label: 'Sub Section'
                    }
                ],
                categoryOption:[],
                loadContent: false,
                loadReport: false
            }
        },
        props:['month'],
        router,
        components:{
          Report
        },
        computed: {
            maxMonth(){
                const current = new Date();
                const month = ((current.getMonth()+1) < 10 ? '0' : '') + (current.getMonth()+1);
                return current.getFullYear()+'-'+month;
            }
        },
        methods:{
            typeChange(){
                this.loadContent = false;
                this.loadReport = false;
                this.fields.category_data = [];
                axios.get('/hr/type-wise-data-view', {
                    params: {
                      type: this.fields.type
                    }
                })
                .then(response => {
                    // console.log(this.fields);

                    this.categoryOption=response.data;
                    this.loadContent = true;
                })
                .catch(error => {
                    console.log(error)
                    this.errors = true
                })
            },
            nameChange(){
                this.loadReport = false;
            },
            generate(){
                this.loadReport = false;
                
                if(this.fields.month_to !== undefined && this.fields.month_from !== undefined){
                   this.loadReport = true;
                    this.$router.replace({ name: 'report', query: this.fields }); 
                }
            },
        },
        mounted(){
            this.fields.month_from = this.month;
            this.fields.month_to = this.month;
        }
    }
</script>
<style>
    .vs__dropdown-toggle {
        padding: 3px 0 6px;
        border-radius: 6px;
    }
    .form-control {border-radius: 6px;  }
    .career-style{
        position: relative;
    }
    .overlay{
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: .5;
        background: #ffffffe8;
        border-radius: 8px;
    }
    .select-search-group.has-float-label label, .select-search-group.has-float-label label, .has-float-label label::after, .has-float-label>span::after {
        background: #F6FBFB;
    }
    .job-card-accordion {
        backdrop-filter: blur(20.5px);
        -webkit-backdrop-filter: blur(20.5px);
        border-radius: 8px;
        transition: .2s background ease;
        animation: .4s _slideUp_1c07o_1;
        animation-timing-function: cubic-bezier(.3,.5,0,1);
        /*background: 0 0;*/
        display: flex;
        /*box-shadow: 0 2.8px 2.2px rgb(0 0 0 / 2%), 0 6.7px 5.3px rgb(0 0 0 / 2%), 0 12.5px 10px rgb(0 0 0 / 2%), 0 22.3px 17.9px rgb(0 0 0 / 2%), 0 41.8px 33.4px rgb(0 0 0 / 2%), 0 100px 80px rgb(0 0 0 / 7%);*/
        flex-direction: column;
        
    }
    
</style>