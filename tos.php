<?php
   $starttime = microtime();
   include("common.php");
   
   $navigationhead = "Terms of Service";
   
   $tosfile = "elements/tos.txt";
   $handle = fopen($tosfile, "rb");
   $termsofservice = fread($handle, filesize($tosfile));
   fclose($handle);
   
   $output = "<table width=\"100%\" cellpadding=\"20\" cellspacing=\"1\" border=\"0\">\n"
            ."  <tr>\n"
            ."    <td class=\"BoardRowBody\">\n"
            .$termsofservice
            ."    </td>\n"
            ."  </tr>\n"
            ."</table>\n";
   
   $pagecontents = $output;
   include("layout.php");
?>