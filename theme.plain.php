<?php
   // This is the default DiscoBoard theme. It's modelled loosely on the
   // look and feel of IGNBoards http://boards.ign.com.
   
   if (!$bodycolor)     { $bodycolor = "black"; }
   if ($bodybackground) { $bodybackgroundtag = " BACKGROUND='".$bodybackground."'"; }
   if (!$pagetitle)     { $pagetitle = "DiscoBoards"; }
   if ($pageareatitle)
   {
     $pagetitle = $pagetitle." | ".$pageareatitle;
   }
   
   if (!$marginx) { $marginx = 0; }
   if (!$marginy) { $marginy = 0; }
   
   // Final definition of page layout conditions...
if((!$userdata['stylesheet']) || ($userdata['stylesheet'] == "NULL"))
{
   $css = "default";
}
else
{
   $css = $userdata['stylesheet'];
}
   $pagetop = "<HTML>\n"
             ."<HEAD>\n"
             ." <TITLE> $pagetitle </TITLE>\n"
             ." <LINK REL='STYLESHEET' HREF='".$DBRoot."/stylesheets/".$css.".php' TYPE='text/css'>\n"
             ."</HEAD>\n"
             ."<BODY MARGINWIDTH=".$marginx." MARGINHEIGHT=".$marginy." LEFTMARGIN=".$marginx." TOPMARGIN=".$marginy." \n"
             ."      BGCOLOR='$bodycolor' ".$bodybackgroundtag." \n"
             ."      TEXT='black' LINK='blue' VLINK='blue' ALINK='red'>\n"
             ."\n";
             
   $pageend = "</BODY>\n"
             ."</HTML>\n";
?>
