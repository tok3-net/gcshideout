<?php
   // This is the default DiscoBoard theme. It's modelled loosely on the
   // look and feel of IGNBoards http://boards.ign.com.
   
   $bodycolor      = "black";
   $bodybackground = "";
   $pagetitle      = "GC Boards";
   if ($pageareatitle)
   {
     $pagetitle = $pagetitle." | ".$pageareatitle;
   }

   if ($bodybackground)
   {
     $bodybackgroundtag = " BACKGROUND='$bodybackground'";
   }
   
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
	."<meta name='keywords' content='game_cheater99, gc, game cheater99, game cheater, discoboard, disco board, disco, gcs hideout, gamecheater, discoboard mods, discoboards modifications, discoboard hacks'>"
	."<meta name='description' content='GC Boards | A Very Modded Version Of DiscoBoard available for download at http://www.gcshideout.com or you can visit the boards at http://www.gcshideout.com/boards'>"
             ." <LINK REL='STYLESHEET' HREF='".$DBRoot."/stylesheets/".$css.".php' TYPE='text/css'>\n"
             .$javascriptlink
             ."<SCRIPT LANGUAGE='javascript'>"
             ."function quickreply() {"
             ."if (document.getElementById('quickreply').style.display=='none') {"
             ."document.getElementById('quickreply').style.display='block';"
             ."} else {"
             ."document.getElementById('quickreply').style.display='none';"
             ."}"
             ."}"
             ."</SCRIPT>"
             ."</HEAD>\n"
             ."<BODY MARGINWIDTH=0 MARGINHEIGHT=0 LEFTMARGIN=0 TOPMARGIN=0 \n"
             ."      BGCOLOR='$bodycolor' ".$bodybackgroundtag." \n"
             ."      TEXT='black' LINK='blue' VLINK='blue' ALINK='red'>\n";
	if ($functionset == "admin")
	{
$pagetop .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[header disabled for admin pages]";
	}
	else
	{
$pagetop .= "<CENTER><IMG SRC='http://www.gcshideout.com/boards/gfx/GCs-Hideout.gif' BORDER=0 WIDTH=100% ALT='GCs Hideout!'></CENTER>";
	}
$pagetop .= "<BR>\n"
             ."<TABLE WIDTH=100% ALIGN=CENTER CELLPADDING=4 CELLSPACING=0 BORDER=0>\n"
             ."<TR><TD WIDTH=10><IMG SRC='$GFXRoot/blank.gif' WIDTH=10 HEIGHT=10 ALT=''></TD>\n"
             ."<TD WIDTH=100% CLASS='MainTable'>\n"
             .""
             .$pageformstart
             .$navigationrow
             .$menurow
             .$areaoptionsrow
             .$messagerow
             .$centerrow
             ."";
   $endtime = microtime();          
   $pageend = $centerrow
             .$bottommessagerow
             .$navigationrow
             ."<TABLE WIDTH=100% CELLPADDING=0 CELLSPACING=0 BORDER=1><TR>\n"
             ."<TD CLASS='BoardColumn' WIDTH=33%>Current Time: ".dateNeat(Date("U"))."</TD>\n"
             ."<TD ALIGN=RIGHT CLASS='BoardColumn' WIDTH=33%><CENTER>Page Generation Time: ".round(($endtime - $starttime), 6)."</CENTER></TD>\n"
             ."<TD ALIGN=RIGHT CLASS='BoardColumn' WIDTH=33%>GC Boards v".$DBVersion."</TD>\n"
             ."</TR></TABLE></TD>\n"
             ."<TD WIDTH=10><IMG SRC='$GFXRoot/blank.gif' WIDTH=10 HEIGHT=10 ALT=''></TD>\n"
             ."</TR>\n"
             ."</TABLE><BR>\n"
             .$pageformstop
             ."</BODY>\n"
             ."</HTML>\n";
?>
