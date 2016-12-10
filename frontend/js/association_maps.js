$(document).ready(function(){
	
	// Buttons fuer input type (snps, gene, region)
	$("#selection_container").buttonset();
	$(".gwas_catalog").show(); $(".own_map").hide();
	
	// Event handler wenn input type geaendert wird
	$("input[name='selection_maps']").change(function(){
		if (this.id == "selection_gwas_catalog") { $(".gwas_catalog").show(); $(".own_map").hide(); }
		if (this.id == "selection_own_map") { $(".gwas_catalog").hide(); $(".own_map").show(); }
	});
	
	$('#assoc_load').hide();	
	$("#helptext-close").click(function() { $("#helptext").hide(); });
	$("#tabs").tabs({active: 0});
	$("#snpinfo-accordion").accordion({collapsible: true, heightStyle: "content", event: "click"});
	
	// Initialisiere Datensatz-Felder 
	loadSelectDatasets();
	
	// initialisiere Status-Variable für default-action bei click auf SNP
	snpclickdefault = 0;
	
	// Default-Font auf Ubuntu setzen
	Highcharts.setOptions({"chart": {"style": { "fontFamily": "Ubuntu"}}});
	
	$.ajax({
		url: 'backend/snipaDiseases.php',
		type: 'GET',
		cache: false,
		dataType: 'json',
		success: function( json ) {
			$.each(json, function(i, value) {
            $('#traitlist').append($('<option>').text(value).attr('value', value));
			});
		}
	});
	
	$('#trait').on('input',function(){
		var text = $('#trait').val();
		$.ajax({
			url: 'backend/snipaDiseases.php?term='+text,
			type: 'GET',
			dataType: 'json',
			cache: false,
			success: function( json ) {
				$('#traitlist').find('option').remove();
				$.each(json, function(i, value) {
				$('#traitlist').append($('<option>').text(value).attr('value', value));
				});
			}
		});
	});
	
	$('#traitlist').on('change',function() {
		var chart = $('#assoc_map').highcharts();

		$('#assoc_load').show();
		
		while(chart.series.length > 0){
			chart.series[0].remove(false);
		}
		
		chart.redraw();

		$('#traitlist option:selected').each(function(){
			var text = encodeURIComponent($(this).text());
			$.ajax({
				url: 'backend/snipaDiseaseSNP.php?term='+text,
				type: 'GET',
				data: {	genomerelease: $("select#dataset-genomerelease").val(), 
					referenceset: $("select#dataset-referenceset").val(), 
					population: $("select#dataset-population").val(), 
					annotation: $("select#dataset-annotation").val()
				},
				dataType: 'json',
				async: false,
				cache: false,
				success: function( json ) {
					chart.addSeries({
						name: text,
						data: json
					}, false, false);
					console.log(chart.series[0]);
				}
			});
		});
		
		chart.redraw();
		$('#assoc_load').hide();
	});
	
	// -------------------------------- TEST FOR OWN MAPS --------------
	
	$('#submit-button').click(function() {
		var chart = $('#assoc_map').highcharts();

		$('#assoc_load').show();
		
		while(chart.series.length > 0){
			chart.series[0].remove(false);
		}
		
		chart.redraw();

		$.ajax(
		{
			url : "backend/snipaOwnDiseaseSNP.php",
			type: "POST",
			data: {	
				genomerelease: $("select#dataset-genomerelease").val(), 
				referenceset: $("select#dataset-referenceset").val(), 
				population: $("select#dataset-population").val(), 
				annotation: $("select#dataset-annotation").val(),  
				snps: $("textarea#snps_input").val(),
				trait: $("input#own_trait").val()
			},
			dataType:"json",
			sync: false,
			cache: false,
			
			error: function() {
				alert("User input invalid!");
			},
			
			success: function( json ) {
				chart.addSeries({
					name: "User series",
					data: json
				}, true, false);
				console.log(chart.series[0]);
			}
		});

		$('#assoc_load').hide();
	});
	
	// -------------------------------- END OWN MAPS -------------------
		
	$('#assoc_map').highcharts({
		chart: {
			spacingTop: 5,
			marginTop: 20,
			spacingRight:15,
			type: 'scatter',
			animation: false,
			plotBackgroundImage: 'http://snipa.helmholtz-muenchen.de/snipa/frontend/img/ideogram_hg19.png'
		},
		title: {
			text: null
		},
		subtitle: {
			text: null
		},
		xAxis: {
			tickInterval: 50000000,
			showLastLabel: true,
			showFirstLabel: true,
			title: {
				enabled: false
			},
			lineWidth: 0,
			showEmpty: false,
			gridLineWidth: 0,
			max: 249000000,
			min: 0
		},
		yAxis: {
			minPadding: 0,
			tickInterval: 0.25,
			offset: 2,
			showFirstLabel: false,
			title: {
				enabled: false
			},
			lineWidth: 0,
			gridLineWidth: 0,
			reversed: true,
			max: 46.25,
			min: 0,
			labels: {
				step: 8,
				formatter: function(){if(this.value/2<23){ return "chr"+(1*(this.value/2)); }else{ return "chrX"; }}
			}
		},
		exporting: {
			enabled: true,
			url: 'backend/HighchartsExport/',
			sourceWidth: 1024,
			sourceHeight: 900,
			filename: 'SNiPA_AssociationMap'
		},
		credits: {
			enabled: false
		},
		legend: {
			enabled: false
		},
		plotOptions: {
			scatter: {
				animation: false,
				marker: {
					radius: 7,
					symbol: 'triangle-down',
					lineColor: '#000000',
					lineWidth: 1,
					states: {
						hover: {
							enabled: true,
							lineColor: '#000000'
						}
					}
				},
				point: {
					cursor: "pointer",
					events: {
						click: function(event) { 
								var tmpsnpname = this.rsid; 
								var tmpsnppos = this.x; 
								var tmpsnpchr = this.chr;
								var tmpgenomerelease = $("select#dataset-genomerelease").val();
								var tmpreferenceset = $("select#dataset-referenceset").val();
								var tmppopulation = $("select#dataset-population").val();
								var tmpannotation = $("select#dataset-annotation").val();
								showPlotAnnotationMenu(event,tmpsnpname,tmpsnppos,tmpsnpchr,tmpsnppos,tmpgenomerelease,tmpreferenceset,tmppopulation,tmpannotation);
							}
						}
				},
				states: {
					hover: {
						marker: {
							enabled: false
						}
					}
				}
				
			}
		},
		tooltip: {
			followPointer: false,
			useHTML: true,
			backgroundColor: '#FFFFFF',
			hideDelay: 0,
			animation: false, 
			borderRadius: 0,
			formatter: function() { var tooltip = '<table class=\'snipa-plot-tooltip\'><thead><tr><td colspan=\'2\'><span style="color: '+this.series.color+'; font-weight: bold;">'+this.point.trait+'</span></td></tr></thead><tbody>';
									tooltip += '<tr><td>Associated SNP:</td><td>'+this.point.rsid+'</td></tr>';
									if (this.point.rsalias.length > 0) { tooltip += '<tr><td>Alias:</td><td>'+this.point.rsalias+'</td></tr>'; }
									tooltip += '<tr><td>Association p-value:</td><td>'+this.point.pval+'</td></tr>';
									tooltip += '<tr><td>Genetic location:</td><td>'+this.point.chr+':'+this.point.x+'</td></tr>';
									tooltip += '</tbody></table>'
									return tooltip;
								}
			},
		series: [{
			data: []

		}]
	});
});


