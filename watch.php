<?php
   $starttime = microtime();
   include("common.php");

   $navigation[] = array("name" => "Favourites",
                         "url"  => "$PHP_SELF");

   if ($action == "removeboard")
   {
     $watch = removeFavouriteBoard($userdata['ID'], $boardid);
     if ($watch)
     {
       $msg = "faveboardupdated";
     }
     else
     {
       $msg = "error";
     }
     $doredirect = 1;
   }
   
   if ($action == "addboard")
   {
     $watch = addFavouriteBoard($userdata['ID'], $boardid);
     if ($watch)
     {
       $msg = "faveboardupdated";
     }
     else
     {
       $msg = "error";
     }
     $doredirect = 1;
   }
   
   if ($action == "addthread")
   {
     $watch = addFavouriteThread($userdata['ID'], $threadid);
     if ($watch)
     {
       $msg = "nowwatchingthread";
     }
     else
     {
       $msg = "error";
     }
     $doredirect = 1;
   }
   
   if ($action == "adduser")
   {
     $watch = addWatchedUser($userdata['ID'], $userid);
     if ($watch)
     {
       $msg = "watchedusersupdated";
     }
     else
     {
       $msg = "error";
     }
     $doredirect = 1;
   }
   
   if ($doredirect)
   {
     if (!$returnurl)
     {
       $returnurl = "index.php";
     }
     if (!strstr($returnurl, "?"))
     {
       $returnurl .= "?";
     }
     else
     {
       $returnurl .= "&";
     }
     Header("Location: ".$returnurl."sysmsg=".$msg);
     Exit();
   }
   
   if ($action == "favouriteboards")
   {
     $navigationhead = "Favourite Boards";
     
     $options['extracolheading'] = "Remove";
     $options['extracolcontent']  = makeLink($PHP_SELF."?action=removeboard&boardid=BOARDID&returnurl=".urlencode($PHP_SELF.$querystring), "Remove");
     $favourites = listBoards(0, $userdata['ID'], $options);
     
     $output = $favourites['boardlist'];
     $dontwrapoutput = 1;
   }
   
   if ($action == "removethreads")
   {
     for ($i = 1; $i <= $checkboxcount; $i++ )
     {
       $checkvar = "select".$i;
       //echo "<I>Checking ".$checkvar."</I><BR>\n";
       if ($$checkvar)
       {
         $remove = removeWatchedThread($userdata['ID'], $$checkvar);
       }
     }
     
     $action = $oldaction;
     $sysmsg = "watchedthreadsupdated";
   }
   
   if ($action == "removeusers")
   {
     for ($i = 1; $i <= $checkboxcount; $i++ )
     {
       $checkvar = "select".$i;
       //echo "<I>Checking ".$checkvar."</I><BR>\n";
       if ($$checkvar)
       {
         $remove = removeWatchedUser($userdata['ID'], $$checkvar);
       }
     }
     
     $action = $oldaction;
     $sysmsg = "watchedusersupdated";
   }
   
   if ($action == "threads")
   {
     $navigationhead = "Watched Threads";
     
     $threads = watchedThreads($userdata['ID']);
     $threadlist = outputWatchedThreads($threads);
     
     $pageformstart = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                     .inputHidden("action", "removethreads")
                     .inputHidden("oldaction", $action)
                     .inputHidden("checkboxcount", $threadlist['checkboxcount']);
     $pageformstop  = "</FORM>\n";

     $output = $threadlist['threadlist'];
     $dontwrapoutput = $threadlist['dontwrapoutput'];
   }
   
   if ($action == "users")
   {
     $navigationhead = "Watched Users";
     
     $watcheduserids = getWatchedUsers($userdata['ID']);
     $watchedusers = formatWatchedUsers($watcheduserids);
     
     $pageformstart = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                     .inputHidden("action", "removeusers")
                     .inputHidden("oldaction", $action)
                     .inputHidden("checkboxcount", $watchedusers['checkboxcount']);
     $pageformstop  = "</FORM>\n";

     $output = $watchedusers['html'];
     $dontwrapoutput = $watchedusers['dontwrapoutput'];
   }
   
   if (!$action)
   {
     $navigationhead = "Your Watches";
     
     // Define options
     $pageopt[] = array("name" => "Manage Favourite Boards",
                        "url"  => $PHP_SELF."?action=favouriteboards",
                        "desc" => "See a list of your current Favourite Boards. You can use this \n"
                                 ."option to remove a board from your Favourites list.\n");
     $pageopt[] = array("name" => "Manage Watched Threads",
                        "url"  => $PHP_SELF."?action=threads",
                        "desc" => "This is a list of threads you're currently watching. You can remove \n"
                                 ."threads from your list here.\n");
     $pageopt[] = array("name" => "Manage Watched User List",
                        "url"  => $PHP_SELF."?action=users",
                        "desc" => "This is a list of users you're currently watching. You can remove \n"
                                 ."users from your list here.\n");
     
     // Show a menu of options availabale to the user
     for ($i = 0; $i < count($pageopt); $i++ )
     {
       $output .= makeLink($pageopt[$i]['url'], $pageopt[$i]['name'])."<BR>\n"
                 .$pageopt[$i]['desc']
                 ."<P>\n";
     }
   }
   
   if ($reason)
   {
     $reasonoutput = "<B CLASS='red'>".$reason."</B>\n"
                    ."<P>\n";
   }
   $output = $reasonoutput
            .$output;
   
   if (!$dontwrapoutput)
   {
     $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
              ."<TR><TD CLASS='BoardRowBody'>\n"
              ."        ".str_replace("\n", "\n        ", $output)."</TD></TR>\n"
              ."</TABLE>\n";
   }       
   $pagecontents = $output;
   include("layout.php");
?>