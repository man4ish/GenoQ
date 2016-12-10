<?php
require_once("frontendHTMLHeader.php");
require_once("frontendHTMLFooter.php");
require_once("frontendPageHeader.php");
require_once("frontendPageNavigation.php");
require_once("frontendPageContent.php");
require_once("frontendPageFooter.php");


$nav_managejob = Array(
                   "jobnew" => Array(
                                       "task" => "new",
									   "text" => "Start a new job",
                                       "subpages" => Array()
                                       ),
                   "jobmodify" => Array(
                                    "task" => "modify",
									"text" => "Modify a previous job",
                                    "subpages" => Array(
                                                      "test1" => Array(
																	"task" => "test1id",
																	"text" => "Test 1"
													                  ),
													  "test2" => Array(
																	"task" => "test2id",
																	"text" => "Test 2"
													                  )
                                                     )
                                    ),
                   "jobcheckinput" => Array(
                                       "task" => "checkinput",
									   "text" => "Check your uploaded data",
                                       "subpages" => Array()
                                       )
                   );







frontendHTMLHeader("metaP server");

frontendPageHeader("img/logo_header_metap.png");

frontendPageNavigationStart();
frontendPageNavigationBox($nav_managejob,"jobid12345","Manage your jobs",$_GET['task']);
frontendPageNavigationEnd();

frontendPageContentStart();
frontendPageContentEnd();

frontendPageFooter("today");

frontendHTMLFooter();

?>