<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="http://code.highcharts.com/stock/highstock.js"></script>
	<script>
		var UNDEFINED;

		var options = {
		    chart: {
		        renderTo: 'container',
                        type: 'scatter',
                        //zoomType: 'xy',
		    },
                    title: {
                     text: 'PCA Plot',
                     //x: -20 //center
                    },
                    xAxis: {
                      gridLineWidth: 0                   
                      //min:-.1,
                      //max:.1,
                    },
                    yAxis: {
                     lineWidth: 1, 
                     gridLineWidth: 0
                      //min:-.1,
                      //max:.1,
                    },
                     plotOptions: {
            series: {
                animation: false
            }
        },
		    series: [{
                        name: 'Q108',
                        color: '#E618C4',
		    	data:[]
		    },
                    {
                        name: 'Q1',
                        color: '#E6DC18',
		    	data:[]
		    },
                    {
                        name: 'Q2',
                        color: '#18A2E6',
		    	data:[]
		    },
                    {
                        name: 'Q3',
                        color: '#18E691',
		    	data:[]
		    }
                    ]
              
		};

		$.get('frontend/pca_data.csv', function(data) {

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

<script src="frontend/js/formvalidator.js"></script>
<script src="frontend/js/pcaplot.js"></script>
<table style="width:100%">
<form name="myForm" id="testForm" action="?task=summary" onsubmit="return validateForm()" method="post">
     <tr><td width="68%"></td><td width="13%"><input id="seachbox" type="text" size=14 name="search" class="inputbox2" /></td><td width="25%"><input type="submit" value="Search"></td></tr>
		 
	<tr><td width="78%"></td><td width="13%">
     <!--<font size=".2">Snp, Gene, Region</font></td></tr>-->
       <font size=".2">Gene, Region</font></td></tr>
</form>
</table>

       <h3>Welcome to Geno<span style="color: rgb(199, 70, 91);">Q!</span></h3>
       <p>GenoQ provides several ways to interact with Qatari genomics data. Allele frequencies, haplotype, linkage disequilibrium etc can be queried for region of interest. User can also interactively map their genome to specific Qatari subgroup. Users can also visualize their GWAS results against Qatari genetic map through regional Regional Association Plot <br />
			At present, GenoQ combines LD data based on the <a href="http://www.1000genomes.org">1000 Genomes Project</a> (Phase 1 version 3) with various annotation layers, such as gene annotations, phenotypic trait associations, and expression-/metabolic quantitative trait loci. See the <a href="?task=documentation">documentation</a> for all data sources integrated into GenoQ.

                        
        </div> <div id="container" style="width:500px;height:340px;margin: 0px 140px"></div>
        <br>
        <script>
				if (Modernizr.svgasimg == true) { 
					$('#home_icon_proxysearch').css('background-image','url(frontend/img/home_icon_proxysearch.svg)');
					$('#home_icon_pairwiseld').css('background-image','url(frontend/img/home_icon_pairwiseld.svg)');
					$('#home_icon_raplot').css('background-image','url(frontend/img/home_icon_raplot.svg)');
					$('#home_icon_ldplot').css('background-image','url(frontend/img/home_icon_ldplot.svg)');
					$('#home_icon_snpannotation').css('background-image','url(frontend/img/home_icon_snpannotation.svg)');
					$('#home_icon_ldannotation').css('background-image','url(frontend/img/home_icon_ldannotation.svg)');
					$('#home_icon_gwas').css('background-image','url(frontend/img/home_icon_gwas.svg)');
					$('#home_icon_variantbrowser').css('background-image','url(frontend/img/home_icon_variantbrowser.svg)');
					$('#home_icon_documentation').css('background-image','url(frontend/img/home_icon_documentation.svg)');

				}
			</script>
			<p>
			Did you use GenoQ for a publication? We would appreciate if you cite us:<br /><br />
			Kumar Manish<sup>&Dagger;</sup>, Thareja Gaurav<sup>&Dagger;</sup>,Kumar Pankaj and  Suhre K, Genoq beta: an interactive, variant-centered annotation browser for Qatar Genome <span style="font-style: italic;"><br />
			<span style="font-size: smaller;"><sup>&Dagger;</sup>These authors contributed equally.</span>
			</p>
			<div style="font-size: smaller;">
			<p>
				If you have feedback, problems or questions regarding GenoQ, feel free to <a href="http://qatar-weill.cornell.edu/research/faculty/suhreLab.html">contact us</a>. Some of GenoQ's features only work on modern browsers. GenoQ was successfully tested on Internet Explorer &ge; 9, Firefox &ge; 27, Safari &ge; 7, and Chrome &ge; 33.
			</p>
			<p>Please note that GenoQ stores a temporary cookie containing a <a href="http://en.wikipedia.org/wiki/Session_ID">session ID</a> on your computer. This session ID is needed for several GenoQ tools, such as the GenoQ clipboard. The cookie will be deleted from your computer when you close your browser. Any data that you may upload during your site visit will be stored in temporary files on our server. These files are inaccessible to other users and will be deleted irreversibly within 24 hours.</p>
			</div>
									

