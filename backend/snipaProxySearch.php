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
$SnpsSentinels = $_POST['snps_sentinels'];
$SnpsGene = $_POST['snps_gene'];
$SnpsRegionChr = $_POST['snps_region_chr'];
$SnpsRegionBegin = $_POST['snps_region_begin'];
$SnpsRegionEnd = $_POST['snps_region_end'];
$Rsquare = $_POST['rsquare'];
$IncludeSentinels = $_POST['incl_sentinel'];
$IncludeFunctionalAnnotation = $_POST['incl_funcann'];
$DynamicTables = $_POST['dyn_tables'];
$Download = $_POST['download'];
$Pairwise = $_POST['pairwise'];

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
$status['totalstepnum'] = 7;
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
			$status['errmessage'] = "You have to enter at least one SNP. If you did so, check the syntax of your SNP and make sure that it is a valid rs identifier. If you entered multiple SNPs, make sure that they are separated by newlines.";
			$allok = FALSE;
		} else {
			$status['ok'] = "OK";
		}
	}  	
	
	// Ensembl Gene Identifier
	if ($SnpsInputType == "gene") {
		$SnpsGene = strtoupper(trim($SnpsGene));
		preg_replace('/\s+/','',$proxySnpsGene);
		if ((strlen($SnpsGene) == 0) || !(preg_match("/^ENSG[0-9]+/",$SnpsGene))) {
			$status['ok'] = "FAIL";
			$status['errmessage'] = "You have to enter a valid ENSEMBL gene identifier.";
			$allok = FALSE;
		} elseif (!(snipaGeneExists($Genomerelease,$Annotation,$SnpsGene))) { // Gibt es den Identifier in der DB?
			$status['ok'] = "FAIL";
			$status['errmessage'] = "The ENSEMBL gene identifier ".$SnpsGene." is not in the annotation release &quot;".$Annorel."&quot;.";
			$allok = FALSE;
		} else {
			$status['ok'] = "OK";
		}
	}  

	// Chromosomale Region
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
	$status['message'] = "Determine genetic positions of sentinel SNPs.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	$Sentinels = array();
	
	// einzelne SNPs
	if ($SnpsInputType == "snps") {
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
			unset($tmprsmapping);
		}
	}  	
	
	
	// Ensembl Gene Identifier
	if ($SnpsInputType == "gene") {
		$tmp = snipaGeneLocation($Genomerelease,$Annotation,$SnpsGene);
		foreach (snipaGetSelfinfo($Genomerelease,$Referenceset,$Population,$tmp[0]['CHR'],$tmp[0]['START'],$tmp[0]['STOP']) as $snp) {
			 $Sentinels[] = array('RSID' => $snp['RSID'], 'CHR' => $snp['CHR'], 'POS' => $snp['POS1']);
		}		
		unset($tmp);
	}  

	// Chromosomale Region
	if ($SnpsInputType == "region") {
		$tmp = snipaGetSelfinfo($Genomerelease,$Referenceset,$Population,$SnpsRegionChr,$SnpsRegionBegin,$SnpsRegionEnd);
		foreach ($tmp as $snp) {
			 $Sentinels[] = array('RSID' => $snp['RSID'], 'CHR' => $snp['CHR'], 'POS' => $snp['POS1']);
		}
		unset($tmp);		
	} 
	
	$status['ok'] = "OK";
	savePlotStatus($status,$JobDir."/status.txt");
}


