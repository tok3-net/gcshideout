<?php
   $starttime = microtime();
   $protectedpage = 1;
   include("common.php");

   if (checkAccess("accessmoderator"))
   {
     if ($action == "movethread")
     {
       $threaddata = fetchRow($threadid, TABLE_THREADS);
       $boarddata  = fetchRow($threaddata['boardid'], TABLE_BOARDS);
       $postdata   = fetchRow($threaddata['postidfirst'], TABLE_POSTS);
       $postids    = fetchIDs($threadid, "thread");
       
       // Can't remove a thread from a board if its the only one on the board... 
       // find all thread IDs on the board.
       $threadids = fetchIDs($boarddata['ID'], "board");

       if (count($threadids) > 1)
       {
         if ($step == 2)
         {
           $postids = fetchIDs($threaddata['ID'], "thread");
           
           //echo "Losing Post IDs ".implode(",", $postids);
           
           // By deleting the thread, we destroy the board's postidlast - find a new one.
           if (in_array($boarddata['postidlast'], $postids))
           {
             //echo "BoardLastPost is in that array - adjust it<BR>\n";
             $newboardlastpostid = fetchPreviousPost($boarddata['ID'], $postids);
             $sql = "UPDATE ".TABLE_BOARDS." SET postidlast = ".intval($newboardlastpostid)."  WHERE ID = ".intval($boarddata['ID']);
             $exe = runQuery($sql);
             //echo "BoardLastPost is now $newboardlastpostid<BR>\n";
           }
           
           // Now, change the thread's boardid to $boardid
           $update['boardid']    = $boardid;
           $update['oldboardid'] = $boarddata['ID'];
           
           $updatethread = updateThread($threadid, $update);
           
           if ($updatethread)
           {
             $newboarddata = fetchRow($boardid, TABLE_BOARDS);
             
             $output = "<B>".$postdata['subject']."</B> (with ".count($postids)." posts) has been moved to <B>".$newboarddata['boardname']."</B><BR>\n"
                      .makeLink("thread.php?threadid=".$threadid, "Click here to see the thread");
           }
           else
           {
             // ???
           }
         }
         
         if (!$step)
         {
           $output = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                    .inputHidden("action", "movethread")
                    .inputHidden("step", "2")
                    .inputHidden("threadid", $threadid)
                    .inputHidden("returnurl", $returnurl)
                    ."Move the thread <B>".$postdata['subject']."</B> (".count($postids)." posts) to:<BR>\n"
                    .inputDBCycle("boardid", $boardid, TABLE_BOARDS, "ID", "boardname", "boardname")
                    .inputSubmit("Move")
                    ."</FORM>\n";
         }
       }
       else
       {
         $output = "<B CLASS='red'>This is the only thread - you can't move it!</B>";
       }
     }
     
     if ($action == "deletethread")
     {
       $threaddata = fetchRow($threadid, TABLE_THREADS);
       $boarddata  = fetchRow($threaddata['boardid'], TABLE_BOARDS);

       // Can't delete a thread if its the only one on the board... find all
       // thread IDs on the board.
       $threadids = fetchIDs($boarddata['ID'], "board");

       if (count($threadids) > 1)
       {
         $postids = fetchIDs($threaddata['ID'], "thread");
         
         //echo "Losing Post IDs ".implode(",", $postids);
         
         // By deleting the thread, we destroy the board's postidlast - find a new one.
         if (in_array($boarddata['postidlast'], $postids))
         {
           //echo "BoardLastPost is in that array - adjust it<BR>\n";
           $newboardlastpostid = fetchPreviousPost($boarddata['ID'], $postids);
           $sql = "UPDATE ".TABLE_BOARDS." SET postidlast = ".intval($newboardlastpostid)."  WHERE ID = ".intval($boarddata['ID']);
           $exe = runQuery($sql);
           //echo "BoardLastPost is now $newboardlastpostid<BR>\n";
         }

         // Safe to delete, there are other threads
         removeThread($threadid);

         $output = "Thread # ".$threaddata['ID']." was removed.";
       }
       else
       {
         // Not safe to delete, since its the only thread on the board
         $output = "<B CLASS='red'>This is the only thread - you can't delete it!</B>";
       }
     }
     
     if ($action == "deletepost")
     {
       $postdata = fetchRow($postid, TABLE_POSTS);
       $authordata = fetchRow($postdata['authorid'], TABLE_USERS);
       
       // If the post is the first post in a thread, we need the second
       $threaddata = fetchRow($postdata['threadid'], TABLE_THREADS);
       $boarddata  = fetchRow($threaddata['boardid'], TABLE_BOARDS);
       $postids = fetchIDs($threaddata['ID'], "thread");
       
       //echo "Board LastPost = $boarddata[postidlast] vs $postid<BR>\n";
       if ($boarddata['postidlast'] == $postid)
       {
         $newboardlastpostid = fetchPreviousPost($boarddata['ID'], $postid);
         //echo "New Last Post for this board should be $newboardlastpostid<BR>\n";
       }
       
       if ( ($threaddata['postidfirst'] == $postid) ||
            ($threaddata['postidlast'] == $postid) )
       {
         for ($i = 0; $i < count($postids); $i++ )
         {
           $thispostid = $postids[$i];
           $prevpostid = $postids[($i-1)];
           $nextpostid = $postids[($i+1)];
           
           //echo "ThisPostID: $thispostid<BR>\n";
           //echo "PrevPostID: $prevpostid<BR>\n";
           //echo "NextPostID: $nextpostid<BR>\n";
           
           if (!$firstpostdecisionmade)
           {
             if (($threaddata['postidfirst'] == $postid) && ($thispostid == $postid))
             {
               // Need to replace postidfirst with $nextpostid if it exists. 
               // If not, then we can just delete the thread
               if ($nextpostid)
               {
                 // Update the first post ID and the post count
                 $vars['postidfirst'] = $nextpostid;
                 
                 $deletepost = 1;
                 $updatethread = 1;
               }
               else
               {
                 $removethread = 1;
               }
               $firstpostdecisionmade = 1;
             }
           }
           
           if (!$lastpostdecisionmade)
           {
             //echo "Examining $threaddata[postidlast] v $postid v $thispostid<BR>\n";
             if (($threaddata['postidlast'] == $postid) && ($thispostid == $postid))
             {
               //echo "Matches! PrevPostID is $prevpostid...<BR>\n";
               // Need to replace postidlast with $prevpostid if it exists. 
               // If not, then we can just delete the thread
               if ($prevpostid)
               {
                 // Update the last post ID and the post count
                 $vars['postidlast'] = $prevpostid;
    
                 $deletepost = 1;
                 $updatethread = 1;
               }
               else
               {
                 $removethread = 1;
               }
               $lastpostdecisionmade = 1;
             }
           }
         }
       }
       else
       {
         // If the post isn't the first, nor the last, then it must be safe
         // to just delete the bugger!
         $deletepost = 1;
         // Mind you, at the same time you do need to decrement the 
         // postcount in the thread :)
         $updatethread = 1;
       }
       
       if ($updatethread)
       {
         $vars['postcount']   = ($threaddata['postcount']-1);
         
         //echo "Updating the thread:<BR>\n";
         //print_r($vars);
         
         $update = updateThread($threaddata['ID'], $vars);
       }
       
       if ($removethread)
       {
         // Can't delete a thread if its the only one on the board...
         $boarddata = fetchRow($threaddata['boardid'], TABLE_BOARDS);
         $threadids = fetchIDs($boarddata['ID'], "board");

         if (count($threadids) > 1)
         {
           // Safe to delete, there are other threads
           removeThread($threaddata['ID']);
         }
         else
         {
           // Not safe to delete, so just remove the content of the message 
           $updatepost = updatePost($postid, "Deleted", "Message deleted", $userdata['ID'], 0);
         }
       }
       
       if ($deletepost)
       {
         removePost($postid);
         
         // Decrement the author's post counter
         $uservars['postcount'] = ($authordata['postcount']-1);
         updateUser($postdata['authorid'], $uservars);
       }

       // Set the board's lastpostid to $newboardlastpostid
       if ($newboardlastpostid)
       {
         $sql = "UPDATE ".TABLE_BOARDS." SET postidlast = ".intval($newboardlastpostid)."  WHERE ID = ".intval($boarddata['ID']);
         $exe = runQuery($sql);
         //echo "$postid was the lastpostid for board $boarddata[ID]. Now its been changed to $newboardlastpostid.<BR>\n";
       }

       if ($returnurl)
       {
         $sysmsg = "sysmsg=postdeleted";
         
         if (!strstr($returnurl, "?"))
         { $returnurl = $returnurl."?".$sysmsg; }
         else
         { $returnurl = $returnurl."&".$sysmsg; }
         
         Header("Location: $returnurl");
         Exit();
       }
       else
       {
         $threadlink = makeLink("thread.php?threadid=$threaddata[ID]", "Click here to see the thread");
 
         if ($deletepost)
         { $did[] = "Post # ".$postid." was removed."; }
         if ($removethread)
         { $did[] = "Thread # ".$threaddata['ID']." was removed."; 
           $threadlink = "";
         }
         if ($updatepost)
         { $did[] = "Post # ".$postid."'s content was removed."; }
         
         $output = implode("<BR>\n", $did)."<BR>\n"
                  .$threadlink;
       }
     }
     
     if (($action == "lock") || ($action == "unlock"))
     {
       $newstatus = 'L';
       if ($action == "unlock") { $newstatus = "x"; }
       
       $lock = toggleLock($threadid, $newstatus);
       if ($lock)
       {
         if ($returnurl)
         {
           if ($action == "lock")
           {
             $sysmsg = "sysmsg=threadlocked";
           }
           else
           {
             $sysmsg = "sysmsg=threadunlocked";
           }
           
           if (!strstr($returnurl, "?"))
           { $returnurl = $returnurl."?".$sysmsg; }
           else
           { $returnurl = $returnurl."&".$sysmsg; }
           
           Header("Location: $returnurl");
           Exit();
         }
         else
         {
           $output = "This thread's locked state has been changed.<BR>\n"
                    .makeLink("thread.php?threadid=$threadid", "Click here to see it");
         }
       }
       else
       {
         // Err, something went wrong?
       }
     }
     
     if ($action == "sticky")
     {
       $stickystatus = setSticky($threadid, $sticky);
       
       if ($stickystatus)
       {
         if ($returnurl)
         {
           if ($sticky)
           {
             $sysmsg = "sysmsg=threadsticky";
           }
           else
           {
             $sysmsg = "sysmsg=threadnotsticky";
           }
           
           if (!strstr($returnurl, "?"))
           { $returnurl = $returnurl."?".$sysmsg; }
           else
           { $returnurl = $returnurl."&".$sysmsg; }
           
           Header("Location: $returnurl");
           Exit();
         }
         else
         {
           $output = "This thread's sticky state has been changed.<BR>\n"
                    .makeLink("thread.php?&threadid=$threadid", "Click here to see it");
         }
       }
       else
       {
         // Err, something went wrong?
       }
     }
     
   }
   else
   {
header("Location: noaccess.php");
   }


   if ($reason)
   {
     $reasonoutput = "<B CLASS='red'>".$reason."</B>\n"
                    ."<P>\n";
   }
   $output = $reasonoutput
            .$output;
   
   $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
            ."<TR><TD CLASS='BoardRowBody'>\n"
            ."        ".str_replace("\n", "\n        ", $output)."</TD></TR>\n"
            ."</TABLE>\n";
            
   $pagecontents = $output;
   include("layout.php");
?>
