<?php

require_once("../backend/snipaMaprsid.php");
require_once("../backend/snipaMapGenes.php");
require_once("../backend/snipaTabix.php");
require_once("../backend/snipaConfig.php");



$Genomerelease = $_POST['genomerelease'];
$Referenceset = $_POST['referenceset'];
$Population = $_POST['population'];
$Annotation = $_POST['annotation'];
$Sentinel = $_POST['sentinel']; 
$HighlightVariants = $_POST['highlightvariants'];
$HighlightMode = preg_replace("/[^hide|show]/","",$_POST['highlightmode']);
$Highcharts = $_POST['highcharts'];
$HiRes = $_POST['hires'];
$PlotWidth = $_POST['plotwidth']; 

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
$status['totalstepnum'] = 16;
$status['message'] = "";
$status['errmessage'] = "";
$status['ok'] = "";

function savePlotStatus($statusarray, $filename) {
	$statfilefh = fopen($filename.".1",'w');
	fwrite($statfilefh, utf8_encode(json_encode($statusarray)));
	fclose($statfilefh);
	copy($filename.".1",$filename); 
}


// überprüfe Länge Sentinel SNP
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Check length of sentinel SNP.";
	$status['errmessage'] = "";
	$status['ok'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	$Sentinel = strtolower(trim($Sentinel));
	
	if (strlen($Sentinel) == 0) {
		$status['ok'] = "FAIL";
		$status['errmessage'] = "No data submitted by user.";
		$allok = FALSE;
	} else {
		$status['ok'] = "OK";
	}
	
	savePlotStatus($status,$JobDir."/status.txt");
}

// überprüfe Syntax vom Sentinel SNP	
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Check syntax of sentinel SNP.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	if (!(preg_match("/^rs[0-9]+/",$Sentinel)))
	{
		$status['ok'] = "FAIL";
		$status['errmessage'] = "Your sentinel SNP has to be an rs-identifier.";
		$allok = FALSE;
	} else {
		$status['ok'] = "OK";
	}
	
	savePlotStatus($status,$JobDir."/status.txt");
}

// Sentinel im Release? Wenn ja, dann mappe rsID auf neuestes dnSNP release
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Determine genetic position of sentinel SNP.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	$SentinelPos = snipaMapRsid($Genomerelease,$Referenceset,$Population,$Sentinel);
	if ($SentinelPos['CHR'] == "" || $SentinelPos['POS'] == "") { 
		$status['ok'] = "FAIL";
		$status['errmessage'] = "The sentinel SNP ".$Sentinel." could not be found in release ".$Genomerelease."-".$Referenceset." (".$Population.").";
		$allok = FALSE;
	} else {
		$tmprsmapping = snipaMapPos($Genomerelease,$Referenceset,$Population,$SentinelPos['CHR'],$SentinelPos['POS'],$SentinelPos['POS']);
		$Sentinel = $tmprsmapping[$SentinelPos['POS']];
		$status['ok'] = "OK";
	}
	
	savePlotStatus($status,$JobDir."/status.txt");
}

// Position aller umgebenden SNPs
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Get genetic positions for SNPs around sentinel (+/- 250 kB).";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	$fhsentisnps = fopen($JobDir."/ldplot_snps_around_sentinel.txt","w");

	$SNPsPos = snipaMapPos($Genomerelease,$Referenceset,$Population,$SentinelPos['CHR'],$SentinelPos['POS']-250000,$SentinelPos['POS']+250000);
	foreach ($SNPsPos as $tmppos => $tmprsid) {
		fwrite($fhsentisnps, $tmprsid.";".$tmppos."\n");
	}

	fclose($fhsentisnps);
	 
	 // FEHLT: ERROR HANDLING
	 $status['ok'] = "OK";
	 
	 savePlotStatus($status,$JobDir."/status.txt");
}