function addToSnpAnnotationsTab(SnpInfoTabId,snpname,snppos,snpchr,sentinelpos,genomerelease,referenceset,population,annotation) {
	$.ajax(
			{
			  url: "backend/snipaRAPlotsAnnotations.php",
			  type: "POST",
			  data: {	snpname: snpname,
						snppos: snppos,
						snpchr: snpchr,
						sentinelpos: sentinelpos,
						genomerelease: genomerelease,
						referenceset: referenceset,
						population: population,
						annotation: annotation
				},
			  dataType: "text",
			  success: function(anno) { 
					$("#nosnpinfo").hide();
					$("#snpinfo-accordion").show();
					var annopanel = "<h3>"+snpname;
					annopanel += "<span onclick=\"$(this).parent('h3').next('div').remove(); $(this).parent('h3').hide().remove(); $('#snpinfo-accordion').accordion('refresh'); if ($('#snpinfo-accordion h3').length < 1) { $('#nosnpinfo').show(); dynplottab = getIndexForId('#tabs-header','#tabs-results'); $('#tabs').tabs({active: dynplottab}); } \" class=\"pinkspan\">delete</span>";
					annopanel += "<span onclick=\"printCard('"+genomerelease+"', '"+referenceset+"', '"+population+"', '"+annotation+"', '"+snpname+"');\" class=\"pinkspan\">save as PDF</span>";
					annopanel += "<span onclick=\"addToSnpBin('"+snpname+"','"+snppos+"','"+snpchr+"','"+genomerelease+"','"+referenceset+"','"+population+"','"+annotation+"'); \" class=\"pinkspan\">add to clipboard</span>";
					annopanel += "</h3>";
					annopanel += "<div>" + anno + "</div>";
					$("#snpinfo-accordion").prepend(annopanel);
					var tabIndex = getIndexForId("#tabs-header",SnpInfoTabId);
					$( "#tabs" ).tabs({active: tabIndex});
					$("#snpinfo-accordion").accordion("refresh");
					$("#snpinfo-accordion").accordion("option","active",0);
					$("#snpinfo-accordion h3 span.pinkspan").click(function() {return false;} ); // Verhindert das ein- und ausklappen bei den löschen und snpbin buttons
					
					// Tooltips für CADD scores etc
					$( ".whatsthis" ).tooltip({
							content:function(){return $(this).attr('title').replace(/\[/g,'<').replace(/\]/g,'>')},
							my: "left top+25", 
							at: "left bottom", 
							collision: "flipfit",
							show: false,
							hide: false,
							track: true
						});
				}
			}
		);
}



