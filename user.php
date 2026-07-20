<?php
   $starttime = microtime();
   include("common.php");

   if ($action == "save")
   {
     $navigationhead = "Edit Your Profile";

     // Password is now system-generated
     //if (!$password1)    { $err[] = "No password was entered"; }
     //if (!$password2)    { $err[] = "No verification password was entered"; }
     //if (($password1) && ($password2))
     //{
     //  if ($password1 != $password2)      
     //                    { $err[] = "Passwords don't match"; }
     //}
     if (!$fname)        { $err[] = "No first name was entered"; }
     if (!$sname)        { $err[] = "No last name was entered"; }
     if (!$contactemail) { $err[] = "No email address was entered"; }
     else
     {
       $validemail = validEmail($contactemail);
       if ($validemail == "invalid-form")
                         { $err[] = "Your email address is malformed"; }
       if ($validemail == "invalid-mx")
                         { $err[] = "Your email address cannot be reached"; }
     }
     if (!$birthdayyear) { $err[] = "No birthday year was entered"; }
     else
     {
       if (!checkdate($birthdaymonth, $birthdayday, $birthdayyear))
                         { $err[] = "The birthday entered was invalid"; }
     }
     if (preg_match('/'.$configoptions['disallowedchars'].'/', $picurl))
                         { $err[] = "Picture URL contains invalid characters"; }
     if (preg_match('/'.$configoptions['disallowedchars'].'/', $wwwurl))
                         { $err[] = "Website URL contains invalid characters"; }

     if ($err)
     {
       // Errors were encountered, stop...
       $reason = implode("<BR>\n", $err);
       $action = "edit";
     }
     else
     {
       $data['fname']        = $fname;
       $data['sname']        = $sname;
       $data['dateofbirth']  = mkTime(0, 0, 0, $birthdaymonth, $birthdayday, $birthdayyear);
       $data['gender']       = $gender;
	   $data['profiletitle'] = $profiletitle;
       $data['country']      = $country;
       $data['contactemail'] = $contactemail;
       $data['contacticq']   = $contacticq;
       $data['contactmsn']   = $contactmsn;
       $data['contactyahoo'] = $contactyahoo;
       $data['contactaim']   = $contactaim;
       $data['company']      = $company;
       $data['jobtitle']     = $jobtitle;
       $data['picurl']       = $picurl;
       $data['wwwurl']       = $wwwurl;
       $data['bio']          = $bio;
       $data['sig1']         = $sig1;
       $data['sig2']         = $sig2;
       $data['sig3']         = $sig3;
       $data['sig4']         = $sig4;
       $data['sig5']         = $sig5;
       
       $update = updateUser($userdata['ID'], $data);
       if ($update)
       {
         $output = "Your profile has been updated.\n";
         $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
                  ."<TR><TD CLASS='BoardRowBody'>\n"
                  ."        ".str_replace("\n", "\n        ", $output)."</TD></TR>\n"
                  ."</TABLE>\n";
       }
       else
       {
         $reason = "Err, something went wrong";
         $action = "edit";
       }
     }
   }
   
   if ($action == "edit")
   {
     $navigationhead = "Edit Your Profile";

     // The form uses $userinfo so as to not step on other users' arrays
     $userinfo = $userdata;
     
     $mandatory = "<SPAN CLASS='red'>•</SPAN>";

     include("elements/profileedit.php");
     $output = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
              .inputHidden("action", "save")
              .$profileeditform
              .inputSubmit("Save Profile")
              ."</FORM>\n";

     if ($reason)
     {
       $reasonoutput = "<B CLASS='red'>".$reason."</B>\n"
                      ."<P>\n";
     }

     $output = $reasonoutput
              ."<P>\n"
              .$output;
              
     $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
              ."<TR><TD CLASS='BoardRowBody'>\n"
              ."        ".str_replace("\n", "\n        ", $output)."</TD></TR>\n"
              ."</TABLE>\n";
   }
   
   if (!$action)
   {
     // Fetch basic info from the user table
     $userinformation = fetchRow($user, TABLE_USERS, "ID", "", "dontcareifblank");
     
     $areaoptions[] = array("name" => "Send this user a Private Message",
                            "url"  => "private.php?action=send&recipient=".$userinformation['displayname']);
     $areaoptions[] = array("name" => "Add to Watched User List",
                            "url"  => "watch.php?action=adduser&userid=".$userinformation['ID']."&returnurl=".urlencode($PHP_SELF.$querystring));

     if (!$userinformation['ID'])
     {
       $reason = "Unknown user!";
       $reasonoutput = "<B CLASS='red'>".$reason."</B>\n"
                      ."<P>\n";
       $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
                ."<TR><TD CLASS='BoardRowBody'>\n"
                ."        ".str_replace("\n", "\n        ", $reasonoutput)."</TD></TR>\n"
                ."</TABLE>\n";
       $navigationhead = "Unknown Profile";
     }
     else
     {
       $navigationhead = "Profile for ".$userinformation['displayname'];

       // Fetch the user's full profile data
       $userinfo = getUserInformation($userinformation['ID'], "users");
       
       // Get the Watched User List for this user ID
       $watcheduserids = getWatchedUsers($userinformation['ID']);
       $watchedusers = formatWatchedUsers($watcheduserids);
       $watcheduserlist = $watchedusers['commadelimited'];
       
       // See how many people's WUL this user ID features in
       $watchinguserdata = usersWatching($userinformation['ID']);
       $watchinguserids = $watchinguserdata['watchinguserids'];
       //print_r($watchinguserids);
       $watchingusersoutput = intval($watchinguserdata['watchingcount'])." user(s)";
       // If the user is looking at their own profile or they're a moderator, show them the actual users
       if ( (($userinformation['ID'] == $userdata['ID']) || checkAccess("accessmoderator")) &&
            ($watchinguserdata['watchingcount'] > 0) )
       {
         //print_r($watchinguserids);
         $watchingusers = formatWatchedUsers($watchinguserids);
         $watchingusersoutput .= ":<BR>\n"
                                .$watchingusers['commadelimited'];
       }
       
       if ($userinfo['contacticq'])
       {
         $icqonline = "<IMG SRC='http://www.gcshideout.com/boards/gfx/icq.png'>&nbsp;";
       }
       if ($userinfo['contactaim'])
       {
         $aimonline = "<IMG SRC='http://www.gcshideout.com/boards/gfx/aim.png'>&nbsp;";
       }
       if ($userinfo['contactmsn'])
       {
         $msnonline = "<IMG SRC='http://www.gcshideout.com/boards/gfx/msn.png'>&nbsp;";
         $msnpre = "<A HREF='http://profiles.msn.com/".$userinfo['contactmsn']."' TARGET='_new'>";
         $msnpost = "</A>";
       }
       if ($userinfo['contactyahoo'])
       {
         $yahooonline = "<IMG SRC='http://opi.yahoo.com/online?u=".$userinfo['contactyahoo']."&m=g&t=0'>&nbsp;";
       }
       
       $classname = fetchClassName($userinfo['classid']);
       if (!in_array($classname, $HiddenClassNames))
       {
         $classnamedisplay = "(".$classname.")";
       }

	if ($AllowSigMarkup)
	{
	$sig1 = bodyText($userinfo['sig1']);
	$sig2 = bodyText($userinfo['sig2']);
	$sig3 = bodyText($userinfo['sig3']);
	$sig4 = bodyText($userinfo['sig4']);
	$sig5 = bodyText($userinfo['sig5']);
	}
	else
	{
	$sig1 = $userinfo['sig1'];
	$sig2 = $userinfo['sig2'];
	$sig3 = $userinfo['sig3'];
	$sig4 = $userinfo['sig4'];
	$sig5 = $userinfo['sig5'];
	}
       
       if (checkAccess("accessmoderator"))
       {
         include("elements/profileshowfull.php");
       }
       else
       {
         include("elements/profileshowpublic.php");
       }
       $output = $profiledisplay;
     }
   }

   if ($userloggedin)
   {
     $areaoptions[] = array("name" => "View Your Profile",
                            "url"  => $PHP_SELF."?user=".$userdata['ID']);
     $areaoptions[] = array("name" => "Edit Your Profile",
                            "url"  => $PHP_SELF."?action=edit");
   }

   $pagecontents = $output;
   include("layout.php");
?>