<?php


function snipaMapRsid($genomerelease,$referenceset,$population,$rsid) {
  global $db_server, $db_user, $db_pass;
  $cols_snps = array("CHR","POS");
  
  $dbcon = @mysql_connect($db_server,$db_user,$db_pass);
  
  $sql_snp = "SELECT ".implode(',',$cols_snps)." FROM snipa.`genomic-".mysql_real_escape_string($genomerelease)."-".mysql_real_escape_string($referenceset)."-".mysql_real_escape_string($population)."-snps` WHERE RSID = '".mysql_real_escape_string($rsid)."' LIMIT 1"; 
  
  $result_snp = mysql_query($sql_snp,$dbcon);
  $row_snp = mysql_fetch_row($result_snp);
  
  $chr = $row_snp[0];
  $pos = $row_snp[1];
  
  mysql_close($dbcon);
  return(array("CHR" => $chr,"POS" => $pos));
}


function snipaMapPos($genomerelease,$referenceset,$population,$chr,$posfrom,$posto) {
  // gibt zu einer Position den aktuellsten rs-Identifier aus
  // rückgabe: array([POS] => RSID)
  // Erstellen des Tabix-Files mit CHR POS RSID
  // tabix -s 1 -b 2 -e 2 -f XXX.gz

  global $path_to_tabix, $path_to_data;

  $fpath = $path_to_data."/genomic/".$genomerelease."/".$referenceset."/".$population."/mapping/".$genomerelease."-".$referenceset."-".$population."-snps.gz";
  if (!file_exists($fpath)) { die("Could not open tabix file"); }
  
  $qcmd = $path_to_tabix."/tabix ".escapeshellarg($fpath)." chr".escapeshellarg($chr).":".escapeshellarg($posfrom)."-".escapeshellarg($posto);
  exec($qcmd,&$qresult);
  $result = array();

  foreach ($qresult as $line) {
    $linesplit = explode("\t",$line);
	$result[$linesplit[1]] = $linesplit[2];
  }
  return($result);
}


function snipaMapPosAlias($genomerelease,$referenceset,$population,$chr,$posfrom,$posto) {
  // gibt zu einer Position alle rs-Identfier INKLUSIVE ALIASES aus
  // rückgabe: array([counter]  => RSID)
  // Erstellen des Tabix-Files mit CHR POS RSID
  // tabix -s 1 -b 2 -e 2 -f XXX.gz

  global $path_to_tabix, $path_to_data;

  $fpath = $path_to_data."/genomic/".$genomerelease."/".$referenceset."/".$population."/mapping/".$genomerelease."-".$referenceset."-".$population."-snpswithaliases.gz";
  if (!file_exists($fpath)) { die("Could not open tabix file"); }
  
  $qcmd = $path_to_tabix."/tabix ".escapeshellarg($fpath)." chr".escapeshellarg($chr).":".escapeshellarg($posfrom)."-".escapeshellarg($posto);
  exec($qcmd,&$qresult);
  $result = array();

  $counter = 0;
  foreach ($qresult as $line) {
    $linesplit = explode("\t",$line);
	$result[$counter][$linesplit[1]] = $linesplit[2];
	$counter++;
  }
  return($result);
}



?>
