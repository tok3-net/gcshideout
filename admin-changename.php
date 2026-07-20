<?php
   $starttime = microtime();
   $protectedpage = 1;
   $functionset   = "admin";
   include("common.php");
if (checkAccess("accessmoderator"))
{     
     if ($action == "savename")
     {
       $navigationhead[] = array("name" => "Change Username",
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
         $action = "changename";
       }
       else
       {
         $data['displayname'] = $displayname;
         
        $sql = "UPDATE ".TABLE_USERS." SET displayname='$displayname' WHERE ID='$userinfo[ID]'";
        $exe = mysqli_query($mysql, $sql); 
         if ($exe)
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "<I>".$username."</I>'s username has been changed to <I>".$displayname."</I>.\n";
           $action = "changename";
         }
         else
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "The username for <I>".$username."</I> was not changed.\n";
           $action = "display";
         }
       }
     }
     
     if (!$action || $action == "changename")
     {
       $navigationhead = "Change Username";
       $mandatory = "<SPAN CLASS='red'>•</SPAN>";
       
       include("elements/adminname.php");
       $output = "<TABLE BODER=0 WIDTH=100%><TR><TD CLASS='BoardRowBody' WIDTH=100%>"
				."<SPAN CLASS='InputSection'>Change Username</SPAN><BR>\n"
                ."Enter the username and new username below.<BR>\n"
                ."<FORM ACTION='admin-changename.php' METHOD=POST>\n"
                .inputHidden("action", "savename")
                .$changenameform
                ."<P>\n"
                .inputSubmit("Change Username")
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