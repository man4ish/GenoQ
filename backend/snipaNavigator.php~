<?php
require_once( "/home/metabolomics/snipa/web/backend/snipaMaprsid.php" );
require_once( "/home/metabolomics/snipa/web/backend/snipaMapGenes.php" );
require_once( "/home/metabolomics/snipa/web/backend/snipaTabix.php" );
require_once( "/home/metabolomics/snipa/web/backend/snipaConfig.php" );


/*
$loginputfile = fopen($argv[1],"r");
$res= fgets($loginputfile);
$rec = explode( "\t", rtrim( $res ) );
$Genomerelease = $rec[0];
$Referenceset  = $rec[1];
$Population    = $rec[2];
$Annotation    = $rec[3];
$flag          = $rec[4];
$JobId         = $rec[5];
$min           = $rec[6];
$max           = $rec[7];
//$chr           = $rec[8];
$forward       = -1;
$chr           = $rec[8];
$static_plot=0;


// for ajax call

*/
$Genomerelease = $_GET['genomerelease'];
$Referenceset  = $_GET['referenceset'];
$Population    = $_GET['population'];
$Annotation    = $_GET['annotation'];
$JobId         = $_GET['jobid'];
$min           = $_GET['start'];
$max           = $_GET['stop'];
$chr           = $_GET['chr'];
$static_plot   = $_GET['recordID'];
#$static_plot=0;



  


//echo exec( 'rm /home/metabolomics/snipa/web/frontend/js/tmp/data.sample');


$start="";
if($forward == "1")  //forward
{
    $max=$max+2000;
} else if($forward=="2"){  // zoomout
    $max=$max+2000;
    $min =$min-2000;
} else if($forward=="-1")  //back
{
   $min=$min+2000;
} else if($forward == "-2")  // zoomin
{
   $max=$max-2000;
   $min =$min+2000;
}
//fclose($loginputfile);
 
$SnpsInputType = "snps";
$Rsquare       = 0.1;


$allok = TRUE;

$JobId = preg_replace( '/[^0-9]/', '', $JobId );
if ( strlen( $JobId ) != 15 ) {
    $allok = FALSE;
}

if ( $allok ) {
    $tmpdatadir = "tmpdata";
    $serverdir  = "/home/metabolomics/snipa/web/";
    $JobDir     = $serverdir . "/" . $tmpdatadir . "/" . $JobId;
    if ( !file_exists( $JobDir ) ) {
        $allok = FALSE;
    }
}



$file = fopen($JobDir."/test.txt","w");

$Genomerelease = $_GET['genomerelease'];
$Referenceset  = $_GET['referenceset'];
$Population    = $_GET['population'];
$Annotation    = $_GET['annotation'];
$JobId         = $_GET['jobid'];
$min           = $_GET['start'];
$max           = $_GET['stop'];
$chr           = $_GET['chr'];
$static_plot   = $_GET['recordID'];
#$static_plot=0;
echo fwrite($file,$Genomerelease."\n");
echo fwrite($file,$Referenceset."\n");
echo fwrite($file,$Population."\n");
echo fwrite($file,$Annotation."\n");
echo fwrite($file,$JobId."\n");
echo fwrite($file,$min."\n");
echo fwrite($file,$max."\n");
echo fwrite($file,$chr."\n");
echo fwrite($file,$static_plot."\n");
fclose($file);


function getsenitalsnps($genomerelease,$referenceset,$population,$annotation, $chr,$posfrom, $posto)
{   
  global $path_to_tabix, $path_to_data;

  $fpath = $path_to_data."/genomic/".$genomerelease."/".$referenceset."/".$population."/ld/".$genomerelease."-".$referenceset."-".$population."-chr".$chr."-ld.gz";

  echo $fpath."\n";
  
  if (!file_exists($fpath)) { die("Could not open tabix file"); }

  $qcmd = $path_to_tabix."/tabix ".escapeshellarg($fpath)." chr".$chr.":".$posfrom."-".$posto;

  echo $qcmd ."\n";

  exec($qcmd,&$qresult);

  return($qresult);
}


