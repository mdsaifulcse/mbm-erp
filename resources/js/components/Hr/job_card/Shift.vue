<template>
  <div>
    <template>
      <popper
        trigger="clickToOpen"
        :options="{
          placement: 'left',
          modifiers: { offset: { offset: '0,10px' } }
        }">
        <div class="popper">
          <div class="card">
            <div class="card-header">
              {{ shiftdate }}
            </div>
            <div class="card-body">
              <div class="form-group has-float-label has-required select-search-group">
                <select class="form-control capitalize select-search" :id="'shift-'+shiftdate"> 
                  <option value="" v-for="(shift, key) in shiftcard.getShiftData">{{ shift.hr_shift_start_time }}</option>
                </select>
                <label :for="'shift-'+shiftdate">Shift</label>
              </div>
              <br>
              <a href="#" class="btn btn-outline-primary"> <i class="fa fa-save"></i> Change</a>
            </div>
          </div>
        </div>
        <a slot="reference" style="height: auto;">{{ shifttime }}</a>
      </popper>
    </template>
  </div>
</template>
<script>
    
    export default {
        name: "job-card-shift",
        data() {
          return {
            
          }
        },
        props:['shifttime', 'shiftdate', 'shiftid', 'shiftcard'],
        
        methods:{
            
        },
        mounted(){
            
        }
    }
</script>

