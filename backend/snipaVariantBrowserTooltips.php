<?php

$type = $_GET['type'];
$element = $_GET['element'];
$name = $_GET['name'];
$jobid = preg_replace( '/[^0-9]/', '', $_GET['jobid']);

$jobpath = "/home/metabolomics/snipa/web/tmpdata/".$jobid."/";


if ($type == "annotation") {
	if ($element == "gene") {
		$anno = unserialize(file_get_contents($jobpath."/plot_genelist_annotations_phparray.txt"));
		print($anno[$name]['ANNOTATION']);
	}
	else if ($element == "regelement") {
		$anno = unserialize(file_get_contents($jobpath."/plot_regulatorylist_annotations_phparray.txt"));
		print($anno[$name]['ANNOTATION']);
	}
	else if ($element == "snp") {
		$anno = unserialize(file_get_contents($jobpath."/plot_snps_annotations_phparray.txt"));
		$tmpannobasic = $anno[$name]['BASICANNOTATION'];
		$tmpannoadvanced = $anno[$name]['ANNOTATION'];
		if ($tmpannobasic != "NA") { print($tmpannobasic); }
		if ($tmpannoadvanced != "NA") { print($tmpannoadvanced); }
	}
}
else if ($type == "link" ){	
	if ($element == "snp") {
		print("http://google.de");
	} 
	else if ($element == "gene") {
		$anno = unserialize(file_get_contents($jobpath."/plot_genelist_annotations_phparray.txt"));
		print($anno[$name]['LINK']);
	}
	else if ($element == "regelement") {
		$anno = unserialize(file_get_contents($jobpath."/plot_regulatorylist_annotations_phparray.txt"));
		print($anno[$name]['LINK']);
	}
}


?>