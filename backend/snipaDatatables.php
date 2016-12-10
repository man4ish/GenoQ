<?php

$jobid = preg_replace('/[^0-9]/','',$_REQUEST['id']);
$type = $_REQUEST['type'];
$content = $_REQUEST['content'];
if ($type == "pages") {
	$sentinelstart = $_REQUEST['iDisplayStart'];
	$sentinelcount = $_REQUEST['iDisplayLength'];
	$sentinelstart = preg_replace('/[^0-9]/','',$sentinelstart);
	$sentinelcount = preg_replace('/[^0-9]/','',$sentinelcount);
}


$tabledatafn = "../tmpdata/".$jobid."/proxySearch.phparray.txt";

function number_format_2dec($x) { return number_format($x, 2); }
function number_format_3dec($x) { return number_format($x, 3); }
function proxy_annotation_link($snp,$pos,$chr,$sentinelpos) { return "<span class='table-annotatable' onclick='showPlotAnnotationMenu(event,\"".$snp."\",".$pos.",".$chr.",".$sentinelpos.",$(\"select#dataset-genomerelease\").val(),$(\"select#dataset-referenceset\").val(),$(\"select#dataset-population\").val(),$(\"select#dataset-annotation\").val());'>".$snp."</span>"; }
function rsaliases($x) { if ($x == "NA") { $y = ""; } else { $y = implode(', ',explode(',',$x)); } return $y; }


if (!file_exists("../tmpdata/".$jobid."/proxySearch.showsentinelsonly")) {
$columns = array("QRSID" => array("desc" => "Sentinel", "type" => "string", "searchable" => true),
				"RSID" => array("desc" => "Proxy", "format" => "proxy_annotation_link", "type" => "string", "searchable" => true),
				"RSALIAS" => array("desc" => "Proxy alias", "format" => "rsaliases", "type" => "string", "searchable" => true, "visible" => false),
				"CHR" => array("desc" => "Chr.", "type" => "numeric", "searchable" => false),
				"POS1" => array("desc" => "Sentinel Pos.", "format" => "number_format", "type" => "formatted-num", "searchable" => false, "visible" => false),
				"POS2" => array("desc" => "Proxy Pos.", "format" => "number_format", "type" => "formatted-num", "searchable" => false),
				"DIST" => array("desc" => "Distance (bp)", "format" => "number_format", "type" => "formatted-num", "searchable" => false),
				"R2" => array("desc" => "LD r&sup2;", "format" => "number_format_2dec", "type" => "numeric", "searchable" => false),
				"D" => array("desc" => "LD D", "format" => "number_format_2dec", "type" => "numeric", "searchable" => false),
				"DPRIME" => array("desc" => "LD D'", "format" => "number_format_2dec", "type" => "numeric", "searchable" => false),
				"MAJOR" => array("desc" => "Proxy Allele A", "type" => "string", "searchable" => false, "visible" => false),
				"MINOR" => array("desc" => "Proxy Allele B", "type" => "string", "searchable" => false),
				"MAF" => array("desc" => "Allele B Frequency", "format" => "number_format_3dec", "type" => "numeric", "searchable" => false),
				"CMMB" => array("desc" => "Recombination rate (CM&frasl;Mb)", "format" => "number_format_2dec", "type" => "numeric", "searchable" => false, "visible" => false),
				"CM" => array("desc" => "Genetic distance (CM)", "format" => "number_format_2dec", "type" => "numeric", "searchable" => false, "visible" => false),
				"COMPEFFECTS" => array("desc" => "Proxy annotation", "type" => "string", "searchable" => true),
				"TRAIT" => array("desc" => "Associated with trait(s)", "type" => "string", "searchable" => false),
				"CISEQTL" => array("desc" => "cis-eQTL", "type" => "string", "searchable" => false),
				"TRANSEQTL" => array("desc" => "trans-eQTL", "type" => "string", "searchable" => false),
				"GENES" => array("desc" => "Genes hit or close-by", "type" => "string", "searchable" => true),
				"REGGENES" => array("desc" => "Potentially regulated genes", "type" => "string", "searchable" => true),
				"EQTLGENES" => array("desc" => "eQTL genes", "type" => "string", "searchable" => true)
	);
} else {
	$columns = array("RSID" => array("desc" => "Variant", "format" => "proxy_annotation_link", "type" => "string", "searchable" => true),
				"RSALIAS" => array("desc" => "Variant alias", "format" => "rsaliases", "type" => "string", "searchable" => true, "visible" => false),
				"CHR" => array("desc" => "Chr.", "type" => "numeric", "searchable" => false),
				"POS2" => array("desc" => "Pos.", "format" => "number_format", "type" => "formatted-num", "searchable" => false),
				"MAJOR" => array("desc" => "Allele A", "type" => "string", "searchable" => false, "visible" => false),
				"MINOR" => array("desc" => "Allele B", "type" => "string", "searchable" => false),
				"MAF" => array("desc" => "Allele B Frequency", "format" => "number_format_3dec", "type" => "numeric", "searchable" => false),
				"CMMB" => array("desc" => "Recombination rate (CM&frasl;Mb)", "format" => "number_format_2dec", "type" => "numeric", "searchable" => false, "visible" => false),
				"CM" => array("desc" => "Genetic distance (CM)", "format" => "number_format_2dec", "type" => "numeric", "searchable" => false, "visible" => false),
				"COMPEFFECTS" => array("desc" => "Annotation", "type" => "string", "searchable" => true),
				"TRAIT" => array("desc" => "Associated with trait(s)", "type" => "string", "searchable" => false),
				"CISEQTL" => array("desc" => "cis-eQTL", "type" => "string", "searchable" => false),
				"TRANSEQTL" => array("desc" => "trans-eQTL", "type" => "string", "searchable" => false),
				"GENES" => array("desc" => "Genes hit or close-by", "type" => "string", "searchable" => true),
				"REGGENES" => array("desc" => "Potentially regulated genes", "type" => "string", "searchable" => true),
				"EQTLGENES" => array("desc" => "eQTL genes", "type" => "string", "searchable" => true)
	);
}

