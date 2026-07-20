<?php
   $starttime = microtime();
   include("common.php");
   $sql = "SELECT private FROM " . TABLE_BOARDS . " WHERE ID = '" . $_GET['boardid'] . "'";
   $exe = mysqli_query($mysql, $sql);
   $row = mysqli_fetch_array($exe);
// if ((!$row['private']) || (checkAccess("accessvip")))
if ((!$row['private']) || ($row['private'] == "1" && checkAccess("accessvip")) || ($row['private'] == "2" && checkAccess("accessinsider")))
{
   
   $viewboard = viewBoard($boardid, $page);
   
   $output    = $viewboard['boardoutput'];
   $centerrow = $viewboard['navbar'];
   $sql = "SELECT vippost FROM " . TABLE_BOARDS . " WHERE ID = '" . $_GET['boardid'] . "'";
   $exe = mysqli_query($mysql, $sql);
   $row = mysqli_fetch_array($exe);
if ((!$row['vippost']) || (checkAccess("accessvip")))
{ 
   $areaoptions[] = array("name" => "Post New Topic",
                          "url"  => "post.php?boardid=$boardid");
   if ($userloggedin)
   {
     $areaoptions[] = array("name" => "Create Poll",
                            "url"  => "poll.php?boardid=$boardid&returnurl=".urlencode($PHP_SELF.$querystring));
   }
}
else
{
   $areaoptions[] = array("name" => "Only Admins Can Create Topics For This Board",
			"url" => "");
}

   if ($userloggedin)
   {
     if (!inFavourites($userdata['ID'], "boardid", $boardid))
     {
       $areaoptions[] = array("name" => "Add to Favourites",
                              "url"  => "watch.php?action=addboard&boardid=$boardid&returnurl=".urlencode($PHP_SELF.$querystring));
     }
   }

   // Show the last 10 logins on the bottom of the page
   $lastusers = fetchLoggedinUsers(10, "recent");
   if ($lastusers['userlist'])
   {
     $bottommessage = makeLink("usersonline.php", "Currently online", "MainMenuLink").": ".$lastusers['userlist'];
   }
}
else
{
header("Location: noaccess.php");
}
   
   $pagecontents = $output;
   include("layout.php");
?>
