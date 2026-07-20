<?php
   $starttime = microtime();
   $protectedpage = 1;
   $functionset   = "admin";
   include("common.php");

   if (checkAccess("accessmoderator"))
   {
     $navigation[] = array("name" => "IP Addresses",
                           "url"  => "$PHP_SELF");
     
     // This page is an administration tool, allowing admins to search IPs in 
     // 2 ways:
     //  - Find all users who have posted from an IP address
     //  - Find all IP addresses a user has posted from
     
     $areaoptions[] = array("name" => "Start a New IP Search",
                            "url"  => $PHP_SELF);
     
     if (($ip) && (checkAccess("accessadmin")))
     {
       $areaoptions[] = array("name" => "Ban ".$ip,
                              "url"  => "admin-ban.php?action=new&ipaddress1=".$ip);
     }
     
     if ($action == "finduserips")
     {
       if ($username)
       {
         $usersearchdata = fetchRow($username, TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
         if ($usersearchdata['ID']) 
         { 
           $userid = $usersearchdata['ID'];
         }
         else
         {
           $reason = "No such user: ".$username;
         }
       }
       if ($userid)
       {
         $userinfo = getUserInformation($userid);
         $navigationhead = "IP Address(es) used by ".$userinfo['displayname'];
         
         $result = findIPAddressFromUser($userid);
         if ($result['resultcount'])
         {
           $output = $result['html'];
         }
         else
         {
           $action = "";
           $reason = "No matches found for ".$ip;
         }
       }
       else
       {
         if (!$reason)
         {
           $reason = "Nothing to search for!";
         }
         $action = "";
       }
     }
  
     if ($action == "findipusers")
     {
       // Figure out what the IP resolves to
       $hostname = gethostbyaddr($ip);
       if ($hostname == $ip)
       {
         $resolve = "$ip does not resolve to a hostname";
       }
       else
       {
         $resolve = "$ip resolves to $hostname";
       }
       $sysmsg = "custom";
       $sysmsgcustomcontent = $resolve;
       
       $result = findIPAddressUse($ip);
       if ($result['resultcount'])
       {
         $output = $result['html'];
       }
       else
       {
         $action = "";
         $reason = "No matches found for ".$ip;
       }
     }
     
     if (!$action)
     {
       include("elements/ipsearch.php");
       $output = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                .inputHidden("action", "findipusers")
                .$ipsearchform
                ."<P>\n"
                .inputSubmit("Find IP Address Use")
                ."</FORM>\n"
                ."<HR SIZE=1>\n"
                ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                .inputHidden("action", "finduserips")
                .$usersearchform
                ."<P>\n"
                .inputSubmit("Find IP Addresses Used")
                ."</FORM>\n";
  
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
     }
   }
   else
   {
     $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
              ."<TR><TD CLASS='BoardRowBody'>\n"
              ."        <B CLASS='red'>You don't have access to this page</B></TD></TR>\n"
              ."</TABLE>\n";
   }
   
   $pagecontents = $output;
   include("layout.php");
?>