// Sortieren der Reihenfolge der Keys des Ausgabearrays  
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


// Sortieren der Tabelle nach einer Spalte, basierend auf php.net/array_multisort
// array_msort($arr1, array('name'=>SORT_DESC, 'cat'=>SORT_ASC));
/*
function array_msort($array, $cols)
{
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
    }
    return $ret;
}

*/



// Ausgabe als nicht-sortierbare Tabelle
if ($type == "pages") {
	if ($content == "header") {

		// Liefere nur Spalten, die auch im phpArray sind
		$tabledata = new SplFileObject($tabledatafn);
		$foundvalidentry = FALSE;
		$categories = array();
		while (!$tabledata->eof() && !($foundvalidentry)) {
			$line = explode("\t",$tabledata->current());
			if (count($line) === 2) {
				$tmp = unserialize($line[1]);
				if (count($tmp) > 0) { $tmp[0]["QRSID"] = ""; $categories = array_flip(array_keys($tmp[0])); $foundvalidentry = TRUE;}
			}
			$tabledata->next();
		}
		$columns = array_intersect_key($columns,$categories);
		
		$header = array();
		$colcount = 0;
		foreach($columns as $column) { 
			$tmp = array();
			$tmp["sTitle"] = $column['desc'];
			$tmp["sType"] = $column['type'];
			$tmp["bSearchable"] = false;
			$tmp["bSortable"] = false;
			$tmp["aTargets"] = array($colcount); $colcount++;
			if (array_key_exists('visible',$column)) { $tmp["bVisible"] = $column['visible']; } else { $tmp["bVisible"] = true; }
			$header[] = $tmp;
		}
		
		print(json_encode(array_map_recursive(function($t) { return is_string($t) ? utf8_encode($t) : $t; }, array_values($header)), JSON_NUMERIC_CHECK));

	}
	
	if ($content == "data") {
		$jsontable = array();
		
		if ($sentinelstart == "") { $sentinelstart = 0; }
		if ($sentinelcount == "") { $sentinelcount = 10; }
		
		// Öffne php-Array Datei
		$tabledata = new SplFileObject($tabledatafn);
		
		//Anzahl der Sentinels
		$tabledata->seek($tabledata->getSize());
		$tabledatalines = $tabledata->key();
		$sentinelstart = min($tabledatalines, $sentinelstart);
		
		
		// Springe zur n-ten Zeile, gegeben durch $sentinelstart
		$tabledata->seek($sentinelstart);
		
		// Gebe die Anzahl der Zeilen aus, die durch $sentinelcount gegeben ist
		$linecount = 1;
		while (!$tabledata->eof() && ($linecount <= $sentinelcount)) {
				$line = explode("\t",$tabledata->current());
				if (count($line) === 2) {
					$sentinel = unserialize($line[0]);
					$proxy = array();
					foreach(unserialize($line[1]) as $tmp) {
						
						// Füge Sentinel hinzu
						$tmp['QRSID'] = $sentinel['RSID'];
						
						// Annotationslink einfügen
						$tmp['RSID'] = proxy_annotation_link($tmp['RSID'],$tmp['POS2'],$tmp['CHR'],$tmp['POS1']);
						
						// Durchsuche colums-Array und führe Funktion auf Werte aus, die ggf. bei "format" angegeben ist - Ausnahme: Annotations-Link
						array_walk($tmp, function(&$value, $key) { 
							global $columns; 
							if (array_key_exists("format",$columns[$key])) { 
								if ($columns[$key]['format'] != "proxy_annotation_link") {
									$value = call_user_func($columns[$key]['format'],$value); 
								}
							}
						});
						$proxy = $tmp + $proxy;
						$jsontable[] = array_values(sortArrayByArray($proxy,array_keys($columns))); 
					}
				}
				$tabledata->next();
				$linecount++;
			}
			
		print(json_encode(array_map_recursive(function($t) { return is_string($t) ? utf8_encode($t) : $t; }, array("sEcho" => intval($_REQUEST['sEcho']), "iTotalRecords" => $tabledatalines, "iTotalDisplayRecords" => $tabledatalines, "aaData" => array_values($jsontable))), JSON_NUMERIC_CHECK));
		}
	}



