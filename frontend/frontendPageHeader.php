<?php

function frontendPageHeader($imglink = "#",$imgdesc = "") {
?>

<div id="container-banner">
	<div style="float: left; margin: 0px; height: 80px; width: 450px;">
        <a href="<?php echo($imglink); ?>"><img src="frontend/img/logo_genoq.png" style="float: left; height: 80px;" alt="<?php echo($imgdesc); ?>" id="logo_snipa" /></a>
	<a href="http://qatar-weill.cornell.edu/research/faculty/suhreLab.html"><img src="frontend/img/logo_hmgu.png" alt="HelmholtzZentrum munich" style="height: 80px; float: right;" id="logo_hmgu" /></a>	

	</div>
	<div style="float: right; margin: 0px; height: 80px; width: 420px;">
    <a href="http://www.helmholtz-muenchen.de/"><img src="frontend/img/logo_wcmcq.png" alt="WCMC" style="height: 80px; float: left;" id="logo_wcmcq" /></a>				
	<a href="<?php echo($imglink); ?>"><img src="frontend/img/logo_genoq_arabic.png" style="float: right; height: 80px;" alt="<?php echo($imgdesc); ?>" id="logo_snipa_arabic" /></a>
	</div>
	<!-- Replace PNGs with SVGs in header if Browser is capable -->
	<script>
		if (Modernizr.svgasimg == true) { 
                        //$('#logo_snipa').attr('src','frontend/img/logo_wcmc.svg');
			//$('#logo_snipa').attr('src','frontend/img/logo.svg');
			//$('#logo_snipa_arabic').attr('src','frontend/img/logo_genoq_arabic.svg');
			$('#logo_wcmcq').attr('src','frontend/img/logo_hmgu.svg');
			$('#logo_hmgu').attr('src','frontend/img/logo_wcmcq.svg');
		}
	</script>	
</div>
	
<?php	
	}	
?>
