<?php
function frontendHTMLHeader($title = "IBIS Metabolomics", $additional = "",$ldflag="") {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

		
		<title>
			<?php echo($title); ?>
		</title>

		
		<link rel="stylesheet" href="frontend/css/jquery-ui-1.10.3.custom.css" />
		<link rel="stylesheet" href="frontend/css/jquery-ui-1.10.3.custom.snipa-additions.css" />
		<script type="text/javascript" src="frontend/js/jquery.js"></script>
		<script type="text/javascript" src="frontend/js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="frontend/js/modernizr-2.8.1/modernizr.js"></script>
		<script type="text/javascript" src="frontend/js/chained-0.9.10/jquery.chained.min.js"></script>
		<script type="text/javascript" src="frontend/js/snipa_common.js"></script>
                <?php
                    if($ldflag=="1") {
                ?>	        
	            <script type="text/javascript" src="frontend/js/snipa_commonld.js"></script>
                <?php }else{ ?>
                    <script type="text/javascript" src="frontend/js/snipa_common.js"></script>
                <?php } ?>
		<?php 
                  echo($additional); 
                ?>
		
		<link rel="stylesheet" type="text/css" href="frontend/css/style.css" media="screen" />
	</head>
	<body>
		<div id="container-all">
<?php	
	}	
?>
