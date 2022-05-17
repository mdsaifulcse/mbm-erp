<template>
  <div>
    <div class="iq-accordion career-style mat-style  ">
      <div class="iq-card iq-accordion-block">
         <div class="active-mat clearfix">
            <div class="container-fluid">
               <div class="row">
                  <div class="col-sm-12"><a class="accordion-title"><span class="header-title"> Employee Wise </span> </a></div>
               </div>
            </div>
         </div>
         <div class="accordion-details">
            <div class="row1">
                <div class="col-12">
                    <form class="" role="form" id="employeeWiseSalary">
                        <div class="panel">
                          <div class="panel-body">
                              
                          </div>
                        </div>
                    </form>
                </div>
            </div>
         </div>
      </div>
      <div class="iq-card iq-accordion-block accordion-active">
         <div class="active-mat clearfix">
            <div class="container-fluid">
               <div class="row">
                  <div class="col-sm-12"><a class="accordion-title"><span class="header-title"> Unit Wise  </span> </a></div>
               </div>
            </div>
         </div>
         <div class="accordion-details">
            <div class="panel">
              <div class="panel-body">  
              	<div class="row">
	                <div class="col-3">
	                	<div class="form-group has-float-label select-search-group">
							<v-select
							  	v-model="fields.unit"
		                        placeholder="Type or choose topics"
		                        :options="unitOptions"
		                        label="label">
		                        <template slot="option" slot-scope="option">
		                            {{ option.label }}
		                        </template>
		                    </v-select>
	                      <label for="unit">Unit</label>
	                    </div>
	                    <div class="form-group">
	                    	<button @click="generate" class="btn btn-primary nextBtn btn-lg pull-right" type="button" id="unitFromBtn"><i class="fa fa-save"></i> Generate</button>
	                    </div>
	                </div>
	            </div>
                
              </div>
            </div>
         </div>

         <div class="result-content">
         	<div v-for="(unitList, u) of result.salaryList">
         		
         			<div v-for="(locList, l) in unitList">
	         			
         				<div v-for="(page, p) in locList">
	         				
	         					<div v-for="(emp, e) in page">
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: {{ emp }}</p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         					<p>মূল বেতন+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য: </p>
		         				</div>
	         				</p>
	         			</div>
	         			
	         		</div>
         		
         	</div>
         </div>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
  	props:[
  		'library',
        'unit'
  	],
    data() {
      return {
        errors: {},
        url:window.location.origin,
        fields: [],
        options :{},
        unitList:[],
        loading:'',
        result:{}
      }
    },
    computed:{
    	unitOptions(){
    		return Object.values(this.unit)
    	}
    },
    
    methods:{
      	generate() {
	      	axios
		      .get('/hr/operation/unit-wise-salary-sheet')
		      .then(response => {
		        this.result = response.data
		        // console.log(response.data)
		      })
		      .catch(error => {
		        console.log(error)
		        this.errors = true
		      })
		      .finally(() => {
		      	console.log('dome')
		      })
	    },
    },
    mounted(){
    	

    }
    
  }
</script>