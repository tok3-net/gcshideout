<?php
   // These used to be auto-populated by register_globals (removed in PHP
   // 5.4). Restore them here since a lot of code below and in the
   // functions.*.php files still references them as bare globals.
   $PHP_SELF    = $_SERVER['PHP_SELF']    ?? '';
   $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'] ?? '';
   $HTTP_HOST   = $_SERVER['HTTP_HOST']   ?? '';

   // ==========================================================================
   // register_globals EMULATION -- READ THIS BEFORE DEPLOYING ANYWHERE PUBLIC
   // ==========================================================================
   // This entire codebase was written assuming register_globals: virtually
   // every page reads submitted form fields and cookies (e.g. $username,
   // $password, $action, $boardsession) as bare variables, with no $_GET/
   // $_POST/$_COOKIE reference anywhere. Without this, login, posting,
   // registration, and every admin action are completely non-functional --
   // not just buggy, but 100% broken -- on any PHP released since 5.4 (2012).
   //
   // This block restores that old behaviour using extract(..., EXTR_SKIP),
   // which will NOT overwrite a variable that already has a value in this
   // scope. Since nothing else has run yet at this point in common.php,
   // that protects nothing here directly -- but every security-sensitive
   // variable this codebase relies on ($userloggedin, $userdata, etc.) is
   // unconditionally (re)computed further down in this same file from
   // actual server-side session validation, AFTER this block runs, so a
   // request can't hand itself a fake $userloggedin=1 and have it stick.
   //
   // This is the same variable-injection risk class register_globals was
   // removed for: it revives the ability for a request to set ANY
   // same-named variable in ANY included script, anywhere in the app, that
   // isn't freshly (re)computed after this point. That risk is NOT limited
   // to common.php -- it applies to every one of the 124 files here. This
   // gets the site running again; it does not make it safe to expose
   // publicly as-is. Treat this as a placeholder for the security pass.
   extract($_COOKIE, EXTR_SKIP);
   extract($_GET,    EXTR_SKIP);
   extract($_POST,   EXTR_SKIP);

   $hostname = $HTTP_HOST;
   $hostname = preg_replace('/[^a-z]/', '', $hostname);

   $settingsfile = "settings.php";
   if (file_exists($settingsfile))
   {
     include($settingsfile);
   }
   else
   {
     echo "<B>FATAL:</B> Couldn't find $settingsfile!<BR>\n";
     Exit();
   }
   
   
   // Set up system URLs
   // ------------------
   
   if (!$AccessProtocol)
   { $AccessProtocol = "http"; }
   
   if (!$functionset)
   { $functionset = "boards"; }
   
   // DBRoot should be the front page
   $DBRoot = $AccessProtocol."://".$ServerAddress.$BaseDir;
   
   // Append basedir to docroot to get the right base directory for the
   // DiscoBoard installation
   //$DocRoot .= $BaseDir;
   // ... or maybe we don't need to do this?
   
   // GFXRoot should be the gfx directory base address
   $GFXRoot = "$DBRoot/gfx";
   
   // Turn HiddenClasses into an array
   $HiddenClassNames = explode(",", $HiddenClasses);
   
   // Configuration Setup
   // -------------------
   $configoptions['themename']          = $ThemeName;
   $configoptions['perpagethreads']     = $ShowThreadsPerPage;
   $configoptions['perpageposts']       = $ShowPostsPerPage;
   $configoptions['profanityfilter']    = $ProfanityFilterActive;
   $configoptions['profanities']        = $ProfanityFilter;
   $configoptions['threshold']          = $Threshold;
   $configoptions['emoticons']          = $EmoticonSet;
   $configoptions['useownicons']        = $UseOwnIcons;
   $configoptions['timezonechange']     = $TimeZoneChange;
   $configoptions['showlastpostername'] = $ShowLastPosterName;
   
   // Disallowed URL characters...
   $configoptions['disallowedchars']    = "[ '\"\(\)\>\<]";
   
   // Define SQL table names as constants
   $tableprefix = "";
   if (trim($DatabasePrefix))
   {
     $tableprefix = $DatabasePrefix."_";
   }
   define("TABLE_ACCESS_CLASS", $tableprefix."access_class");
   define("TABLE_BOARD_SESSIONS", $tableprefix."board_sessions");
   define("TABLE_BOARDS", $tableprefix."boards");
   define("TABLE_CLASS_PERMISSION", $tableprefix."class_permission");
   define("TABLE_COUNTRY_CODES", $tableprefix."country_codes");
   define("TABLE_FAVOURITES", $tableprefix."favourites");
   define("TABLE_GROUPS", $tableprefix."groups");
   define("TABLE_ICON_GROUPS", $tableprefix."icon_groups");
   define("TABLE_ICONS", $tableprefix."icons");
   define("TABLE_POLL_OPTIONS", $tableprefix."poll_options");
   define("TABLE_POLL_RESPONSES", $tableprefix."poll_responses");
   define("TABLE_POLLS", $tableprefix."polls");
   define("TABLE_POSTS", $tableprefix."posts");
   define("TABLE_RECOVERYVERIFICATION", $tableprefix."recoveryverification");
   define("TABLE_STICKY_THREADS", $tableprefix."sticky_threads");
   define("TABLE_SYSTEM_BAN", $tableprefix."system_ban");
   define("TABLE_THEME", $tableprefix."theme");
   define("TABLE_THREADS", $tableprefix."threads");
   define("TABLE_USER_NOTES", $tableprefix."user_notes");
   define("TABLE_USERS", $tableprefix."users");
   
   if (!$ThemeName)
   {
     echo "<B>FATAL:</B> No theme configuration found!<BR>\n";
     Exit();
   }
   
   $javascriptlink = " <SCRIPT LANGUAGE='JavaScript1.2' SRC='".$DBRoot."/javascript.php'></SCRIPT>\n";
   
   // ASSEMBLE REDIRECTION STRING
   // ==========================================================================   
   // ==========================================================================   

   // Get POST'd vars
   foreach($_POST as $key => $value)
   { 
     // <// Homesite code highlight marker
     if ($querystring)
     {
       $querystring .= "&";
     }
     $querystring .= urlencode($key)."=".urlencode($value);
   }
   
   // Get GET'd vars
   foreach($_GET as $key => $value)
   { 
     // <// Homesite code highlight marker
     if ($querystring)
     {
       $querystring .= "&";
     }
     $querystring .= urlencode($key)."=".urlencode($value);
   }
   
   if ($querystring)
   {
     $querystring = "?".$querystring;
   }
   
   $loginpage = "$DBRoot/login.php?returnurl=".urlencode("$PHP_SELF".$querystring);
   $loginpageplain = "$DBMRoot/login.php";

   // Compatibility shims for old mysql_* functions that have no direct
   // mysqli_* equivalent (everything else maps 1:1 by name).
   if (!function_exists('mysqli_result'))
   {
     function mysqli_result($result, $row, $field = 0)
     {
       if ($row < 0 || $row >= mysqli_num_rows($result)) { return null; }
       mysqli_data_seek($result, $row);
       $line = mysqli_fetch_array($result);
       return isset($line[$field]) ? $line[$field] : null;
     }
   }
   
   if (!function_exists('mysqli_field_name'))
   {
     function mysqli_field_name($result, $index)
     {
       $finfo = mysqli_fetch_field_direct($result, $index);
       return $finfo ? $finfo->name : false;
     }
   }
   
   // Establish connection to MySQL
   // -----------------------------
   
   // As of PHP 8.1, mysqli throws exceptions on error by default. This
   // codebase expects the old-style "check the return value" behaviour
   // (if (!$exe) { ... mysqli_error(...) ... }), so switch mysqli back
   // to silent/return-false mode to preserve that behaviour.
   mysqli_report(MYSQLI_REPORT_OFF);
   
   // Try the connection
   if ($mysql = mysqli_connect($MySQLServer, $MySQLUsername, $MySQLPassword))
   {
     // Select the database
     $select = mysqli_select_db($mysql, $MySQLDatabase);
     
     if (!$select)
     {
       // Database selection didn't work, so output an error
       $err[] = "Failed to select database '$MySQLDatabase': ".mysqli_error($mysql);
     }
   }
   else
   {
     // Database connection didn't work, so output an error
     $err[] = "Failed connection to $MySQLServer: ".mysqli_error($mysql);
   }
   
   // Check to see if anything went wrong
   if ($err)
   {
     echo "<B>Fatal Error</B><BR>\n"
         .implode("<BR>\n", $err);
     Exit();
   }
   
   // Load Support Functions
   // ----------------------
   $functionfiles[] = "generic";
   switch ($functionset)
   {
     case "iconview":
       $functionfiles[] = "iconview";
       break;
     case "admin":
       $functionfiles[] = "admin";
       $functionfiles[] = "html";
       $functionfiles[] = "session";
       $functionfiles[] = "board";
       $functionfiles[] = "textformat";
       break;
     default:
       $functionfiles[] = "html";
       $functionfiles[] = "session";
       $functionfiles[] = "board";
       $functionfiles[] = "textformat";
   }
   foreach ($functionfiles as $key => $value)
   {
     include("functions.".$value.".php");
   }

   // SESSION VALIDATION
   // -----------------------------------------------------------------------
   // -----------------------------------------------------------------------
   
   if (SESSION_FUNCTIONS_AVAILABLE == 1)
   {
     if ($boardsession)
     { $boardsessionkey = $boardsession; }
     
     if ($boardsessionkey)
     {
       $debug .= "We have a session key $boardsessionkey<BR>\n";
       // Got a session key, pull it and check it out
       $session = pullSession($boardsessionkey, "board");
       
       if ($session['ID'])
       {
         // We've pulled a valid session, attempt to refresh it
         $sessiondata = refreshSession($boardsessionkey, "board");
         
         if ($sessiondata['result'] == "success")
         {
           $debug .= "Session $sessiondata[ID] updated successfully.<BR>\n";
           $userloggedin = 1;
  
           $userdata = getUserInformation($session['username'], "users");
  
           // Override the current page length settings with the user's
           // individual settings, if present.
           if ($userdata['perpagethreads'])
           {
             $configoptions['perpagethreads'] = $userdata['perpagethreads'];
           }
           if ($userdata['perpageposts'])
           {
             $configoptions['perpageposts']   = $userdata['perpageposts'];
           }
           if ($userdata['timezone'])
           {
             $configoptions['timezonechange'] = $userdata['timezone'];
           }
         }
         elseif ($sessiondata['result'] == "fail")
         {
           $debug .= "Session $boardsessionkey could not be updated.<BR>\n";
           $userloggedin = 0;
           if (($PHP_SELF != "/index.php") && ($PHP_SELF != "/login.php"))
           {
             $debug .= "Doing redirection.<BR>\n";
             // Not on the front page, you should be redirected
             //echo $debug;
             $doredirect = 1;
             $extraparams = "&timeout=1&olduser=".urlencode($session['username']);
           }
           $session     = "";
           $sessiondata = "";
         }
       }
       else
       {
         // No valid session data was found...
         $debug .= "Didn't find a session ID for $boardsessionkey<BR>\n";
         $doredirect = 1;
       }
     }
     else
     {
       $debug .= "We DON'T have a session key<BR>\n";
       //echo $debug;
       $doredirect = 1;
     }
     
     if (($doredirect) && ($protectedpage))
     {
       $redirectdestination = "$loginpage$extraparams";
       $debug .= "Page is protected, redirecting to ".$redirectdestination."...<BR>\n";
     }
     
     //echo $debug;
  
     if ($protectedpage)
     {
       if ($doredirect)
       {
         if (($PHP_SELF != "/index.php") && ($PHP_SELF != "/login.php"))
         {
           closeSession($boardsessionkey, "board");
           Header("Location: ".$redirectdestination);
           Exit();
         }
       }
     }
   }
   
   // -----------------------------------------------------------------------
   // -----------------------------------------------------------------------
   
   // Check the System Bans to see if this user is banned from this IP
   
   if (BOARD_FUNCTIONS_AVAILABLE == 1)
   {
     // Don't do this if we're on banned.php already
     if ((basename($PHP_SELF) != "banned.php") && (basename($PHP_SELF) != "login.php"))
     {
       $userbanstate = assessBanStatus($REMOTE_ADDR, $userdata['ID']);
       if ($userbanstate['banned'])
       {
         Header("Location: $DBRoot/banned.php");
       }
     }
   }

   // -----------------------------------------------------------------------
   // -----------------------------------------------------------------------
   
   // Do some cache control, if we have a logged-in user
   if ($userdata['ID'])
   {
     Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");               // Date in the past
     Header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified
     Header("Cache-Control: no-store, no-cache, must-revalidate");   // HTTP/1.1
     Header("Cache-Control: post-check=0, pre-check=0", false);
     Header("Pragma: no-cache"); 
   }

   // -----------------------------------------------------------------------
   // -----------------------------------------------------------------------
   
   if (BOARD_FUNCTIONS_AVAILABLE == 1)
   {
     // Handle site navigation drill-down bar here
     $navigation[] = array("name" => "[".applyOnlyTextEffects($SiteCode)."]",
                           "url"  => $SiteURL);
     $navigation[] = array("name" => applyOnlyTextEffects($DiscoBoardName),
                           "url"  => "index.php");
     
     if ($postid)
     {
       $postdata   = fetchRow($postid, TABLE_POSTS);
       $threaddata = fetchRow($postdata['threadid'], TABLE_THREADS);
       $boarddata  = fetchRow($threaddata['boardid'], TABLE_BOARDS);
       $groupdata  = fetchRow($boarddata['groupid'], TABLE_GROUPS);
       
       $navigation[] = array("name" => applyOnlyTextEffects($groupdata['groupname']),
                             "url"  => "index.php?action=viewgroup&groupid=".$boarddata['groupid']);
       $navigation[] = array("name" => applyOnlyTextEffects($boarddata['boardname']),
                             "url"  => "board.php?boardid=".$threaddata['boardid']);
       $navigation[] = array("name" => ProfanityFilter(applyOnlyTextEffects($postdata['subject'])),
                             "url"  => "");
     }
     
     if ($threadid)
     {
       $threaddata = fetchRow($threadid, TABLE_THREADS);
       $firstpostdata = fetchRow($threaddata['postidfirst'], TABLE_POSTS);
       $boarddata  = fetchRow($threaddata['boardid'], TABLE_BOARDS);
       $groupdata  = fetchRow($boarddata['groupid'], TABLE_GROUPS);
       
       $navigation[] = array("name" => applyOnlyTextEffects($groupdata['groupname']),
                             "url"  => "index.php?action=viewgroup&groupid=".$boarddata['groupid']);
       $navigation[] = array("name" => applyOnlyTextEffects($boarddata['boardname']),
                             "url"  => "board.php?boardid=".$threaddata['boardid']);
       $navigation[] = array("name" => ProfanityFilter(applyOnlyTextEffects($firstpostdata['subject'])),
                             "url"  => "");
     }
     
     if ($boardid)
     {
       $boarddata = fetchRow($boardid, TABLE_BOARDS);
       $groupdata = fetchRow($boarddata['groupid'], TABLE_GROUPS);
  
       $navigation[] = array("name" => applyOnlyTextEffects($groupdata['groupname']),
                             "url"  => "index.php?action=viewgroup&groupid=".$groupdata['ID']);
       $navigation[] = array("name" => applyOnlyTextEffects($boarddata['boardname']),
                             "url"  => "board.php?boardid=".$boardid);
     }
   }
?>
