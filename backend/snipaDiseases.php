<?php
require("../backend/snipaConfig.php");

if (!isset($_REQUEST['term'])) { 
  $data = file($path_to_gwascatalog."/traitlist.txt", FILE_IGNORE_NEW_LINES);
  print(utf8_encode(json_encode($data)));
  } else {
  $data = file($path_to_gwascatalog."/traitlist.txt", FILE_IGNORE_NEW_LINES);
  $data = preg_grep('/'.$_REQUEST['term'].'/i', $data);
  print(utf8_encode(json_encode($data)));
}
?>
