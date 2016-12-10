$(document).ready(function(){
	
	$("#submit-button").click(function() 
	{
	
	// Get ID for temp directory
	$.ajax({
		dataType: "text",
		async: false,
		url: "backend/ssnapTempdir.php",
		success: 
		function(randid) {
			if (randid.length == 15) 
				{
					$("#progressbar").progressbar({	value: false });
					$(".progress-label").text("");
					$("#message").html("");
					
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
					  url : "backend/ssnapRAPlots.php",
					  type: "POST",
					  data: {	id: randid, 
								rel: $("select#rel").val(), 
								annorel: $("select#annorel").val(), 
								pop: $("select#pop").val(), 
								highcharts: $("select#highcharts").val(), 
								assocs: $("textarea#assocs").val(), 
								sentinel: $("input#sentinel").val() 
							},
					  dataType:"json",
					  
					  beforeSend: 
						function() { 
							
						},
						
					  success: 
						function(phpresults) {
						  updateStatus(randid)
						  if (phpresults['ok'] !== "FAIL") {
							$('#progress-dialog').dialog("close");
							$('#message').hide();
							$('#progressbar').hide();
							$('#raplot-form-container').hide();
							$('#plots').html('<a href="tmpdata/'+randid+'/raplot_plot_dynamic.html"><img src="tmpdata/'+randid+'/raplot_plot_static.png" /></a>')
						  } else {
							$('#progress-dialog').dialog({ buttons: 
															[ { text: "OK", 
																click: function() { 
																	$(this).dialog("close");
																	
																} 
															} ] 
														  });
						  }
						}
					});
				
				
				
				}
			}	
		});
	
	});
});



function updateStatus(id){
  $.ajax({ dataType: "json",
           url: 'tmpdata/'+id+'/status.txt', 
		   cache: false, 
		   error: function(a,textstatus,c) { console.log(textstatus); if (textstatus == "parsererror") { setTimeout("updateStatus("+id+")", 150);} },
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
							value: pbvalue,
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

