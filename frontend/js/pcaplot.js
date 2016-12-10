<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="http://code.highcharts.com/stock/highstock.js"></script>
<script>
		var UNDEFINED;
		var options = {
		    chart: {
		        renderTo: 'container',
                        type: 'scatter',
                        zoomType: 'x',
		    },
                    title: {
                     text: 'Monthly Average Temperature',
                    },
                    xAxis: {
                      //min:-.1,
                      //max:.1,
                    },
                    yAxis: {
                      //min:-.1,
                      //max:.1,
                    },
		    series: [{
                        color: 'rgba(223, 83, 83, .5)',
		    	data:[]
		    },
                    {
                        color: 'green',
		    	data:[]
		    },
                    {
                        color: 'blue',
		    	data:[]
		    },
                    {
                        color: 'black',
		    	data:[]
		    }
                    ]
              
		};

		$.get('pca_data.csv', function(data) {

	    	var lines = data.split('\n'),
	    		items, itemValues;
	        
	        $.each(lines, function(lineNo, line){

	        	if(lineNo > 0) {

		        	itemsDates = line.split('\t');

		        	$.each(itemsDates, function(itemNo, item) {
		            	if(itemsDates[2] == 0) {
			                options.series[0].data.push({
			                	x: parseFloat(itemsDates[0]),
                                                y: parseFloat(itemsDates[1])
			                });
		            	}else if(itemsDates[2] == 1) {
			                options.series[1].data.push({
			                	x: parseFloat(itemsDates[0]),
                                                y: parseFloat(itemsDates[1])
			                });
		            	} 
                                else if(itemsDates[2] == 2) {
                                      options.series[2].data.push({
			                	x: parseFloat(itemsDates[0]),
                                                y: parseFloat(itemsDates[1])
			                });
                                }
                                else if(itemsDates[2] == 3) {
                                      options.series[3].data.push({
			                	x: parseFloat(itemsDates[0]),
                                                y: parseFloat(itemsDates[1])
			                });
                                }
		            });
		        }

	        });

		    // Create the chart
		    var chart = new Highcharts.Chart(options);
		});

	</script>
