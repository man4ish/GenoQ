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
$SnpsVariants = $_POST['snps_variants'];
$SnpsLDSentinel = $_POST['snps_ld_sentinel'];
$SnpsLDRsquare = $_POST['rsquare'];
$SnpsGene = $_POST['snps_gene'];
$SnpsRegionChr = $_POST['snps_region_chr'];
$SnpsRegionBegin = $_POST['snps_region_begin'];
$SnpsRegionEnd = $_POST['snps_region_end'];
$IncludeFunctionalAnnotation = $_POST['incl_funcann'];

$starttime = time(); 
$allok = TRUE;

$JobId =  preg_replace( '/[^0-9]/', '', $_POST['id']);
if (strlen($JobId) != 15) { $allok = FALSE; }

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
$status['totalstepnum'] = 9;
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
	if ($SnpsInputType == "variants") {
		// speichere SNPs im Array, lösche nicht-"rs" Zeilen
		$SnpsVariants = trim($SnpsVariants);
		$SnpsVariantsArray = preg_split('/\r\n|[\r\n]/',$SnpsVariants);
		foreach ($SnpsVariantsArray as &$entry) {
			$entry = trim($entry);
			if (!(preg_match("/^rs[0-9]+/",$entry))) { $entry = ""; }
		}
		unset($entry);
		$SnpsVariantsArray = array_filter($SnpsVariantsArray,'strlen');
		
		
		if (count($SnpsVariantsArray) == 0) {
			$status['ok'] = "FAIL";
			$status['errmessage'] = "You have to enter at least one variant. If you did so, check the syntax of your input and make sure that you have entered a valid rs identifier. If you entered multiple variants, make sure that they are separated by newlines.";
			$allok = FALSE;
		} else {
			$status['ok'] = "OK";
		}
	}  	
		
	// SNPs im LD
	if ($SnpsInputType == "ld") {
		// speichere SNPs im Array, lösche nicht-"rs" Zeilen
		$SnpsLDSentinel = trim($SnpsLDSentinel);
		if (!(preg_match("/^rs[0-9]+/",$SnpsLDSentinel)))
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

	if ($SnpsInputType == "region") {
		$SnpsRegionBegin = preg_replace( '/[^0-9]/', '',$SnpsRegionBegin);
		$SnpsRegionEnd = preg_replace( '/[^0-9]/', '',$SnpsRegionEnd);
		
		if ((strlen($SnpsRegionBegin) == 0) || (strlen($SnpsRegionEnd) == 0)){
			$status['ok'] = "FAIL";
			$status['errmessage'] = "Please provide both begin and end position of the chromosomal region.";
			$allok = FALSE;
		} elseif ($SnpsRegionBegin > $SnpsRegionEnd) {
			$status['ok'] = "FAIL";
			$status['errmessage'] = "Begin position must be equal or less than the end position.";
			$allok = FALSE;
		} elseif ($SnpsRegionEnd-$SnpsRegionBegin > 1000000) {
			$status['ok'] = "FAIL";
			$status['errmessage'] = "Maximum allowed distance between begin and end position is 1,000,000 bp.";
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
	$status['message'] = "Determine genetic positions.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	$PlotThis = array();
	
	
	// einzelne Varianten
	if ($SnpsInputType == "variants") {
		// Bestimme Konsens-Position und Chromosom
		$SnpsVariantsChrPos = array();
		foreach ($SnpsVariantsArray as $snp) {
			 $tmp = snipaMapRsid($Genomerelease,$Referenceset,$Population,$snp);
			 if (!empty($tmp['CHR'])) {
				 $SnpsVariantsChrPos['CHR'][] = $tmp['CHR'];
				 $SnpsVariantsChrPos['POS'][] = $tmp['POS'];
				 $tmprsmapping = snipaMapPos($Genomerelease,$Referenceset,$Population,$tmp['CHR'],$tmp['POS'],$tmp['POS']);
				 $tmprs = $tmprsmapping[$tmp['POS']];
				 $SnpsVariantsChrPos['SNP'][] = $tmprs;
			}
		}
		unset($tmp); unset($tmprs); unset($tmprsmapping);
		
		// Konsens Chromosom
		$ChrCnt = array_count_values($SnpsVariantsChrPos['CHR']); $consensusChr = array_search(max($ChrCnt),$ChrCnt);
		
		
		// Entferne SNPs, die nicht auf dem Konsens-Chromosom liegen
		$consensusChrKeys = array_keys($SnpsVariantsChrPos['CHR'],$consensusChr);
		function array_keep($array,$keys) { return array_intersect_key($array, array_fill_keys($keys,null)); }
		foreach (array("CHR","POS","SNP") as $entry) { $SnpsVariantsChrPos[$entry] = array_keep($SnpsVariantsChrPos[$entry],$consensusChrKeys); }
		$PlotThis = array('CHR' => $consensusChr, 'BEGIN' => min($SnpsVariantsChrPos['POS']), 'END' => max($SnpsVariantsChrPos['POS']));
		
		// Array umstrukturieren
		$PlotVariants = array();
		foreach ($SnpsVariantsChrPos as $key1 => $val1) {  $tmpcount=0; foreach ($val1 as $val2) { $PlotVariants[$tmpcount][$key1] = $val2; $tmpcount++; } }
		
	}  	
	
	// SNPs im LD
	if ($SnpsInputType == "ld") {
		$tmp = snipaMapRsid($Genomerelease,$Referenceset,$Population,$SnpsLDSentinel);
		// Mappe Pos zu rs-ID im aktuellen dbSNP release
		if (!empty($tmp['POS'])) {
			$tmprsmapping = snipaMapPos($Genomerelease,$Referenceset,$Population,$tmp['CHR'],$tmp['POS'],$tmp['POS']);
			$SnpsLDSentinel = $tmprsmapping[$tmp['POS']];
		}
		$PlotThis = array('CHR' => $tmp['CHR'], 'BEGIN' => $tmp['POS'], 'END' => $tmp['POS']);
		unset($tmp);
	}  	

	// Ensembl Gene Identifier
	if ($SnpsInputType == "gene") {
		$tmp = snipaGeneLocation($Genomerelease,$Annotation,$SnpsGene);
		$PlotThis = array('CHR' => $tmp[0]['CHR'], 'BEGIN' => $tmp[0]['START'], 'END' => $tmp[0]['STOP']);
		unset($tmp);
	}  

	// Chromosomale Region
	if ($SnpsInputType == "region") {
		$PlotThis = array("CHR" => $SnpsRegionChr, "BEGIN" => $SnpsRegionBegin, "END" => $SnpsRegionEnd);
	} 
	
	if ($PlotThis['CHR'] == "" | $PlotThis['BEGIN'] == "" | $PlotThis['END'] == "") {
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


// Grundlegende Annotation aus Tabix laden
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Get basic SNP annotations.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");

	$snps = array();
	
	if ($SnpsInputType == "variants") {
		$TabixBasic = array();
		foreach ($PlotVariants as $val) {
			$tmp = snipaGetSelfinfo($Genomerelease,$Referenceset,$Population,$val['CHR'],$val['POS'],$val['POS']);
			$TabixBasic[] = $tmp[0];
		}
	}
	
	if ($SnpsInputType == "ld") { 
		$TabixBasic = snipaGetProxies($Genomerelease,$Referenceset,$Population,$PlotThis['CHR'],$PlotThis['BEGIN'],"","",$SnpsLDRsquare); 
		// Bestimme minimale und maximale Position im LD Block
		foreach($TabixBasic as $tmp) {
			if ($tmp['POS2'] < $PlotThis['BEGIN']) { $PlotThis['BEGIN'] = $tmp['POS2']; }
			if ($tmp['POS2'] > $PlotThis['END']) { $PlotThis['END'] = $tmp['POS2']; }
		}
		unset($tmp);
	}
	
	if ($SnpsInputType == "gene" || $SnpsInputType == "region") { 
		$TabixBasic = snipaGetSelfinfo($Genomerelease,$Referenceset,$Population,$PlotThis['CHR'],$PlotThis['BEGIN'],$PlotThis['END']); 
	}
	
	foreach($TabixBasic as $entry) {
		$snps[$entry['POS2']] = array("RSID" => $entry['RSID']);
	}
	
	
	
	
	// Schreibe Positionen der SNPs fuer SNP-Annotation
	$Sentinels = array();
	foreach ($snps as $tmpkey => $tmpval) {
		 $Sentinels[] = array('RSID' => $tmpval['RSID'], 'CHR' => $PlotThis['CHR'], 'POS' => $tmpkey);
	}
	file_put_contents($JobDir."/sentinels.json",json_encode(array_map_recursive(function($t) { return is_string($t) ? utf8_encode($t) : $t; }, $Sentinels), JSON_NUMERIC_CHECK));
	
	if (count($Sentinels) < 1) {
		$status['ok'] = "FAIL";
		$allok = FALSE;
		$status['errmessage'] = "The chromosomal region you have selected contains no variants in ".$Referenceset."/".$Population.".";
	} else {
		$status['ok'] = "OK";
	}
	savePlotStatus($status,$JobDir."/status.txt");
}


// Erweiterte Annotation laden und mit Tabix-Output mergen
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Get advanced SNP annotations.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	if ($SnpsInputType == "ld" ||  $SnpsInputType == "gene" || $SnpsInputType == "region") {
		$TabixAdvanced = snipaGetSNPAnnotations($Genomerelease,$Annotation,$PlotThis['CHR'],$PlotThis['BEGIN'],$PlotThis['END']);
	}
	
	if ($SnpsInputType == "variants") {
		$TabixAdvanced = array();
		foreach ($PlotVariants as $val) {
			$tmp = snipaGetSNPAnnotations($Genomerelease,$Annotation,$val['CHR'],$val['POS'],$val['POS']);
			$TabixAdvanced[] = $tmp[0];
		}
	}
	
	foreach($TabixAdvanced as $entry) {
		if (array_key_exists($entry['POS'],$snps)) {
			$tmprs = $snps[$entry['POS']]['RSID'];
			$snps[$entry['POS']] = array_merge(array("RSID" => $tmprs),unserialize($entry['PHPARRAY']));
		}
	}
	
}


// Schreibe alle SNP-Annotationen in ein Temp-File
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Prepare SNP annotations.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	/*
	if ($IncludeFunctionalAnnotation == 1) {
		
		$sentinelsTotal = count($snps);
		$sentinelsCount = 0;
		
		foreach($snps as $key => $val) {
				$sentinelsCount++;
				if (($sentinelsCount % 100) == 0) { $status['message'] = "Prepare SNP annotations (".$sentinelsCount." of ".$sentinelsTotal.")."; savePlotStatus($status,$JobDir."/status.txt");}
				set_time_limit(15);
				$url = "http://localhost/snipa/backend/snipaRAPlotsAnnotations?snpname=".$val['RSID']."&snpchr=".$PlotThis['CHR']."&snppos=".$key."&sentinelpos=".$key."&genomerelease=".$Genomerelease."&referenceset=".$Referenceset."&population=".$Population."&annotation=".$Annotation."";
				$annopanel = "<h3>".$val['RSID'];
				$annopanel .= "<span onclick=\"$(this).parent('h3').next('div').remove(); $(this).parent('h3').hide().remove(); $('#snpinfo-accordion').accordion('refresh'); if ($('#snpinfo-accordion h3').length < 1) { $('#nosnpinfo').show(); resultstab = getIndexForId('#tabs-header','#tabs-results'); $('#tabs').tabs({active: resultstab}); } \" class=\"pinkspan\">delete</span>";
				$annopanel .= "<span onclick=\"printCard('".$Genomerelease."', '".$Referenceset."', '".$Population."', '".$Annotation."', '".$val['RSID']."');\" class=\"pinkspan\">save as PDF</span>";
				$annopanel .= "<span onclick=\"addToSnpBin('".$val['RSID']."','".$key."','".$PlotThis['CHR']."','".$Genomerelease."','".$Referenceset."','".$Population."','".$Annotation."'); \" class=\"pinkspan\">add to clipboard</span>";
				$annopanel .= "</h3>";
				$annopanel .= "<div>".file_get_contents($url)."</div>";
				file_put_contents($JobDir."/snp_anno.html",sanitize_output($annopanel)."\n", FILE_APPEND);
		}
	}
	*/
	$status['ok'] = "OK";
	savePlotStatus($status,$JobDir."/status.txt");
}




// Tabellarische Darstellung der Functional Annotations
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Get functional annotations from database.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	//if ($IncludeFunctionalAnnotation == 1) {

		# Extrahiere Annotation aus PHPARRAY
		$Annotations=array();
		foreach ($TabixAdvanced as $tabixanno) {
			$Annotations[$tabixanno['CHR']][$tabixanno['POS']]['COMPEFFECTS'] = str_replace(',', ', ', str_replace('_', '&nbsp;', $tabixanno['COMPEFFECTS']));
			$func = unserialize($tabixanno['PHPARRAY']);
			if(array_key_exists('genes', $func)){
					$genestring = array();
					foreach($func['genes'] AS $key => $value){
							array_push($genestring, $value);
					}
					$Annotations[$tabixanno['CHR']][$tabixanno['POS']]['GENES'] = implode(', ',$genestring);
			}
			if(array_key_exists('reg_genes', $func)){
					$genestring = array();
					foreach($func['reg_genes'] AS $key => $value){
							array_push($genestring, $value);
					}
					$Annotations[$tabixanno['CHR']][$tabixanno['POS']]['REGGENES'] = implode(', ',$genestring);
			}
			if(array_key_exists('eQTL-genes', $func)){
					$genestring = array();
					foreach($func['eQTL-genes'] AS $key => $value){
							array_push($genestring, $value);
					}
					$Annotations[$tabixanno['CHR']][$tabixanno['POS']]['EQTLGENES'] = implode(', ',$genestring);
			}
			if(array_key_exists('variant_association', $func)){
					$Annotations[$tabixanno['CHR']][$tabixanno['POS']]['TRAIT'] = "yes";	
			}
			if(array_key_exists('cis-eQTL', $func)){
					$Annotations[$tabixanno['CHR']][$tabixanno['POS']]['CISEQTL'] = "yes";	
			}
			if(array_key_exists('trans-eQTL', $func)){
					$Annotations[$tabixanno['CHR']][$tabixanno['POS']]['TRANSEQTL'] = "yes";	
			}
		}
		unset($tabixanno); unset($field);
	
	
		// file_put_contents($JobDir."/tmp.Annotations",print_r($Annotations,TRUE));
		// file_put_contents($JobDir."/tmp.TabixBasic",print_r($TabixBasic,TRUE));
		// file_put_contents($JobDir."/tmp.snps",print_r($snps,TRUE));
	
		# mapping der positionen zu rs-ids [POS => RSID]
		$posrsidmap = array();
		foreach($snps as $key => $val) { $posrsidmap[$key] = $val['RSID']; }
		$rsidposmap = array_flip($posrsidmap);
		file_put_contents($JobDir."/tmp.posrsidmap",print_r($posrsidmap,TRUE));
	
		# Setze Default-Werte, falls keine Annotation für Variante vorhanden
		$AnnotationFields = array("COMPEFFECTS" => "", "EQTLGENES" => "-", "REGGENES" => "-", "GENES" => "-", "TRAIT" => "no", "CISEQTL" => "no", "TRANSEQTL" => "no"); 
		for($i=0; $i<count($TabixBasic); $i++) {
			foreach ($AnnotationFields as $field => $default) { 
				$tmpanno = "";
				if (array_key_exists($field,$Annotations[$TabixBasic[$i]['CHR']][$TabixBasic[$i]['POS2']])) { $tmpanno = $Annotations[$TabixBasic[$i]['CHR']][$TabixBasic[$i]['POS2']][$field]; }
				else { $tmpanno = $default; } // Setze default-Werte ein, falls keine Annotation zum SNP vorhanden
				$TabixBasic[$i][$field] = $tmpanno;
			}
		}
	
		
		// file_put_contents($JobDir."/tmp.TabixBasic2",print_r($TabixBasic,TRUE));
		
		# schreibe serialisiertes php-Array 
		if ($SnpsInputType == "ld") {
			file_put_contents($JobDir."/proxySearch.phparray.txt",
			serialize(array("RSID" => $SnpsLDSentinel, "CHR" => $PlotThis['CHR'], "POS"=>$rsidposmap[$SnpsLDSentinel], "Genomerelease"=>$Genomerelease, "Referenceset"=>$Referenceset, "Population"=>$Population, "Annotation"=>$Annotation))."\t".serialize($TabixBasic)."\n", 
			FILE_APPEND | LOCK_EX);
			file_put_contents($JobDir."/proxySearch.count", count($TabixBasic), LOCK_EX);
		} else {
			foreach($TabixBasic as $val) {
				file_put_contents($JobDir."/proxySearch.phparray.txt",
				serialize(array("RSID" => $val['RSID'], "CHR" => $val['CHR'], "POS"=>$val['POS2'], "Genomerelease"=>$Genomerelease, "Referenceset"=>$Referenceset, "Population"=>$Population, "Annotation"=>$Annotation))."\t".serialize(array(array("DIST" => 0) + $val))."\n", 
				FILE_APPEND | LOCK_EX);
			}
			file_put_contents($JobDir."/proxySearch.count", count($TabixBasic), LOCK_EX);
			touch($JobDir."/proxySearch.showsentinelsonly");
		}
	

	
		# Download-Version erstellen (der Einfachheit halber keine Unterscheidung zwischen LD-Suche und Bereichssuche)
		if ($SnpsInputType == "ld") { 
				$helptext_header = array("QRSID" => "Query SNP rsID",
					 "RSID" => "Proxy SNP rsID",
					 "CHR" => "Chromosome",
					 "POS1" => "Sentinel SNP Position",
					 "POS2" => "Proxy SNP Position",
					 "DIST" => "Distance",
					 "R2" => "LD r^2",
					 "D" => "LD D",
					 "DPRIME" => "LD D'",
					 "MAJOR" => "Proxy Allele A",
					 "MINOR" => "Proxy Allele B",
					 "MAF" => "Allele B Frequency",
					 "CMMB" => "Recombination Rate (CM/Mb)",
					 "CM" => "Genetic distance (CM)",
					 "COMPEFFECTS" => "Compressed functional annotation of Proxy SNP",
					 "TRAIT" => "Associated with trait (yes/no)",
					 "CISEQTL" => "Associated with eQTL in cis (yes/no)",
					 "TRANSEQTL" => "Associated with eQTL in trans (yes/no)",
					 "GENES" => "Genes hit or close-by (distance 5KB max)",
					 "REGGENES" => "Potentially regulated genes (linked via promoter or enhancer)",
					 "EQTLGENES" => "Genes linkes via eQTL-associations"
					 );
		} else {
			$helptext_header = array(
					 "RSID" => "SNP rsID",
					 "CHR" => "Chromosome",
					 "POS1" => "Position",
					 "MAJOR" => "Allele A",
					 "MINOR" => "Allele B",
					 "MAF" => "Allele B Frequency",
					 "CMMB" => "Recombination Rate (CM/Mb)",
					 "CM" => "Genetic distance (CM)",
					 "COMPEFFECTS" => "Compressed functional annotation of SNP",
					 "TRAIT" => "Associated with trait (yes/no)",
					 "CISEQTL" => "Associated with eQTL in cis (yes/no)",
					 "TRANSEQTL" => "Associated with eQTL in trans (yes/no)",
					 "GENES" => "Genes hit or close-by (distance 5KB max)",
					 "REGGENES" => "Potentially regulated genes (linked via promoter or enhancer)",
					 "EQTLGENES" => "Genes linkes via eQTL-associations"
					 );
		}
		$helptext = "Header abbreviations:\n\n";
		foreach($helptext_header as $abr => $fulltext) {
			$helptext .= $abr." = ".$fulltext."\n";
		}
		$helptext .= "\n\nInformation about this job:\n\n";
		$helptext .= "Genome assembly: ".$Genomerelease."\n";
		$helptext .= "Reference set: ".$Referenceset."\n";
		$helptext .= "Population: ".$Population."\n";
		$helptext .= "Annotation release: ".$Annotation."\n";
		$helptext .= "Input type: ".$SnpsInputType."\n";
	
		file_put_contents($JobDir."/blockannotation.description.txt", $helptext, LOCK_EX | FILE_APPEND);

		// Variable, dass einmalig der Header im CSV-File geschrieben wird
		$dlfileinclheader = TRUE;

		// Funktion zum Sortieren des Ausgabearrays nach den oben definierten Keys 
		function sortArrayByArray($array,$orderArray) {
			$ordered = array();
	    		foreach($orderArray as $key) {
		    	if (array_key_exists($key,$array)) {
    				$ordered[$key] = $array[$key];
		    		unset($array[$key]);
    				}
    			}
			//return $ordered + $array; // enthält auch die nicht-sortierten Keys des urspruenglichen Arrays
			return $ordered;
		}
	
		$downloadresults = $TabixBasic;
			foreach($downloadresults as $line) {
				$dlfile = fopen($JobDir."/blockannotation.results.csv",'a');
				// Fuege QRSID (=sentinel ID) ein
				if ($SnpsInputType == "ld") { $line = array('QRSID' => $SnpsLDSentinel) + $line; } 
				// Sortiere Array nach den Keys aus dem description-File (s.o.)
				$line = sortArrayByArray($line,array_keys($helptext_header));
				// Header nur beim ersten Eintrag
				if ($dlfileinclheader) {
					$dlfileinclheader = FALSE;
					fputcsv($dlfile, array_keys($line), "\t");
				}
				array_walk($line, function(&$el){ $el = str_replace("&nbsp;"," ",$el); $el = html_entity_decode($el); } );
				fputcsv($dlfile, $line, "\t");
				fclose($dlfile);
			}
	
		// Zippe results-Dateien zum Download
		system("cd ".$JobDir."; zip blockannotation.results.zip blockannotation.description.txt blockannotation.results.csv > /dev/null",$cmderrorlevel);
	//}
	
	
	$status['ok'] = "OK";
	savePlotStatus($status,$JobDir."/status.txt");
}


