$(document).ready(function(){

	// Blendet Hilfetext aus
	$("#helptext-close").click(function() { $("#helptext").hide(); });
	
	// Initialisiere Datensatz-Felder und Gennamen-Autocomplete
	loadSelectDatasets("genesautocomplete");
	
	// Update der annorelease Variable wenn anderes Release gewählt 
	$("select#dataset-genomerelease").change(function() {
		iniGenesAutocomplete();
	}); 
	$("select#dataset-annotation").change(function() {
		iniGenesAutocomplete();
	});
	
	// Buttons fuer input type (snps, gene, region)
	$("#selection_snps_container").buttonset();
	$("#snps_variants").hide(); $("#snps_ld").show(); $("#snps_ld_r2").show(); $("#snps_gene").hide(); $("#snps_region").hide();
	
	// Buttons fuer Functionale Annotation
	$("#incl_funcann_container").buttonset();
	
	// Event handler wenn input type geaendert wird
	$("input[name='selection_snps']").change(function(){
		if (this.id == "selection_snps_variants") { $("#snps_variants").show(); $("#snps_ld").hide(); $("#snps_ld_r2").hide(); $("#snps_gene").hide(); $("#snps_region").hide(); }
		if (this.id == "selection_snps_ld") {  $("#snps_variants").hide(); $("#snps_ld").show(); $("#snps_ld_r2").show(); $("#snps_gene").hide(); $("#snps_region").hide(); }
		if (this.id == "selection_snps_gene") { $("#snps_variants").hide(); $("#snps_ld").hide(); $("#snps_ld_r2").hide(); $("#snps_gene").show(); $("#snps_region").hide(); }
		if (this.id == "selection_snps_region") { $("#snps_variants").hide(); $("#snps_ld").hide(); $("#snps_ld_r2").hide(); $("#snps_gene").hide(); $("#snps_region").show(); }
	});
	
	// Slider fuer LD Threshold
	$(function() {
		var ld_threshold_select = $("#ld_threshold");
		var ld_threshold_slider_labels = new Array("","0.1","0.2","0.3","0.4","0.5","0.6","0.7","0.8","0.9","1.0")
		var ld_threshold_slider = $("<div id='ld_threshold_slider_label_container' style='margin-bottom: 5px;'><div id='ld_threshold_slider_label'>0.8</div></div><div id='ld_threshold_slider'></div>").insertAfter(ld_threshold_select);
		$("#ld_threshold_slider_label").css("position","relative");
		$("#ld_threshold_slider_label").css("width","20px");
		$("#ld_threshold_slider_label").css("margin-left",-($("#ld_threshold_slider_label_container").width()/7.5));
		$("#ld_threshold_slider_label").css("left", (8/9)*100+"%"); 
		$("#ld_threshold_slider").slider({
			min: 1,
			max: 10,
			step: 1, 
			value: ld_threshold_select[0].selectedIndex + 1,
			slide: function(event,ui) {
				ld_threshold_select[0].selectedIndex = ui.value -1;
				$("#ld_threshold_slider_label").text(ld_threshold_slider_labels[ui.value]);
				$("#ld_threshold_slider_label").css("left", ((ui.value)/(9))*100+"%");
				$("#ld_threshold_slider_label").css("margin-left",-($("#ld_threshold_slider_label_container").width()/7.5));
				}
			});
		$("#ld_threshold").hide();
		}
	);
	
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
	
	// Button für SNP pastebin bei Varianten
	if ($("#snpbinbox").is(":visible")) {
		$("#snps_variants_input").after("<button id='snps_variants_from_pastebin'>Paste from SNiPA's clipboard</button>");
		$("#snps_variants_from_pastebin").button().click(function() {
			$.ajax({
				  url: "backend/snipaSnpBin.php",
				  type: "POST",
				  data: { task: "read" },
				  dataType: "json",
				  success: function(data) {	
					$.each(data, function(i,v) {
						$("#snps_variants_input").val($("#snps_variants_input").val()+v.value+'\n');
					});
				  }
			  });
			}
		);
	}
	
	
	// SNP Autocomplete bei Sentinel
	$.ajax({
		  url: "backend/snipaSnpBin.php",
		  type: "POST",
		  data: { task: "read" },
		  dataType: "json",
		  success: function(data) {	
			$("#ld_sentinel").autocomplete({
			source: data,
			minLength: 0
			}).focus(function() { $(this).autocomplete("search",$("#ld_sentinel").val()); });	
		  }
	  });

	// Starte Anfrage wenn Submit button angeklickt
	$("#submit-button").click(startJob);
	
	// Event Trigger falls Enter-Key im Feld gedrückt wird zum starten des Jobs
	$("#ld_sentinel").keypress(function(event) { if (event.which == 13) { $("#submit-button").click(); } });
	$("#gene_symbol").keypress(function(event) { if (event.which == 13) { $("#submit-button").click(); } });
	$("#snps_region_begin").keypress(function(event) { if (event.which == 13) { $("#submit-button").click(); } });
	$("#snps_region_end").keypress(function(event) { if (event.which == 13) { $("#submit-button").click(); } });
});



