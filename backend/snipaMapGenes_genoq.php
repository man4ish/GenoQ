<?php


function snipaMapGenes($genomerelease,$annotation,$chr,$posfrom,$posto) {
  global $db_server, $db_user, $db_pass;
  $cols_genes = array("ID","NAME","CHR","START","STOP","SIZE","STRAND","HIGHLIGHT","ANNOTATION","LINK","PHPARRAY");
  
  $output = array();
  
  $dbcon = @mysql_connect($db_server,$db_user,$db_pass);
  
  $sql_pos = "SELECT ".implode(',',$cols_genes)." FROM snipa.`annotation-".$genomerelease."-".$annotation."-genes` WHERE CHR = '".$chr."' AND STOP >= '".$posfrom."' AND START <= '".$posto."'"; 
  
  $result = mysql_query($sql_pos,$dbcon);
  
  $rowcount = 0;
  while($row = mysql_fetch_assoc($result)) {
    	$output[$rowcount] = $row;
		$rowcount++;
  }

  mysql_close($dbcon);
  return($output);
}


function snipaMapRegulatory($genomerelease,$annotation,$chr,$posfrom,$posto) {
  global $db_server, $db_user, $db_pass;
  $cols_anno = array("NAME","CHR","START","STOP","SIZE","ANNOTATION","LINK","PHPARRAY");
  
  $output = array();
  
  $dbcon = @mysql_connect($db_server,$db_user,$db_pass);
  
  $sql_pos = "SELECT ".implode(',',$cols_anno)." FROM snipa.`annotation-".$genomerelease."-".$annotation."-regulatory` WHERE CHR = '".$chr."' AND STOP >= '".$posfrom."' AND START <= '".$posto."'"; 
  
  $result = mysql_query($sql_pos,$dbcon);
  
  $rowcount = 0;
  while($row = mysql_fetch_assoc($result)) {
    	$output[$rowcount] = $row;
		$rowcount++;
  }

  mysql_close($dbcon);
  return($output);
}


function snipaMapCNV($genomerelease,$annotation,$chr,$posfrom,$posto) {
  global $db_server, $db_user, $db_pass;
  $cols_anno = array("ID","NAME","CHR","START","STOP","SIZE","STRAND","HIGHLIGHT","ANNOTATION","LINK","PHPARRAY");

  $output = array();

  $dbcon = @mysql_connect($db_server,$db_user,$db_pass);

 

  $sql_pos = "SELECT ".implode(',',$cols_anno)." FROM snipa.`annotation-genoq-cnv` WHERE CHR = '".$chr."' AND STOP <= '".$posto."' AND START >= '".$posfrom."'";

  $file = fopen("/home/metabolomics/snipa/web/backend/cnvtest.txt","w");
  fwrite($file,$sql_pos);
  fclose($file);

  $result = mysql_query($sql_pos,$dbcon);

  $rowcount = 0;
  while($row = mysql_fetch_assoc($result)) {
        $output[$rowcount] = $row;
                $rowcount++;
  }

  mysql_close($dbcon);
  return($output);
}



// Gibt es den Ensembl Gene identifier in der DB?
function snipaGeneExists($genomerelease,$annotation,$ensemblid) {
   global $db_server, $db_user, $db_pass;
  $dbcon = @mysql_connect($db_server,$db_user,$db_pass) or die (mysql_error());
  mysql_select_db('snipa');
  $sql_pos = "SELECT * FROM `annotation-".$genomerelease."-".$annotation."-synonyms` WHERE NAME = '".mysql_real_escape_string(trim($ensemblid))."' LIMIT 0,10"; 
  $result = mysql_query($sql_pos,$dbcon);
  mysql_close($dbcon);
  if (mysql_num_rows($result) > 0) { return(true); } else { return(false); }
}


// Start- und Stopposition zum Ensembl Identifier
function snipaGeneLocation($genomerelease,$annotation,$ensemblid) {
  global $db_server, $db_user, $db_pass;
  $cols_genes = array("ID","CHR","START","STOP","SIZE");
  $output = array();
  $dbcon = @mysql_connect($db_server,$db_user,$db_pass);
  $sql_pos = "SELECT ".implode(',',$cols_genes)." FROM snipa.`annotation-".$genomerelease."-".$annotation."-genes` WHERE ID = '".$ensemblid."'"; 
  $result = mysql_query($sql_pos,$dbcon);
  $rowcount = 0;
  while($row = mysql_fetch_assoc($result)) {
    	$output[$rowcount] = $row;
		$rowcount++;
  }
  mysql_close($dbcon);
  return($output);
}



?>
