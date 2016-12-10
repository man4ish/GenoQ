<?php
require_once("../backend/snipaMaprsid.php");
require_once("../backend/snipaMapGenes.php");
require_once("../backend/snipaTabix.php");
require_once("../backend/snipaConfig.php");
require_once("../backend/snipaGeneSort.php");
include_once("../backend/HighchartsPHP/Highchart.php");

$Genomerelease = $_REQUEST['genomerelease'];
$Referenceset = $_REQUEST['referenceset'];
$Population = $_REQUEST['population'];
$Annotation = $_REQUEST['annotation'];
$Pos = $_REQUEST['pos']; 
$Chr = $_REQUEST['chr'];
$Size = $_REQUEST['size'];
$PlotWidth = $_REQUEST['plotwidth']; 
$Sentinel = $_REQUEST['sentinel'];


$allok = TRUE;

$JobId =  preg_replace( '/[^0-9]/', '', $_REQUEST['id']);
if (strlen($JobId) != 15) { $allok = FALSE; }

// Verzeichnis für temporäre Dateien
if ($allok) {
	$tmpdatadir = "tmpdata";
	$serverdir = "/home/metabolomics/snipa/web/";
	$JobDir = $serverdir."/".$tmpdatadir."/".$JobId;
	if (!file_exists($JobDir)) {
		$allok = FALSE;
	}
}

// Grundlegende Annotation aus Tabix laden
if ($allok) {
	$TabixFields = array("RSID","RSALIAS","MAJOR","MINOR","MAF","CMMB","CM");
	$Tabix = snipaGetSelfinfo($Genomerelease,$Referenceset,$Population,$Chr,$Pos-$Size,$Pos+$Size);

	$snps = array();
	
	foreach($Tabix as $entry) {
		$tmpalias = "";
		if ($entry['RSALIAS'] != "NA") {
			$tmpalias = "<tr><td>Alias:&nbsp;</td><td>".implode(", ",explode(",",$entry['RSALIAS']))."</td></tr>";
		}
		$basicannotation = "<table class='snipa-plot-tooltip'><thead><tr><th colspan='2'>SNP ".$entry['RSID']."</th></tr></thead><tbody>".$tmpalias."<tr><td>Genetic location:&nbsp;</td><td>".$Chr.":".number_format($entry['POS1'])."</td></tr><tr><td>Recombination rate:&nbsp;</td><td>".number_format($entry['CMMB'], 2)." cM/Mb</td></tr></tbody></table>";
		$snps[$entry['POS1']] = array("RSID" => $entry['RSID'], "MAJOR" => $entry['MAJOR'], "MINOR" => $entry['MINOR'], "MAF" => $entry['MAF'], "CMMB" => $entry['CMMB'], "CM" => $entry['CM'], "BASICANNOTATION" => trim($basicannotation, '"'));
	}
}


// Erweiterte Annotation aus Tabix laden
if ($allok) {
	$Tabix = snipaGetSNPAnnotations($Genomerelease,$Annotation,$Chr,$Pos-$Size,$Pos+$Size);
	
	foreach($Tabix as $entry) {
		if (array_key_exists($entry['POS'],$snps)) {
			$snps[$entry['POS']]['FUNC'] = $entry['FUNC'];
			$snps[$entry['POS']]['MULTIPLE'] = $entry['MULTIPLE'];
			$snps[$entry['POS']]['DISEASE'] = $entry['DISEASE'];
			$snps[$entry['POS']]['ANNOTATION'] = trim($entry['ANNOTATION'], '"');
		}
	}
	
	// Setze default-Werte, falls keine Annotation vorhanden ist
	foreach ($snps as $key => $val) {
		if (!array_key_exists('FUNC',$val)) {
			$snps[$key]['FUNC'] = 1;
			$snps[$key]['MULTIPLE'] = 0;
			$snps[$key]['DISEASE'] = 0;
			$snps[$key]['ANNOTATION'] = "";
		}
	}
}


