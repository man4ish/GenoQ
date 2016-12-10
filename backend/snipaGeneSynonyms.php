<?php

if (!isset($_REQUEST['term'])) { exit; } else 

{

require("../backend/snipaConfig.php");
  
  $dbcon = @mysql_connect($db_server,$db_user,$db_pass) or die (mysql_error());
  mysql_select_db('snipa');
  $genomerelease= mysql_real_escape_string($_REQUEST['genomerelease']);
  $annotation= mysql_real_escape_string($_REQUEST['annotation']);
  $sql_pos = "SELECT SYN.KEY,SYN.NAME,GENES.START,GENES.STOP,GENES.CHR,GENES.SIZE FROM (SELECT * FROM `annotation-".$genomerelease."-".$annotation."-synonyms` 
			  WHERE NAME LIKE '%".mysql_real_escape_string(trim($_REQUEST['term']))."%' LIMIT 0,10) as SYN
			  JOIN `annotation-".$genomerelease."-".$annotation."-genes` as GENES ON SYN.KEY = GENES.ID ORDER BY SYN.NAME ASC"; 
  $result = mysql_query($sql_pos,$dbcon);
  
  $data = array();
	if ($result && mysql_num_rows($result))
	{
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'label' => $row['KEY'] .', '. $row['NAME'] . ' (chr'.$row['CHR'].':'.$row['START'].'-'.$row['STOP'].', '.$row['SIZE'].'bp)' ,
				'value' => $row['KEY']
			);
		}
	}
	 
  mysql_close($dbcon);
  if ($_REQUEST['format'] == "json") { print(utf8_encode(json_encode($data))); }
  if ($_REQUEST['format'] == "hits") { print(count($data)); }
  flush();

}
?>