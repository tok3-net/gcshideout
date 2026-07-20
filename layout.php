<?php
   //if (ereg("^/admin", $PHP_SELF))
   //{
   //  $ThemeName = "admin";
   //}

   // Create the menu bar
   if ($userloggedin)
   {
     // Do this to save usernameDisplay() from doing a database query
     $userformatarray = array("postcount" => $userdata['postcount'],
                              "displayname" => $userdata['displayname'],
                              "displayformat" => $userdata['displayformat']);
     $menurowdata[] = "Logged in as: ".usernameDisplay($userdata['ID'], "MainMenuLinkLight", "", $userformatarray)." | \n"
                     .makeLink("login.php?action=logout&returnurl=".urlencode($PHP_SELF.$querystring), "Log Out", "MainMenuLink");

     $menurowdata[] = makeLink("private.php", "Private Messages", "MainMenuLink").countUnreadPrivateMessages($userdata['ID']);
   }
   else
   {
     $menurowdata[] = makeLink("login.php?returnurl=".urlencode($PHP_SELF.$querystring), "Login", "MainMenuLink");
     $menurowdata[] = makeLink("register.php?returnurl=".urlencode($PHP_SELF.$querystring), "Register", "MainMenuLink");
   }
   $menurowdata[] = makeLink("options.php", "Options", "MainMenuLink");
     // Only users with admin access get this...
     if (checkAccess("accessvip"))
     {
       $menurowdata[] = makeLink("admin.php?pageurl=".urlencode($PHP_SELF.$querystring), "Admin", "MainMenuLink", array("target" => "_top"));
     }
   $menurowdata[] = makeLink("help.php", "Help", "MainMenuLink");
