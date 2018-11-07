<?php

?>

<script type="text/javascript">
  $(function() {

    
    
    var start = moment().subtract(59, 'days');
    var end = moment();

    function cb(begin, finish) {
	  chartUpdate(begin.format('MMM DD'), finish.format('MMM DD'));
      $('#reportrange span').html(begin.format('MMMM D, YYYY') + ' - ' + finish.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
      maxDate: moment(),
      minDate: moment().subtract(59, 'days'),
      startDate: start,
      endDate: end,
      ranges: {
        'Today': [moment(), moment()],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'Last 60 days': [moment().subtract(59, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      }
    }, cb);
	
	$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  });

  var ctx = document.getElementById("lineChart");
  var adSpendArr = <?= json_encode($adSpendArr); ?>;
  var ppcSalesArr = <?= json_encode($ppcSalesArr); ?>;
  var ppcAcosArr = <?= json_encode($acos); ?>;
  var dateArr = <?= json_encode($dateArr); ?>;

  var lineData = {
    labels: dateArr,
    datasets: [{
      label: "Ad Spend ($)",
      yAxisID: 'A',
      data: adSpendArr,
      fill: false,
      pointRadius: 0,
      hoverRadius: 4,
      pointBorderColor: '#ffffff',
      pointBorderWidth: 2,
      pointBackgroundColor: "rgb(244, 72, 66)",
      hoverBorderWidth: 3,
      // backgroundColor: "rgba(244, 72, 66, 0.1)",
      borderWidth: 2.5,
      borderColor: "rgba(244, 72, 66, 0.7)",
      //type: 'line'
    }, {
      label: "PPC Sales ($)",
      yAxisID: 'A',
      data: ppcSalesArr,
      fill: false,
      pointRadius: 0,
      hoverRadius: 4,
      pointBorderColor: '#ffffff',
      pointBorderWidth: 2,
      pointBackgroundColor: "rgb(89, 255, 152)",
      hoverBorderWidth: 3,
      // backgroundColor: "rgba(89, 255, 152, 0.1)",
      borderWidth: 2.5,
      borderColor: "rgba(89, 255, 152, 0.7)",
      //type: 'line'
    }, {
      label: "PPC ACoS (%)",
      yAxisID: 'B',
      data: ppcAcosArr,
      fill: true,
      backgroundColor: "rgba(114, 187, 255, 0.4)",
    }]
  };

  Chart.defaults.LineWithLine = Chart.defaults.line;
  Chart.controllers.LineWithLine = Chart.controllers.line.extend({
    draw: function(ease) {
      Chart.controllers.line.prototype.draw.call(this, ease);

      if (this.chart.tooltip._active && this.chart.tooltip._active.length) {
        var activePoint = this.chart.tooltip._active[0],
            ctx = this.chart.ctx,
            x = activePoint.tooltipPosition().x,
            topY = this.chart.scales['y-axis-0'].top,
            bottomY = this.chart.scales['y-axis-0'].bottom;

        // draw line
        ctx.save();
        ctx.beginPath();
        ctx.moveTo(x, topY);
        ctx.lineTo(x, bottomY);
        ctx.lineWidth = 2;
        ctx.strokeStyle = '#07C';
        ctx.stroke();
        ctx.restore();
      }
    }
  });

  /*var chart = new Chart(ctx, {
    type: 'LineWithLine',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
      datasets: [{
        label: 'Statistics',
        data: [3, 1, 2, 5, 4, 7, 6],
        backgroundColor: 'rgba(0, 119, 204, 0.8)',
        borderColor: 'rgba(0, 119, 204, 0.3)',
        fill: false
      }, {
        label: 'asdfasdf',
        data: [1,2,3,4,5,6,7],
        backgroundColor: 'rgba(0, 119, 204, 0.8)',
        borderColor: 'rgba(0, 119, 204, 0.3)',
        fill: false
      }]
    },
    options: {
      responsive: true,
      tooltips: {
        mode: 'x',
        intersect: false
      },
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });*/

  var myChart = new Chart(ctx, {
    type: "line",
    data: lineData,

    options: {
      /*elements: {
        line: {
          fill: '-1'
        }
      },
      hover: {
        mode: 'x',
        intersect: false
      },*/
      tooltips: {
        //mode: 'x',
        intersect: false
      },
      responsive: true,
      maintainAspectRatio: false,
      
      scales: {
        // Remove grid lines
        xAxes: [{
          gridLines: {
            color: "rgba(0, 0, 0, 0)"
          }/*,
  
          scaleLabel: {
            show: true,
            labelString: 'Value'
          }*/
        }],
        
        yAxes: [{
          id: 'A',
          type: 'linear',
          position: 'left',
          gridLines: {
            color: "#dbdbdb"
          }
        },
        
        {
          id: 'B',
          type: 'linear',
          position: 'right',
          gridLines: {
            color: "rgba(0, 0, 0, 0)"
          },
          ticks: {
            beginAtZero: true,
            display: false,
            max: Math.round(Math.max.apply(null, ppcSalesArr)) * 2
            /*callback: function(value, index, values) {
              return value + '%';
            }*/
          }
        }]
      }
    } //options
  });

  function chartUpdate(startUpdate, endUpdate) {
    startArr = dateArr.indexOf(startUpdate);
    endArr = dateArr.indexOf(endUpdate);

    subLabels = dateArr.slice(startArr, endArr + 1);
    subAdSpend = adSpendArr.slice(startArr, endArr + 1);
    subSales = ppcSalesArr.slice(startArr, endArr + 1);
    subAcos = ppcAcosArr.slice(startArr, endArr + 1);

    myChart.data.labels = subLabels;
    myChart.data.datasets[0].data = subAdSpend;
    myChart.data.datasets[1].data = subSales;
    myChart.data.datasets[2].data = subAcos;

    myChart.update();
  }

  
  /*$("#lineChart").on("mousemove", function(evt) {
    var element = $("#cursor"),
    offsetLeft = element.offset().left,
    domElement = element.get(0),
    clientX = parseInt(evt.clientX - offsetLeft),
    ctx = element.get(0).getContext('2d');

    ctx.clearRect(0, 0, domElement.width, domElement.height),
    ctx.beginPath(),
    ctx.moveTo(clientX, 33),
    ctx.lineTo(clientX, document.getElementById('lineChart').offsetHeight - 53),
    ctx.strokeStyle = "#07C",
    ctx.stroke()
  });*/
  
</script>
