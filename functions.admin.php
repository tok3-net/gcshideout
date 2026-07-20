<?php
   define("ADMIN_FUNCTIONS_AVAILABLE", 1);

   // --------------------------------------------------------------------------
   // ADMINISTRATOR-ONLY FUNCTIONS
   // --------------------------------------------------------------------------
   
   // Creates a new board group. Also creates a new Board within that Group
   Function newGroup($groupname)
   {
     if (!false)
     {
       $groupname = addSlashes($groupname);
     }
     
     // Make the SQL
     $sql = "INSERT INTO ".TABLE_GROUPS." (groupname) VALUES ('$groupname')";
     
     // Run it
     if ($exe = runQuery($sql))
     {
       // Get the inserted board's ID
       $groupid = fetchLastInsert();
       
       // Now create a thread and post for it
       $board = newBoard($groupid, "New ".strtoupper($groupname)." board", "z", "Description", "0", "0");
     }
     
     return $groupid;
   }
   
   // Creates a new Board, and posts a Welcome message in there (this is done
   // for database integrity issues - a Board must have Posts to show up in
   // the Board List)
   Function newBoard($group, $boardname, $boardrank = "", $boarddescription = "", $boardvippost, $boardprivate)
   {
     $safeboardname        = $boardname;
     $safeboardrank        = $boardrank;
     $safeboarddescription = $boarddescription;
     $safeboardvippost = $boardvippost;
     $safeboardprivate = $boardprivate;
     if (!false)
     {
       $safeboardname        = addSlashes($safeboardname);
       $safeboardrank        = addSlashes($safeboardrank);
       $safeboarddescription = addSlashes($safeboarddescription);
       $safeboardvippost = addSlashes($safeboardvippost);
       $safeboardprivate = addSlashes($safeboardprivate);
     }
     
     // Make the SQL
     $sql = "INSERT INTO ".TABLE_BOARDS." (boardname, boardrank, description, groupid, vippost, private) "
           ."VALUES ('$safeboardname', '$safeboardrank', '$safeboarddescription', '$group', '$safeboardvippost', '$safeboardprivate')";
     
     // Run it
     if ($exe = runQuery($sql))
     {
       // Get the inserted board's ID
       $boardid = fetchLastInsert();
       
       // Now create a thread and post for it
       $thread = newThread($boardid, "Welcome to the ".$boardname." board!", 1, "Hope you enjoy this new board.  Here is a quick description of it:[hr]".$boarddescription);
     }
     
     return $boardid;
   }
   
   // Outputs a select box of all threads with the subject of the first post.
   // Probably not much use to anyone, but hey...
   Function inputThreadID ($name, $value)
   {
     $sql = "SELECT t.ID, fp.subject "
           ."  FROM ".TABLE_THREADS." t, ".TABLE_POSTS." fp "
           ." WHERE fp.threadid = t.ID ";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $selected = "";
         if ($row['ID'] == $value)
         { $selected = " SELECTED"; }
         
         $options .= " <OPTION VALUE='".$row['ID']."'".$selected."> ".$row['subject']." </OPTION>\n";
       }
       $output = "<SELECT NAME='$name'>\n"
                .$options
                ."</SELECT>\n";
     }
     else
     {
       $output = "Whoops.";
     }
     
     return $output;
   }
   
   // Returns a single message from the database, by message ID
   Function viewSingleMessage($msgid)
   {
     $sql = "SELECT p.ID, p.threadid, p.postdate, p.authorid, p.recipientid, p.subject, p.body, "
           ."       p.edituserid, p.editdate, p.editcount, p.pollid, p.ipaddress, "
           ."       u.displayname, u.displayformat, u.postcount, u.created, "
           ."       u.sig1, u.sig2, u.sig3, u.sig4, u.sig5, "
           ."       a.classname AS userclassname "
           ."  FROM ".TABLE_POSTS." p, ".TABLE_USERS." u, ".TABLE_ACCESS_CLASS." a "
           ." WHERE u.ID = p.authorid "
           ."   AND u.classid = a.ID "
           ."   AND p.ID = ".intval($msgid);
     $exe = runQuery($sql);
     
     $row = fetchResultArray($exe);
     
     if ($row['ID'])
     {
       $html = "<TABLE WIDTH=100% ALIGN=CENTER CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
              .formatPost($row, "administrator")
              ."</TABLE>\n";
     }
     
     $return['html'] = $html;
     
     return $return;
   }

   // Returns a HTML table list of classes with class attributes in columns
   Function listClasses($options = "", $blankrow = "")
   {
     global $PHP_SELF;
     
     if ($blankrow) { $options['classid'] = 1; }
     
     if ($options['classid'])
     { $classidcondition = "   AND ac.ID = ".$options['classid']." "; }
     
     $sql = "SELECT ac.ID, ac.classname, ac.nameformat, cp.ID as permissionrowid, cp.accessadmin, cp.accessmanager, cp.accessvip, "
           ."       cp.accessmoderator, cp.accessinsider, cp.accessread, cp.accesswrite, cp.accessdelete, "
           ."       cp.accesstimeedit, cp.accessfulledit, cp.accessnameformat, cp.accesslogin "
           ."  FROM ".TABLE_ACCESS_CLASS." ac, ".TABLE_CLASS_PERMISSION." cp"
           ." WHERE ac.ID = cp.classid "
           .$classidcondition
           ." ORDER BY ID";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         if ($options['overridevalues'])
         { 
           $row = $options['overridevalues']; 
         }
         
         $currentadmin    = $row['accessadmin'];
         $permissionrowid = $row['permissionrowid'];
         $classname       = $row['classname'];
         $nameformat      = $row['nameformat'];
         
         if ($options['showlinks'])
         {
           $linkstart = "<A HREF='".$PHP_SELF."?classid=".$row['ID']."'>";
           $linkstop  = "</A>";
           if ($options['linkparams'])
           {
             $linkstart = "<A HREF='".$PHP_SELF.$options['linkparams']."&classid=".$row['ID']."'>";
           }
         }
         
         $accessadmin      = intval($row['accessadmin']);
		 $accessmanager    = intval($row['accessmanager']);
         $accessmoderator  = intval($row['accessmoderator']);
		 $accessvip		   = intval($row['accessvip']);
	$accessinsider = intval($row['accessinsider']);
         $accessread       = intval($row['accessread']);
         $accesswrite      = intval($row['accesswrite']);
         $accessdelete     = intval($row['accessdelete']);
         $accesstimeedit   = intval($row['accesstimeedit']);
         $accessfulledit   = intval($row['accessfulledit']);
         $accessnameformat = intval($row['accessnameformat']);
         $accesslogin      = intval($row['accesslogin']);
         
         if ($options['checkboxes'])
         {
           $accessadmin      = inputCheckbox("accessadmin", "1", $row['accessadmin']);
           $accessmanager    = inputCheckbox("accessmanager", "1", $row['accessmanager']);
           $accessmoderator  = inputCheckbox("accessmoderator", "1", $row['accessmoderator']);
		   $accessvip		 = inputCheckbox("accessvip", "1", $row['accessvip']);
	$accessinsider = inputCheckbox("accessinsider", "1", $row['accessinsider']);
           $accessread       = inputCheckbox("accessread", "1", $row['accessread']);
           $accesswrite      = inputCheckbox("accesswrite", "1", $row['accesswrite']);
           $accessdelete     = inputCheckbox("accessdelete", "1", $row['accessdelete']);
           $accesstimeedit   = inputCheckbox("accesstimeedit", "1", $row['accesstimeedit']);
           $accessfulledit   = inputCheckbox("accessfulledit", "1", $row['accessfulledit']);
           $accessnameformat = inputCheckbox("accessnameformat", "1", $row['accessnameformat']);
           $accesslogin      = inputCheckbox("accesslogin", "1", $row['accesslogin']);
         }
         
         $outputclassname = $row['classname'];

         if ($options['editclassname'])
         {
           $outputclassname = inputText("classname", $row['classname'], 15);
         }
         
         $outputrowid = $linkstart.$row['ID'].$linkstop;
         if ($blankrow)
         {
           $outputrowid = "NEW";
           $outputclassname = inputText("classname", $options['classname'], 10);
           $accessadmin      = inputCheckbox("accessadmin", "1", $options['accessadmin']);
           $accessmanager    = inputCheckbox("accessmanager", "1", $options['accessmanager']);
           $accessmoderator  = inputCheckbox("accessmoderator", "1", $options['accessmoderator']);
           $accessvip        = inputCheckbox("accessvip", "1", $options['accessvip']);
           $accessinsider = inputCheckbox("accessinsider", "1", $options['accessinsider']);
           $accessread       = inputCheckbox("accessread", "1", $options['accessread']);
           $accesswrite      = inputCheckbox("accesswrite", "1", $options['accesswrite']);
           $accessdelete     = inputCheckbox("accessdelete", "1", $options['accessdelete']);
           $accesstimeedit   = inputCheckbox("accesstimeedit", "1", $options['accesstimeedit']);
           $accessfulledit   = inputCheckbox("accessfulledit", "1", $options['accessfulledit']);
           $accessnameformat = inputCheckbox("accessnameformat", "1", $options['accessnameformat']);
           $accesslogin      = inputCheckbox("accesslogin", "1", $options['accesslogin']);
         }
         
         $results .= "<TR><TD CLASS='BoardRowBody'>".$outputrowid."</TD>\n"
                    ."    <TD CLASS='BoardRowBody'>".$outputclassname."</TD>\n"
                    ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>".$accessadmin."</TD>\n"
                    ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>".$accessmanager."</TD>\n"
                    ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>".$accessmoderator."</TD>\n"
                    ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>".$accessvip."</TD>\n"
                    ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>".$accessinsider."</TD>\n"
                    ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>".$accessread."</TD>\n"
                    ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>".$accesswrite."</TD>\n"
                    ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>".$accessdelete."</TD>\n"
                    ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>".$accesstimeedit."</TD>\n"
                    ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>".$accessfulledit."</TD>\n"
                    ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>".$accessnameformat."</TD>\n"
                    ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>".$accesslogin."</TD></TR>\n";
       }

       $results = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
                 ."<TR><TD CLASS='BoardColumn' WIDTH=5%></TD>\n"
                 ."    <TD CLASS='BoardColumn' WIDTH=50%>Class</TD>\n"
                 ."    <TD CLASS='BoardColumn' ALIGN=CENTER WIDTH=5%>Admin</TD>\n"
                 ."    <TD CLASS='BoardColumn' ALIGN=CENTER WIDTH=5%>Manager</TD>\n"
                 ."    <TD CLASS='BoardColumn' ALIGN=CENTER WIDTH=5%>Mod</TD>\n"
                 ."    <TD CLASS='BoardColumn' ALIGN=CENTER WIDTH=5%>VIP</TD>\n"
                 ."    <TD CLASS='BoardColumn' ALIGN=CENTER WIDTH=5%>Insider</TD>\n"
                 ."    <TD CLASS='BoardColumn' ALIGN=CENTER WIDTH=5%>Read</TD>\n"
                 ."    <TD CLASS='BoardColumn' ALIGN=CENTER WIDTH=5%>Write</TD>\n"
                 ."    <TD CLASS='BoardColumn' ALIGN=CENTER WIDTH=5%>Delete</TD>\n"
                 ."    <TD CLASS='BoardColumn' ALIGN=CENTER WIDTH=5%>TimeEdit</TD>\n"
                 ."    <TD CLASS='BoardColumn' ALIGN=CENTER WIDTH=5%>FullEdit</TD>\n"
                 ."    <TD CLASS='BoardColumn' ALIGN=CENTER WIDTH=5%>NameFormat</TD>\n"
                 ."    <TD CLASS='BoardColumn' ALIGN=CENTER WIDTH=5%>Login</TD></TR>\n"
                 .$results
                 ."</TABLE>\n";
     }
     
     $return['classlist']       = $results;
     $return['classcount']      = resultCount($exe);
     $return['nameformat']      = $nameformat;       // Meaningless if no classid is passed in
     $return['currentadmin']    = $currentadmin;     // Meaningless if no classid is passed in
     $return['permissionrowid'] = $permissionrowid;  // Ditto.
     $return['classname']       = $classname;        // Ditto.
     
     return $return;
   }

   // Updates a class in the database - you can set a new name and new default
   // name format preference
   Function updateClass($classid, $newname, $nameformat)
   {
     if (!false)
     { 
       $newname = addSlashes($newname);
       $nameformat = addSlashes($nameformat);
     }
     
     $sql = "UPDATE ".TABLE_ACCESS_CLASS." SET classname = '$newname', nameformat = '$nameformat' WHERE ID = $classid";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   // Creates a new class in the database - you can set the name and default
   // name format preference
   Function newClass($classname, $nameformat)
   {
     if (!false)
     { 
       $classname = addSlashes($classname);
       $nameformat = addSlashes($nameformat);
     }
     
     $sql = "INSERT INTO ".TABLE_ACCESS_CLASS." (classname, nameformat) VALUES ('$classname', '$nameformat')";
     if ($exe = runQuery($sql))
     {
       return fetchLastInsert();
     }
     else
     {
       return 0;
     }  
   }
   
   // Inserts a new permission matrix to the database for a class ID
   Function newClassPermissions($classid, $data)
   {
     $newdata = $data;
     
     if (!false)
     {
       foreach ($data as $key => $value)
       {
         $newdata[$key] = addSlashes($value);
       }
     }
     
     $nochangefields = array("ID", "classid");
     
     // Allow this function to alter anything that's thrown at it
     foreach ($newdata as $key => $value)
     {
       if (!in_array($key, $nochangefields))
       {
         $fieldname[]  = "$key";
         $fieldvalue[] = "'$value'";
       }
     }
     
     $fieldnames  = implode(",", $fieldname);
     $fieldvalues = implode(",", $fieldvalue);
     
     $sql = "INSERT INTO ".TABLE_CLASS_PERMISSION." (classid, ".$fieldnames." ) "
           ." VALUES (".intval($classid).", ".$fieldvalues." )";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   // Updates a class' permission matrix in the database
   Function updateClassPermissions($permissionsetid, $data)
   {
     $newdata = $data;
     if (!false)
     {
       foreach ($data as $key => $value)
       {
         $newdata[$key] = addSlashes($value);
       }
     }
     
     $nochangefields = array("ID", "classid");
     
     // Allow this function to alter anything that's thrown at it
     foreach ($newdata as $key => $value)
     {
       if (!in_array($key, $nochangefields))
       {
         $fieldupdate[] = "$key = '$value'";
       }
     }
     
     $fieldupdates = implode(",", $fieldupdate);
     
     $sql = "UPDATE ".TABLE_CLASS_PERMISSION." "
           ."   SET ".$fieldupdates." "
           ." WHERE ID = $permissionsetid";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   // Update a Board Group - you can set a name name and new ranking
   Function updateGroupData($groupid, $newname, $newrank)
   {
     if (!false)
     { 
       $newname = addSlashes($newname); 
       $newrank = addSlashes($newrank); 
     }
     
     $sql = "UPDATE ".TABLE_GROUPS." SET groupname = '$newname', grouprank = '$newrank' WHERE ID = $groupid";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   // Delete a Board from the database. Will retrieve the ID of all Threads
   // in the Board and call removeThread() for each Thread ID
   Function removeBoard($id)
   {
     if (is_array($id))
     {
       for ($i = 0; $i < count($id); $i++ )
       {
         $condition[] = "ID = ".$id[$i]." ";
         $delboards[] = $id[$i];
       }
       $conditions = implode(" OR ", $condition);
     }
     else
     {
       $conditions = "ID = $id ";
       $delboards[] = $id;
     }
     
     // Remove threads from this board before you delete the board
     foreach ($delboards as $key => $value)
     {
       $threadids = fetchIDs($value, "board");
       removeThread($threadids);
     }

     $sql = "DELETE FROM ".TABLE_BOARDS." WHERE ".$conditions;
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   // Delete a Group from the database. Will retrieve the ID of all Boards
   // in the Group and call removeBoard() for each Board ID
   Function removeGroup($id)
   {
     if (is_array($id))
     {
       for ($i = 0; $i < count($id); $i++ )
       {
         $condition[] = "ID = ".$id[$i]." ";
         $delgroups[] = $id[$i];
       }
       $conditions = implode(" OR ", $condition);
     }
     else
     {
       $conditions = "ID = $id ";
       $delgroups[] = $id;
     }
     
     // Remove boards from this group before you delete the group
     foreach ($delgroups as $key => $value)
     {
       $boardids = fetchIDs($value, "group");
       removeBoard($boardids);
     }
     
     $sql = "DELETE FROM ".TABLE_GROUPS." WHERE ".$conditions;
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   // Reads in data from $filename (uploaded file), base64's it
   // and then chunk_split()'s it so that it's nice and neat, plain
   // text mime-encoded for database insertion and portability
   Function fileToBase64($filename)
   {
     // Read the file in
     $datafilesize = filesize($filename);
     $fp = fopen($filename, "r");
     $tempdata = fread($fp, $datafilesize);
     fclose($fp);
     
     $base64filedata = base64_encode($tempdata);
     $chunkedfiledata = chunk_split($base64filedata);
     
     return $chunkedfiledata;
   }
   
   // Dynamically updates given fields in the icon table
   Function updateIcon($iconid, $data)
   {
     $newdata = $data;
     if (!false)
     {
       foreach ($data as $key => $value)
       {
         $newdata[$key] = addSlashes($value);
       }
     }
     
     $nochangefields = array("ID");
     
     // Allow this function to alter anything that's thrown at it
     foreach ($newdata as $key => $value)
     {
       if (!in_array($key, $nochangefields))
       {
         $fieldupdate[] = "$key = '$value'";
       }
     }
     $fieldupdates = implode(",", $fieldupdate);
     
     if ($changetimestamp == "yes")
     {
       $lastupdated = ", updated = ".intval(Date("U"));
     }
     
     $sql = "UPDATE ".TABLE_ICONS." "
           ."   SET ".$fieldupdates.$lastupdated." "
           ." WHERE ID = $iconid";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   // Create a new entry in the Icon table in the database. An icon
   // goes into a group, and has a name, filename, data (the icon itself)
   // and a mimetype which all go into the database
   Function newIcon($groupid, $iconname, $iconfilename, $iconfiledata, $iconmimetype)
   {
     if (!false)
     {
       $iconname     = addSlashes($iconname);
       $iconfilename = addSlashes($iconfilename);
       $iconfiledata = addSlashes($iconfiledata);
       $iconmimetype = addSlashes($iconmimetype);
       $mimetype     = addSlashes($mimetype);
     }
     $filedata = addSlashes($filedata);
     
     $sql = "INSERT INTO ".TABLE_ICONS." (groupid, iconname, filename, data, mimetype) "
           ." VALUES ($groupid, '$iconname', '$iconfilename', '$iconfiledata', '$iconmimetype')";
     if ($exe = runQuery($sql))
     {
       return fetchLastInsert();
     }
     else
     {
       return 0;
     }
   }
   
   // Create a new icon group - an icon group can be restricted to only
   // allow members of a certain class ID to use it, this is optional
   Function newIconGroup($groupname, $classid = "")
   {
     if (!false)
     { $groupname = addSlashes($groupname); }
     
     $sql = "INSERT INTO ".TABLE_ICON_GROUPS." (groupname, classid) VALUES ('$groupname', ".intval($classid).")";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   // Delete an icon group from the database
   Function removeIconGroup($groupid)
   {
     $sql = "DELETE FROM ".TABLE_ICON_GROUPS." WHERE ID = ".intval($groupid);
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   // Update an icon group - change its name and class ID restriction
   Function updateIconGroup($groupid, $groupname, $classid)
   {
     if (!false)
     { $groupname = addSlashes($groupname); }
     
     $sql = "UPDATE ".TABLE_ICON_GROUPS." "
           ."   SET groupname = '$groupname', classid = ".intval($classid)." "
           ." WHERE ID = $groupid";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }

   // Delete an icon - remove its ID from the database
   Function deleteIcon ($iconid)
   {
     global $DocRoot;
     global $BaseDir;
     
     // Deleting an icon is a two-part operation: database then disk
     
     // Fetch the current icon information
     $icondata = fetchRow($iconid, TABLE_ICONS);
     
     // Delete the database entry
     $sql = "DELETE FROM ".TABLE_ICONS." WHERE ID = ".intval($iconid);
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   // Change the group to which an icon belongs
   Function changeIconGroup($iconid, $newgroupid)
   {
     $sql = "UPDATE ".TABLE_ICONS." SET groupid = ".intval($newgroupid)." WHERE ID = ".intval($iconid);
     
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   // Interrogates the session table and returns all data between selected
   // times. This is a bit screwy due to timezone differences.
   Function viewSessions ($unixstart, $unixstop)
   {
     global $session;
     
     $sql = "SELECT bs.ID as sessionnumber, bs.username, bs.issuetime, bs.expirytime, u.* "
           ."  FROM ".TABLE_BOARD_SESSIONS." bs, ".TABLE_USERS." u "
           ." WHERE bs.username = u.ID "
           ."   AND issuetime > ".intval($unixstart)." "
           ."   AND expirytime <= ".intval($unixstop);
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $logintime  = Date("d M Y H:i:s", $row['issuetime']);
         $logouttime = Date("d M Y H:i:s", $row['expirytime']);
         if ($row['expirytime'] > Date("U"))
         {
           $logouttime = "<SPAN CLASS='grey'>".$logouttime."</SPAN>";
         }
         
         $unameextra = "";
         if ($row['sessionnumber'] == $session['ID'])
         {
           $unameextra = " <B STYLE='font-size: 8pt;'>(You)</B>";
         }
         
         $sessiondata .= "<TR><TD CLASS='BoardRowHeading'>".$row['sessionnumber']."</TD>\n"
                        ."    <TD CLASS='BoardRowBody'>".usernameDisplay($row['username']).$unameextra."</TD>\n"
                        ."    <TD CLASS='BoardRowHeading'>".$logintime."</TD>\n"
                        ."    <TD CLASS='BoardRowHeading'>".$logouttime."</TD></TR>\n";
       }
       $sessions = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
                  ."<TR><TD WIDTH=10% CLASS='BoardColumn'>SessID</TD>\n"
                  ."    <TD WIDTH=40% CLASS='BoardColumn'>Username</TD>\n"
                  ."    <TD WIDTH=25% CLASS='BoardColumn'>Login</TD>\n"
                  ."    <TD WIDTH=25% CLASS='BoardColumn'>Logout/Expiry</TD>\n"
                  ."    </TR>\n"
                  .$sessiondata
                  ."</TABLE>\n";
     }
     
     return $sessions;
   }
   
   // Find all the IP addresses associated with a user ID. IPs are used
   // on signup, posts and sessions. This function interrogates all three
   // sources and returns the results in a uniform format.
   Function findIPAddressFromUser($userid)
   {
     global $PHP_SELF;

     // Fetch this user's signup ip
     $user = fetchRow($userid, TABLE_USERS);
     if ($user['ipaddress'])
     {
       $result[] = array("ip" => $user['ipaddress'],
                         "match" => "Account created on ".dateNeat($user['created']));
     }

     // Fetch all IPs from this user's posts
     $sql = "SELECT COUNT(ID) as postcount, ipaddress "
           ."  FROM ".TABLE_POSTS." "
           ." WHERE authorid = ".$userid
           ." GROUP BY ipaddress";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $result[] = array("ip" => $row['ipaddress'],
                           "host" => gethostbyaddr($row['ipaddress']),
                           "match" => "Posted ".$row['postcount']." messages");
       }
     }

     // Fetch all login IPs from this user's sessions
     $sql = "SELECT COUNT(ID) as sessioncount, login_ip "
           ."  FROM ".TABLE_BOARD_SESSIONS." "
           ." WHERE username = ".$userid
           ."   AND login_ip <> '' "
           ." GROUP BY login_ip";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $result[] = array("ip" => $row['login_ip'],
                           "host" => gethostbyaddr($row['login_ip']),
                           "match" => "Started ".$row['sessioncount']." session(s)");
       }
     }

     // Now put it all together into a HTML table
     for ($i = 0; $i < count($result); $i++ )
     {
       $findallips = "&nbsp;";
       if (!$result[$i]['fullmatch'])
       {
         $findallips = makeLink($PHP_SELF."?action=finduserips&userid=".$result[$i]['userid'], "Find User's IPs");
       }
       $host = $result[$i]['host'];
       if ($result[$i]['host'] == $result[$i]['ip'])
       {
         $host = "-";
       }
       $tablerow .= "<TR><TD CLASS='BoardRowBody'>".makeLink($PHP_SELF."?ip=".urlencode($result[$i]['ip'])."&action=findipusers", $result[$i]['ip'])."</TD>\n"
                   ."    <TD CLASS='BoardRowBody'>".$host."</TD>\n"
                   ."    <TD CLASS='BoardRowBody'>".$result[$i]['match']."</TD></TR>\n";
     }     
     $table = "<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1 BORDER=0>\n"
             ."<TR><TD WIDTH=25% CLASS='BoardColumn'>IP Address</TD>\n"
             ."    <TD WIDTH=50% CLASS='BoardColumn'>Hostname</TD>\n"
             ."    <TD WIDTH=25% CLASS='BoardColumn'>Description</TD></TR>\n"
             .$tablerow
             ."</TABLE>\n";
     
     $return['results'] = $result;
     $return['resultcount'] = count($result);
     $return['html'] = $table;
     
     return $return;
   }
   
   // This function finds all the user IDs that have used an IP address to
   // post, log in or sign up and returns them in a uniform format.
   Function findIPAddressUse($ip)
   {
     global $PHP_SELF;
     
     // Function runs two checks:
     //  - which users posted from this IP?
     //  - which users signed up from this IP?
     
     // Check to see who posted from this IP
     $sql = "SELECT p.authorid, u.displayname, u.displayformat, u.postcount, COUNT(p.authorid) as ippostcount, p.ipaddress"
           ."  FROM ".TABLE_POSTS." p, ".TABLE_USERS." u "
           ." WHERE u.ID = p.authorid "
           ."   AND p.recipientid = '' "
           ."   AND ipaddress = '".$ip."' "
           ."GROUP BY authorid";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         // Rather than run a crapload of database queries, let's give the username formatting
         // function everything it needs from here:
         $dataarray = array("postcount" => $row['postcount'], 
                            "displayname" => $row['displayname'], 
                            "displayformat" => $row['displayformat']);
         $username = usernameDisplay($row['authorid'], "", "showstar", $dataarray);
         
         // Display a 100% match if all their posts came from the IP
         if ($row['ippostcount'] == $row['postcount'])
         {
           $matchrate = "<B STYLE='color: green'>100%</B>";
           $fullmatch = 1;
         }
         else
         {
           $matchrate = sprintf("%0.1f", ($row['ippostcount'] / $row['postcount']) * 100)."%";
           $fullmatch = 0;
         }
         
         $result[] = array("userid" => $row['authorid'],
                           "username" => $username,
                           "extra" => $row['ippostcount']." of ".$row['postcount']." posts came from ".$ip,
                           "match" => $matchrate,
                           "fullmatch" => $fullmatch);
       }
     }
     
     // Now check to see who signed up from this IP
     $sql = "SELECT u.ID as userid, u.displayname, u.displayformat, u.postcount, u.ip_signup"
           ."  FROM ".TABLE_USERS." u "
           ." WHERE ip_signup = '".$ip."' ";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         // Rather than run a crapload of database queries, let's give the username formatting
         // function everything it needs from here:
         $dataarray = array("postcount" => $row['postcount'], 
                            "displayname" => $row['displayname'], 
                            "displayformat" => $row['displayformat']);
         $username = usernameDisplay($row['userid'], "", "showstar", $dataarray);
         $result[] = array("userid" => $row['userid'],
                           "username" => $username,
                           "extra" => "Signed up from ".$ip);
       }
     }
     
     // Now check to see who logged in from this IP
     $sql = "SELECT s.username, COUNT(s.ID) as sessioncount, u.displayname, u.displayformat, u.postcount"
           ."  FROM ".TABLE_BOARD_SESSIONS." s, ".TABLE_USERS." u "
           ." WHERE s.username = u.ID "
           ."   AND s.login_ip = '".$ip."' "
           ." GROUP BY s.username";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         // Rather than run a crapload of database queries, let's give the username formatting
         // function everything it needs from here:
         $dataarray = array("postcount" => $row['username'], 
                            "displayname" => $row['displayname'], 
                            "displayformat" => $row['displayformat']);
         $username = usernameDisplay($row['username'], "", "showstar", $dataarray);
         $result[] = array("userid" => $row['username'],
                           "username" => $username,
                           "extra" => $row['sessioncount']." session/s from ".$ip);
       }
     }
     
     
     // Now put it all together into a HTML table
     for ($i = 0; $i < count($result); $i++ )
     {
       $findallips = "&nbsp;";
       if (!$result[$i]['fullmatch'])
       {
         $findallips = makeLink($PHP_SELF."?action=finduserips&userid=".$result[$i]['userid'], "Find User's IPs");
       }
       $tablerow .= "<TR><TD CLASS='BoardRowBody'>".$result[$i]['username']."</TD>\n"
                   ."    <TD CLASS='BoardRowBody'>".$result[$i]['extra']."</TD>\n"
                   ."    <TD CLASS='BoardRowBody'>".$findallips."</TD>\n"
                   ."    <TD CLASS='BoardRowBody'>".$result[$i]['match']."&nbsp;</TD></TR>\n";
     }     
     $table = "<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1 BORDER=0>\n"
             ."<TR><TD WIDTH=25% CLASS='BoardColumn'>Username</TD>\n"
             ."    <TD WIDTH=45% CLASS='BoardColumn'>IP Match Information</TD>\n"
             ."    <TD WIDTH=20% CLASS='BoardColumn'>Option</TD>\n"
             ."    <TD WIDTH=10% CLASS='BoardColumn'>Rate</TD></TR>\n"
             .$tablerow
             ."</TABLE>\n";
     
     $return['results'] = $result;
     $return['resultcount'] = count($result);
     $return['html'] = $table;
     
     return $return;
   }
   
   // Add an admin note against a user ID. Stores the note data, the user 
   // ID and the name of the user who wrote it
   Function addAdminNote($userid, $authorid, $note)
   {
     if (!false)
     {
       $note = addSlashes($note);
     }
     
     $sql = "INSERT INTO ".TABLE_USER_NOTES." "
           ." (userid, authorid, notedate, note) "
           ." VALUES "
           ." (".intval($userid).", ".intval($authorid).", ".Date("U").", '".$note."')";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   // Deletes an admin note by ID
   Function removeAdminNote($noteid)
   {
     $sql = "DELETE FROM ".TABLE_USER_NOTES." WHERE ID = ".intval($noteid);
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   // Returns all the admin notes for a specific user ID
   Function fetchAdminNotes($userid)
   {
     global $PHP_SELF;

     $sql = "SELECT un.*, u.displayname, u.displayformat "
           ."  FROM ".TABLE_USER_NOTES." un, ".TABLE_USERS." u "
           ." WHERE u.ID = un.authorid "
           ."   AND userid = ".intval($userid)
           ." ORDER BY notedate ASC";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $dataarray = array("displayname" => $row['displayname'], 
                            "displayformat" => $row['displayformat']);
         $authorname = usernameDisplay($row['authorid'], "", "", $dataarray);
         $notedata .= "<TR VALIGN=BASELINE>\n"
                     ."    <TD CLASS='BoardRowBody'>".$row['ID']."</TD>\n"
                     ."    <TD CLASS='BoardRowBody'>\n"
                     ."        ".dateNeat($row['notedate'])." by ".$authorname."\n"
                     ."        <HR SIZE=1>\n"
                     .bodyText($row['note'])
                     ."        <HR SIZE=1>\n"
                     ."        <DIV ALIGN=RIGHT>".makeLink($PHP_SELF."?action=usernotes&step=removenote&userid=".$userid."&noteid=".$row['ID'], "Remove Note")."</DIV></TD></TR>\n";
       }
       
       if (!resultCount($exe))
       {
         $notedata = "<TR><TD ALIGN=CENTER><B>No notes found</B></TD></TR>\n";
         $notecount = 0;
       }
       else
       {
         $noteheadings = "<TR><TD WIDTH=10% CLASS='BoardColumn'>ID</TD>\n"
                        ."    <TD WIDTH=90% CLASS='BoardColumn'>Note</TD></TR>\n";
       }
       $notetable = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
                   .$noteheadings
                   .$notedata
                   ."</TABLE>\n";
     }
     
     $return['html'] = $notetable;
     $return['notecount'] = resultCount($exe);
     
     return $return;
   }
   
   // Add a ban to the system's ban table
   Function newSystemBan($details)
   {
     $sql = "INSERT INTO ".TABLE_SYSTEM_BAN." (ip_start, ip_stop, userid, adminid, active) "
           ." VALUES ('".$details['ip1']."', '".$details['ip2']."', ".intval($details['userid']).", ".intval($details['adminid']).", ".intval($details['active']).")";
     if ($exe = runQuery($sql))
     {
       return fetchLastInsert();
     }
   }
   
   // Delete bans - $ids needs to be an array, even if there's only one
   Function removeSystemBans($ids)
   {
     $sql = "DELETE FROM ".TABLE_SYSTEM_BAN." WHERE ID = ".implode(" OR ID = ", $ids);
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
?>