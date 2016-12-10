<?php

require_once("../backend/snipaMaprsid.php");
require_once("../backend/snipaMapGenes.php");
require_once("../backend/snipaTabix.php");
require_once("../backend/snipaConfig.php");

require_once("/home/metabolomics/snipa/tools/dompdf/dompdf/dompdf_config.inc.php");


$Genomerelease = $_POST['genomerelease'];
$Referenceset = $_POST['referenceset'];
$Population = $_POST['population'];
$Annotation = $_POST['annotation'];
$SnpsSentinels = $_POST['snps_sentinels'];

$starttime = time(); 
$allok = TRUE;

function array_map_recursive($callback, $array) {
	foreach ($array as $key => $value) {
		if (is_array($array[$key])) {
			$array[$key] = array_map_recursive($callback, $array[$key]);
		}
		else {
			$array[$key] = call_user_func($callback, $array[$key]);
		}
	}
	return $array;
}


function sanitize_output($buffer)
{
    $search = array(
        '/\>[^\S ]+/s', //strip whitespaces after tags, except space
        '/[^\S ]+\</s', //strip whitespaces before tags, except space
        '/(\s)+/s'  // shorten multiple whitespace sequences
        );
    $replace = array(
        '>',
        '<',
        '\\1'
        );
  $buffer = preg_replace($search, $replace, $buffer);
    return $buffer;
}


$JobId =  preg_replace( '/[^0-9]/', '', $_POST['id']);
if (strlen($JobId) != 15) { $allok = FALSE; }

// generiere Verzeichnisse für temporäre Dateien
if ($allok) {
	$tmpdatadir = "tmpdata";
	$serverdir = "/home/metabolomics/snipa/web/";
	$JobDir = $serverdir."/".$tmpdatadir."/".$JobId;
	if (!file_exists($JobDir)) {
		$allok = FALSE;
	}
}


// Array zum speichern des Skriptstatuses für jquery-Abfrage
$status = array();
$status['stepnum'] = 0;
$status['totalstepnum'] = 5;
$status['message'] = "";
$status['errmessage'] = "";
$status['ok'] = "";

function savePlotStatus($statusarray, $filename) {
	$statfilefh = fopen($filename.".1",'w');
	fwrite($statfilefh, utf8_encode(json_encode($statusarray)));
	fclose($statfilefh);
	copy($filename.".1",$filename); 
}


// Sanity Check für alle Sentinel-Eingabeoptionen
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Check format of input data.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");

	// speichere SNPs im Array, lösche nicht-"rs" Zeilen
	$SnpsSentinels = trim($SnpsSentinels);
	$SnpsSentinelsArray = preg_split('/\r\n|[\r\n]/',$SnpsSentinels);
	foreach ($SnpsSentinelsArray as &$entry) {
		$entry = trim($entry);
		if (!(preg_match("/^rs[0-9]+/",$entry))) { $entry = ""; }
	}
	unset($entry);
	$SnpsSentinelsArray = array_filter($SnpsSentinelsArray,'strlen');
	
	
	if (count($SnpsSentinelsArray) == 0) {
		$status['ok'] = "FAIL";
		$status['errmessage'] = "You have to enter at least one SNP. If you did so, check the syntax of your SNP and make sure that it is a valid rs identifier. If you entered multiple SNPs, make sure that they are seperated by newlines.";
		$allok = FALSE;
	} else {
		$status['ok'] = "OK";
	}  	

	savePlotStatus($status,$JobDir."/status.txt");
}


// Hole Positionen für alle sentinel SNPs
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Determine genetic positions of sentinel SNPs.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	$Sentinels = array();
	
	foreach ($SnpsSentinelsArray as $snp) {
		 $tmp = snipaMapRsid($Genomerelease,$Referenceset,$Population,$snp);
		 $tmprs = $snp;
		 if (!empty($tmp['POS'])) {
			$tmprsmapping = snipaMapPos($Genomerelease,$Referenceset,$Population,$tmp['CHR'],$tmp['POS'],$tmp['POS']);
			$tmprs = $tmprsmapping[$tmp['POS']];
		 }
		 $Sentinels[] = array('RSID' => $tmprs, 'CHR' => $tmp['CHR'], 'POS' => $tmp['POS']);
		 unset($tmp);
		 unset($tmprs);
		 file_put_contents($JobDir."/tmprsmapping",print_r($tmprsmapping,TRUE));
		 unset($tmprsmapping);
	}
	
	file_put_contents($JobDir."/sentinels.json",json_encode(array_map_recursive(function($t) { return is_string($t) ? utf8_encode($t) : $t; }, $Sentinels), JSON_NUMERIC_CHECK));
		
	$status['ok'] = "OK";
	savePlotStatus($status,$JobDir."/status.txt");
}