// Sanity check fuer Highlight-SNPs
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Check syntax of variants that should be highlighted.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	$HighlightVariants = trim($HighlightVariants);
	$HighlightVariantsArray = preg_split('/\r\n|[\r\n]/',$HighlightVariants);
	foreach ($HighlightVariantsArray as &$entry) {
		$entry = trim($entry);
		if (!(preg_match("/^rs[0-9]+/",$entry))) { $entry = ""; }
	}
	unset($entry);
	$HighlightVariantsArray = array_filter($HighlightVariantsArray,'strlen');
		
	if ((strlen($HighlightVariants) > 0) && (count($HighlightVariantsArray) == 0)) {
		$status['ok'] = "FAIL";
		$status['errmessage'] = "Please check if you've entered valid rs identifiers.";
		$allok = FALSE;
	} else {
		$status['ok'] = "OK";
	}
	savePlotStatus($status,$JobDir."/status.txt");
}

// Bestimme Positionen fuer Highlight SNPs
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Determine genetic positions of variants that should be highlighted.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	$HighlightVariantsPos = array();
	
	foreach ($HighlightVariantsArray as $snp) {
		 $tmp = snipaMapRsid($Genomerelease,$Referenceset,$Population,$snp);
		 if (count($tmp) > 0) {	$HighlightVariantsPos[] = $tmp['POS']; }
		 unset($tmp);
	}

	$HighlightVariantsPos = array_filter($HighlightVariantsPos,'strlen');
	
	$status['ok'] = "OK";
	savePlotStatus($status,$JobDir."/status.txt");
}
	
// Grundlegende Annotation aus Tabix laden
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Get basic SNP annotations.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	$TabixFields = array(	"POS1" => "Query SNP Position",
								"POS2" => "Proxy SNP Position",
								"RSALIAS" => "Alias",
								"MAJOR" => "Proxy Allele A",
								"MINOR" => "Proxy Allele B",
								"MAF" => "Allele B Frequency",
								"CMMB" => "Recombination Rate (CM/Mb)",
								"CM" => "Genetic distance (CM)"
								 );
	
	$fhtabix = fopen($JobDir."/ldplot_snps_annotations_basic.txt","w");
	
	$lineformatted = "";
	// Schreibe Header für Tabix-File
	foreach ($TabixFields as $col => $desc) {
		$lineformatted .= $col.";";
	}
	$lineformatted = rtrim($lineformatted,";"); // letztes Semikolon einer Zeile entfernen
	$lineformatted .= "\n";
	fwrite($fhtabix,$lineformatted);
	
	$tmpsegmentsize = 10000;
	$tmppos1 = $SentinelPos['POS']-250000;
	$tmppos2 = $SentinelPos['POS']+250000;
	for ($i = $tmppos1; $i <= $tmppos2; $i = $i+$tmpsegmentsize) {
		set_time_limit(30);
		$Tabix = snipaGetSelfinfo($Genomerelease,$Referenceset,$Population,$SentinelPos['CHR'],$i,min($i+$tmpsegmentsize-1,$tmppos2));
		$lineformatted = "";
		for ($j=0; $j < count($Tabix); $j++) {
			if ($Tabix[$j]['POS1'] == $Tabix[$j]['POS2']) {
				foreach ($TabixFields as $col => $desc) { 
					$lineformatted .= "".str_replace(";",",",$Tabix[$j][$col]).";";
				}
				$lineformatted = substr($lineformatted,0,-1);  // letztes Semikolon einer Zeile entfernen
				$lineformatted .= "\n";
			}
		}
		fwrite($fhtabix,$lineformatted);
	}

	fclose($fhtabix);
	
	// FEHLT: ERROR HANDLING
	$status['ok'] = "OK";
	
	savePlotStatus($status,$JobDir."/status.txt");
}