// Schreiben der Block-Annotation über Hilfsfunktion
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	if (($SnpsInputType == "region") && ($SnpsRegionEnd-$SnpsRegionBegin > 100000)) { $status['message'] = "Create block annotation. Since you have chosen a rather large genetic region, this could take a few minutes."; } else { $status['message'] = "Create block annotation."; }
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	set_time_limit(1200);
	/*
	 * *********************************************
	 * IMPORTANT !!!
	 * *********************************************
	 * REMOVE STR_REPLACE AFTER UPDATE TO GRCH38 !!! 
	 * */
	file_put_contents($JobDir."/tmp.snpsphparray",print_r($snps,TRUE));
	file_put_contents($JobDir."/tmp.snpsjson",json_encode($snps));
	file_put_contents($JobDir."/block_anno.html",str_replace("http://www.ensembl", "http://grch37.ensembl", printAnno($snps)));
}


if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Generating report for this job.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	
	function human_filesize($bytes, $decimals = 2) {
	  $sz = 'BKMGTP';
	  $factor = floor((strlen($bytes) - 1) / 3);
	  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}
	
	$report = array();
	$report['userinput']['genomerelease'] = $Genomerelease;
	$report['userinput']['referenceset'] = $Referenceset;
	$report['userinput']['population'] = $Population;
	$report['userinput']['annotation'] = $Annotation;
	$report['userinput']['inputtype'] = $SnpsInputType;
	$inputdetails = "";
	if ($SnpsInputType == "gene") { $inputdetails .= "Gene identifier: ".$SnpsGene.""; }
	if ($SnpsInputType == "region") { $inputdetails .= "Genetic region: chr".$SnpsRegionChr.":".$SnpsRegionBegin."-".$SnpsRegionEnd.""; }
	if ($SnpsInputType == "variants") { 
		
	}
	$report['userinput']['inputdetails'] = $inputdetails;
	$report['jobinfo']['dldescription'] = "";
	$report['jobinfo']['dlcsv'] = "";
	$report['jobinfo']['dlcsvsize'] = "";
	$report['jobinfo']['dlzip'] = "";
	$report['jobinfo']['dlzipsize'] = "";
	$report['jobinfo']['dldescription'] = $tmpdatadir."/".$JobId."/blockannotation.description.txt";
	$report['jobinfo']['dlcsv'] = $tmpdatadir."/".$JobId."/blockannotation.results.csv";
	$report['jobinfo']['dlcsvsize'] = human_filesize(filesize($JobDir."/blockannotation.results.csv"));
	$report['jobinfo']['dlzip'] = $tmpdatadir."/".$JobId."/blockannotation.results.zip";
	$report['jobinfo']['dlzipsize'] = human_filesize(filesize($JobDir."/blockannotation.results.zip"));

		
	file_put_contents($JobDir."/report.txt",utf8_encode(json_encode($report)));
	
	$status['ok'] = "OK";
	savePlotStatus($status,$JobDir."/status.txt");
}
	



