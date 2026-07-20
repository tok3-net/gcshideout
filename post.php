<?php
   $starttime = microtime();
   $protectedpage = 1;
   include("common.php");
   $sql = "SELECT vippost, private FROM " . TABLE_BOARDS . " WHERE ID = '" . $_GET['boardid'] . "'";
   $exe = mysqli_query($mysql, $sql);
   $row = mysqli_fetch_array($exe);
	$vippost = $row['vippost'];
	$privateboard = $row['private'];
   $threaddata = fetchRow($threadid, TABLE_THREADS);
   $boarddata  = fetchRow($threaddata['boardid'], TABLE_BOARDS);
   $groupdata  = fetchRow($boarddata['groupid'], TABLE_GROUPS);
   $sql = "SELECT private FROM " . TABLE_BOARDS . " WHERE ID = '" . $threaddata['boardid'] . "'";
   $exe = mysqli_query($mysql, $sql);
   $row = mysqli_fetch_array($exe);
   $privateboard = $row['private'];
if ((!$vippost) &&  ((!$privateboard) || ($privateboard == "1" && checkAccess("accessvip")) || ($privateboard == "2" && checkAccess("accessinsider"))))
{
$canpostmes = 1;
}
if (checkAccess("accessvip"))
{
$canpostmes = 1;
}

   if ($action == "process" && $canpostmes)
   {
     if (!trim($subject)) { $err[] = "No subject was entered"; }
     if (!trim($body))    { $err[] = "No body was entered"; }
     if ($button != " Edit ")
     {
       // Don't check the threshold if we're only editing
       if (overThreshold ("post", $userdata['ID']))
                          { $err[] = "Sorry, your post threshold has been reached"; }
     }
   }
   
   if (is_array($err))
   {
     $reason = implode("<BR>\n", $err);
     $action = "";
   }
   
   if ($action == "process" && $canpostmes)
   {
     // Workaround for form submission via carriage return in the subject field - no
     // button is pressed and thus no $button comes in so the page doesn't know what
     // to do. We send through the button label separately and read it here so we
     // can pretend they hit the button and carry out the form's default action.
     if (!$button)
     {
       $button = " Post ";
     }
     
     // Behaves differently based on whether or not "Post", "Edit" or "Preview" was pressed
     if ($button == " Post " && $canpostmes)
     {
       $cancontinue = 1;
       if (!checkAccess("accesswrite") && !$canpostmes)
       {
         $cancontinue = 0;
         $nowriteaccess = 1;
       }
    
       if ($cancontinue && $canpostmes)
       {
         if ($threadid)
         {
           // Make sure the thread they're replying to isn't locked
           $locked = isLocked($threadid);

           
           // If its not, go ahead and make the new post
           if ((!$locked) || (checkAccess("accessmoderator")))
           {
if ($canpostmes)
{
             $post = newPost($threadid, $subject, $userdata['ID'], $body);
             if ($post)
             {
               if (false)
               {
                 $subject = stripSlashes($subject);
               }

	header("Location: thread.php?threadid=".$threadid."&page=last");
             }
}
else
{
header("Location: noaccess.php");
}
           }
           else
           {
             // It's locked, they're screwed
             $output = "You can't reply to a locked thread.";
           }

         }
         elseif ($boardid && $cancontinue && $canpostmes)
         {
           $thread = newThread($boardid, $subject, $userdata['ID'], $body);
           if ($thread)
           {

	header("Location: thread.php?threadid=$thread");
           }
         }
         else
         {
           $output = "Sorry, something went wrong.<BR>\n";
         }
       }
       else
       {
         // They don't have accesswrite if they get to here
         $output = "Sorry, you don't have write access";
       }
     }
     elseif ($button == "Preview" && $canpostmes)
     {
       // Strip the \'s if magic quotes is on
       if (false)
       {
         $subject = stripSlashes($subject);
         $body    = stripSlashes($body);
       }
       
       // Use formatPost() to present this as if it had come from the boards proper
       $postdata['authorid']      = $userdata['ID'];
       $postdata['sig1']          = $userdata['sig1'];
       $postdata['sig2']          = $userdata['sig2'];
       $postdata['sig3']          = $userdata['sig3'];
       $postdata['sig4']          = $userdata['sig4'];
       $postdata['sig5']          = $userdata['sig5'];
       $postdata['displayname']   = $userdata['displayname'];
       $postdata['displayformat'] = $userdata['displayformat'];
       $postdata['postcount']     = $userdata['postcount'];
       $postdata['created']       = $userdata['created'];
       $postdata['postdate']      = Date("U");
       $postdata['subject']       = $subject;
       $postdata['body']          = $body;
       
       $preview = "<TABLE WIDTH=100% ALIGN=CENTER CELLPADDING=0 CELLSPACING=1 BORDER=3 BORDERCOLOR='red'><TR><TD>\n"
                 ."<TABLE WIDTH=100% ALIGN=CENTER CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
                 .formatPost($postdata)
                 ."</TABLE>\n"
                 ."</TABLE>\n";
       $action = "";
     }
   }
   
   if (!$action)
   {

     if ($post && $canpostmes)
     {
       $replytodata = fetchRow($post, TABLE_POSTS);
     }
     //print_r($replytodata);
     //$replytoauthor = fetchRow($replytodata['authorid'], TABLE_USERS);
     
     // If its not already set, set the subject line here, but only if we're
     // replying. If $threadid isn't set, we're starting a new thread on the
     // message board so let them type in their own subject line
     if ($threadid && $canpostmes)
     {
       $locked = isLocked($threadid);
       
       $cancontinue = 0;
       if (!$locked || checkAccess("accessmoderator"))
       {
         $cancontinue = 1;
         if (!$subject)
         {
           $subject = $replytodata['subject'];
         }
         $replyinformation = "<SPAN CLASS='InputSection'>In Response To:</SPAN>\n"
                            .makeLink("thread.php?threadid=".$threadid, ProfanityFilter($replytodata['subject']), "BoardRowBodyLink")."<BR>\n"
                            ."<SPAN CLASS='InputSection'>Posted By:</SPAN>\n"
                            .usernameDisplay($replytodata['authorid'], "", "showstar")."<BR>\n"
                            ."<P>\n";
if ($canpostmes)
{  
         $navigation[] = array("name" => "Post Reply",
                               "url"  => "");
}
       }
     }
     else
     {
if ($canpostmes)
{
       $navigation[] = array("name" => "Post New Thread",
                             "url"  => "");
}
     }
     
     $proceedbuttonlabel = "Post";
     // State-dependant settings

     // Whereas, if we're creating a new post, do this...
     if (!$locked || checkAccess("accessmoderator"))
     {
if ($canpostmes)
{
       $cancontinue = 1;
}
else
{
	$cancontinue = 0;
}
     }

     // Check whether they're over their threshold or not
     // (this used to be outside the post/edit condition, which
     // stopped users editing if they were over their threshold
     // ... whoops)
     if (overThreshold ("post", $userdata['ID']))
     {
       $cancontinue = 0;
       $thresholdreached = 1;
     }
     
     // Check that they have access to "accesswrite" or they
     // won't be allowed to continue...
     if (!checkAccess("accesswrite") || !$canpostmes)
     {
       $cancontinue = 0;
       $nowriteaccess = 1;
     }

     if ($threadid && $canpostmes)
     {
       $threaddisplay = makeLink("thread.php?threadid=".$threadid, "View the thread you're replying to in a new window", "", array("target" => "_new"))."<BR>\n";
     }
// =======================
// VIP Post SQL Was Here      
// ======================
     if ($cancontinue && $canpostmes)
     {

       include("elements/post.php");
       $output = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                .inputHidden("action", "process")
                .inputHidden("threadid", $threadid)
                .inputHidden("boardid", $boardid)
                .inputHidden("post", $post)
                .$replyinformation
                .$postform
                .$threaddisplay
                ."<P>\n"
                .inputSubmit(" ".$proceedbuttonlabel." ")." "
                .inputSubmit("Preview")
                ."</FORM>\n";
     }
     else
     {
       if ($locked)
       {
         $output = "You can't reply to a locked thread.";
       }
       elseif ($thresholdreached)
       {
         $output = "Sorry, your post threshold has been reached";
       }
       elseif ($nowriteaccess)
       {
header("Location: noaccess.php");
       }
       elseif ($vippost)
       {
	header("Location: noaccess.php");
       }
     }
   }
   
   if ($reason)
   {
     $reasonoutput = "<B CLASS='red'>".$reason."</B>\n"
                    ."<P>\n";
   }
   
   $output = $reasonoutput.$output;

   $output = $preview
            ."<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
            ."<TR><TD CLASS='BoardRowBody'>\n"
            ."        ".str_replace("\n", "\n        ", $output)."</TD></TR>\n"
            ."</TABLE>\n";
            
   $pagecontents = $output;
   include("layout.php");
?>