function showResults(randid) {
	// Lade Block-Annotation
	$.ajax({ type: "GET", url: 'tmpdata/'+randid+'/block_anno.html', async: false, dataType: "text", success: function(data){ $("#annotationcontainer").html(data);  } });
	
	if ($("input[name=incl_funcann]:checked").val() == 1) {
		
		$('#tabs-header').append('<li class="plots-tabs-tab"><a href="#tabs-snpinfo-table">Variant list</a></li>');
		$('#tabs-body').append('<div id="tabs-snpinfo-table" class="plots-tabs-body-content" style="overflow: auto;"><div id="table-coltoggle-header"></div><table id="proxy-results-table"><tr><td></td></tr></table></div>');
		$('#tabs-header').append('<li class="plots-tabs-tab"><a href="#tabs-snpinfo">Variant annotations</a></li>');
		$('#tabs-body').append('<div id="tabs-snpinfo" class="plots-tabs-body-content"><span id="nosnpinfo">There are no variant annotations that could be displayed. Click on the arrow (<img src="frontend/img/common_link_annotation.png" alt="" />) next to a variant in the "Variant list". Detailed annotations will then be displayed in this section.</span><div id="snpinfo-accordion"></div></div>');
		$('#tabs-header').append('<li class="plots-tabs-tab"><a href="#tabs-report">Report</a></li>');
		$('#tabs-body').append('<div id="tabs-report" class="plots-tabs-body-content"></div>');
		
		$("#snpinfo-accordion").accordion({collapsible: true, heightStyle: "content", event: "click"});
		
		$( "#tabs" ).tabs("refresh");
		$( "#tabs" ).tabs({active: 0});
		
		// initialisiere Status-Variable für default-action bei click auf SNP
		snpclickdefault = 0;
	
		
		// Report einfuegen
		$.ajax({url: 'tmpdata/'+randid+'/report.txt', dataType: "json"})
		.done(function(report) { 
			var reporthtml = "";
			
			reporthtml += "Your data was mapped and annotated according to the &quot;" + report['userinput']['genomerelease']+ "/" + report['userinput']['referenceset'] + "&quot; release, &quot;"+report['userinput']['population']+"&quot; population. Functional annotations are based on &quot;"+report['userinput']['annotation']+"&quot;. </p>";
			reporthtml += "Your input type was &quot;" + report['userinput']['inputtype'] + "&quot;.</p>";
			
			if (report['jobinfo']['dldescription'] != "") {
				reporthtml += "<p>You can download the results as a comma-separated text file here:";
				reporthtml += "<ul><li><a href='"+report['jobinfo']['dldescription']+"' style='color: rgb(228,0,58);' target='_blank'>column header description and job information</a></li>";
				reporthtml += "<li><a href='"+report['jobinfo']['dlcsv']+"' style='color: rgb(228,0,58);' target='_blank'>results file</a> ("+report['jobinfo']['dlcsvsize']+")</li> ";
				reporthtml += "<li><a href='"+report['jobinfo']['dlzip']+"' style='color: rgb(228,0,58);' target='_blank'>ZIPped results file</a> ("+report['jobinfo']['dlzipsize']+")</li></ul></p>";
			}
			
			$('#tabs-report').prepend(reporthtml); 
		});
	
	
		/*
		// Lade SNP-Annotation
		$.ajax({ type: "GET", url: 'tmpdata/'+randid+'/snp_anno.html', async: false, dataType: "text", 
				 success: function(data){ 
					$("#snpinfo-accordion").accordion({collapsible: true, heightStyle: "content", event: "click"});
					$("#nosnpinfo").hide();
					$("#snpinfo-accordion").show();
					$("#snpinfo-accordion").html(data);
					$("#snpinfo-accordion").accordion("refresh");
					$("#snpinfo-accordion").accordion("option","active",0);
					$("#snpinfo-accordion h3 span").click(function() {return false;} );
			} 
		});
		*/
		
		// Tabellarische Darstellung der SNP-Annotationen
		var tableslinecount = 0;
		var tablestoolarge = false;
		$.ajax({ type: "GET", url: 'tmpdata/'+randid+'/proxySearch.count', async: false, dataType: "text", success: function(data){ tableslinecount = data;  } });
		
		if (tableslinecount == 0) {
			$("#proxy-results-table").html("Your query returned no results.");
		} else {
			if (tableslinecount > 25000) { tablestoolarge = true;}
			if (!(tablestoolarge)) { // sortierbare und durchsuchbare Tabelle; wird als ganzes JSON-Array an den Browser übergeben
				$("#proxy-results-table").html("<tr><td>Please stand by while snipa downloads the results table...</td></tr>");
				$.ajax({
					dataType: 'text',
					type: "GET",
					//url: "backend/snipaDatatables.php?id="+randid+"&datatype=complete",
					url: "backend/snipaDatatables.php?id="+randid+"&type=all&content=header",
					success: function(dataStr) {
						var proxydata = eval('('+dataStr+')');
						$("#proxy-results-table").html("");
						proxytable = $("#proxy-results-table").dataTable({
							"bDeferRender": true,
							"bProcessing": true,
							"oLanguage": { "sSearch":"" },
							//"aaData": proxydata.aaData,
							//"aoColumnDefs": proxydata.aoColumnsDefs,
							"aoColumnDefs": proxydata,
							"sAjaxSource": "backend/snipaDatatables.php?id="+randid+"&type=all&content=data",
							"sScrollX": "92%",
							"sScrollY": Math.min(500,tableslinecount*15+120)+"px",
							"sDom": '<<"#table-coltoggle">f><>rtiS'
							//"sDom": '<f><>rtiS'
						});
												
						// Fixiere die ersten beiden Spalten
						//new $.fn.dataTable.FixedColumns(proxytable, {"iLeftColumns": 2, "iLeftWidth": 700, "sHeightMatch": "none" });
						
						// Links zum Ein-/Ausblenden von Spalten
						// Links zum Ein-/Ausblenden von Spalten
						$("#table-coltoggle").append("");
						for (var i=0; i < proxydata.length; i++) {
							colvisibility = ""; if (proxydata[i]['bVisible'] == false) {colvisibility = " table-coltoggle-disabled"; }  
							$("#table-coltoggle").append("<span id='proxy-results-table-coltoggle-link"+i+"' onclick='fnShowHide("+i+");' class='table-coltoggle-enabled"+colvisibility+"'>"+proxydata[i]['sTitle'].replace(/\ /g,"&nbsp;")+"</span> ");
						}
																						
						$("#table-coltoggle-header").html('<span>Show or hide columns:</span><span style="float: right; width: 167px;">Filter columns:</span>');
						
						// Nasty Fix fuer Header-breitenberechnung, die nicht korrekt funktioniert, wenn div bei table draw hidden
						$("#tabs").on("tabsactivate", function(event,ui) { if (ui.newPanel.attr('id') == 'tabs-snpinfo-table') { proxytable.fnAdjustColumnSizing(); } });
					}
				});
			} else { // Tabelle, deren Inhalt Seitenweise vom Server geliefert wird. Nicht sortier- und durchsuchbar.
				// Hinweis falls Tabelle zu groß für sortierbare Version
				if (tablestoolarge) { $("#proxy-tabs-results").prepend("<span style='display: block; background-color: rgb(245,245,245); '>Note: sorting and filtering of columns is disabled since the resulting table exceeds 25,000 lines.</span><br />"); }
				$.ajax({
					dataType: 'text',
					type: "GET",
					url: "backend/snipaDatatables.php?id="+randid+"&type=pages&content=header",
					success: function(dataStr) {
						var proxydata = eval('('+dataStr+')');
						proxytable = $("#proxy-results-table").dataTable({
							"bServerSide": true,
							"bProcessing": true,
							"bSort": false,
							"sScrollX": "92%",
							"oLanguage": { "sSearch":"", "sInfo":"Showing _START_ to _END_ of _TOTAL_ sentinel SNP(s)." },
							"aoColumnDefs": proxydata,
							"sAjaxSource": "backend/snipaDatatables.php?id="+randid+"&type=pages&content=data",
							"sDom": '<<"#table-coltoggle">ip><>rt<"bottom"ip>'
							//"sDom": '<ip><>rt<"bottom"ip>'
						});
												
						// Fixiere die ersten beiden Spalten
						//new $.fn.dataTable.FixedColumns(proxytable, {"iLeftColumns": 2, "iLeftWidth": 700, "sHeightMatch": "none" });
						
						// Links zum Ein-/Ausblenden von Spalten
						$("#table-coltoggle").css('width','100%');
						$("#table-coltoggle").append("");
						
						for (var i=0; i < proxydata.length; i++) {
							colvisibility = ""; if (proxydata[i]['bVisible'] == false) {colvisibility = " table-coltoggle-disabled"; }  
							$("#table-coltoggle").append("<span id='proxy-results-table-coltoggle-link"+i+"' onclick='fnShowHide("+i+");' class='table-coltoggle-enabled"+colvisibility+"'>"+proxydata[i]['sTitle'].replace(/\ /g,"&nbsp;")+"</span> ");
						}
						
						$("#table-coltoggle-header").html('<span>Show or hide columns:</span>');
						
						// Nasty Fix fuer Header-breitenberechnung, die nicht korrekt funktioniert, wenn div bei table draw hidden
						$("#tabs").on("tabsactivate", function(event,ui) { if (ui.newPanel.attr('id') == 'tabs-snpinfo-table') { proxytable.fnAdjustColumnSizing(); } });
					}
				});
				
			}
			
		}
		
		
	}
		
	/* ENDE Funktionen nach erfolgreichem Berechnen der Input-Daten */
	$('#progress-dialog').dialog("close");
	$('#message').hide();
	$('#progressbar').hide();
	$('#form-container').hide();
	
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
	
	$('#tabs').show();
}



