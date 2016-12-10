<?php

$id = time()*100000+mt_rand(0,99999);
$tmpdatadir = "/home/metabolomics/snipa/web/tmpdata/".$id."/";

if (mkdir($tmpdatadir)) {
	$status = array();
	$status['stepnum'] = 1;
	$status['totalstepnum'] = 100;
	$status['message'] = "";
	$status['errmessage'] = "";
	$status['ok'] = "";
	
	$statfilefh = fopen($tmpdatadir."/status.txt.1",'w');
	fwrite($statfilefh, utf8_encode(json_encode($status)));
	fclose($statfilefh);
	copy($tmpdatadir."/status.txt.1",$tmpdatadir."/status.txt");
	print($id);
}