// Schreibe SNPs in File
if ($allok) {
	if (count($snps >0)) {
	$snpsfields = array("RSID","MAJOR","MINOR","MAF","CMMB","FUNC","MULTIPLE","DISEASE");
		
		// Daten für dynamischen Plot
		$snpsfh = fopen($JobDir."/plot_snps_pos_annotations.txt",'w');
			// Header schreiben
			$snpsheader = array_merge(array("POS"),$snpsfields);
			fputcsv($snpsfh,$snpsheader,";");
			foreach ($snps as $pos => $val) {
				$line = array();
				$line[] = $pos;
				foreach ($snpsfields as $field) {
					$line[] = $val[$field];
				}
				fputcsv($snpsfh,$line,";");
			}
		fclose($snpsfh);
		
		// Daten für Tooltip
		$snpsannotations = array();
		foreach ($snps as $pos => $val) {
			$snpsannotations[$pos] = array('BASICANNOTATION' => $val['BASICANNOTATION'], 'ANNOTATION' => $val['ANNOTATION']);
		}
		file_put_contents($JobDir."/plot_snps_annotations_phparray.txt", serialize($snpsannotations));
	}
}


// Annotationen der Gene und regulatorische Elemente in php Array schreiben
if ($allok) {
	$Genes = snipaMapGenes($Genomerelease,$Annotation,$Chr,$Pos-$Size,$Pos+$Size);
	$Regulatory = snipaMapRegulatory($Genomerelease,$Annotation,$Chr,$Pos-$Size,$Pos+$Size);

	$GenesAnnotations = array();
	for ($j=0; $j < count($Genes); $j++) {	
		$GenesAnnotations[$Genes[$j]["ID"]]["ANNOTATION"] = $Genes[$j]["ANNOTATION"];
		$GenesAnnotations[$Genes[$j]["ID"]]["LINK"] = $Genes[$j]["LINK"];
		}
	file_put_contents($JobDir."/plot_genelist_annotations_phparray.txt", serialize($GenesAnnotations), LOCK_EX);
	
	$RegulatoryAnnotations = array();
	for ($j=0; $j < count($Regulatory); $j++) {	
		$RegulatoryAnnotations[$Regulatory[$j]["NAME"]]["ANNOTATION"] = $Regulatory[$j]["ANNOTATION"];
		$RegulatoryAnnotations[$Regulatory[$j]["NAME"]]["LINK"] = $Regulatory[$j]["LINK"];
		}
	file_put_contents($JobDir."/plot_regulatorylist_annotations_phparray.txt", serialize($RegulatoryAnnotations), LOCK_EX);
}


// Berechne Layer für Gen- und Regulatorische Listen
if ($allok) {

	$GenesLayers = array();
	foreach ($Genes as $gene) 
			{ $GenesLayers[] = array( "START" => $gene["START"],
									  "STOP" => $gene["STOP"], 
									  "NAME" => $gene["NAME"], 
									  "ID" => $gene['ID'], 
									  "LAYER" => 1, 
									  "STRAND" => $gene["STRAND"], 
									  "HIGHLIGHT" => $gene["HIGHLIGHT"]);}
	$genelist = $GenesLayers;
	if (count($genelist)>1) {
		$genelist = genesort($GenesLayers,true,$Size*2,$PlotWidth,5000);
	}
	
	$RegulatoryLayers = array();
	foreach ($Regulatory as $reg) { $RegulatoryLayers[] = array("START" => $reg["START"], "STOP" => $reg["STOP"], "NAME" => $reg["NAME"], "LAYER" => 1);}
	$regellist = $RegulatoryLayers;
	if (count($regellist)>1) {
		$regellist = genesort($RegulatoryLayers,false,$Size*2,$PlotWidth,500);
	}
}


