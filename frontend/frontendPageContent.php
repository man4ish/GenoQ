
<?php
	function frontendPageContentStart($header = "") { ?>
		<div id="container-main">
				<div class="box">
					<div class="header">
						<strong><?php echo($header); ?></strong>
					</div>
					<div class="content">
						<div class="page" id="page">
<?php	}
?>

<?php
	function frontendPageContentEnd() { ?>
						</div>
					</div>
				</div>
			</div>
		<div class="cleardiv"></div>
<?php	}
?>

<?php
	function frontendPageContentTitle($title = "",$helptext = "") {
		echo("<h1>$title</h1>");
		echo("<div class=\"helptext\">$helptext</div>");
	}
?>
