
<?php
print($_GET['var']);
?>

<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="robots" content="noindex">
  <title> - jsFiddle demo</title>
  
  
  

  
  
  
  <link rel="stylesheet" type="text/css" href="/css/result-light.css">
  
  <style type='text/css'>
    
  </style>
  




<script type='text/javascript'>//<![CDATA[

$(function() {
  var options = {
    chart: {
      renderTo: 'container',
      plotBackgroundColor: null,
      plotBorderWidth: 1, //null,
      plotShadow: false
    },
    title: {
      text: ''
    },
    tooltip: {
      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        showInLegend: true,
        dataLabels: {
          enabled: false,
          format: '<b>{point.name}</b>: {point.percentage:.1f} %',
          style: {
            color:  ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
          },
          showInLegend: true
        }
      }
    },

    series: [{}]
  };
  

  $.getJSON('frontend/js/data.json', function(data) {
  options.series = data;
  var chart = new Highcharts.Chart(options);
  });


});

//]]> 

</script>

</head>
<body>

<div id="container" style="width: 400px; height: 400px; margin: 0 auto"></div>

  
</body>

</html>