// Fertig
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Finished. Stand by while browser finishes downloading and rendering the results page.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	$status['ok'] = "OK";
	savePlotStatus($status,$JobDir."/status.txt");
}


print(utf8_encode(json_encode($status)));
?>

<?php
// Globale Hilfsvariable zum Speichern der Block-Annotation
$txt = "";
// Hilfsfunktion für das Schreiben der Block-Annotation
function printAnno($snparray){
	global $txt, $PlotThis;
	$txt = "";
	add("<div class='annotation-section'>");
	add("<h2 class='efftype'>Block annotations</h2>");
	add("<table class='annotation top'><tr><th class='snpinf' colspan='2'>Block info</th></tr><tr><th>genomic range</th><td>chr".$PlotThis['CHR'].":".number_format(min(array_keys($snparray)))."-".number_format(max(array_keys($snparray)))."  <a href='http://www.ensembl.org/Homo_sapiens/Location/View?db=core;r=".$PlotThis['CHR'].":".min(array_keys($snparray))."-".max(array_keys($snparray))."' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='region @ EnsEMBL' /></a></td></tr><tr><th>block size</th><td>".number_format(max(array_keys($snparray))-min(array_keys($snparray))+1)." bp</td></tr><tr><th>variant count</th><td>".count($snparray)." variants</td></tr></table><br/>");
	$total = array();
	foreach($snparray as $key => $varr){
		// reformat pubmed-ids for array_merge_recursive to do what i want it to do
		if(array_key_exists('variant_association', $varr)){
			foreach($varr['variant_association'] as $study => $sarr){
				foreach($sarr as $id => $entry){
					if(is_numeric($id)){
						$varr['variant_association'][$study]["pmid".$id] = $varr['variant_association'][$study][$id];
						unset($varr['variant_association'][$study][$id]);
					}
				}
			}
		}
		$total = array_merge_recursive($total, $varr);
	}
	if(is_array($total['score']['CADD'])){
		foreach($total['score'] as $key => $varr){
			$varr = array_filter($varr, "is_numeric");
			$total['score'][$key] = "&mu; = ".sprintf("%.3f", mean($varr))." [".min($varr)." &ndash; ".max($varr)."]";
		}
	}
	$total = super_unique($total);
	$total = shrink($total);
	// re-establish pubmed-ids
	if(array_key_exists('variant_association', $total)){
		foreach($total['variant_association'] as $study => $sarr){
			foreach($sarr as $id => $entry){
				if(preg_match('/pmid\d+/',$id)){
					$total['variant_association'][$study][(preg_replace('/pmid(\d+)$/', '$1',$id))] = $total['variant_association'][$study][$id];
					unset($total['variant_association'][$study][$id]);
				}
			}
		}
	}
	annotate($total, $snparray);
	
	// Insert for debugging:
	
	//add("<pre>");
	//add(print_r($total,TRUE));
	//add("</pre>");
	
	add("</div>");
	return $txt;
}
// Hilfsfunktion für Mean-Berechnung
function mean($arr){
    if (!count($arr)) return 0;
    $sum = 0;
    for ($i = 0; $i < count($arr); $i++){
        $sum += $arr[$i];
    }

    return $sum / count($arr);
}
// Hilfsfunktion für array shrinkage
function shrink($array){
	foreach($array as $key => $value){
		if(is_array($value) && count($value) == 1 && array_key_exists("0", $value)){
			$array[$key] = $value[0];
		}else{
			$array[$key] = shrink($array[$key]);
		}
	}
	return $array;
}
// Hilfsfunktion für Multi-level-unified array
function super_unique($array){
	$result = $array;
	if(array_key_exists(0, $array)){
		$result = array_map("unserialize", array_unique(array_map("serialize", $array)));
	}
	foreach ($result as $key => $value){
		if ( is_array($value) ){
			$result[$key] = super_unique($value);
		}
	}
	return $result;
}
function super_unique_old($array){
	$result = array_intersect_key($array, array_unique(array_map('serialize', $array )));

	foreach ($result as $key => $value){
		if ( is_array($value) ){
			$result[$key] = super_unique($value);
		}
	}
	return $result;
}

