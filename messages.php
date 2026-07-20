<?php
   $starttime = microtime();
   $protectedpage = 1;
   include("common.php");

   if (!$userid)
   { 
     $sysmsg = "custom";
     $sysmsgcustomcontent = "This function requires a userid.";
   }
   else
   {
     $userinformation = fetchRow($userid, TABLE_USERS);

     $areaoptions[] = array("name" => "Send This User a Private Message",
                            "url"  => "private.php?action=send&recipient=".$userinformation['displayname']);
     $areaoptions[] = array("name" => "View This User's Profile",
                            "url"  => "user.php?user=".$userid);
     
     
     $navigationhead = "Message History for ".$userinformation['displayname'];
     
     $opts['searchtype']  = "public";
     $opts['authorid']    = $userid;
     $opts['statusnull']  = 1;
     $opts['pagvars']     = "userid=$userid";
     
     $posts = postSearch($opts, $page);
     
     $output    = $posts['msglist'];
     $centerrow = $posts['navbar'];
   }
   
   $pagecontents = $output;
   include("layout.php");
?>
