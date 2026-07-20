<?php
   $starttime = microtime();
   include("common.php");
   
   $navigationhead = "No Access";
   
 $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>"
            ."<TR><TD CLASS='BoardRowBody'>"
	."<B>You don't have access to this page.</B>"
            ."</TD></TR>"
            ."</TABLE>";
   
   $pagecontents = $output;
   include("layout.php");
?>