<?php
   $starttime = microtime();
   $protectedpage = 1;
   include("common.php");

   $navigation[] = array("name" => "Users currently online",
                         "url"  => "");
                         
   $online = fetchLoggedinUsers();
   $output = $online['html'];

   $pagecontents = $output;
   include("layout.php");
?>