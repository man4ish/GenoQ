$(document).ready(function(){
	// Not used right now
});

// Menue fuer SNP-Annotationen im dynamischen Plot
function showSnpListMenu(eventtype,snpPosArray,snpchr,genomerelease,referenceset,population,annotation) { 
		var row = $(eventtype.target).closest('tr');
		row.css("background", "#ccc");
		var containerDiv = $(document.createElement('div'));
		containerDiv.className = 'snplist-container';
		var html = '<table style="width: 100%;"><tr><th>Variant</th><th>Position</th></tr>';
		for(var key in snpPosArray){
			html += "<tr><td style='text-align: center;'>"+snpPosArray[key]+"</td><td style='text-align: center;'>chr"+snpchr+":"+key+"</td></tr>";
		}
		html += "</table>";
		containerDiv.html(html);
		$(document.body).append(containerDiv);
		containerDiv.show(); 
		containerDiv.dialog({
				title: 'Variant list',
				modal: true,
				position: { my: 'left top', of: eventtype, offset: '10 10', collision: 'fit' },
				close: function() {
								row.css("background", "none");
							},
				buttons: [ { text: 'Show annotation', 
							 click: function() { 
												 $(this).dialog('destroy'); 
												 $(this).remove();
												 for(var key in snpPosArray){
													addToSnpAnnotationsTab('#tabs-snpinfo',snpPosArray[key],key,snpchr,key,genomerelease,referenceset,population,annotation);
												 }
												 row.css("background", "none");
											   }
						   } ,
						   { text: 'Copy to clipboard', 
							 click: function() { 
													$(this).dialog('destroy'); 
													$(this).remove();
													for(var key in snpPosArray){
														addToSnpBin(snpPosArray[key],key,snpchr,genomerelease,referenceset,population,annotation);
													}
													row.css("background", "none");
												}
						   } 
						 ]
			});  
		
}
