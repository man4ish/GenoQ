$(document).ready(function(){
	
	// Blendet Hilfetext aus
	$("#helptext-close").click(function() { $("#helptext").hide(); });
	
	// Initialisiere select-Boxen fuer Datensaetze
	$("#dataset-genomerelease").load("backend/snipaDatasets.php?action=get&type=genomerelease", function() { 
		$("#dataset-referenceset").load("backend/snipaDatasets.php?action=get&type=referenceset", function() {
			$("#dataset-population").load("backend/snipaDatasets.php?action=get&type=population", function() {
				$("#dataset-annotation").load("backend/snipaDatasets.php?action=get&type=annotation", function() {
					$("#dataset-referenceset").chained("#dataset-genomerelease");
					$("#dataset-population").chained("#dataset-referenceset");
					$("#dataset-annotation").chained("#dataset-genomerelease");
				});
			});
		});
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
	
	// Buttons fuer input type (snps, gene, region)
	$("#selection_snps_container").buttonset();
	$("#snps_sentinels").show(); $("#snps_gene").hide(); $("#snps_region").hide();

	// Event handler wenn input type geaendert wird
	$("input[name='selection_snps']").change(function(){
		if (this.id == "selection_snps_sentinels") { $("#snps_sentinels").show(); $("#snps_gene").hide(); $("#snps_region").hide(); }
		if (this.id == "selection_snps_gene") { $("#snps_sentinels").hide(); $("#snps_gene").show(); $("#snps_region").hide(); }
		if (this.id == "selection_snps_region") { $("#snps_sentinels").hide(); $("#snps_gene").hide(); $("#snps_region").show(); }
	});
	
	
	// Buttons fuer include Sentinels
	$("#incl_sentinel_container").buttonset();
	
	// Buttons fuer Functionale Annotation
	$("#incl_funcann_container").buttonset();
		
	// Buttons fuer dynamic tables
	$("#dynamic_tables_container").buttonset();
	
	// Buttons fuer download als zip-tsv.
	$("#enable_download_container").buttonset();
		
	// Intialisiere Variable fuer Annorelease
	var annorelease = $("#annorel option:selected").val();
	
	// Gennamen Autocomplete
	$("#gene_symbol").autocomplete({source: 'backend/snipaGeneSynonyms.php?format=json&annorelease='+annorelease, minLength: 2});
	
	// Update der annorelease Variable wenn anderes Release gewählt sowie der Autocomplete-Funktion fuer Gennamen
	$("#annorel").change(function() {
		annorelease = $("#annorel option:selected").val();
		$("#gene_symbol").autocomplete({source: 'backend/snipaGeneSynonyms.php?format=json&annorelease='+annorelease, minLength:2});
	});
		
	// Button für SNP pastebin
	if ($("#snpbinbox").is(":visible")) {
		$("#snps_sentinels_input").after("<button id='snps_sentinels_from_pastebin'>Paste from SNP clipboard</button>");
		$("#snps_sentinels_from_pastebin").button().click(function() {
			$.ajax({
				  url: "backend/snipaSnpBin.php",
				  type: "POST",
				  data: { task: "read" },
				  dataType: "json",
				  success: function(data) {	
					$.each(data, function(i,v) {
						$("#snps_sentinels_input").val($("#snps_sentinels_input").val()+v.value+'\n');
					});
				  }
			  });
			}
		);
	}
	
	// Intialisiere Tabs
	$( "#proxy-tabs" ).tabs().hide();
	
	// Starte Anfrage wenn Submit button angeklickt
	$("#submit-button").click(startJob); 
	
	// Event Trigger falls Enter-Key im Feld gedrückt wird zum starten des Jobs
	$("#gene_symbol").keypress(function(event) { if (event.which == 13) { $("#submit-button").click(); } });
	$("#snps_region_begin").keypress(function(event) { if (event.which == 13) { $("#submit-button").click(); } });
	$("#snps_region_end").keypress(function(event) { if (event.which == 13) { $("#submit-button").click(); } });
	
});

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
				  url : "backend/snipaProxySearch.php",
				  type: "POST",
				  data: {	id: randid, 
							rel: $("select#rel").val(), 
							annorel: $("select#annorel").val(), 
							pop: $("select#pop").val(), 
							snps_input_type: $("input[name=selection_snps]:checked").val(), 
							snps_sentinels: $("textarea#snps_sentinels_input").val(),
							snps_gene: $("input#gene_symbol").val(), 
							snps_region_chr: $("select#snps_region_chr").val(),
							snps_region_begin: $("input#snps_region_begin").val(),
							snps_region_end: $("input#snps_region_end").val(),
							rsquare: $("select#ld_threshold").val(), 
							incl_sentinel: $("input[name=incl_sentinel]:checked").val(), 
							incl_funcann: $("input[name=incl_funcann]:checked").val(), 
							download: $("input[name=enable_download]:checked").val(), 
							dyn_tables: $("input[name=dynamic_tables]:checked").val(),
							pairwise: $("input#pairwise").val()
						},
				  dataType:"json",
				  
				  beforeSend: function() {},
					
				  success: function(phpresults) {
								updateStatus(randid);
								if (phpresults['ok'] !== "FAIL") { showResults(randid); } else { $('#progress-dialog').dialog({ buttons: [ { text: "OK", click: function() { $(this).dialog("close"); } } ] });}
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
	$('#proxy-form-container').hide();
	$("#proxy-snpinfo-accordion").accordion({collapsible: true, heightStyle: "content", event: "click"});
	/* START Funktionen nach erfolgreichem Berechnen der Input-Daten */
	
	$.ajax({url: 'tmpdata/'+randid+'/report.txt', dataType: "json"})
		.done(function(report) { 
			var reporthtml = "";
			
			reporthtml += "<p>Your job has the ID " + report['jobinfo']['jobid'] +  ". ";
			reporthtml += "Processing this job took approximately " + report['jobinfo']['runtime'] + " seconds.</p>";
			reporthtml += "Your data was mapped and annotated according to the &quot;" + report['userinput']['release'] + "&quot; release, &quot;"+report['userinput']['population']+"&quot; population. Functional annotations are based on &quot;"+report['userinput']['annotation']+"&quot;. </p>";
			reporthtml += "Your input type was &quot;" + report['userinput']['inputtype'] + "&quot;. Details: "+report['userinput']['inputdetails']+"</p>";
			
			if (report['jobinfo']['dldescription'] != "") {
				reporthtml += "<p>You can download the results as a comma-separated text file here:";
				reporthtml += "<ul><li><a href='"+report['jobinfo']['dldescription']+"' style='color: rgb(228,0,58);' target='_blank'>column header description and job information</a></li>";
				reporthtml += "<li><a href='"+report['jobinfo']['dlcsv']+"' style='color: rgb(228,0,58);' target='_blank'>results file</a> ("+report['jobinfo']['dlcsvsize']+")</li> ";
				reporthtml += "<li><a href='"+report['jobinfo']['dlzip']+"' style='color: rgb(228,0,58);' target='_blank'>ZIPped results file</a> ("+report['jobinfo']['dlzipsize']+")</li></ul></p>";
			}
			
			$('#proxy-tabs-report').prepend(reporthtml); 
		});
	
	// Status-Variable für default-action bei click auf SNP
	snpclickdefault = 0;
	

	// Tabellen je nach Anzahl der Zeilen der Ergebinstabelle sortierbar 
	var tablesdynamic = $("input[name=dynamic_tables]:checked").val();
	var tableslinecount = 0;
	var tablestoolarge = false;
	$.ajax({ type: "GET", url: 'tmpdata/'+randid+'/proxySearch.count', async: false, dataType: "text", success: function(data){ tableslinecount = data;  } });
	
	if (tableslinecount == 0) {
		$("#proxy-results-table").html("Your query returned no results.");
	} else {
		if (tableslinecount > 25000) { tablestoolarge = true;}
		if ((tablesdynamic == 1) && !(tablestoolarge)) { // sortierbare und durchsuchbare Tabelle; wird als ganzes JSON-Array an den Browser übergeben
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
					$("#table-coltoggle").append("");
					for (var i=0; i < proxydata.length; i++) {
						$("#table-coltoggle").append("<span id='proxy-results-table-coltoggle-link"+i+"' onclick='fnShowHide("+i+");' class='table-coltoggle-enabled'>"+proxydata[i]['sTitle'].replace(/\ /g,"&nbsp;")+"</span> ");
					}
					
					$("#table-coltoggle-header").html('<span>Show or hide columns:</span><span style="float: right; width: 167px;">Filter columns:</span>');
				}
			});
		} else { // Tabelle, deren Inhalt Seitenweise vom Server geliefert wird. Nicht sortier- und durchsuchbar.
			// Hinweis falls Tabelle zu groß für sortierbare Version
			if ((tablesdynamic == 1) && tablestoolarge) { $("#proxy-tabs-results").prepend("<span style='display: block; background-color: rgb(245,245,245); '>Note: sorting and filtering of columns is disabled since the resulting table exceeds 25,000 lines.</span><br />"); }
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
						$("#table-coltoggle").append("<span id='proxy-results-table-coltoggle-link"+i+"' onclick='fnShowHide("+i+");' class='table-coltoggle-enabled'>"+proxydata[i]['sTitle'].replace(/\ /g,"&nbsp;")+"</span> ");
					}
					
					$("#table-coltoggle-header").html('<span>Show or hide columns:</span>');
				}
			});
			
		}
	
	}
	
	/* ENDE Funktionen nach erfolgreichem Berechnen der Input-Daten */
	$('#proxy-tabs').show();
}