// Menue fuer SNP-Annotationen im dynamischen Plot
function showPlotAnnotationMenu(eventtype,snpname,snppos,snpchr,sentinelpos,genomerelease,referenceset,population,annotation) { 
	if (snpclickdefault == 0) {
		$('#snpdetails-container').show(); 
		$('#snpdetails-container').dialog({
				title: 'SNP ' + snpname,
				modal: true,
				position: { my: 'left top', of: eventtype, offset: '10 10', collision: 'fit' },
				buttons: [ { text: 'Show annotation', 
							 click: function() { if ($('#snpdetails-sameaction').is(':checked')) {
													snpclickdefault = 1;
													 }
												 $(this).dialog('destroy'); 
												 $('#snpdetails-menu').html('');
												 addToSnpAnnotationsTab('#tabs-snpinfo',snpname,snppos,snpchr,sentinelpos,genomerelease,referenceset,population,annotation);
											   }
						   } , 
						   { text: 'Copy to clipboard', 
							 click: function() { 
												 if ($('#snpdetails-sameaction').is(':checked')) {
													snpclickdefault = 2;
												 }
												$('#snpdetails-menu').html('');
												$(this).dialog('destroy'); 
												addToSnpBin(snpname,snppos,snpchr,genomerelease,referenceset,population,annotation);
												}
						   } 
						 ]
			}); 
		$('#snpdetails-menu').html('You can either get detailed annotations for this variant or copy it to SNiPA\'s clipboard.<br /><input type=\"checkbox\" id=\"snpdetails-sameaction\" style=\"width: 10px;\" /> Use this as default action for this session.');  
		} 
		
	if (snpclickdefault == 1) { addToSnpAnnotationsTab('#tabs-snpinfo',snpname,snppos,snpchr,sentinelpos,genomerelease,referenceset,population,annotation); }
	if (snpclickdefault == 2) { addToSnpBin(snpname,snppos,snpchr,genomerelease,referenceset,population,annotation); }
} 


