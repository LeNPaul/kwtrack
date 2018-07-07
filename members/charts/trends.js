$('.chart').each((index, element) => {
  let data = '';
  console.log(data);
  let json = $.parseJSON(data);
  console.log(json);
  let ranks = [];
  for (let i = 0; i < json.length; i++) {
    let currentRank;
    if (json[i]['page'] > 1) {
      currentRank = -1 * 17 * parseInt(json[i]['page']) + parseInt(json[i]['rank']);
      ranks.push(parseInt(currentRank));
    } else {
      currentRank = -1 * parseInt(json[i]['rank']);
      ranks.push(parseInt(currentRank));          
    }
  }

  let maxValue = ranks.reduce((a, b) => Math.max(a, b));
  console.log(ranks);

  let chartData = {
    labels:[1,2,3,4,5,6,7],
    datasets: [{
      label: "rank",
      borderWidth: 1.5,
      data: ranks,
      backgroundColor: "red",
      borderColor: "lightblue",
      fill: false,
      lineTension: 0,
      pointRadius: 0
    }]
  };

  let options = {
    maintainAspectRatio: false,
    legend: { display: false },
    scales: {
      xAxes: [{
        display: false,
        gridLines: { display: false }
      }],
      yAxes: [{
        display: false,
        gridLines: { display: false },
        ticks: { 
          beginAtZero: true,
          stepSize: 1,
          max: maxValue
        }
      }]
    }
  };

  let ctx = element.getContext('2d');
  window.myLine = new Chart(ctx, {
    type: "line",
    data: chartData,
    options: options
  });
});