<?php
   $starttime = microtime();
   $protectedpage = 1;
   include("common.php");

   $navigation[] = array("name" => "Icon Selection",
                         "url"  => $PHP_SELF);
   
   $iconsperpage = 25;
   global $userdata;
   if ($configoptions['useownicons'] || checkAccess("accessvip") || $userdata['chooseicon'])
   {
     if ($action == "saveownicon")
     {
       if (!$iconurl) { $err[] = "No URL was provided"; }
       else
       {
         if (preg_match('/'.$configoptions['disallowedchars'].'/', $iconurl))  
                      { $err[] = "Sorry, that URL contains illegal characters"; }
       }
       
       if ($err)
       {
         $reason = implode("<BR>\n", $err);
         $action = "setownicon";
       }
       else
       {
         $vars['ownicon'] = $iconurl;
         
         $update = updateUser($userdata['ID'], $vars, "no");
         if ($update)
         {
	$vars['chooseicon'] = ($userdata['chooseicon'] - "1");
	$update = updateUser($userdata['ID'], $vars);
           $output = "<TABLE>\n"
                    ."<TR><TD>\n"
                    ."        ".getIcon($userdata['ID'])."</TD>\n"
                    ."    <TD>Your icon has been changed.</TD>\n"
                    ."</TABLE>";
         }
         else
         {
           $reason = "Err, something went wrong";
           $action = "setownicon";
         }
       }
     }
     
     if ($action == "setownicon")
     {
       include("elements/ownicon.php");
       $output = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                .inputHidden("action", "saveownicon")
                .$owniconform
                .inputSubmit("Set Icon")
                ."</FORM>\n";
     }
   }
   
   if ($action == "seticon")
   {
     if ((!$iconid) && (!$override))                        { $err[] = "No icon selected"; }
     if (!checkIconUserPermission($iconid, $userdata['ID']))  { $err[] = "You don't have access to icon ".$iconid; }
     
     if ($err)
     {
       $reason = implode("<BR>\n", $err);
       $action = "";
     }
     else
     {
       $data['iconid']  = intval($iconid);
       $data['ownicon'] = "";
       
       if (!$override)
       {
         $icondata = fetchRow($iconid, TABLE_ICONS);
         $iconimage = getIcon("", array("iconid"=>$iconid, "align"=>"middle"));
         $iconname  = $icondata['iconname'];
       }
       else
       {
         $iconname = "None";
       }
       
       $update = updateUser($userdata['ID'], $data);
       if ($update)
       {
         $navigation[] = array("name" => "Icon Set",
                               "url"  => "");
         $output = $iconimage
                  ."Your icon has been set to <B>".$iconname."</B>.";
       }
       else
       {
         $reason = "Err, something went wrong";
         $action = "";
       }
     }
   }
   
   if (!$action)
   {
     if (!$page)    { $page = 1; }
     if (!$orderby) { $orderby = "iconname"; }
     
     // Make sure the user only sees icons in their access class
     $browseopt['userclassid'] = $userdata['classid'];
     // Standard icon browser options
     $browseopt['groupid']     = $groupid;
     $browseopt['orderby']     = $orderby;
     $browseopt['page']        = $page;
     $browseopt['perpage']     = $iconsperpage;
     
     $icons = listIcons($browseopt);
     
     $centerrow = $icons['navbar'];

     if ($configoptions['useownicons'] || checkAccess("accessvip") || $userdata['chooseicon'])
     {
       $owniconlink = " | <A HREF='$PHP_SELF?action=setownicon'>Use My Own Icon</A>\n";
     }
     $noiconlink = " | <A HREF='$PHP_SELF?action=seticon&override=1'>No Icon</A>\n";
     
     $output    = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                 .inputHidden("action", "")
                 ."View group: \n"
                 .inputIconGroupUser("groupid", $groupid, "edit", "-- Select Group --")
                 .inputSubmit("Browse")
                 .$owniconlink
                 .$noiconlink
                 ."</FORM>\n"
                 ."<SPAN CLASS='InputSection'>Icon Selection</SPAN><BR>\n"
                 ."Select your new icon and press <I>Set My Icon</I>\n"
                 ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                 .inputHidden("action", "seticon")
                 .inputHidden("categoryid", $categoryid)
                 .inputHidden("page", $page)
                 .inputHidden("orderby", $orderby)
                 .$icons['icontable']
                 .inputSubmit("Set My Icon")
                 ."</FORM>\n";
   }
    
   if ($reason)
   {
     $reasonoutput = "<B CLASS='red'>".$reason."</B>\n"
                    ."<P>\n";
   }

   $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
            ."<TR><TD CLASS='BoardRowBody'>\n"
            ."        ".str_replace("\n", "\n        ", $reasonoutput.$output)."</TD></TR>\n"
            ."</TABLE>\n";

   $pagecontents = $output;
   include("layout.php");
?>