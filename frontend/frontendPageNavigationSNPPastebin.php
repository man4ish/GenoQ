<?php ?>
	<div class="snpbinbox" id="snpbinbox">
		<div class="snpbinheader" id="snpbinheader">Variant clipboard (<span id="snpbincount"><?php print(sizeof($_SESSION['snpbin'])); ?></span>) <span onclick="clearSnpBin();" style="color: rgb(228,0,58); float: right; cursor: pointer;">reset</span></div>
		<div class="snpbincontent">
			<div id="snpbin"><?php foreach ($_SESSION['snpbin'] as $row) { print ("<span style=\"display: block;\" id=\"".$row['randid']."\" onmouseover=\"$('#".$row['randid']." img').show();\" onmouseout=\"$('#".$row['randid']." img').hide();\"><strong>".$row['snpname']."</strong> (".$row['snpchr'].":".$row['snppos'].") <img src=\"frontend/img/snpbin_delete.png\" alt=\"remove this SNP\" style=\"display: none; cursor: pointer;\" onclick=\"removeFromSnpBin(".$row['randid'].");\" /></span>"); }; ?></div>
		</div>
	</div>
	
	<?php
	
	if (sizeof($_SESSION['snpbin']) > 0) { ?>
		<script type="text/javascript">$("#snpbinbox").show()</script>
	<?php } else { ?>
		<script type="text/javascript">$("#snpbinbox").hide()</script>
	<?php } ?>
	
