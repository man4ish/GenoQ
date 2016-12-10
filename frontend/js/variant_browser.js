$(document).ready(function(){

	// Blendet Hilfetext aus
	$("#helptext-close").click(function() { $("#helptext").hide(); });
	
	// Buttons fuer input type (snps, gene, region)
	$("#selection_snps_container").buttonset();
	$("#snps_sentinels").show(); $("#snps_gene").hide(); $("#snps_region").hide();
	
	// Initialisiere Datensatz-Felder und Gennamen-Autocomplete
	loadSelectDatasets("genesautocomplete");
	
	// Update der annorelease Variable wenn anderes Release gewählt 
	$("select#dataset-genomerelease").change(function() {
		iniGenesAutocomplete();
	}); 
	$("select#dataset-annotation").change(function() {
		iniGenesAutocomplete();
	}); 
	
	// Event handler wenn input type geaendert wird
	$("input[name='selection_snps']").change(function(){
		if (this.id == "selection_snps_sentinels") { $("#snps_sentinels").show(); $("#snps_gene").hide(); $("#snps_region").hide(); }
		if (this.id == "selection_snps_gene") { $("#snps_sentinels").hide(); $("#snps_gene").show(); $("#snps_region").hide(); }
		if (this.id == "selection_snps_region") { $("#snps_sentinels").hide(); $("#snps_gene").hide(); $("#snps_region").show(); }
	});
	
	// Intialisiere Tabs
	$( "#tabs" ).tabs().hide();
	
	// SNP Autocomplete
	$.ajax({
		  url: "backend/snipaSnpBin.php",
		  type: "POST",
		  data: { task: "read" },
		  dataType: "json",
		  success: function(data) {	
			$("#sentinel").autocomplete({
			source: data,
			minLength: 0
			}).focus(function() { $(this).autocomplete("search",$("#sentinel").val()); });	
		  }
	  });
	
		
	// Status-Variable für default-action bei click auf SNP
	snpclickdefault = 0;
	
	// Starte Anfrage wenn Submit button angeklickt
	$("#submit-button").click(startJob);
	
	// Event Trigger falls Enter-Key im Feld gedrückt wird zum starten des Jobs
	$("#sentinel").keypress(function(event) { if (event.which == 13) { $("#submit-button").click(); } });
	$("#gene_symbol").keypress(function(event) { if (event.which == 13) { $("#submit-button").click(); } });
	$("#snps_region_position").keypress(function(event) { if (event.which == 13) { $("#submit-button").click(); } });
	
});



function startJob() 
{
	// Get ID for temp directory
	$.ajax({
		dataType: "text",
		async: false,
		cache: false,
		url: "backend/snipaTempdir.php",
		success: 
		function(randid) {
			if (randid.length == 15) {
				$("#progressbar").progressbar({	value: false });
				$(".progress-label").text("");
				$("#message").html("");
				$('#message').show();
				$('#progressbar').show();
				
				$("#progress-dialog").dialog({
					title: "Your job is being processed.",
					modal: true,
					dialogClass: "no-close",
					width: 500,
					resizable: false,
					draggable: false,
					buttons: {}
				});

				
				$.ajax(
				{
				  url : "backend/snipaVariantBrowser.php",
				  type: "POST",
				  data: {	id: randid, 
							genomerelease: $("select#dataset-genomerelease").val(), 
							referenceset: $("select#dataset-referenceset").val(), 
							population: $("select#dataset-population").val(), 
							annotation: $("select#dataset-annotation").val(), 
							snps_input_type: $("input[name=selection_snps]:checked").val(), 
							snps_sentinel: $("input#sentinel").val(),
							snps_gene: $("input#gene_symbol").val(), 
							snps_region_chr: $("select#snps_region_chr").val(),
							snps_region_position: $("input#snps_region_position").val()
						},
				  dataType:"json",
				  
				  beforeSend: function() {
								},
					
				  success: function(phpresults) {
								updateStatus(randid);
								if (phpresults['ok'] !== "FAIL") { 
									saveSelectDatasets();
									showResults(randid); 
								} else { $('#progress-dialog').dialog({ buttons: [ { text: "OK", click: function() { $(this).dialog("close"); } } ] });}
							}
				});
			}
		}
	});
}

