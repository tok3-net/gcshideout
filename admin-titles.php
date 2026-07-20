<?php
   $starttime = microtime();
   $protectedpage = 1;
   $functionset   = "admin";
   include("common.php");

   $navigation[] = array("name" => "Titles",
                         "url"  => $PHP_SELF);
   
   $mandatory = "<SPAN CLASS='red'>•</SPAN>";
   
   $allowedfileextensions = array("php", "php3", "html", "txt");
   
   if (checkAccess("accessmoderator"))
   {
     include("elements/usersearch.php");
     $form = "<FORM ACTION='".$PHP_SELF."' METHOD=POST>\n"
            .inputHidden("action", "update")
            .$usersearchform
            .inputSubmit("Edit")
            ."</FORM>\n";
     
     if ($action == "save")
     {
       $data['title'] = $newtitle;
       
       $update = updateUser($userid, $data, "no");
       
       $action = "";
       $sysmsg = "custom";
       $sysmsgcustomcontent = "Title for ".$username." has been updated";
     }
     
     if ($action == "update")
     {
       $username = trim($username);
       
       if (!$username) { $err[] = "No username was entered"; }
       else
       {
         $usersearchdata = fetchRow($username, TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
         if ($usersearchdata['ID']) 
         { 
           $userid = $usersearchdata['ID'];
         }
         else
         {
           $err[] = "No such user: ".$username;
         }

         if ($err)
         {
           $action = "";
           $reason = implode("<BR>\n", $err);
         }
         else
         {
           $userinfo = getUserInformation($userid);
           
           $currenttitle = applyOnlyTextEffects($userinfo['title']);
	$sqltitle = $userinfo['title'];
           
           include("elements/usertitle.php");
           $output = "<FORM ACTION='".$PHP_SELF."' METHOD=POST>\n"
                    .inputHidden("action", "save")
                    .inputHidden("userid", $userid)
                    .inputHidden("username", $userinfo['displayname'])
                    .$titleform
                    .inputSubmit("Update Title")
                    ."</FORM>\n";
         }

       }
       
     }
     
     if (!$action)
     {
       $output = $form;
     }
   }
   else
   {
header("Location: noaccess.php");
   }
   
   if ($reason)
   {
     $reasonoutput = "<B CLASS='red'>".$reason."</B>\n"
                    ."<P>\n";
   }
   
   $output = $reasonoutput.$output;
   
   // ALWAYS wrap output on this page.
   $wrapoutput = 1;
   
   if ($wrapoutput)
   {
     $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
              ."<TR><TD CLASS='BoardRowBody'>\n"
              ."        ".str_replace("\n", "\n        ", $output)."</TD></TR>\n"
              ."</TABLE>\n";
   }       

   $pagecontents = $output;
   include("layout.php");
?>