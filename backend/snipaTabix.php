<?php


function snipaGetProxies($genomerelease,$referenceset,$population,$chr,$querypos,$pos1 = 0,$pos2 = 0,$r2threshold = 0) {

  // Szenario 1: Abfrage zu einem einzigen SNP: $chr und $querypos sowie $r2threshold müssen gesetzt sein, $pos1 und $pos2 bleiben leer.
  //             Ausgabe enthält - bis auf den Query-SNP - keine Selbstinformationen der gefundenen SNPs
  // Szenario 2: Bereichsabfrage: $r2threshold und $chr, $pos1 und $pos2 müssen gesetzt sein; $querypos bleibt leer.
  //             Ausgabe entspricht den Tabix-Daten, incl. Selbstinfos aller SNPs

  global $path_to_tabix, $path_to_data;
    
  // Pfad muss noch auf richtigkeit überprüft werden etc.
  $fpath = $path_to_data."/genomic/".$genomerelease."/".$referenceset."/".$population."/ld/".$genomerelease."-".$referenceset."-".$population."-chr".$chr."-ld.gz";
  if (!file_exists($fpath)) { die("Could not open tabix file"); }
  
  // Szenario 1: Abfrage eines einzelnen Query-SNPs
  if ($pos1 == "") { $pos1 = $querypos; }
  if ($pos2 == "") { $pos2 = $pos1;  }

  // Szenario 2: Bereichsabfrage
  $range = FALSE;
  if ($querypos == "" || $querypos == 0) { $range = TRUE; }
  
  $qcmd = $path_to_tabix."/tabix ".escapeshellarg($fpath)." chr".escapeshellarg($chr).":".escapeshellarg($pos1)."-".escapeshellarg($pos2);
  exec($qcmd,&$qresult);
  $result = array();

  // Header einfuegen
  exec("zcat ".escapeshellarg($fpath)." | head --lines=1",&$hresult);
  $colnames = explode("\t",$hresult[0]);
  $r2col = array_search("R2",$colnames);
  $pos1col = array_search("POS1",$colnames);
  $pos2col = array_search("POS2",$colnames);
  $rsidcol = array_search("RSID",$colnames);
    
  $linecount = 0;
  foreach ($qresult as $line) {
    $linesplit = explode("\t",$line);
    // Szenario 1: Selbstinfo bei nicht-Query-SNPs rausfiltern (überspringe Zeile falls pos1 und pos2 gleich sind, es sei denn bei Query-SNP-Position
    if (($range == FALSE) && !((($linesplit[$pos1col] == $querypos) && ($linesplit[$pos2col] == $querypos)) || ($linesplit[$pos1col] != $linesplit[$pos2col]))) { continue; }
	// ansonsten Szenario 2 (Bereichsabfrage)
    
   // Filtere SNPs aus, die keine RSID haben
   if (preg_match("/^rs/",$linesplit[$rsidcol]) === 0) { continue; }
	
   // R2 Filter auf Ausgabedaten anwenden
	if ($linesplit[$r2col] >= $r2threshold) { 
       $colcount = 0;
       foreach ($colnames as $colname) {
          $result[$linecount][$colname] = $linesplit[$colcount];
          $colcount++;
       }
	   // Füge Spalte mit der Distanz zwischen POS2 und POS2 hinzu
	   $result[$linecount]['DIST'] = ($linesplit[$pos2col]-$linesplit[$pos1col]);
	   // Chromosomangabe: führendes "chr" entfernen
	   $result[$linecount]['CHR'] = preg_replace('/[^\d|X]/','',$result[$linecount]['CHR']);
	   
       $linecount++;
     }
  }

  return($result);
}



function snipaGetSelfinfo($genomerelease,$referenceset,$population,$chr = 0,$posfrom = 0,$posto = 0) {

  global $path_to_tabix, $path_to_data;
  set_time_limit(240);
	
  $fpath = $path_to_data."/genomic/".$genomerelease."/".$referenceset."/".$population."/self/".$genomerelease."-".$referenceset."-".$population."-chr".$chr."-self.gz";
  if (!file_exists($fpath)) { die("Could not open tabix file - ".$fpath); }
  
  $qcmd = $path_to_tabix."/tabix ".escapeshellarg($fpath)." chr".escapeshellarg($chr).":".escapeshellarg($posfrom)."-".escapeshellarg($posto);
  exec($qcmd,&$qresult);
  $result = array();

  // Header einfuegen
  exec("zcat ".escapeshellarg($fpath)." | head --lines=1",&$hresult);
  $colnames = explode("\t",$hresult[0]);
  $pos1col = array_search("POS1",$colnames);
  $pos2col = array_search("POS2",$colnames);
  $rsidcol = array_search("RSID",$colnames);
    
  $linecount = 0;
  foreach ($qresult as $line) {
    $linesplit = explode("\t",$line);
    
	// Filtere SNPs aus, die keine RSID haben
	if (preg_match("/^rs/",$linesplit[$rsidcol]) === 0) { continue; }
	
       $colcount = 0;
       foreach ($colnames as $colname) {
          $result[$linecount][$colname] = $linesplit[$colcount];
          $colcount++;
       }
	   
	   $result[$linecount]['CHR'] = preg_replace('/[^\d|X]/','',$result[$linecount]['CHR']);
	   
	   
	   
       $linecount++;
    
  }

  return($result);
}



function snipaGetSNPAnnotations($genomerelease,$annotation,$chr = 0,$posfrom = 0,$posto = 0, $compressed = FALSE) {
  // bei compressed wird das phparray sowie das tooltip-html (annotation) nicht ausgegeben

  global $path_to_tabix, $path_to_data;
  
  $fpath = $path_to_data."annotation/".$genomerelease."/".$annotation."/annotation/chr".$chr.".tabix.gz";
  if (!file_exists($fpath)) { die("Could not open tabix file"); }
  
  $qcmd = $path_to_tabix."/tabix ".escapeshellarg($fpath)." chr".escapeshellarg($chr).":".escapeshellarg($posfrom)."-".escapeshellarg($posto);
  exec($qcmd,&$qresult);
  $result = array();

  // Header einfuegen
  exec("zcat ".escapeshellarg($fpath)." | head --lines=1",&$hresult);
  $colnames = explode("\t",$hresult[0]);
  
 
  $linecount = 0;
  foreach ($qresult as $line) {
    $linesplit = explode("\t",$line);
    
       $colcount = 0;
       foreach ($colnames as $colname) {
          if ($compressed == TRUE && ($colname == "PHPARRAY" || $colname == "ANNOTATION")) { 
			$result[$linecount][$colname] = ""; $colcount++; continue; 
		  }
		  $result[$linecount][$colname] = $linesplit[$colcount];
		  // Entferne "chr" von der Chromosomangabe
		  $result[$linecount]['CHR'] = preg_replace('/[^\d|X]/','',$result[$linecount]['CHR']);
          $colcount++;
       }
	   $linecount++;
    
  }

  return($result);
}



?>
