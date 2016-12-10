// SNP-Bin leeren
function clearSnpBin() {
	$.ajax(
			{
			  url: "backend/snipaSnpBin.php",
			  type: "POST",
			  data: { task: "clear"
					},
			  dataType: "text",
			  success: function() { 
					$("#snpbinbox").hide("blind", {direction: "up"},400); 
					$("#snpbin").html(''); 
					$("#snpbincount").html('0');
				}
			}
		);
}

// SNP zum SNP-Bin hinzufügen
function addToSnpBin(snpname,snppos,snpchr,genomerelease,referenceset,population,annotation) {
	var randnum = Math.floor(Math.random()*1000000000);
	$.ajax(
			{
			  url: "backend/snipaSnpBin.php",
			  type: "POST",
			  data: {	snpname: snpname,
						snppos: snppos,
						snpchr: snpchr,
						genomerelease: genomerelease,
						referenceset: referenceset,
						population: population,
						annotation: annotation,
						task: "add",
						randid: randnum
					},
			  dataType: "text",
			  success: function(data) { 
						$("#snpbinbox").show();
						if (data == "snpexists") {
							$("#snpbin").prepend('<span id="alreadyexists" style=\'display: block; color: red; \'><strong>' + snpname + '</strong> is already in the clipboard!</span>');
							$("#alreadyexists").hide({effect: "highlight", duration: 2000, complete: function() { $(this).remove(); } });
						} else {
							$("#snpbin").prepend('<span id=\''+ randnum +'\' style=\'display: block;\' onmouseover=\'$("#'+randnum+' img").show();\' onmouseout=\'$("#'+randnum+' img").hide();\'><strong>' + snpname + '</strong> (' + snpchr + ':'+ snppos + ') <img src=\'frontend/img/snpbin_delete.png\' alt=\'remove this SNP\' style=\'display: none; cursor: pointer;\' onclick=\'removeFromSnpBin('+randnum+');\' /></span>'); 
							var snpcnt = $("#snpbin span").length;
						}
						$("#snpbincount").html(snpcnt);
						for (var i=0;i<1;i++) {
							$("#snpbinheader").animate({backgroundColor: "#E4003A"},50);
							$("#snpbinheader").animate({backgroundColor: "#333333"},450);
						}
				}
			}
		);
}

// SNP vom SNP-Bin entfernen
function removeFromSnpBin(randid) {
	$.ajax(
			{
			  url: "backend/snipaSnpBin.php",
			  type: "POST",
			  data: {	task: "remove",
						randid: randid
					},
			  dataType: "text",
			  success: function(data) { 
							$("#"+randid+"").remove();
							snpcnt = $("#snpbin span").length;
							$("#snpbincount").html(snpcnt);
							if (snpcnt < 1) {
								$("#snpbinbox").hide("blind", {direction: "up"},400); 
								$("#snpbin").html(''); 
								$("#snpbincount").html('0');
							}
						}
			}
		);
}

// Liefert zu einem Tabiid den entsprechenden Index
function getIndexForId(tabsDivId, searchedId) {
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

// Basenpaarzahl in "human readable"
function bpToHR(bps) {
  var sizes = ['b', 'kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];
  if (bps == 0) return 'n/a';
  var i = parseInt(Math.floor(Math.log(bps) / Math.log(1000)));
  if (i == 0) { return (bps / Math.pow(1000, i)) + ' ' + sizes[i]; }
  return (bps / Math.pow(1000, i)).toFixed(0) + ' ' + sizes[i];
}

// Initialisiere Gennamen Autocomplete
function iniGenesAutocomplete() { 
	//autocompgenomerelease = $("select#dataset-genomerelease option:selected").val();
	autocompgenomerelease = $("option:selected","select#dataset-genomerelease").val(); // Workaround für IE8
	//autocompannotation = $("select#dataset-annotation option:selected").val();
	autocompannotation = $("option:selected","select#dataset-annotation").val(); 
	$("#gene_symbol").autocomplete({source: 'backend/snipaGeneSynonyms.php?format=json&genomerelease='+autocompgenomerelease+'&annotation='+autocompannotation, minLength: 2}); 
}

// Initialisiere Select-Felder für Genomerelease, Referenzset, Population und Annotation
function loadSelectDatasets(optGenesAutocomplete) {
	$("#dataset-genomerelease").load("backend/snipaDatasetsld.php?action=get&type=genomerelease", function() { 
		$("#dataset-referenceset").load("backend/snipaDatasetsld.php?action=get&type=referenceset", function() {
			$("#dataset-population").load("backend/snipaDatasetsld.php?action=get&type=population", function() {
				$("#dataset-annotation").load("backend/snipaDatasetsld.php?action=get&type=annotation", function() {
					//var test1 = $("#dataset-referenceset").html();
					//alert(test1);
					//$("#dataset-referenceset").html(test1);
					$("#dataset-referenceset").chained("#dataset-genomerelease");
					$("#dataset-population").chained("#dataset-referenceset");
					$("#dataset-annotation").chained("#dataset-genomerelease");
					if (optGenesAutocomplete == "genesautocomplete") {
						iniGenesAutocomplete();
					}
				});
			});
		});
	});
}

// Schreibe Werte für Genomerelease, Referenzset, Population und Annotation in PHP-Session
function saveSelectDatasets(setgenrel,setrefset,setpop,setanno) {
	$.ajax(
			{
			  url: "backend/snipaDatasetsld.php",
			  type: "GET",
			  data: { action: "set",
			          genomerelease: $("select#dataset-genomerelease").val(),
					  referenceset: $("select#dataset-referenceset").val(),
					  population: $("select#dataset-population").val(),
					  annotation: $("select#dataset-annotation").val()
					},
			  dataType: "text"
			}
		);
}

// Daten für Tooltips nachladen
jQuery.extend(
   {
	  getAnnotations: function(url) 
	  {
		  var result = null;
		  $.ajax(
		  {
			url: url,
			type: 'get',
			dataType: 'text',
			async: false,
			cache: false,
			timeout: 3000,
			success: function(data) { result = data; },
			error: function(x,t,m) { if (t==='timeout') { result = 'Annotations could not be downloaded<br />due to a connection timeout.<br />Please try again later.' } else { result = 'Annotations could not be downloaded<br />since the server seems to be offline.<br />Please try again later.'  } }
		  });
	  return result;
	  }
   }
);


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

// SNPCard als PDF Export
function printCard(genomerelease, referenceset, population, annotation, snp){
	var url = "backend/snipaPdfExporter?snp="+snp+"&genomerelease="+genomerelease+"&population="+population+"&annotation="+annotation+"&referenceset="+referenceset+"";
	location.href=url;
}

