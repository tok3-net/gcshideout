<?php
   $starttime = microtime();
   include("common.php");
   $navigation[] = array("name" => "Help",
                         "url"  => $PHP_SELF);

   $markupexample[] = array("[b]Bold Text[/b]");
   $markupexample[] = array("[i]Italic Text[/i]");
   $markupexample[] = array("[u]Underlined Text[/u]");
   $markupexample[] = array("[o]Overlined Text[/o]");
   $markupexample[] = array("[strike]Struck-Through Text[/strike]");
   $markupexample[] = array("[center]Centered Text[/center]");
   $markupexample[] = array("[blink]Blinking Text![/blink]");
   $markupexample[] = array("[bq]Blockquoted Text[/bq]");
   $markupexample[] = array("[spaces]Text   With   All    Spaces Shown Between Words[/spaces]");
   $markupexample[] = array("[quote]Quoted Text[/quote]");
   $markupexample[] = array("[spoiler]Spoiler Text[/spoiler]");
   $markupexample[] = array("[hr] (Horizontal Rule)");
   $markupexample[] = array("[ul]['li']Unordered List[li]Unordered List[/ul]");
   $markupexample[] = array("[ol]['li']Ordered List[li]Ordered List[/ul]");
   $markupexample[] = array("[color=red]Red Text[/color]");
   $markupexample[] = array("[hl=yellow]Yellow Highlight[/hl]");
   $markupexample[] = array("[ucol=Username]Usernames Colors[/ucol]");
   $markupexample[] = array("[left-border=lime]Lime Left Border[/left-border]");
   $markupexample[] = array("[right-border=navy]Navy Right Border[/right-border]");
   $markupexample[] = array("[top-border=brown]Brown Top Border[/top-border]");
   $markupexample[] = array("[bottom-border=orange]Orange Bottom Border[/bottom-border]");
   $markupexample[] = array("[border=green]Green Border[/border]");
   $markupexample[] = array("[dashedborder=blue]Blue Dashed Border[/dashedborder]");
   $markupexample[] = array("/me Does Something IRC-Style");
   $markupexample[] = array("[link=http://www.gcshideout.com/]GCs Hideout![/link]");
   $markupexample[] = array("[image=http://www.gcshideout.com/Images/GCs-Hideout.gif]");
   $markupexample[] = array("[fimage=http://www.gcshideout.com/Images/GCs-Hideout.gif]");
   $markupexample[] = array("[icon=http://www.gcshideout.com/Images/tediz.jpg]");
if (checkAccess("accessvip"))
{
$markupexample[] = array("[vip]VIP Only Text[/vip]");
}
   
   for ($i = 0; $i < count($markupexample); $i++ )
   {
     $markuprow .= "<TR VALIGN=BASELINE>\n"
                  ."    <TD STYLE='font-size: 8pt;'>".$markupexample[$i][0]."</TD>\n"
                  ."    <TD>=</TD>\n"
                  ."    <TD>".bodyText($markupexample[$i][0], "(Insert-Username-Here)")."</TD></TR>\n";
   }
   $markupexamples = "<TABLE CELLPADDING=4 CELLSPACING=1 BORDER=0>\n"
                    ."<TR><TD COLSPAN=2 CLASS='BoardRowHeading'>Markup</TD>\n"
                    ."    <TD CLASS='BoardRowHeading'>Output</TD></TR>\n"
                    .$markuprow
                    ."</TABLE>\n";
   
   // Show the smiley list as currently configured
   foreach ($configoptions['emoticons'] as $key => $value)
   {
     $facerow .= "<TR VALIGN=BASELINE>\n"
                  ."    <TD STYLE='font-size: 8pt;'>".$value['code']." or [face_".$value['name']."]</TD>\n"
                  ."    <TD>=</TD>\n"
                  ."    <TD>".bodyText("[face_".$value['name']."]")."</TD></TR>\n";
   }
   $faceexamples = "<TABLE CELLPADDING=4 CELLSPACING=1 BORDER=0>\n"
                  ."<TR><TD COLSPAN=2 CLASS='BoardRowHeading'>Code</TD>\n"
                  ."    <TD CLASS='BoardRowHeading'>Output</TD></TR>\n"
                  .$facerow
                  ."</TABLE>\n";

