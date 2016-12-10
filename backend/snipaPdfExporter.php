<?php

require_once("../backend/snipaMaprsid.php");
require_once("../backend/snipaMapGenes.php");
require_once("../backend/snipaTabix.php");
require_once("../backend/snipaConfig.php");

require_once("/home/metabolomics/snipa/tools/dompdf/dompdf/dompdf_config.inc.php");

$Genomerelease = $_GET['genomerelease'];
$Referenceset = $_GET['referenceset'];
$Population = $_GET['population'];
$Annotation = $_GET['annotation'];
$Snp = $_GET['snp'];

// Hole Positionen für alle sentinel SNPs
$tmp = snipaMapRsid($Genomerelease,$Referenceset,$Population,$Snp);
$val = array('RSID' => $Snp, 'CHR' => $tmp['CHR'], 'POS' => $tmp['POS']);
unset($tmp);
$url = "http://localhost/snipa/backend/snipaRAPlotsAnnotations?snpname=".$val['RSID']."&snpchr=".$val['CHR']."&snppos=".$val['POS']."&sentinelpos=".$val['POS']."&genomerelease=".$Genomerelease."&referenceset=".$Referenceset."&population=".$Population."&annotation=".$Annotation."";
$printContent = "<html><head><title>SNiPA SNPcard - ".$val['RSID']."</title>";
$printContent .= "<link rel='stylesheet' href='http://localhost/snipa/frontend/css/print.css' type='text/css' media='screen, print' />";
$printContent .= "</head><body><h1><span class='head'><b><i>SN<span style='color: rgb(228,0,58);'>i</span>PA</i>card</b> &ndash; ";
$printContent .= $val['RSID'];
$printContent .= "</span></h1>";
$printContent .= "<div style='page-break-after: always;'>".str_replace('src=\'frontend/img', "src='../frontend/img", file_get_contents($url))."</div>";
$printContent .= "</body></html>";

$dompdf = new DOMPDF();
$dompdf->load_html($printContent);
$dompdf->render();
$dompdf->stream("".$val['RSID']."_".$Population.".pdf");

?>
