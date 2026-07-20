<?php
   $starttime = microtime();
   $protectedpage = 1;
   include("common.php");

   $postdata = fetchRow($postid, TABLE_POSTS);
   $threaddata = fetchRow($postdata['threadid'], TABLE_THREADS);
   $authordata = getUserInformation($postdata['authorid']);

   if ($action == "post")
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
   
   if ($action == "process")
   {
     // Workaround for form submission via carriage return in the subject field - no
     // button is pressed and thus no $button comes in so the page doesn't know what
     // to do. We send through the button label separately and read it here so we
     // can pretend they hit the button and carry out the form's default action.
     if (!$button)
     {
       $button = " Edit ";
     }
     
     // Behaves differently based on whether or not "Post", "Edit" or "Preview" was pressed
     if ($button == " Edit ")
     {
       // Get the thread this post is in
       $postdata = fetchRow($postid, TABLE_POSTS);
       $oldeditcount = $postdata['editcount'];
       
       $editdata = isEditable($postdata);
       if ($editdata['editable'])
       {
         $post = updatePost($postid, $subject, $body, $userdata['ID'], $oldeditcount);
         if ($post == $postid)
         {
           if ((checkAccess("accessmoderator")) ||  
               ($postdata['authorid'] == $userdata['ID']) && ($postid == $threaddata['postidfirst']))
           {
             // Check to see if the locked status of this thread changed
             if ($locked != $originallocked)
             {
               // It did, so lock/unlock the thread accordingly
               switch ($locked)
               {
                 case "YES":
                   $newstatus = 'L';
                   $lockupdate = ", and the thread has been locked";
                   break;
                 case "NO":
                   $newstatus = "x";
                   $lockupdate = ", and the thread has been unlocked";
                   break;
               }
               // Here's where we actually do it
               //echo "$postdata[threadid], $newstatus<BR>\n";
               $lock = toggleLock($postdata['threadid'], $newstatus);
               //print_r($lock);
             }
           }
           
           if ($userdata['ID'] == $postdata['authorid'])
           {
             $messageowner = "Your";
           }
           else
           {
             $messageowner = "This";
           }
           
           if (false)
           {
             $subject = stripSlashes($subject);
           }
           $output = $messageowner." message, <I>".ProfanityFilter(applyOnlyTextEffects($subject))."</I> (ID ".$postid.") has been updated".$lockupdate.".<BR>\n"
                    .makeLink("thread.php?threadid=".$postdata['threadid'], "Click here to see it");
           
         }
       }
       else
       {
         $output = "You can't edit this message";
       }
     }
     elseif ($button == "Preview")
     {
       // Strip the \'s if magic quotes is on
       if (false)
       {
         $subject = stripSlashes($subject);
         $body    = stripSlashes($body);
       }

       // Use formatPost() to present this as if it had come from the boards proper
       $postdata['authorid']      = $postdata['authorid'];
       $postdata['sig1']          = $authordata['sig1'];
       $postdata['sig2']          = $authordata['sig2'];
       $postdata['sig3']          = $authordata['sig3'];
       $postdata['sig4']          = $authordata['sig4'];
       $postdata['sig5']          = $authordata['sig5'];
       $postdata['displayname']   = $authordata['displayname'];
       $postdata['displayformat'] = $authordata['displayformat'];
       $postdata['postcount']     = $authordata['postcount'];
       $postdata['created']       = $authordata['created'];
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
     // If we've come back here from a preview, these two vars will exist...
     if (!$subject) { $subject = $postdata['subject']; }
     if (!$body)    { $body = $postdata['body']; }

     // Populate the lock-topic drop-down
     $locked = "NO";
     $lockedstatus = isLocked($postdata['threadid']);
     if ($lockedstatus)
     {
       $locked = "YES";
     }

     $navigation[] = array("name" => "Edit Message",
                           "url"  => "");
   
     $editdata = isEditable($postdata);
     
     if ($editdata['editable'])
     { 
       $cancontinue = 1;
     }
   
     if ($cancontinue)
     {
       if ( (checkAccess("accessmoderator")) || 
            (($postdata['authorid'] == $userdata['ID']) && ($postdata['ID'] == $threaddata['postidfirst'])) 
          )
       {
         $lockhidden = inputHidden("originallocked", $locked);
         $lockcontrol = "Lock Topic? ".inputChoice("yesno", "locked", $locked);
       }
       include("elements/post.php");
       $output = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                .inputHidden("action", "process")
                .inputHidden("threadid", $threadid)
                .inputHidden("boardid", $boardid)
                .inputHidden("postid", $postid)
                .$lockhidden
                .$replyinformation
                .$postform
                .$threaddisplay
                ."<P>\n"
                .inputSubmit(" Edit ")." "
                .inputSubmit("Preview")
                ."<P>\n"
                .$lockcontrol
                ."</FORM>\n";
     }
     else
     {
       $output = "You can't edit this post.";
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
