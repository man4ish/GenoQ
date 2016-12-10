<?php
require("../backend/snipaConfig.php");
require("../backend/snipaMaprsid.php");
require("../backend/snipaTabix.php");

$chrom_map = array(
	'1' => 1,
	'2' => 3,
	'3' => 5,
	'4' => 7,
	'5' => 9,
	'6' => 11,
	'7' => '13',
	'8' => '15',
	'9' => '17',
	'10' => '19',
	'11' => '21',
	'12' => '23',
	'13' => '25',
	'14' => '27',
	'15' => '29',
	'16' => '31',
	'17' => '33',
	'18' => '35',
	'19' => '37',
	'20' => '39',
	'21' => '41',
	'22' => '43',
	'X' => '45'
	);


$Genomerelease = $_POST['genomerelease'];
$Referenceset = $_POST['referenceset'];
$Population = $_POST['population'];
$Annotation = $_POST['annotation'];
$SnpsSentinels = $_POST['snps'];
$Trait = $_POST['trait'];

// speichere SNPs im Array, lösche nicht-"rs" Zeilen
$SnpsSentinels = trim($SnpsSentinels);
$SnpsSentinelsArray = preg_split('/\r\n|[\r\n]/',$SnpsSentinels);
foreach ($SnpsSentinelsArray as &$entry) {
	$entry = trim($entry);
	if (!(preg_match("/^rs[0-9]+( [\d\.\-eE]+)?/",$entry))) { $entry = ""; }
}
unset($entry);
$SnpsSentinelsArray = array_filter($SnpsSentinelsArray,'strlen');

if(count($SnpsSentinelsArray)>0){
	$result = array();
	foreach($SnpsSentinelsArray as $key => $value) {
		$info = explode(" ", $value);
		$pos = snipaMapRsid($Genomerelease,$Referenceset,$Population,$info[0]);
		$pval = sprintf("%.0E", $info[1]);
		if(isset($pos['POS'])){
			$selfinfo = snipaGetSelfinfo($Genomerelease,$Referenceset,$Population,$pos['CHR'],$pos['POS'],$pos['POS'],$info[0]);
			$result[] = array( "x" => $pos['POS'], "y" => $chrom_map[$pos['CHR']], "chr" => $pos['CHR'], "rsid" => $selfinfo[0]['RSID'], "rsalias" => implode(", ",explode(",",str_replace("NA","",$selfinfo[0]['RSALIAS']))), "trait" => $Trait, "pval" => preg_replace('/([\d\.]+)E\-(\d+)/','\1&times;10<sup>-\2</sup>', $pval) );
		}
	}
	print(json_encode(array_map(function($t) { return is_string($t) ? utf8_encode($t) : $t; }, array_values($result)), JSON_NUMERIC_CHECK));
}else{
	exit(1);
}
?>
