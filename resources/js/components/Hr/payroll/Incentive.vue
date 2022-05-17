<template>
  <div>
    <div class="iq-accordion career-style mat-style  ">
      <div class="iq-card iq-accordion-block">
         <div class="active-mat clearfix">
            <div class="container-fluid">
               <div class="row">
                  <div class="col-sm-12"><a class="accordion-title"><span class="header-title"> Upload File </span> </a></div>
               </div>
            </div>
         </div>
         <div class="accordion-details">
            <div class="row1">
                <div class="col-12">
                    <form class="" role="form" id="employeeWiseSalary">
                        <div class="panel">
                              <div class="panel-body">
                                  <div class="row">
                                      <div class="col-sm-4">
                                          <div class="form-group select-search-group has-float-label has-required select-search-group">
                                              <input type="file" class="form-control" id="as_id" style="line-height:16px;">
                                              <label for="as_id">Choose XLSX File</label>
                                          </div>
                                          
                                      </div>
                                      
                                      <div class="col-sm-2">
                                          <div class="form-group has-float-label has-required">
                                            <input type="date" class="form-control" v-model="file_disburse" id="disburse_date" required>
                                            <label for="disburse_date">Disburse Date</label>
                                          </div>
                                      </div>
                                      <div class="col-sm-2">
                                          <button  @click="fileUpload()" type="button" class="btn btn-outline-primary btn-sm"><i class="fa fa-save"></i> Upload</button>
                                          
                                      </div>
                                  </div>
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
                  <div class="col-sm-12"><a class="accordion-title"><span class="header-title"> Entry Base </span> </a></div>
               </div>
            </div>
         </div>
         <div class="accordion-details">
            <div class="panel">
              <div class="panel-body">  
                <div class='table-wrapper-scroll-y table-custom-scrollbar'>
                    <table class="table table-bordered table-hover table-fixed table-head" id="itemList">
                        <thead>
                            <tr class="text-center active">
                                <th width="2%">
                                    <button class="btn btn-sm btn-outline-success" type="button" @click="addNewRow"><i class="fa fa-plus"></i></button>
                                </th>
                                <th width="2%">SL.</th>
                                <th width="15%">ID</th>
                                <th width="15%">Name</th>
                                <th width="15%">Designation</th>
                                <th width="15%">Department</th>
                                <th width="8%">Floor</th>
                                <th width="8%">Line</th>
                                <th width="8%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                          
                          <tr v-for="(employee, k) in employees" :key="k">
                            <td scope="row" class="trashIconContainer">
                              <button class="btn btn-outline-danger delete btn-sm" type="button" @click="deleteRow(k, employee)">
                                  <i class="fa fa-trash"></i>
                              </button>
                            </td>
                            <td>{{ ++k }}</td>
                            <td>
                              <input type="text" v-model="employee.associate_id" class="form-control autocomplete_txt" autocomplete="off" autofocus="autofocus" v-on:keyup="searchEmployee(employee)" onclick="this.select()">
                              <input type="hidden" v-model="employee.as_id">
                            </td>
                            <td>
                              <input type="text" class="form-control" readonly>
                            </td>
                            <td>
                              <input type="text" value="" class="form-control" readonly>
                            </td>
                            <td>
                              <input type="text" value="" class="form-control" readonly>
                            </td>
                            <td>
                              <input type="text" value="" class="form-control " readonly>
                            </td>
                            <td>
                              <input type="text" value="" class="form-control " readonly>
                            </td>
                            
                            <td>
                              <input type="number" step="any" v-model="employee.amount" class="form-control" autocomplete="off" value="0" onClick="this.select()" @change="calculateAmount(employee)">
                            </td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr>
                            <td colspan="8" class="text-right">Total</td> <td class="text-right">{{ total_amount }}</td>
                          </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="bottom-section pb-3">
                  <button class="btn btn-md btn-outline-success pull-right"> <i class="fa fa-save"></i> Save </button>
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
    // name: "Incentive",
    data() {
      return {
        errors: {},
        url:window.location.origin, 
        total_amount: 0,
        disburse:'',
        line:'',
        employees: [{
          associate_id: '',
          as_id: '',
          amount: 0
        }],
        file:{},
        file_disburse:''
      }
    },
    methods:{
      saveIncentive() {
        console.log(JSON.stringify(this.employees));
      },
      fileUpload() {

      },
      searchEmployee(employee){
        if(employee.associate_id !== '' && employee.associate_id !== null){
          
        }
      },
      calculateTotal() {
        var total, subtotal;
        subtotal = this.employees.reduce(function (sum, employee) {
          var lineTotal = parseFloat(employee.amount);
          if (!isNaN(lineTotal)) {
              return sum + lineTotal;
          }
        }, 0);

        total = parseFloat(subtotal);
        if (!isNaN(total)) {
            this.total_amount = total.toFixed(2);
        } else {
            this.total_amount = '0.00'
        }
      },
      calculateAmount(employee) {
        var total = parseFloat(employee.product_price) * parseFloat(employee.product_qty);
        if (!isNaN(total)) {
            employee.amount = total.toFixed(2);
        }
        this.calculateTotal();
      },
      deleteRow(index, employee) {
        var idx = this.employees.indexOf(employee);
        if (idx > -1) {
            this.employees.splice(idx, 1);
        }
        this.calculateTotal();
      },
      addNewRow() {
        let flag = 0;
        if(this.employees.length === 0){
          flag = 1;
        }else{
          var lastId = this.employees[this.employees.length -1].as_id;
          if(lastId !== ''){
            flag = 1;
          }
        }
        
        if(flag === 1){
          this.employees.push({
            associate_id: '',
            as_id: '',
            amount: 0
          });
        }
        
      }
    },
    
  }
</script>
<style>
  p span, p span font{
      font-size: 10px;
  }
  .iq-accordion-block {
    margin-bottom: 15px !important;
    padding: 10px 0;
  }
  
</style>