// Schreibe alle SNP-Annotationen in ein Temp-File
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Prepare SNP annotations.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");

	$sentinelsTotal = count($Sentinels);
	$sentinelsCount = 0;
	
	foreach($Sentinels as $val) {
			$sentinelsCount++;
			if (($sentinelsCount % 100) == 0) { $status['message'] = "Prepare SNP annotations (".$sentinelsCount." of ".$sentinelsTotal.")."; savePlotStatus($status,$JobDir."/status.txt");}
			set_time_limit(15);
			if (empty($val['POS'])) { continue; }
						
			$url = "http://localhost/snipa/backend/snipaRAPlotsAnnotations?snpname=".$val['RSID']."&snpchr=".$val['CHR']."&snppos=".$val['POS']."&sentinelpos=".$val['POS']."&genomerelease=".$Genomerelease."&referenceset=".$Referenceset."&population=".$Population."&annotation=".$Annotation."";
			$annopanel = "<h3>".$val['RSID'];
			$annopanel .= "<span onclick=\"$(this).parent('h3').next('div').remove(); $(this).parent('h3').hide().remove(); $('#snpinfo-accordion').accordion('refresh'); if ($('#snpinfo-accordion h3').length < 1) { $('#nosnpinfo').show(); resultstab = getIndexForId('#tabs-header','#tabs-results'); $('#tabs').tabs({active: resultstab}); } \" class=\"pinkspan\">delete</span>";
			
			$annopanel .= "<span onclick=\"printCard('".$Genomerelease."', '".$Referenceset."', '".$Population."', '".$Annotation."', '".$val['RSID']."');\" class=\"pinkspan\">save as PDF</span>";

			$annopanel .= "<span onclick=\"addToSnpBin('".$val['RSID']."','".$val['POS']."','".$val['CHR']."','".$Genomerelease."','".$Referenceset."','".$Population."','".$Annotation."'); \" class=\"pinkspan\">add to clipboard</span>";
			$annopanel .= "</h3>";
			$annopanel .= "<div>".file_get_contents($url)."</div>";

			file_put_contents($JobDir."/snp_anno.html",sanitize_output($annopanel)."\n", FILE_APPEND);
	}
	
	$status['ok'] = "OK";
	savePlotStatus($status,$JobDir."/status.txt");
}



	
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Generating report for this job.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	
	
	$report = array();
	$report['userinput']['genomerelease'] = $Genomerelease;
	$report['userinput']['referenceset'] = $Referenceset;
	$report['userinput']['population'] = $Population;
	$report['userinput']['annotation'] = $Annotation;
	$report['userinput']['inputtype'] = $SnpsInputType;
	$notmapped = 0;
	$tmpnotidentified = ""; foreach($Sentinels as $tmp) { if (empty($tmp['POS'])) {  $notmapped++; $tmpnotidentified .= $tmp['RSID']." "; } } unset($tmp); 
	$report['userinput']['notmapped'] = $notmapped;
	if ($tmpnotidentified =="") { $report['userinput']['notmappedsnps'] = ""; } else { $report['userinput']['notmappedsnps'] = " (".$tmpnotidentified.")";}
	$report['jobinfo']['jobid'] = $JobId;
	$report['jobinfo']['runtime'] = time() - $starttime;
			
	file_put_contents($JobDir."/report.txt",utf8_encode(json_encode($report)));
	
	$status['ok'] = "OK";
	savePlotStatus($JobDir."/status.txt");
}
	



// Fertig
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Finished.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	$status['ok'] = "OK";
	savePlotStatus($status,$JobDir."/status.txt");
}

print(utf8_encode(json_encode($status)));

?>
