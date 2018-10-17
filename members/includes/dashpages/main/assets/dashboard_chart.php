<?php

?>

<script type="text/javascript">
  $(function() {

    var start = moment().subtract(59, 'days');
    var end = moment();

    function cb(begin, finish) {
      start = begin.format('MMM DD');
      end = finish.format('MMM DD');
      chartUpdate(start, end);
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
      fill: 'origin',
      pointRadius: 2.5,
      hoverRadius: 4,
      pointBorderColor: '#ffffff',
      pointBorderWidth: 1,
      pointBackgroundColor: "rgb(244, 72, 66)",
      hoverBorderWidth: 3,
      backgroundColor: "rgba(244, 72, 66, 0.1)",
      borderWidth: 1.5,
      borderColor: "rgba(244, 72, 66, 0.7)",
      type: 'line'
    }, {
      label: "PPC Sales ($)",
      yAxisID: 'A',
      data: ppcSalesArr,
      fill: true,
      pointRadius: 2.5,
      hoverRadius: 4,
      pointBorderColor: '#ffffff',
      pointBorderWidth: 1,
      pointBackgroundColor: "rgb(89, 255, 152)",
      hoverBorderWidth: 3,
      backgroundColor: "rgba(89, 255, 152, 0.1)",
      borderWidth: 1.2,
      borderColor: "rgba(89, 255, 152, 0.7)",
      type: 'line'
    }, {
      label: "PPC ACoS (%)",
      yAxisID: 'B',
      data: ppcAcosArr,
      fill: true,
      backgroundColor: "rgba(114, 187, 255, 0.4)",
    }]
  };

  var myChart = new Chart(ctx, {
    type: "bar",
    data: lineData,

    options: {
      /*toolips: {
        mode: 'y',
        intersect: false
      },*/
      /*tooltips: {
        enabled: false,
        custom: function(tooltipModel) {
          // Tooltip Element
          var tooltipEl = document.getElementById('chartjs-tooltip');

          // Create element on first render
          if (!tooltipEl) {
            tooltipEl = document.createElement('div');
            tooltipEl.id = 'chartjs-tooltip';
            tooltipEl.innerHTML = "<table></table>";
            document.body.appendChild(tooltipEl);
          }

          // Remove if no tooltip
          if (tooltipModel.opacity === 0) {
            tooltipEl.remove();
            return;
          }

          // Set caret Position
          tooltipEl.classList.remove('above', 'below', 'no-transform');
          if (tooltipModel.yAlign) {
            tooltipEl.classList.add(tooltipModel.yAlign);
          } else {
            tooltipEl.classList.add('no-transform');
          }

          function getBody(bodyItem) {
            return bodyItem.lines;
          }

          // Set Text
          if (tooltipModel.body) {
            var titleLines = tooltipModel.title || [];
            var bodyLines = tooltipModel.body.map(getBody);

            var innerHtml = '<thead>';

            titleLines.forEach(function(title) {
              innerHtml += '<tr><th>' + title + '</th></tr>';
            });
            innerHtml += '</thead><tbody>';

            bodyLines.forEach(function(body, i) {
              var colors = tooltipModel.labelColors[i];
              var style = 'background:' + colors.backgroundColor;
              style += '; border-color:' + colors.borderColor;
              style += '; border-width: 2px';
              var span = '<span style="' + style + '"></span>';
              innerHtml += '<tr><td>' + span + body + '</td></tr>';
            });
            innerHtml += '</tbody>';

            var tableRoot = tooltipEl.querySelector('table');
            tableRoot.innerHTML = innerHtml;
          }

          // `this` will be the overall tooltip
          var position = this._chart.canvas.getBoundingClientRect();

          // Display, position, and set styles for font
          tooltipEl.style.opacity = 1;
          tooltipEl.style.position = 'absolute';
          tooltipEl.style.left = position.left + tooltipModel.caretX + 'px';
          tooltipEl.style.top = position.top + tooltipModel.caretY + 'px';
          tooltipEl.style.fontFamily = tooltipModel._bodyFontFamily;
          tooltipEl.style.fontSize = tooltipModel.bodyFontSize + 'px';
          tooltipEl.style.fontStyle = tooltipModel._bodyFontStyle;
          tooltipEl.style.padding = tooltipModel.yPadding + 'px ' + tooltipModel.xPadding + 'px';
        }
      },*/
	  tooltips: {
		mode: 'x',
	    intersect: false
	  },
	  hover: {
		mode: 'nearest',
		intersect: false
	  },
	  responsive: true,
      elements: {
        line: {
          //fill: '-1'
        }
      },
      scales: {
        // Remove grid lines
        xAxes: [{
          gridLines: {
            color: "rgba(0, 0, 0, 0)",
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
            color: "rgba(0, 0, 0, 0)",
          },
          /*ticks: {
            callback: function(value, index, values) {
              return '$' + value;
            }
          }*/
          //stacked: true
        }, {
          id: 'B',
          type: 'linear',
          position: 'right',
          gridLines: {
            color: "rgba(0, 0, 0, 0)",
          },
          ticks: {
            beginAtZero: true,
            display: false,
            max: Math.round(Math.max.apply(null, ppcSalesArr)) * 2,
            /*callback: function(value, index, values) {
              return value + '%';
            }*/
          }
        }]
      },
	  
	  animation: {
		  onComplete: function() {
		  }
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
  };

  $(document).ready(function(){
	$("#lineChart").on("mousemove", function(evt) {
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
	});
  });
</script>
