<?php
   $starttime = microtime();
   include("common.php");
   
   $navigationhead = "User Login";
   
   // If we're locked up at the moment, dont let anyone log in
   if ($NoLogin)
   {
     $action = "";
     $reason = "Logins are not allowed at the moment";
   }
   
   if ($action == "logout")
   {
     if (!$returnurl)
     {
       $returnurl = "login.php";
     }
     closeSession($boardsessionkey, "board");
     Header("Location: ".$returnurl);
   }
   
   // If no value for this exists, use the system's default. This shouldn't happen, though...
   if (!$sessiontime)
   {
     $sessiontime = $SessionTime;
   }

   if ($action == "auth")
   {
     $auth = userAuth($username, $password);
     
     switch ($auth['result'])
     {
       case "valid":
         if ($rememberusername)
         {
           // Blank the current boardlastusername cookies
           sendCookie("boardlastusername", "");
           // Set a cookie that will last for a year with the username in it
           sendExpiringCookie("boardlastusername", $username, 0);
         }
         
         // Stuff specific to this session
         $sessionstartextradata = array("timeout"  => $sessiontime,
                                        "login_ip" => $REMOTE_ADDR);
         $sessionstart = startSession($auth['userid'], "", "board", $sessionstartextradata);
         //var_dump($sessionstart);
         
         if ($sessionstart['status'] == "success")
         {
           // Redirect if the username is the same and a returnurl is supplied
           $redirecturl = "index.php";
           if ($returnurl)
           {
             $redirecturl = $returnurl;
           }

           // Touch the user's last login date
           $lastlogin = setLastLogin($auth['userid'], Date("U"));
           
           Header("Location: ".$redirecturl);
           Exit();
         }
         else
         {
           // Session start didn't work for some reason - what was it?
           if ($sessionstart['reson'] == "existingsession")
           {
             $reason = "You are already logged in!";
           }
           else
           {
             $reason = "Session start didn't work ... panic!<BR>\n";
           }
         }
         break;
         
       case "nologin":
         $reason = $username." is not allowed to log in";
         break;
         
       case "invaliduser":
         $reason = "Unknown username: ".$username;
         break;
         
       case "wrongpass":
         $reason = "Wrong password for ".$username;
         break;
     }
     
     $action = "";
   }
   
   if (!$action)
   {
     if ($userloggedin)
     {
       $output = "You are currently logged in as ".usernameDisplay($userdata['ID'])."<BR>\n"
                ."If this isn't you, please ".makeLink($PHP_SELF."?action=logout", "Logout Now");
     }
     else
     {
       if ($reason)
       {
         $reasonoutput = "<B CLASS='red'>".$reason."</B>\n"
                        ."<P>\n";
       }
       
       // $boardlastusername is the name of the cookie assigned to "remember
       // my username" ... makes it a little easier for some users to log in.
       // If its present and they're not already trying to log in as someone
       // else, set it as the username to display on the form.
       if (($boardlastusername) && (!$username))
       {
         $username = $boardlastusername;
       }
       
       $mandatory = "<SPAN CLASS='red'>•</SPAN>";

       include("elements/login.php");
       $output = $reasonoutput
                ."<TABLE WIDTH=100%>\n"
                ."<TR VALIGN=TOP>\n"
                ."    <TD WIDTH=60%>\n"
                ."        <B>Please enter your username and password</B>\n"
                ."        <FORM ACTION='$PHP_SELF' METHOD=POST NAME='loginnow'>\n"
                ."        ".inputHidden("action", "auth")
                ."        ".inputHidden("olduser", $olduser)
                ."        ".inputHidden("returnurl", $returnurl)
//                ."        ".str_replace("\n", "\n        ", str_replace("BUTTON", inputSubmit("Login Now"), $loginform))
	  .$loginform
                ."        </FORM></TD>\n"
                ."    <TD WIDTH=40%>\n"
                ."        <B>New User?</B><BR>\n"
                ."        &nbsp; &raquo; ".makeLink("register.php", "Sign up now!")."<BR>\n"
                ."        <P>\n"
                ."        <B>Lost your password?</B><BR>\n"
                ."        &nbsp; &raquo; ".makeLink("recoverpass.php", "Get a new one")."</TD></TR>\n"
                ."</TABLE>\n";
     }
   }
   
   $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
            ."<TR><TD CLASS='BoardRowBody'>\n"
            ."        ".str_replace("\n", "\n        ", $output)."</TD></TR>\n"
            ."</TABLE>\n";
   
   $pagecontents = $output;
   include("layout.php");
?>