<?php
   $starttime = microtime();
   $protectedpage = 1;
   $functionset   = "admin";
   include("common.php");
if (checkAccess("accessmoderator"))
{

   $navigation[] = array("name" => "Ban / Un-Ban User",
                         "url"  => $PHP_SELF);
{ 
   
   if ($action == "ban")
   {
     if ($username)
     {
       $vars['classid'] = 7;
       
       $userinfo = fetchRow($username, TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
if ($userinfo['classid'] == "6")
{
       if ($userinfo['ID'])
       {
         $update = updateUser($userinfo['ID'], $vars);
	$from = "From: <ban@gcshideout.com>";
	$message = "To: ".$username.",\n\n"
		."You were banned from GC Boards for the following reason:\n"
		.$banreason."\n\n"
		."You Will Be Unbanned: ".$unbandate."\n";
	mail($userinfo['contactemail'], "You Have Been Banned", $message, $from);
         if ($update)
         {
           $output = "$username has been banned.<BR>\n"
                    ."<P>\n"
	."Please make sure to unban the user on the correct date to avoid problems.";
         }
         else
         {
           $reason = "Couldn't ban this user - you'll have to get an administrator to do it.<BR>\n";
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
if ($userinfo['classid'] != "6")
{
       $action = "";
       $sysmsg = "custom";
       $sysmsgcustomcontent = $username." Couldn't Be Banned Because They Are Not In The Member Access Class";
}
     
   }

   if ($action == "unban")
   {
     if ($username)
     {
       $vars['classid'] = 6;
       
       $userinfo = fetchRow($username, TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
if ($userinfo['classid'] == "7")
{
       if ($userinfo['ID'])
       {
         $update = updateUser($userinfo['ID'], $vars);
         if ($update)
         {
           $output = "$username has been un-banned.<BR>\n"
                    ."<P>\n";
         }
         else
         {
           $reason = "Couldn't un-ban this user - you'll have to get an administrator to do it.<BR>\n";
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
if ($userinfo['classid'] != "7")
{
       $action = "";
       $sysmsg = "custom";
       $sysmsgcustomcontent = $username." Isn't Currently Banned";
}
     
   }
   
   if (!$action || $action == "")
   {

     $output = ""
			  ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
			  .inputHidden("action", "unban")
			  ."<SPAN CLASS='InputSection'>Username To UnBan: </SPAN>".inputText("username", $username)."<BR>"
			  .inputSubmit("Un-Ban User")
			  ."</FORM>\n"
			  ."<HR SIZE=2>"
              ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
              .inputHidden("action", "ban")
              ."<SPAN CLASS='InputSection'>Username To Ban: </SPAN>".inputText("username", $username)."<BR>"
	."<SPAN CLASS='InputSection'>UnBan Date: </SPAN>".inputText("unbandate", $unbandate)."<BR>"
	."<TEXTAREA NAME='banreason' COLS='40' ROWS='10'>- - - Reason For Banning - - -</TEXTAREA><BR>"
              .inputSubmit("Ban User")
              ."</FORM>\n\n";
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
}
}
else
{
header("Location: noaccess.php");
}
   $pagecontents = $output;
   include("layout.php");
?>