<template>
	<div>
		<div class="row">
            <div class="col-sm-6">
                <div class="form-group has-required has-float-label">
                    <input type="text" v-model="hr_shift_break_time" class="form-control"  />
                    <label  for="hr_shift_break_time">Break Minute</label>
                </div>
                
            </div>
		</div>
	</div>
</template>


<script type="text/javascript">
//import RestaurentCardLoader from './RestaurentCardLoader.vue';

export default {
    props: {
        shift: {
            type: Object,
            required: true
        },


    },
    //components: {RestaurentCardLoader},
    data () {
        return {
        }
    },

    created () {
        /*this.setParams();
        this.fetchLibrary();
        this.fetch(this.startPoint);*/
    },

    methods: {
        
        checkBoxFilter(){
            
        }

    }
}
</script>
