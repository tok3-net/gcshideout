<?php
   if (!$globaldebugmode)
   {
     setCookie("globaldebugmode", "on", 0, "/");
     $out =  "Debug mode ON";
   }
   else
   {
     setCookie("globaldebugmode", "", 0, "/");
     $out = "Debug mode OFF";
   }
   
   //Header("Content-type: text/plain");
   echo $out;
?>