// ------------------------------
// Actions And Output
// ------------------------------

if ($action == "intro")
{
      $starttime = microtime();
     $output = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
			."<TR><TD CLASS='BoardRowBody'>\n"
            ."<P>\n"
            ."<B>GCs Hideout Board Introduction</B>\n"
            ."<P>\n"
            ."GC Boards is a modified version of DiscoBoards.  DiscoBoards is a board system "
			."which was made to replicate IGN's board system.  These boards have many markup codes and "
			."emoticons.  If you are not sure what those are you can check out those links to the left "
			."in the Help Menu.  Basically everything you need to know is in the Help Menu.  If there "
			."is something you are un-sure of you can always contact one of the higher ups on the boards "
			."(Administrators, Managers, Moderators, VIPs, etc...) and they should be able to help you, "
			."or you can post a topic on what you need help with in the board that it corresponds with."
			."</TD></TR></TABLE>";
}

if ($action == "markup")
{
   $starttime = microtime();   
     $output = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
			."<TR><TD CLASS='BoardRowBody'>\n"
            ."<P>\n"
            ."<B>Markup Codes</B>\n"
            ."<P>\n"
            ."GC Boards supports the following markup codes:\n"
            ."<P>\n"
            .$markupexamples
            ."<P>\n"
			."</TD></TR></TABLE>";
}

if ($action == "emoticon")
{
   $starttime = microtime();   
     $output = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
			."<TR><TD CLASS='BoardRowBody'>\n"
            ."<P>\n"
            ."<B>Emoticons</B>\n"
            ."<P>\n"
            ." With GC Boards you can use the following emoticons:\n"
            ."<P>\n"
            .$faceexamples
            ."<P>\n"
			."</TD></TR></TABLE>";

}

if ($action == "defboard")
{
   $starttime = microtime();   
     $output = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>"
			."<TR><TD CLASS='BoardRowBody'>"
            ."A board is a bunch of topics usually all related by a certain category.  Boards are usually "
			."grouped in Categories.  Boards are also sometimes referred to as Forums."
			."</TD></TR></TABLE>";
}

if ($action == "defcategory")
{
   $starttime = microtime();   
     $output = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>"
			."<TR><TD CLASS='BoardRowBody'>"
            ."A category has no posts it is just a menu bar above a bunch of boards that tells a user what "
			."type of topics would be in the boards below.  The Board names will also tell the user what kind "
			."of topics to expect in the Board"
			."</TD></TR></TABLE>";
}

if ($action == "defpoll")
{
   $starttime = microtime();   
     $output = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>"
			."<TR><TD CLASS='BoardRowBody'>"
            ."A poll can be made by a user in a Board, and it asks users who visit the poll a question of the "
			."poll author's choice.  The user will be able to choose from a list of options of how they would like "
			."to answer the question."
			."</TD></TR></TABLE>";
}

if ($action == "defprivatemessage")
{
   $starttime = microtime();   
     $output = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>"
			."<TR><TD CLASS='BoardRowBody'>"
            ."A private message or PM for short is like an email to another user.  Users on GCs Hideout Boards "
			."can use this to chat with other users.  You will be notified of new Private Messages up in the main "
			."link bar beside Private Message.  If you have say 2 Unread Private Messages istead of saying Private "
			."Message in the main link bar it will say Private Message - 2 new, as soon as you read the private "
			."message it will not be counted as new anymore."
			."</TD></TR></TABLE>";
}

if ($action == "defreply")
{
   $starttime = microtime();  
     $output = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>"
			."<TR><TD CLASS='BoardRowBody'>"
            ."A reply is just a message left by a user in a topic.  A reply will usually say the users views "
			."on the topic.  A reply is also known as a post"
			."</TD></TR></TABLE>";
}