// TMP: schreibe SNP-Liste, Genliste und Regelement-Liste als serialisiertes Array in Datei
//file_put_contents($JobDir."/tmpdev_snps.txt", serialize($snps), LOCK_EX);
//file_put_contents($JobDir."/tmpdev_genes.txt", serialize($genelist), LOCK_EX);
//file_put_contents($JobDir."/tmpdev_regelements.txt", serialize($regellist), LOCK_EX);


// Erstelle dynamischen RA-Plot
if ($allok) {
	// PHP-Adaption des R-Skriptes:
	
	// X-Achse Limits
	$plotxmin = max(0,$Pos-$Size);
	$plotxmax = $Pos+$Size;
	
	// Y Plotrange
	$plotmax = 4*1.2;
	// Untergrenze: 15% der Plotrange unter y=0 für CMMB-Plot
	$plotmin = -0.15*$plotmax;

	// Pro Layer in der Genliste 7.5% nach unten, -5% Abstand zum CMMB-Plot
	$numgenelayers = 0;
	foreach($genelist as $gene) { $numgenelayers = max($numgenelayers,$gene['LAYER']); }
	if ($numgenelayers > 0) { 
		array_walk_recursive($genelist, function(&$item, $key) { 
			global $plotmin,$plotmax; 
			if ($key == "LAYER") { $item = $plotmin-0.05*$plotmax-0.075*$plotmax*$item; } 
		});
		$plotmin = $plotmin-0.05*$plotmax-0.075*$plotmax*$numgenelayers;
    }
	
	// Pro Layer in Regelementsliste 2% nach unten, 3% Abstand zu den Genen
	$numregellayers = 0;
	foreach($regellist as $regel) { $numregellayers = max($numregellayers,$regel['LAYER']); }
	if ($numregellayers > 0) { 
		array_walk_recursive($regellist, function(&$item, $key) { 
			global $plotmin,$plotmax; 
			if ($key == "LAYER") { $item = $plotmin-0.02*$plotmax-0.03*$plotmax*$item; } 
		});
		$plotmin = $plotmin-0.02*$plotmax-0.03*$plotmax*$numregellayers;
    }
	
	// Zum schluss nochmal 5% Abstand nach unten
	$plotmin = $plotmin-0.05*$plotmax;
	$plotrange = -$plotmin + $plotmax;
	
	// Randfarbe der SNPs und Füllfarbe, falls der SNP der Sentinel
	foreach($snps as $pos => $val) {
		if ($val['MULTIPLE'] == 1) { $snps[$pos]['lineColor'] = "#006611"; $snps[$pos]['lineWidth'] = 1.5; } 
		if ($val['DISEASE'] == 1) { $snps[$pos]['lineColor'] = "#003399"; $snps[$pos]['lineWidth'] = 2; /* $snps[$pos]['radius'] = 6; */ }
		if ($pos == $Sentinel) { $snps[$pos]['fillColor'] = "#DDDD00";  $snps[$pos]['radius'] = 8;}
	}

	// SNP Gruppen
	// 1: unknown
	// 2: Transkript unbekannt
	// 3: Regulatorisch unbekannt
	// 4: Regulatorisch direkt
	// 5: Transkript direkt

	// Punktformen nach Funktion
	$groupsymbol = array("circle","square","diamond","triangle","triangle-down");
	$groupname = array("unknown effect","putative effect on transcript","putative regulatory effect","direct regulatory effect","direct effect on transcript");
	$groupsymbolsize = array(4,4,5,5,5);
	
	// annotierte Gene
	$genenameblacklist = array("/^RP[1]+/i");
	$genenamereplace = array("TEST");
        if (count($genelist) > 0) {
		// mittelpunkt für label
		array_walk($genelist, function(&$item,$key) { 
				global $plotxmin,$plotxmax; 
				$item['START'] = max($plotxmin,$item['START']);
				$item['STOP'] = min($plotxmax,$item['STOP']);
				$item['middle'] = ($item['START']+$item['STOP'])/2; 
			});
		array_walk($genelist, function(&$item,$key) {
			global $genenameblacklist;
			global $genenamereplace;
			$tmpgenename = $item['NAME'];
                        // $tmpgenename = preg_replace($genenameblacklist,$genenamereplace,$item['NAME']);

			if ($item['STRAND'] == "+") { $item['label'] = $tmpgenename." >"; }
			if ($item['STRAND'] == "-") { $item['label'] = "< ".$tmpgenename; }
			$item['genestart'] = $item['START'];
			$item['genestop'] = $item['STOP'];
		});

		// Faerbe Gene ein, wenn HIGHLIGHT gesetzt ist und dickere Linie
		array_walk($genelist, function(&$item,$key) {
			if ($item['HIGHLIGHT'] > 0) { $item['color'] = "#005500"; } else { $item['color'] = "#009900"; }
			if ($item['HIGHLIGHT'] > 0) { $item['lineWidth'] = 3; } else { $item['lineWidth'] = 2; }
		});
	}
	
	// Rekombinationsrate, starte recomb-track 15% der plotange unterhalb des SNP-Tracks
	$recombrate = array();
	$oldval = 0;
	foreach ($snps as $pos => $val) {
		if (!is_numeric($val['CMMB'])) { continue; }
		//if ($val['CMMB'] == $oldval) { continue; } else { $oldval = $val['CMMB']; }
		$recombrate[] = array("x" => $pos, "cmmbmap" => $val['CMMB'], "y" => (0-0.15*$plotmax)+($val['CMMB']/60)*(6*$plotmax/8));
	}
	
	// optimiere Recombrate, sodass aufeinanderfolgende, identische Werte nicht mehrfach geplottet werden
	$rctest = $recombrate;
	$delkeys = array();
	if (count($rctest) > 0) {
		for ($i=1;$i<(count($rctest)-1);$i++) {
			if ($rctest[$i]['cmmbmap'] == $rctest[$i-1]['cmmbmap'] && $rctest[$i]['cmmbmap'] == $rctest[$i+1]['cmmbmap']) { $delkeys[] = $i; }
		}
	}
	
	//file_put_contents($JobDir."/recomtest.txt", print_r($delkeys,TRUE));
		
	// Erstelle neues Highcharts-Objekt
	
	$chart = new Highchart();
	$chart->includeExtraScripts();
	
	// SNPs plotten
	if (count($snps) > 0) {
		$tmpsnps = array();
		foreach ($snps as $pos => $entry) {
			$tmpsnpline = array("x" => intval($pos), "y" => intval($entry['FUNC']-1), "snpname" => $entry['RSID']);
			if (array_key_exists('lineColor',$entry)) { $tmpsnpline["lineColor"] = $entry['lineColor']; } 
			if (array_key_exists('lineWidth',$entry)) { $tmpsnpline["lineWidth"] = $entry['lineWidth']; } 
			if (array_key_exists('fillColor',$entry)) { $tmpsnpline["fillColor"] = $entry['fillColor']; } 
			if (array_key_exists('radius',$entry)) { $tmpsnpline["radius"] = $entry['radius']; } 
			$tmpsnps[$entry['FUNC']][] = $tmpsnpline;
		}
		foreach($tmpsnps as $group => $data) {
			$chart->series[] = 
				array(
					"data" => $tmpsnps[$group], 
					"color" => "#EEEEEE",
					"marker" => array("lineColor" => "#444444", "lineWidth" => 1.2, "symbol" => $groupsymbol[$group-1], "radius" => $groupsymbolsize[$group-1], "states" => array("hover" => array("fillColor" => "#006600", "radius" => $groupsymbolsize[$group-1]+3))),
					"name" => $groupname[$group-1],
					"turboThreshold" => 0,
					"type" => "scatter",
					"index" => $group+5
				);
		}
		
		// SNPs - Legendensymbole für Multiple und Trait Associated
		$chart->series[] = 
			array(
				"data" => array("x" => 0, "y" => 0),
				"type" => "scatter",
				"color" => "#EEEEEE",
				"showInLegend" => true,
				"name" => "multiple effects",
				"marker" => array("enabled" => true, "states" => array("hover" => array("enabled" => false)), "symbol" => $groupsymbol[0], "lineWidth" => 2, "lineColor" => "#006611"),
				"events" => array("legendItemClick" => new HighchartJsExpr("function() { return false; }")),
				"index" => count($groupsymbol)+6
			);
		$chart->series[] = 
			array(
				"data" => array("x" => 0, "y" => 0),
				"type" => "scatter",
				"color" => "#EEEEEE",
				"showInLegend" => true,
				"name" => "associated with trait",
				"marker" => array("enabled" => true, "states" => array("hover" => array("enabled" => false)), "symbol" => $groupsymbol[0], "lineWidth" => 2, "lineColor" => "#003399"),
				"events" => array("legendItemClick" => new HighchartJsExpr("function() { return false; }")),
				"index" => count($groupsymbol)+7
			);
		
	}
	
	// Gene plotten
	if (count($genelist) > 0) {
		# Dummy-Eintrag für Legende
		$chart->series[] = 
			array(
				"data" => array("x"=>0,"y"=>0),
				"type" => "line", 
				"color" => "#009900", 
				"showInLegend" => true,
				"name" => "transcript",
				"id" => "genes",
				"marker" => array("enabled" => false, "states" => array("hover" => array("enabled" => false))),
				"index" => 3
			);
		
		for ($i=0;$i<count($genelist);$i++) {
			$tmpgendata = 
			array(
				array(
					"x" => $genelist[$i]['START'],
					"y" => $genelist[$i]['LAYER'],
					"genename" => $genelist[$i]['NAME'],
					"geneid" => $genelist[$i]['ID']
					),
				array(
					"x" => $genelist[$i]['middle'],
					"y" => $genelist[$i]['LAYER'],
					"label" => $genelist[$i]['label'],
					"genename" => $genelist[$i]['NAME'],
					"geneid" => $genelist[$i]['ID']
					),
				array(
					"x" => $genelist[$i]['STOP'],
					"y" => $genelist[$i]['LAYER'],
					"genename" => $genelist[$i]['NAME'],
					"geneid" => $genelist[$i]['ID']
					)
			);
			$chart->series[] = array(
				"data" => $tmpgendata,
				"type" => "line",
				"color" =>  $genelist[$i]['color'],
				"lineWidth" =>  $genelist[$i]['lineWidth'],
				"showInLegend" => false,
				"linkedTo" => "genes",
				"marker" => array("enabled" => false, "states" => array("hover" => array("enabled" => false))),
				"index" => 100+$i
			);

		}
	}
	
	// Regulatorische Elemente plotten
	if (count($regellist) > 0) {
		# Dummy-Eintrag für Legende
		$chart->series[] = 
			array(
				"data" => array("x"=>0,"y"=>0),
				"type" => "line", 
				"color" => "#0000FF", 
				"showInLegend" => true,
				"name" => "regulatory element",
				"id" => "regel",
				"marker" => array("enabled" => false, "states" => array("hover" => array("enabled" => false))),
				"index" => 2
			);
		
		for ($i=0;$i<count($regellist);$i++) {
			$tmpregeldata = 
			array(
				array(
					"x" => $regellist[$i]['START'],
					"y" => $regellist[$i]['LAYER'],
					"regelname" => $regellist[$i]['NAME']
					),
				array(
					"x" => $regellist[$i]['STOP'],
					"y" => $regellist[$i]['LAYER'],
					"regelname" => $regellist[$i]['NAME']
					)
			);
			$chart->series[] = array(
				"data" => $tmpregeldata,
				"type" => "line",
				"color" => "#0000FF",
				"showInLegend" => false,
				"linkedTo" => "regel",
				"marker" => array("enabled" => false, "states" => array("hover" => array("enabled" => false))),
				"index" => 1000+$i
			);

		}
	}
	
	// CMMB plotten
	if (count($recombrate > 0)) {
		$chart->series[] = 
		array(
			"data" => $recombrate,
			"type" => "line",
			"index" => 1,
			"showInLegend" => true,
			"name" => "recombination rate",
			"turboThreshold" => 0, 
			"marker" => array("enabled" => false, "states" => array("hover" => array("enabled" => false))),
			"color" => "#BBBBFF",
			"events" => array("legendItemClick" => new HighchartJsExpr("function() { if (this.visible) { detailchart.yAxis[1].update({labels: { enabled: false}, title: {text: null} }); } else { detailchart.yAxis[1].update({labels: { enabled: true}, title: {text: 'Recombination rate (cM/Mb)'} }); } }"))
		);
	}

	// y-Achse
	$chart->yAxis[0]->title = array("text" => "Functional annotation");
	$chart->yAxis[0]->min = $plotmin;
	$chart->yAxis[0]->max = $plotmax;
	$chart->yAxis[0]->tickPositions = array(1,2,3,4,$plotmax);
	$chart->yAxis[0]->startOnTick = false;
	$chart->yAxis[0]->endOnTick = true;
	$chart->yAxis[0]->tickinterval = 1;
	$chart->yAxis[0]->gridLineColor = "transparent";
	$chart->yAxis[0]->gridLineWidth = 0;
	$chart->yAxis[0]->labels->enabled = false;
	
	// y-Achse für CMMB
	$chart->chart->alignTicks = FALSE;
	$chart->yAxis[1]->title = array("text" => "Recombination rate (cM/Mb)");
	$chart->yAxis[1]->opposite = TRUE;
	$chart->yAxis[1]->min = $plotmin;
	$chart->yAxis[1]->max = $plotmax;
	$chart->yAxis[1]->startOnTick = false;
	$chart->yAxis[1]->endOnTick = false;
	$chart->yAxis[1]->tickPositions = array((0-0.15*$plotmax)+(0/60)*(6*$plotmax/8),(0-0.15*$plotmax)+(20/60)*(6*$plotmax/8),(0-0.15*$plotmax)+(40/60)*(6*$plotmax/8),(0-0.15*$plotmax)+(60/60)*(6*$plotmax/8));
	$chart->yAxis[1]->labels->formatter = new HighchartJsExpr("function() { return Math.round((this.value+".(0.15*$plotmax).")*(80/".$plotmax.")); }");
	$chart->yAxis[1]->gridLineColor = "transparent";
	$chart->yAxis[1]->gridLineWidth = 0;
	
	
	// x-Achse
	$chart->xAxis->title = array("text" => "Chromosome ".$Chr);
	$chart->xAxis->min = $plotxmin;
	$chart->xAxis->max = $plotxmax;
	
	// Tooltips
	$tooltipfun = "
		function() { 
			var annourl = 'backend/snipaVariantBrowserTooltips.php';
			var s = '';
			if (this.point.snpname) { 
				s = $.getAnnotations(annourl+'?jobid=".$JobId."&type=annotation&element=snp&name='+this.point.x); 
			}

			if (this.point.geneid) {
				s = '<strong>' + 
				this.point.genename + '</strong><br />' + 
				$.getAnnotations(annourl+'?jobid=".$JobId."&type=annotation&element=gene&name='+this.point.geneid);
			}
			if (this.point.cmmbmap) {
				s = 'Recombination rate: ' + this.point.cmmbmap + ' cM/Mb';
			}

			if (this.point.regelname) {
				s = '<strong>' + 
				this.point.regelname + '</strong><br />' +
				$.getAnnotations(annourl+'?jobid=".$JobId."&type=annotation&element=regelement&name='+this.point.regelname);
			}
			return s;
		}";
	$chart->tooltip->useHTML = true;
	$chart->tooltip->hideDelay = 0;
	$chart->tooltip->borderRadius = 0;
	$chart->tooltip->animation = false;
	$chart->tooltip->formatter = new HighchartJsExpr($tooltipfun);
	
	// Links für Gene und Regulatorische Elemente
	$linkfun = "
		function() { 
			var annourl = 'backend/snipaVariantBrowserTooltips.php';
			if (this.options.geneid) { url = $.getAnnotations(annourl+'?jobid=".$JobId."&type=link&element=gene&name='+this.options.geneid); }
			if (this.options.regelname) { url = $.getAnnotations(annourl+'?jobid=".$JobId."&type=link&element=regelement&name='+this.options.regelname); }
			if (url != null) { window.open(url,'_blank') }
		}";
	$chart->plotOptions->line =
		array(
			"animation" => false,
			"cursor" => "pointer",
			"point" => array("events" => array("click" => new HighchartJsExpr($linkfun))),
			"dataLabels" => array("enabled" => true, "formatter" => new HighchartJsExpr("function() { return this.point.label; } "))
		);
	
	// Mouseover-Effekt für SNPs (zeichnet vertikale rote Linie)
	$chart->plotOptions->scatter->point->events->mouseOver = new HighchartJsExpr("function() { detailchart.xAxis[0].addPlotLine({color: '#FF0000', width: 1, value: this.x, id: 'vertline'}); }");
	$chart->plotOptions->scatter->point->events->mouseOut = new HighchartJsExpr("function() { detailchart.xAxis[0].removePlotLine('vertline'); }");
	
	// Annotationsmenü für SNPs
	$annotationfun = "
		function(event) { 
			var tmpevent = event; 
			var tmpsnpname = this.snpname; 
			var tmpsnppos = this.x; 
			var tmpsnpchr = '".$Chr."';
			var tmpgenomerelease = $('select#dataset-genomerelease').val();
			var tmpreferenceset = $('select#dataset-referenceset').val();
			var tmppopulation = $('select#dataset-population').val();
			var tmpannotation = $('select#dataset-annotation').val();
			showPlotAnnotationMenu(tmpevent,tmpsnpname,tmpsnppos,tmpsnpchr,tmpsnppos,tmpgenomerelease,tmpreferenceset,tmppopulation,tmpannotation);
	}";
	$chart->plotOptions->scatter->point->events->click = new HighchartJsExpr($annotationfun);
	$chart->plotOptions->scatter->animation = false;
	$chart->plotOptions->scatter->cursor = "pointer";
	
	
	// Allgemeine Charteinstellungen
	$chart->chart->zoomType = "x";
	$chart->chart->animation = false;
	$chart->chart->plotBorderColor = "#000000";
	$chart->chart->plotBorderWidth = 1;
	$chart->title->text = null;
	$chart->credits->enabled = false;
	
	$chart->chart->renderTo = "detailcontainer";
	
	// Exportiereinstellungen
	$chart->exporting->url = "backend/HighchartsExport/";
	$chart->exporting->buttons->contextButton->align = "right";
	$chart->exporting->buttons->contextButton->verticalAlign = "top";
	$chart->exporting->filename = "snipa_variantbrowser_chr".$Chr."-".$Pos."_".$Genomerelease."_".$Referenceset."_".$Population."_".$Annotation;
	$chart->exporting->sourceWidth = 1024;
	$chart->exporting->sourceHeight = 768;
	
	// Reset-Zoom-Button verschieben
	$chart->chart->resetZoomButton->position = array("x" => -30, "y" => 10);
		
	//Ausgabe der Highchartsoptionen
	print("detailoptions = ".utf8_encode($chart->renderOptions()).";"); 
}




?>
