<?php
   $starttime = microtime();
   include("common.php");
   
   if ($action)
   {
     if ($username)
     {
       $vars['classid'] = 1;
       
       $userinfo = fetchRow($username, TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
       if ($userinfo['ID'])
       {
		if ($userinfo['ID'] == "1")
		{
         $update = updateUser($userinfo['ID'], $vars);
         if ($update)
         {
           $output = "$username has been upgraded.<BR>\n"
                    ."<P>\n"
                    ."<SPAN CLASS='red'>\n"
					."You don't have to delete this file as it will only upgrade the first user ever "
					."registered with the boards.  If anyone else try's to use it, it wont work."
                    ."</SPAN>\n";
         }
         else
         {
           $reason = "Couldn't update this user - you'll have to do it manually.<BR>\n";
           $action = "";
         }
		}
if ($userinfo['ID'] != "1")
{

	$hackip = $REMOTE_ADDR;
	$message = "Someone tried to hack your discoboard.\n"
			  ."UserName: ".$username."\n"
			  ."IP Address: ".$hackip."\n\n"
			  .$SiteURL."".$BaseDir;
	$from = "Hack-Attempt@gcshideout.com";
	$to = $EmailAddress;
	mail($to, "Hack Attempt", $message, "From:<".$from.">");

           $reason = "Couldn't update this user - you'll have to do it manually.<BR>\n";
           $action = "";	
}
       }
       else
       {
         $reason = "Didn't find a user named  <I>".$username."</I>. Mistyped?<BR>\n";
         $action = "";
       }
     }
     
   }
   
   if (!$action)
   {
     $output = "Enter a username to upgrade to Super User (class 1)\n"
              ."<P>\n"
              ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
              .inputHidden("action", "super")
              .inputText("username", $username)
              .inputSubmit("Upgrade")
              ."</FORM>\n";
   }

   if ($reason)
   {
     $reasonoutput = "<B CLASS='red'>".$reason."</B>\n"
                    ."<P>\n";
   }
   
   $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
            ."<TR><TD CLASS='BoardRowBody'>\n"
            ."        ".str_replace("\n", "\n        ", $reason.$output)."</TD></TR>\n"
            ."</TABLE>\n";
   $pagecontents = $output;
   include("layout.php");
?>