if ($action == "defsignature")
{
    $starttime = microtime();  
     $output = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>"
			."<TR><TD CLASS='BoardRowBody'>"
            ."A users signature can be set in the Edit Your Profile option of the Options link.  Your signature "
			."gets displayed at the bottom of every Post or Reply you make."
			."</TD></TR></TABLE>";
}

if ($action == "deftopic")
{
      $starttime = microtime();
     $output = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>"
			."<TR><TD CLASS='BoardRowBody'>"
            ."A topic is usually based on one thing and is there for users to post their replies on that one "
			."thing.  Topics can be used as discussions on whatever the user feels like."
			."</TD></TR></TABLE>";
}
if ($action == "defuser")
{
   $starttime = microtime();   
     $output = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>"
			."<TR><TD CLASS='BoardRowBody'>"
            ."A user is anyone who registers on the site and posts messages."
			."</TD></TR></TABLE>";
}

if ($action == "login")
{
   $starttime = microtime();   
     $output = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>"
			."<TR><TD CLASS='BoardRowBody'>"
            ."In order to login you have to have registered.  If you haven't registered yet click on the Register "
			."link in the main menu.  If you have just registered it will send a random password to your email "
			."address.  If you want to change your password after check out the Changing Password help topic.  Then "
			."to login click on the Login link in the main menu bar."
			."</TD></TR></TABLE>";
}

if ($action == "stars")
{
    $starttime = microtime();  
     $output = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>"
			."<TR><TD CLASS='BoardRowBody'>"
	."<TABLE BORDER=1 WIDTH=100%>"
	."<TR>"
	."<TD CLASS='BoardRowBody'><B>Star System #</B></TD><TD CLASS='BoardRowBody'><B>Star 1</B></TD>"
	."<TD CLASS='BoardRowBody'><B>Star 2</B></TD><TD CLASS='BoardRowBody'><B>Star 3</B></TD>"
	."<TD CLASS='BoardRowBody'><B>Star 4</B></TD><TD CLASS='BoardRowBody'><B>Star 5</B></TD>"
	."<TD CLASS='BoardRowBody'><B>Star 6</B></TD><TD CLASS='BoardRowBody'><B>Star 7</B></TD>"
	."<TD CLASS='BoardRowBody'><B>Star 8</B></TD><TD CLASS='BoardRowBody'><B>Star 9</B></TD>"
	."<TD CLASS='BoardRowBody'><B>Star 10</B></TD></TR>"
	."<TR>"
	."<TD CLASS='BoardRowBody'>1</TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/star1.gif' alt='50 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/star2.gif' alt='250 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/star3.gif' alt='500 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/star4.gif' alt='1000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/star5.gif' alt='5000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/star6.gif' alt='10000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/star7.gif' alt='20000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/star8.gif' alt='30000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/star9.gif' alt='40000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/star10.gif' alt='50000 Posts'></TD></TR>"
	."<TR>"
	."<TD CLASS='BoardRowBody'>2</TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/IGN/star1.gif' alt='50 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/IGN/star2.gif' alt='250 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/IGN/star3.gif' alt='500 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/IGN/star4.gif' alt='1000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/IGN/star5.gif' alt='5000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/IGN/star6.gif' alt='10000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/IGN/star7.gif' alt='20000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/IGN/star8.gif' alt='30000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/IGN/star9.gif' alt='40000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/IGN/star10.gif' alt='50000 Posts'></TD></TR>"
	."<TR>"
	."<TD CLASS='BoardRowBody'>3</TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/Red/star1.gif' alt='50 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/Red/star2.gif' alt='250 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/Red/star3.gif' alt='500 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/Red/star4.gif' alt='1000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/Red/star5.gif' alt='5000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/Red/star6.gif' alt='10000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/Red/star7.gif' alt='20000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/Red/star8.gif' alt='30000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/Red/star9.gif' alt='40000 Posts'></TD>"
	."<TD CLASS='BoardRowBody'>"
            ."<img src='".$DBRoot."/gfx/stars/Red/star10.gif' alt='50000 Posts'>"
			."</TD></TR></TABLE>";
}

