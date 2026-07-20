<?php
   $starttime = microtime();
   include("common.php");
   
   // Moved these up the top so we can work out what page is the final one
   $threaddata = fetchRow($threadid, TABLE_THREADS);
   $boarddata  = fetchRow($threaddata['boardid'], TABLE_BOARDS);
   $groupdata  = fetchRow($boarddata['groupid'], TABLE_GROUPS);

   $sql = "SELECT private, vippost FROM " . TABLE_BOARDS . " WHERE ID = '" . $threaddata['boardid'] . "'";
   $exe = mysqli_query($mysql, $sql);
   $row = mysqli_fetch_array($exe);
   $privateboard = $row['private'];
   $vippost = $row['vippost'];
if ((!$privateboard) || ($privateboard == "1" && checkAccess("accessvip")) || ($privateboard == "2" && checkAccess("accessinsider")))
{
   
   // If we're told to go to the last page, do it... but we need to work
   // out the page number of the last page according to how many posts
   // we show per page
   if ($page == "last")
   {
     $postcount = $threaddata['postcount'];
     $numpages = ($postcount / $configoptions['perpageposts']);
     $page = intval($numpages);
     if ($numpages > intval($numpages))
     {
       $page = intval($numpages)+1;
     }
   }
   
   $viewthread = viewThread($threadid, $page);

   $output    = $viewthread['threadoutput'];
   $centerrow = $viewthread['navbar'];

   $newviewcount = intval($threaddata['viewcount']+1);
   //echo "Current View Count: ".$threaddata['viewcount']."<BR>\n";
   //echo "New View Count: ".intval($newviewcount)."<BR>\n";
   $updateviewcount = incrementViewCount($threadid, intval($newviewcount));
   
   if (checkAccess("accesswrite"))
   {
if (checkAccess("accessvip") || !$vippost)
{
     // If the thread is locked, viewthread() will output [threadlocked]
     if (!$viewthread['threadlocked'])
     {
       $areaoptions[] = array("name" => "Post Reply",
                              "url"  => "post.php?threadid=$threadid&post=".$threaddata['postidfirst']);
     }
}
   }
}
else
{
header("Location: noaccess.php");
}
   if (checkAccess("accessmoderator"))
   {
     // If the thread is locked, viewthread() will output [threadlocked]
     if ($viewthread['threadlocked'])
     {
	   
       $areaoptions[] = array("name" => "Unlock Topic",
                              "url"  => "moderators.php?action=unlock&threadid=$threadid&returnurl=".urlencode($PHP_SELF.$querystring));
     }
     else
     {
       $areaoptions[] = array("name" => "Lock Topic",
                              "url"  => "moderators.php?action=lock&threadid=$threadid&returnurl=".urlencode($PHP_SELF.$querystring));
     }
     // Check to see if the thread is sticky
     if (isSticky($threadid))
     {
       $areaoptions[] = array("name" => "Remove Sticky",
                              "url"  => "moderators.php?action=sticky&sticky=0&threadid=$threadid&returnurl=".urlencode($PHP_SELF.$querystring));
     }
     else
     {
       $areaoptions[] = array("name" => "Make Sticky",
                              "url"  => "moderators.php?action=sticky&sticky=1&threadid=$threadid&returnurl=".urlencode($PHP_SELF.$querystring));
     }

     $areaoptions[] = array("name" => "Delete Thread",
                            "url"  => "moderators.php?action=deletethread&threadid=$threadid&returnurl=".urlencode("board.php?boardid=".$boarddata['ID']),
                            "onclick" => "return confirmLink(this, 'delete this thread?')");
     $areaoptions[] = array("name" => "Move Thread",
                            "url"  => "moderators.php?action=movethread&threadid=$threadid&returnurl=".urlencode("board.php?boardid=".$boarddata['ID']));
   }
   
   // If you want to provide next/previous thread links, do 'em here
   // ..........
   
   
   // Show the last 10 logins on the bottom of the page
   $lastusers = fetchLoggedinUsers(10, "recent");
   if ($lastusers['userlist'])
   {
     $bottommessage = makeLink("usersonline.php", "Currently online", "MainMenuLink").": ".$lastusers['userlist'];
   }
   
   $pagecontents = $output;
   include("layout.php");
?>