if($static_plot==0)
{
  // $test = fopen('/home/metabolomics/snipa/web/backend/sample2.txt', 'w' );
 //fwrite( $test,$chr." ".$min." ".$max."\n");
 //fwrite($test,$Genomerelease." ".$Referenceset." ".$Population." ".$Annotation." ".$chr." ".$min." ".$max."\n");
 //fclose($test); 
  $flag=0;
  $flname = $JobDir . "/LDHeatMapZoom_" . $flag .".csv";
    
  if ( file_exists( $flname ) ) {
        unlink( $flname );
  }
  $dlfile = fopen( $flname, 'a' );
  #if($Referenceset=="1kgpp3v5") 
  if($flag=="0")
  {  
           fwrite($dlfile,"CHR\tPOS1\tPOS2\tR2\tD\tDPRIME\tRSID\tRSID_SYN\tMINOR\tMAF\tMAJOR\tCMMB\tCM\n");  
  } 
  else {
           fwrite($dlfile,"CHR\tPOS1\tPOS2\tR2\tD\tDPRIME\tRSID\tMINOR\tMAF\tMAJOR\tCMMB\tCM\n");
  }
  #fwrite($dlfile,"CHR\tPOS1\tPOS2\tR2\tD\tDPRIME\tRSID\tMINOR\tMAF\tMAJOR\tCMMB\tCM\n");

 
  if ( ( $chr  != "" ) && ( $min != "" ) ) 
  {
       $tabixresults   = getsenitalsnps  ( $Genomerelease, $Referenceset, $Population, $Annotation, $chr, $min, $max);  
       foreach ( $tabixresults as $line ) { fwrite($dlfile,$line."\n");}                   
  }
  fclose( $dlfile );
  //echo exec( 'rm /home/metabolomics/snipa/web/frontend/js/tmp/data0.json');
  $zoomlevel=0;
  $mousetracking=0;

  $file = fopen($JobDir."/matrixtest.txt","w");

  

  echo fwrite($file,'Rscript generatematrix_annotation.R ' . $flname . ' ' . $Population . ' ' . $zoomlevel . " 0 ".$mousetracking.' '.$JobDir."\n");
  #print('Rscript generatematrix_annotation.R ' . $flname . ' ' . $Population . ' ' . $zoomlevel . " 0 ".$mousetracking.' '.$JobDir);
  echo exec( 'sudo Rscript generatematrix_annotation.R ' . $flname . ' ' . $Population . ' ' . $zoomlevel . " 0 ".$mousetracking.' '.$JobDir); 

  $qtrflname = $JobDir . "/LDHeatMapZoom_" . "1" .".csv";
    
  if ( file_exists( $qtrflname ) ) {
        unlink( $qtrflname );
  }
  $qtrdlfile = fopen( $qtrflname, 'a' );
  fwrite($qtrdlfile,"CHR\tPOS1\tPOS2\tR2\tD\tDPRIME\tRSID\tMINOR\tMAF\tMAJOR\tCMMB\tCM\n");  
  if ( ( $chr  != "" ) && ( $min != "" ) ) 
  {
       $tabixresults   = getsenitalsnps  ( $Genomerelease, "genoq", "qtr", $Annotation, $chr, $min, $max);  
       foreach ( $tabixresults as $line ) { fwrite($qtrdlfile,$line."\n");}                   
  }

  echo fwrite($file,'Rscript generatematrix_annotation.R ' . $qtrflname . ' ' . $Population . ' ' . $zoomlevel . " 1 ".$mousetracking.' '.$JobDir."\n");
  print('Rscript generatematrix_annotation.R ' . $qtrflname . ' ' . $Population . ' ' . $zoomlevel . " 1 ".$mousetracking.' '.$JobDir);
  echo exec( 'sudo Rscript generatematrix_annotation.R ' . $qtrflname . ' ' . $Population . ' ' . $zoomlevel . " 1 ".$mousetracking.' '.$JobDir);
  fclose($file);
}
else
{
   echo exec( "echo '$#Hanuman007' |sudo rm ".$JobDir."/data.sample");
   $SNPsPos = snipaMapPos2($Genomerelease,$Referenceset,$Population,$chr,$min,$max,$JobDir."/LDHeatMapZoom_0.csv",0,$JobDir);
   $SNPsPos = snipaMapPos2($Genomerelease,"genoq","qtr",$chr,$min,$max,$JobDir."/LDHeatMapZoom_1.csv",1,$JobDir);
}
  $logfile = fopen($JobDir."/summary.log", "w") or die("Unable to open file!");
  fwrite($logfile, $Genomerelease."\t".$Referenceset."\t".$Population."\t".$Annotation."\t".$flag."\t".$JobId."\t".$min."\t".$max."\t".$chr."\n");
  fclose($logfile);
?>
	
