<?php
   $starttime = microtime();
   $protectedpage = 1;
   $functionset   = "admin";
   include("common.php");

   $navigation[] = array("name" => "Systemwide IP Bans",
                         "url"  => $PHP_SELF);
   
   $areaoptions[] = array("name" => "Set a New Ban",
                          "url"  => $PHP_SELF."?action=new");
   
   $mandatory = "<SPAN CLASS='red'>•</SPAN>";

   if (checkAccess("accessmanager"))
   {
     if ($action == "delete")
     {
       $bans = getSystemBans();
       for ($i = 0; $i < count($bans['bans']); $i++)
       {
         $checkvar = "ban".$bans['bans'][$i]['ID'];
         
         if ($$checkvar == "del")
         {
           $deleteid[] = $bans['bans'][$i]['ID'];
         }
       }
       
       if (is_array($deleteid))
       {
         $del = removeSystemBans($deleteid);
         if ($del)
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "System IP Bans updated";
         }
       }
       $action = "";
     }
     
     if ($action == "save")
     {
       if (!trim($ipaddress1))  { $err[] = "No IP Address was entered"; }
       
       if (($ipaddress1) && (!isValidIP($ipaddress1)))
                                { $err[] = "The first IP address is invalid"; }
       if (($ipaddress2) && (!isValidIP($ipaddress2)))
                                { $err[] = "The second IP address is invalid"; }
       if ($username)
       {
         $banuserinfo = fetchRow($username, TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
         if (!$banuserinfo['ID']) { $err[] = "The targetted user, <I>".$username."</I>, was not found"; }
       }
       
       if ($err)
       {
         $reason = implode("<BR>\n", $err);
         $action = "new";
       }
       else
       {
         $ban['ip1'] = ip2long($ipaddress1);
         $ban['ip2'] = ip2long($ipaddress1);
         if ($ipaddress2)
         {
           $ban['ip2'] = ip2long($ipaddress2);
         }
         if ($banuserinfo['ID'])
         {
           $ban['userid'] = $banuserinfo['ID'];
         }
         $ban['adminid'] = $userdata['ID'];
         if ($active)
         {
           $ban['active'] = 1;
         }
         
         $setban = newSystemBan($ban);
         if ($setban)
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "Ban inserted (".$setban.")";
         }
         else
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "Ban wasn't inserted...";
         }
         $action = "";
       }
     }
     
     if ($action == "new")
     {
       $navigationhead = "Set a new ban";
       
       $wrapoutput = 1;
       
       include("elements/ip-ban.php");
       $output = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                .inputHidden("action", "save")
                .$ipbanform
                ."<P>\n"
                .inputSubmit("Set IP Ban")
                ."</FORM>\n";
     }
   
     if (!$action)
     {
       $navigationhead = "Current Bans";
       
       // No action, just show the bans
       $bans = getSystemBans();
       if ($bans['bancount'])
       {
         $pageformstart = "<FORM ACTION='".$PHP_SELF."' METHOD=POST>\n"
                         .inputHidden("action", "delete");
         $pageformstop  = "</FORM>\n";
       }
       $output = $bans['html'];
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
