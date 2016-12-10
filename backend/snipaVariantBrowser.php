<?php

require_once("../backend/snipaMaprsid.php");
require_once("../backend/snipaMapGenes.php");
require_once("../backend/snipaTabix.php");
require_once("../backend/snipaConfig.php");


$Genomerelease = $_POST['genomerelease'];
$Referenceset = $_POST['referenceset'];
$Population = $_POST['population'];
$Annotation = $_POST['annotation'];
$SnpsInputType = $_POST['snps_input_type'];
$SnpsSentinel = $_POST['snps_sentinel'];
$SnpsGene = $_POST['snps_gene'];
$SnpsRegionChr = $_POST['snps_region_chr'];
$SnpsRegionPosition = $_POST['snps_region_position'];

$starttime = time(); 
$allok = TRUE;

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
$status['totalstepnum'] = 4;
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

	// einzelne SNPs
	if ($SnpsInputType == "snps") {
		// speichere SNPs im Array, lösche nicht-"rs" Zeilen
		$SnpsSentinel = trim($SnpsSentinel);
		if (!(preg_match("/^rs[0-9]+/",$SnpsSentinel)))
		{
			$status['ok'] = "FAIL";
			$status['errmessage'] = "Check the syntax of your SNP and make sure that it is a valid rs identifier.";
			$allok = FALSE;
		} else {
			$status['ok'] = "OK";
		}
	}  	
	
	// Ensembl Gene Identifier
	if ($SnpsInputType == "gene") {
		$SnpsGene = strtoupper(trim($SnpsGene));
                $proxySnpsGene = preg_replace('/\s+/','',$SnpsGene);
		//$proxySnpsGene = preg_replace('/\s+/','',$proxySnpsGene);
		if ((strlen($SnpsGene) == 0) || !(preg_match("/^ENSG[0-9]+/",$SnpsGene))) {
			$status['ok'] = "FAIL";
			$status['errmessage'] = "You have to enter a valid ENSEMBL gene identifier.";
			$allok = FALSE;
		} elseif (!(snipaGeneExists($Genomerelease,$Annotation,$SnpsGene))) { // Gibt es den Identifier in der DB?
			$status['ok'] = "FAIL";
			$status['errmessage'] = "The ENSEMBL gene identifier ".$SnpsGene." is not in the annotation release &quot;".$Genomerelease."/".$Annotation."&quot;.";
			$allok = FALSE;
		} else {
			$status['ok'] = "OK";
		}
	}  

	// Chromosomale Region
	if ($SnpsInputType == "region") {
		$SnpsRegionPosition = preg_replace( '/[^0-9]/', '',$SnpsRegionPosition);
		
		if ((strlen($SnpsRegionPosition) == 0)){
			$status['ok'] = "FAIL";
			$status['errmessage'] = "Please provide a chromosomal position.";
			$allok = FALSE;
		} else {
			$status['ok'] = "OK";
		}
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
	$PlotThis = array();

	
	// einzelne SNPs
	if ($SnpsInputType == "snps") {
		$tmp = snipaMapRsid($Genomerelease,$Referenceset,$Population,$SnpsSentinel);
		$PlotThis = array('CHR' => $tmp['CHR'], 'POS' => $tmp['POS']);
		unset($tmp);
	}  	

        //$myfile = fopen("error.log", "w") or die("Unable to open file!");
        //fwrite($myfile, "done\n");
        //fclose($myfile);

	// Ensembl Gene Identifier
	if ($SnpsInputType == "gene") {
		$tmp = snipaGeneLocation($Genomerelease,$Annotation,$SnpsGene);
		$PlotThis = array('CHR' => $tmp[0]['CHR'], 'POS' => $tmp[0]['STOP']-0.5*($tmp[0]['STOP']-$tmp[0]['START']));
		unset($tmp);
	}  

	// Chromosomale Region
	if ($SnpsInputType == "region") {
		$PlotThis = array("CHR" => $SnpsRegionChr, "POS" => $SnpsRegionPosition);
	} 
	
	if ($PlotThis['CHR'] == "" | $PlotThis['POS'] == "") {
		$status['ok'] = "FAIL";
		if ($SnpsInputType == "snps") {	$status['errmessage'] = "The SNP you entered was not found in the selected release or population. Please check your input data."; }
		if ($SnpsInputType == "gene") {	$status['errmessage'] = "The gene you entered was not found in the selected annotation release. Please check your input data."; }
		if ($SnpsInputType == "region") {	$status['errmessage'] = "There was an error with the chromosomal position you've provided. Please check your input data."; }
		$allok = FALSE;
	} else {
		$status['ok'] = "OK";
	}
	savePlotStatus($status,$JobDir."/status.txt");
}

// schreibe Positionsinformationen in Javascript-Variablem
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Write positions to file.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	$plotThisJS = "curpos = ".$PlotThis['POS']."; ";
	$plotThisJS .= "curchr = '".$PlotThis['CHR']."'; ";
	$plotThisJS .= "curgenomerelease = '".$Genomerelease."'; ";
	$plotThisJS .= "curreferenceset = '".$Referenceset."'; ";
	$plotThisJS .= "curannotation = '".$Annotation."'; ";
	$plotThisJS .= "curpopulation = '".$Population."'; ";
	
	// falls einzelner SNP als Input - Positionsinformation zum Einfaerben
	if ($SnpsInputType == "snps") {
		$tmp = snipaMapRsid($Genomerelease,$Referenceset,$Population,$SnpsSentinel);
		$plotThisJS .= "sentinel = ".$tmp['POS']."; ";
	} else {
		$plotThisJS .= "sentinel = -1; ";
	}
		
	file_put_contents($JobDir."/plotthis.txt",$plotThisJS);
	
	$status['ok'] = "OK";
	savePlotStatus($status,$JobDir."/status.txt");
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
