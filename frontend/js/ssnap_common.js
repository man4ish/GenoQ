// SNP-Bin leeren
function clearSnpBin() {
	$.ajax(
			{
			  url: "backend/ssnapSnpBin.php",
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
function addToSnpBin(snpname,snppos,snpchr,release,population,annorelease) {
	var randnum = Math.floor(Math.random()*1000000000);
	$.ajax(
			{
			  url: "backend/ssnapSnpBin.php",
			  type: "POST",
			  data: {	snpname: snpname,
						snppos: snppos,
						snpchr: snpchr,
						release: release,
						population: population,
						annorelease: annorelease,
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
			  url: "backend/ssnapSnpBin.php",
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