// Funktionelle Annotationen aus Tabix abfragen
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Get functional annotations from database.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	if ($IncludeFunctionalAnnotation == 1) {
		
		// Setze die Felder und deren Default-Werte
				
		if (($SnpsInputType == "gene") || ($SnpsInputType == "region")) {
			$minpos = 10e10; $maxpos = 0;
			foreach($Sentinels as $tmp) {
				if ($tmp['POS'] < $minpos) { $minpos = $tmp['POS']; }
				if ($tmp['POS'] > $minpos) { $maxpos = $tmp['POS']; }
			}
			unset($tmp);
			$tabixannos = snipaGetSNPAnnotations($Genomerelease,$Annotation,$Sentinels[0]['CHR'],$minpos - 250000,$maxpos + 250000);
			// Formatiere Array um auf $funcan['CHR']['POS']['COMPEFFECTS|TS|RG|MOTIF']
			// Zweck: schnelleres Mergen mit Tabix-Arrays
			$Annotations = array();
			foreach ($tabixannos as $tabixanno) {
				$Annotations[$tabixanno['CHR']][$tabixanno['POS']]['COMPEFFECTS'] = str_replace(',', ', ', str_replace('_', '&nbsp;', $tabixanno['COMPEFFECTS']));
				// TEMP: ersetze TS,RG und MOTIF durch Werte aus dem PHP-Array
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
		} elseif (($SnpsInputType == "snps")) {
			$Annotations = array();
			foreach ($Sentinels as $entry) {
				if (empty($entry['CHR']) || empty($entry['POS'])) { continue; }
				set_time_limit(20);
				$tabixannos = snipaGetSNPAnnotations($Genomerelease,$Annotation,$entry['CHR'],$entry['POS']-250000,$entry['POS']+250000);
				foreach ($tabixannos as $tabixanno) {
					$Annotations[$tabixanno['CHR']][$tabixanno['POS']]['COMPEFFECTS'] = str_replace(',', ', ', str_replace('_', '&nbsp;', $tabixanno['COMPEFFECTS']));
					// TEMP: ersetze TS,RG und MOTIF durch Werte aus dem PHP-Array
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
			}
			unset($entry);
		}
	}
	$status['ok'] = "OK";
	savePlotStatus($status,$JobDir."/status.txt");
}


// Proxies-Abfrage aus Tabix, ggf. Annotationen mergen und Download-File erstellen
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Query proxy SNPs from database.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	$sentinelsTotal = count($Sentinels);
	$sentinelsCount = 0;
	
	// Fuer Download-Version den Header im Klartext ausgeben
	if ($Download == 1) {
		$helptext_header = array("QRSID" => "Query SNP rsID",
					 "RSID" => "Proxy SNP rsID",
					 "RSALIAS" => "Proxy SNP alias rsID(s)",
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
		$helptext = "Header abbreviations:\n\n";
		foreach($helptext_header as $abr => $fulltext) {
			$helptext .= $abr." = ".$fulltext."\n";
		}
		$helptext .= "\n\nInformation about this job:\n\n";
		$helptext .= "Job ID: ".$JobId."\n";
		$helptext .= "Genome assembly: ".$Genomerelease."\n";
		$helptext .= "Reference set: ".$Referenceset."\n";
		$helptext .= "Population: ".$Population."\n";
		$helptext .= "Annotation release: ".$Annotation."\n";
		$helptext .= "Input type: ".$SnpsInputType."\n";
		if ($SnpsInputType == "gene") { $helptext .= "Gene identifier: ".$SnpsGene."\n"; }
		if ($SnpsInputType == "region") { $helptext .= "Genetic region: chr".$SnpsRegionChr.":".$SnpsRegionBegin."-".$SnpsRegionEnd."\n"; }
		if ($SnpsInputType == "snps") { 
			$tmpnotidentified = ""; foreach($Sentinels as $tmp) { if (empty($tmp['POS'])) {  $tmpnotidentified .= $tmp['RSID']." "; } } unset($tmp); 
			$helptext .= "SNPs not in Reference Set or Population: ".$tmpnotidentified."\n";
			}
		
		file_put_contents($JobDir."/proxySearch.description.txt", $helptext, LOCK_EX | FILE_APPEND);

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
			return $ordered + $array;
		}
	}

	// Zähler der Zeilen der Ergebnistabelle
	$ResultsCount = 0;
	
	
	foreach ($Sentinels as $sentinel) { 
		set_time_limit(10);
		$sentinelsCount++;
		$tabixresults = array();
		if (($sentinelsCount % 100) == 0) { $status['message'] = "Query proxy SNPs from database (".$sentinelsCount." of ".$sentinelsTotal.")."; savePlotStatus($status,$JobDir."/status.txt");}
		if (($sentinel['CHR'] != "") && ($sentinel['POS'] != "")) {	
			// Tabix-Abfrage und ausfiltern von SNPs, die keine rs-ID haben
			//$tabixresults = array_filter(snipaGetProxies($Rel,$Pop,$sentinel['CHR'],$sentinel['POS'],"","",$Rsquare), 'norsid');
			$tabixresults = snipaGetProxies($Genomerelease,$Referenceset,$Population,$sentinel['CHR'],$sentinel['POS'],"","",$Rsquare);
		}
		
		// Filter für Pairwise LD
		if ($Pairwise == 1) {
			$sentinelrsids = array_map(function($element){return $element['RSID'];},$Sentinels);
			for($i=0; $i<count($tabixresults); $i++) {
				if (in_array($tabixresults[$i]['RSID'],$sentinelrsids) == FALSE) { $tabixresults[$i] = NULL; }
			}
			// Lösche leere Keys und Reindizierung des Arrays
			$tabixresults = array_values(array_filter($tabixresults));
		}
		
		// Filtere ggf. selfinfo raus (sentinel = proxy), falls Option gesetzt
		if ($IncludeSentinels == 0) { 
			for($i=0; $i<count($tabixresults); $i++) {
				if ($tabixresults[$i]['DIST'] == 0) { $tabixresults[$i] = NULL; }
			}
			// Lösche leere Keys und Reindizierung des Arrays
			$tabixresults = array_values(array_filter($tabixresults));
		} 	
		
		$sentinel['Genomerelease'] = $Genomerelease; $sentinel['Referenceset'] = $Referenceset; $sentinel['Population'] = $Population; $sentinel['Annotation'] = $Annotation; 
		// Fuege ggf. Funktionelle Annotation hinzu
		if ($IncludeFunctionalAnnotation == 1) {
			$AnnotationFields = array("COMPEFFECTS" => "", "EQTLGENES" => "-", "REGGENES" => "-", "GENES" => "-", "TRAIT" => "no", "CISEQTL" => "no", "TRANSEQTL" => "no"); 
			for($i=0; $i<count($tabixresults); $i++) {
				foreach ($AnnotationFields as $field => $default) { 
					$tmpanno = $Annotations[$tabixresults[$i]['CHR']][$tabixresults[$i]['POS2']][$field];
					// Setze default-Werte ein, falls keine Annotation zum SNP vorhanden
					if (empty($tmpanno)) { $tmpanno = $default; }
					$tabixresults[$i][$field] = $tmpanno;
				}
			}
		}
		
		$ResultsCount = $ResultsCount + count($tabixresults);
		file_put_contents($JobDir."/proxySearch.phparray.txt",serialize($sentinel)."\t".serialize($tabixresults)."\n", FILE_APPEND | LOCK_EX);
		
		
		// Tabelle formatieren falls Download erwünscht
		if ($Download == 1) {

			$downloadresults = $tabixresults;
			foreach($downloadresults as $line) {
				$dlfile = fopen($JobDir."/proxySearch.results.csv",'a');
				// Fuege QRSID (=sentinel ID) ein
				$line = array('QRSID' => $sentinel['RSID']) + $line;
				// Sortiere Array nach den Keys aus dem description-File (s.o.)
				$line = sortArrayByArray($line,array_keys($helptext_header));
				// Header nur beim ersten Eintrag
				if ($dlfileinclheader) {
					$dlfileinclheader = FALSE;
					fputcsv($dlfile, array_keys($line), "\t");
				}
				array_walk($line, function(&$el){ $el = html_entity_decode($el); } );
				fputcsv($dlfile, $line, "\t");
				fclose($dlfile);
			}
		}
		
	}
	
	file_put_contents($JobDir."/proxySearch.sentinels.txt",print_r($Sentinels,true)."\n", FILE_APPEND | LOCK_EX);
	file_put_contents($JobDir."/proxySearch.count", $ResultsCount, LOCK_EX);

	$status['message'] = "Query proxy SNPs from database (".$sentinelsCount." of ".$sentinelsTotal.").";
	$status['ok'] = "OK";
	savePlotStatus($status,$JobDir."/status.txt");
}


