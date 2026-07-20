<?php
   // This is the default DiscoBoard theme. It's modelled loosely on the
   // look and feel of IGNBoards http://boards.ign.com.
   
   if (!$pagetitle)     { $pagetitle = "GC Boards"; }
   if ($pageareatitle)
   {
     $pagetitle = $pagetitle." | ".$pageareatitle;
   }
   
   // Final definition of page layout conditions...
   $pagetop = "<HTML>\n"
             ."<HEAD>\n"
             ." <TITLE> $pagetitle </TITLE>\n"
             ."</HEAD>\n"
             ."\n";
             
   $pageend = "</BODY>\n"
             ."</HTML>\n";
?>