// Ausgabe als JSON Array
if ($type == "all") {
	
	if ($content == "header") {

		// Liefere nur Spalten, die auch im phpArray sind
		$tabledata = new SplFileObject($tabledatafn);
		$foundvalidentry = FALSE;
		$categories = array();
		while (!$tabledata->eof() && !($foundvalidentry)) {
			$line = explode("\t",$tabledata->current());
			if (count($line) === 2) {
				$tmp = unserialize($line[1]);
				if (count($tmp) > 0) { $tmp[0]["QRSID"] = ""; $categories = array_flip(array_keys($tmp[0])); $foundvalidentry = TRUE;}
			}
			$tabledata->next();
		}
		$columns = array_intersect_key($columns,$categories);
		
		$header = array();
		$colcount = 0;
		foreach($columns as $column) { 
			$tmp = array();
			$tmp["sTitle"] = $column['desc'];
			$tmp["sType"] = $column['type'];
			$tmp["bSearchable"] = $column['searchable'];
			if (array_key_exists('visible',$column)) { $tmp["bVisible"] = $column['visible']; } else { $tmp["bVisible"] = true; }
			$tmp["aTargets"] = array($colcount); $colcount++;
			
			$header[] = $tmp;
		}
		
		print(json_encode(array_map_recursive(function($t) { return is_string($t) ? utf8_encode($t) : $t; }, array_values($header)), JSON_NUMERIC_CHECK));

	}
	
	if ($content == "data") {
	
		ini_set('memory_limit','1024M');
		
		// Liefere nur Spalten, die auch im phpArray sind
		$tabledata = new SplFileObject($tabledatafn);
		$foundvalidentry = FALSE;
		$categories = array();
		while (!$tabledata->eof() && !($foundvalidentry)) {
			$line = explode("\t",$tabledata->current());
			if (count($line) === 2) {
				$tmp = unserialize($line[1]);
				if (count($tmp) > 0) { $tmp[0]["QRSID"] = ""; $categories = array_flip(array_keys($tmp[0])); $foundvalidentry = TRUE;}
			}
			$tabledata->next();
		}
		$columns = array_intersect_key($columns,$categories);
		
		$header = array();
		$colcount = 0;
		foreach($columns as $column) { 
			$tmp = array();
			$tmp["sTitle"] = $column['desc'];
			$tmp["sType"] = $column['type'];
			$tmp["bSearchable"] = $column['searchable'];
			if (array_key_exists('visible',$column)) { $tmp["bVisible"] = $column['visible']; } else { $tmp["bVisible"] = true; }
			$tmp["aTargets"] = array($colcount); $colcount++;
			$header[] = $tmp;
			
		}
		
		
		$limit = 25000;
		$curlimit = 0;
		$jsontable = array();
			
		$tabledata = new SplFileObject($tabledatafn);
		while (!$tabledata->eof() && ($curlimit <= $limit)) {
			$line = explode("\t",$tabledata->fgets());
			if (count($line) === 2) {
				$sentinel = unserialize($line[0]);
				$proxy = array();
				foreach(unserialize($line[1]) as $tmp) {
					
			
					// Füge Sentinel hinzu
					$tmp['QRSID'] = $sentinel['RSID'];
					
					// Annotationslink einfügen
					$tmp['RSID'] = proxy_annotation_link($tmp['RSID'],$tmp['POS2'],$tmp['CHR'],$tmp['POS1']);
					
					// Durchsuche colums-Array und führe Funktion auf Werte aus, die ggf. bei "format" angegeben ist - Ausnahme: Annotations-Link
					array_walk($tmp, function(&$value, $key) { 
						global $columns; 
						if (array_key_exists("format",$columns[$key])) { 
							if ($columns[$key]['format'] != "proxy_annotation_link") {
								$value = call_user_func($columns[$key]['format'],$value); 
							}
						}
					});
					
					
					$proxy = $tmp + $proxy;
					
					$jsontable[] = array_values(sortArrayByArray($proxy,array_keys($columns))); 
					$curlimit++;
				}
			}
		}
		
		print(json_encode(array_map_recursive(function($t) { return is_string($t) ? utf8_encode($t) : $t; }, array("aaData" => array_values($jsontable))), JSON_NUMERIC_CHECK));
	}
}
	

?>
