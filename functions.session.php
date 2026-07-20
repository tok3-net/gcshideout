<?php
   define("SESSION_FUNCTIONS_AVAILABLE", 1);

   Function hashPassword($password, $salt)
   {
     return crypt($password, $salt);
   }
   
   Function userAuth($username, $password)
   {
     $authinfo = fetchRow($username, TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
     //print_r($authinfo);

     $encpassword = hashPassword($password, "authenticate");
     
     if (!$authinfo['ID'])
     {
       $result = "invaliduser";
     }
     elseif ($authinfo['encpassword'] != $encpassword)
     {
       //echo "You gave me $encpassword, I want $authinfo[encpassword]<BR>\n";
       $result = "wrongpass";
     }
     elseif ($authinfo['encpassword'] == $encpassword)
     {
       $classdata = getUserInformation($authinfo['ID']);
       
       if ($classdata['accesslogin'])
       {
         $result = "valid";
         $userid = $authinfo['ID'];
       }
       else
       {
         $result = "nologin";
       }
     }
     
     $return = array("result" => $result,
                     "userid" => $userid);
     return $return;
   }

   // ===============================================================================

   Function closeSession($magickey, $sessiontype)
   {
     global $mysql;
     // Session cleanup. We don't want passwords lying around.
     if ($sessiontype == "board")
     {
       $table = TABLE_BOARD_SESSIONS;
     }
     $session = fetchRow($magickey, $table, "magickey", "idfieldistext", "dontcareifblank");
     
     // Test for a valid session by looking at $session['ID']
     if ($session['ID'])
     {
       $deletepasswordsql = "UPDATE ".$table
                           ."   SET password = '-DELETED-', "
                           ."       expirytime = ".Date("U")." "
                           ."WHERE ID = $session[ID] ";
       if ($deletepasswordexe = runQuery($deletepasswordsql))
       {
         $return['status'] = "success";
         // Should delete the cookie from their browser
         sendCookie($sessiontype."sessionkey", "", 0);
       }
       else
       {
         $return['status'] = "fail";
         $return['reason'] = mysqli_error($mysql);
       }
     }
     else
     {
       $return['status'] = "fail";
       $return['reason'] = "sessionnotfound";
     }
     return $return;
   }
      
   Function pullSession($magickey, $sessiontype)
   {
     if ($sessiontype == "board")
     {
       $table = TABLE_BOARD_SESSIONS;
     }
     $session = fetchRow($magickey, $table, "magickey", "idfieldistext", "dontcareifblank");
     //var_dump($session);
     return $session;
   }
      
   Function refreshSession($magickey, $sessiontype)
   {
     global $mysql;
     // This function is designed to refresh a session, ie, update its
     // expiry time to $preregsessionexpiry seconds into the future. It
     // will check to see if the session has expired and return as such.
     global $sessiontimeout;
     
     if ($sessiontype == "board")
     {
       $table = TABLE_BOARD_SESSIONS;
     }

     //send value we want to look for, tablename, fieldname corresponding to the value, is field text?, if blank then abort or not
     $session = fetchRow($magickey, $table, "magickey", "idfieldistext", "dontcareifblank");

     if ($sessiontype == "board")
     {
       $sessionexpirytime = (intval($session['session_minutes']) * 60); // value is mins, we want seconds
       $extraupdates = ", lastactivity = ".Date("U");
     }
     
     //if fetchRow has worked then it will return an array of the data (in this case, an ID)     
     // Test for a valid session by looking at $session['ID']
     if ($session['ID'])
     {
       $now = Date("U");
       //echo "now = $now<br>";
       if ($now > $session['expirytime'])
       {
         // Session has expired
         $return['result'] = "fail";
         $return['reason'] = "expired";
       }
       else
       {
         $updatesql = "UPDATE ".$table
                     ."   SET expirytime = ".intval($now+$sessionexpirytime)
                     .$extraupdates
                     ." WHERE ID = ".$session['ID'];
         //echo "updatesql = $updatesql<br>";
         if ($updateexe = runQuery($updatesql))
         {
           // We've updated the session in the database. We don't need to update
           // the cookie, though. (because cookie doesn't expire until browser is closed)
           $return['result'] = "success";
         }
         else
         {
           $return['result'] = "fail";
           $return['reason'] = "db";
           $return['detail'] = mysqli_error($mysql);
           //echo mysqli_error($mysql);
         }
       }
     }
     else
     {
       $return['result'] = "fail";
       $return['reason'] = "nosession";
     }
   
     $return['username'] = $session['username'];
     return $return;
   }
   
   Function startSession($username, $password, $sessiontype, $extradata)
   {
     global $mysql;
     global $sessiontimeout;
     global $REMOTE_ADDR;
     global $MultipleLogins;
     
     if ($sessiontype == "board")
     {
       $sessionexpirytime = (intval($extradata['timeout']) * 60); // Value is in minutes, we want seconds
       $extrafieldnames = ", session_minutes, lastactivity, login_ip ";
       $extrafieldvalues = ", ".intval($extradata['timeout']).", ".Date("U").", '".$extradata['login_ip']."'";
       $table = TABLE_BOARD_SESSIONS;
     }

     $now = Date("U");
    
     // Sessions may only start if there is not a current session
     $checkcursql = "SELECT MAX(ID) AS maxid "
                   ."  FROM ".$table
                   ." WHERE username = '$username' "
                   ."   AND password <> '-DELETED-' "
                   ."   AND expirytime > $now ";

     //echo "<br>checksql = $checkcursql<BR>\n";
     if ($checkcurexe = runQuery($checkcursql))
     {
       $row = fetchResultArray($checkcurexe);
       $checkresult = $row['maxid'];

       // If there's no current session, OR multiple logins are allowed...
       if ((!$checkresult) || ($MultipleLogins))
       {
         // Generate a magic key
         $randomseed = intval(preg_replace('/[^0-9]/', "", substr(Date("U").microtime(), 4, 10)));
         srand($randomseed);
         $newid = rand(11111, 99999);
        
         $magickey = md5($newid.$REMOTE_ADDR.time());
          //echo "magickey was generated<br>";   
         // Now do the data insert
         $insertsql = "INSERT INTO ".$table
                     ."(username, password, magickey, issuetime, "
                     ." expirytime".$extrafieldnames.") "
                     ."VALUES "
                     ."('$username', '$password', '$magickey', "
                     ." ".Date("U").", "
                     ." ".(Date("U")+$sessionexpirytime).$extrafieldvalues.")";
               
         //echo "insertsql = $insertsql<BR>\n";
         if ($insertexe = runQuery($insertsql))
         {
           // This is now added to the database, throw them a bone. Or a cookie.
           sendCookie($sessiontype."sessionkey", "", 0);                // Delete existing
           sendExpiringCookie($sessiontype."sessionkey", $magickey, 0); // Set new
           $return['status'] = "success";
           $return['magickey'] = $magickey;
           $return['sessionid'] = fetchLastInsert();
         }
         else
         {  
           //echo "insert sql failed<br>";
           // Everything's gone pear-shaped.
           $return['status'] = "error";
           $return['reason']  = "db";
           $return['detail']  = mysqli_error($mysql);
         }
       }
       else
       { 
         // echo "session already exists<br>";
         // got a $checkresult, so they already have a session.
         $getsessionsql = "SELECT * "
                         ."  FROM ".$table
                         ." WHERE ID = $checkresult";
         //echo "$getsessionsql<BR>\n";
         if ($getsessionexe = runQuery($getsessionsql))
         {
           //echo "reason for error = existingsession<br>";
           $row = fetchResultArray($getsessionexe);
           $return['status'] = "error";
           $return['reason']  = "existingsession";
           $return['expires'] = $row['expirytime'];
         }
         else
         {
           // Everything's gone pear-shaped.
           $return['status'] = "error";
           $return['reason']  = "db";
           $return['detail']  = mysqli_error($mysql);
         }
       }
     }
     //couldn't select MAX(ID) from (whatever)_sessions.
     else
     {
       // Everything's gone pear-shaped.
       $return['status'] = "error";
       $return['reason']  = "db";
       $return['detail']  = mysqli_error($mysql);
     }

     //var_dump($return);
     return $return;
   }
?>