function showResults(randid) {
	$('#progress-dialog').dialog("close");
	$('#message').hide();
	$('#progressbar').hide();
	$('#form-container').hide();
	$("#snpinfo-accordion").accordion({collapsible: true, heightStyle: "content", event: "click"});
	
	// Lade Positionen
	$.ajax(
	{
	  url : "tmpdata/"+randid+"/plotthis.txt",
	  type: "GET",
	  dataType:"script",
	  async: false
	});
	
	// Maximale Position bestimmen -> globale variable karyogramsize
	$.ajax(
	{
	  url : "backend/snipaVariantBrowserKaryogramJS.php",
	  type: "GET",
	  data: {	genomerelease: curgenomerelease,
				annotation: curannotation,
				chr: curchr,
				type: "karyogram_size"
			},
	  dataType:"script",
	  async: false
	});
	
	// noch fix: breite des plots (abstand von der mitte in bp)
	currange = 100000;
	curplotwidth = $("#tabs-body").width();
	
	curpos = Math.min(karyogramsize,Math.abs(curpos));
	
	// default-Font setzen
	Highcharts.setOptions({"chart": {"style": { "fontFamily": "Ubuntu", "fontSize": "10pt"}}});
	
	detailchart = $('#detailcontainer').highcharts({exporting: { enabled: false }, xAxis: { text: false}, yAxis: { text: false }, title: {text: false}, legend: { enabled: false }, series: [{ data: []	}]});
	karyogramchart = $('#karyogramcontainer').highcharts({exporting: { enabled: false }, xAxis: { text: false}, yAxis: { text: false }, title: {text: false}, legend: { enabled: false }, series: [{ data: []	}]});
	
	variantBrowserKaryogram(curgenomerelease,curreferenceset,curpopulation,curannotation,curchr,curpos,currange,curplotwidth,sentinel);
	

	// Scroll-Buttons
	$("#scroll-upstream-100").html(bpToHR(2*currange)).click(function(){ 
		curpos = Math.max(0+currange,Math.round(curpos-2*currange));
		variantBrowserKaryogram(curgenomerelease,curreferenceset,curpopulation,curannotation,curchr,curpos,currange,curplotwidth,sentinel);
	});
	
	$("#scroll-downstream-100").html(bpToHR(2*currange)).click(function(){ 
		curpos = Math.min(karyogramsize-currange,Math.round(curpos+2*currange));
		variantBrowserKaryogram(curgenomerelease,curreferenceset,curpopulation,curannotation,curchr,curpos,currange,curplotwidth,sentinel);
	});
	
	$("#scroll-upstream-50").html(bpToHR(currange)).click(function(){ 
		curpos = Math.max(0+currange,Math.round(curpos-currange));
		variantBrowserKaryogram(curgenomerelease,curreferenceset,curpopulation,curannotation,curchr,curpos,currange,curplotwidth,sentinel);
	});
	
	$("#scroll-downstream-50").html(bpToHR(currange)).click(function(){ 
		curpos = Math.min(karyogramsize-currange,Math.round(curpos+currange));
		variantBrowserKaryogram(curgenomerelease,curreferenceset,curpopulation,curannotation,curchr,curpos,currange,curplotwidth,sentinel);
	});
	
	$('#tabs').show();
}



function variantBrowserDetail(genomerelease,referenceset,population,annotation,chromosome,position,plotrange,plotwidth,sentinel) {
	$.ajax({
		dataType: "text",
		async: false,
		cache: false,
		url: "backend/snipaTempdir.php",
		success: 
		function(randid) {
			if (randid.length == 15) 
				{
					$.ajax(
						{
						  url : "backend/snipaVariantBrowserJS.php",
						  type: "GET",
						  data: {	id: randid, 
									genomerelease: genomerelease, 
									referenceset: referenceset,
									population: population, 
									annotation: annotation, 
									chr: chromosome,
									pos: position,
									size: plotrange,
									plotwidth: plotwidth,
									sentinel: sentinel
								},
						  dataType:"script",
						  cache: false,
						  beforeSend: function() { 
								$("#loading-dialog").dialog({
									title: "Rendering...",
									modal: true,
									dialogClass: "no-close",
									width: 400,
									resizable: false,
									draggable: false,
									buttons: {}
								});
								$("#loading-bar").progressbar({	value: false });
								$("#loading-message").html("<p>Plotting selected chromosomal region...<br />Please stand by.</p>");
							},
						  success: 
							function() {
							
								detailchart = new Highcharts.Chart(detailoptions);
								$("#loading-dialog").dialog("close");
						   }
						});
				}
			}
		});
}


