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

  Chart.defaults.LineWithLine = Chart.defaults.line;
  Chart.controllers.LineWithLine = Chart.controllers.line.extend({
    draw: function(ease) {
      Chart.controllers.line.prototype.draw.call(this, ease);

      if (this.chart.tooltip._active && this.chart.tooltip._active.length) {
        var activePoint = this.chart.tooltip._active[0],
            ctx = this.chart.ctx,
            x = activePoint.tooltipPosition().x,
            axisAtopY = this.chart.scales['A'].top,
            axisAbottomY = this.chart.scales['A'].bottom;

        // draw line
        ctx.save();
        ctx.beginPath();
        ctx.moveTo(x, axisAtopY);
        ctx.lineTo(x, axisAbottomY);
        ctx.lineWidth = 2;
        ctx.strokeStyle = '#07C';
        ctx.stroke();
        ctx.restore();
      }
    }
  });

  var lineData = {
    labels: dateArr,
    datasets: [{
      label: "Ad Spend ($)",
      yAxisID: 'A',
      data: adSpendArr,
      fill: false,

      pointRadius: 0,
      hoverRadius: 6,
      pointBorderColor: '#ffffff',
      pointHoverBorderColor: '#ffffff',
      pointBackgroundColor: "rgb(244, 72, 66)",
      hoverBorderWidth: 3,

      borderWidth: 1,
      borderColor: "rgba(244, 72, 66, 0.7)",
    }, {
      label: "PPC Sales ($)",
      yAxisID: 'A',
      data: ppcSalesArr,
      fill: false,

      pointRadius: 0,
      hoverRadius: 6,
      pointBorderColor: '#ffffff',
      pointHoverBorderColor: '#ffffff',
      pointBackgroundColor: "rgb(89, 255, 152)",
      hoverBorderWidth: 3,

      borderWidth: 2,
      borderColor: "rgb(89, 255, 152)"
    }, {
      label: "<b>PPC ACoS</b>",
      yAxisID: 'B',
      data: ppcAcosArr,
      fill: false,

      pointRadius: 0,
      hoverRadius: 6,
      pointBorderColor: '#ffffff',
      pointHoverBorderColor: '#ffffff',
      pointBackgroundColor: "rgb(114, 187, 255)",
      hoverBorderWidth: 3,

      borderWidth: 1.4,
      borderColor: "#d14785"
    }]
  };

  var chart = new Chart(ctx, {
    type: "LineWithLine",
    data: lineData,

    options: {
      /*elements: {
        line: {
          fill: '-1'
        }
      },*/
      hover: {
        mode: 'index',
        intersect: false
      },
      multiTooltipTemplate: "<%=datasetLabel%> : <%= value %>",
      tooltips: {
        mode: 'index',
        intersect: false,
        backgroundColor: "rgba(255,255,255,0.8)",

        titleFontSize: 16,
        titleFontStyle: 'bold',
        titleFontColor: '#444444',
        titleMarginBottom: 20,

        bodyFontSize: 14,
        bodySpacing: 8,
        bodyFontColor: '#000000',

        xPadding: 25,
        yPadding: 25,

        caretPadding: 10,
        caretSize: 7,
        cornerRadius: 2,
        displayColors: false,
        borderWidth: 1,
        borderColor: "#cccccc",

        callbacks: {
          // label: function(item, data) {
          //   console.log(item);
          //   return data + '$' + item.yLabel;
          // },
          title: function(item, data) {
            return "Stats for " + item[0].xLabel;
          }
        }
      },

      responsive: true,
      maintainAspectRatio: false,

      scales: {
        // Remove grid lines
        xAxes: [{
          gridLines: {
            borderDash: [5, 3],
            color: "rgba(209, 209, 209, 0.3)"
          },

          scaleLabel: {
            show: true,
            labelString: 'Value'
          }
        }],

        yAxes: [{
          id: 'A',
          type: 'linear',
          position: 'left',
          gridLines: {
            borderDash: [5, 3],
            color: "rgba(209, 209, 209, 0.3)"
          }
        }, {
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
