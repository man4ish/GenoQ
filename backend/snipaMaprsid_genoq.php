<?php
function snipaMapRsid($genomerelease,$referenceset,$population,$rsid) {
  global $db_server, $db_user, $db_pass;
  $cols_snps = array("CHR","POS");

  if($population="qtr"){$referenceset="1kgpp1v3";}  
  $myfile = fopen("rsfile.txt", "a") or die("Unable to open file!");
  $dbcon = @mysql_connect($db_server,$db_user,$db_pass);
   
  $sql_snp = "SELECT ".implode(',',$cols_snps)." FROM snipa.`genomic-".$genomerelease."-".$referenceset."-".$population."-snps` WHERE RSID = '".$rsid."' LIMIT 1"; 
  fwrite($myfile, $sql_snp.";\n");  
  $result_snp = mysql_query($sql_snp,$dbcon);
  $row_snp = mysql_fetch_row($result_snp);
  
  $chr = $row_snp[0];
  $pos = $row_snp[1];
  
  fclose($myfile);
  mysql_close($dbcon);
  return(array("CHR" => $chr,"POS" => $pos));
}


function snipaMapPos($genomerelease,$referenceset,$population,$chr,$posfrom,$posto) {
  // Erstellen des Tabix-Files mit CHR POS RSID
  // tabix -s 1 -b 2 -e 2 -f XXX.gz

  $myfile = fopen("newfile2.txt", "w") or die("Unable to open file!");
  

  global $path_to_tabix, $path_to_data;

  $fpath = $path_to_data."/genomic/".$genomerelease."/".$referenceset."/".$population."/mapping/".$genomerelease."-".$referenceset."-".$population."-snps.gz";
  fwrite($myfile, $fpath); 
  if (!file_exists($fpath)) { die("Could not open tabix file"); }
  

  $qcmd = $path_to_tabix."/tabix ".escapeshellarg($fpath)." chr".$chr.":".$posfrom."-".$posto;
 
  
  exec($qcmd,&$qresult);
  
  $result = array();

  foreach ($qresult as $line) {
    $linesplit = explode("\t",$line);
	$result[$linesplit[1]] = $linesplit[2];
	#fwrite($myfile, $line);
  }
  
  fclose($myfile);
  return($result);
}



#need to move this function inside in snipaLDPlots.php  
function snipaMapPos2($genomerelease,$referenceset,$population,$chr,$posfrom,$posto,$flname,$ref,$JobDir) {
    $txt = "Jane Doe\n";
  // Erstellen des Tabix-Files mit CHR POS RSID
  // tabix -s 1 -b 2 -e 2 -f XXX.gz
  $maflag=0;

  $myfile = fopen($flname, "w") or die("Unable to open file!");
  

  global $path_to_tabix, $path_to_data;

  $fpath = $path_to_data."/genomic/".$genomerelease."/".$referenceset."/".$population."/ld/".$genomerelease."-".$referenceset."-".$population."-chr".$chr."-ld.gz";
   
  if (!file_exists($fpath)) { die("Could not open tabix file"); }
  
   
  #fwrite($myfile,"$Referenceset\n");
  $qcmd = $path_to_tabix."/tabix ".escapeshellarg($fpath)." chr".$chr.":".$posfrom."-".$posto;	
  if($referenceset=="1kgpp3v5") 
  {  
           fwrite($myfile,"CHR\tPOS1\tPOS2\tR2\tD\tDPRIME\tRSID\tRSID_SYN\tMINOR\tMAF\tMAJOR\tCMMB\tCM\n");  
  } else 
  {
           fwrite($myfile,"CHR\tPOS1\tPOS2\tR2\tD\tDPRIME\tRSID\tMINOR\tMAF\tMAJOR\tCMMB\tCM\n");
  }
  //fwrite($myfile, $qcmd); 
  
  exec($qcmd,&$qresult);
  
  $result = array();

  foreach ($qresult as $line) {
    $linesplit = explode("\t",$line);
	$result[$linesplit[1]] = $line;
	fwrite($myfile, $line."\n");
  }
  
  fclose($myfile);
  //echo exec('Rscript /home/metabolomics/snipa/web/backend/generate_static_plot.R '. $flname . ' ' . $population . '  1 '.'.');
  //echo exec('Rscript /home/metabolomics/snipa/web/backend/testfunction.R '. $flname . ' ' . $population . '  1 '.'.');
  #if($ref=="1"){echo exec('Rscript /home/metabolomics/snipa/web/backend/generate_plot.R');}
  $myfile = fopen("rnewfile.txt", "a") or die("Unable to open file!");
  echo system('cp /home/metabolomics/snipa/web/backend/generate_plot.R '.$JobDir);
  if($ref=="1"){
    $maflag=0;
    #echo exec('Rscript /home/metabolomics/snipa/web/backend/testfunction.R '. $flname . ' ' . $population . '  1 '.'.');
    fwrite($myfile, './snipaGenerateMatrix '. $flname . ' ' . $population . '  1 '.'. '.$maflag.'\n'); 
    echo exec('./snipaGenerateMatrix '. $flname . ' ' . $population . '  1 '.'. '.$maflag.' '.$JobDir);
    echo exec('Rscript '.$JobDir.'/generate_plot.R '.$JobDir);
  }
  else {
    if($referenceset=="1kgpp3v5")
    {
      $maflag=1;
    }

    fwrite($myfile, './snipaGenerateMatrix '. $flname . ' ' . $population . '  0 '.'. '.$maflag.'\n');
    #echo exec('Rscript /home/metabolomics/snipa/web/backend/testfunction.R '. $flname . ' ' . $population . '  0 '.'.');
    echo exec('./snipaGenerateMatrix '. $flname . ' ' . $population . '  0 '.'. '.$maflag.' '.$JobDir);
  }
   
  fclose($myfile);

#echo exec('./snipaGenerateMatrix '. $flname . ' ' . $population . '  0 '.'.');
  //$txt = "Jane Doe\n";
  return($result);
}


function snipaMapCpg($chr,$posfrom,$posto) {

  global $path_to_tabix, $path_to_data;

  $fpath="/home/metabolomics/snipa/data/genomic/grch37/genoq/qtr/qtrdb/genoq/cpg/cpg.chr".$chr.".gz";
  if (!file_exists($fpath)) { die("Could not open tabix file"); }

  //$path_to_tabix="/usr/bin";

  $qcmd = $path_to_tabix."/tabix ".escapeshellarg($fpath)." chr".$chr.":".$posfrom."-".$posto;
  
  //$qcmd = "tabix ".escapeshellarg($fpath)." chr".$chr.":".$posfrom."-".$posto;

  exec($qcmd,&$qresult);

  $result = array();

  foreach ($qresult as $line) {
    $linesplit = explode("\t",$line);
        $result[$linesplit[1]] = $line;
  }

  return($result);
}


?>