function startJob(){
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
			
				setTimeout("updateStatus("+randid+")",500);

				
				$.ajax(
				{
				  url : "backend/snipaBlockAnnotation.php",
				  type: "POST",
				  data: {	id: randid, 
							genomerelease: $("select#dataset-genomerelease").val(), 
							referenceset: $("select#dataset-referenceset").val(), 
							population: $("select#dataset-population").val(), 
							annotation: $("select#dataset-annotation").val(), 
							snps_input_type: $("input[name=selection_snps]:checked").val(), 
							snps_variants: $("textarea#snps_variants_input").val(),
							snps_ld_sentinel: $("input#ld_sentinel").val(),
							snps_gene: $("input#gene_symbol").val(), 
							snps_region_chr: $("select#snps_region_chr").val(),
							snps_region_begin: $("input#snps_region_begin").val(),
							snps_region_end: $("input#snps_region_end").val(),
							rsquare: $("select#ld_threshold").val(),
							incl_funcann: $("input[name=incl_funcann]:checked").val() 
						},
				  dataType:"json",
				  
				  beforeSend: function() {},
					
				  success: function(phpresults) {
								updateStatus(randid);
								if (phpresults['ok'] !== "FAIL") { saveSelectDatasets(); showResults(randid); } else { $('#progress-dialog').dialog({ buttons: [ { text: "OK", click: function() { $(this).dialog("close"); } } ] });}
							}
				});
			}
		}
	});
}



