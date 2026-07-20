<?php
   $starttime = microtime();
   $protectedpage = 1;
   $functionset   = "admin";
   include("common.php");
if (checkAccess("accessmoderator"))
{     
     if ($action == "savedollar")
     {
       $navigationhead[] = array("name" => "Change User's Amount Of Money",
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
         $action = "changedollar";
       }
       else
       {
         // Assign postcount to data variable
         $data['dollars'] = $dollars;
         
         $update = updateUser($userinfo['ID'], $data);
         if ($update)
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = $username." Has $".$dollars." Now.\n";
           $action = "changedollar";
         }
         else
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "The postcount for <I>".$username."</I> was not changed.\n";
           $action = "display";
         }
       }
     }
     
     if (!$action || $action == "changedollar")
     {
       $navigationhead = "Change User's Amount Of Money";
       $mandatory = "<SPAN CLASS='red'>•</SPAN>";
       
       include("elements/adminchangedollar.php");
       $output = "<TABLE BODER=0 WIDTH=100%><TR><TD CLASS='BoardRowBody' WIDTH=100%>"
				."<SPAN CLASS='InputSection'>Change User's Amount Of Money</SPAN><BR>\n"
                ."Enter the username and new postcount below.<BR>\n"
                ."<FORM ACTION='admin-changedollar.php' METHOD=POST>\n"
                .inputHidden("action", "savedollar")
                .$changedollarform
                ."<P>\n"
                .inputSubmit("Change Amount Of Money")
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