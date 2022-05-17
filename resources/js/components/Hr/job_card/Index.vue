<template>
    <div>
        <div class="iq-accordion career-style mat-style  ">
            <div class="overlay"></div>
            <div class="iq-card iq-accordion-block mb-3 accordion-active job-card-accordion">
                <div class="active-mat clearfix">
                  <div class="container-fluid">
                     <div class="row">
                        <div class="col-sm-12"><a class="accordion-title"><span class="header-title" style="line-height:1.8;"> Employee Wise </span> </a></div>
                     </div>
                  </div>
                </div>
                <div class="accordion-details pt-4 pl-4 pr-4" style="margin-left: 20%;">
                    <form>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group has-float-label has-required select-search-group">
                                    <v-select v-model="fields.associate" label="associate_id" :filterable="false" :options="associates" @search="onSearch" id="associate" placeholder="Type to search...">
                                        <template slot="no-options">
                                          type to search ..
                                        </template>
                                        <template slot="option" slot-scope="option">
                                            <div class="d-center">
                                            {{ option.associate_name }}
                                            </div>
                                        </template>
                                        <template slot="selected-option" slot-scope="option">
                                            <div class="selected d-center">
                                            {{ option.associate_name }}
                                            </div>
                                        </template>
                                    </v-select>
                                    <label for="associate"> Associate's ID </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group has-float-label has-required select-search-group">
                                    <input type="month" class="form-control" id="month" v-model="fields.month_year" placeholder=" Month-Year" required="required" :max="attributes.max_month" />
                                    <label  for="month"> Month </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-success btn-md" @click="generate()"><i class="fa fa-search"></i> Generate</button>
                                &nbsp;
                                
                                <a @click="reloadPage" data-toggle="tooltip" data-placement="top" title="" data-original-title="Reload Job Card" class="btn btn-outline-primary btn-md"><i class="fa fa-refresh"></i></a>
                            </div>
                        </div>
                    </form>
               </div>
            </div>
        </div>

        <div class="page-content content-show">
            <router-view :key="$route.path" :params="fields" :viewtype="viewtype" @viewtype="setViewType" @monthYear="setMonthYear"></router-view>
        </div>
    </div>
</template>
<script>
    
    import Report from './Report';
    import VueRouter from 'vue-router';
    const router = new VueRouter({
        mode: 'history',
        routes: [
            {
              path: '/hr/operation/job_card?associate=:id&month_year=:month',
              name: 'jobcard',
              component: Report,
              props: true
            },

            {
                path: '/hr/operation/job_card?associate=:id&month_year=:month&view=edit',
                name: 'job-card-edit',
                component: Report,
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
        name: "Job-Card",
        data() {

            return {
                fields: {
                    month_year:'',
                    associate:'',
                },
                associates:[],
                errors: {},
                viewtype:'show'
            }
        },
        props:['attributes', 'view'],
        router,
        computed: {
            maxMonth(){
                const current = new Date();
                const month = ((current.getMonth()+1) < 10 ? '0' : '') + (current.getMonth()+1);
                return current.getFullYear()+'-'+month;
            }

        },
        methods:{
            onSearch(search, loading){
                if(search.length) {
                    loading(true);
                    axios.get('/hr/associate-search', {
                        params: {
                          keyword: search
                        }
                    })
                    .then(response => {
                        this.associates = response.data
                        // console.log(response.data)
                    })
                    .catch(error => {
                        console.log(error)
                        this.errors = true
                    })
                    .finally(() => {
                        loading(false);
                    })
                }
            },

            setAssociate(){
                axios.get('/hr/single-associate-search', {
                    params: {
                      keyword: this.attributes.associate
                    }
                })
                .then(response => {
                    this.fields.associate = response.data
                    this.generate();
                })
                .catch(error => {
                    console.log(error)
                    this.errors = true
                })
            },
            generate(){
                if(this.fields.associate !== undefined && this.fields.associate !== null && this.fields.month_year !== undefined){
                    // console.log(this.viewtype);
                    if(this.viewtype === 'show'){
                        this.$router.push({ name: 'jobcard', params: { id: this.fields.associate.associate_id, month: this.fields.month_year } });
                    }else{
                        this.$router.push({ name: 'job-card-edit', params: { id: this.fields.associate.associate_id, month: this.fields.month_year } });
                    }
                }else if(this.fields.associate == undefined){
                    $('#associate').notify('Associate id required', 'error');
                }else{
                    $('#month').notify('Month is required', 'error');
                }
            },
            setViewType(viewtype){
                this.viewtype = viewtype;
            },
            setMonthYear(monthYear){
                this.fields.month_year = monthYear;
                this.generate();
            },
            reloadPage(){
                location.reload();
            }
        },
        mounted(){
            if(this.attributes.associate !== undefined && this.attributes.associate !== ''){
                this.viewtype = this.view;
                this.setAssociate();
            }
            this.fields.month_year = this.attributes.month_year;
            this.fields.max_month = this.attributes.max_month;
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