<?php
// frontendPageNavigationBox()
//
// Erstellt durch rekursive Fuktion frontendPageNavigationBoxAus() aus einem mehrdimensionalen Array (Beispiel unten) eine hirarchische Navigation (2 Ebenen). 
//
// $nav_struc = Array(
//                   "jobnew" => Array(
//                                       "position" => "1",
//                                       "task" => "new",
//									     "text" => "Start a new job",
//                                       "subpages" => Array()
//                                       ),
//                   "jobmodify" => Array(
//                                    "position" => "2",
//                                    "task" => "modify",
//									"text" => "Modify a previous job",
//                                    "subpages" => Array(
//                                                      "test1" => Array(
//																	"position" => "1",
//																	"task" => "test1id",
//																	"text" => "Test 1"
//													                  ),
//													  "test2" => Array(
//																	"position" => "2",
//																	"task" => "test2id",
//																	"text" => "Test 2"
//													                  )
//                                                     )
//                                    )
//                      )



function frontendPageNavigationBox($menuArray = Array(), $heading = "", $active = "") {
?>
<div class="box">
	<div class="header"><?php echo($heading); ?></div>
	<div class="content">
		<?php echo(frontendPageNavigationBoxAux($menuArray, $active)); ?>
	</div>
</div>
<?php
}  
?>


<?php
function frontendPageNavigationBoxAux($menuArray, $active = "", $level = 0) {
  global $contentboxtext;
  $out = '<ul class="level'.$level.'">';
    foreach ($menuArray AS $index => $menuItem) {
		
		$task = $menuItem["task"];
		$taskurl = 'index.php?task='.$task;
		$text = $menuItem["text"];
		$highlight = ($active == $task);
		if ($highlight) { $contentboxtext = $menuItem["text"]; } 
		$li = '<li class="level'.$level;
		if ($highlight) { $li .= ' active'; }
		$li .= '">';
		if ($highlight) { $li .= '<a class="active" href="'.$taskurl.'">'.$text.'</a>'; }
		else { $li .= '<a href="'.$taskurl.'">'.$text.'</a>'; }
		$li .= '</li>';
		$out .= $li;
		
		if (array_key_exists('subpages', $menuItem) && count($menuItem['subpages']) > 0) {
		  $out .= "<li>";
		  $out .= frontendPageNavigationBoxAux($menuItem['subpages'],$active,$level+1);
		  $out .= "</li>";
		}
    }
  $out .= '</ul>';
  return $out;
}
?>



<?php
	function frontendPageNavigationStart() { ?>
		<div id="container-navigation">
			<div class="box">
				<div class="header"><a href="index.php">Home</a></div>
			</div>
<?php	}
?>

<?php
	function frontendPageNavigationEnd() { ?>
		</div>
<?php	}
?>
