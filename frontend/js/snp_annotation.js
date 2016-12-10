$(document).ready(function(){
	
	// Blendet Hilfetext aus
	$("#helptext-close").click(function() { $("#helptext").hide(); });
	
	// Initialisiere Datensatz-Felder
	loadSelectDatasets();
	
		
	// Button für SNP pastebin
	if ($("#snpbinbox").is(":visible")) {
		$("#snps_sentinels_input").after("<button id='snps_sentinels_from_pastebin'>Paste from SNiPA's clipboard</button>");
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
	$( "#tabs" ).tabs().hide();
	
	// Starte Anfrage wenn Submit button angeklickt
	$("#submit-button").click(startJob);
	
});


function showResults(randid) {
	/* START Funktionen nach erfolgreichem Berechnen der Input-Daten */
	
	$.ajax({url: 'tmpdata/'+randid+'/report.txt', dataType: "json"})
		.done(function(report) { 
			var reporthtml = "";
			
			reporthtml += "<p>Your job has the ID " + report['jobinfo']['jobid'] +  ". ";
			reporthtml += "Processing this job took approximately " + report['jobinfo']['runtime'] + " seconds.</p>";
			reporthtml += "Your data was mapped and annotated according to the &quot;" + report['userinput']['genomerelease'] + "/" + report['userinput']['referenceset'] + "&quot; release, &quot;"+report['userinput']['population']+"&quot; population. Functional annotations are based on &quot;"+report['userinput']['annotation']+"&quot;. </p>";
			reporthtml += "<p>For "+report['userinput']['notmapped']+" SNP(s), no chromosomal location could be found in the selected reference panel or population"+report['userinput']['notmappedsnps']+".</p>";
			$('#tabs-report').prepend(reporthtml); 
		});
		
	// Lade SNP-Annotation
	$.ajax({ type: "GET", url: 'tmpdata/'+randid+'/snp_anno.html', async: false, dataType: "text", 
			 success: function(data){ 
				$("#snpinfo-accordion").accordion({collapsible: true, heightStyle: "content", event: "click"});
				$("#nosnpinfo").hide();
				$("#snpinfo-accordion").show();
				$("#snpinfo-accordion").html(data);
				$("#snpinfo-accordion").accordion("refresh");
				$("#snpinfo-accordion").accordion("option","active",0);
				$("#snpinfo-accordion h3 span.pinkspan").click(function() {return false;} );
		} 
	});
	
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
	

	/* ENDE Funktionen nach erfolgreichem Berechnen der Input-Daten */
	$('#tabs').show();
}


function startJob() {
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
			  url : "backend/snipaSNPAnnotation.php",
			  type: "POST",
			  data: {	id: randid, 
						genomerelease: $("select#dataset-genomerelease").val(), 
						referenceset: $("select#dataset-referenceset").val(), 
						population: $("select#dataset-population").val(), 
						annotation: $("select#dataset-annotation").val(),  
						snps_sentinels: $("textarea#snps_sentinels_input").val()
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