// Erweiterte Annotation aus Tabix laden
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Get detailed SNP annotations.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	$TabixFields = array("POS","FUNC","MULTIPLE","DISEASE","ANNOTATION");
	
	$fhtabix = fopen($JobDir."/ldplot_snps_annotations_advanced.txt","w");
	
	$lineformatted = "";
	// Schreibe Header für Tabix-File
	foreach ($TabixFields as $col) {
		$lineformatted .= $col.";";
	}
	$lineformatted = rtrim($lineformatted,";"); // letztes Semikolon einer Zeile entfernen
	$lineformatted .= "\n";
	fwrite($fhtabix,$lineformatted);
	
	$tmpsegmentsize = 10000;
	$tmppos1 = $SentinelPos['POS']-250000;
	$tmppos2 = $SentinelPos['POS']+250000;
	for ($i = $tmppos1; $i <= $tmppos2; $i = $i+$tmpsegmentsize) {
		set_time_limit(30);
		$Tabix = snipaGetSNPAnnotations($Genomerelease,$Annotation,$SentinelPos['CHR'],$i,min($i+$tmpsegmentsize-1,$tmppos2));
		$lineformatted = "";
		for ($j=0; $j < count($Tabix); $j++) {
			foreach ($TabixFields as $col) { 
				$lineformatted .= "".str_replace(";",",",$Tabix[$j][$col]).";";
			}
			$lineformatted = substr($lineformatted,0,-1);  // letztes Semikolon einer Zeile entfernen
			$lineformatted .= "\n";
		}
		fwrite($fhtabix,$lineformatted);
	}

	fclose($fhtabix);
	
	// FEHLT: ERROR HANDLING
	$status['ok'] = "OK";
	
	savePlotStatus($status,$JobDir."/status.txt");
}


// Merge Query-SNPs und Annotationen 
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Merge SNP annotations.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");

	$rcmd = "library('plyr'); ";
	$rcmd .= "tmp1 <- read.table('".$JobDir."/ldplot_snps_around_sentinel.txt', sep=';', header=F); ";
	$rcmd .= "colnames(tmp1) <- c('SNP','POS'); ";
	$rcmd .= "tmp2 <- read.table('".$JobDir."/ldplot_snps_annotations_basic.txt', sep=';', header=T); ";
	$rcmd .= "tmp3 <- merge(tmp1,tmp2, by.x='POS', by.y='POS2', all.x=T, all.y=F); ";
	$rcmd .= "tmp3\$POS1 <- NULL; ";
	$rcmd .= "tmp4 <- read.table('".$JobDir."/ldplot_snps_annotations_advanced.txt', sep=';', header=T, quote='\\\"'); ";
	$rcmd .= "tmp5 <- merge(tmp3,tmp4, by.x='POS', by.y='POS', all.x=T, all.y=F); ";
	$rcmd .= "tmp5 <- arrange(tmp5,POS); ";
	$rcmd .= "tmp5[which(is.na(tmp5\$FUNC)),'FUNC'] <- 1; ";
	$rcmd .= "tmp5[which(is.na(tmp5\$DISEASE)),'DISEASE'] <- 0; ";
	$rcmd .= "tmp5[which(is.na(tmp5\$MULTIPLE)),'MULTIPLE'] <- 0; ";
	$rcmd .= "write.table(file = '".$JobDir."/ldplot_snps_pos_annotations.txt', tmp5, row.names=F, col.names=T, quote=T, sep=';'); ";

	file_put_contents($JobDir."/ldplot_snps_pos_annotations.R", $rcmd, LOCK_EX);
	system("Rscript --vanilla ".$JobDir."/ldplot_snps_pos_annotations.R",$rcmderrorlevel);
	if ($rcmderrorlevel != 0) {
		$status['ok'] = "FAIL";
		$status['errmessage'] = "Data could no be joined.";
		$allok = FALSE;
	}  else {
		$status['ok'] = "OK";
	}
	
	savePlotStatus($status,$JobDir."/status.txt");
}

// R2 Werte zu sentinel SNP hinzufügen
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Query LD data for sentinel SNP.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	
	$TabixFields = array(	"POS1" => "Query SNP Position",
								"POS2" => "Proxy SNP Position",
								"R2" => "R Squared",
								"D" => "D",
								"DPRIME" => "D'",
								);
	
	$fhtabix = fopen($JobDir."/ldplot_snps_in_ld_to_sentinel.txt","w");
	
	$lineformatted = "";
	// Schreibe Header für Tabix-File
	foreach ($TabixFields as $col => $desc) {
		$lineformatted .= $col.";";
	}
	$lineformatted = rtrim($lineformatted,";"); // letztes Semikolon einer Zeile entfernen
	$lineformatted .= "\n";
	fwrite($fhtabix,$lineformatted);
	
	$SentinelProxies = snipaGetProxies($Genomerelease,$Referenceset,$Population,$SentinelPos['CHR'],$SentinelPos['POS']);
	
	$lineformatted = "";
	for ($j=0; $j < count($SentinelProxies); $j++) {	
		foreach ($TabixFields as $col => $desc) { 
					$lineformatted .= "".$SentinelProxies[$j][$col].";";
				}
		$lineformatted = substr($lineformatted,0,-1);  // letztes Semikolon einer Zeile entfernen
		$lineformatted .= "\n";
	}
	
	fwrite($fhtabix,$lineformatted);
	fclose($fhtabix);
	
	// FEHLT: ERROR HANDLING
	$status['ok'] = "OK";
	
	savePlotStatus($status,$JobDir."/status.txt");
}