global $AllowShop;
if ($AllowShop)
{
   $menurowdata[] = makeLink("shop.php", "Shop", "MainMenuLink");
}
   $menurowdata[] = makeLink("staff.php", "Staff", "MainMenuLink");
   $menurowdata[] = makeLink("tos.php", "TOS", "MainMenuLink");

   
   // Add user-defined menu bar options
   if (is_array($MenuBarOptions))
   {
     foreach ($MenuBarOptions as $key => $value)
     {
       $menurowname = $value['name'];
       if ($value['colour'])
       {
         $menurowname = "<SPAN STYLE='color: ".$value['colour'].";'>".$value['name']."</SPAN>";
       }
       $menurowdata[] = makeLink($value['url'], $menurowname, "MainMenuLink");
     }
   }
   
   $menurow = "<TABLE WIDTH=100% CELLPADDING=4 CELLSPACING=1 BORDER=0>\n"
             ."<TR><TD CLASS='MainMenuRow'><B>".implode(" | \n", $menurowdata)."</B></TD></TR>\n"
             ."</TABLE>\n";
   
   // Now create the centerrow if we have anything to put in it...
   if ($centerrow)
   {
     $centerrow = "<TABLE WIDTH=100% CELLPADDING=4 CELLSPACING=1 BORDER=0>\n"
                 ."<TR><TD ALIGN=CENTER CLASS='MainMenuRow'><B>".$centerrow."</B></TD></TR>\n"
                 ."</TABLE>\n";
   }

   
   // Specific area-related options now - this is the stuff that changes from page to page, as
   // it directly relates to the part of the board you're looking at
   if ($areaoptions)
   {
     for($i = 0; $i < count($areaoptions); $i++ )
     {
       $extra = "";
       if ($areaoptions[$i]['onclick'])
       {
         $extra['onclick'] = $areaoptions[$i]['onclick'];
       }
       $areaoptionsrowdata[] = makeLink($areaoptions[$i]['url'], $areaoptions[$i]['name'], "MainMenuLink", $extra);
     }
     $areaoptionsrow = "<TABLE WIDTH=100% CELLPADDING=4 CELLSPACING=1 BORDER=0>\n"
                      ."<TR><TD CLASS='MainMenuRow'><B>".implode(" | \n", $areaoptionsrowdata)."</B></TD></TR>\n"
                      ."</TABLE>\n";
   }

   // Navigation heirarchy now
   if (($navigation) || ($navigationhead))
   {
     // $navigation is defined elsewhere, and added to page-by-page. Here we loop 
     // through it and create the direct HTML parts as array elements.
     for($i = 0; $i < count($navigation); $i++ )
     {
       // Display with or without a link - depends if they have a [url] element or not
       if ($navigation[$i]['url'])
       {
         $navigationrowdata[] = makeLink($navigation[$i]['url'], $navigation[$i]['name'], "MainMenuLink");
       }
       else
       {
         $navigationrowdata[] = $navigation[$i]['name'];
       }
     }
     
     // Now we go through each element of that array and output it accordingly ... if its the first
     // option then we append a space after it (thats the board's id as in [IGN.com]) and thereafter
     // we append a &raquo;
     for ($i = 0; $i < count($navigationrowdata); $i++ )
     {
       if (!$i)
       {
         // Output the first element and then put a space after it
         $navigationrowoutput .= $navigationrowdata[$i]."&nbsp;";
       }
       else
       {
         // Next element output
         $navigationrowoutput .= $navigationrowdata[$i]."\n";
         // Append the raquo unless this is the last element
         if ($i < (count($navigationrowdata)-1))
         {
           $navigationrowoutput .= " &raquo; ";
         }
       }
     }
     // And the final, non-linked element which tells the user what they're doing NOW...
     if ($navigationhead) 
     {
       $navigationrowoutput .= " &raquo; ".$navigationhead;
     }
     // Put it all together
     $navigationrow = "<TABLE WIDTH=100% CELLPADDING=4 CELLSPACING=1 BORDER=0>\n"
                     ."<TR><TD CLASS='MainMenuRow'>".$navigationrowoutput."</TD></TR>\n"
                     ."</TABLE>\n";

     // Decide the page's name in the title bar
     if ($navigationhead)
     {
       $pageareatitle = $navigationhead; 
     }
     else
     {
       $navrowcount = count($navigationrowdata);
       $pageareatitle = $navigationrowdata[($navrowcount-1)];
     }
     $pageareatitle = strip_tags($pageareatitle);
   }
 
   // Generate system messages from inbound sysmsg codes
   switch ($sysmsg)
   {
     case "watchedusersupdated":
       $systemmessagebody = "Your watched user list has been updated.";
       break;
     case "watchedthreadsupdated":
       $systemmessagebody = "Your watched thread list has been updated.";
       break;
     case "nowwatchingthread":
       $systemmessagebody = "This thread is now being watched.";
       break;
     case "threadnotsticky":
       $systemmessagebody = "This thread is no longer sticky.";
       break;
     case "threadsticky":
       $systemmessagebody = "This thread is now sticky.";
       break;
     case "threadunlocked":
       $systemmessagebody = "This thread is no longer locked.";
       break;
     case "threadlocked":
       $systemmessagebody = "This thread is now locked.";
       break;
     case "voterecorded":
       $systemmessagebody = "Your vote has been recorded.";
       break;
     case "alreadyvoted":
       $systemmessagebody = "You have already voted.";
       break;
     case "noboard":
       $systemmessagebody = "No board with the specified ID exists.";
       break;
     case "pollcreated":
       $systemmessagebody = "Your poll has been created.";
       break;
     case "faveboardupdated":
       $systemmessagebody = "Your favourite boards list has been updated.";
       break;
     case "error":
       $systemmessagebody = "An error occurred while carrying out your request.";
       break;
     case "custom":
       $systemmessagebody = $sysmsgcustomcontent;
       break;
   }
   if ($systemmessagebody)
   {
     $systemmessage = "<B STYLE='color: yellow;'>".$systemmessagebody."</B>";
   }
   
   // Custom-coloured system message...
   $systemmessage .= $systemmessageextra;
   
   // If $systemmessage exists, output it
   if ($systemmessage)
   {
     $messagerow = "<TABLE WIDTH=100% CELLPADDING=4 CELLSPACING=1 BORDER=0>\n"
                  ."<TR><TD CLASS='MainMenuRow'>".$systemmessage."</TD></TR>\n"
                  ."</TABLE>\n";
   }
   
   // If $bottommessage exists, output it
   if ($bottommessage)
   {
     $bottommessagerow = "<TABLE WIDTH=100% CELLPADDING=4 CELLSPACING=1 BORDER=0>\n"
                        ."<TR><TD CLASS='MainMenuRow'>".$bottommessage."</TD></TR>\n"
                        ."</TABLE>\n";
   }
   
   // Now include the theme itself
   $themefile = "theme.".$ThemeName.".php";
   //echo "DocRoot is $DocRoot<BR>\n";
   //echo "ThemeFile is $themefile<BR>\n";
   include($themefile);
   
   echo $pagetop
       .str_replace("        #%#", "", $pagecontents) // Linewrap fix for <TEXTAREA>
       .$pageend
       ."<!-- GC Boards ".$DBVersion." running on ".$HTTP_HOST.", copyright 2004 GCs Hideout -->\n";
?>
