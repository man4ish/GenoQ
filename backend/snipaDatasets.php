<?php
session_start();
include("../snipa/datasets.php");


$action = $_REQUEST['action'];

if ($action == "set") {
	$_SESSION['datasetsdefault'] = array("genomerelease" => $_REQUEST['genomerelease'], "referenceset" => $_REQUEST['referenceset'], "population" => $_REQUEST['population'], "annotation" => $_REQUEST['annotation']);
}

if ($action == "get") {

	if (!isset($_SESSION['datasetsdefault']) || (count($_SESSION['datasetsdefault']) == 0)) {
		$_SESSION['datasetsdefault'] = $snipaDatasetsDefault;
	}

	$selects = array("genomerelease" => array(), 
			 "referenceset" => array(), 
			 "population" => array(), 
			 "annotation" => array());
			 
	$idtoname = array();


	foreach ($snipaDatasets['genomerelease'] as $genrels => $genrel) {
		$selects["genomerelease"][] = $genrel['id'];
		$idtoname[$genrel['id']] = $genrel['name'];
		foreach ($genrel['referenceset'] as $refsets => $refset) {
			$selects['referenceset'][$refset['id']][] = $genrel['id'];
			$idtoname[$refset['id']] = $refset['name'];
			foreach ($refset['population'] as $pops => $pop) {
				$selects['population'][$pop['id']][] = $refset['id'];
				$idtoname[$pop['id']] = $pop['name'];
			}
		}
		
		foreach ($genrel['annotation'] as $annos => $anno) {
			$selects['annotation'][$anno['id']][] = $genrel['id'];
			$idtoname[$anno['id']] = $anno['name'];
		}
	}

	if ($_REQUEST['type'] == "genomerelease") {
		// Select-Felder für Genome-Release
		//print('<select id="dataset-genomerelease">');
		print('<option value="">--</option>');
			foreach (array_unique($selects['genomerelease']) as $val) {
				$tmpselect = " ";
				if ($val == $_SESSION['datasetsdefault']['genomerelease']) { $tmpselect = ' selected="selected"'; }
				print('<option value="'.$val.'"'.$tmpselect.'>'.$idtoname[$val].'</option>');
			}
		//print('</select>');
	}

	if ($_REQUEST['type'] == "referenceset") {
		# Select-Felder für Referenzset
		//print('<select id="dataset-referenceset">');
		print('<option value="">--</option>');
		foreach ($selects['referenceset'] as $key => $val) {
			$tmpselect = " ";
			if ($key == $_SESSION['datasetsdefault']['referenceset']) { $tmpselect = ' selected="selected"'; }
			print('<option class="'.implode(" ",array_unique($val)).'" value="'.$key.'"'.$tmpselect.'>'.$idtoname[$key].'</option>');
		}
		//print('</select>');
	}

	if ($_REQUEST['type'] == "population") {
		# Select-Felder für Population
		//print('<select id="dataset-population">');
		print('<option value="">--</option>');
		foreach ($selects['population'] as $key => $val) {
			$tmpselect = " ";
			if ($key == $_SESSION['datasetsdefault']['population']) { $tmpselect = ' selected="selected"'; }
			print('<option class="'.implode(" ",array_unique($val)).'" value="'.$key.'"'.$tmpselect.'>'.$idtoname[$key].'</option>');
		}
		//print('</select>');
	}
	
	if ($_REQUEST['type'] == "annotation") {	
		# Select-Felder für Annotation
		//print('<select id="dataset-annotation">');
		print('<option value="">--</option>');
		foreach ($selects['annotation'] as $key => $val) {
			$tmpselect = " ";
			if ($key == $_SESSION['datasetsdefault']['annotation']) { $tmpselect = ' selected="selected"'; }
			print('<option class="'.implode(" ",array_unique($val)).'" value="'.$key.'"'.$tmpselect.'>'.$idtoname[$key].'</option>');
		}
		//print('</select>');
	}
}
?>