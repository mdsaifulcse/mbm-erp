<template>
    <div class="container">
        <canvas ref="chart"></canvas>
    </div>
</template>
<script>
    import moment from 'moment';
    import { Bar } from 'vue-chartjs'
    export default {
        extends: Bar,
        name: "Bar-Chart",
        data() {
          return {
            data:{},
            loader:true,
            dataMonths:[],
            dataLabel:[],
          }
        },
        props:['dataParams'],
        methods:{
            preview(){
                let self = this;
                let dataMonths = self.dataParams.map(item => moment(item.yearmonth).format('YYYY, MMMM'));
                let dataLabel = self.dataParams.map(item => item.totalSalary);
                
                var chart = self.$refs.chart;
                var ctx = chart.getContext("2d");
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: dataMonths,
                        datasets: [
                            {
                              label: 'Salary',
                              backgroundColor: '#f87979',
                              data: dataLabel
                            }
                        ]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
            }
        },
        mounted(){
            this.preview();
            
        }
    }
</script>
