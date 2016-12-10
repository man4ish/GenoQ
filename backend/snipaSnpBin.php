<?php
session_start();
$task = $_POST['task'];

if (($task == "add") || ($task == "clear")) {
	$snpname = $_POST['snpname'];
	$snppos = $_POST['snppos'];
	$snpchr = $_POST['snpchr'];
	$genomerelease = $_POST['genomerelease'];
	$referenceset = $_POST['referenceset'];
	$population = $_POST['population'];
	$annotation = $_POST['annotation'];
	$randid = $_POST['randid'];

	if ($task == "add") { 
		$newsnp = TRUE;
		foreach ($_SESSION['snpbin'] as $row) {
			if ($snpname == $row['snpname']) { $newsnp = FALSE; print("snpexists"); }
		}
		if ($newsnp) {
			array_unshift($_SESSION['snpbin'], array("snpname" => $snpname, "snppos" => $snppos, "snpname" => $snpname, "snpchr" => $snpchr, "genomerelease" => $genomerelease, "referenceset" => $referenceset, "population" => $population, "annotation" => $annotation, "randid" => $randid)); 
		}
	}
	
	
	if ($task == "clear") { $_SESSION['snpbin'] = array(); }
}

if ($task == "remove") {
	$randid = $_POST['randid'];
	$_SESSION['snpbin'] = removeElementWithValue($_SESSION['snpbin'],"randid",$randid);
}

if ($task == "read") {
        //$snpname='';
	//$snpname = $_POST['snpname'];
	$result = array();
	foreach ($_SESSION['snpbin'] as $row) {
			array_push($result,array("value" => $row['snpname'], "label" => $row['snpname']." (Chr ".$row['snpchr'].", Pos ".$row['snppos'].")", "randid" => $row['randid']));
	}
	print(utf8_encode(json_encode($result)));
}


function removeElementWithValue($array, $key, $value){
     foreach($array as $subKey => $subArray){
          if($subArray[$key] == $value){
               unset($array[$subKey]);
          }
     }
     return $array;
}
?>