// Ein- und Ausblenden von Spalten bei dynamischen Tabellen
function fnShowHide(iCol) {
	var bVis = proxytable.fnSettings().aoColumns[iCol].bVisible;
	proxytable.fnSetColumnVis( iCol, bVis ? false : true);
	$("#proxy-results-table-coltoggle-link"+iCol).toggleClass("table-coltoggle-disabled", bVis);
}



// Liefert zu einem Tabiid den entsprechenden Index
function getIndexForId(tabsDivId, searchedId)
{
    var index = -1;
    var i = 0, els = $(tabsDivId).find("a");
    var l = els.length, e;
    while ( i < l && index == -1 )
    {
        e = els[i];
        if (searchedId == $(e).attr('href') )
        { index = i; }
        i++;
    };
    return index;
}

// Aktualisiert den Fortschrittsbalken
function updateStatus(id){
  $.ajax({ dataType: "json",
           url: 'tmpdata/'+id+'/status.txt', 
		   cache: false, 
		   error: function(a,textstatus,c) { if (textstatus == "parsererror") { setTimeout("updateStatus("+id+")", 150);} },
		   success: function(data){
			   pbvalue = 0;

			   if(data){
					var total = data['totalstepnum'];
					var current = data['stepnum'];
					var message = data['message'];
					var ok = data['ok'];
					var errmessage = data['errmessage'];
					var pbvalue = Math.floor((current / total) * 100);
					if (pbvalue>0){
						$("#progressbar").progressbar({
							value: pbvalue
						});
						$(".progress-label").text(pbvalue+" %");
					}
					$("#message").html(message+"..");
				}
				if ((pbvalue < 100) && (ok !== "FAIL")) {
				   setTimeout("updateStatus("+id+")", 500);
				}
				if (ok == "FAIL") {
					$("#message").html(message+".. <span style='font-weight: bold; color: rgb(180,0,0);'>FAILED</span>.<br />"+errmessage);
				}
			}
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
					annopanel += "<span onclick=\"$(this).parent('h3').next('div').remove(); $(this).parent('h3').hide().remove(); $('#snpinfo-accordion').accordion('refresh'); if ($('#snpinfo-accordion h3').length < 1) { $('#nosnpinfo').show(); resultstab = getIndexForId('#tabs-header','#tabs-snpinfo-table'); $('#tabs').tabs({active: resultstab}); } \" class=\"pinkspan\">delete</span>";
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





