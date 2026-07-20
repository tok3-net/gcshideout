<?php
   $starttime = microtime();
   include("common.php");
   
   $navigationhead = "Password Recovery";

   if ($action == "changepass")
   {
     $change = changePassword($token);
     if ($change)
     {
       $output = "Your password has been changed.<BR>\n"
                ."An email has been sent to ".$change." containing the new password.\n";
     }
     else
     {
       $output = "Sorry, the token you provided appears to be invalid.<BR>\n";
     }
   }
   
   if ($action == "send")
   {
     if (($username) && ($email))  { $err[] = "Please enter ONLY a username OR email address"; }
     else
     {
       if ($username)
       { $recover = fetchRow($username, TABLE_USERS, "displayname", "idfieldistext", "dontcareifnull"); }
       elseif ($email)
       { $recover = fetchRow($email, TABLE_USERS, "contactemail", "idfieldistext", "dontcareifnull"); }
       else
                                   { $err[] = "Either a username or password must be provided"; }
       
       if (!$err)
       {
         if (!$recover['ID'])        { $err[] = "No user matching that criteria was found"; }
       }
     }
     
     if ($err)
     {
       $reason = implode("<BR>\n", $err);
       $action = "";
     }
     else
     {
       $sendemail = sendPasswordRecoveryVerification($recover);
       $output = "A verification email has been sent to ".$recover['contactemail'].".<BR>\n"
                ."Please follow the instructions contained in that email to reset your password.<BR>\n";
     }
   }
   
   if (!$action)
   {
     include("elements/recoverpass.php");
     $output = "" 
              ."<SPAN CLASS='InputSection'>Password Recovery</SPAN><BR>\n"
              ."If you proceed with password recovery, your account will \n"
              ."have a new password set and mailed to you. To ensure that \n"
              ."only the account holder can have a new password set, an \n"
              ."authorisation code will be sent to you via email. \n"
              ."<P>\n"
              ."Enter your username <U>or</U> email address to begin.\n"
              ."<P>\n"
              ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
              .inputHidden("action", "send")
              .$recoverform
              ."<P>\n"
              .inputSubmit("Start")
              ."</FORM>\n";
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