// R2 Werte mit annotierten SNPs mergen und input files für die Plots generieren.
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Merge LD data and annotated SNPs.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	
	$rcmd = "library('plyr'); ";
	$rcmd .= "tmp1 <- read.table('".$JobDir."/ldplot_snps_pos_annotations.txt', sep=';', header=T); ";
	$rcmd .= "tmp2 <- read.table('".$JobDir."/ldplot_snps_in_ld_to_sentinel.txt', sep=';', header=T); ";
	$rcmd .= "tmp3 <- merge(tmp1,tmp2, by.x='POS', by.y='POS2', all.x=T, all.y=F); ";
	$rcmd .= "tmp3 <- arrange(tmp3,POS); ";
	$rcmd .= "tmp3\$POS1 <- NULL; ";
	$rcmd .=  "tmp3[which(!(is.na(tmp3[,'RSALIAS']))),'RSALIASHTML'] <- paste0('<tr><td>Alias:&nbsp;</td><td>',gsub(',',', ',tmp3[which(!(is.na(tmp3[,'RSALIAS']))),'RSALIAS']),'</td></tr>'); ";
	$rcmd .= "tmp3[which((is.na(tmp3[,'RSALIAS']))),'RSALIASHTML'] <- as.character(''); ";
	$rcmd .= "extract <- function(x){ e <- ifelse(x == 0, 0, floor(log10(x))); m <- x/10^e; list(mantissa = m, exponent = e); } 
			tmp3\$BASICANNOTATION <- '<table></table>'; 
			tmp3\$BASICANNOTATION <- paste(
			'<table class=\'snipa-plot-tooltip\'><thead><tr><th colspan=\'2\'>SNP ',tmp3\$SNP,'</th></tr></thead><tbody>',tmp3\$RSALIASHTML,
			'<tr><td>Genetic location:</td><td>".$SentinelPos['CHR'].":',tmp3\$POS,'</td></tr>',
			'<tr><td>LD to ".$Sentinel.":</td><td>r<sup>2</sup>=',round(tmp3\$R2, digits=2),'; D\'=',round(tmp3\$DPRIME, digits=2),'</td></tr>',
			'<tr><td>Distance to ".$Sentinel.":</td><td>',abs(tmp3\$POS-".$SentinelPos['POS']."),'&nbsp;bp</td></tr>',
			'<tr><td>Recombination rate:</td><td>',round(tmp3\$CMMB, digits=2),' cM/Mb</td></tr>',
			'</tbody></table>',
			sep=''); ";
	$rcmd .= "tmp3\$LINK <- NA; "; 
	
	$rcmd .= "write.table(subset(tmp3, select=c('POS','SNP','CMMB','R2','FUNC','DISEASE','MULTIPLE')), file = '".$JobDir."/ldplot_snps_pos_annotations_ld.txt', row.names=F, col.names=T, quote=F, sep=';'); ";
	$rcmd .= "write.table(subset(tmp3, select=c('POS','BASICANNOTATION','ANNOTATION','LINK')), file = '".$JobDir."/ldplot_snps_annotations_phparray.tmp', row.names=F, col.names=F, quote=T, sep=';'); ";

	file_put_contents($JobDir."/ldplot_snps_pos_annotations_ld.R", $rcmd, LOCK_EX);
	system("Rscript --vanilla ".$JobDir."/ldplot_snps_pos_annotations_ld.R",$rcmderrorlevel);
	if ($rcmderrorlevel != 0) {
		$status['ok'] = "FAIL";
		$status['errmessage'] = "Data could no be merged.";
		$allok = FALSE;
	}  else {
		$status['ok'] = "OK";
	}
	
	savePlotStatus($status,$JobDir."/status.txt");
}