if ($action == "menu")
{
   $starttime = microtime();
       $ThemeName = "plain";
       $bodycolor = "#000D2F";
       $marginx = 3;
       $marginy = 3;
       $nowrap = 1;
$output = ""
                ."<SCRIPT LANGUAGE='JavaScript'>"
                ."function closeHelpFrame() { "
                ."parent.location = parent.frames['main'].location; "
                ."return false; "
                ."} \n"
                ."</SCRIPT>\n"
		  ."<table width=100% cellspacing=1 cellpadding=2 border=0>"
		  ."<tr valign='top'><td width=100%><center><span class='statistictext' valign='top'><B>Help Menu</B></span></center></td></tr>"
		  ."<tr><td width=100%><hr width='100%'></td></tr>"
		  ."<tr><td width=100%>&nbsp;<a href='help.php?action=intro' class='AdminMenuLink' target='main'>Introduction</a></td></tr><br>"
		  ."<tr><td width=100%>&nbsp;<a href='help.php?action=emoticon' class='AdminMenuLink' target='main'>Emoticons</a></td></tr>"
		  ."<tr><td width=100%>&nbsp;<a href='help.php?action=login' class='AdminMenuLink' target='main'>Logging In</a></td></tr>"
		  ."<tr><td width=100%>&nbsp;<a href='help.php?action=markup' class='AdminMenuLink' target='main'>Markup Codes</a></td></tr>"
		  ."<tr><td width=100%>&nbsp;<a href='help.php?action=stars' class='AdminMenuLink' target='main'>Stars</a></td></tr>"
		  ."<tr><td width=100%><span class='statistictext'><B>Definitions</B></span></td></tr>"
		  ."<tr><td width=100%>&nbsp;&nbsp;<a href='help.php?action=defboard' class='AdminMenuLink' target='main'>Board</a></td></tr>"
		  ."<tr><td width=100%>&nbsp;&nbsp;<a href='help.php?action=defcategory' class='AdminMenuLink' target='main'>Category</a></td></tr>"
		  ."<tr><td width=100%>&nbsp;&nbsp;<a href='help.php?action=defpoll' class='AdminMenuLink' target='main'>Poll</a></td></tr>"
		  ."<tr><td width=100%>&nbsp;&nbsp;<a href='help.php?action=defprivatemessage' class='AdminMenuLink' target='main'>Private Message</a></td></tr>"
		  ."<tr><td width=100%>&nbsp;&nbsp;<a href='help.php?action=defreply' class='AdminMenuLink' target='main'>Reply</a></td></tr>"
		  ."<tr><td width=100%>&nbsp;&nbsp;<a href='help.php?action=defsignature' class='AdminMenuLink' target='main'>Signature</a></td></tr>"
		  ."<tr><td width=100%>&nbsp;&nbsp;<a href='help.php?action=deftopic' class='AdminMenuLink' target='main'>Topic</a></td></tr>"
		  ."<tr><td width=100%>&nbsp;&nbsp;<a href='help.php?action=defuser' class='AdminMenuLink' target='main'>User</a></td></tr>"
		  ."<tr><td width=100%>&nbsp;</td></tr>"
		  ."<tr><td width=100%>&nbsp;<A HREF=".$DBRoot." CLASS='statistictext' onClick='return closeHelpFrame();'>[close frame]</A></td></tr>"
		  ."</table>";

}

if (!$action)
{
       $menusrc = $PHP_SELF."?action=menu";
       $mainsrc = $PHP_SELF."?action=intro";
       include("elements/frameset.php");
       $output = $frameset;
       $ThemeName = "frameset";
       $nowrap = 1;
}


   $pagecontents = $output;
   include("layout.php");
?>