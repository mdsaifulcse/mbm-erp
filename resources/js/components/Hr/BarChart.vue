<template>
    <div class="container">
        <canvas ref="chart"></canvas>
    </div>
</template>
<script>
    import { Bar } from 'vue-chartjs'
    export default {
        extends: Bar,
        name: "Bar-Chart",
        data() {
          return {
            data:{},
            loader:true
          }
        },
        props:['dataParams'],
        methods:{
            preview(){
                // var chart = this.$refs.chart;
                // var ctx = chart.getContext("2d");
                // var myChart = new Chart(ctx, {
                //     type: 'bar',
                //     data: {
                //         labels: this.data.lebel,
                //         datasets: [{
                //             label: '# of Votes',
                //             data: this.data.data,
                //             borderWidth: 1
                //         }]
                //     },
                //     options: {
                //         scales: {
                //             yAxes: [{
                //                 ticks: {
                //                     beginAtZero: true
                //                 }
                //             }]
                //         }
                //     }
                // });

                var chart = this.$refs.chart;
                var ctx = chart.getContext("2d");
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        datasets: [
                            {
                              label: 'Salary',
                              backgroundColor: '#f87979',
                              data: [40, 20, 12, 39, 10, 40, 39, 80, 40, 20, 12, 11]
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