// Wandle SNP-Annotationsfile von CSV in PHP-Array um
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Convert SNP annotations to PHP array.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
	
	$SnpsAnnotations = array();
	$snpannofh = fopen($JobDir."/ldplot_snps_annotations_phparray.tmp","r");
	while (($snpannotmp = fgetcsv($snpannofh,0,';','"')) !== FALSE) {
		$SnpsAnnotations[$snpannotmp[0]]['BASICANNOTATION'] = $snpannotmp[1];
		$SnpsAnnotations[$snpannotmp[0]]['ANNOTATION'] = $snpannotmp[2];
		$SnpsAnnotations[$snpannotmp[0]]['LINK'] = $snpannotmp[3];
	}
	fclose($snpannofh);
	file_put_contents($JobDir."/ldplot_snps_annotations_phparray.txt", serialize($SnpsAnnotations), LOCK_EX);
	
	// FEHLT: ERROR HANDLING
	$status['ok'] = "OK";
	
	savePlotStatus($status,$JobDir."/status.txt");
}


// Gene und regulatorische Elemente herausschreiben
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Look for genes and regulatory elements in plotting region.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
				
	// schreibe statische Gen-Liste für Plots
	$Genes = snipaMapGenes($Genomerelease,$Annotation,$SentinelPos['CHR'],$SentinelPos['POS']-250000,$SentinelPos['POS']+250000);
	$GenesFields = array("ID","NAME","START","STOP","STRAND","HIGHLIGHT");
	$lineformatted = "";
	$fhgenes = fopen($JobDir."/ldplot_genelist_static.txt","w");
	foreach ($GenesFields as $col) {
		$lineformatted .= $col.";";
	}
	$lineformatted = substr($lineformatted,0,-1);  // letztes Semikolon einer Zeile entfernen
	$lineformatted .= "\n";
	for ($j=0; $j < count($Genes); $j++) {	
		foreach ($GenesFields as $col) { 
					$lineformatted .= "".$Genes[$j][$col].";";
				}
		$lineformatted = substr($lineformatted,0,-1);  // letztes Semikolon einer Zeile entfernen
		$lineformatted .= "\n";
	}
	fwrite($fhgenes,$lineformatted);
	fclose($fhgenes);
	
	if ($Highcharts == 1) {
		// schreibe statische Regulatory-Elements-Liste für Plots
		$Regulatory = snipaMapRegulatory($Genomerelease,$Annotation,$SentinelPos['CHR'],$SentinelPos['POS']-250000,$SentinelPos['POS']+250000);
		$RegulatoryFields = array("NAME","START","STOP");
		$lineformatted = "";
		$fhregulatory = fopen($JobDir."/ldplot_regulatorylist_static.txt","w");
		foreach ($RegulatoryFields as $col) {
			$lineformatted .= $col.";";
		}
		$lineformatted = substr($lineformatted,0,-1);  // letztes Semikolon der Zeile entfernen
		$lineformatted .= "\n";
		for ($j=0; $j < count($Regulatory); $j++) {	
			foreach ($RegulatoryFields as $col) { 
						$lineformatted .= "".$Regulatory[$j][$col].";";
					}
			$lineformatted = substr($lineformatted,0,-1);  // letztes Semikolon der Zeile entfernen
			$lineformatted .= "\n";
		}
		fwrite($fhregulatory,$lineformatted);
		fclose($fhregulatory);
	}
	
	if ($Highcharts == 1) {
		// schreibe php-Arrays für dynamische Annotation
		$GenesAnnotations = array();
		for ($j=0; $j < count($Genes); $j++) {	
			$GenesAnnotations[$Genes[$j]["ID"]]["ANNOTATION"] = $Genes[$j]["ANNOTATION"];
			$GenesAnnotations[$Genes[$j]["ID"]]["LINK"] = $Genes[$j]["LINK"];
			}
		file_put_contents($JobDir."/ldplot_genelist_annotations_phparray.txt", serialize($GenesAnnotations), LOCK_EX);
		
		$RegulatoryAnnotations = array();
		for ($j=0; $j < count($Regulatory); $j++) {	
			$RegulatoryAnnotations[$Regulatory[$j]["NAME"]]["ANNOTATION"] = $Regulatory[$j]["ANNOTATION"];
			$RegulatoryAnnotations[$Regulatory[$j]["NAME"]]["LINK"] = $Regulatory[$j]["LINK"];
			}
		file_put_contents($JobDir."/ldplot_regulatorylist_annotations_phparray.txt", serialize($RegulatoryAnnotations), LOCK_EX);
	}
	// FEHLT: ERROR HANDLING
	$status['ok'] = "OK";
	
	savePlotStatus($status,$JobDir."/status.txt");
}