// Hilfsfunktion (print-Ersatz) für die Erstellung der Block-Annotation
function add($mixed_var){
	global $txt;
	$mixed_var = str_replace("rowspan='1'", "", $mixed_var);
	$txt = $txt."\n".$mixed_var;
}
// Hilfsfunktion um nach Genen zu sortieren
function sortByGene($a, $b){
	return (($a['gene_symbol'] < $b['gene_symbol']) ? -1 : 1);
}
function sortGeneList($a, $b){
	return (($a[0] < $b[0]) ? -1 : 1);
}
// Adaptierte Annotation-Style-Funktion aus snipaRAPlotsAnnotations.php
function annotate($functional, $snparray){
	global $PlotThis, $Genomerelease, $Referenceset, $Population, $Annotation;
	// Descriptions of scores
	$phyloP = "[b]phyloP[/b] is a conservation score represented as [b]-log(P)[/b] of a test for [b]neutral evolution[/b] of a nucleotide.[br/][br/][b][u]Positive score[/u][/b][br/]The position is predicted to be rather [b]conserved[/b].[br/][br/][b][u]Negative score[/u][/b][br/]The position is predicted to be rather [b]fast-evolving[/b].";
	$phastCons = "[b]phastCons[/b] is a conservation score represented by the probability (i.e., range is 0 to 1) for a nucleotide to belong to a [b]conserved element[/b].[br/][br/][b][u]High score (max. 1)[/u][/b][br/]The position is predicted to be rather [b]conserved[/b].[br/][br/][b][u]Low score (min. 0)[/u][/b][br/]The position is predicted to be rather [b]fast-evolving[/b].";
	$gerp = "[b]GERP++[/b] is a conservation score quantified in terms of \"rejected substitutions\" per nucleotide, defined as number of substitutions [b]expected under neutrality[/b] minus number of substitutions observed.[br/][br/][b][u]Positive score[/u][/b][br/]The position shows a substitution deficit (it is [b]conserved[/b]).[br/][br/][b][u]Negative score[/u][/b][br/]The position shows a substitution surplus (it is [b]fast-evolving[/b]).";
	$cadd = "[b]CADD[/b] (Combined Annotation Dependent Depletion) integrates multiple annotations into one metric by contrasting variants that survived natural selection with simulated mutations. The scaled C-scores given here range from 1 to 99.[br/][br/][b][u]Score interpretation[/u][/b][br/]A score &ge; 10 indicates that this is predicted to be one of the 10% most deleterious substitutions that you can do to the human genome, a score &ge; 20 indicates the 1% most deleterious and so on.";
	// End of descriptions
	
	add("<table class='annotation'>
<tr><th class='duper' colspan='4'>Basic features</th></tr>
<tr><th class='super' colspan='2'>Conservation/deleteriousness</th><th class='super' colspan='2'>Linked genes</th></tr>
<tr><th>phyloP&nbsp;<a class='whatsthis' target='_blank' title='{$phyloP}' href='http://compgen.bscb.cornell.edu/phast/'></a></th><td>{$functional['score']['phyloP']}</td><th>gene(s) hit or close-by</th><td>");
	if(array_key_exists('genes', $functional)){
		asort($functional['genes']);
		$tmp = array();
		foreach($functional['genes'] as $key => $value){
			array_push($tmp, "{$functional['genes'][$key]} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$functional['genes'][$key]} @ EnsEMBL' /></a>");
		}
		add(join(", ", $tmp));
	}else{
		add("&ndash;");
	}
	add("</td></tr>
	<tr><th>phastCons&nbsp;<a class='whatsthis' target='_blank' title='{$phastCons}' href='http://compgen.bscb.cornell.edu/phast/'></a></th><td>{$functional['score']['phastCons']}</td><th>eQTL gene(s)</th><td>");
	if(array_key_exists('eQTL-genes', $functional)){
		asort($functional['eQTL-genes']);
		$tmp = array();
		foreach($functional['eQTL-genes'] as $key => $value){
			array_push($tmp, "{$functional['eQTL-genes'][$key]} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$functional['eQTL-genes'][$key]} @ EnsEMBL' /></a>");
		}
		add(join(", ", $tmp));
	}else{
		add("&ndash;");
	}
	add("</td></tr>
	<tr><th>GERP++&nbsp;<a class='whatsthis' target='_blank' title='{$gerp}' href='http://mendel.stanford.edu/SidowLab/downloads/gerp/'></a></th><td>{$functional['score']['GERP++']}</td><th>potentially regulated gene(s)</th><td>");
	if(array_key_exists('reg_genes', $functional)){
		asort($functional['reg_genes']);
		$tmp = array();
		foreach($functional['reg_genes'] as $key => $value){
			array_push($tmp, "{$functional['reg_genes'][$key]} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$functional['reg_genes'][$key]} @ EnsEMBL' /></a>");
		}
		add(join(", ", $tmp));
	}else{
		add("&ndash;");
	}
	add("</td></tr>
	<tr><th>CADD score&nbsp;<a class='whatsthis' target='_blank' title='{$cadd}' href='http://cadd.gs.washington.edu/'></a></th><td>{$functional['score']['CADD']}</td><th>disease gene(s)</th><td>");
	if(array_key_exists('gene_associations', $functional)){
		$tmp = array();
		foreach($functional['gene_associations'] as $source => $genes){
			foreach($genes as $key => $value){
				foreach($value as $trait => $info){
					$tmp["{$info['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$info['gene_symbol']} @ EnsEMBL' /></a>"] = 1;
				}
			}
		}
		add(join(", ", array_keys($tmp)));
	}else{
		add("&ndash;");
	}
	add("</td></tr>
	</table><br/>");

	add("</div>");

	
	//Disease Annotations
	 
	if(array_key_exists('gene_associations', $functional) || array_key_exists('variant_association', $functional)){
		add("<div class='annotation-section'>");
		add("<h2 class='efftype'>Trait annotations</h2>");
			
		if(array_key_exists('variant_association', $functional)){

			// associations
			if(array_key_exists('gwascatalog_variants', $functional['variant_association']) || array_key_exists('metabolomics_variants', $functional['variant_association']) || array_key_exists('dbgap_variants', $functional['variant_association'])){
				add("<table class='annotation'>
				<tr><th class='duper' colspan='6'>Variant association</th></tr>
				<tr><th class='super'>trait</th><th class='super'>min(p-value)</th><th class='super'>source DB</th><th class='super' colspan='2'>source entry/link</th><th class='super'>variant(s)</th></tr>");
			
			
				// GWAS Catalog
				if(array_key_exists('gwascatalog_variants', $functional['variant_association'])){
					$temp = $functional['variant_association']['gwascatalog_variants'];
					krsort($temp);
					foreach($temp as $key => $value){
						foreach($value as $trait => $tarr){
							//Create SNP List
							$list = array();
							foreach($snparray as $rs => $value){
								if(isset($value['variant_association']['gwascatalog_variants'][$key]) && array_key_exists($trait ,$value['variant_association']['gwascatalog_variants'][$key])){
									$list[$rs] = $value['RSID'];
								}
							}
							$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
							
							//Print Line
							add(sprintf("<tr><td>$trait</td><td>&lt;".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',minimum($tarr['P-value']))."</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>{$tarr['external_id']}</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>".$listsymbol."</tr>", preg_replace('/([\d\.]+)e\-(\d+)/','\2',minimum($tarr['P-value']))));
						}
					}
				}
				
				// Metabolomics GWAS Server
				if(array_key_exists('metabolomics_variants', $functional['variant_association'])){
					$temp = $functional['variant_association']['metabolomics_variants'];
					krsort($temp);
					foreach($temp as $key => $value){
						foreach($value as $trait => $tarr){
							//Create SNP List
							$list = array();
							foreach($snparray as $rs => $value){
								if(isset($value['variant_association']['metabolomics_variants'][$key]) && array_key_exists($trait ,$value['variant_association']['metabolomics_variants'][$key])){
									$list[$rs] = $value['RSID'];
								}
							}
							$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
							
							//Print Line
							add(sprintf("<tr><td>$trait</td><td>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',minimum($tarr['P-value']))."</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>{$tarr['external_id']}</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>".$listsymbol."</tr>", preg_replace('/(<?[\d\.]+)e\-(\d+)/','\2',minimum($tarr['P-value']))));
						}
					}
				}
				
				// dbGaP
				if(array_key_exists('dbgap_variants', $functional['variant_association'])){
					$temp = $functional['variant_association']['dbgap_variants'];
					krsort($temp);
					foreach($temp as $key => $value){
						foreach($value as $trait => $tarr){
							//Create SNP List
							$list = array();
							foreach($snparray as $rs => $value){
								if(isset($value['variant_association']['dbgap_variants'][$key]) && array_key_exists($trait ,$value['variant_association']['dbgap_variants'][$key])){
									$list[$rs] = $value['RSID'];
								}
							}
							$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
							
							//Print Line
							add(sprintf("<tr><td>$trait</td><td>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',minimum($tarr['P-value']))."</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>{$tarr['external_id']}</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/dbgap.png' alt='dbGaP' title='view in dbGaP' /></a></td>".$listsymbol."</tr>", preg_replace('/([\d\.]+)e\-(\d+)/','\2',minimum($tarr['P-value']))));
						}
					}
				}
				add("</table><br/>");
			} // end associations
			
			// annotations
			if(array_key_exists('clinvar_variants', $functional['variant_association']) || array_key_exists('omim_variants', $functional['variant_association']) || array_key_exists('hgmd_variants', $functional['variant_association']) || array_key_exists('drugbank_fx_variants', $functional['variant_association']) || array_key_exists('drugbank_adr_variants', $functional['variant_association']) || array_key_exists('uniprot_variants', $functional['variant_association'])){
				add("<table class='annotation'>
				<tr><th class='duper' colspan='6'>Variant annotation</th></tr>
				<tr><th class='super'>trait</th><th class='super'>type</th><th class='super'>source DB</th><th class='super' colspan='2'>source entry/link</th><th class='super'>Variant(s)</th></tr>");
			
				// ClinVar
				if(array_key_exists('clinvar_variants', $functional['variant_association'])){
					$temp = $functional['variant_association']['clinvar_variants'];
					krsort($temp);
					foreach($temp as $key => $value){
						foreach($value as $trait => $tarr){
							//Create SNP List
							$list = array();
							foreach($snparray as $rs => $value){
								if(isset($value['variant_association']['clinvar_variants'][$key]) && array_key_exists($trait ,$value['variant_association']['clinvar_variants'][$key])){
									$list[$rs] = $value['RSID'];
								}
							}
							$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
							
							//Print Line
							add("<tr><td>$trait</td><td>{$tarr['Annotated_as']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/clinvar.png' alt='ClinVar' title='view in ClinVar' /></a></td>".$listsymbol."</tr>");
						}
					}
				}
				// HGMD
				if(array_key_exists('hgmd_variants', $functional['variant_association'])){
					$temp = $functional['variant_association']['hgmd_variants'];
					krsort($temp);
					foreach($temp as $key => $value){
						foreach($value as $trait => $tarr){
							//Create SNP List
							$list = array();
							foreach($snparray as $rs => $value){
								if(isset($value['variant_association']['hgmd_variants'][$key]) && array_key_exists($trait ,$value['variant_association']['hgmd_variants'][$key])){
									$list[$rs] = $value['RSID'];
								}
							}
							$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
							
							//Print Line
							add("<tr><td>$trait&nbsp;<a class='whatsthis' target='_blank' title='Only visible to registered users @ HGMD public' href='#'></a></td><td>{$tarr['type']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/hgmd.png' alt='HGMD' title='view in HGMD public' /></a></td>".$listsymbol."</tr>");
						}
					}
				}
				// OMIM
				if(array_key_exists('omim_variants', $functional['variant_association'])){
					$temp = $functional['variant_association']['omim_variants'];
					krsort($temp);
					foreach($temp as $key => $value){
						foreach($value as $trait => $tarr){
							//Create SNP List
							$list = array();
							foreach($snparray as $rs => $value){
								if(isset($value['variant_association']['omim_variants'][$key]) && array_key_exists($trait ,$value['variant_association']['omim_variants'][$key])){
									$list[$rs] = $value['RSID'];
								}
							}
							$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
							
							//Print Line
							add("<tr><td>$trait</td><td>{$tarr['type']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>".preg_replace('/\s\(.+$/', '', $key)."</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/omim.png' alt='OMIM' title='view in OMIM' /></a></td>".$listsymbol."</tr>");
						}
					}
				}
				// UniProt
				if(array_key_exists('uniprot_variants', $functional['variant_association'])){
					$temp = $functional['variant_association']['uniprot_variants'];
					krsort($temp);
					foreach($temp as $key => $value){
						foreach($value as $trait => $tarr){
							//Create SNP List
							$list = array();
							foreach($snparray as $rs => $value){
								if(isset($value['variant_association']['uniprot_variants'][$key]) && array_key_exists($trait ,$value['variant_association']['uniprot_variants'][$key])){
									$list[$rs] = $value['RSID'];
								}
							}
							$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
							
							//Print Line
							if(preg_match("/^MIM/",$tarr['external_id'])){
								add("<tr><td>$trait</td><td>{$tarr['type']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/omim.png' alt='OMIM' title='view in OMIM' /></a></td>".$listsymbol."</tr>");
							}else{
								add("<tr><td>$trait</td><td>{$tarr['type']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/uniprot.png' alt='UniProt' title='view in UniProt' /></a></td></tr>");
							}
						}
					}
				}
				// DrugBank FX
				if(array_key_exists('drugbank_fx_variants', $functional['variant_association'])){
					$temp = $functional['variant_association']['drugbank_fx_variants'];
					krsort($temp);
					foreach($temp as $key => $value){
						foreach($value as $trait => $tarr){
							//Create SNP List
							$list = array();
							foreach($snparray as $rs => $value){
								if(isset($value['variant_association']['drugbank_fx_variants'][$key]) && array_key_exists($trait ,$value['variant_association']['drugbank_fx_variants'][$key])){
									$list[$rs] = $value['RSID'];
								}
							}
							$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
							
							//Print Line
							add("<tr><td>$trait</td><td>{$tarr['type']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>".$listsymbol."</tr>");
						}
					}
				}
				// DrugBank ADR
				if(array_key_exists('drugbank_adr_variants', $functional['variant_association'])){
					$temp = $functional['variant_association']['drugbank_adr_variants'];
					krsort($temp);
					foreach($temp as $key => $value){
						foreach($value as $trait => $tarr){
							//Create SNP List
							$list = array();
							foreach($snparray as $rs => $value){
								if(isset($value['variant_association']['drugbank_adr_variants'][$key]) && array_key_exists($trait ,$value['variant_association']['drugbank_adr_variants'][$key])){
									$list[$rs] = $value['RSID'];
								}
							}
							$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
							
							//Print Line
							add("<tr><td>$trait</td><td>{$tarr['type']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td></tr>");
						}
					}
				}
				
				add("</table><br/>");
			} // end annotations
		}
		
		// GENES
		if(array_key_exists('gene_associations', $functional)){
			add("<table class='annotation'>
			<tr><th class='duper' colspan='5'>Disease gene annotation</th></tr>
			<tr><th class='super'>gene</th><th class='super'>trait</th><th class='super'>source DB</th><th class='super' colspan='2'>source entry/link</th></tr>");
			foreach($functional['gene_associations'] as $source => $genes){
				foreach($genes as $key => $value){
					foreach($value as $trait => $info){
						$tr = str_replace('@','',$trait);
						if(preg_match("/^MIM/",$info['external_id'])){
							add("<tr><td>{$info['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$info['gene_symbol']} @ EnsEMBL' /></a></td><td>$tr</td><td><a class='web' href='{$info['source_link']}' target='_blank'>{$info['source']}</a></td><td>{$info['external_id']}</td><td><a href='{$info['link']}' target='_blank'><img src='frontend/img/omim.png' alt='OMIM' title='view in OMIM' /></a></td></tr>");
						}else{
							add("<tr><td>{$info['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$info['gene_symbol']} @ EnsEMBL' /></a></td><td>$tr</td><td><a class='web' href='{$info['source_link']}' target='_blank'>{$info['source']}</a></td><td>{$info['external_id']}</td><td><a href='{$info['link']}' target='_blank'><img src='frontend/img/".strtolower($info['source']).".png' alt='{$info['source']}' title='view in {$info['source']}' /></a></td></tr>");
						}
					}
				}
			}
			add("</table><br/>");
		}
		
		add("</div>");
	}


	// direct transcript
	if(array_key_exists('direct_transcript_effect', $functional)){
		uasort($functional['direct_transcript_effect'], 'sortByGene');
		// TODO: add mature mirna & splice site variants
		add("<div class='annotation-section'>");
		add("<h2 class='efftype'>Direct effect on transcript</h2>");
		if($subf = getDTSEffectArray($functional)){
			if(array_key_exists('direct_transcript_effect', $subf)){
				add("<table class='annotation'><tr><th class='duper' colspan='10'>Amino acid sequence alteration</th></tr>
		<tr><th class='super'>gene</th><th class='super'>effect type</th><th class='super'>affected transcript</th><th class='super'>RefSeq id</th><th class='super'>protein</th><th class='super'>exchanged AA's</th><th class='super'>exchanged codons</th><th class='super'>SIFT prediction</th><th class='super'>PolyPhen prediction</th><th class='super'>variant(s)</th></tr>");
				foreach($subf['direct_transcript_effect'] as $key => $value){
					//Create SNP List
					$list = array();
					foreach($snparray as $rs => $snpdat){
						if(isset($snpdat['direct_transcript_effect']) && array_key_exists($key ,$snpdat['direct_transcript_effect'])){
							$list[$rs] = $snpdat['RSID'];
						}
					}
					$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
					
					//Print Line
					add("<tr>
					<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
					");
					if(!is_array($value['effect'])){
						add("<td>{$value['effect']}</td>");
					}else{
						add("<td>".implode(", ", $value['effect'])."</td>");
					}
					add("<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
					<td>{$value['refseq']}</td>
					<td>".((array_key_exists('ensp', $value)) ? ("{$value['ensp']}".(($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$value}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
					");
					if(!is_array($value['codon'])){
						add("<td>{$value['amino']}</td>
					<td>{$value['codon']}</td>
					<td>{$value['sift']}</td>
					<td>{$value['polyphen']}</td>".$listsymbol."</tr>");
					}else{
						add("<td>".count($value['amino'])."</td>
					<td>".count($value['codon'])."</td>
					<td>".implode(", ", $value['sift'])."</td>
					<td>".implode(", ", $value['polyphen'])."</td>".$listsymbol."</tr>");
					}
				}
				add("</table><br/>");
			}
			if(array_key_exists('miRNA', $subf)){
				add("<table class='annotation'><tr><th class='duper' colspan='5'>Mature miRNA variant</th></tr>
		<tr><th class='super'>miRNA gene</th><th class='super'>effect</th><th class='super'>affected transcript</th><th class='super'>RefSeq id</th><th class='super'>variant(s)</th></tr>");
				foreach($subf['miRNA'] as $key => $value){
					//Create SNP List
					$list = array();
					foreach($snparray as $rs => $snpdat){
						if(isset($snpdat['direct_transcript_effect']) && array_key_exists($key ,$snpdat['direct_transcript_effect'])){
							$list[$rs] = $snpdat['RSID'];
						}
					}
					$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
					
					//Print Line
					add("<tr><td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td><td>{$value['effect']}</td><td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td><td>{$value['refseq']}</td>".$listsymbol."</tr>");
				}
				add("</table><br/>");
			}
			if(array_key_exists('splice', $subf)){
				add("<table class='annotation'><tr><th class='duper' colspan='10'>Amino acid sequence alteration (splice region)</th></tr>
		<tr><th class='super'>gene</th><th class='super'>effect type</th><th class='super'>affected transcript</th><th class='super'>RefSeq id</th><th class='super'>protein</th><th class='super'>amino acid</th><th class='super'>codon</th><th class='super'>SIFT prediction</th><th class='super'>PolyPhen prediction</th><th class='super'>varian(s)</th></tr>");
				foreach($subf['splice'] as $key => $value){
					//Create SNP List
					$list = array();
					foreach($snparray as $rs => $snpdat){
						if(isset($snpdat['direct_transcript_effect']) && array_key_exists($key ,$snpdat['direct_transcript_effect'])){
							$list[$rs] = $snpdat['RSID'];
						}
					}
					$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
					
					//Print Line
					$tmp = preg_split('/\s\(/', $value['effect']);
					add("<tr>
					<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
					<td>{$tmp[0]}</td>
					<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
					<td>{$value['refseq']}</td>
					<td>".((array_key_exists('ensp', $value)) ? ("{$value['ensp']}".(($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$value}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
					");
					if(!is_array($value['codon'])){
						add("<td>{$value['amino']}</td>
					<td>{$value['codon']}</td>
					<td>{$value['sift']}</td>
					<td>{$value['polyphen']}</td>".$listsymbol."</tr>");
					}else{
						add("<td>".count($value['amino'])."</td>
					<td>".count($value['codon'])."</td>
					<td>".implode(", ", $value['sift'])."</td>
					<td>".implode(", ", $value['polyphen'])."</td>".$listsymbol."</tr>");
					}
				}
				add("</table><br/>");
			}
		}
		add("</div>");
	}

	
	// direct regulatory
	if(array_key_exists('cis-eQTL', $functional) || array_key_exists('trans-eQTL', $functional)){
		add("<div class='annotation-section'>");
		add("<h2 class='efftype'>Direct effect on regulation</h2>");
		
		// cis-eQTL
		if(array_key_exists('cis-eQTL', $functional)){
			add("<table class='annotation'><tr><th class='duper' colspan='7'><i>cis</i>-eQTL</th></tr>
		<tr><th class='super'>gene</th><th class='super'>transcript</th><th class='super'>probe</th><th class='super'>tissue</th><th class='super'>min(statistic) (type)</th><th class='super'>source</th><th class='super'>variant(s)</th></tr>");
			foreach($functional['cis-eQTL'] as $study => $starr){
							
				foreach($functional['cis-eQTL'][$study] as $key => $value){
					add("<tbody>");
					if($key==="link" || $key==="source"){
						continue;
					}
					$tsc = count($value['ProbeData']);
					$tic = count($value['ProbeStats']);
					$rsp = max($tsc, $tic);
					$probestring = "?";
					if($key != "?"){
						$probestring = $key." <a href='http://www.ensembl.org/Homo_sapiens/Location/Genome?fdb=funcgen;ftype=ProbeFeature;id=$key' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='$key @ EnsEMBL' /></a>";
					}
					if($tsc == 0){
						$tirsp = 1;
						$tik = array_keys($value['ProbeStats']);
						//Create SNP List
						$list = array();
						foreach($snparray as $rs => $snpdat){
							if(isset($snpdat['cis-eQTL'][$study]) && array_key_exists($key ,$snpdat['cis-eQTL'][$study])){
								$list[$rs] = $snpdat['RSID'];
							}
						}
						$listsymbol = "<td rowspan='$rsp' style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
						
						//Print Line
						add(sprintf("<tr><td rowspan='$rsp'>?</td><td rowspan='$rsp'>?</td><td rowspan='$rsp'>$probestring</td><td rowspan='$tirsp'>{$tik[0]}</td><td rowspan='$tirsp'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',minimum($value['ProbeStats'][$tik[0]]['stat']))." ({$value['ProbeStats'][$tik[0]]['stattype']})</td><td rowspan='$rsp'>{$functional['cis-eQTL'][$study]['source']} <a href='{$functional['cis-eQTL'][$study]['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>".$listsymbol."</tr>", preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',minimum($value['ProbeStats'][$tik[0]]['stat']))));
						for($i=1; $i<$tic; $i++){
							add(sprintf("<tr><td rowspan='$tirsp'>{$tik[$i]}</td><td rowspan='$tirsp'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',minimum($value['ProbeStats'][$tik[$i]]['stat']))." ({$value['ProbeStats'][$tik[$i]]['stattype']})</td></tr>", preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',minimum($value['ProbeStats'][$tik[$i]]['stat']))));
						}
					}else{
						$tsrsp = 1;
						$tirsp = 1;
						$tik = array_keys($value['ProbeStats']);
						$tsk = array_keys($value['ProbeData']);
						for($i=0; ($i<$tic || $i<$tsc); $i++){
							add("<tr>");
							if($i<$tsc-1){
								add("<td rowspan='$tsrsp'>{$value['ProbeData'][$tsk[$i]]['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['ProbeData'][$tsk[$i]]['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ProbeData'][$tsk[$i]]['gene_symbol']} @ EnsEMBL' /></a></td><td rowspan='$tsrsp'>{$tsk[$i]} ".(($tsk[$i]=="?") ? "" : "<a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$tsk[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$tsk[$i]} @ EnsEMBL' /></a>")."</td>");
							}
							if($i==$tsc-1){
								add("<td rowspan='".($rsp-$i)."'>{$value['ProbeData'][$tsk[$i]]['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['ProbeData'][$tsk[$i]]['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ProbeData'][$tsk[$i]]['gene_symbol']} @ EnsEMBL' /></a></td><td rowspan='".($rsp-$i)."'>{$tsk[$i]} ".(($tsk[$i]=="?") ? "" : "<a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$tsk[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$tsk[$i]} @ EnsEMBL' /></a>")."</td>");
							}
							if($i==0){
								add("<td rowspan='$rsp'>$probestring</td>");
							}
							if($i<$tic-1){
								//Create SNP List
								$list = array();
								foreach($snparray as $rs => $snpdat){
									if(isset($snpdat['cis-eQTL'][$study]) && array_key_exists($key ,$snpdat['cis-eQTL'][$study])){
										$list[$rs] = $snpdat['RSID'];
									}
								}
								$listsymbol = "<td rowspan='$tirsp' style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
								
								//Print Line
								add(sprintf("<td rowspan='$tirsp'>{$tik[$i]}</td><td rowspan='$tirsp'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',minimum($value['ProbeStats'][$tik[$i]]['stat']))." ({$value['ProbeStats'][$tik[$i]]['stattype']})</td><td rowspan='".$tirsp."'>{$functional['cis-eQTL'][$study]['source']} <a href='{$functional['cis-eQTL'][$study]['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>".$listsymbol, preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',minimum($value['ProbeStats'][$tik[$i]]['stat']))));
							}
							if($i==$tic-1){
								//Create SNP List
								$list = array();
								foreach($snparray as $rs => $snpdat){
									if(isset($snpdat['cis-eQTL'][$study]) && array_key_exists($key ,$snpdat['cis-eQTL'][$study])){
										$list[$rs] = $snpdat['RSID'];
									}
								}
								$listsymbol = "<td rowspan='".($rsp-$i)."' style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
								
								//Print Line
								add(sprintf("<td rowspan='".($rsp-$i)."'>{$tik[$i]}</td><td rowspan='".($rsp-$i)."'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',minimum($value['ProbeStats'][$tik[$i]]['stat']))." ({$value['ProbeStats'][$tik[$i]]['stattype']})</td><td rowspan='".($rsp-$i)."'>{$functional['cis-eQTL'][$study]['source']} <a href='{$functional['cis-eQTL'][$study]['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>".$listsymbol, preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',minimum($value['ProbeStats'][$tik[$i]]['stat']))));
							}
							add("</tr>");
						}
					}
					add("</tbody>");
				}
			}
			add("</table><br/>");
		}
		
		// trans-eQTL
		if(array_key_exists('trans-eQTL', $functional)){
			add("<table class='annotation'><tr><th class='duper' colspan='8'><i>trans</i>-eQTL</th></tr>
		<tr><th class='super'>gene</th><th class='super'>transcript</th><th class='super'>probe</th><th class='super'>chromosome</th><th class='super'>tissue</th><th class='super'>min(statistic) (type)</th><th class='super'>source</th><th class='super'>variant(s)</th></tr>");
			foreach($functional['trans-eQTL'] as $study => $starr){
							
				foreach($functional['trans-eQTL'][$study] as $key => $value){
					add("<tbody>");
					if($key==="link" || $key==="source"){
						continue;
					}
					$tsc = count($value['ProbeData']);
					$tic = count($value['ProbeStats']);
					$rsp = max($tsc, $tic);
					$probestring = "?";
					if($key != "?"){
						$probestring = $key." <a href='http://www.ensembl.org/Homo_sapiens/Location/Genome?fdb=funcgen;ftype=ProbeFeature;id=$key' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='$key @ EnsEMBL' /></a>";
					}
					if($tsc == 0){
						$tirsp = 1;
						$tik = array_keys($value['ProbeStats']);
						//Create SNP List
						$list = array();
						foreach($snparray as $rs => $snpdat){
							if(isset($snpdat['trans-eQTL'][$study]) && array_key_exists($key ,$snpdat['trans-eQTL'][$study])){
								$list[$rs] = $snpdat['RSID'];
							}
						}
						$listsymbol = "<td rowspan='$rsp' style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
						
						//Print Line
						add(sprintf("<tr><td rowspan='$rsp'>?</td><td rowspan='$rsp'>?</td><td rowspan='$rsp'>$probestring</td><td rowspan='$rsp'>{$value['ProbeStats'][$tik[0]]['chromosome']}</td><td rowspan='$tirsp'>{$tik[0]}</td><td rowspan='$tirsp'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',minimum($value['ProbeStats'][$tik[0]]['stat']))." ({$value['ProbeStats'][$tik[0]]['stattype']})</td><td rowspan='$rsp'>{$functional['trans-eQTL'][$study]['source']} <a href='{$functional['trans-eQTL'][$study]['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>".$listsymbol."</tr>", preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',minimum($value['ProbeStats'][$tik[0]]['stat']))));
						for($i=1; $i<$tic; $i++){
							add(sprintf("<tr><td rowspan='$tirsp'>{$tik[$i]}</td><td rowspan='$tirsp'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',minimum($value['ProbeStats'][$tik[$i]]['stat']))." ({$value['ProbeStats'][$tik[$i]]['stattype']})</td></tr>", preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',minimum($value['ProbeStats'][$tik[$i]]['stat']))));
						}
					}else{
						$tsrsp = 1;
						$tirsp = 1;
						$tik = array_keys($value['ProbeStats']);
						$tsk = array_keys($value['ProbeData']);
						for($i=0; ($i<$tic || $i<$tsc); $i++){
							add("<tr>");
							if($i<$tsc-1){
								add("<td rowspan='$tsrsp'>{$value['ProbeData'][$tsk[$i]]['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['ProbeData'][$tsk[$i]]['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ProbeData'][$tsk[$i]]['gene_symbol']} @ EnsEMBL' /></a></td><td rowspan='$tsrsp'>{$tsk[$i]} ".(($tsk[$i]=="?") ? "" : "<a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$tsk[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$tsk[$i]} @ EnsEMBL' /></a>")."</td>");
							}
							if($i==$tsc-1){
								add("<td rowspan='".($rsp-$i)."'>{$value['ProbeData'][$tsk[$i]]['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['ProbeData'][$tsk[$i]]['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ProbeData'][$tsk[$i]]['gene_symbol']} @ EnsEMBL' /></a></td><td rowspan='".($rsp-$i)."'>{$tsk[$i]} ".(($tsk[$i]=="?") ? "" : "<a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$tsk[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$tsk[$i]} @ EnsEMBL' /></a>")."</td>");
							}
							if($i==0){
								add("<td rowspan='$rsp'>$probestring</td><td rowspan='$rsp'>{$value['ProbeStats'][$tik[$i]]['chromosome']}</td>");
							}
							if($i<$tic-1){
								//Create SNP List
								$list = array();
								foreach($snparray as $rs => $snpdat){
									if(isset($snpdat['trans-eQTL'][$study]) && array_key_exists($key ,$snpdat['trans-eQTL'][$study])){
										$list[$rs] = $snpdat['RSID'];
									}
								}
								$listsymbol = "<td rowspan='$tirsp' style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
								
								//Print Line
								add(sprintf("<td rowspan='$tirsp'>{$tik[$i]}</td><td rowspan='$tirsp'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',minimum($value['ProbeStats'][$tik[$i]]['stat']))." ({$value['ProbeStats'][$tik[$i]]['stattype']})</td><td rowspan='".$tirsp."'>{$functional['trans-eQTL'][$study]['source']} <a href='{$functional['trans-eQTL'][$study]['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>".$listsymbol, preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',minimum($value['ProbeStats'][$tik[$i]]['stat']))));
							}
							if($i==$tic-1){
								//Create SNP List
								$list = array();
								foreach($snparray as $rs => $snpdat){
									if(isset($snpdat['trans-eQTL'][$study]) && array_key_exists($key ,$snpdat['trans-eQTL'][$study])){
										$list[$rs] = $snpdat['RSID'];
									}
								}
								$listsymbol = "<td rowspan='".($rsp-$i)."' style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
								
								//Print Line
								add(sprintf("<td rowspan='".($rsp-$i)."'>{$tik[$i]}</td><td rowspan='".($rsp-$i)."'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',minimum($value['ProbeStats'][$tik[$i]]['stat']))." ({$value['ProbeStats'][$tik[$i]]['stattype']})</td><td rowspan='".($rsp-$i)."'>{$functional['trans-eQTL'][$study]['source']} <a href='{$functional['trans-eQTL'][$study]['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>".$listsymbol, preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',minimum($value['ProbeStats'][$tik[$i]]['stat']))));
							}
							add("</tr>");
						}
					}
					add("</tbody>");
				}
			}
			add("</table><br/>");
		}
		
		add("</div>");
	}


	// putative regulatory
	if(array_key_exists('putative_regulatory_effect', $functional) || array_key_exists('variation_proximal_to_gene', $functional)){
		add("<div class='annotation-section'>");
		add("<h2 class='efftype'>Putative effect on regulation</h2>");
		
		// TFBS
		if(array_key_exists('TFBS_variant', $functional['putative_regulatory_effect'])){
			add("<table class='annotation'><tr><th class='duper' colspan='6'>Transcription factor binding site variation</th></tr>
			<tr><th class='super'>transcription factor</th><th class='super'>binding motif</th><th class='super'>motif position</th><th class='super'>highly informative position</th><th class='super'>score change</th><th class='super'>variant(s)</th></tr>");
			foreach($functional['putative_regulatory_effect']['TFBS_variant'] as $key => $value){
				//Create SNP List
				$list = array();
				foreach($snparray as $rs => $snpdat){
					if(isset($snpdat['putative_regulatory_effect']['TFBS_variant']) && array_key_exists($key ,$snpdat['putative_regulatory_effect']['TFBS_variant'])){
						$list[$rs] = $snpdat['RSID'];
					}
				}
				$listsymbol = "<td><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
				
				if(is_array($value['motif_position'])){
					$value['motif_position'] = implode(",", $value['motif_position']);
				}
				//Print Line
				add(sprintf("<tr><td>".str_replace(",", ", ", $value['TF'])."</td><td>$key</td><td>{$value['motif_position']}</td><td>{$value['HI_position']}</td><td>%.3f</td>".$listsymbol."</tr>", $value['score_change']));
			}
			add("</table><br/>");
		}
		
		// FANTOM5
		if(!empty($functional['putative_regulatory_effect']['regulatory_fantom5'])){
			$ffcp = preg_grep("/FFCP/", array_keys($functional['putative_regulatory_effect']['regulatory_fantom5']));
			$ffce = preg_grep("/FFCE/", array_keys($functional['putative_regulatory_effect']['regulatory_fantom5']));
			
			if(!empty($ffcp)){
				add("<table class='annotation'><tr><th class='duper' colspan='4'>FANTOM5 expressed promoter</th></tr>
				<tr><th class='super'>SNiPA promoter id</th><th class='super'>variant(s)</th><th class='super'>associated transcript(s)</th><th class='super'>gene</th></tr>");
				foreach($ffcp as $key => $value){
					add("<tbody>");
					$cc = count($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data']);
					$ckey = array_keys($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data']);
					//Create SNP List
					$list = array();
					foreach($snparray as $rs => $snpdat){
						if(isset($snpdat['putative_regulatory_effect']['regulatory_fantom5']) && array_key_exists($value ,$snpdat['putative_regulatory_effect']['regulatory_fantom5'])){
							$list[$rs] = $snpdat['RSID'];
						}
					}
					$listsymbol = "<td rowspan='$cc' style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
					
					//Print Line
					add("<tr><td rowspan='$cc'>$value <a href='{$functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['link']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='region @ EnsEMBL' /></a></td>".$listsymbol."<td>");
					$tgen = array();
					foreach($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data'][$ckey[0]] as $k => $v){
						array_push($tgen, $k." <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$k}'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$k} @ EnsEMBL' /></a>");
					}
					add(join(", ",$tgen));
					add("</td><td>{$v} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$ckey[0]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$v} @ EnsEMBL' /></a></td></tr>");
					
					for($i = 1; $i < $cc; $i++){
						add("<tr><td>");
						$tgen = array();
						foreach($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data'][$ckey[$i]] as $k => $v){
							array_push($tgen, $k." <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$k}'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$k} @ EnsEMBL' /></a>");
						}
						add(join(", ",$tgen));
						add("</td><td>{$v} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$ckey[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$v} @ EnsEMBL' /></a></td></tr>");
					}
					add("</tbody>");
				}
				add("</table><br/>");
			}		
			
			if(!empty($ffce)){
				add("<table class='annotation'><tr><th class='duper' colspan='4'>FANTOM5 expressed enhancer</th></tr>
				<tr><th class='super'>SNiPA enhancer id</th><th class='super'>variant(s)</th><th class='super'>associated transcript(s)</th><th class='super'>gene</th></tr>");
				foreach($ffce as $key => $value){
					add("<tbody>");
					$cc = count($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data']);
					$ckey = array_keys($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data']);
					//Create SNP List
					$list = array();
					foreach($snparray as $rs => $snpdat){
						if(isset($snpdat['putative_regulatory_effect']['regulatory_fantom5']) && array_key_exists($value ,$snpdat['putative_regulatory_effect']['regulatory_fantom5'])){
							$list[$rs] = $snpdat['RSID'];
						}
					}
					$listsymbol = "<td rowspan='$cc' style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
					
					//Print Line
					add("<tr><td rowspan='$cc'>$value <a href='{$functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['link']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='region @ EnsEMBL' /></a></td>".$listsymbol."<td>");
					$tgen = array();
					foreach($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data'][$ckey[0]] as $k => $v){
						array_push($tgen, $k." <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$k}'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$k} @ EnsEMBL' /></a>");
					}
					add(join(", ",$tgen));
					add("</td><td>{$v} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$ckey[0]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$v} @ EnsEMBL' /></a></td></tr>");
					
					for($i = 1; $i < $cc; $i++){
						add("<tr><td>");
						$tgen = array();
						foreach($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data'][$ckey[$i]] as $k => $v){
							array_push($tgen, $k." <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$k}'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$k} @ EnsEMBL' /></a>");
						}
						add(join(", ",$tgen));
						add("</td><td>{$v} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$ckey[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$v} @ EnsEMBL' /></a></td></tr>");
					}
					add("</tbody>");
				}
				add("</table><br/>");
			}
		}

		// encode DHS
		if(!empty($functional['putative_regulatory_effect']['regulatory_encode'])){
			$encp = preg_grep("/ENCP/", array_keys($functional['putative_regulatory_effect']['regulatory_encode']));
			$ence = preg_grep("/ENCE/", array_keys($functional['putative_regulatory_effect']['regulatory_encode']));
			
			if(!empty($encp)){
				add("<table class='annotation'><tr><th class='duper' colspan='3'>ENCODE promoter-associated DHS</th></tr>
				<tr><th class='super'>SNiPA promoter id</th><th class='super'>variant(s)</th><th class='super'>associated gene(s)</th></tr>");
				foreach($encp as $key => $value){
					add("<tbody>");
					$cc = count($functional['putative_regulatory_effect']['regulatory_encode'][$value]['data']);
					$ckey = array_keys($functional['putative_regulatory_effect']['regulatory_encode'][$value]['data']);
					//Create SNP List
					$list = array();
					foreach($snparray as $rs => $snpdat){
						if(isset($snpdat['putative_regulatory_effect']['regulatory_encode']) && array_key_exists($value ,$snpdat['putative_regulatory_effect']['regulatory_encode'])){
							$list[$rs] = $snpdat['RSID'];
						}
					}
					$listsymbol = "<td rowspan='$cc' style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
					
					//Print Line
					add("<tr><td rowspan='$cc'>$value <a href='{$functional['putative_regulatory_effect']['regulatory_encode'][$value]['link']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='region @ EnsEMBL' /></a></td>".$listsymbol."<td>{$functional['putative_regulatory_effect']['regulatory_encode'][$value]['data'][$ckey[0]]} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$ckey[0]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$functional['putative_regulatory_effect']['regulatory_encode'][$value]['data'][$ckey[0]]} @ EnsEMBL' /></a></td></tr>");
					for($i = 1; $i < $cc; $i++){
						add("<tr><td>{$functional['putative_regulatory_effect']['regulatory_encode'][$value]['data'][$ckey[$i]]} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$ckey[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$functional['putative_regulatory_effect']['regulatory_encode'][$value]['data'][$ckey[$i]]} @ EnsEMBL' /></a></td></tr>");
					}
					add("</tbody>");
				}
				add("</table><br/>");
			}		
			
			if(!empty($ence)){
				add("<table class='annotation'><tr><th class='duper' colspan='4'>ENCODE promoter-associated distal DHS (Enhancer)</th></tr>
				<tr><th class='super'>SNiPA enhancer id</th><th class='super'>variant(s)</th><th class='super'>associated SNiPA promoter id</th><th class='super'>associated gene(s)</th></tr>");
				foreach($ence as $key => $value){
					add("<tbody>");
					$cc = count($functional['putative_regulatory_effect']['regulatory_encode'][$value]['data']);
					$ckey = array_keys($functional['putative_regulatory_effect']['regulatory_encode'][$value]['data']);
					//Create SNP List
					$list = array();
					foreach($snparray as $rs => $snpdat){
						if(isset($snpdat['putative_regulatory_effect']['regulatory_encode']) && array_key_exists($value ,$snpdat['putative_regulatory_effect']['regulatory_encode'])){
							$list[$rs] = $snpdat['RSID'];
						}
					}
					$listsymbol = "<td rowspan='$cc' style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
					
					//Print Line
					add("<tr><td rowspan='$cc'>$value <a href='{$functional['putative_regulatory_effect']['regulatory_encode'][$value]['link']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='region @ EnsEMBL' /></a></td>".$listsymbol."<td>{$ckey[0]}</td><td>");
					$tgen = array();
					foreach($functional['putative_regulatory_effect']['regulatory_encode'][$value]['data'][$ckey[0]] as $k => $v){
						array_push($tgen, $v." <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$k}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$v} @ EnsEMBL' /></a>");
					}
					add(join("<br/>",$tgen));
					add("</td></tr>");
					
					for($i = 1; $i < $cc; $i++){
						add("<tr><td>$ckey[$i]</td><td>");
						$tgen = array();
						foreach($functional['putative_regulatory_effect']['regulatory_encode'][$value]['data'][$ckey[$i]] as $k => $v){
							array_push($tgen, $v." <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$k}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$v} @ EnsEMBL' /></a>");
						}
						add(join("<br/>",$tgen));
						add("</td></tr>");
						add("</td></tr>");
					}
					add("</tbody>");
				}
				add("</table><br/>");
			}
		}
		
		// regulatory cluster
		if(!empty($functional['putative_regulatory_effect']['regulatory'])){
			add("<table class='annotation'><tr><th class='duper' colspan='4'>Regulatory feature cluster</th></tr>
			<tr><th class='super'>element id</th><th class='super'>variant(s)</th><th class='super'>tissue/cell</th><th class='super'>factors</th></tr>");
			foreach($functional['putative_regulatory_effect']['regulatory'] as $value => $key){
				add("<tbody>");
				$cc = count($functional['putative_regulatory_effect']['regulatory'][$value]['data']);
				$ckey = array_keys($functional['putative_regulatory_effect']['regulatory'][$value]['data']);
				//Create SNP List
				$list = array();
				foreach($snparray as $rs => $snpdat){
					if(isset($snpdat['putative_regulatory_effect']['regulatory']) && array_key_exists($value ,$snpdat['putative_regulatory_effect']['regulatory'])){
						$list[$rs] = $snpdat['RSID'];
					}
				}
				$listsymbol = "<td rowspan='$cc' style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
				
				//Print Line
				add("<tr><td rowspan='$cc'>$value  <a href='http://www.ensembl.org/Homo_sapiens/Regulation/Cell_line?rf={$value}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value} @ EnsEMBL' /></a>".((array_key_exists("biotype", $functional['putative_regulatory_effect']['regulatory'][$value])) ? "<br/>(".str_replace("_", " ", $functional['putative_regulatory_effect']['regulatory'][$value]['biotype']).")" : "")."</td>".$listsymbol."<td>{$ckey[0]}</td><td>{$functional['putative_regulatory_effect']['regulatory'][$value]['data'][$ckey[0]]}</td></tr>");
				for($i = 1; $i < $cc; $i++){
					add("<tr><td>$ckey[$i]</td><td>{$functional['putative_regulatory_effect']['regulatory'][$value]['data'][$ckey[$i]]}</td></tr>");
				}
				add("</tbody>");
			}
			add("</table><br/>");
		}
		
		// mirTS
		if(!empty($functional['putative_regulatory_effect']['mirTS'])){
			add("<table class='annotation'><tr><th class='duper' colspan='7'>Variation in RISC binding site</th></tr>
			<tr><th class='super'>gene</th><th class='super'>variant(s)</th><th class='super'>affected transcript(s)</th><th class='super'>targeting miRNA(s)</th></tr>");
			foreach($functional['putative_regulatory_effect']['mirTS'] as $key => $value){
				//Create SNP List
				$list = array();
				foreach($snparray as $rs => $snpdat){
					if(isset($snpdat['putative_regulatory_effect']['mirTS']) && array_key_exists($key ,$snpdat['putative_regulatory_effect']['mirTS'])){
						$list[$rs] = $snpdat['RSID'];
					}
				}
				$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
				
				//Print Line
				add("<tr><td>$key <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>".$listsymbol);
				#add TS links
				if(!(is_array($value['transcripts']))){
					$tarts = explode(", ", $value['transcripts']);
					array_walk($tarts, function(&$el){ $el = "{$el} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$el}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$el} @ EnsEMBL' /></a>";});
					asort($tarts);
					$str = implode("<br/>",$tarts);
					add("<td>{$str}</td>");
				}else{
					$uniquets = array();
					foreach($value['transcripts'] as $key => $tstr){
						$tarts = explode(", ", $tstr);
						$uniquets = array_merge($uniquets, $tarts);
					}
					$uniquets = array_unique($uniquets);
					array_walk($uniquets, function(&$el){ $el = "{$el} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$el}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$el} @ EnsEMBL' /></a>";});
					asort($uniquets);
					$str = implode("<br/>",$uniquets);
					add("<td>{$str}</td>");
				}
				#add miRNA links
				if(!(is_array($value['mirnas']))){
					$tarts = explode(", ", $value['mirnas']);
					array_walk($tarts, function(&$el){ $el = "{$el} <a href='http://mirbase.org/cgi-bin/query.pl?terms={$el}' target='_blank'><img src='frontend/img/mirbase.png' alt='miRBase' title='{$el} @ miRBase' /></a>";});
					asort($tarts);
					$str = implode("<br/>",$tarts);
					add("<td>{$str}</td>");
				}else{
					$uniquets = array();
					foreach($value['mirnas'] as $key => $tstr){
						$tarts = explode(", ", $tstr);
						$uniquets = array_merge($uniquets, $tarts);
					}
					$uniquets = array_unique($uniquets);
					array_walk($uniquets, function(&$el){ $el = "{$el} <a href='http://mirbase.org/cgi-bin/query.pl?terms={$el}' target='_blank'><img src='frontend/img/mirbase.png' alt='miRBase' title='{$el} @ miRBase' /></a>";});
					asort($uniquets);
					$str = implode("<br/>",$uniquets);
					add("<td>{$str}</td>");
				}
			}
			add("</table><br/>");
		}
		
		// variation proximal to gene
		if(array_key_exists('variation_proximal_to_gene', $functional)){
			uasort($functional['variation_proximal_to_gene'], 'sortByGene');
			add("<table class='annotation'><tr><th class='duper' colspan='7'>Variation proximal to gene</th></tr>
			<tr>
			<th class='super'>gene</th>
			<th class='super'>variant type</th>
			<th class='super'>min(distance)</th>
			<th class='super'>transcript</th>
			<th class='super'>RefSeq id</th>
			<th class='super'>protein</th>
			<th class='super'>variant(s)</th></tr>");
			foreach($functional['variation_proximal_to_gene'] as $value => $key){
				//Create SNP List
				$list = array();
				foreach($snparray as $rs => $snpdat){
					if(isset($snpdat['variation_proximal_to_gene']) && array_key_exists($value ,$snpdat['variation_proximal_to_gene'])){
						$list[$rs] = $snpdat['RSID'];
					}
				}
				$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
				
				//Print Line
				add("<tr>
				<td>{$key['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key['gene_symbol']} @ EnsEMBL' /></a></td>
				<td>".((is_array($key['effect'])) ? implode(", ",$key['effect']) : $key['effect'])."</td>
				<td>".minimum($key['distance'])."</td>
				<td>{$value} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$value}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value} @ EnsEMBL' /></a></td>
				<td>{$key['refseq']}</td>
				<td>".((array_key_exists('ensp', $key)) ? ("{$key['ensp']}".(($key['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$value}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
				".$listsymbol."</tr>");
			}
			add("</table><br/>");
		}
		
		add("</div>");
	}

	//putative transcript
	if(array_key_exists('putative_transcript_effect', $functional)){
		uasort($functional['putative_transcript_effect'], 'sortByGene');
		add("<div class='annotation-section'>");
		add("<h2 class='efftype'>Putative effect on transcript</h2>");
		$efftype = 'putative_transcript_effect';
		$subf = array();
		
		if($subf = getEffectArray($functional,$efftype,'/synonymous/')){
			add("<table class='annotation'><tr><th class='duper' colspan='8'>Synonymous coding variant</th></tr>
			<tr>
			<th class='super'>gene</th>
			<th class='super'>affected transcript</th>
			<th class='super'>RefSeq id</th>
			<th class='super'>protein</th>
			<th class='super'>AA's</th>
			<th class='super'>exchanged codons</th>
			<th class='super'>variant(s)</th></tr>");
			foreach($subf[$efftype] as $key => $value){
				//Create SNP List
				$list = array();
				foreach($snparray as $rs => $snpdat){
					if(isset($snpdat[$efftype]) && array_key_exists($key ,$snpdat[$efftype]) && getEffectArray($snpdat,$efftype,'/synonymous/')){
						$list[$rs] = $snpdat['RSID'];
					}
				}
				$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
				
				//Print Line
				add("<tr>
				<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
				<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
				<td>{$value['refseq']}</td>
				<td>".((array_key_exists('ensp', $value)) ? ("{$value['ensp']}".(($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
				");
				if(!is_array($value['codon'])){
						add("<td>{$value['amino']}</td>
					<td>{$value['codon']}</td>");
					}else{
						add("<td>".count($value['amino'])."</td>
					<td>".count($value['codon'])."</td>");
					}
				add($listsymbol."</tr>");
			}
			add("</table><br/>");
		}
		if($subf = getEffectArray($functional,$efftype,'/intron.+splice region/')){
			add("<table class='annotation'><tr><th class='duper' colspan='7'>Intron variant (splice region)</th></tr>
			<tr><th class='super'>gene</th>
			<th class='super'>affected transcript</th>
			<th class='super'>RefSeq id</th>
			<th class='super'>protein</th>
			<th class='super'>variant(s)</th></tr>");
			foreach($subf[$efftype] as $key => $value){
				//Create SNP List
				$list = array();
				foreach($snparray as $rs => $snpdat){
					if(isset($snpdat[$efftype]) && array_key_exists($key ,$snpdat[$efftype]) && getEffectArray($snpdat,$efftype,'/intron.+splice region/')){
						$list[$rs] = $snpdat['RSID'];
					}
				}
				$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
				
				//Print Line
				add("<tr>
				<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
				<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
				<td>{$value['refseq']}</td>
				<td>".((array_key_exists('ensp', $value)) ? ("{$value['ensp']}".(($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
				".$listsymbol."</tr>");
			}
			add("</table><br/>");
		}
		if($subf = getEffectArray($functional,$efftype,'/3 prime.+splice region/')){
			add("<table class='annotation'><tr><th class='duper' colspan='5'>3'-UTR variant (splice region)</th></tr>
			<tr><th class='super'>gene</th>
			<th class='super'>affected transcript</th>
			<th class='super'>RefSeq id</th>
			<th class='super'>protein</th>
			<th class='super'>variant(s)</th></tr>");
			foreach($subf[$efftype] as $key => $value){
				//Create SNP List
				$list = array();
				foreach($snparray as $rs => $snpdat){
					if(isset($snpdat[$efftype]) && array_key_exists($key ,$snpdat[$efftype]) && getEffectArray($snpdat,$efftype,'/3 prime.+splice region/')){
						$list[$rs] = $snpdat['RSID'];
					}
				}
				$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
				
				//Print Line
				add("<tr>
				<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
				<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
				<td>{$value['refseq']}</td>
				<td>".((array_key_exists('ensp', $value)) ? ("{$value['ensp']}".(($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
				".$listsymbol."</tr>");
			}
			add("</table><br/>");
		}
		if($subf = getEffectArray($functional,$efftype,'/5 prime.+splice region/')){
			add("<table class='annotation'><tr><th class='duper' colspan='5'>5'-UTR variant (splice region)</th></tr>
			<tr><th class='super'>gene</th>
			<th class='super'>affected transcript</th>
			<th class='super'>RefSeq id</th>
			<th class='super'>protein</th>
			<th class='super'>variant(s)</th></tr>");
			foreach($subf[$efftype] as $key => $value){
				//Create SNP List
				$list = array();
				foreach($snparray as $rs => $snpdat){
					if(isset($snpdat[$efftype]) && array_key_exists($key ,$snpdat[$efftype]) && getEffectArray($snpdat,$efftype,'/5 prime.+splice region/')){
						$list[$rs] = $snpdat['RSID'];
					}
				}
				$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
				
				//Print Line
				add("<tr>
				<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
				<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
				<td>{$value['refseq']}</td>
				<td>".((array_key_exists('ensp', $value)) ? ("{$value['ensp']}".(($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
				".$listsymbol."</tr>");
			}
			add("</table><br/>");
		}
		if($subf = getEffectArray($functional,$efftype,'/non coding exon.+splice region/')){
			add("<table class='annotation'><tr><th class='duper' colspan='4'>Non-coding exon variant (splice region)</th></tr>
			<tr><th class='super'>gene</th>
			<th class='super'>affected transcript</th>
			<th class='super'>RefSeq id</th>
			<th class='super'>variant(s)</th></tr>");
			foreach($subf[$efftype] as $key => $value){
				//Create SNP List
				$list = array();
				foreach($snparray as $rs => $snpdat){
					if(isset($snpdat[$efftype]) && array_key_exists($key ,$snpdat[$efftype]) && getEffectArray($snpdat,$efftype,'/non coding exon.+splice region/')){
						$list[$rs] = $snpdat['RSID'];
					}
				}
				$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
				
				//Print Line
				add("<tr>
				<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
				<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
				<td>{$value['refseq']}</td>
				".$listsymbol."</tr>");
			}
			add("</table><br/>");
		}
		if($subf = getEffectArray($functional,$efftype,'/intron variant$/')){
			add("<table class='annotation'><tr><th class='duper' colspan='6'>Intron variant</th></tr>
			<tr><th class='super'>gene</th>
			<th class='super'>affected transcript</th>
			<th class='super'>RefSeq id</th>
			<th class='super'>protein</th>
			<th class='super'>variant(s)</th></tr>");
			foreach($subf[$efftype] as $key => $value){
				//Create SNP List
				$list = array();
				foreach($snparray as $rs => $snpdat){
					if(isset($snpdat[$efftype]) && array_key_exists($key ,$snpdat[$efftype]) && getEffectArray($snpdat,$efftype,'/intron variant$/')){
						$list[$rs] = $snpdat['RSID'];
					}
				}
				$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
				
				//Print Line
				add("<tr>
				<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
				<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
				<td>{$value['refseq']}</td>
				<td>".((array_key_exists('ensp', $value)) ? ("{$value['ensp']}".(($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
				".$listsymbol."</tr>");
			}
			add("</table><br/>");
		}
		if($subf = getEffectArray($functional,$efftype,'/3 prime.+variant$/')){
			add("<table class='annotation'><tr><th class='duper' colspan='5'>3'-UTR variant</th></tr>
			<tr>
			<th class='super'>gene</th>
			<th class='super'>affected transcript</th>
			<th class='super'>RefSeq id</th>
			<th class='super'>protein</th>
			<th class='super'>variant(s)</th></tr>");
			foreach($subf[$efftype] as $key => $value){
				//Create SNP List
				$list = array();
				foreach($snparray as $rs => $snpdat){
					if(isset($snpdat[$efftype]) && array_key_exists($key ,$snpdat[$efftype]) && getEffectArray($snpdat,$efftype,'/3 prime.+variant$/')){
						$list[$rs] = $snpdat['RSID'];
					}
				}
				$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
				
				//Print Line
				add("<tr>
				<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
				<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
				<td>{$value['refseq']}</td>
				<td>".((array_key_exists('ensp', $value)) ? ("{$value['ensp']}".(($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
				".$listsymbol."</tr>");
			}
			add("</table><br/>");
		}
		if($subf = getEffectArray($functional,$efftype,'/5 prime.+variant$/')){
			add("<table class='annotation'><tr><th class='duper' colspan='5'>5'-UTR variant</th></tr>
			<tr>
			<th class='super'>gene</th>
			<th class='super'>affected transcript</th>
			<th class='super'>RefSeq id</th>
			<th class='super'>protein</th>
			<th class='super'>variant(s)</th></tr>");
			foreach($subf[$efftype] as $key => $value){
				//Create SNP List
				$list = array();
				foreach($snparray as $rs => $snpdat){
					if(isset($snpdat[$efftype]) && array_key_exists($key ,$snpdat[$efftype]) && getEffectArray($snpdat,$efftype,'/5 prime.+variant$/')){
						$list[$rs] = $snpdat['RSID'];
					}
				}
				$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
				
				//Print Line
				add("<tr>
				<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
				<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
				<td>{$value['refseq']}</td>
				<td>".((array_key_exists('ensp', $value)) ? ("{$value['ensp']}".(($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
				".$listsymbol."</tr>");
			}
			add("</table><br/>");
		}
		if($subf = getEffectArray($functional,$efftype,'/non coding exon.+variant$/')){
			add("<table class='annotation'><tr><th class='duper' colspan='4'>Non-coding exon variant</th></tr>
			<tr>
			<th class='super'>gene</th>
			<th class='super'>affected transcript</th>
			<th class='super'>RefSeq id</th>
			<th class='super'>variant(s)</th></tr>");
			foreach($subf[$efftype] as $key => $value){
				//Create SNP List
				$list = array();
				foreach($snparray as $rs => $snpdat){
					if(isset($snpdat[$efftype]) && array_key_exists($key ,$snpdat[$efftype]) && getEffectArray($snpdat,$efftype,'/non coding exon.+variant$/')){
						$list[$rs] = $snpdat['RSID'];
					}
				}
				$listsymbol = "<td style='vertical-align: center;'><span class='listwrap' onclick='showSnpListMenu(event,".json_encode($list).",".$PlotThis['CHR'].",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'><p class='list'>".count($list)."</p></span></td>";
				
				//Print Line
				add("<tr>
				<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
				<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
				<td>{$value['refseq']}</td>
				".$listsymbol."
				</tr>");
			}
			add("</table><br/>");
		}
		add("</div>");
	}

}
// minimum function that distinguishes between arrays and scalars
function minimum($mixed){
	return ((is_array($mixed)) ? min($mixed) : $mixed);
}

// function for retrieval of sub-arrays
function getEffectArray($array, $effectType, $pattern){
	$result = array();
	foreach($array[$effectType] as $key => $value){
		if(is_array($value['effect'])){
			foreach($value['effect'] as $ind => $subeffe){
				if(preg_match($pattern, $subeffe)){
					$value['effect'] = $subeffe;
					$result[$effectType][$key] = $value;
				}
			}
		}else if(preg_match($pattern, $value['effect'])){
			$result[$effectType][$key] = $value;
		}
	}
	if(empty($result)){
		return false;
	}
	return $result;
}

// function for retrieval of direct TS sub-arrays
function getDTSEffectArray($array){
	$effectType = 'direct_transcript_effect';
	$result = array();
	foreach($array[$effectType] as $key => $value){
		if(preg_match('/miRNA/', $value['effect'])){
			$result['miRNA'][$key] = $value;
		}else if(preg_match('/splice.+region/', $value['effect'])){
			$result['splice'][$key] = $value;
		}else{
			$result[$effectType][$key] = $value;
		}
	}
	if(empty($result)){
		return false;
	}
	return $result;
}
?>
