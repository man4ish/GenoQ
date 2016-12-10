function validateForm() {
	          $('#progressbar1').hide();
	          
              var x = document.forms["myForm"]["search"].value;
              if (x == null || x == "") {
                  alert("Chr/Rsid/Gene Name must be filled out");
                  return false;
              }
              
              var val = x.split(":");  
              var chr = val[0];
              var range = val[1];
              var minmaxrange = range.split("-");
              var start = minmaxrange[0];
              var stop = minmaxrange[1];
              if(isNaN(chr)) {
					if (chr =='X' || chr == 'Y') {
					
			        } else {
						alert("Pls enter valid value of chromosome\n");
					    return false;
					}
			  } else if((chr < 1 || chr > 22) ) {
						alert("Pls enter valid value of chromosome\n");
						return false;
			  } 
			  
			  if(chr == 1 && stop > 249250621) {
				  alert("Max range value for chr1 is 249250621!\n");
                  return false;
			  } else if(chr == 2 && stop > 243199373) {
				  alert("Max range value for chr2 is 243199373!\n");
                  return false;
			  } else if(chr == 3 && stop > 198022430) {
				  alert("Max range value for chr3 is 198022430!\n");
                  return false;
			  } else if(chr == 4 && stop > 191154276) {
				  alert("Max range value for chr4 is 191154276!\n");
                  return false;
			  } else if(chr == 5 && stop > 180915260	) {
				  alert("Max range value for chr5 is 180915260!\n");
                  return false;
			  } else if(chr == 6 && stop > 171115067) {
				  alert("Max range value for chr6 is 171115067!\n");
                  return false;
			  } else if(chr == 7 && stop > 159138663) {
				  alert("Max range value for chr7 is 159138663!\n");
                  return false;
			  } else if(chr == 8 && stop > 146364022) {
				  alert("Max range value for chr8 is 146364022!\n");
                  return false;
			  } else if(chr == 9 && stop > 141213431	) {
				  alert("Max range value for chr9 is 141213431!\n");
                  return false;
			  } else if(chr == 10 && stop > 135534747) {
				  alert("Max range value for chr10 is 135534747!\n");
                  return false;
			  } else if(chr == 11 && stop > 135006516) {
				  alert("Max range value for chr11 is 135006516!\n");
                  return false;
			  } else if(chr == 12 && stop > 133851895) {
				  alert("Max range value for chr12 is 133851895!\n");
                  return false;
			  } else if(chr == 13 && stop > 115169878) {
				  alert("Max range value for chr13 is 115169878!\n");
                  return false;
			  } else if(chr == 14 && stop > 107349540) {
				  alert("Max range value for chr14 is 107349540!\n");
                  return false;
			  } else if(chr == 15 && stop > 102531392) {
				  alert("Max range value for chr15 is 102531392!\n");
                  return false;
			  } else if(chr == 16 && stop > 90354753) {
				  alert("Max range value for chr16 is 90354753!\n");
                  return false;
			  } else if(chr == 17 && stop > 81195210) {
				  alert("Max range value for chr17 is 81195210!\n");
                  return false;
			  } else if(chr == 18 && stop > 78077248) {
				  alert("Max range value for chr18 is 78077248!\n");
                  return false;
			  } else if(chr == 19 && stop > 59128983) {
				  alert("Max range value for chr19 is 59128983!\n");
                  return false;
			  } else if(chr == 20 && stop > 63025520) {
				  alert("Max range value for chr20 is 63025520!\n");
                  return false;
			  } else if(chr == 21 && stop > 48129895) {
				  alert("Max range value for chr21 is 48129895!\n");
                  return false;
			  } else if(chr == 22 && stop > 51304566) {
				  alert("Max range value for chr22 is 51304566!\n");
                  return false;
			  } else if(chr == "X" && stop > 155270560) {
				  alert("Max range value for chrX is 155270560!\n");
                  return false;
			  } else if(chr == "Y" && stop > 59373566) {
				  alert("Max range value for chrY is 59373566!\n");
                  return false;
			  } 
     }
