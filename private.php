<?php
   $starttime = microtime();
   $protectedpage = 1;
   include("common.php");

   $navigation[] = array("name" => "Private Messages",
                         "url"  => "$PHP_SELF");

   $areaoptions[] = array("name" => "Write New Message",
                          "url"  => $PHP_SELF."?action=send");
   $areaoptions[] = array("name" => "Unread Messages",
                          "url"  => $PHP_SELF."?action=unread");
   $areaoptions[] = array("name" => "Read Messages",
                          "url"  => $PHP_SELF."?action=read");
   $areaoptions[] = array("name" => "Sent Messages",
                          "url"  => $PHP_SELF."?action=sent");
   
   if (!$action)
   { $action = "unread"; }
   
   if ($action == "viewmessage")
   {
     $navigation[] = array("name" => "Read Message",
                           "url"  => "");

     $message = viewPrivateMessage($messageid);
     
     $output = $message['message'];
   }
   
   if ($action == "sendmessage")
   {
     $navigation[] = array("name" => "Send Message",
                           "url"  => "");

     if (!$recipient)     { $err[] = "No recipient was specified"; }
     else
     {
       // If recipient has a , in it, it's bound for multiple recipients so we 
       // need to break it apart on the , and verify them all
       if (strstr($recipient, ","))
       {
         $recipients = explode(",", $recipient);
         for ($i = 0; $i < count($recipients); $i++ )
         {
           $thisrecipient = trim($recipients[$i]);
           
           $recipientinfo = fetchRow($thisrecipient, TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
           if (!$recipientinfo['ID'])
                            { $err[] = "One of the recipients, <I>".$recipients[$i]."</I>, was not found"; }
           else
           {
             $recipientids[] = $recipientinfo['ID'];
           }
         }
         
         $recipientoutput = implode(", ", $recipients);
       }
       else
       {
         // No , so its just going for one user
         $recipientinfo = fetchRow($recipient, TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
         
         if (!$recipientinfo['ID'])
                            { $err[] = "The recipient of this message, <I>".$recipient."</I>, was not found"; }
         else
         {
           $recipientids[] = $recipientinfo['ID'];
         }
         
         $recipientoutput = $recipient;
       }
     }
     if (!trim($subject)) { $err[] = "No subject was specified"; }
     
     if ($err)
     {
       $reason = implode("<BR>\n", $err);
       $action = "send";
     }
     else
     {
       // Post it to everyone in $recipientinfo...
       for ($i = 0; $i < count($recipientids); $i++ )
       {
         $post = newPost(0, $subject, $userdata['ID'], $body, $recipientids[$i]);
         if (!$post)
         {
           $posterror = 1;
         }
       }
       
       if (!$posterror)
       {
         if (false)
         {
           $subject = stripSlashes($subject);
         }
         $output = "Your message, <B>".$subject."</B>, has been sent to <B>$recipientoutput</B>";
         $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
                  ."<TR><TD CLASS='BoardRowBody'>\n"
                  ."        ".str_replace("\n", "\n        ", $output)."</TD></TR>\n"
                  ."</TABLE>\n";
       }
       else
       {
         $reason = "An error occurred while posting your message.";
         $action = "send";
       }
     }
   }
   
   if ($action == "send")
   {
     $navigation[] = array("name" => "Write New Message",
                           "url"  => "");

     if ($reason)
     {
       $reasonoutput = "<B CLASS='red'>".$reason."</B>\n"
                      ."<P>\n";
     }
     
     // Strip slashes from inbound subject lines
     if (false)
     {
       $originalsubject = stripSlashes($originalsubject);
     }
     
     if ((!$subject) && ($originalsubject))
     {
       $subject = "RE: ".preg_replace('/^RE\: /', "", $originalsubject);
     }

     include("elements/post.php");
     $output = $reasonoutput
              ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
              .inputHidden("action", "sendmessage")
              ."<SPAN CLASS='InputSection'>Recipient</SPAN><BR>\n"
              ."Enter the name of the user you are sending this message to<BR>\n"
              ."To send to multiple users, separate each name with a comma (,)<BR>\n"
              .inputText("recipient", $recipient, 15)
              ."<P>\n"
              .$replyinformation
              .$postform
              ."<P>\n"
              .inputSubmit("Send Message")." "
              ."</FORM>\n";
     $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
              ."<TR><TD CLASS='BoardRowBody'>\n"
              ."        ".str_replace("\n", "\n        ", $output)."</TD></TR>\n"
              ."</TABLE>\n";
   }
   
   if ($action == "delete")
   {
     for ($i = 1; $i <= $checkboxcount; $i++ )
     {
       $delvar = "del".$i;
       //echo "<I>Checking for $delvar</I><BR>\n";
       if ($$delvar)
       {
         $remove = removePost($$delvar);
         if ($remove)
         {
           $deletecount++;
         }
       }
     }
     
     $action = $oldaction;
     $sysmsg = "custom";
     $sysmsgcustomcontent = "Deleted ".intval($deletecount)." private message(s)";
   }
   
   if ($action == "sent")
   {
     $opts['searchtype']  = "sent private";
     $opts['authorid']    = $userdata['ID'];
     $opts['usercolumn']  = "recipient";
     $opts['pagvars']     = "action=sent";
     $opts['showdelete']  = "yes";         // Maybe not?
     $opts['thisaction']  = "sent";        // Irrelevant if not...
     
     $posts = postSearch($opts, $page);
     
     $output    = $posts['msglist'];
     $centerrow = $posts['navbar'];

     $navigation[] = array("name" => "Sent Messages",
                           "url"  => "");
   }
   
   if ($action == "read")
   {
     $opts['searchtype']  = "read private";
     $opts['recipientid'] = $userdata['ID'];
     $opts['status']      = "R";
     $opts['usercolumn']  = "author";
     $opts['pagvars']     = "action=read";
     $opts['showdelete']  = "yes";
     $opts['thisaction']  = "read";
     
     $posts = postSearch($opts, $page);
     
     $output    = $posts['msglist'];
     $centerrow = $posts['navbar'];

     $navigation[] = array("name" => "Read Messages",
                           "url"  => "");
   }
      
   if ($action == "unread")
   {
     $opts['searchtype']  = "unread private";
     $opts['recipientid'] = $userdata['ID'];
     $opts['status']      = "U";
     $opts['usercolumn']  = "author";
     $opts['pagvars']     = "action=unread";
     $opts['showdelete']  = "yes";
     $opts['thisaction']  = "unread";
     
     $posts = postSearch($opts, $page);
     
     $output    = $posts['msglist'];
     $centerrow = $posts['navbar'];

     $navigation[] = array("name" => "Unread Messages",
                           "url"  => "");
   }

   $pageformstart = $posts['formstart'];
   $pageformstop  = $posts['formstop'];
   
   $pagecontents = $output;
   include("layout.php");
?>
