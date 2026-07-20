<?php
   $starttime = microtime();
   $protectedpage = 1;
   include("common.php");
global $canpostmes;
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
   
   if ($action == "post")
   {
     // Workaround for form submission via carriage return in the subject field - no
     // button is pressed and thus no $button comes in so the page doesn't know what
     // to do. We send through the button label separately and read it here so we
     // can pretend they hit the button and carry out the form's default action.
     if (!$button)
     {
       $button = $proceedbuttonlabel;
     }
     
     // Behaves differently based on whether or not "Post", "Edit" or "Preview" was pressed

     if ($button == " Post ")
     {
       $cancontinue = 1;

       if (!checkAccess("accesswrite") || !$canpostmes)
       {
         $cancontinue = 0;
         $nowriteaccess = 1;
       }
       
       if ($cancontinue)
       {
         if ($threadid)
         {
           // Make sure the thread they're replying to isn't locked
           $locked = isLocked($threadid);
           
           // If its not, go ahead and make the new post
   
           if (!$locked)
           {
             $post = newPost($threadid, $subject, $userdata['ID'], $body);
             if ($post)
             {
               if (false)
               {
                 $subject = stripSlashes($subject);
               }
               $output = "Your message, <I>".ProfanityFilter($subject)."</I> has been posted.<BR>\n"
                        .makeLink("thread.php?threadid=$threadid", "Click here to see it");
             }
           }
           else
           {
             // It's locked, they're screwed
             $output = "You can't reply to a locked thread.";
           }
         }
         elseif ($boardid)
         {
           $thread = newThread($boardid, $subject, $userdata['ID'], $body);
           if ($thread)
           {
             if (false)
             {
               $subject = stripSlashes($subject);
             }
             $output = "Your new thread, <I>".ProfanityFilter($subject)."</I> has been created.<BR>\n"
                      .makeLink("thread.php?threadid=$thread", "Click here to see it");
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
header("Location: noaccess.php");
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
     if ($post)
     {
       $replytodata = fetchRow($post, TABLE_POSTS);
     }
     //print_r($replytodata);
     //$replytoauthor = fetchRow($replytodata['authorid'], TABLE_USERS);
     
     // If its not already set, set the subject line here, but only if we're
     // replying. If $threadid isn't set, we're starting a new thread on the
     // message board so let them type in their own subject line
     if ($threadid)
     {
       $locked = isLocked($threadid);
       
       $cancontinue = 0;
       if (!$locked)
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
  
         $navigation[] = array("name" => "Post Reply",
                               "url"  => "");
       }
     }
     else
     {
       $navigation[] = array("name" => "Post New Thread",
                             "url"  => "");
     }
     
     $proceedbuttonlabel = "Post";
     // State-dependant settings

     // Whereas, if we're creating a new post, do this...
     if (!$locked)
     {
       $cancontinue = 1;
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

     
     if ($threadid)
     {
       $threaddisplay = makeLink("thread.php?&threadid=".$threadid, "View the thread you're replying to in a new window", "", array("target" => "_new"))."<BR>\n";
     }
         
     if ($cancontinue && $canpostmes)
     {
       include("elements/post.php");
       $output = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                .inputHidden("action", "post")
                .inputHidden("proceedbuttonlabel", " ".$proceedbuttonlabel." ")
                .inputHidden("threadid", $threadid)
                .inputHidden("boardid", $boardid)
                .inputHidden("post", $post)
                .$replyinformation
                .$postform
                .$threaddisplay
                //."<SPAN CLASS='InputSection'>Lock Topic?</SPAN> ".inputChoice("yesno", "locked", $locked)
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