// Ein- und Ausblenden von Spalten bei dynamischen Tabellen
function fnShowHide(iCol) {
	var bVis = proxytable.fnSettings().aoColumns[iCol].bVisible;
	proxytable.fnSetColumnVis( iCol, bVis ? false : true);
	$("#proxy-results-table-coltoggle-link"+iCol).toggleClass("table-coltoggle-disabled", bVis);
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


// Menue fuer SNP-Annotationen im dynamischen Plot
function showPlotAnnotationMenu(eventtype,snpname,snppos,snpchr,sentinelpos,release,population,annorelease) { 
	if (snpclickdefault == 0) {
		$('#proxy-snpdetails-container').show(); 
		$('#proxy-snpdetails-container').dialog({
				title: 'SNP ' + snpname,
				modal: true,
				position: { my: 'left top', of: eventtype, offset: '10 10', collision: 'fit' },
				buttons: [ { text: 'Show annotation', 
							 click: function() { if ($('#proxy-snpdetails-sameaction').is(':checked')) {
													snpclickdefault = 1;
												 }
												 $(this).dialog('destroy'); 
												 $('#proxy-snpdetails-menu').html('');
												 addToSnpAnnotationsTab('#proxy-tabs-snpinfo',snpname,snppos,snpchr,sentinelpos,release,population,annorelease);
											   }
						   } , 
						   { text: 'Copy to clipboard', 
							 click: function() { 
												 if ($('#proxy-snpdetails-sameaction').is(':checked')) {
													snpclickdefault = 2;
												 }
												$('#proxy-snpdetails-menu').html('');
												$(this).dialog('destroy'); 
												addToSnpBin(snpname,snppos,snpchr,release,population,annorelease);
												}
						   } 
						 ]
			});  
		$('#proxy-snpdetails-menu').html('You can either get detailed annotations for this SNP or copy it to the clipboard.<br /><input type=\"checkbox\" id=\"proxy-snpdetails-sameaction\" style=\"width: 10px;\" /> Use this as default action for this session.');  
		} 
		
	if (snpclickdefault == 1) { addToSnpAnnotationsTab('#proxy-tabs-snpinfo',snpname,snppos,snpchr,sentinelpos,release,population,annorelease); }
	if (snpclickdefault == 2) { addToSnpBin(snpname,snppos,snpchr,release,population,annorelease); }
} 



function addToSnpAnnotationsTab(SnpInfoTabId,snpname,snppos,snpchr,sentinelpos,release,population,annorelease) {
	$.ajax(
			{
			  url: "backend/snipaRAPlotsAnnotations.php",
			  type: "GET",
			  data: {	snpname: snpname,
						snppos: snppos,
						snpchr: snpchr,
						sentinelpos: sentinelpos,
						release: release,
						population: population,
						annorelease: annorelease
				},
			  dataType: "text",
			  success: function(annotation) { 
					$("#nosnpinfo").hide();
					$("#proxy-snpinfo-accordion").show();
					var annopanel = "<h3>"+snpname;
					annopanel += "<span onclick=\"$(this).parent('h3').next('div').remove(); $(this).parent('h3').hide().remove(); $('#proxy-snpinfo-accordion').accordion('refresh'); if ($('#proxy-snpinfo-accordion h3').length < 1) { $('#nosnpinfo').show(); resultstab = getIndexForId('#proxy-tabs-header','#proxy-tabs-results'); $('#proxy-tabs').tabs({active: resultstab}); } \" class=\"pinkspan\">delete</span>";
					annopanel += "<span onclick=\"addToSnpBin('"+snpname+"','"+snppos+"','"+snpchr+"','"+release+"','"+population+"','"+annorelease+"'); \" class=\"pinkspan\">add to clipboard</span>";
					annopanel += "</h3>";
					annopanel += "<div>" + annotation + "</div>";
					$("#proxy-snpinfo-accordion").prepend(annopanel);
					if (snpclickdefault != 1) {
						var tabIndex = getIndexForId("#proxy-tabs-header",SnpInfoTabId);
						$("#proxy-tabs").tabs({active: tabIndex});
					}
					$("#proxy-snpinfo-accordion").accordion("refresh");
					$("#proxy-snpinfo-accordion").accordion("option","active",0);
					$("#proxy-snpinfo-accordion h3 span").click(function() {return false;} ); // Verhindert das ein- und ausklappen bei den löschen und snpbin buttons
				}
			}
		);
}