function variantBrowserKaryogram(genomerelease,referenceset,population,annotation,chromosome,position,plotrange,plotwidth,sentinel) {
	$.ajax(
		{
		  url : "backend/snipaVariantBrowserKaryogramJS.php",
		  type: "GET",
		  data: {	genomerelease: genomerelease, 
					annotation: annotation,
					chr: chromosome,
					type: "karyogram_size"
				},
		  dataType:"script",
		  async: false
		});
	
	var karyogramoptions = {
		exporting: { enabled: false },
		credits: { enabled: false },
		title: { text: null },
		legend: { enabled: false },
		chart: {
			renderTo: 'karyogramcontainer',
			reflow: false,
			borderWidth: 0,
			backgroundColor: null,
			zoomType: false,
			plotBackgroundImage: 'backend/snipaVariantBrowserKaryogramJS.php?type=karyogram_image&chr='+chromosome+"&genomerelease="+genomerelease+"&annotation="+annotation,
			events: {
				load: function(event) {
					var xAxis = this.xAxis[0];
					xAxis.removePlotLine('mask');
					xAxis.addPlotLine({
						id: 'mask',
						value: position,
						color: '#FF0000',
						width: 4
					});
					curplotwidth = $("#tabs-body").width();
					variantBrowserDetail(genomerelease,referenceset,population,annotation,chromosome,position,plotrange,plotwidth,sentinel);
				},
				click: function(event) {
					var xAxis = this.xAxis[0];
					xAxis.removePlotLine('mask');
					xAxis.addPlotLine({
						id: 'mask',
						value: event.xAxis[0].value,
						color: '#FF0000',
						width: 4
					});
					curplotwidth = $("#tabs-body").width();
					variantBrowserDetail(genomerelease,referenceset,population,annotation,chromosome,event.xAxis[0].value,plotrange,plotwidth,sentinel);
					curpos = event.xAxis[0].value;
				} 				
			}
		},
		xAxis: {
			max: karyogramsize,
			min: 0,
			title: { text: "Chromosome "+chromosome }
		},
		yAxis: {
			max: 5,
			min: 1,
			gridLineWidth: 0,
			labels: { enabled: false },
			title: { text: null }
		},
		series: [{
			type: 'area'
		}],
		plotOptions: {
			area: {
				animation: false,
				lineWidth: 1,
				marker: {
					enabled: false
				},
				shadow: false,
				states: {
					hover: {
						lineWidth: 1
					}
				},
				enableMouseTracking: false
			}
		}
	};
	
	$.getJSON("backend/snipaVariantBrowserKaryogramJS.php?type=functional_annotation&genomerelease="+genomerelease+"&annotation="+annotation+"&chr="+chromosome, function(data) {
		karyogramoptions.series[0].data = data;
		var karyogramchart = new Highcharts.Chart(karyogramoptions);
	});
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



function addToSnpAnnotationsTab(SnpInfoTabId,snpname,snppos,snpchr,sentinelpos,genomerelease,referenceset,population,annotation) {
	$.ajax(
			{
			  url: "backend/snipaRAPlotsAnnotations.php",
			  type: "GET",
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
					annopanel += "<span onclick=\"$(this).parent('h3').next('div').remove(); $(this).parent('h3').hide().remove(); $('#snpinfo-accordion').accordion('refresh'); if ($('#snpinfo-accordion h3').length < 1) { $('#nosnpinfo').show(); resultstab = getIndexForId('#tabs-header','#tabs-browser'); $('#tabs').tabs({active: resultstab}); } \" class=\"pinkspan\">delete</span>";
					annopanel += "<span onclick=\"printCard('"+genomerelease+"', '"+referenceset+"', '"+population+"', '"+annotation+"', '"+snpname+"');\" class=\"pinkspan\">save as PDF</span>";
					annopanel += "<span onclick=\"addToSnpBin('"+snpname+"','"+snppos+"','"+snpchr+"','"+genomerelease+"','"+referenceset+"','"+population+"','"+annotation+"'); \" class=\"pinkspan\">add to clipboard</span>";
					annopanel += "</h3>";
					annopanel += "<div>" + anno + "</div>";
					$("#snpinfo-accordion").prepend(annopanel);
					if (snpclickdefault != 1) {
						var tabIndex = getIndexForId("#tabs-header",SnpInfoTabId);
						$("#tabs").tabs({active: tabIndex});
					}
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



