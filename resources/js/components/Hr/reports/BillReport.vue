<template>
	<div>
		<div class="animationLoading" v-if="flag === false">
			<div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div>
		</div>
	</div>
</template>

<script>
    export default {
  
        data() {
          return {
            errors: {},
            url:window.location.origin
          }
        },
        props:['data', 'flag'],
        
        methods:{
            
        },
        mounted(){

        }
    }
</script>