// Erstelle statischen RA-Plot
if ($allok) {
	$status['stepnum'] = $status['stepnum']+1;
	$status['message'] = "Generate static linkage disequilibrium plot.";
	$status['ok'] = "";
	$status['errmessage'] = "";
	savePlotStatus($status,$JobDir."/status.txt");
				
	$rcmd = "setwd('".$JobDir."'); 
		source('".$serverdir."/backend/snipaLDPlots.R'); 
		snplist1 <- read.table('".$JobDir."/ldplot_snps_pos_annotations_ld.txt', header = T, sep=';'); 
		genelist1 <- read.table('".$JobDir."/ldplot_genelist_static.txt', header=T, sep=';'); 
		
		if (nrow(genelist1) > 1) {
		genelist2 <- merge(genesort2(subset(genelist1, select=c('START','STOP','NAME')), plotrange_x_px = ".$PlotWidth."),
						   subset(genelist1, select=c('NAME','STRAND')),
						   by='NAME'
						   )
		} else { genelist2 <- genelist1; }; 

		hlpos <- c(".implode(',',$HighlightVariantsPos).");
		hlmode <- '".$HighlightMode."';
		
		png(file = '".$JobDir."/ldplot_plot_static.png', width=".$PlotWidth.", height=600, pointsize = 18); 
		locus.plot.classic('".$Sentinel."', '".$SentinelPos['CHR']."', snplist1, genelist2, hlpos, hlmode); 
		dev.off(); ";
		
		$rcmd .= "
		multires <- c(570,600,630,660,690,720,750,780,810,840,870,900); 
		for (res in multires) { 
			if (nrow(genelist1) > 1) {
			genelist2 <- merge(genesort2(subset(genelist1, select=c('START','STOP','NAME')), plotrange_x_px = res),
							   subset(genelist1, select=c('NAME','STRAND')),
							   by='NAME'
							   )
			} else { genelist2 <- genelist1 }; 

			png(file = paste('".$JobDir."/ldplot_plot_static_',res,'.png',sep=''), width=res, height=600, pointsize = 18); 
			locus.plot.classic('".$Sentinel."','".$SentinelPos['CHR']."', snplist1, genelist2, hlpos, hlmode); 
			dev.off(); 
		}	
		";
		
	if ($HiRes == 1) {
		$rcmd .= "if (nrow(genelist1) > 1) {
					genelist2 <- merge(genesort2(subset(genelist1, select=c('START','STOP','NAME')), plotrange_x_px = 960),
					subset(genelist1, select=c('NAME','STRAND')),
					by='NAME'
					)
					} else { genelist2 <- genelist1 }; ";
		$rcmd .= "png(file = '".$JobDir."/ldplot_plot_static_1920.png', width=1920, height=1080, pointsize = 30); 
				  locus.plot.classic('".$Sentinel."', '".$SentinelPos['CHR']."', snplist1, genelist2, hlpos, hlmode); 
		          dev.off(); ";
		$rcmd .= "pdf(file = '".$JobDir."/ldplot_plot_static.pdf', paper='a4r', width=12, height=8, pointsize = 12); 
				  locus.plot.classic('".$Sentinel."', '".$SentinelPos['CHR']."', snplist1, genelist2, hlpos, hlmode); 
		          dev.off()";
	}
	
	file_put_contents($JobDir."/ldplot_plot_static.R", $rcmd, LOCK_EX);
	
	system("Rscript --vanilla ".$JobDir."/ldplot_plot_static.R 1> /dev/null",$rcmderrorlevel);
	if ($rcmderrorlevel != 0) {
		$status['ok'] = "FAIL";
		$status['errmessage'] = "Plot could not be created.";
		$allok = FALSE;
	}  else {
		$status['ok'] = "OK";
	}
	
	savePlotStatus($status,$JobDir."/status.txt");
}


