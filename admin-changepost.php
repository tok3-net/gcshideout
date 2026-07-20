<?php
   $starttime = microtime();
   $protectedpage = 1;
   $functionset   = "admin";
   include("common.php");
if (checkAccess("accessmoderator"))
{     
     if ($action == "savepost")
     {
       $navigationhead[] = array("name" => "Change User's Post Count",
								 "url"  => $PHP_SELF);

       if (!$username)     { $err[] = "No username was entered"; }
       {
         $userinfo = fetchRow($username, TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
         if (!$userinfo['ID'])
                           { $err[] = "User '".$username."' doesn't exist"; }
       }
       
       if ($err)
       {
         $reason = implode("<BR>\n", $err);
         $action = "changepost";
       }
       else
       {
         // Assign postcount to data variable
         $data['postcount'] = $postcount;
         
         $update = updateUser($userinfo['ID'], $data);
         if ($update)
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "The posts for <I>".$username."</I> has been changed to <I>".$postcount."</I>.\n";
           $action = "changepost";
         }
         else
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "The postcount for <I>".$username."</I> was not changed.\n";
           $action = "display";
         }
       }
     }
     
     if (!$action || $action == "changepost")
     {
       $navigationhead = "Change User's Post";
       $mandatory = "<SPAN CLASS='red'>•</SPAN>";
       
       include("elements/adminchangepost.php");
       $output = "<TABLE BODER=0 WIDTH=100%><TR><TD CLASS='BoardRowBody' WIDTH=100%>"
				."<SPAN CLASS='InputSection'>Change User's Post</SPAN><BR>\n"
                ."Enter the username and new postcount below.<BR>\n"
                ."<FORM ACTION='admin-changepost.php' METHOD=POST>\n"
                .inputHidden("action", "savepost")
                .$changepostform
                ."<P>\n"
                .inputSubmit("Change Post")
                ."</FORM>\n"
				."</TD></TR></TABLE>";
     }
}
else
{
header("Location: noaccess.php");
}
$pagecontents = $output;
include("layout.php");
?>