// Ggf. Downloadversion zippen
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "ZIP results file.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");

	if ($Download == 1) {
		system("cd ".$JobDir."; zip proxySearch.results.zip proxySearch.description.txt proxySearch.results.csv > /dev/null",$cmderrorlevel);
	
		if ($cmderrorlevel != 0) {
			$status['ok'] = "FAIL";
			$status['errmessage'] = "Results files could not be zipped. Please try again with the downloads option disabled.";
			$allok = FALSE;
		} else {
			$status['ok'] = "OK";
		}
	} else {	
		$status['ok'] = "OK";
	}
	savePlotStatus($status,$JobDir."/status.txt");
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
	if ($SnpsInputType == "snps") { 
		$tmpnotidentified = ""; foreach($Sentinels as $tmp) { if (empty($tmp['POS'])) {  $tmpnotidentified .= $tmp['RSID']." "; } } unset($tmp); 
		$inputdetails .= "SNPs not in Reference Set or Population: ".$tmpnotidentified."";
	}
	$report['userinput']['inputdetails'] = $inputdetails;
	$report['jobinfo']['jobid'] = $JobId;
	$report['jobinfo']['runtime'] = time() - $starttime;
	$report['jobinfo']['dldescription'] = "";
	$report['jobinfo']['dlcsv'] = "";
	$report['jobinfo']['dlcsvsize'] = "";
	$report['jobinfo']['dlzip'] = "";
	$report['jobinfo']['dlzipsize'] = "";
	if ($Download == 1) {
		$report['jobinfo']['dldescription'] = $tmpdatadir."/".$JobId."/proxySearch.description.txt";
		$report['jobinfo']['dlcsv'] = $tmpdatadir."/".$JobId."/proxySearch.results.csv";
		$report['jobinfo']['dlcsvsize'] = human_filesize(filesize($JobDir."/proxySearch.results.csv"));
		$report['jobinfo']['dlzip'] = $tmpdatadir."/".$JobId."/proxySearch.results.zip";
		$report['jobinfo']['dlzipsize'] = human_filesize(filesize($JobDir."/proxySearch.results.zip"));
	}
		
	file_put_contents($JobDir."/report.txt",utf8_encode(json_encode($report)));
	
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
