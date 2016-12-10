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


if (isset($_REQUEST['term'])) { 
	$term = urldecode($_REQUEST['term']);
	$result = array();
	$data = file($path_to_gwascatalog."/gwascatalog.out", FILE_IGNORE_NEW_LINES);
	$data = preg_grep('/\t'. preg_quote($term, "/").'\t/i', $data);
	foreach($data as $key => $value) {
		$info = explode("\t", $value);
		$pos = snipaMapRsid($_REQUEST['genomerelease'],$_REQUEST['referenceset'],$_REQUEST['population'],$info[0]);
		if(isset($pos['POS'])){
			$selfinfo = snipaGetSelfinfo($_REQUEST['genomerelease'],$_REQUEST['referenceset'],$_REQUEST['population'],$pos['CHR'],$pos['POS'],$pos['POS'],$info[0]);
			$result[] = array( "x" => $pos['POS'], "y" => $chrom_map[$pos['CHR']], "chr" => $pos['CHR'], "rsid" => $selfinfo[0]['RSID'], "rsalias" => implode(", ",explode(",",str_replace("NA","",$selfinfo[0]['RSALIAS']))), "trait" => $info[1], "pval" => preg_replace('/([\d\.]+)E\-(\d+)/','\1&times;10<sup>-\2</sup>',strtoupper($info[2])) );
		}
	}
	print(json_encode(array_map(function($t) { return is_string($t) ? utf8_encode($t) : $t; }, array_values($result)), JSON_NUMERIC_CHECK));
} else { exit; }
?>
