<?php
   $starttime = microtime();
   $protectedpage = 1;
   $functionset   = "admin";
   include("common.php");

   if (checkAccess("accessvip"))
   {
     $navigation[] = array("name" => "Administration",
                           "url"  => "$PHP_SELF?action=blank");

if ($action==usernotes)
{
if (checkAccess("accessmoderator"))
{     
       
       if ($step == "removenote")
       {
         $userinformation = fetchRow($userid, TABLE_USERS);
         $username = $userinformation['displayname'];

         $remove = removeAdminNote($noteid);
         if ($remove)
         {
           $step = "viewuser";
           $sysmsg = "custom";
           $sysmsgcustomcontent = "Note ".$noteid." has been removed";
         }
         else
         {
           $step = "newnote";
           $sysmsg = "custom";
           $sysmsgcustomcontent = "Note ".$noteid." couldn't be removed";
         }
       }
       
       if ($step == "savenote")
       {
         $userinformation = fetchRow($userid, TABLE_USERS);
         $username = $userinformation['displayname'];

         $save = addAdminNote($userid, $userdata['ID'], $body);
         if ($save)
         {
           $step = "viewuser";
           $sysmsg = "custom";
           $sysmsgcustomcontent = "Your note has been saved";
         }
         else
         {
           $step = "newnote";
           $sysmsg = "custom";
           $sysmsgcustomcontent = "Your note couldn't be saved";
         }
       }
       
       if ($step == "newnote")
       {
         $userinformation = fetchRow($userid, TABLE_USERS);

         $navigation[] = array("name" => $userinformation['displayname'],
                               "url"  => "$PHP_SELF?action=usernotes&step=viewuser&username=".$userinformation['displayname']);
         $navigationhead = "New Note";
         
         $pageformstart = "<FORM ACTION='".$PHP_SELF."' METHOD=POST>\n"
                         .inputHidden("action", $action)
                         .inputHidden("userid", $userid)
                         .inputHidden("step", "savenote");
         $pageformstop =  "</FORM>";
         $output = "Enter a new note for ".$userinformation['displayname']."<BR>\n"
                  .inputTextArea("body", $body, 45, 5, "", "", "", "linewrapfix")
                  ."<P>\n"
                  .inputSubmit("Save Note");
       }
       
       if ($step == "viewuser")
       {
         $userinformation = fetchRow($username, TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
         $navigationhead = $userinformation['displayname'];
         
         if ($userinformation['ID'])
         {
           $areaoptions[] = array("name" => "Add Note For ".$userinformation['displayname'],
                                  "url"  => $PHP_SELF."?action=usernotes&step=newnote&userid=".$userinformation['ID']);
    
           $notes = fetchAdminNotes($userinformation['ID']);
           $output = $notes['html'];
           $nowrap = 1;
         }
         else
         {
           $step = "";
           $sysmsg = "custom";
           $sysmsgcustomcontent = "Unknown user";
           
         }
       }
       
       if (!$step)
       {
         $output = "<B>User Notes</B><BR>\n"
                  ."Enter the name of a user for whom to view notes.<BR>\n"
                  ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                  .inputHidden("action", $action)
                  .inputHidden("step", "viewuser")
                  .inputText("username", $username)
                  .inputSubmit("View")
                  ."</FORM>\n";
       }
}
     
}



if (checkAccess("accessadmin"))
{
                           
     if ($action == "viewmessage")
     {
       $navigation[] = array("name" => "View Single Message",
                             "url"  => "$PHP_SELF?action=viewmessage");

       if ($messageid)
       {
         $navigationhead = "Message ".$messageid;

         $viewmessage = viewSingleMessage($messageid);
         if ($viewmessage['html'])
         {
           $output = $viewmessage['html'];
           $nowrap = 1;
           $sysmsg = "custom";
           $sysmsgcustomcontent = "Viewing message ".$messageid;
         }
         else
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "Invalid Message ID: $messageid";
           $messageid = "";
         }
       }

       if (!$messageid)
       {
         $navigationhead = "Selection";

         $output = "<B>View Individual Message</B><BR>\n"
                  ."Enter the ID number of a message to view it.<BR>\n"
                  ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                  .inputHidden("action", "viewmessage")
                  .inputText("messageid", "", 5)
                  .inputSubmit("View")
                  ."</FORM>\n";
       }
       
     }
}
if (checkAccess("accessadmin"))
{     
     if ($action == "savepass")
     {
       $navigationhead = "Change User's Password";

       if (!$username)     { $err[] = "No username was entered"; }
       {
         $userinfo = fetchRow($username, TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
         if (!$userinfo['ID'])
                           { $err[] = "User '".$username."' doesn't exist"; }
       }
       if (!$password)     { $err[] = "No password was entered"; }
       
       if ($err)
       {
         $reason = implode("<BR>\n", $err);
         $action = "changepass";
       }
       else
       {
         // Hash the password and store it
         $data['encpassword'] = hashPassword($password, "authenticate");;
         
         $update = updateUser($userinfo['ID'], $data);
         if ($update)
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "The password for <I>".$username."</I> has been changed to <I>".$password."</I>.\n";
           $action = "display";
         }
         else
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "The password for <I>".$username."</I> was not changed.\n";
           $action = "display";
         }
       }
     }
     
     if ($action == "changepass")
     {
       $navigationhead = "Change User's Password";
       $mandatory = "<SPAN CLASS='red'>•</SPAN>";
       
       include("elements/adminchangepass.php");
       $output = "<SPAN CLASS='InputSection'>Change User's Password</SPAN><BR>\n"
                ."Enter the username and new password below.<BR>\n"
                ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                .inputHidden("action", "savepass")
                .$changepassform
                ."<P>\n"
                .inputSubmit("Change Password")
                ."</FORM>\n";
     }
}
if (checkAccess("accessvip"))
{

     if ($action == "viewsessions")
     {
       $navigation[] = array("name" => "Daily System Stats",
                             "url"  => "$PHP_SELF?action=viewsessions&step=1");

       if (!$step) { $step = 1; }
       
       if ($step == 2)
       {
         $unixstart = mkTime(0, 0, 0, $frommonth, $fromday, $fromyear);
         $unixstop  = mkTime(23, 59, 59, $tomonth, $today, $toyear);
         
         $output = viewSessions ($unixstart, $unixstop);
         $nowrap = 1;
       }
       
       if ($step == 1)
       {
         $today = Date("U");
         $yesterday = (Date("U")-3600*24);
         
         $form = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                .inputHidden("action", "viewsessions")
                .inputHidden("step", "2")
                ."View sessions from \n"
                .inputDMYtext("from", Date("d", $yesterday), Date("m", $yesterday), Date("Y", $yesterday))
                ." to \n"
                .inputDMYtext("to", Date("d"), Date("m"), Date("Y"))
                .inputSubmit("Go")
                ."</FORM>\n";
          
          $output .= $form;
       }
     }
}
if (checkAccess("accessmanager"))
{                           
     if ($action == "manageicons")
     {
       $navigation[] = array("name" => "Icon Management",
                             "url"  => "$PHP_SELF?action=manageicons&step=1");

       if ($step == "savenewicon")
       {
         $navigationhead = "Add New Icon";

         //echo "NewIcon is $newicon.<BR>\n";
         if (($newicon) && ($newicon != "none"))
         {
           $source = $newicon;

           $filedata = fileToBase64($source);
           $newicon = newIcon($groupid, $iconname, $newicon_name, $filedata, $newicon_type);
           
           if ($newicon)
           {
             $icondata['iconid'] = $newicon;
             $icondata['align']  = "MIDDLE";
             
             $output = getIcon("", $icondata)
                      ."Your new icon, $newicon_name, has been put into group ".$groupid."<BR>\n"
                      ."<A HREF='$PHP_SELF?action=manageicons&step=2&groupid=".$groupid."'>Click here to see it</A>\n"
                      ."<P><SPAN CLASS='PlainText'>\n"
                      ."&nbsp; &raquo; <A HREF='$PHP_SELF?action=manageicons&step=addicon&groupid=".$groupid."'>Add Another Icon Here</A><BR>\n"
                      ."&nbsp; &raquo; <A HREF='$PHP_SELF?action=manageicons'>Return to Icon Management</A>\n";
           }
           else
           {
             $sysmsg = "custom";
             $sysmsgcustomcontent = "Icon data could not be inserted";
             $step = "addicon";
           }
         }
         else
         {
           $step = "addicon";
         }
       }
       
       if ($step == "addicon")
       {
         $navigationhead = "Add New Icon";

         include("elements/newicon.php");
         $output = "<FORM ACTION='$PHP_SELF' ENCTYPE='multipart/form-data' METHOD=POST>\n"
                  .inputHidden("MAX_FILE_SIZE", 50000)
                  .inputHidden("action", "manageicons")
                  .inputHidden("step", "savenewicon")
                  .inputHidden("groupid", $groupid)
                  .$iconform
                  ."<P>\n"
                  .inputSubmit("Save Icon")
                  ."</FORM>\n";
       }
       
       if ($step == "removegroup")
       {
         $removegroup = removeIconGroup($groupid);
         if ($removegroup)
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "Group ".$groupid." was removed";
         }
         else
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "Group ".$groupid." couldn't be removed";
         }
         $step = "";
       }
       
       if ($step == "creategroup")
       {
         $navigationhead = "Create New Icon Group";

         $creategroup = newIconGroup($groupname, $classid);
         if ($creategroup)
         {
           $sysmsgcustom = "custom";
           $sysmsgcustomcontent = "Your new group, $groupname, has been created.\n";
           $step = 2;
           $groupid = fetchLastInsert($exe);
         }
         else
         {
           $sysmsgcustom = "custom";
           $sysmsgcustomcontent = "Your new icon group was not created.\n";
           $step = "newgroup";
         }
       }
       
       if ($step == "newgroup")
       {
         $navigationhead = "Create New Icon Group";

         include("elements/icongroup.php");
         $output = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                  .inputHidden("action", "manageicons")
                  .inputHidden("step", "creategroup")
                  .$icongroupform
                  ."<P>\n"
                  .inputSubmit("Create Group")
                  ."</FORM>\n";
       }
       
       if ($step == "updategroup")
       {
         $navigationhead = "Change Icon Group";

         $update = updateIconGroup($groupid, $groupname, $classid);
         
         if ($update)
         {
           $sysmsgcustom = "custom";
           $sysmsgcustomcontent = "This group, '".$groupname."', has been updated.<BR>\n";
           $step = 2;
         }
       }
       
       if ($step == "editgroup")
       {
         $navigationhead = "Change Icon Group";

         $icongroupdata = fetchRow($groupid, TABLE_ICON_GROUPS);
         $currentgroupid   = $icongroupdata['ID'];
         $currentgroupname = $icongroupdata['groupname'];
         $currentclassid   = $icongroupdata['classid'];
         
         if (!$reason)
         {
           $groupname = $currentgroupname;
           $classid   = $currentclassid;
         }
         
         include("elements/icongroup.php");
         $output = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                  .inputHidden("action", "manageicons")
                  .inputHidden("step", "updategroup")
                  .inputHidden("groupid", $groupid)
                  .$icongroupform
                  ."<P>\n"
                  .inputSubmit("Update Group")
                  ."</FORM>\n";
       }
       
       if ($step == 3)
       {
         $navigationhead = "Update Icons";

         if (!$iconid)   { $err[] = "No icon was selected"; }
         if (!$activity) { $err[] = "No activity was selected"; }
         
         if ($err)
         {
           $reason = implode("<BR>\n", $err);
           $step = 2;
         }
         else
         {
           // $activity will either contain "del" to delete the icon,
           // or an integer value which is to be the icon's new groupid.
           if ($activity == "del")
           {
             $operation = deleteIcon($iconid);
             $successmsg = "Icon ".$iconid." has been deleted";
           }
           else
           {
             $groupdata = fetchRow($activity, TABLE_ICON_GROUPS);
             
             // Must be a move operation, then
             $operation = changeIconGroup($iconid, $activity);
             $successmsg = "Icon ".$iconid." has been moved to <I>".$groupdata['groupname']."</I>\n";
           }
           
           if ($operation)
           {
             // Operation (whatever it was) was successful
             $sysmsgcustom = "custom";
             $sysmsgcustomcontent = $successmsg;
           }
           else
           {
             $sysmsgcustom = "custom";
             $sysmsgcustomcontent = "Sorry, your requested operation was not carried out.";
           }
           $step = 2;
         }
       }
       
       if ($step == 2)
       {
         $icongroupdata = fetchRow($groupid, TABLE_ICON_GROUPS);

         $navigationhead = "Icons in ".$icongroupdata['groupname'];
        
         if (!$page) { $page = 1; }
        
         $browseopt['showall'] = 1; 
         $browseopt['groupid'] = $groupid;
         $browseopt['orderby'] = "iconname";
         $browseopt['page']    = $page;
         $browseopt['perpage'] = 25;
         $browseopt['linkparameters'] = "action=manageicons&step=2";
         
         $icons = listIcons($browseopt);
         
         $centerrow = $icons['navbar'];
         
         $extraoptions = array("del" => "Delete Icon");
         
         $output = $reasonoutput
                  ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                  .inputHidden("action", "manageicons")
                  .inputHidden("step", 2)
                  ."View group: \n"
                  .inputIconGroup("groupid", $groupid, "edit", "-- Select Group --")
                  .inputSubmit("Browse")
                  ."</FORM>\n"
                  ."<SPAN CLASS='InputSection'>Icon Selection</SPAN><BR>\n"
                  ."Select an icon, operation and press <I>Proceed</I>\n"
                  ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                  .inputHidden("action", "manageicons")
                  .inputHidden("step", 3)
                  .inputHidden("groupid", $groupid)
                  .inputHidden("groupname", $icongroupdata['groupname'])
                  .$icons['icontable']
                  .inputIconGroup("activity", $activity, "edit", "-- Select Option --", "", "Move to '", "' group &nbsp;", $extraoptions)
                  .inputSubmit("Proceed")
                  ."</FORM><SPAN CLASS='PlainText'>\n"
                  ."&nbsp; &raquo; <A HREF='$PHP_SELF?action=manageicons&step=addicon&groupid=".$groupid."'>Add an icon to this group</A>\n";
       }
       
       if (!$step) { $step = 1; }
       
       if ($step == 1)
       {
         $navigationhead = "Icon Groups";

         $icongroups = listIconGroups();
         
         $areaoptions[] = array("name" => "Create a New Icon Group",
                                "url"  => $PHP_SELF."?action=manageicons&step=newgroup");

         $output = $icongroups;

         $nowrap = 1;
       }
     }
}
if (checkAccess("accessmanager"))
{
     
     if ($action == "managestructure")
     {
       $navigation[] = array("name" => "Board Structure",
                             "url"  => "$PHP_SELF?action=managestructure&step=1");

       if (!$step) { $step = 1; }
       
       if ($step == "reallyremove")
       {
         $navigationhead = "Group Removal";
         
         $groupdata = fetchRow($groupid, TABLE_GROUPS);

         $remove = removeGroup($groupid);
         if ($remove)
         {
           $sysmsgcustom = "custom";
           $sysmsgcustomcontent = "The group <I>".$groupdata['groupname']."</I> has been removed.";
         }
         else
         {
           $sysmsgcustom = "custom";
           $sysmsgcustomcontent = "The group <I>".$groupdata['groupname']."</I> could not be removed.";
         }
         $step = 1;
       }
       
       if ($step == "remove")
       {
         $navigationhead = "Group Removal";

         // Remove a group. Confirm that they want to remove it.
         $groupdata = fetchRow($groupid, TABLE_GROUPS);
         
         $output = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                  .inputHidden("action", "managestructure")
                  .inputHidden("step", "reallyremove")
                  .inputHidden("groupid", $groupid)
                  ."<B CLASS='red'>IMPORTANT</B>\n"
                  ."<P>\n"
                  ."Please confirm that you want to delete <B>".$groupdata['groupname']."</B>\n"
                  ."<P>\n"
                  .inputCheckbox("confirm", 1, $confirm)." Confirm removal\n"
                  ."<P>\n"
                  .inputSubmit("Continue")
                  ."</FORM>\n";
       }

       if ($step == "reallyremoveboard")
       {
         $navigationhead = "Board Removal";
         
         $boarddata = fetchRow($boardid, TABLE_BOARDS);

         $remove = removeBoard($boardid);
         if ($remove)
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "The board <I>".$boarddata['boardname']."</I> has been removed.";
         }
         else
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "The board <I>".$boarddata['boardname']."</I> could not be removed.";
         }
         $step = 1;
       }
       
       if ($step == "removeboard")
       {
         $navigationhead = "Board Removal";

         // Remove a board. Confirm that they want to remove it.
         $boarddata = fetchRow($boardid, TABLE_BOARDS);
         
         $output = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                  .inputHidden("action", "managestructure")
                  .inputHidden("step", "reallyremoveboard")
                  .inputHidden("boardid", $boardid)
                  ."<B CLASS='red'>IMPORTANT</B>\n"
                  ."<P>\n"
                  ."Please confirm that you want to delete <B>".$boarddata['boardname']."</B>\n"
                  ."<P>\n"
                  .inputCheckbox("confirm", 1, $confirm)." Confirm removal\n"
                  ."<P>\n"
                  .inputSubmit("Continue")
                  ."</FORM>\n";
       }

       if ($step == "saveboard")
       {
         $navigationhead = "Board Management";

         if (!trim($boardname)) { $err[] = "No board name was entered"; }
         
         if ($err)
         {
           $reason = implode("<BR>\n", $err);
           $step = "editboard";
         }
         else
         {
           if ($boardid)
           {
             $boardstuff = updateBoardData($boardid, $boardname, $boardrank, $boarddescription, $boardgroupid, $boardvippost, $boardprivate);
           }
           else
           {
             $boardstuff = newBoard($groupid, $boardname, $boardrank, $boarddescription, $boardvippost, $boardprivate);
           }
           
           if ($boardstuff)
           {
             $sysmsg = "custom";
             $sysmsgcustomcontent = "Your requested change has been performed";
           }
           else
           {
             $sysmsgcustom = "custom";
             $sysmsgcustomcontent = "Your requested change was not performed";
           }
           $step = 1;
         }
       }
       
       if ($step == "editboard")
       {
         $navigationhead = "Board Details";

         $submitbutton = "Create Board";
         $intro        = "Enter the information below to create a new Board";
         if ($boardid)
         {
           $submitbutton = "Update Board";
           $intro        = "Update the information below to change this Board";
         }
          
         if (($boardid) && (!$err))
         {
           $boarddata = fetchRow($boardid, TABLE_BOARDS);
           $boardname        = $boarddata['boardname'];
           $boardrank        = $boarddata['boardrank'];
           $boarddescription = $boarddata['description'];
           $boardgroupid     = $boarddata['groupid'];
           $boardvippost     = $boarddata['vippost'];
           $boardprivate     = $boarddata['private'];
         }
         
         include("elements/boardedit.php");
         $output = $intro
                  ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                  .inputHidden("action", "managestructure")
                  .inputHidden("step", "saveboard")
                  .inputHidden("boardid", $boardid)
                  .inputHidden("groupid", $groupid)
                  .$boardform
                  .inputSubmit($submitbutton)
                  ."</FORM>\n";
         
       }
       
       if ($step == 3)
       {
         $navigationhead = "Group Management";

         if (!trim($groupname)) { $err[] = "No group name was entered"; }
         
         if ($err)
         {
           $reason = implode("<BR>\n", $err);
           $step = 2;
         }
         else
         {
           if ($groupid)
           {
             $groupstuff = updateGroupData($groupid, $groupname, $grouprank);
           }
           else
           {
             $groupstuff = newGroup($groupname);
           }
           
           if ($groupstuff)
           {
             $sysmsgcustom = "custom";
             $sysmsgcustomcontent = "Your requested change has been performed";
           }
           else
           {
             $sysmsgcustom = "custom";
             $sysmsgcustomcontent = "Your requested change was not performed";
           }
           $step = 1;
         }
       }
       
       if ($step == 2)
       {
         $navigationhead = "Group Details";

         $submitbutton = "Create Group";
         $intro        = "Enter the information below to create a new Group";
         if ($groupid)
         {
           $submitbutton = "Update Group";
           $intro        = "Update the information below to change this Group";
         }
          
         if (($groupid) && (!$err))
         {
           $groupdata = fetchRow($groupid, TABLE_GROUPS);
           $groupname = $groupdata['groupname'];
           $grouprank = $groupdata['grouprank'];
         }
         
         include("elements/groupedit.php");
         $output = $intro
                  ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                  .inputHidden("action", "managestructure")
                  .inputHidden("step", 3)
                  .inputHidden("groupid", $groupid)
                  .$groupform
                  .inputSubmit($submitbutton)
                  ."</FORM>\n";
       }
       
       if ($step == 1)
       {
         $navigationhead = "Board Setup";

         $areaoptions[] = array("name" => "Create a New Group",
                                "url"  => $PHP_SELF."?action=managestructure&step=2");
         
         $groups = listGroups("admin", "all");
         
         $output = $groups;
         $nowrap = 1;
       }
     }
}
if (checkAccess("accessadmin"))
{
     
     if ($action == "manageclasses")
     {
       $navigation[] = array("name" => "Access Management",
                             "url"  => "$PHP_SELF?action=manageclasses&step=1");

       if (!$step) { $step = 1; }
       
       if ($step == "savenewclass")
       {
         $classinfo['accessadmin']      = $accessadmin;
         $classinfo['accessmanager']    = $accessmanager;
         $classinfo['accessmoderator']  = $accessmoderator;
         $classinfo['accessvip']	= $accessvip;
         $classinfo['accessinsider'] = $accessinsider;
         $classinfo['accessread']       = $accessread;
         $classinfo['accesswrite']      = $accesswrite;
         $classinfo['accessdelete']     = $accessdelete;
         $classinfo['accesstimeedit']   = $accesstimeedit;
         $classinfo['accessfulledit']   = $accessfulledit;
         $classinfo['accessnameformat'] = $accessnameformat;
         $classinfo['accesslogin']      = $accesslogin;
         
         $newclassid = newClass($classname);
         if ($newclassid)
         {
           $permissions = newClassPermissions($newclassid, $classinfo);
           
           $sysmsgcustom = "custom";
           $sysmsgcustomcontent = "Your requested Access Class, '".$classname."' has been created";
         }
         else
         {
           $sysmsgcustom = "custom";
           $sysmsgcustomcontent = "Couldn't create a new Access Class";
         }
         $step = 1;
       }
       
       if ($step == "newclass")
       {
         $options['classname']        = $classname;
         $options['accessadmin']      = $accessadmin;
		 $options['accessmanager']    = $accessmanager;
         $options['accessmoderator']  = $accessmoderator;
		 $options['accessvip']		= $accessvip;
         $options['accessinsider'] = $accessinsider;
         $options['accessread']       = $accessread;
         $options['accesswrite']      = $accesswrite;
         $options['accessdelete']     = $accessdelete;
         $options['accesstimeedit']   = $accesstimeedit;
         $options['accessfulledit']   = $accessfulledit;
         $options['accessnameformat'] = $accessnameformat;
         $options['accesslogin']      = $accesslogin;
         $classes = listClasses($options, "blankrow");

         $output = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                  .inputHidden("action", "manageclasses")
                  .inputHidden("step", "savenewclass")
                  .inputHidden("permissionrowid", $classes['permissionrowid'])
                  .inputHidden("currentadmin", $classes['currentadmin'])
                  .inputHidden("currentname", $classes['classname'])
                  .$classes['classlist']
                  .$confirmrow
                  .inputSubmit("Submit Changes")
                  ."</FORM>\n";
       }

       if ($step == 3)
       {
         $navigationhead = "Update Class Permissions";
         
         if ((intval($currentadmin) != intval($accessadmin)) && (!$confirm))
         {
           $err[] = "This change affects Admin access - please confirm it.";
           
           $options['overridevalues'] = array("ID"               => $classid,
                                            "classname"        => $classname,
                                            "permissionrowid"  => $permissionrowid,
                                            "accessadmin"      => $accessadmin,
			"accessmanager"    => $accessmanager,
                                            "accessmoderator"  => $accessmoderator,
			"accessvip"		   => $accessvip,
			  "accessinsider" => $accessinsider,
                                            "accessread"       => $accessread,
                                            "accesswrite"      => $accesswrite,
                                            "accessdelete"     => $accessdelete,
                                            "accesstimeedit"   => $accesstimeedit,
                                            "accessfulledit"   => $accessfulledit,
                                            "accessnameformat" => $accessnameformat,
                                            "accesslogin" => $accesslogin);
           $confirmrow = "<BR>".inputCheckbox("confirm", 1)." <SPAN CLASS='BoardRowBody'>Confirm this change</SPAN><BR><BR>";
         }
         if (!trim($classname))
         { $err[] = "No class name was entered"; }
         
         if ($err)
         {
           $reason = implode("<BR>\n", $err);
           $step = 2;
         }
         else
         {
           // Put the name format settings in here, too
           $newsettings['hlcolour']       = $hlcolour;
           $newsettings['txtcolour']      = $txtcolour;
           $newsettings['stylebold']      = $bold;
           $newsettings['styleitalic']    = $italic;
           
           $newsettings['styleunderline']     = $underline;
           $newsettings['styleoverline']      = $overline;
           $newsettings['stylestrikethrough'] = $strikethrough;
           $newsettings['bottomborder'] = $bottomborder;
           $newsettings['topborder']    = $topborder;
           $newsettings['leftborder']   = $leftborder;
           $newsettings['rightborder']  = $rightborder;
           $newsettings['bordercolour'] = $bordercolour;
           
           if ($hlcolour || $txtcolour || $bold || $italic || $underline || $overline || $strikethrough || $bottomborder || $topborder || $leftborder || $rightborder )
           {
             $nameformat = serialize($newsettings);
           }
           
           $updateclass = updateClass($classid, $classname, $nameformat);
  
           $vars['accessadmin']      = $accessadmin;
	$vars['accessmanager']    = $accessmanager;
           $vars['accessmoderator']  = $accessmoderator;
	$vars['accessvip']		   = $accessvip;
           $vars['accessinsider'] = $accessinsider;
           $vars['accessread']       = $accessread;
           $vars['accesswrite']      = $accesswrite;
           $vars['accessdelete']     = $accessdelete;
           $vars['accesstimeedit']   = $accesstimeedit;
           $vars['accessfulledit']   = $accessfulledit;
           $vars['accessnameformat'] = $accessnameformat;
           $vars['accesslogin']      = $accesslogin;
           
           $changeperm = updateClassPermissions($permissionrowid, $vars);
           
           $sysmsgcustom = "custom";
           $sysmsgcustomcontent = "Access Levels have been updated for <B>$classname</B>.\n";
           $step = 1;
         }
       }
       
       if ($step == 2)
       {
         $navigationhead = "Change Class Permissions";
  
         $options['classid']       = $classid;
         $options['showlinks']     = 0;
         $options['checkboxes']    = 1;
         $options['editclassname'] = 1;
         $classes = listClasses($options);
         
         $formatsettings = unserialize($classes['nameformat']);
         //print_r($formatsettings);
         //echo "<BR>\n";
         $hlcolour  = $formatsettings['hlcolour'];
         $txtcolour = $formatsettings['txtcolour'];
         $bold      = $formatsettings['stylebold'];
         $italic    = $formatsettings['styleitalic'];
         // Legacy
         $bottomborder  = $formatsettings['styleunderline'];
         $topborder     = $formatsettings['styleoverline'];
         $strikethrough = $formatsettings['stylestrikethrough'];
         // Border switches
         $bottomborder = $formatsettings['bottomborder'];
         $topborder    = $formatsettings['topborder'];
         $leftborder   = $formatsettings['leftborder'];
         $rightborder  = $formatsettings['rightborder'];
         $bordercolour = $formatsettings['bordercolour'];

         include("elements/nameformat.php");
         $pageformstart = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                         .inputHidden("action", "manageclasses")
                         .inputHidden("classid", $classid)
                         .inputHidden("step", 3)
                         .inputHidden("permissionrowid", $classes['permissionrowid'])
                         .inputHidden("currentadmin", $classes['currentadmin'])
                         .inputHidden("currentname", $classes['classname']);
         $pageformstop = "</FORM>\n";
         $output = $classes['classlist']
                  .$confirmrow
                  ."<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
                  ."<TR><TD COLSPAN=3 CLASS='BoardColumn'>Default Name Format</TD></TR>\n"
                  ."<TR><TD COLSPAN=3 CLASS='BoardRowBody'>\n"
                  .$nameformattable."</TD></TR>\n"
                  ."<TR><TD COLSPAN=3 CLASS='BoardRowHeading' ALIGN=RIGHT>\n"
                  .inputSubmit("Update Access Class")."</TD></TR>\n"
                  ."</TABLE>\n";
         
         $nowrap = 1;
       }
       
       if ($step == 1)
       {
         $navigationhead = "List Available Classes";
  
         // Show a list of available classes
         $options['linkparams'] = "?action=manageclasses&step=2";
         $options['showlinks']  = 1;
         $classes = listClasses($options);
         
         $areaoptions[] = array("name" => "Create a New Access Class",
                                "url"  => $PHP_SELF."?action=manageclasses&step=newclass");

         $output = $classes['classlist'];
         $nowrap = 1;
       }
     }
}
if (checkAccess("accessadmin"))
{
     
     if ($action == "manageusers")
     {
       $navigation[] = array("name" => "User Management",
                             "url"  => "$PHP_SELF?action=manageusers&step=1");

       if (!$step) { $step = 1; }
       
       if ($step == 3)
       {
         $navigationhead = "Change User Access";

         // Figure out the new classid for these users
         $classid = str_replace("class", "", $change);
  
         for ($i = 1; $i <= $checkboxcount; $i++ )
         {
           $var = "user".$i;
           
           if ($$var)
           {
             $updateuserid = $$var;
             
             // Fetch the information for the class we're putting this user
             // in - we need this so we can copy the nameformat element over
             $classdata = fetchRow($classid, TABLE_ACCESS_CLASS);
             
             $data['classid'] = $classid;
             $data['displayformat'] = $classdata['nameformat']; // The new access class' default format
             
             $update = updateUser($updateuserid, $data);
           }
         }
         
         $sysmsgcustom = "custom";
         $sysmsgcustomcontent = "Your requested changes have been made";
         $step = 1;
       }
       
       if ($step == 2)
       {
         $navigationhead = "User Search Results";

         if ($classid)
         { 
           $options['classid'] = $classid;
         }
         
         if (!is_array($options))
         {
           if (!trim($searchuser)) { $err[] = "No username to match!"; }
         }
         
         if ($err)
         {
           $reason = implode("<BR>\n", $err);
         }
         else
         {
           $searchresults = listUsers($searchuser, "admin", $options);
           
           if ($$searchresults['resultcnt']) 
           {
             $sysmsgcustom = "custom";
             $sysmsgcustomcontent = "No users were found"; 
             $step = 1;
           }
           else
           {
             $nowrap = 1;
             $searchresults['usertable'] = str_replace("BUTTON", inputSubmit("Modify Users"), $searchresults['usertable']);
             $pageformstart = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n" 
                             .inputHidden("action", "manageusers")
                             .inputHidden("checkboxcount", $searchresults['checkboxcount'])
                             .inputHidden("step", 3);
             $output = $searchresults['usertable'];
             $pageformstop = "</FORM>\n";
           }
         }
       }
       
       if ($step == 1)
       {
         $navigationhead = "User Search";
         
         $usercount = countUsers();
         
         for ($i = 0; $i < count($usercount); $i++ )
         {
           $userinforow .= "<TR><TD><A HREF='$PHP_SELF?action=manageusers&step=2&classid=".$usercount[$i]['classid']."'>".$usercount[$i]['classname']."</A></TD>\n"
                          ."    <TD>".$usercount[$i]['usercount']."</TD></TR>\n";
           $totalusercount = $totalusercount + $usercount[$i]['usercount'];
         }
         $userinfotable = "<TABLE>\n"
                         .$userinforow
                         ."<TR><TD><B>Total</B></TD>\n"
                         ."    <TD><B>".$totalusercount."</B></TD></TR>\n"
                         ."</TABLE>\n";
         
         include("elements/listusers.php");
         $output = "<TABLE WIDTH=100%>\n"
                  ."<TR VALIGN=TOP>\n"
                  ."    <TD WIDTH=75%>\n"
                  ."        <FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                  ."        ".inputHidden("action", "manageusers")
                  ."        ".inputHidden("step", 2)
                  ."        ".str_replace("\n", "        ", $searchform)
                  ."        ".inputSubmit("Search")
                  ."        </FORM></TD>\n"
                  ."    <TD WIDTH=25%>\n"
                  .$userinfotable
                  ."        </TD></TR>\n"
                  ."</TABLE>\n";
         
       }
     }
}
     
     if ($action == "blank")
     {
       $output = "Administration options are shown to the left.<BR><BR>"
				."You can access whatever option are under your access class name heading and below.<br><br>"
				."ex. <BR>If you are a moderator, you can access everything under Moderator and VIP because VIP is "
				."below Moderator.";
     }
     
     if ($action == "menu")
     {
       $navigationhead = "Administration";
       
       // Define options
	   $pageoptadmin[] = array("name" => "Add User",
						  	   "url"  => "admin-adduser.php",
						  	   "desc" => "Adds a user with a password you set.\n");
       $pageoptadmin[] = array("name" => "Change User's Password",
                         	   "url"  => $PHP_SELF."?action=changepass",
                       		   "desc" => "Change the password on a user's account.\n");
		$pageoptadmin[] = array("name" => "Edit / View A File",
				        "url"  => "admin-editfile.php",
				        "desc" => "Allows an administrator to view or edit files in the DiscoBoard directory.\n");
       $pageoptadmin[] = array("name" => "Manage Access Classes",
                          	   "url"  => $PHP_SELF."?action=manageclasses",
                         	   "desc" => "Change the permissions assigned to access classes or create new ones.\n");
       $pageoptadmin[] = array("name" => "Manage Users",
                          	   "url"  => $PHP_SELF."?action=manageusers",
                         	   "desc" => "Assign different access classes to board users.\n");
       $pageoptadmin[] = array("name" => "View A Message",
                         	   "url"  => $PHP_SELF."?action=viewmessage",
                          	   "desc" => "View any message on the board by its unique ID.\n");

       $pageoptmanager[] = array("name" => "IP Address Bans",
                         	     "url"  => "admin-ban.php",
                           	     "desc" => "Administer the system-wide IP bans\n");
       $pageoptmanager[] = array("name" => "Manage Board Structure",
                          	     "url"  => $PHP_SELF."?action=managestructure",
                         	     "desc" => "Add, remove or edit boards and the groups they're presented in.\n");
       $pageoptmanager[] = array("name" => "Manage Icons",
                         	     "url"  => $PHP_SELF."?action=manageicons",
                          	     "desc" => "Add, remove or edit icons and associated groups.\n");

       $pageoptmod[] = array("name" => "Assign Titles",
                          	 "url"  => "admin-titles.php",
                          	 "desc" => "Set or Change a user's Title field\n");
	   $pageoptmod[] = array("name" => "Ban/Un-Ban A User",
							 "url"  => "admin-banuser.php",
							 "desc" => "Puts the specified username into the ban class for now.\n");
	   $pageoptmod[] = array("name" => "Change User's Username",
				"url" => "admin-changename.php",
				"desc" => "Allows you to change a user's username.\n");
	   $pageoptmod[] = array("name" => "Change Users Post Count",
							 "url"  => "admin-changepost.php",
							 "desc" => "Changes a users post count.\n");
global $AllowShop;
if ($AllowShop)
{
       $pageoptmod[] = array("name" => "Edit Users Money",
			"url" => "admin-changedollar.php",
			"desc" => "Changes a users amount of money.\n");
}
       $pageoptmod[] = array("name" => "IP Address Search",
                          	 "url"  => "ip.php",
                          	 "desc" => "Find users by IP address or IPs used by a user\n");
       $pageoptmod[] = array("name" => "User Notes",
                          	 "url"  => $PHP_SELF."?action=usernotes",
                         	 "desc" => "View admin-only notes about any board user\n");

	   $pageoptvip[] = array("name" => "Change Your Own Title",
						  	 "url"  => "own-title.php?functionset=admin",
						 	 "desc" => "Using this you can change your own title.  And ONLY your own title.\n");
       $pageoptvip[] = array("name" => "Daily System Stats",
                         	 "url"  => $PHP_SELF."?action=viewsessions",
                             "desc" => "Check which users have accessed the system.\n");
       $pageoptvip[] = array("name" => "User Name Style",
                         	 "url"  => "options.php?action=nameformat&functionset=admin",
                             "desc" => "Change your user name colors and style\n");

//----------------------------------------------------       
       // Show a menu of options availabale to Admins

       
       // New style
       for ($i = 0; $i < count($pageoptadmin); $i++ )
       {
         $menurowadmin .= "<TR><TD>".makeLink($pageoptadmin[$i]['url'], $pageoptadmin[$i]['name'], "AdminMenuLink", array("target" => "main"))."</TD></TR>\n";
       }
//----------------------------------------------------
       // Show a menu of options availabale to Managers

       
       // New style
       for ($i = 0; $i < count($pageoptmanager); $i++ )
       {
         $menurowmanager .= "<TR><TD>".makeLink($pageoptmanager[$i]['url'], $pageoptmanager[$i]['name'], "AdminMenuLink", array("target" => "main"))."</TD></TR>\n";
       }
//----------------------------------------------------
       // Show a menu of options availabale to Mods

       
       // New style
       for ($i = 0; $i < count($pageoptmod); $i++ )
       {
         $menurowmod .= "<TR><TD>".makeLink($pageoptmod[$i]['url'], $pageoptmod[$i]['name'], "AdminMenuLink", array("target" => "main"))."</TD></TR>\n";
       }
//----------------------------------------------------
       // Show a menu of options availabale to VIP's

       
       // New style
       for ($i = 0; $i < count($pageoptvip); $i++ )
       {
         $menurowvip .= "<TR><TD>".makeLink($pageoptvip[$i]['url'], $pageoptvip[$i]['name'], "AdminMenuLink", array("target" => "main"))."</TD></TR>\n";
       }
//----------------------------------------------------
       // [close] link doesn't work ... yet
       $output = ""
                ."<SCRIPT LANGUAGE='JavaScript'>\n"
                ."function closeAdminFrame() { \n"
                ."parent.location = parent.frames['main'].location; \n"
                ."return false; \n"
                ."} \n"
                ."</SCRIPT>\n"
                ."<SPAN CLASS='statistictext'><B>Administrator</B> "
                ."</SPAN><BR>\n"
                ."<TABLE CELLSPACING=4>\n"
                .$menurowadmin
				."</TABLE>"
                ."<SPAN CLASS='statistictext'><B>Manager</B> "
                ."</SPAN><BR>\n"
                ."<TABLE CELLSPACING=4>\n"
                .$menurowmanager
				."</TABLE>"
                ."<SPAN CLASS='statistictext'><B>Moderator</B> "
                ."</SPAN><BR>\n"
				."<TABLE CELLSPACING=4>\n"
                .$menurowmod
                ."</TABLE>"
                ."<SPAN CLASS='statistictext'><B>VIP</B> "
                ."</SPAN><BR>\n"
				."<TABLE CELLSPACING=4>\n"
                .$menurowvip
                ."</TABLE><BR>\n"
                ."<A HREF='#' CLASS='statistictext' onClick='return closeAdminFrame();'>[close frame]</A>";
       $ThemeName = "plain";
       $bodycolor = "#666666";
       $marginx = 3;
       $marginy = 3;
       $nowrap = 1;
     }
     
     if (!$action)
     {
       $menusrc = $PHP_SELF."?action=menu";
       $mainsrc = $PHP_SELF."?action=blank";
       include("elements/frameset.php");
       $output = $frameset;
       $ThemeName = "frameset";
       $nowrap = 1;
     }
   }
   else
   {
	header("Location: noaccess.php");
   }

   if ($reason)
   {
     $reasonoutput = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0><TR><TD CLASS='BoardRowBody'>\n"
                    ."<B CLASS='red'>".$reason."</B>\n"
                    ."</TD></TR></TABLE><BR>\n";
   }
   $output = $reasonoutput
            .$output;
   
   if (!$nowrap)
   {
     $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
              ."<TR><TD CLASS='BoardRowBody'>\n"
              ."        ".str_replace("\n", "\n        ", $output)."</TD></TR>\n"
              ."</TABLE>\n";
   }       
   $pagecontents = $output;
   include("layout.php");
?>
