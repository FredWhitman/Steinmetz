
$(document).ready(function(){
    $.ajax
    (
        {
            url : "http://192.168.1.51/maintenance/dbProductionMonthYear.php?",
            type : "GET",
            success : function(data)
            {
                console.log(data);
                var month = [];
                var totalParts = [];
                var rejects = [];
      
                for(var i in data) 
                {
                    month.push("Month " + data[i].dMonth);
                    totalParts.push(data[i].TotalMolded);
                    rejects.push(data[i].TotalRejects);
                }

                var chartdata = 
                {
                    labels: month,
                    datasets: 
                    [{ 
                        label: "TotaledMolded",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(59, 89, 152, 0.75)",
                        borderColor: "rgba(59, 89, 152, 1)",
                        pointHoverBackgroundColor: "rgba(59, 89, 152, 1)",
                        pointHoverBorderColor: "rgba(59, 89, 152, 1)",
                        data: totalParts,
                        yAxisID: 'y'
                    },
                    {
                        label: "TotalRejects",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(29, 202, 255, 0.75)",
                        borderColor: "rgba(29, 202, 255, 1)",
                        pointHoverBackgroundColor: "rgba(29, 202, 255, 1)",
                        pointHoverBorderColor: "rgba(29, 202, 255, 1)",
                        data: rejects,
                        yAxisID: 'y1'
                    }]
                };
         
                var ctx = $("#mycanvas");

                var LineGraph = new Chart(ctx, 
                {
                    type: 'line',
                    data: chartdata,
                    scales: 
                    {
                        y: 
                        {
                            type: 'linear',
                            display: true,
                            position: 'left'
                        },
                        y1: 
                        {
                            type: 'linear',
                            display: true,
                            position: 'right'
                        }
                    }
                });
            },
                error : function(data) 
                {

                }
        });
      });
  