// Erstelle dynamischen RA-Plot
if ($allok) {
	if ($Highcharts == 0) { $status['stepnum'] = $status['stepnum']+1; } else {
		$status['stepnum'] = $status['stepnum']+1;
		$status['message'] = "Generate dynamic linkage disequilibrium plot (this could take a while).";
		$status['ok'] = "";
		$status['errmessage'] = "";
		savePlotStatus($status,$JobDir."/status.txt");
		
		$rcmd = "setwd('".$JobDir."'); 
			source('".$serverdir."/backend/snipaLDPlots.R'); 
			jobid <- ".$JobId."; 
			snplist1 <- read.table('".$JobDir."/ldplot_snps_pos_annotations_ld.txt', header = T, sep=';'); 
			genelist1 <- read.table('".$JobDir."/ldplot_genelist_static.txt', header=T, sep=';'); 
			regellist1 <- read.table('".$JobDir."/ldplot_regulatorylist_static.txt', header=T, sep=';'); 
			
			if (nrow(genelist1) > 1) {
			genelist2 <- merge(genesort2(subset(genelist1, select=c('START','STOP','NAME')), plotrange_x_px = ".$PlotWidth."),
							   subset(genelist1, select=c('ID','NAME','STRAND','HIGHLIGHT')),
							   by='NAME'
							   )
			} else { genelist2 <- genelist1 }; 
							   
			if (nrow(regellist1) > 1) { regellist2 <- genesort2(regellist1, label=FALSE, mindist=500) } else { regellist2 <- regellist1 }; 
			
			hlpos <- c(".implode(',',$HighlightVariantsPos).");
			hlmode <- '".$HighlightMode."';
			
			";
				
		$rcmd .= "lph <- locus.plot.highcharts('".$Sentinel."','".$SentinelPos['CHR']."',snplist1,genelist2,regellist2,hlpos,hlmode); 
		
		
		cat('chartoptions = ', file = 'ldplot_plot_dynamic.js'); 
		cat(lph, file = 'ldplot_plot_dynamic.js', append = TRUE); ";

			
		

			
			
		file_put_contents($JobDir."/ldplot_plot_dynamic.R", $rcmd, LOCK_EX);
		
		system("R --vanilla < ".$JobDir."/ldplot_plot_dynamic.R 1> /dev/null 2> /dev/null",$rcmderrorlevel);
		if ($rcmderrorlevel != 0) {
			$status['ok'] = "FAIL";
			$status['errmessage'] = "Plot could not be created.";
			$allok = FALSE;
		}  else {
			$status['ok'] = "OK";
		}
	}
	
	savePlotStatus($status,$JobDir."/status.txt");

	}

// Erstelle Report
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
	$report['snppos']['sentinelchr'] = $SentinelPos['CHR'];
	$report['snppos']['sentinelpos'] = $SentinelPos['POS'];
	$report['userinput']['sentinelname'] = $Sentinel;
	$report['jobinfo']['jobid'] = $JobId;
	$report['jobinfo']['runtime'] = time() - $starttime;
	$report['jobinfo']['staticplotpdf'] = "";
	$report['jobinfo']['staticplotpng'] = "";
	if ($HiRes == 1) {
		$report['jobinfo']['staticplotpdf'] = $tmpdatadir."/".$JobId."/ldplot_plot_static.pdf";
		$report['jobinfo']['staticplotpng'] = $tmpdatadir."/".$JobId."/ldplot_plot_static_1920.png";
	}
		
	file_put_contents($JobDir."/report.txt",utf8_encode(json_encode($report)));
	
	$status['ok'] = "OK";
	savePlotStatus($status,$JobDir."/status.txt");
}
	
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
