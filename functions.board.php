<?php
   define("BOARD_FUNCTIONS_AVAILABLE", 1);


   // provide a list of groups available
   Function listGroups($listtype = "", $opengroup = "")
   {
     global $PHP_SELF;
     global $userloggedin;
     global $userdata;
     
     $sql = "SELECT g.ID, g.groupname, g.grouprank, COUNT(b.ID) "
           ."  FROM ".TABLE_GROUPS." g, ".TABLE_BOARDS." b"
           ." WHERE b.groupid = g.ID "
           ." GROUP BY groupname "
           ." ORDER BY grouprank";
     $exe = runQuery($sql);
     
     if (resultCount($exe))
     {
       while ($row = fetchResultArray($exe))
       {
         if (!$listtype)
         {
           $boards = "";
           if (($opengroup == $row['ID']) || ($opengroup == "all"))
           {
             //echo "Fetching boards for Group $row[ID]<BR>\n";
             $boards = listBoards($row['ID'], "", "" , $row['groupname']);
             $grouprow .= $boards['boardlist'];
           }
           else
           {
             $grouprow .= "<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1 BORDER=0>\n"
                         ."<TR><TD CLASS='BoardColumn'>&nbsp;&raquo;&nbsp;".makeLink($PHP_SELF."?action=viewgroup&groupid=".$row['ID'], $row['groupname'], "BoardColumn")."</A></TD></TR>\n"
                         ."</TABLE>\n";
           }
         }
         elseif ($listtype == "admin")
         {
           if (($opengroup == $row['ID']) || ($opengroup == "all"))
           {
             $boards = listBoards($row['ID'], "", "" , $row['groupname']);
             
             $boarddata = $boards['boarddata'];

             $boardrow = "";
             for ($i = 0; $i < count($boarddata); $i++ )
             {
               $boardrow .= "<TR><TD CLASS='BoardRowBody' COLSPAN=2>&nbsp;</TD>\n"
                           ."    <TD CLASS='BoardRowBody' WIDTH=5%>".$boarddata[$i]['ID']."</TD>\n"
                           ."    <TD CLASS='BoardRowBody'>".applyOnlyTextEffects($boarddata[$i]['boardname'])."</TD>\n"
                           ."    <TD CLASS='BoardRowBody'><A HREF='$PHP_SELF?action=managestructure&step=editboard&boardid=".$boarddata[$i]['ID']."'>Edit</A></TD>\n"
                           ."    <TD CLASS='BoardRowBody'><A HREF='$PHP_SELF?action=managestructure&step=removeboard&boardid=".$boarddata[$i]['ID']."'>Remove</A></TD></TR>\n";
             }
           }
           $grouprow .= "<TR><TD CLASS='BoardRowHeading' ALIGN=CENTER>".$row['ID']."</TD>\n"
                       ."    <TD CLASS='BoardRowHeading' ALIGN=CENTER>".$row['grouprank']."</TD>\n"
                       ."    <TD CLASS='BoardRowHeading' COLSPAN=2><B>".$row['groupname']."</B></TD>\n"
                       ."    <TD CLASS='BoardRowHeading' WIDTH=10%><A HREF='$PHP_SELF?action=managestructure&step=2&groupid=".$row['ID']."'>Edit</A></TD>\n"
                       ."    <TD CLASS='BoardRowHeading' WIDTH=10%><A HREF='$PHP_SELF?action=managestructure&step=remove&groupid=".$row['ID']."'>Remove</A></TD></TR>\n"
                       .$boardrow
                       ."<TR><TD COLSPAN=2 CLASS='BoardRowBody'>&nbsp;</TD>\n"
                       ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>&raquo;</TD>\n"
                       ."    <TD COLSPAN=3 CLASS='BoardRowBody'><A HREF='$PHP_SELF?action=managestructure&step=editboard&groupid=".$row['ID']."'>Create New Board in <I>".$row['groupname']."</I></A></TD></TR>\n";
         }
       }

       // If a user is logged in, and we're not in admin mode, get their favourite boards
       if (($userloggedin) && (!$listtype) && (($opengroup == "all") || (!$opengroup)))
       {
         $favourites = listBoards(0, $userdata['ID'], "", "Your Favourite Boards");
         
         // Only show Your Favourite Boards if they HAVE favourite boards
         if ($favourites['boardcount'])
         {
           $favouritesoutput = $favourites['boardlist'];
         }
       }
     }
     else
     {
       if ($listtype == "admin")
       {
         $grouprow = "<TR><TD COLSPAN=4 ALIGN=CENTER>\n"
                    ."        <BR>\n"
                    ."        No groups / boards found.<BR>\n"
                    ."        <BR></TD></TR>\n";
       }
       else
       {
         $grouprow = "<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1 BORDER=0>\n"
                    ."<TR><TD COLSPAN=4 ALIGN=CENTER>\n"
                    ."        <BR>\n"
                    ."        No groups / boards found.<BR>\n"
                    ."        <BR></TD></TR>\n"
                    ."</TABLE>\n";
       }
     }
     
     if ($listtype == "admin")
     {
       $tablestart = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n";
       $tablestop  = "</TABLE>\n";
       if (resultCount($exe))
       {
         $headings = "<TR><TD CLASS='BoardColumn' WIDTH=5% ALIGN=CENTER>Group</TD>\n"
                    ."    <TD CLASS='BoardColumn' WIDTH=5% ALIGN=CENTER>Rank</TD>\n"
                    ."    <TD CLASS='BoardColumn' COLSPAN=2 WIDTH=70%>Group / Board Name</TD>\n"
                    ."    <TD CLASS='BoardColumn' COLSPAN=2 WIDTH=20%>Options</TD></TR>\n";
       }
     }
     
     $groupoutput = $favouritesoutput
                   .$tablestart
                   .$headings
                   .$grouprow
                   .$tablestop;
     
     return $groupoutput;
   }
   
   Function listBoards($groupid, $favouritesuser = "", $options = "", $boardnameheading = "")
   {
     global $PHP_SELF;
     
     if ($favouritesuser)
     {
       // The user is only looking at their favourite boards
       $favouritetable     = ", ".TABLE_FAVOURITES." f ";
       $favouritecondition = "   AND f.ownerid = $favouritesuser "
                            ."   AND b.ID = f.boardid ";
     }
     else
     {
       $favouritecondition = "   AND b.groupid = $groupid ";
     }
     
     $sql = "SELECT g.ID as groupid, g.groupname, b.ID, b.boardname, b.description, b.private, COUNT(t.ID) AS topics, SUM(t.postcount) AS messages, p.ID as lastpostid, p.postdate "
           ."  FROM ".TABLE_GROUPS." g, ".TABLE_BOARDS." b, ".TABLE_THREADS." t, ".TABLE_POSTS." p".$favouritetable
           ." WHERE t.boardid = b.ID "
           ."   AND p.ID = b.postidlast "
           ."   AND g.ID = b.groupid "
           .$favouritecondition
           ." GROUP BY boardname "
           ." ORDER BY boardrank ";
     $exe = runQuery($sql);
     
     $resultcount = resultCount($exe);
     if ($resultcount)
     {
       while ($row = fetchResultArray($exe))
       {
         $result[] = $row;
         
         $viewlink  = "board.php?boardid=".$row['ID']."&lastpostid=".$row['lastpostid'];
         $grouplink = $PHP_SELF."?action=viewgroup&groupid=".$row['groupid'];
         
         // This gets passed back to the caller to do with as it sees fit
         $boardgroupname = $row['groupname'];
         
         if ($favouritesuser)
         {
           // Also output the groupname
           $groupcolumn = "    <TD CLASS='BoardRowBody'>".makeLink($grouplink, applyOnlyTextEffects($row['groupname']), "BoardRowHeadingLink")."</A></TD>\n";
         }
         
         if ($options['extracolcontent'])
         {
           $extracolumn = "    <TD CLASS='BoardRowBody'>".str_replace("BOARDID", $row['ID'], $options['extracolcontent'])."</TD>\n";
         }
       
         if (!$favouritesuser)
         {
	     if ($row['private']){
		   $descriptionoutput = "<span style='font-size: 8pt;'>".$row['description']."</span>";
           if ($row['description'])
           {
             $descriptionoutput = "<SPAN STYLE='font-size: 8pt;'>".$row['description']."</SPAN>";
           }
           }
else
{
             $descriptionoutput = "";
           if ($row['description'])
           {
             $descriptionoutput = "<SPAN STYLE='font-size: 8pt;'>".$row['description']."</SPAN>";
           }
           }
         }
else
{
	     if ($row['private']){
             $descriptionoutput = "<span style='font-size: 10pt;'>".$row['groupname']."</span>";
           }
else
{
             $descriptionoutput = "<span style='font-size: 10pt;'>".$row['groupname']."</span>";
           }

         }

if($row['private'] == 0)
{
         $grouprow .= "<TR><TD CLASS='BoardRowHeading' width='1%'>".makeLinkmark($viewlink, applyOnlyTextEffects($row['boardname']), "MarkerLink")."</TD>\n"
		     ."<TD CLASS='BoardRowBody'>".makeLink($viewlink, applyOnlyTextEffects($row['boardname']), "SubjectLink")."<BR>".$descriptionoutput."</TD>\n"
                     ."    <TD CLASS='BoardRowHeading'>".number_format($row['topics'])."</TD>\n"
                     ."    <TD CLASS='BoardRowHeading'>".number_format($row['messages'])."</TD>\n"
                     ."    <TD CLASS='BoardRowBody'>".makeLink($viewlink, dateNeat($row['postdate']), "BoardRowHeadingLink")."</A></TD>\n"
                     .$extracolumn
                     ."    </TR>\n";
}
elseif($row['private'] == 1)
{
	if(checkAccess("accessvip")){
         $grouprow .= "<TR><TD CLASS='BoardRowHeading' width='1%'>".makeLinkmark($viewlink, applyOnlyTextEffects($row['boardname']), "MarkerLink")."</TD>\n"
		     ."<TD CLASS='BoardRowBody'>".makeLink($viewlink, applyOnlyTextEffects($row['boardname']), "SubjectLink")."<BR><I><U>Private Board</U> - </I>".$descriptionoutput."</TD>\n"
                     ."    <TD CLASS='BoardRowHeading'>".number_format($row['topics'])."</TD>\n"
                     ."    <TD CLASS='BoardRowHeading'>".number_format($row['messages'])."</TD>\n"
                     ."    <TD CLASS='BoardRowBody'>".makeLink($viewlink, dateNeat($row['postdate']), "BoardRowHeadingLink")."</A></TD>\n"
                     .$extracolumn
                     ."    </TR>\n";
	}
else
{
		$grouprow .="";
	}
}
elseif($row['private'] == 2)
{
	if(checkAccess("accessinsider")){
         $grouprow .= "<TR><TD CLASS='BoardRowHeading' width='1%'>".makeLinkmark($viewlink, applyOnlyTextEffects($row['boardname']), "MarkerLink")."</TD>\n"
		     ."<TD CLASS='BoardRowBody'>".makeLink($viewlink, applyOnlyTextEffects($row['boardname']), "SubjectLink")."<BR><I><U>Insider Board</U> - </I>".$descriptionoutput."</TD>\n"
                     ."    <TD CLASS='BoardRowHeading'>".number_format($row['topics'])."</TD>\n"
                     ."    <TD CLASS='BoardRowHeading'>".number_format($row['messages'])."</TD>\n"
                     ."    <TD CLASS='BoardRowBody'>".makeLink($viewlink, dateNeat($row['postdate']), "BoardRowHeadingLink")."</A></TD>\n"
                     .$extracolumn
                     ."    </TR>\n";
	}
else
{
		$grouprow .="";
	}
}
else
{
         $grouprow .= "<TR><TD CLASS='BoardRowHeading' width='1%'>".makeLinkmark($viewlink, applyOnlyTextEffects($row['boardname']), "MarkerLink")."</TD>\n"
		     ."<TD CLASS='BoardRowBody'>".makeLink($viewlink, applyOnlyTextEffects($row['boardname']), "SubjectLink")."<BR>".$descriptionoutput."</TD>\n"
                     ."    <TD CLASS='BoardRowHeading'>".number_format($row['topics'])."</TD>\n"
                     ."    <TD CLASS='BoardRowHeading'>".number_format($row['messages'])."</TD>\n"
                     ."    <TD CLASS='BoardRowBody'>".makeLink($viewlink, dateNeat($row['postdate']), "BoardRowHeadingLink")."</A></TD>\n"
                     .$extracolumn
                     ."    </TR>\n";
}}}

     $boardnamewidth   = 65;
     //$boardnameheading = "Board Name";
     if ($favouritesuser)
     {
       // Also output the groupname
       $boardnamewidth   = 65;
       //$boardnameheading = "Your Favourite Boards";
       //$groupheading     = "    <TD WIDTH=25% CLASS='BoardColumn'>Group</A></TD>\n";
     }
     if ($extracolumn)
     {
       $extraheading = "    <TD CLASS='BoardColumn'>".$options['extracolheading']."</TD>\n";
     }
     $boardoutput = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
                   ."<TR><TD colspan='2' WIDTH=".$boardnamewidth."% CLASS='BoardColumn'>".$boardnameheading."</TD>\n"
                   .$groupheading
                   ."    <TD WIDTH=10% CLASS='BoardColumn'>Topics</TD>\n"
                   ."    <TD WIDTH=10% CLASS='BoardColumn'>Messages</TD>\n"
                   ."    <TD WIDTH=15% CLASS='BoardColumn'>Last Post</TD>\n"
                   .$extraheading
                   ."    </TR>\n"
                   .$grouprow
                   ."</TABLE>\n";
     
     $return['boardlist']  = $boardoutput;
     $return['groupname']  = $boardgroupname;
     $return['boardcount'] = $resultcount;
     $return['boarddata']  = $result;
     
     return $return;
   }


   
   Function viewBoard($boardid, $pagenumber)
   {
     global $PHP_SELF;
     global $configoptions;
     global $GFXRoot;
     
     if (!$pagenumber) { $pagenumber = 1; }

     $sql = "SELECT COUNT(t.ID) AS result"
           ."  FROM ".TABLE_THREADS." t, ".TABLE_USERS." u, ".TABLE_POSTS." fp, ".TABLE_POSTS." lp "
           ." WHERE fp.ID = t.postidfirst "
           ."   AND lp.ID = t.postidlast "
           ."   AND u.ID = fp.authorid "
           ."   AND t.boardid = $boardid ";
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       $resultcount = $row['result'];
     }
     //echo "Found ".$resultcount." posts in this thread<BR>\n";
     
     $startrow = (($pagenumber*$configoptions['perpagethreads']) - $configoptions['perpagethreads']);
     $pageinfovars['currentpage']    = $pagenumber;
     $pageinfovars['startrow']       = $startrow;
     $pageinfovars['totalrows']      = $resultcount;
     $pageinfovars['pagename']       = "board.php";
     $pageinfovars['linkparameters'] = "&boardid=".$boardid;

     $pagination = getPaginationInformation($pageinfovars, "thread");

     $sql = "SELECT t.ID, t.sticky, t.status, t.viewcount, t.postcount, t.oldboardid, "
           ."       fp.subject, fp.authorid, fp.pollid, LENGTH(fp.body) AS postlength, "
           ."       fp.authorid as firstpostuserid, "
           ."       ufp.displayname as firstpostusername, "
           ."       ufp.displayformat as firstpostusernameformat, "
           ."       fp.postdate AS firstpostdate, "
           ."       lp.ID AS lastpostid, "
           ."       lp.authorid AS lastpostuserid, "
           ."       ulp.displayname as lastpostusername, "
           ."       ulp.displayformat as lastpostusernameformat, "
           ."       lp.postdate AS lastpostdate"
           ."  FROM ".TABLE_THREADS." t, ".TABLE_USERS." ufp, ".TABLE_USERS." ulp, ".TABLE_POSTS." fp, ".TABLE_POSTS." lp "
           ." WHERE fp.ID = t.postidfirst "
           ."   AND lp.ID = t.postidlast "
           ."   AND ufp.ID = fp.authorid "
           ."   AND ulp.ID = lp.authorid "
           ."   AND t.boardid = $boardid "
           ." ORDER BY sticky DESC, lastpostdate DESC"
           ." LIMIT ".intval($startrow).", ".intval($configoptions['perpagethreads']);
     $exe = runQuery($sql);

     if (resultCount($exe))
     {
       $boardoutput = formatThreadList($exe);
     }

     $return['boardoutput'] = $boardoutput['html'];
     $return['navbar']      = $pagination['newnavigation'];
     
     return $return;
   }
   
   Function formatThreadList($exe, $options = "")
   {
     global $GFXRoot;
     global $configoptions;
     global $PHP_SELF;
     
     $i = 0;
     while ($row = fetchResultArray($exe))
     {
       $i++;
       
       // Decide what the leading bullet to display for this thread is:
       // 2 character slots:
       //  - char 1: • for normal post, ? for a poll, and ! for a sticky note
       //  - char 2: " " normally, "-" for a locked topic
       // 2 formatting slots for stuff to decorate the subject line with
       //  - formatpre: opening tag
       //  - formatpost: closing tag
       $leadingbullet = "•";
       $leadingspacer = " ";
       $formatpre     = "";
       $formatpost    = "";

       $displayclass  = "";

       if ($row['status'] == "L")
       {
         // Thread is locked, set the spacer to a -
         $leadingspacer = " ";
         $formatpre = "<SPAN STYLE='text-decoration: line-through;'>\n";
         $formatpost = "</SPAN>\n ";
       }
       
       if ($row['sticky'])
       {
         // Sticky thread, use a !
         $leadingbullet = "<IMG SRC='".$GFXRoot."/sticky.gif' WIDTH=18 HEIGHT=18 ALT='[Sticky Thread]' ALIGN=ABSMIDDLE>";
         
         // Set displayclass to "BoardRowBodySticky"
         $displayclass = "BoardRowBodySticky";
       }
       
       if ($row['pollid'])
       {
         // Poll, use a ?
         $leadingbullet = "<IMG SRC='".$GFXRoot."/poll.gif' WIDTH=18 HEIGHT=18 ALT='[Poll]' ALIGN=ABSMIDDLE>";
       }
       
       // Append char 2 (the spacer) to it.
       $leadingbullet .= $leadingspacer;
       
       // If the postcount is higher than the user's current page count, do pagination
       $mininavigation = "";
       if($row['postcount'] > $configoptions['perpageposts'])
       {
         $minipagination['startrow']       = 1;
         $minipagination['currentpage']    = 1;
         $minipagination['totalrows']      = $row['postcount'];
         $minipagination['pagename']       = "thread.php";
         $minipagination['linkparameters'] = "threadid=".$row['ID']."&lastpostid=".$row['lastpostid'];
         
         $minipagination = getPaginationInformation($minipagination, "posts");
         $mininavigation = " <SPAN STYLE='font-size: 7pt;'>".$minipagination['mininavigation']."</SPAN>";
       }
       
       if (!$displayclass)
       {
         $rowdisplayclass = "BoardRowBody";
       }
       else
       {
         $rowdisplayclass = $displayclass;
       }
       
       // Dunno if we really want to indicate a thread was moved...
       //$moved = "";
       //if ($row['oldboardid'])
       //{
       //  $moved = "<SPAN STYLE='font-size: 8pt;'><B CLASS='red'>&nbsp;Moved</B></SPAN>";
       //}
       
       if ($options['extracol'] == "checkbox")
       {
         $checkboxcol = "    <TD ALIGN=CENTER>".inputCheckbox("select".$i, $row['ID'])."</TD>\n";
         $checkboxcount++;
       }
       
       // Do we show "Date by X" or "Date" for the time/date of the last post
       $lastpostinfo = "<SPAN STYLE='font-size: 8pt;'>".dateNeat($row['lastpostdate'])."</SPAN>";
       if ($configoptions['showlastpostername'])
       {
         // Stop usernameDisplay() going to the database again
         $lastpostauthordisplaydata['userid']        = $row['lastpostuserid'];
         $lastpostauthordisplaydata['displayformat'] = $row['lastpostusernameformat'];
         $lastpostauthordisplaydata['displayname']   = $row['lastpostusername'];
         $lastpostusername = usernameDisplay("", "AuthorLinkSmall", "", $lastpostauthordisplaydata);
         $lastpostinfo = "<SPAN STYLE='font-size: 8pt;'>".dateNeat($row['lastpostdate'])." by ".$lastpostusername."</SPAN>";
       }
       
       // Stop usernameDisplay() going to the database again
       $authordisplaydata['userid']        = $row['firstpostuserid'];
       $authordisplaydata['displayformat'] = $row['firstpostusernameformat'];
       $authordisplaydata['displayname']   = $row['firstpostusername'];
       $authorusername = usernameDisplay("", "AuthorLink", "", $authordisplaydata);
       
       $notextmarker = "";
       if ($row['postlength'] < 1)
       {
         $notextmarker = " (nt)";
       }
       
       $viewlink = "thread.php?threadid=".$row['ID']."&lastpostid=".$row['lastpostid'];
       $boarddatarows .= "<TR><TD CLASS='".$rowdisplayclass."'>".$formatpre.$leadingbullet.makeLink($viewlink, ProfanityFilter(applyOnlyTextEffects($row['subject'])), "SubjectLink").$notextmarker.$formatpost.$mininavigation.$moved."</A></TD>\n"
                        ."    <TD CLASS='BoardRowHeading'>".$authorusername."</TD>\n"
                        ."    <TD ALIGN=CENTER CLASS='".$rowdisplayclass."'>".number_format($row['viewcount'])."</TD>\n"
                        ."    <TD ALIGN=CENTER CLASS='".$rowdisplayclass."'>".number_format($row['postcount']-1)."</TD>\n"
                        ."    <TD CLASS='BoardRowHeading'>".$lastpostinfo."</TD>\n"
                        .$checkboxcol
                        ."    </TR>\n";
     }

     if ($options['extracol'] == "checkbox")
     {
       $checkboxhead = "    <TD ALIGN=CENTER CLASS='BoardColumn'>?</TD>\n";
       $submitbutton = "Remove";
       if ($options['submitbutton'])
       {
         $submitbutton = $options['submitbutton'];
       }
       $lastrow = "<TR ALIGN=CENTER>\n"
                 ."    <TD COLSPAN=5 CLASS='BoardColumn'>&nbsp;</TD>\n"
                 ."    <TD CLASS='BoardColumn'>".inputSubmit($submitbutton)."</TD></TR>\n";
     }

     $boardoutput = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
                   ."<TR><TD WIDTH=55% CLASS='BoardColumn'>Topic</TD>\n"
                   ."    <TD WIDTH=15% CLASS='BoardColumn'>Author</TD>\n"
                   ."    <TD ALIGN=CENTER WIDTH=7% CLASS='BoardColumn'>Views</TD>\n"
                   ."    <TD ALIGN=CENTER WIDTH=8% CLASS='BoardColumn'>Replies</TD>\n"
                   ."    <TD WIDTH=15% CLASS='BoardColumn'>Last Post</TD>\n"
                   .$checkboxhead
                   ."</TR>\n"
                   .$boarddatarows
                   .$lastrow
                   ."</TABLE>\n";

     $return['html'] = $boardoutput;
     $return['checkboxcount'] = $checkboxcount;
     
     return $return;
   }
   
   Function viewThread ($threadid, $pagenumber = 1)
   {
     global $PHP_SELF;
     global $configoptions;
     
     $locked = isLocked($threadid);
     
     if (!$pagenumber) { $pagenumber = 1; }
     
     // Fetch the post count
     $sql = "SELECT COUNT(*) AS result "
           ."  FROM ".TABLE_POSTS." p, ".TABLE_USERS." u "
           ." WHERE u.ID = p.authorid "
           ."   AND p.threadid = $threadid";
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       $resultcount = $row['result'];
     }
     //echo "Found ".$resultcount." posts in this thread<BR>\n";
     
     $startrow = (($pagenumber*$configoptions['perpageposts']) - $configoptions['perpageposts']);
     $pageinfovars['currentpage']    = $pagenumber;
     $pageinfovars['startrow']       = $startrow;
     $pageinfovars['totalrows']      = $resultcount;
     $pageinfovars['pagename']       = "thread.php";
     $pageinfovars['linkparameters'] = "&threadid=".$threadid;

     $pagination = getPaginationInformation($pageinfovars, "posts");

     //echo "Found ".$pagination['pagecount']." pages of ".$configoptions['perpageposts']." posts each<BR>\n";
     //echo "<TABLE BORDER=1>\n"
     //    ."<TR><TD WIDTH=25>".$pagination['firstlink']."</TD>\n"
     //    ."    <TD WIDTH=25>".$pagination['prevlink']."</TD>\n"
     //    ."    <TD WIDTH=25>".$pagination['nextlink']."</TD>\n"
     //    ."    <TD WIDTH=25>".$pagination['finallink']."</TD></TR>\n"
     //    ."<TR><TD COLSPAN=4>".$pagination['newnavigation']."</TD>\n"
     //    ."</TABLE>\n";
     // Fetch the data
     
     $sql = "SELECT p.ID, p.threadid, p.postdate, p.authorid, p.subject, p.body, "
           ."       p.edituserid, p.editdate, p.editcount, p.pollid, p.ipaddress, "
           ."       u.displayname, u.displayformat, u.postcount, u.created, "
           ."       u.sig1, u.sig2, u.sig3, u.sig4, u.sig5, u.title, "
           ."       a.classname AS userclassname "
           ."  FROM ".TABLE_POSTS." p, ".TABLE_USERS." u, ".TABLE_ACCESS_CLASS." a "
           ." WHERE u.ID = p.authorid "
           ."   AND u.classid = a.ID "
           ."   AND p.threadid = $threadid"
           ." ORDER BY ID ASC"
           ." LIMIT ".intval($startrow).", ".intval($configoptions['perpageposts']);
     $exe = runQuery($sql);
     
     if (resultCount($exe))
     {
       while ($row = fetchResultArray($exe))
       {
         $i++;
         $row['counter'] = $i;
         
         if (!$maintopic)
         { $maintopic = $row['subject']; }
         
         // Pass in the locked state so that formatPost() doesn't display "post reply"
         $extra['locked'] = $locked;
         $threadrow .= formatPost($row, "", $extra);
       }
     }
     
       
     if($extra['locked'])
    {
	     $quickout="<TR><TD WIDTH=20% CLASS='BoardRowBody'></TD><TD WIDTH=80% CLASS='BoardRowBody'><div id='quickreply' style='display: none;'><B CLASS='red'>Sorry This Thread Is Locked</B></div></td></tr>";
    } else {
		 $quickout= "<TR style><TD WIDTH=20% CLASS='BoardRowBody'></TD><TD WIDTH=80% CLASS='BoardRowBody'><div id='quickreply' style='display: none;'><FORM ACTION='post.php' METHOD=POST>\n"
                .inputHidden("action", "process")
                .inputHidden("threadid", $threadid)
                .inputHidden("boardid", $boardid)
                .inputHidden("post", $post)
                .inputHidden("subject", "".$maintopic."")
				."<H3>Quick Reply</H3>"
                ."<TEXTAREA NAME='body' WRAP=VIRTUAL  ROWS=5  COLS=50></TEXTAREA>"
                ."<br><INPUT TYPE=SUBMIT VALUE=' Post ' CLASS='button' NAME='replyButton'><INPUT TYPE=SUBMIT VALUE='Preview' CLASS='button' NAME='button'>"
                ."</form></div></TD></TR>\n";
            }
$threadoutput = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
                    ."<TR><TD WIDTH=20% CLASS='BoardColumn'>Author</TD>\n"
                    ."    <TD WIDTH=80% CLASS='BoardColumn'>Topic: ".ProfanityFilter(applyOnlyTextEffects($maintopic))."</TD></TR>\n"
				   .$threadrow
				   .$quickout
                   ."</TABLE>\n";
     
     $return['threadoutput'] = $threadoutput;
     $return['navbar']       = $pagination['newnavigation'];
     $return['threadlocked'] = $locked;

     return $return;
   }
   
   Function isEditable($postdata)
   {
     global $GraceTime;
     global $userdata;
     
     $accesstimeedit = checkAccess("accesstimeedit");
     $accessfulledit = checkAccess("accessfulledit");
     
     //echo "UID $userdata[ID], AuthorID $postdata[authorid], TimeEdit $accesstimeedit, FullEdit $accessfulledit<BR>\n";
     
     $editable = 0;
     
     if ( (($userdata['ID'] == $postdata['authorid']) && ($accesstimeedit)) || 
          ($accessfulledit) 
        )
     {
       // How long since the message was posted?
       $now = Date("U");
       $ninetyminsago = ($now - ($GraceTime * 60));
       
       $difference = $now - $postdata['postdate'];
       $diffmins = intval($difference/60);
       
       //echo "DiffMins = $diffmins, Grace $GraceTime<BR>\n";
       
       // Let them edit this post if they have timeedit access and its within the grace period,
       // or they have edit access.
       if ( (($accesstimeedit) && ($diffmins <= $GraceTime)) || 
            ($accessfulledit)
          )
       {
         // How long left to edit this post?
         $minsremaining = ($GraceTime - $diffmins);
         
         if (!checkAccess("accessfulledit"))
         {
           $timeinformation = "<SPAN STYLE='font-size: 8pt;'>(".$minsremaining." minutes left)</SPAN>";
         }
         
         // Then, it can be edited
         $editable = 1;
       }
     }
     
     $return['editable'] = $editable;
     $return['timeinfo'] = $timeinformation;
     
     //print_r($return);
     
     return $return;
   }
   
   Function formatPost ($postdata, $displaymode = "public", $extra = "")
   {
     global $mysql;
     global $userdata;
     global $GraceTime;
     global $HiddenClassNames;
     global $PHP_SELF;
     global $querystring;
     
     if (!$displaymode) { $displaymode = "public"; }
     
     // Get this user's icon
     $icon = getIcon($postdata['authorid']);
if (!$icon && $userdata['displayname'] ==  $postdata['displayname'])
{
$icon = "<a href='icon.php'>[Your Icon Here]</a><br><br><br>";
}
     
     // Output their signature neatly (if its set)
     if ($postdata['sig1'])
     {
       for ($i = 0; $i <5; $i++ )
       {
         $sigline = "sig".($i+1);
         if ($postdata[$sigline])
         {
	global $AllowSigMarkup;
	if ($AllowSigMarkup)
	{
           $sigdata .= bodyText($postdata[$sigline])."<BR>\n";
	}  
	else
	{
	$sigdata .= $postdata[$sigline]."<BR>\n";       
	}
         }
       }
       $signature = "<SPAN CLASS='SignatureTitle'>-----signature-----</SPAN>\n"
						       ."<DIV CLASS='SignatureText'>\n"
                   .$sigdata
                   ."</DIV>";
     }
     
     // Check to see if the user has access to edit this post
     $editinformation = isEditable($postdata);
     if ($editinformation['editable'])
     {
       // They do have the ability to edit this post, so display the edit link
       // $editinformation['timeinfo'] tells the user how long they have left to edit
       // the message if applicable ... if n/a, it's empty.
       $editlink = makeLink("postedit.php?postid=".$postdata['ID'], "Edit Message")." " 
                  .$editinformation['timeinfo']." | \n";
     }

     // Check to see if the user has access to delete this post (moderator access required)
     if (checkAccess("accessmoderator"))
     {
       // Custom JavaScript confirmation on this link asks the user to confirm deletion
       $linkextra['onclick'] = "return confirmLink(this, 'delete this message?')";
       $deletelink = makeLink("moderators.php?action=deletepost&postid=".$postdata['ID'], "Delete Message", "X", $linkextra)." | \n";
     }
     
     // If they have moderator access, show them the IP this post came from
     if (checkAccess("accessmoderator"))
     { 
       $ipaddressoutput = makeLink("ip.php?ip=".urlencode($postdata['ipaddress'])."&action=findipusers", $postdata['ipaddress'])." | \n";
     }

     // Sometimes we need to output the message number, too
     $messagenumber = "Message ID: ".$postdata['ID'];

     // Link to add the thread to the user's watch list
     $watchlink = makeLink("watch.php?action=addthread&threadid=".$postdata['threadid']."&returnurl=".urlencode($PHP_SELF.$querystring), "Watch Thread");

     // Decide what controls to display based on the mode we're running in
     switch ($displaymode)
     {
       case "public":
         // If the message is public, display:
         // IP ADDRESS | EDIT LINK | DELETE LINK | REPLY LINK | QUICK REPLY LINK | WATCH LINK
   $threaddata = fetchRow($postdata['threadid'], TABLE_THREADS);
   $boarddata  = fetchRow($threaddata['boardid'], TABLE_BOARDS);

   $sql = "SELECT vippost FROM " . TABLE_BOARDS . " WHERE ID = '" . $threaddata['boardid'] . "'";
   $exe = mysqli_query($mysql, $sql);
   $row = mysqli_fetch_array($exe);
$vippost = $row['vippost'];
         if (!$extra['locked'])
         {
	if (!$vippost || checkAccess("accessvip"))
	{
           $replylink = makeLink("post.php?threadid=".$postdata['threadid']."&post=".$postdata['ID'], "Post Reply");
	}
         }
         else
         {
           $replylink = "<B CLASS='red'>Locked</B>";
         }

global $AllowQuickReply;
   $threaddata = fetchRow($postdata['threadid'], TABLE_THREADS);
   $boarddata  = fetchRow($threaddata['boardid'], TABLE_BOARDS);

   $sql = "SELECT vippost FROM " . TABLE_BOARDS . " WHERE ID = '" . $threaddata['boardid'] . "'";
   $exe = mysqli_query($mysql, $sql);
   $row = mysqli_fetch_array($exe);
$vippost = $row['vippost'];
		if ((!$extra['locked']) && ($AllowQuickReply))
		{
			if (!$vippost || checkAccess("accessvip"))
			{
			$quickreply = "<A HREF='#quickreply' CLASS='BoardRowBodyLink' TITLE='Quick Reply' onclick='quickreply()'>Quick Reply</a> | ";
			}
		}
		else
		{
			$quickreply = "";
		}

		if ($extra['locked'] && checkAccess("accessmoderator"))
		{
			$lockedlink = makeLink("post.php?threadid=".$postdata['threadid']."&post=".$postdata['ID'], "Moderator Reply");
		}
		else
		{
			$lockedlink = "";
		}

         $controls = $ipaddressoutput
                    .$editlink
                    .$deletelink
                    .$replylink." | \n"
                    .$quickreply
                    .$watchlink." | \n"
					.$lockedlink;
         break;
       case "private":
         // If the message is private, then display:
         //  MESSAGE NUMBER | REPLY OPTION
         $replylink = makeLink("private.php?action=send&recipient=".urlencode($postdata['displayname'])."&originalsubject=".urlencode($postdata['subject']), "Send Reply");
         $controls = $ipaddressoutput
                    .$messagenumber." | \n"
                    .$replylink;
         break;
       case "administrator":
         if ($postdata['recipientid'])
         {
           $msgtype = "This PM was sent to ".usernameDisplay($postdata['recipientid']);
         }
         else
         {
           $msgtype = "Public message";
         }
         $controls = $ipaddressoutput
                    .$messagenumber." | \n"
                    .$msgtype;
     }

     // If the post has been edited, show them the appropriate edit information
     if ($postdata['editcount'])
     {
       $posteditedinformation = "<SPAN STYLE='font-weight: bold;'>Last Edit:</SPAN> <SPAN STYLE='font-weight: normal;'>".dateNeat($postdata['editdate'])." by ".usernameDisplay($postdata['edituserid'])." \n"
                               ."<SPAN STYLE='font-size: 8pt;'>(".intval($postdata['editcount'])." edits total)</SPAN></SPAN>\n"
                               ."";
     }
     //$debugdata = "$postdata[counter] ($postdata[ID])";
     
     // Show the text by default, but if its a poll then add the poll output
     if ($postdata['pollid'])
     {
       $bodytext = showPoll($postdata['pollid']);
     }
     $bodytext .= bodyText(ProfanityFilter($postdata['body']), $postdata['displayname']);
     
     // Don't display the user class if its one of the $nodisplayclasses classes
     if (!in_array($postdata['userclassname'], $HiddenClassNames))
     {
       $classnamedisplay = "<SPAN STYLE='font-weight: bold;'>".$postdata['userclassname']."</SPAN><BR>";
     }
     
     $titledisplay = "";
     if ($postdata['title'])
     {
       $titledisplay = "        <SPAN STYLE='font-weight: bold;'>Title:</SPAN> ".applyOnlyTextEffects($postdata['title'])."<BR>\n";
     }
     
     $authordisplay['userid']        = $postdata['authorid'];
     $authordisplay['displayname']   = $postdata['displayname'];
     $authordisplay['displayformat'] = $postdata['displayformat'];
     $authordisplay['postcount']     = $postdata['postcount'];
     
     // Final table row(s) we output here
     $output = "<TR VALIGN=TOP>\n"
              ."    <TD WIDTH=20% ROWSPAN=3 CLASS='BoardRowHeading'>\n"
              ."        ".$debugdata.usernameDisplay("", "", "showstar", $authordisplay)."<BR>\n"
              ."        <SPAN STYLE='font-size: 8pt;'>".$classnamedisplay."\n"
              .$titledisplay
              ."        <SPAN STYLE='font-weight: bold;'>Posts:</SPAN> ".number_format($postdata['postcount'])."<BR>\n"
              ."        <SPAN STYLE='font-weight: bold;'>Registered:</SPAN> ".Date("M y", $postdata['created'])."<BR>\n"
              ."        </SPAN><BR>\n"
              ."        ".$icon."</TD>\n"
              ."    <TD WIDTH=80% CLASS='BoardRowHeading'>\n"
              ."        <SPAN STYLE='font-weight: bold;'>Date Posted:</SPAN> <SPAN STYLE='font-weight: normal;'>".dateNeat($postdata['postdate'])."</SPAN>\n"
              ."        <SPAN STYLE='font-weight: bold;'>Subject:</SPAN> <SPAN STYLE='font-weight: normal;'>".ProfanityFilter(applyOnlyTextEffects($postdata['subject']))."</SPAN>\n"
              ."        ".$posteditedinformation."</TD></TR>\n"
              ."<TR><TD CLASS='BoardRowBody'>\n"
              //."        ".word_wrap($postdata['body'], 78, "\n", "        ")."</TD></TR>\n"
              ."        ".$bodytext."<BR><BR>\n"
              ."        ".ProfanityFilter($signature)."</TD></TR>\n"
              ."<TR><TD CLASS='BoardRowBody' ALIGN=RIGHT>\n"
              ."        ".str_replace("\n", "\n        ", $controls)."</TD></TR>\n";
     return $output;
   }
   
   Function showPoll($pollid)
   {
     global $PHP_SELF;
     global $querystring;
     global $userdata;
     
     $poll = fetchRow($pollid, TABLE_POLLS);
     
     $question = $poll['question'];
     
     // Fetch all responses
     $sql = "SELECT DISTINCT optionid, COUNT(optionid) AS result "
           ."  FROM ".TABLE_POLL_RESPONSES." "
           ." WHERE pollid = $pollid"
           ." GROUP BY optionid ";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $optionid = $row['optionid'];
         $optcount = $row['result'];
         //echo "$optionid = $optcount<BR>\n";
         
         // Populate the result array - the key is the option's ID
         $result[$optionid] = $optcount;
         $responsecount = $responsecount + $optcount;
         
         if ($optcount >= $winningoptcount)
         { 
           $pollvotes[$optionid] = $optcount;
           $winningoption[] = $optionid;
           $winningoptcount = $optcount;
         }
         $lastoptcount = $optcount;
       }
       
       // No rows were returned, no votes were cast, no option's winning
       if (!resultCount($exe))
       {
         $winningoption[] = 0;
       }
     }
     
     if ($poll['expirydate'] < Date("U"))
     {
       // Poll has expired
       $expirymessage = "<SPAN STYLE='font-size: 8pt;'>Voting on this poll closed at ".dateNeat($poll['expirydate'], "datetime")."</SPAN>\n";
     }
     else
     {
       // Do this if they haven't voted
       if (!checkPollVote($pollid, $userdata['ID']))
       {
           $expirymessage = "<SPAN STYLE='font-size: 8pt;'>Voting ends: ".dateNeat($poll['expirydate'], "datetime")."</SPAN>\n";
           $formtop = "<FORM ACTION='poll.php' METHOD=POST>\n"
                     .inputHidden("action", "vote")
                     .inputHidden("pollid", $pollid)
                     .inputHidden("returnurl", $PHP_SELF.$querystring);
           $formend = inputSubmit("Vote Now")
                     ."</FORM>\n";
       }
       else
       {
         $expirymessage = "<SPAN STYLE='font-size: 8pt;'>You have already voted on this topic. Voting ends: ".Date("d M Y H:i", $poll['expirydate'])."</SPAN>\n";
       }
     }
     
     // Fetch possible answers
     $sql = "SELECT ID, optionname "
           ."  FROM ".TABLE_POLL_OPTIONS." "
           ." WHERE pollid = $pollid";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $i++;
         $optionid = $row['ID'];
         
         if ($graphcolor == "red")
         { $graphcolor = "blue"; }
         else
         { $graphcolor = "red"; }
         
         $usecolor = $graphcolor;
         if (in_array($optionid, $winningoption))
         { 
           if ($pollvotes[$optionid] == $winningoptcount)
           { $usecolor = "winner"; }
         }
         
         // Check to see if we're showing form elements or not?
         if ($formtop)
         {
           $radiobutton = "<INPUT TYPE=RADIO NAME='option' VALUE='".$optionid."'>";
         }
         
         // Find a percentage
         $percent = 0;
         if ($responsecount)
         {
           $percent  = ($result[$optionid]/$responsecount);
         }
         // Use the percentage to decide how wide the make the bar
         $pixwidth = intval($percent*300);
         // If its 0, hack the width to 1 pixel or the browser will scale it.
         if (!$pixwidth) { $pixwidth = 1; }
         
         $votes = "vote";
         if ((intval($result[$optionid]) > 1) || (!intval($result[$optionid])))
         {
           $votes = "votes";
         }
         $votecountoutput = intval($result[$optionid])." ".$votes;
         
         //echo "Result for $optionid is ".$result[$optionid]." of $responsecount - ".($result[$optionid]/$responsecount)." - ".$percent." percent. PixWidth = ".$pixwidth."<BR>\n";
         $options .= "<TR><TD>".$radiobutton."</TD>\n"
                    ."    <TD CLASS='BoardColumn'><IMG SRC='gfx/graph-".$usecolor.".gif' WIDTH=".$pixwidth." HEIGHT=20 ALT=''></TD>\n"
                    ."    <TD ALIGN=RIGHT><SPAN CLASS='InputSection'>&nbsp;".sprintf("%0.1f", ($percent*100))."%</SPAN></TD></TR>\n"
                    ."<TR><TD></TD>\n"
                    ."    <TD COLSPAN=2>".applyOnlyTextEffects(ProfanityFilter($row['optionname']))." - ".$votecountoutput."<BR></TD></TR>\n"
                    ."<TR><TD COLSPAN=3><IMG SRC='gfx/blank.gif' WIDTH=100 HEIGHT=5 ALT=''></TD></TR>\n";
       }
       
       $output = "<SPAN CLASS='InputSection'>".ProfanityFilter($question)."</SPAN><BR>\n"
                .$expirymessage
                ."<P>\n"
                .$formtop
                ."<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0>\n"
                ."<TR><TD></TD>\n"
                ."    <TD><IMG SRC='gfx/blank.gif' WIDTH=300 HEIGHT=1 ALT=''></TD></TR>\n"
                .$options
                ."</TABLE>\n"
                .$formend;
     }
     
     return $output;
   }
   
   Function checkPollVote($pollid, $userid)
   {
     $sql = "SELECT optionid FROM ".TABLE_POLL_RESPONSES." WHERE userid = $userid AND pollid = $pollid";
     if ($exe = runQuery($sql))
     {
       if ($row = fetchResultArray($exe))
       {
         if ($row['optionid'])
         {
           return 1;
         }
       }
     }
     return 0;
   }
   
   Function answerPoll($pollid, $userid, $option)
   {
     $sql = "INSERT INTO ".TABLE_POLL_RESPONSES." (pollid, userid, optionid) "
           ." VALUES ($pollid, $userid, $option)";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     return 0;
   }
   
   Function ProfanityFilter($text)
   {
     global $configoptions;
     
     //echo "Running on ".htmlentities($text)."<BR>\n";
     
     // This comes from $ProfanityFilter in the settings file
     $profanities = $configoptions['profanities'];
     
     // Don't actually do anything unless ProfanityFilterActive is
     // set in the settings file
     if ($configoptions['profanityfilter'])
     {
       for ($i = 0; $i < count($profanities); $i++ )
       {
         // Patterns come from admin-configured settings, so escape any
         // literal '~' in them to avoid breaking out of our delimiter.
         $text = preg_replace('~'.str_replace('~', '\~', $profanities[$i][0]).'~i', $profanities[$i][1], $text);
       }
     }
     
     // Return the end result
     return $text;
   }
   
   
   Function getIcon($userid, $extra = "")
   {
     global $userdata;
     
     $thisuserdata = $userdata;
     if ($userdata['ID'] != $userid)
     {
       $thisuserdata = fetchRow($userid, TABLE_USERS);
     }
     
     if ($thisuserdata['ownicon'])
     {
       $output = "<IMG SRC='".$thisuserdata['ownicon']."' WIDTH=80 HEIGHT=80 ALT=''>";
     }
     else
     {
       $iconid = $thisuserdata['iconid'];
       if ($extra['iconid'])
       {
         $iconid = $extra['iconid'];
       }
       if ($extra['align'])
       {
         $alignment = " ALIGN=".$extra['align'];
       }
       
       if ($iconid)
       {
         $output = "<IMG SRC='icon-display.php?iconid=".$iconid."' WIDTH=80 HEIGHT=80 ALT=''".$alignment.">";
       }
       else
       {
         $output = "";
       }
     }
     
     return $output;
   }
   
   Function usernameDisplay($userid, $linkclass = "", $showstar = "", $fulldata = "")
   {
     global $userdata;
     global $globaldebugmode;
     
     if (!$linkclass) { $linkclass = "AuthorLink"; }

     // This avoids us fetching the userid's information if it is actually the 
     // logged-in user -- IF the user is actually logged in ($userdata is only
     // an array if they're logged in)
     if (($userdata['ID'] == $userid) && (is_array($userdata)))
     {
       $runmode = "Own Userdata";

       $userdisplaydata = $userdata;
     }
     else
     {
       // Either we pass in $fulldata, or we call getUserInfo()
       if (!$fulldata)
       {
         $runmode = "Database Query";

         $userdisplaydata = getUserInformation($userid);
       }
       else
       {
         $runmode = "Passed-in Data";

         // FullData needs to contain:
         // - postcount
         // - displayname
         // - displayformat
         $userdisplaydata = $fulldata;
         //echo "Bypassing db query<BR>\n";
         //print_r($fulldata);
         //echo "<BR>btw, userid is $userid<BR>\n";
         
         if (!$userid)
         {
           $userid = $fulldata['userid'];
         }
       }
     }

     if ($showstar)
     {
       // Get the star icon for this user
       $star = fetchStarIcon($userdisplaydata['postcount']);
     }
     
     // If displayformat exists, then the user has specified a color format
     // for their displayname. Use textEffects() to decode it, and use it
     // as the basis for the link ($outputusername is the result)
     
     $outputusername = $userdisplaydata['displayname'];
     if ($userdisplaydata['displayformat'])
     { 
       //echo "Got text format codes for $userdisplaydata[displayname].<BR>\n";
       
       // Turn $userdisplaydata['displayformat'] into [] markup codes.
       $formatprefs = unserialize($userdisplaydata['displayformat']);
       if ($globaldebugmode)
       {
         echo "<B>Name Formatting Preferences for ".$outputusername." from ".$runmode.":</B><BR>\n";
         print_r($formatprefs);
         echo "<BR>\n";
       }
/*
       $displaystring = $userdisplaydata['displayname'];
       if (!$formatprefs['stylebold'])     { $cssstylestring .= "font-weight: normal;"; }
       if ($formatprefs['styleitalic'])    { $displaystring = "[i]".$displaystring."[/i]"; }
       if ($formatprefs['styleunderline'])     { $displaystring = "[u]".$displaystring."[/u]"; }
       if ($formatprefs['styleoverline'])      { $displaystring = "[o]".$displaystring."[/o]"; }
       if ($formatprefs['stylestrikethrough']) { $displaystring = "[strike]".$displaystring."[/strike]"; }
       if ($formatprefs['hlcolour'])       { $displaystring = "[hl=".$formatprefs['hlcolour']."]".$displaystring."[/hl]"; }
       if ($formatprefs['txtcolour'])      { $displaystring = "[color=".$formatprefs['txtcolour']."]".$displaystring."[/color]"; }
       // Borders. MSIE sucks, so we'll use the CSS border attribute if they're all on.
       if ($formatprefs['topborder'] && $formatprefs['bottomborder'] && $formatprefs['leftborder'] && $formatprefs['rightborder'])
                                         { $cssstylestring .= "border: 1px solid ".$formatprefs['bordercolour'].";"; }
       else
       {
         // They're not all on, so add the borders together individually
         if ($formatprefs['topborder'])    { $cssstylestring .= "border-top: 1px solid ".$formatprefs['bordercolour'].";"; }
         if ($formatprefs['bottomborder']) { $cssstylestring .= "border-bottom: 1px solid ".$formatprefs['bordercolour'].";"; }
         if ($formatprefs['leftborder'])   { $cssstylestring .= "border-left: 1px solid ".$formatprefs['bordercolour'].";"; }
         if ($formatprefs['rightborder'])  { $cssstylestring .= "border-right: 1px solid ".$formatprefs['bordercolour'].";"; }
*/
$displaystring = $userdisplaydata['displayname'];
       if (!$formatprefs['stylebold'])     { $cssstylestring .= "font-weight: normal;"; }
       if ($formatprefs['styleitalic'])    { $displaystring = "[i]".$displaystring."[/i]"; }
       if ($formatprefs['styleunderline'])     { $displaystring = "[u]".$displaystring."[/u]"; }
       if ($formatprefs['styleoverline'])      { $cssstylestring .= "text-decoration: overline;"; }
       if ($formatprefs['stylestrikethrough']) { $cssstylestring .= "text-decoration: line-through;"; }
       if ($formatprefs['hlcolour'])       { $cssstylestring .= "background-color: ".$formatprefs['hlcolour'].";"; }
       if ($formatprefs['txtcolour'])      { $cssstylestring .= "color: ".$formatprefs['txtcolour'].";"; }
       // Borders. MSIE sucks, so we'll use the CSS border attribute if they're all on.
       if ($formatprefs['topborder'] && $formatprefs['bottomborder'] && $formatprefs['leftborder'] && $formatprefs['rightborder'])
                                         { $cssstylestring .= "border: 1px solid ".$formatprefs['bordercolour'].";"; }
       else
       {
         // They're not all on, so add the borders together individually
         if ($formatprefs['topborder'])    { $cssstylestring .= "border-top: 1px solid ".$formatprefs['bordercolour'].";"; }
         if ($formatprefs['bottomborder']) { $cssstylestring .= "border-bottom: 1px solid ".$formatprefs['bordercolour'].";"; }
         if ($formatprefs['leftborder'])   { $cssstylestring .= "border-left: 1px solid ".$formatprefs['bordercolour'].";"; }
         if ($formatprefs['rightborder'])  { $cssstylestring .= "border-right: 1px solid ".$formatprefs['bordercolour'].";"; }

       }
       $outputusername = textEffects($displaystring);
       
       if ($cssstylestring)
       {
         $outputusername = "<SPAN STYLE='".$cssstylestring."'>".$outputusername."</SPAN>";
       }
     }  
     
     $userdisplay = makeLink("user.php?user=".urlencode($userid), $outputusername, $linkclass).$star;
     return $userdisplay;
   }
   
   Function makeLink($url, $name, $class = "X", $extra = "" )
   {
     if ($class == "X") { $class = "BoardRowBodyLink"; }
     if ($class)
     { $classparam = "CLASS='$class' "; }
     
     if ($extra['onclick'])
     {
       $onclick = " onclick=\"".$extra['onclick']."\"";
     }
     
     if ($extra['target'])
     {
       $target = " TARGET='".$extra['target']."'";
     }
     
     return "<A ".$classparam."HREF='".$url."'".$onclick.$target.">".$name."</A>";
   }
   Function makeLinkmark($url, $name, $class = "X", $extra = "" )
   {
     if ($class == "X") { $class = "BoardRowBodyLink"; }
     if ($class)
     { $classparam = "CLASS='$class' "; }
     
     if ($extra['onclick'])
     {
       $onclick = " onclick=\"".$extra['onclick']."\"";
     }
     
     if ($extra['target'])
     {
       $target = " TARGET='".$extra['target']."'";
     }
     
     return "<A ".$classparam."HREF='".$url."'".$onclick.$target.">!</A>";
   }
   
   Function fetchStarIcon($postcount)
   {
     global $PostLevel;
     global $globaldebugmode;
     global $userdata;

     foreach ($PostLevel as $key => $value)
     {
       $i++;
       
       if (!$staricon)
       {
         if ($globaldebugmode)
         {
           echo "Star $key, Level: $value. Post Count: $postcount<BR>\n";
         }
         if ($postcount >= $value)
         {
           $starlevel = $key;
         }
         else
         {
           $stoplooking = 1;
         }
         
         if ($i == count($PostLevel))
         {
           $stoplooking = 1;
         }
if ($userdata['starsystem'])
{         
if ($userdata['starsystem'] == "1")
{
         if (($stoplooking) && ($starlevel))
         {
           $staricon = "<IMG ALIGN=ABSMIDDLE HSPACE=2 SRC='gfx/stars/star".$starlevel.".gif'>";
         }
}
if ($userdata['starsystem'] == "2")
{
         if (($stoplooking) && ($starlevel))
         {
           $staricon = "<IMG ALIGN=ABSMIDDLE HSPACE=2 SRC='gfx/stars/IGN/star".$starlevel.".gif'>";
         }
}
if ($userdata['starsystem'] == "3")
{
         if (($stoplooking) && ($starlevel))
         {
           $staricon = "<IMG ALIGN=ABSMIDDLE HSPACE=2 SRC='gfx/stars/Red/star".$starlevel.".gif'>";
         }
}
}
else
{
         if (($stoplooking) && ($starlevel))
         {
           $staricon = "<IMG ALIGN=ABSMIDDLE HSPACE=2 SRC='gfx/stars/star".$starlevel.".gif'>";
         }
}
       }
     }

     return $staricon;
   }
   
   Function newThread ($boardid, $subject, $author, $body, $pollid = "")
   {
     $sql = "INSERT INTO ".TABLE_THREADS." (boardid, status, sticky) VALUES ($boardid, 'X', 0)";
     if ($exe = runQuery($sql))
     {
       // Get the inserted thread's ID
       $threadid = fetchLastInsert();
       
       // Now create a post for this thread
       $postid = newPost($threadid, $subject, $author, $body, "", $pollid);
       
       if ($postid)
       {
         $sql = "UPDATE ".TABLE_THREADS." set postidfirst = $postid WHERE ID = $threadid";
         $exe = runQuery($sql);
       }
     }
     
     return $threadid;
   }
   
   Function newPost($threadid, $subject, $author, $body, $recipient = "", $pollid = "")
   {
     global $userdata;
     global $REMOTE_ADDR;
     
     // Polls scan their inputs for magic quote activity themselves...
     if (!$pollid)
     {
       if (!false)
       {
         $subject = addSlashes($subject);
         $body    = addSlashes($body);
       }
     }
     
     if ($recipient)
     {
       $privatemessage = 1;
       // This is a private message, set its status as 'U' (unread)
       $statusfield = ", status";
       $statusvalue = ", 'U'";
     }
     if ($pollid)
     {
       $poll = 1;
       // This is a voting poll
       $pollfield = ", pollid";
       $pollvalue = ", '$pollid'";
     }
     $sql = "INSERT INTO ".TABLE_POSTS." (threadid, postdate, authorid, recipientid, subject, body, ipaddress".$statusfield.$pollfield.") "
           ." VALUES (".$threadid.", ".Date("U").", $author, ".intval($recipient).", '$subject', '$body', '$REMOTE_ADDR'".$statusvalue.$pollvalue.")";
     if ($exe = runQuery($sql))
     {
       // Get the inserted post's ID
       $postid = fetchLastInsert();

       // Only do all this stuff if its a public message - private messages don't count
       if (($postid) && (!$privatemessage))
       {
         // Get the current information for this thread - we need to increment the
         // postcount value, and along the way we'll set the last post id to this
         // post id
         $threaddata = fetchRow($threadid, TABLE_THREADS);
         $sql = "UPDATE ".TABLE_THREADS." "
               ."   SET postidlast = $postid, postcount = ".($threaddata['postcount']+1)." "
               ." WHERE ID = $threadid";
         $exe = runQuery($sql);
         
         // We also need to update the lastpostid of the board to which this
         // thread belongs
         $sql = "UPDATE ".TABLE_BOARDS." "
               ."   SET postidlast = $postid "
               ." WHERE ID = $threaddata[boardid]";
         $exe = runQuery($sql);
         
         // Finally, add one to the author's current postcount
         $authordata = $userdata;
         if ($author != $userdata['ID'])
         {
           $authordata = fetchRow($author, TABLE_USERS);
         }
         $sql = "UPDATE ".TABLE_USERS." "
               ."   SET postcount = ".($authordata['postcount']+1)." "
	.", dollars = ".($authordata['dollars']+10)." "
               ." WHERE ID = ".$author;
         $exe = runQuery($sql);
       }
     }
     
     return $postid;
   }
   
   Function updatePost($postid, $subject, $body, $edituserid, $oldeditcount)
   {
     if (!false)
     {
       $subject = addSlashes($subject);
       $body    = addSlashes($body);
     }
     
     $sql = "UPDATE ".TABLE_POSTS." "
           ."   SET subject = '$subject', body = '$body', "
           ."       edituserid = ".intval($edituserid).", editdate = ".Date("U").", "
           ."       editcount = ".intval($oldeditcount+1)." "
           ." WHERE ID = $postid";
     if ($exe = runQuery($sql))
     {
       return $postid;
     }
     else
     {
       return 0;
     }
   }
   
   // Make a neat date output decided by the proximity of the date to now. For
   // example, if the date is less than 1 day old, show the time. If its less
   // than 1 week old, show the date and time, if its older than 1 week,
   // show the d-M
   Function dateNeat($datevalue = "", $type = "")
   {
     global $configoptions;
     
     // Seconds in a year
     $now = Date("U");
     $onedaybefore = $now - (3600 * 24);
     $oneweekbefore = $now - ($oneday * 7);

     if ($configoptions['timezonechange'])
     {
       $adjustment = ($configoptions['timezonechange']*3600);
     }
     
     switch ($type)
     {
       case "never":
         $output = "Never";
         break;

       case "time":
         $output = Date("g:ia", $datevalue+$adjustment);
         break;

       case "datetime":
         $output = Date("j-M g:ia", $datevalue+$adjustment);
         break;

       default:
         // Work out the format to show on your own...!
       
         if (!$datevalue)
         {
           // No input. Shouldn't happen. Might.
           $output = "Never";
         }
         elseif ($datevalue > $onedaybefore)
         {
           // Less than a day old, show the time
           $output = Date("g:ia", $datevalue+$adjustment);
         }
         elseif ($datevalue > $oneweekbefore)
         {
           // Less than 1 week old, show the time
           $output = Date("j-M g:ia", $datevalue+$adjustment);
         }
         else
         {
           // Over 1 week old, show the date
           $output = Date("j-M", $datevalue);
         }
     }
     
     return $output;
   }
   
   function generatePassword()
   {
     // Generate a random password
     srand((double)microtime()*1000000);
     $unique_str = substr(md5(rand(0,9999999)), 0, 8);
     return $unique_str; 
   }
   
   Function createUser($data)
   {
     global $DocRoot;
     global $DiscoBoardName;
     global $DBRoot;
     global $configoptions;
     
     if (admincountUserEmails($data['email']))
     {
       return 1;
     }
     
     $newdata = $data;
     if (!false)
     {
       foreach ($data as $key => $value)
       {
         $newdata[$key] = addSlashes($value);
       }
     }
     
     $password = generatePassword();
     
     $encpassword = hashPassword($password, "authenticate");
     
     //echo "Password for $newdata[displayname] is $password<BR>\n";
     
     $classid = 6; // Default normal user class
     
     $sql = "INSERT INTO ".TABLE_USERS." "
           ."(created, classid, displayname, encpassword, "
           ." fname, sname, dateofbirth, "
           ." gender, country, contactemail, timezone, ip_signup, "
           ." perpageposts, perpagethreads ) "
           ."VALUES "
           ."(".Date("U").", ".$classid.", '$newdata[displayname]', '$encpassword', "
           ." '$newdata[firstname]', '$newdata[lastname]', ".intval($newdata['birthday']).", "
           ." '$newdata[gender]', '$newdata[country]', '$newdata[email]', '".$configoptions['timezonechange']."', '$newdata[ipaddress]', "
           ." ".intval($configoptions['perpageposts']).", ".intval($configoptions['perpagethreads'])." ) ";
     if ($exe = runQuery($sql))
     {
       $email = implode("", file("templates/newuser.txt"));
       
       // Replace the special bits in the welcome email
       $email = str_replace("FNAME", $data['firstname'], $email);
       $email = str_replace("DISCOBOARDNAME", $DiscoBoardName, $email);
       $email = str_replace("USERNAME", $data['displayname'], $email);
       $email = str_replace("PASSWORD", $password, $email);
       $email = str_replace("DBROOT", $DBRoot, $email);
       
       $recipient = trim($data['firstname']." ".$data['lastname']);
       $recipientfull = "$recipient <$data[email]>";
       
       sendSystemEmail($recipientfull, "welcome", $email);
       
       return 1;
     }
     else
     {
       return 0;
     }
   }
   

   Function admincreateUser($data)
   {
     global $DocRoot;
     global $DiscoBoardName;
     global $DBRoot;
     global $configoptions;
     
     if (admincountUserEmails($data['email']))
     {
       return 1;
     }
     
     $newdata = $data;
     if (!false)
     {
       foreach ($data as $key => $value)
       {
         $newdata[$key] = addSlashes($value);
       }
     }
     
     //$password = generatePassword();
     $password = $newdata['password'];
     $encpassword = hashPassword($password, "authenticate");
     
     //echo "Password for $newdata[displayname] is $password<BR>\n";
     
     $classid = $data['setclassid']; // Sets class id
     
     $sql = "INSERT INTO ".TABLE_USERS." "
           ."(created, classid, displayname, encpassword, "
           ." fname, sname, dateofbirth, "
           ." gender, country, contactemail, timezone, ip_signup, "
           ." perpageposts, perpagethreads ) "
           ."VALUES "
           ."(".Date("U").", ".$classid.", '$newdata[displayname]', '$encpassword', "
           ." '$newdata[firstname]', '$newdata[lastname]', ".intval($newdata['birthday']).", "
           ." '$newdata[gender]', '$newdata[country]', '$newdata[email]', '".$configoptions['timezonechange']."', '$newdata[ipaddress]', "
           ." ".intval($configoptions['perpageposts']).", ".intval($configoptions['perpagethreads'])." ) ";
     if ($exe = runQuery($sql))
     {
       $email = implode("", file("templates/newuser.txt"));
       
       // Replace the special bits in the welcome email
       $email = str_replace("FNAME", $data['firstname'], $email);
       $email = str_replace("DISCOBOARDNAME", $DiscoBoardName, $email);
       $email = str_replace("USERNAME", $data['displayname'], $email);
       $email = str_replace("PASSWORD", $password, $email);
       $email = str_replace("DBROOT", $DBRoot, $email);
       
       $recipient = trim($data['firstname']." ".$data['lastname']);
       $recipientfull = "$recipient <$data[email]>";
       
       sendSystemEmail($recipientfull, "welcome", $email);
       
       return 1;
     }
     else
     {
       return 0;
     }
   }






   Function sendSystemEmail($to, $type, $body)
   {
     global $DiscoBoardName;
     global $HTTP_HOST;
     global $DBVersion;
     
     // Append the version string
     $body .= "-- \n"
             ."DiscoBoard version $DBVersion";
     
     switch ($type)
     {
       case "welcome":
         $subject = "$DiscoBoardName New User Information";
         $from    = "$DiscoBoardName <nobody@".$HTTP_HOST.">";
         break;
       case "passrecovery":
         $subject = "$DiscoBoardName Password Recovery Instructions";
         $from    = "$DiscoBoardName <nobody@".$HTTP_HOST.">";
         break;
       case "passreset":
         $subject = "$DiscoBoardName New Password Information";
         $from    = "$DiscoBoardName <nobody@".$HTTP_HOST.">";
         break;
       case "passchange":
         $subject = "$DiscoBoardName New Password Information";
         $from    = "$DiscoBoardName <nobody@".$HTTP_HOST.">";
         break;
       default:
         $subject = "Email from $DiscoBoardName";
         $from    = "$DiscoBoardName <nobody@".$HTTP_HOST.">";
     }
     
     mail($to, $subject, $body,
          "From: $from\nReply-to: GC-Boards@".$HTTP_HOST);
     
     //echo "Email sent to ".htmlentities($to)."<BR><PRE>".$body."</PRE>\n";
   }

   function calculateAge($birthdayunix)
   {
     // get current date broken down into month,day,year variables.
     $month_now = date("m");
     $day_now   = date("d");
     $year_now  = date("Y");
     
     // Get the values for the input date
     $month = date("m", $birthdayunix);
     $day   = date("d", $birthdayunix);
     $year  = date("Y", $birthdayunix);
     
     // initial guess at age is differance between this year and birth year.
     $age = $year_now - $year;

     // if birth month is same as current month, then must look at date to get age
     if ($month_now == $month)
     {
       // if the birthday has not come yet then age is age-1
       if ($day_now < $day)
       {
         return ($age-1);
       }
       else
       {
         //if the birthday has already come then age is acurate
         return ($age);
       }
     }
     //if birth month has not come yet, then age is age-1
     elseif ($month_now < $month)
     {
       return ($age -1);
     }
     else 
     {
       //if birth month has already come then age is acurate
       return ($age);
     }
   }
   
   Function setLastLogin($userid, $date)
   {
     $sql = "UPDATE ".TABLE_USERS." "
           ."   SET lastlogin = ".intval($date)." "
           ." WHERE ID = $userid";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   Function updateUser($userid, $data, $changetimestamp = "yes")
   {
     global $userdata;
     
     $newdata = $data;
     if (!false)
     {
       foreach ($data as $key => $value)
       {
         $newdata[$key] = addSlashes($value);
       }
     }
     
     $nochangefields = array("ID", "displayname");
     
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
     
     $sql = "UPDATE ".TABLE_USERS." "
           ."   SET ".$fieldupdates.$lastupdated." "
           ." WHERE ID = $userid";
     if ($exe = runQuery($sql))
     {
       if ($userdata['ID'] == $userid)
       {
         // We just updated this user, so we should re-fetch their info
         $userdata = getUserInformation($userid);
       }
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   
   // Expected input array $vars:
   //  currentpage
   //  startrow
   //  totalrows
   //  pagename
   //  linkparameters
   Function getPaginationInformation($vars, $pagetype)
   {
     global $configoptions;
     
     if ($pagetype == "thread")
     {
       $resultsperpage = $configoptions['perpagethreads'];
     }
     elseif ($pagetype == "posts")
     {
       $resultsperpage = $configoptions['perpageposts'];
     }
     else
     {
       $resultsperpage = $pagetype;
     }
     //echo "<I>Res per page is $resultsperpage</I><BR>\n";
     
     $page = $vars['currentpage'];
     $startrow = $vars['startrow'];
     
     $resultcount = $vars['totalrows'];
     
     // Find out the number of pages we've got available
     $pagecount = ($resultcount / $resultsperpage);
     
     //echo "<I>Pagecount is $pagecount</I><BR>\n";

     // If $pagecount is X.x, round it up.
     if (($pagecount - intval($pagecount)) != 0)
     {
       $pagecount = intval($pagecount)+1;
     }
     
     //echo "<I>Pagecount is $pagecount</I><BR>\n";
     
     // Decide what image numbers we'll start and end with on this page
     $startresult = 0 + (($page-1) * $resultsperpage);
     $finalresult = $startresult + $resultsperpage;
     
     // Make sure we stop early if we're on the last page
     if ($finalresult > $resultcount)
     { $finalresult = $resultcount; }
     
     // Set up the navigation links to go from page to page
     for ($i = 1; $i <= $pagecount; $i++ )
     {
       // Direct page number linkage
       if ($page == $i)
       {
         // This is the current page, so don't link it
         $navigation .= "<B>".$i."</B>";
       }
       else
       {
         // This is not the current page, so let them link to it
         $navigation .= "<A HREF='".$vars['pagename']."?".$vars['linkparameters']."&page=".$i."'>".$i."</A>";
       }
       
       // Pretty | to separate page numbers
       if ($i < ($pagecount))
       {
         $navigation .= " | ";
       }
       
       // And a \n to end the line...
       $navigation .= "\n";
     }
     
     // Next and Previous page links
     $nexturl = $vars['pagename']."?".$vars['linkparameters']."&page=".($page+1);
     $prevurl = $vars['pagename']."?".$vars['linkparameters']."&page=".($page-1);
     $thisurl = $vars['pagename']."?".$vars['linkparameters']."&page=".($page);
     $firsturl = $vars['pagename']."?".$vars['linkparameters']."&page=1";
     $finalurl = $vars['pagename']."?".$vars['linkparameters']."&page=".($pagecount);
     
     $nextlink = "<A HREF='".$nexturl."'>Next &raquo;</A>";
     $prevlink = "<A HREF='".$prevurl."'>&laquo; Back</A>";
     $thislink = "<A HREF='".$thisurl."'>Reload</A>";
     $firstlink = "<A HREF='".$firsturl."'>|&laquo; First Page</A>";
     $finallink = "<A HREF='".$finalurl."'>Final Page &raquo;|</A>";
     
     // Make sure we don't display the links where they shouldn't go
     if ($page == $pagecount)
     {
       $nextlink = "&nbsp;"; 
       $finallink = "&nbsp;";
     }
     if ($page == 1)
     {
       $prevlink = "&nbsp;";
       $firstlink = "&nbsp;";
     }

     // New: Window navigation. Makes sure only 10 pages are linked to directly
     // at a time. Only bother to do this if pagecount is over 1.
     
     if ($pagecount > 1)
     {
       // Number of pages to link directly
       $window = 10;
       if ($pagecount < $window)
       {
         $window = $pagecount;
       }
       
       $prospectivewindowstart = ($page - ($window * 0.5));
       //echo "PWStart: $prospectivewindowstart<BR>\n";
       if ($prospectivewindowstart < 1)
       {
         $windowstart = 1;
       }
       else
       {
         $windowstart = $prospectivewindowstart;
       }
       
       $prospectivewindowstop = ($windowstart + $window - 1);
       //echo "PWStop: $prospectivewindowstop<BR>\n";
       if ($prospectivewindowstop > $pagecount)
       {
         $windowstop = $pagecount;
       }
       else
       {
         $windowstop = $prospectivewindowstop;
       }
  
       if ($windowstart != 1)
       {
         $newnavigation .= makeLink($firsturl, "&laquo;", "MainMenuLink")." | \n";
       }
       for ($i = $windowstart; $i <= $windowstop; $i++ )
       {
         // Direct page number linkage
         if ($page == $i)
         {
           // This is the current page, so don't link it
           $newnavigation .= "<SPAN STYLE='font-weight: normal;'>".$i."</SPAN>";
         }
         else
         {
           // This is not the current page, so let them link to it
           $newnavigation .= makeLink($vars['pagename']."?".$vars['linkparameters']."&page=".intval($i), intval($i), "MainMenuLink");
         }
         if ($i != $pagecount)
         {
           $newnavigation .= " | ";
         }
         $newnavigation .= "\n";
       }
       if ($windowstop != $pagecount)
       {
         $newnavigation .= makeLink($finalurl, "&raquo;", "MainMenuLink")."\n";
       }
       $newnavigation .= " &nbsp; - &nbsp; ";
       if ($prevlink == "&nbsp;")
       {
         $newnavigation .= "<STRIKE>Previous</STRIKE> | \n";
       }
       else
       {
         $newnavigation .= makeLink($prevurl, "Previous", "MainMenuLink")." | \n";
       }
       if ($nextlink == "&nbsp;")
       {
         $newnavigation .= "<STRIKE>Next</STRIKE> | \n";
       }
       else
       {
         $newnavigation .= makeLink($nexturl, "Next", "MainMenuLink")." | \n";
       }
       $newnavigation .= makeLink($thisurl, "Reload", "MainMenuLink")."\n";
     }
     
     // minipagination is shown in a small font size next to a thread's subject
     if ($pagecount > 1)
     {
       for ($i = 1; $i <= $pagecount; $i++ )
       {
         $minipagelink[] = makeLink($vars['pagename']."?".$vars['linkparameters']."&page=".intval($i), intval($i), '');
       }
       $mininav = "[page: ".implode(" ", $minipagelink)."]";
     }
     
     $return['pagecount'] = $pagecount;
     
     $return['firstlink'] = $firstlink;
     $return['finallink'] = $finallink;
     $return['nextlink']  = $nextlink;
     $return['prevlink']  = $prevlink;
     
     // Output the different navigation types
     $return['navigation']     = $navigation;
     $return['newnavigation']  = $newnavigation;
     $return['mininavigation'] = $mininav;
     
     return $return;
   }
   
   Function listIcons($opts)
   {
     global $PHP_SELF;
     
     if (($opts['userclassid']) && ($opts['showall'] != 1))
     {
       $tableextra = ", ".TABLE_ICON_GROUPS." ig";
       $condition .= "   AND i.groupid = ig.ID AND (ig.classid = 0 OR ig.classid = ".$opts['userclassid'].") ";
     }
     if ($opts['groupid'])
     {
       $condition .= "   AND i.groupid = ".$opts['groupid']." ";
     }
     if ($opts['orderby'])
     {
       $orderby = " ORDER BY ".$opts['orderby'];
     }
     
     // Get a count of the icons available first
     $sql = "SELECT COUNT(*) AS result"
           ."  FROM ".TABLE_ICONS." i".$tableextra
           ." WHERE i.ID > 0 "
           .$condition;
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       $resultcount = $row['result'];
     }
     
     if ($opts['linkparameters'])
     {
       $linkparametersout = $opts['linkparameters']."&";
     }
     
     // Yes, even the icon browser has pagination
     $startrow = (($opts['page']*$opts['perpage']) - $opts['perpage']);
     $pageinfovars['currentpage']    = $opts['page'];
     $pageinfovars['startrow']       = $startrow;
     $pageinfovars['totalrows']      = $resultcount;
     $pageinfovars['pagename']       = $PHP_SELF;
     $pageinfovars['linkparameters'] = $linkparametersout."groupid=".$opts['groupid']."&orderby=".$opts['orderby'];
     
     $pagination = getPaginationInformation($pageinfovars, $opts['perpage']);
     
     // Get a count of the icons available first
     $sql = "SELECT i.ID, i.iconname, i.filename, ig.groupname, ig.classid "
           ."  FROM ".TABLE_ICONS." i, ".TABLE_ICON_GROUPS." ig "
           ." WHERE i.groupid = ig.ID "
           .$condition
           .$orderby
           ." LIMIT ".intval($startrow).", ".intval($opts['perpage']);
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         // 5 icons per row

         $i++; // Only goes from 1-5
         $j++; // Goes from 1 to the number of icons we pulled out

         //echo "Doing icon $i ($row[ID])...<BR>\n";
         $cells .= "    <TD WIDTH=5% ALIGN=RIGHT><INPUT TYPE=RADIO NAME='iconid' VALUE='".$row['ID']."'></TD>\n"
                  ."    <TD WIDTH=15% CLASS='BoardRowHeading' ALIGN=CENTER>\n"
                  ."        ".getIcon("", array("iconid"=>$row['ID']))."<BR>\n"
                  ."        <SPAN STYLE='font-size: 8pt;'>".$row['iconname']."</SPAN></TD>\n";
         if (($i == 5) || ($j == resultCount($exe)))
         {
           $rows .= "<TR VALIGN=MIDDLE>\n"
                   .$cells."</TR>\n";
           $cells = "";
           $i = 0;
         }
       }
       
       if (!resultCount($exe))
       {
         $rows = "<TR><TD ALIGN=CENTER><B CLASS='red'>-- No icons found in this group! --</B></TD></TR>\n";
       }
       
       $icontable = "<TABLE>\n"
                   .$rows
                   ."</TABLE><BR>\n";
     }
     
     $return['icontable'] = $icontable;
     $return['navbar']    = $pagination['newnavigation'];
     
     return $return;
   }
   
   Function addWatchedUser($userid, $watcheduserid)
   {
     // Check to see if it exists first
     $sql = "SELECT COUNT(*) AS result "
           ."  FROM ".TABLE_FAVOURITES." "
           ." WHERE ownerid = ".$userid." "
           ."   AND userid = ".$watcheduserid;
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       $resultcount = $row['result'];
     }
     
     // Check to see if its already there
     if (!$resultcount)
     {
       // It's not - let's add it
       $sql = "INSERT INTO ".TABLE_FAVOURITES." (ownerid, userid) "
             ."VALUES ($userid, $watcheduserid)";
       if ($exe = runQuery($sql))
       {
         return 1;
       }
       else
       {
         return 0;
       }
     }
     else
     {
       return 1;
     }
   }
   
   Function addFavouriteThread($userid, $threadid)
   {
     // Check to see if it exists first
     $sql = "SELECT COUNT(*) AS result "
           ."  FROM ".TABLE_FAVOURITES." "
           ." WHERE ownerid = ".$userid." "
           ."   AND threadid = ".$threadid;
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       $resultcount = $row['result'];
     }
     
     // Check to see if its already there
     if (!$resultcount)
     {
       // It's not - let's add it
       $sql = "INSERT INTO ".TABLE_FAVOURITES." (ownerid, threadid) "
             ."VALUES ($userid, $threadid)";
       if ($exe = runQuery($sql))
       {
         return 1;
       }
       else
       {
         return 0;
       }
     }
     else
     {
       return 1;
     }
   }
   
   Function removeWatchedUser($userid, $watcheduserid)
   {
     $sql = "DELETE FROM ".TABLE_FAVOURITES." WHERE ownerid = $userid AND userid = $watcheduserid";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
   }
   
   Function removeWatchedThread($userid, $threadid)
   {
     $sql = "DELETE FROM ".TABLE_FAVOURITES." WHERE ownerid = $userid AND threadid = $threadid";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
   }
   
   Function addFavouriteBoard($userid, $boardid)
   {
     // Check to see if it exists first
     $sql = "SELECT COUNT(*) AS result "
           ."  FROM ".TABLE_FAVOURITES." "
           ." WHERE ownerid = ".$userid." "
           ."   AND boardid = ".$boardid;
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       $resultcount = $row['result'];
     }
     
     // Check to see if its already there
     if (!$resultcount)
     {
       // It's not - let's add it
       $sql = "INSERT INTO ".TABLE_FAVOURITES." (ownerid, boardid) "
             ."VALUES ($userid, $boardid)";
       if ($exe = runQuery($sql))
       {
         return 1;
       }
       else
       {
         return 0;
       }
     }
     else
     {
       return 1;
     }
   }
   
   Function removeFavouriteBoard($userid, $boardid)
   {
     $sql = "DELETE FROM ".TABLE_FAVOURITES." "
           ." WHERE ownerid = $userid "
           ."   AND boardid = $boardid";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   Function inFavourites($userid, $type, $id)
   {
     // Check to see if it exists first
     $sql = "SELECT COUNT(*) AS result "
           ."  FROM ".TABLE_FAVOURITES." "
           ." WHERE ownerid = ".$userid." "
           ."   AND ".$type." = ".$id;
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       $resultcount = $row['result'];
     }
     
     return $resultcount;
   }
   
   Function getLastPost($userid)
   {
     $lastpostdata = fetchLastPost($userid);
     
     if (is_array($lastpostdata))
     {
       $lastpost = dateNeat($lastpostdata['postdate'])."&nbsp;";
     }
     else
     {
       $lastpost = "None";
     }
     return $lastpost;
   }

   Function getLastPostOnly($userid)
   {
     $lastpostdata = fetchLastPost($userid);
     
     if (is_array($lastpostdata))
     {
       $lastpost = dateNeat($lastpostdata['postdate']);
     }
     else
     {
       $lastpost = "None";
     }
     return $lastpost;
   }
   
   Function fetchLastPost($userid)
   {
     $sql = "SELECT p.ID, p.threadid, p.postdate, p.subject, "
           ."       t.ID AS threadid, b.ID as boardid, b.boardname "
           ."  FROM ".TABLE_POSTS." p, ".TABLE_THREADS." t, ".TABLE_BOARDS." b, ".TABLE_USERS." u "
           ." WHERE u.ID = p.authorid "
           ."   AND p.threadid = t.ID "
           ."   AND t.boardid = b.ID "
           ."   AND u.ID = ".$userid." "
           ." ORDER BY ID DESC"
           ." LIMIT 0, 1";
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       
       $return['postid']     = $row['ID'];
       $return['subject']    = $row['subject'];
       $return['postdate']   = $row['postdate'];
       $return['threadid']   = $row['threadid'];
       $return['boardid']    = $row['boardid'];
       $return['boardname']  = $row['boardname'];
       
       return $return;
     }
   }
   
   Function sendPasswordRecoveryVerification($userdata)
   {
     global $PHP_SELF;
     global $DocRoot;
     global $DiscoBoardName;
     global $DBRoot;
     
     // Generate a new token. A password will do for this.
     $token = generatePassword();
     
     $tokensafe = addSlashes($token);
     
     $sql = "INSERT INTO ".TABLE_RECOVERYVERIFICATION." (userid, token) "
           ."VALUES ($userdata[ID], '$tokensafe')";
     if ($exe = runQuery($sql))
     {
       $email = implode("", file("templates/recoverpass.txt"));
       
       $changeurl = $DBRoot."/recoverpass.php?action=changepass&token=".urlencode($token);
       
       // Replace the special bits in the welcome email
       $email = str_replace("FNAME", $userdata['firstname'], $email);
       $email = str_replace("DISCOBOARDNAME", $DiscoBoardName, $email);
       $email = str_replace("TOKEN", $token, $email);
       $email = str_replace("CHANGEURL", $changeurl, $email);
       
       $recipient = trim($userdata['fname']." ".$userdata['sname']);
       $recipientfull = "$recipient <$userdata[contactemail]>";
       
       sendSystemEmail($recipientfull, "passrecovery", $email);
     }
   }
   
   Function changePassword($token)
   {
     global $DocRoot;
     global $DiscoBoardName;
     
     // Fetch the userid for the token
     $tokendata = fetchRow($token, TABLE_RECOVERYVERIFICATION, "token", "idfieldistext", "dontcareifblank");
     
     if (!$tokendata['userid'])
     {
       return 0;
     }
     else
     {
       $userdata = fetchRow($tokendata['userid'], TABLE_USERS);
       
       // Generate a new password and encrypt it
       $password = generatePassword();
       
       // Set the update variables
       $data['encpassword'] = hashPassword($password, "authenticate");;
       
       // Do the update
       $update = updateUser($tokendata['userid'], $data);
       if ($update)
       {
         // Fetch the email template
         $email = implode("", file("templates/resetpass.txt"));
         
         // Replace the special bits in the welcome email
         $email = str_replace("FNAME", $userdata['firstname'], $email);
         $email = str_replace("DISCOBOARDNAME", $DiscoBoardName, $email);
         $email = str_replace("PASSWORD", $password, $email);
         
         // Decide who to send this email to
         $recipient = trim($userdata['fname']." ".$userdata['sname']);
         $recipientfull = "$recipient <$userdata[contactemail]>";
         
         sendSystemEmail($recipientfull, "passreset", $email);
         
         // Delete the token
         $sql = "DELETE FROM ".TABLE_RECOVERYVERIFICATION." WHERE ID = $tokendata[ID]";
         $exe = runQuery($sql);
         
         return $userdata['contactemail'];
       }
       else
       {
         return 0;
       }
     }
   }
   
   Function countUnreadPrivateMessages($userid)
   {
     $opts['searchtype']     = "unread private";
     $opts['recipientid']    = $userid;
     $opts['status']         = "U";
     $opts['onlyreturncount'] = 1;
     
     $posts = postSearch($opts);
     
     if ($posts)
     {
       $output = " - <B STYLE='color: yellow;'>".$posts." new</B>";
     }
     return $output;
   }
   
   Function postSearch($opts, $pagenumber = "")
   {
     global $PHP_SELF;
     global $configoptions;
     
     if (!$pagenumber) { $pagenumber = 1; }

     if ($opts['authorid'])    { $condition[] = " p.authorid = $opts[authorid]"; }
     if ($opts['recipientid']) { $condition[] = " p.recipientid = $opts[recipientid]"; }
     if ($opts['status'])      { $condition[] = " p.status = '$opts[status]'"; }
     if ($opts['statusnull'])  { $condition[] = " p.status IS NULL"; }
     
     if ($opts['searchtype'] == "sent private")
                             { $condition[] = " (p.status = 'U' OR p.status = 'R')"; }
     
     if ($condition)
     {
       $searchcondition = " WHERE ".implode(" AND ", $condition);
     }
     
     $sql = "SELECT COUNT(*) AS result "
           ."  FROM ".TABLE_POSTS." p "
           .$searchcondition;
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       $resultcount = $row['result'];
     }
     
     if ($opts['onlyreturncount'])
     {
       return $resultcount;
     }
     
     $startrow = (($pagenumber*$configoptions['perpagethreads']) - $configoptions['perpagethreads']);
     $pageinfovars['currentpage']    = $pagenumber;
     $pageinfovars['startrow']       = $startrow;
     $pageinfovars['totalrows']      = $resultcount;
     $pageinfovars['pagename']       = $PHP_SELF;
     $pageinfovars['linkparameters'] = $opts['pagvars'];

     $pagination = getPaginationInformation($pageinfovars, "thread");
     
     if ($opts['searchtype'] == "public")
     {
       // Search public messages - there's only an author field
       $sql = "SELECT p.ID, p.threadid, p.postdate, p.authorid, p.subject, "
             ."       t.ID as threadid, b.ID as boardid, b.private, b.boardname, g.ID as groupid, g.groupname"
             ."  FROM ".TABLE_POSTS." p, ".TABLE_THREADS." t, ".TABLE_BOARDS." b, ".TABLE_GROUPS." g "
             .$searchcondition
             ."   AND p.threadid = t.ID "
             ."   AND t.boardid = b.ID "
             ."   AND b.groupid = g.ID "
             ." ORDER BY ID DESC"
             ." LIMIT ".intval($startrow).", ".intval($configoptions['perpagethreads'])
             ."";
     }
     else
     { 
       // Search private messages, with author and recipient ID fields
       $sql = "SELECT p.ID, p.threadid, p.postdate, p.authorid, p.subject, "
             ."       ua.displayname AS authordisplayname, ur.ID AS recipientid, ur.displayname AS recipientdisplayname "
             ."  FROM ".TABLE_POSTS." p, ".TABLE_USERS." ua, ".TABLE_USERS." ur "
             .$searchcondition
             ."   AND p.authorid = ua.ID "
             ."   AND p.recipientid = ur.ID "
             ." ORDER BY ID DESC"
             ." LIMIT ".intval($startrow).", ".intval($configoptions['perpagethreads'])
             ."";
     }
     
     $exe = runQuery($sql);
     
     if (resultCount($exe))
     {
       while ($row = fetchResultArray($exe))
       {
         $leadingbullet = "• ";
         
         if ($opts['searchtype'] == "public")
         {
// if (!$row['private'] || checkAccess("accessvip"))
if ((!$row['private']) || ($row['private'] == "1" && checkAccess("accessvip")) || ($row['private'] == "2" && checkAccess("accessinsider")))
{
           $usernameheading = "Board";
           $viewthreadlink = "thread.php?threadid=".$row['threadid'];
           $viewboardlink  = "board.php?boardid=".$row['boardid'];
           $viewgrouplink  = "index.php?action=viewgroup&groupid=".$row['groupid'];
           $msglistrow .= "<TR><TD CLASS='BoardRowBody'>".$leadingbullet.makeLink($viewthreadlink, ProfanityFilter($row['subject']), "BoardRowHeadingLink")."</TD>\n"
                         ."    <TD CLASS='BoardRowHeading'>".makeLink($viewboardlink, $row['boardname'], "BoardRowHeadingLink")."</TD>\n"
                         ."    <TD CLASS='BoardRowHeading'>".makeLink($viewgrouplink, $row['groupname'], "BoardRowHeadingLink")."</TD>\n"
                         ."    <TD CLASS='BoardRowBody'>".dateNeat($row['postdate'], "datetime")."</TD>\n"
                         ."</TR>\n";
}
else
{
           $usernameheading = "Board";
           $viewthreadlink = "thread.php?threadid=".$row['threadid'];
           $viewboardlink  = "board.php?boardid=".$row['boardid'];
           $viewgrouplink  = "index.php?action=viewgroup&groupid=".$row['groupid'];
           $msglistrow .= "<TR><TD CLASS='BoardRowBody' COLSPAN=3><B>#&nbsp;#&nbsp;#&nbsp;Message Posted In A Private Board&nbsp;#&nbsp;#&nbsp;#</B></TD>\n"
                         ."<TD CLASS='BoardRowBody'>".dateNeat($row['postdate'], "datetime")."</TD>\n"
                         ."</TR>\n";
}
         }
         else
         {
           // Which username do we show?
           if ($opts['usercolumn'] == "author")
           {
             $username = usernameDisplay($row['authorid']);
             $usernameheading = "Author";
           }
           elseif ($opts['usercolumn'] == "recipient")
           {
             $username = usernameDisplay($row['recipientid']);
             $usernameheading = "Recipient";
           }
           else
           {
             echo "No usercolumn option set!<BR>\n";
             $username = usernameDisplay($row['authorid']);
             $usernameheading = "Recipient";
           }
           
           if ($opts['showdelete'])
           {
             $checkbox = "<INPUT TYPE=CHECKBOX NAME='del".($checkboxcount+1)."' VALUE='".$row['ID']."'>";
             $checkboxcell = "<TD CLASS='BoardRowBody' ALIGN=CENTER>".$checkbox."</A></TD>";
           }
           
           $viewlink = $PHP_SELF."?action=viewmessage&messageid=".$row['ID'];
           $msglistrow .= "<TR><TD CLASS='BoardRowBody'>".$leadingbullet.makeLink($viewlink, ProfanityFilter($row['subject']), "BoardRowHeadingLink")."</TD>\n"
                         ."    <TD CLASS='BoardRowHeading'>".$username."</TD>\n"
                         ."    <TD CLASS='BoardRowHeading'>".dateNeat($row['postdate'], "datetime")."</TD>\n"
                         ."    ".$checkboxcell."\n"
                         ."</TR>\n";
         
           // Increment the checkboxcount
           $checkboxcount++;
         }
       }
       
       if ($opts['searchtype'] == "public")
       {
         $msglistoutput = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
                         ."<TR><TD WIDTH=40% CLASS='BoardColumn'>Subject</TD>\n"
                         ."    <TD WIDTH=25% CLASS='BoardColumn'>Board</TD>\n"
                         ."    <TD WIDTH=15% CLASS='BoardColumn'>Group</TD>\n"
                         ."    <TD WIDTH=20% CLASS='BoardColumn'>Date</TD>\n"
                         ."</TR>\n"
                         .$msglistrow
                         ."</TABLE>\n";
       }
       else
       {
         $subjectwidth = 60;
         $selecthead   = "";
         if ($opts['showdelete'])
         {
           $subjectwidth = 55;
           $selecthead   = "<TD WIDTH=5% CLASS='BoardColumn' ALIGN=CENTER>?</TD>";
           $formstart = "<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                       .inputHidden("action", "delete")
                       .inputHidden("oldaction", $opts['thisaction'])
                       .inputHidden("page", $pagenumber)
                       .inputHidden("checkboxcount", $checkboxcount);
           $formstop  = "</FORM>\n";
           $submitrow = "<TR><TD COLSPAN=3 CLASS='BoardColumn'>&nbsp;</TD>\n"
                       ."    <TD CLASS='BoardColumn' ALIGN=CENTER>".inputSubmit("Delete")."</TD></TR>\n";
         }
         
         $msglistoutput = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
                         ."<TR><TD WIDTH=".$subjectwidth."% CLASS='BoardColumn'>Topic</TD>\n"
                         ."    <TD WIDTH=15% CLASS='BoardColumn'>".$usernameheading."</TD>\n"
                         ."    <TD WIDTH=15% CLASS='BoardColumn'>Date Posted</TD>\n"
                         ."    ".$selecthead."\n"
                         ."</TR>\n"
                         .$msglistrow
                         .$submitrow
                         ."</TABLE>\n";
       }
     }
     else
     {
       $msglistoutput = "<TABLE WIDTH=100% CELLPADDING=5 CELLSPACING=1 BORDER=0>\n"
                       ."<TR><TD CLASS='BoardRowBody' ALIGN=CENTER>No ".$opts['searchtype']." messages were found</TD></TR>\n"
                       ."</TABLE>\n";
     }
     
     $return['msglist']   = $msglistoutput;
     $return['navbar']    = $pagination['newnavigation'];
     $return['formstart'] = $formstart;
     $return['formstop']  = $formstop;

     return $return;
   }
   
   Function viewPrivateMessage($msgid)
   {
     global $userdata;

     $messagedata = fetchRow($msgid, TABLE_POSTS);
     $authordata  = fetchRow($messagedata['authorid'], TABLE_USERS);

     $postdata['ID']            = $messagedata['ID'];
     $postdata['authorid']      = $messagedata['authorid'];
     $postdata['sig1']          = $authordata['sig1'];
     $postdata['sig2']          = $authordata['sig2'];
     $postdata['sig3']          = $authordata['sig3'];
     $postdata['sig4']          = $authordata['sig4'];
     $postdata['sig5']          = $authordata['sig5'];
     $postdata['title']         = $authordata['title'];
     $postdata['displayname']   = $authordata['displayname'];
     $postdata['displayformat'] = $authordata['displayformat'];
     $postdata['postcount']     = $authordata['postcount'];
     $postdata['created']       = $authordata['created'];
     $postdata['postdate']      = $messagedata['postdate'];
     $postdata['subject']       = $messagedata['subject'];
     $postdata['body']          = $messagedata['body'];
     $postdata['ipaddress']     = $messagedata['ipaddress'];

     $message = "<TABLE WIDTH=100% ALIGN=CENTER CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
               .formatPost($postdata, "private")
               ."</TABLE>\n";
     
     // Okay thats the output part taken care of. Now we have to mark this as
     // read, by changing the message's status to R. Only do this if the
     // current user ID is the same as the recipientid, though.
     if ($userdata['ID'] == $messagedata['recipientid'])
     {
       $sql = "UPDATE ".TABLE_POSTS." SET status = 'R' WHERE ID = $msgid";
       $exe = runQuery($sql);
     }

     $return['message'] = $message;
     
     return $return;
   }
   
   Function newPoll($vars)
   {
     // This shouldn't be needed here - /poll.php checks magic_quotes_gpc for us
     //if (!false)
     //{ $vars['question'] = addSlashes($vars['question']); }
     
     $sql = "INSERT INTO ".TABLE_POLLS." (authorid, question, expirydate) "
           ." VALUES ($vars[ownerid], '$vars[question]', ".intval($vars['expiry']).")";
     if ($exe = runQuery($sql))
     {
       $pollid = fetchLastInsert();
       
       for ($i = 1; $i <= 10; $i++ )
       {
         $answervar = "answer".$i;
         
         if ($vars[$answervar])
         {
           if (!false)
           { $vars[$answervar] == addSlashes($vars[$answervar]); }
           
           $sql = "INSERT INTO ".TABLE_POLL_OPTIONS." (pollid, optionname) "
                 ." VALUES ($pollid, '".$vars[$answervar]."')";
           $exe = runQuery($sql);
         }
       }
     }
     
     // Now create a post with this poll ID hooked up to it
     $thread = newThread ($vars['boardid'], $vars['question'], $vars['ownerid'], $vars['body'], $pollid);
     if ($thread)
     {
       return $thread;
     }
     else
     {
       return 0;
     }
   }

   //function to display text using username formatting
   Function userColorDisplay($usernameraw, $islink, $isclose = 0)
   {
     global $userdata;
     global $globaldebugmode;
     global $closestring;

     if($isclose)
       return $closestring;
     
     $closestring = "";
     $userid = -1;

     $safesearch = "";
     if (!false)
     { $safesearch = addSlashes($safesearch); }
     
     $sql = "SELECT u.ID, u.displayname "
           ."  FROM ".TABLE_USERS." u"
           ." WHERE u.displayname LIKE '%$safesearch%'"
           ." ORDER BY ID";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         if(strtolower($row['displayname']) == strtolower($usernameraw))
           $userid = $row['ID'];
       }
     }

     $displaytext = "<SPAN CLASS='AuthorLink'>";
     if($userid != -1)
     {
       if (($userdata['ID'] == $userid) && (is_array($userdata)))
       {
         $runmode = "Own Userdata";
  
         $userdisplaydata = $userdata;
       }
       else
       {
         // Either we pass in $fulldata, or we call getUserInfo()
         if (!$fulldata)
         {
           $runmode = "Database Query";

           $userdisplaydata = getUserInformation($userid);
         }
         else
         {
           $runmode = "Passed-in Data";

           $userdisplaydata = $fulldata;
         
           if (!$userid)
           {
             $userid = $fulldata['userid'];
           }
         }
       }
       
       if ($userdisplaydata['displayformat'])
       { 
         //echo "Got text format codes for $userdisplaydata[displayname].<BR>\n";
       
         // Turn $userdisplaydata['displayformat'] into [] markup codes.
         $formatprefs = unserialize($userdisplaydata['displayformat']);
         if ($globaldebugmode)
         {
           echo "<B>Name Formatting Preferences for ".$outputusername." from ".$runmode.":</B><BR>\n";
           print_r($formatprefs);
           echo "<BR>\n";
         }
         $displaystring = "";
         if ($formatprefs['stylebold'])     { $cssstylestring .= "font-weight: bold;"; }
             else                         { $cssstylestring .= "font-weight: normal;"; }
         if ($formatprefs['styleitalic'])    { $cssstylestring .= "font-style: italic;"; }
         
         if ($formatprefs['styleunderline'])    { $displaystring = "[u]".$displaystring; $closestring .= "[/u]"; }
         
         if ($formatprefs['stylestrikethrough'])    { $displaystring = "[strike]".$displaystring; $closestring .= "[/strike]"; }
         
         if ($formatprefs['styleoverline'])    { $cssstylestring .= "text-decoration: overline;"; }
             else                            { $cssstylestring .= "text-decoration: none;"; }
       
         if ($formatprefs['hlcolour'])       { $cssstylestring .= "background-color: ".$formatprefs['hlcolour'].";"; }
         if ($formatprefs['txtcolour'])      { $cssstylestring .= "color: ".$formatprefs['txtcolour'].";"; }
         // Borders. MSIE sucks, so we'll use the CSS border attribute if they're all on.
         if ($formatprefs['topborder'] && $formatprefs['bottomborder'] && $formatprefs['leftborder'] && $formatprefs['rightborder'])
                                         { $cssstylestring .= "border: 1px solid ".$formatprefs['bordercolour'].";"; }
         else
         {
           // They're not all on, so add the borders together individually
           if ($formatprefs['topborder'])    { $cssstylestring .= "border-top: 1px solid ".$formatprefs['bordercolour'].";"; }
           if ($formatprefs['bottomborder']) { $cssstylestring .= "border-bottom: 1px solid ".$formatprefs['bordercolour'].";"; }
           if ($formatprefs['leftborder'])   { $cssstylestring .= "border-left: 1px solid ".$formatprefs['bordercolour'].";"; }
           if ($formatprefs['rightborder'])  { $cssstylestring .= "border-right: 1px solid ".$formatprefs['bordercolour'].";"; }
         }
         $displaytext = textEffects($displaystring);
       
         if ($cssstylestring)
         {
           $displaytext = "<SPAN STYLE='".$cssstylestring."'>".$displaytext;
         }
       
       }  
     
//       $userdisplay = makeLink("user.php?user=".urlencode($userid), $outputusername, $linkclass);
     }

     return $displaytext;
   }



   
   Function listUsers($search, $pagestyle = "user", $options = "")
   {
     global $userdata;
     
     if (!$pagestyle) { $pagestyle = "user"; }
     
     if (is_array($options))
     {
       // Allow this function to search on anything that's thrown at it
       foreach ($options as $key => $value)
       {
         $condition[] = "$key = '$value'";
       }
       $conditions = "   AND ".implode(",", $condition);
     }
     
     $safesearch = $search;
     if (!false)
     { $safesearch = addSlashes($safesearch); }
     
     if ($pagestyle != "user")
     {
       // Users can match themselves in public, but not in the admin screen
       $usermatchcondition = "   AND u.ID <> ".$userdata['ID'];
     }
     
     $sql = "SELECT u.ID, u.displayname, c.classname, u.postcount, u.displayformat "
           ."  FROM ".TABLE_USERS." u, ".TABLE_ACCESS_CLASS." c"
           ." WHERE u.displayname LIKE '%$safesearch%'"
           .$usermatchcondition
           ."   AND u.classid = c.ID "
           .$conditions
           ." ORDER BY ID";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $i++;
         
         $checkbox = "<INPUT TYPE=CHECKBOX NAME='user".$i."' VALUE='".$row['ID']."'>";
         if ($pagestyle == "user")
         { $checkbox = ""; }
         
         // Do this to save usernameDisplay() from doing a database query
         $userformatarray = array("postcount" => $row['postcount'],
                                  "displayname" => $row['displayname'],
                                  "displayformat" => $row['displayformat']);

         $userrows .= "<TR><TD CLASS='BoardRowBody' WIDTH=50%>".usernameDisplay($row['ID'], "", "", $userformatarray)."</TD>\n"
                     ."    <TD CLASS='BoardRowBody' WIDTH=40%>".$row['classname']."</TD>\n"
                     ."    <TD CLASS='BoardRowBody' WIDTH=10% ALIGN=CENTER>".$checkbox."</TD></TR>\n";
         //if ($i < resultCount($exe))
         //{
         //  $userrows .= "<TR><TD COLSPAN=3 CLASS='BoardColumn'><IMG SRC='gfx/blank.gif' WIDTH=100 HEIGHT=1 ALT=''></TD></TR>\n";
         //}
       }

       $checkboxcount = $i;
       
       if ($pagestyle == "user")
       {
         $options = " <OPTION VALUE='addwatch'> Add to Watched User List </OPTION>\n"
                   ." <OPTION VALUE='delwatch'> Delete from Watched User List </OPTION>\n";
       }
       elseif ($pagestyle == "admin")
       {
         $classdata = fetchClasses();

         for ($i = 0; $i < count($classdata); $i++ )
         {
           $options .= " <OPTION VALUE='class".$classdata[$i]['ID']."'> Set user class: ".$classdata[$i]['classname']." </OPTION>\n";
         }
       }
       
       $selectgadget = "<TABLE WIDTH=100% CELLPADDING=10 CELLSPACING=1 BORDER=0><TR><TD CLASS='BoardRowBody'>\n"
                      ."<SELECT NAME='change'>\n"
                      .$options
                      ."</SELECT>BUTTON</TD></TR>\n"
                      ."</TABLE>\n";
       if ($pagestyle == "user")
       {
         $selectgadget = "";
       }
       
       $usertable = "<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1 BORDER=0>\n"
                   //."<TR><TD COLSPAN=3 CLASS='BoardColumn'><IMG SRC='gfx/blank.gif' WIDTH=100 HEIGHT=1 ALT=''></TD></TR>\n"
                   ."<TR><TD WIDTH=50% CLASS='BoardColumn'>Username</TD>\n"
                   ."    <TD WIDTH=40% CLASS='BoardColumn'>Class</TD>\n"
                   ."    <TD WIDTH=10% ALIGN=CENTER CLASS='BoardColumn'>&nbsp;</TD></TR>\n"
                   .$userrows
                   //."<TR><TD COLSPAN=3 CLASS='BoardColumn'><IMG SRC='gfx/blank.gif' WIDTH=100 HEIGHT=1 ALT=''></TD></TR>\n"
                   ."</TABLE>\n"
                   .$selectgadget;
     }
     
     $return['usertable']     = $usertable;
     $return['resultcnt']     = resultCount($exe);
     $return['checkboxcount'] = $checkboxcount;
     
     return $return;
   }
   
   Function fetchClasses()
   {
     $sql = "SELECT ID, classname FROM ".TABLE_ACCESS_CLASS." ORDER BY ID";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $result[] = $row;
       }
     }
     return $result;
   }
   
   Function getUserInformation($userid)
   {
     $sql = "SELECT u.*, a.classname, "
           ."       c.accessadmin, c.accessmanager, c.accessmoderator, c.accessvip, c.accessinsider, c.accessread, "
           ."       c.accesswrite, c.accessdelete, c.accesstimeedit, "
           ."       c.accessfulledit, c.accessnameformat, c.accesslogin "
           ."  FROM ".TABLE_USERS." u, ".TABLE_ACCESS_CLASS." a, ".TABLE_CLASS_PERMISSION." c "
           ." WHERE u.classid = a.ID "
           ."   AND c.classid = a.ID "
           ."   AND u.ID = $userid";
     if ($exe = runQuery($sql))
     {
       return fetchResultArray($exe);
     }
   }
   
   
   Function isSticky($threadid)
   {
     $threaddata = fetchRow($threadid, TABLE_THREADS);
     return $threaddata['sticky'];
   }
   
   Function isLocked($threadid, $threaddata = "")
   {
     if (!$threaddata)
     {
       $threaddata = fetchRow($threadid, TABLE_THREADS);
     }
     
     if ($threaddata['status'] == "L")
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   Function setSticky($threadid, $sticky)
   {
     $sql = "UPDATE ".TABLE_THREADS." SET sticky = ".intval($sticky)." WHERE ID = $threadid";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     return 0;
   }
   
   Function toggleLock($threadid, $new)
   {
     $sql = "UPDATE ".TABLE_THREADS." SET status = '".$new."' WHERE ID = $threadid";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     return 0;
   }
   
   Function fetchClassName($classid)
   {
     $sql = "SELECT classname FROM ".TABLE_ACCESS_CLASS." WHERE ID = $classid";
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
     }
     return $row['classname'];
   }
   
   
   Function removePost($id, $type = "post")
   {
     switch ($type)
     {
       case "post":
         $fieldname = "ID";
         break;
       case "thread":
         $fieldname = "threadid";
         break;
     }
     
     if (is_array($id))
     {
       for ($i = 0; $i < count($id); $i++ )
       {
         $condition[] = $fieldname." = $id ";
       }
       $conditions = implode(" OR ", $condition);
     }
     else
     {
       $conditions = $fieldname." = $id ";
     }
     
     // See if there are any related polls?
     $sql = "SELECT DISTINCT pollid FROM ".TABLE_POSTS." WHERE ".$conditions;
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         if ($row['pollid'])
         {
           $pollid[] = $row['pollid'];
         }
       }
     }
     
     if (is_array($pollid))
     {
       //echo "A poll is being deleted...<BR>\n";
       $pollids = "(".implode(",", $pollid).")";
       $delpollsql = "DELETE FROM ".TABLE_POLLS." WHERE ID in ".$pollids;
       $delpollexe = runQuery($delpollsql);
     }

     $sql = "DELETE FROM ".TABLE_POSTS." WHERE ".$conditions;
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   Function removeThread($id)
   {
     if (is_array($id))
     {
       for ($i = 0; $i < count($id); $i++ )
       {
         $condition[]  = "ID = ".$id[$i]." ";
         $delthreads[] = $id[$i];
       }
       $conditions = implode(" OR ", $condition);
     }
     else
     {
       $conditions = "ID = $id ";
       $delthreads[] = $id;
     }
     
     // Remove posts from this thread before you delete it
     foreach ($delthreads as $key => $value)
     {
       removePost($value, "thread");
     }
     
     $sql = "DELETE FROM ".TABLE_THREADS." WHERE ".$conditions;
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   
   Function fetchIDs($id, $idtype = "group")
   {
     // If fed a group ID, returns board IDs below it.
     // If fed a board ID, returns thread IDs below it.
     // If fed a thread ID, returns post IDs below it.
     
     switch ($idtype)
     {
       case "group":
         $tablename = TABLE_BOARDS;
         $fieldname = "groupid";
         break;
       case "board":
         $tablename = TABLE_THREADS;
         $fieldname = "boardid";
         break;
       case "thread":
         $tablename = TABLE_POSTS;
         $fieldname = "threadid";
         break;
     }
     
     $sql = "SELECT ID from ".$tablename." WHERE ".$fieldname." = ".$id." ORDER BY ID ASC";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $return[] = $row['ID'];
       }
     }
     
     return $return;
   }

   Function updateBoardData($boardid, $newname, $newrank, $newdescription, $newgroupid, $newvippost, $newprivate)
   {
     if (!false)
     { 
       $newname = addSlashes($newname); 
       $newrank = addSlashes($newrank); 
       $newdescription = addSlashes($newdescription); 
     }
     
     $sql = "UPDATE ".TABLE_BOARDS." "
           ."   SET boardname = '$newname', boardrank = '$newrank', "
           ."       description = '$newdescription', vippost = '$newvippost', private = '$newprivate', groupid = ".intval($newgroupid)
           ." WHERE ID = ".intval($boardid);
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }

   Function updateThread($threadid, $data)
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
         $fieldupdate[] = "$key = '$value' ";
       }
     }
     $fieldupdates = implode(",", $fieldupdate);
     
     $sql = "UPDATE ".TABLE_THREADS." "
           ."   SET ".$fieldupdates." "
           ." WHERE ID = $threadid";
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   Function listIconGroups()
   {
     global $PHP_SELF;
     
     $sql = "SELECT ig.ID, ig.groupname, ig.classid "
           ."  FROM ".TABLE_ICON_GROUPS." ig "
           ." GROUP BY ID "
           ." ORDER BY groupname";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $classnameout = "";
         if ($row['classid'])
         { $classnameout = fetchClassName($row['classid']); }
         
         $groupoutput .= "<TR><TD CLASS='BoardRowBody' ALIGN=CENTER><A HREF='$PHP_SELF?action=manageicons&step=2&groupid=".$row['ID']."'>".$row['ID']."</A></TD>\n"  
                        ."    <TD CLASS='BoardRowBody'>".$row['groupname']."</TD>\n"
                        ."    <TD CLASS='BoardRowBody'>".$classnameout."</TD>\n"
                        ."    <TD WIDTH=10% CLASS='BoardRowBody'><A HREF='$PHP_SELF?action=manageicons&step=editgroup&groupid=".$row['ID']."'>Edit</A></TD>\n"
                        ."    <TD WIDTH=10% CLASS='BoardRowBody'><A HREF='$PHP_SELF?action=manageicons&step=removegroup&groupid=".$row['ID']."'>Remove</A></TD></TR>\n";
       }
       
       $groupoutput = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
                     ."<TR><TD CLASS='BoardColumn' WIDTH=5% ALIGN=CENTER>ID</TD>\n"
                     ."    <TD CLASS='BoardColumn' WIDTH=40%>Name</TD>\n"
                     ."    <TD CLASS='BoardColumn' WIDTH=35%>Class</TD>\n"
                     ."    <TD CLASS='BoardColumn' COLSPAN=2 WIDTH=20%>Options</TD></TR>\n"
                     .$groupoutput
                     ."</TABLE>\n";
     }
     
     return $groupoutput;
   }
   
   Function inputUserClass($name, $value, $editorshow = "edit", $firstdisplay = "", $firstvalue = "", $prepend = "", $append = "", $extraoptions = "")
   {
     $sql = "SELECT ID, classname "
           ."  FROM ".TABLE_ACCESS_CLASS." "
           ." ORDER BY classname";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $select = "";
         if ($value == $row['ID'])
         { 
           $select = " SELECTED"; 
           $actual = $prepend.$row['classname'].$append;
         }
         
         $classopt .= " <OPTION VALUE='".$row['ID']."'".$select."> ".$prepend.$row['classname'].$append." </OPTION>\n";
       }
       if (is_array($extraoptions))
       {
         foreach($extraoptions as $key => $value)
         {
           $select = "";
           if ($value == $key)
           { $select = " SELECTED"; }
           
           $classopt .= " <OPTION VALUE='".$key."'".$select."> ".$value." </OPTION>\n";
         }
       }
       
       if ($firstdisplay)
       {
         $firstopt = " <OPTION VALUE='".$firstvalue."'> ".$firstdisplay." </OPTION>\n";
       }
       $classselect = "<SELECT NAME='$name'>\n"
                     .$firstopt
                     .$classopt
                     ."</SELECT>\n";

       if ($editorshow == "edit")
       {
         return $classselect;
       }
       else
       {
         return $actual;
       }
     }
   }
   
   Function inputIconGroup($name, $value, $editorshow = "edit", $firstdisplay = "", $firstvalue = "", $prepend = "", $append = "", $extraoptions = "")
   {
     $sql = "SELECT ID, groupname "
           ."  FROM ".TABLE_ICON_GROUPS." "
           ." ORDER BY groupname";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $select = "";
         if ($value == $row['ID'])
         { 
           $select = " SELECTED"; 
           $actual = $prepend.$row['groupname'].$append;
         }
         
         $classopt .= " <OPTION VALUE='".$row['ID']."'".$select."> ".$prepend.$row['groupname'].$append." </OPTION>\n";
       }
       if (is_array($extraoptions))
       {
         foreach($extraoptions as $key => $value)
         {
           $select = "";
           if ($value == $key)
           { $select = " SELECTED"; }
           
           $classopt .= " <OPTION VALUE='".$key."'".$select."> ".$value." </OPTION>\n";
         }
       }
       
       if ($firstdisplay)
       {
         $firstopt = " <OPTION VALUE='".$firstvalue."'> ".$firstdisplay." </OPTION>\n";
       }
       $classselect = "<SELECT NAME='$name'>\n"
                     .$firstopt
                     .$classopt
                     ."</SELECT>\n";

       if ($editorshow == "edit")
       {
         return $classselect;
       }
       else
       {
         return $actual;
       }
     }
   }
   
   Function inputIconGroupUser($name, $value, $editorshow = "edit", $firstdisplay = "", $firstvalue = "", $prepend = "", $append = "", $extraoptions = "")
   {
     global $userdata;
     
     $sql = "SELECT ID, groupname "
           ."  FROM ".TABLE_ICON_GROUPS." "
           ." WHERE classid = 0 OR classid IS NULL OR classid = ".$userdata['classid']." "
           ." ORDER BY groupname";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $select = "";
         if ($value == $row['ID'])
         { 
           $select = " SELECTED"; 
           $actual = $prepend.$row['groupname'].$append;
         }
         
         $classopt .= " <OPTION VALUE='".$row['ID']."'".$select."> ".$prepend.$row['groupname'].$append." </OPTION>\n";
       }
       if (is_array($extraoptions))
       {
         foreach($extraoptions as $key => $value)
         {
           $select = "";
           if ($value == $key)
           { $select = " SELECTED"; }
           
           $classopt .= " <OPTION VALUE='".$key."'".$select."> ".$value." </OPTION>\n";
         }
       }
       
       if ($firstdisplay)
       {
         $firstopt = " <OPTION VALUE='".$firstvalue."'> ".$firstdisplay." </OPTION>\n";
       }
       $classselect = "<SELECT NAME='$name'>\n"
                     .$firstopt
                     .$classopt
                     ."</SELECT>\n";

       if ($editorshow == "edit")
       {
         return $classselect;
       }
       else
       {
         return $actual;
       }
     }
   }
   
   Function checkIconUserPermission($iconid, $userid)
   {
     global $userdata;
     
     $userinfo = $userdata;
     if ($userdata['ID'] != $userid)
     {
       $userinfo = fetchRow($userid, TABLE_USERS);
     }
     
     $sql = "SELECT ig.classid "
           ."  FROM ".TABLE_ICONS." i, ".TABLE_ICON_GROUPS." ig "
           ." WHERE i.groupid = ig.ID "
           ."   AND i.ID = ".intval($iconid);
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);

       if ($row['classid'] > 0)
       {
         if ($row['classid'] == $userinfo['classid'])
         {
           return 1;
         }
         else
         {
           return 0;
         }
       }
       else
       {
         return 1;
       }
     }
   }
   
   Function incrementViewCount($threadid, $newviewcount)
   {
     $sql = "UPDATE ".TABLE_THREADS." SET viewcount = ".intval($newviewcount)." WHERE ID = ".intval($threadid);
     if ($exe = runQuery($sql))
     {
       return 1;
     }
     else
     {
       return 0;
     }
   }
   
   Function countUserEmails($emailaddress)
   {
     $safeemail = $emailaddress;
     if (!false)
     {
       $safeemail = addSlashes($safeemail);
     }
     $sql = "SELECT COUNT(*) AS result FROM ".TABLE_USERS." WHERE contactemail = '$safeemail'";
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       
       $return = $row['result'];
     }
     //print_r($return);
     return $return;
   }

   Function admincountUserEmails($emailaddress)
   {
     return 0;
   }


   Function countUsers()
   {
     $safeemail = $emailaddress;
     if (!false)
     {
       $safeemail = addSlashes($safeemail);
     }
     $sql = "SELECT c.classname, c.ID AS classid, COUNT(u.ID) AS usercount "
           ."  FROM ".TABLE_USERS." u, ".TABLE_ACCESS_CLASS." c "
           ." WHERE u.classid = c.ID "
           ." GROUP BY classname "
           ." ORDER BY classname";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $userinfo[] = $row;
       }
     }
     return $userinfo;
   }
   
   Function fetchLoggedinUsers($numberofusers = 10, $mode = "normal")
   {
     if ($numberofusers)
     {
       $limit = " LIMIT 0, ".intval($numberofusers);
     }
     
     if ($mode == "recent")
     {
       // Only show users whose activity was recent (ie, last 10 minutes)
       $activitycondition = "   AND lastactivity > ".(Date("U")-600);
     }
     
     $sql = "SELECT b.username, b.lastactivity, u.postcount, u.displayname, u.displayformat "
           ."  FROM ".TABLE_BOARD_SESSIONS." b, ".TABLE_USERS." u"
           ." WHERE b.username = u.ID "
           ."   AND expirytime >= ".Date("U")
           .$activitycondition
           ." ORDER BY lastactivity DESC "
           .$limit;
     if ($exe = runQuery($sql))
     {
       if (resultCount($exe))
       {
         // Initialise the array
         $userids = array();

         while ($row = fetchResultArray($exe))
         {
           // We've pulled out enough data to send to usernameDisplay() so that it doesn't need
           // to do any database queries itself...
           $dataarray = array("postcount" => $row['postcount'], 
                              "displayname" => $row['displayname'], 
                              "displayformat" => $row['displayformat']);
           
           // Get the light and dark versions of the username
           $username = usernameDisplay($row['username'], "", "showstar", $dataarray);
           $usernamelight = usernameDisplay($row['username'], "MainMenuLinkLight", "", $dataarray);
           
           // Don't repeat usernames
           if (!in_array($row['username'], $userids))
           {
             $userids[] = $row['username'];
             $users[] = $usernamelight;
             
             // Now we keep this to make our users-currently-online table later
             $sessiondata[] = array("username" => $username,
                                    "lastactivity" => $row['lastactivity']);
           }
         }
         
         $usernames = implode(", ", $users);
       }
     }
     
     // Now we make the table
     for ($i = 0; $i < count($sessiondata); $i++ )
     {
       $tablerow .= "<TR><TD CLASS='BoardRowHeading'>".$sessiondata[$i]['username']."</TD>\n"
                   ."    <TD CLASS='BoardRowBody'>".dateNeat($sessiondata[$i]['lastactivity'])."</TD></TR>\n";
     }
     $table = "<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1 BORDER=0>\n"
             ."<TR><TD WIDTH=25% CLASS='BoardColumn'>Username</TD>\n"
             ."    <TD WIDTH=75% CLASS='BoardColumn'>Last Activity</TD></TR>\n"
             .$tablerow
             ."</TABLE>\n";

     $return['userids']   = $userids;
     $return['usernames'] = $users;
     $return['userlist']  = $usernames;
     $return['html']      = $table;
     
     return $return;
   }
   
   Function fetchPreviousPost($boardid, $postid)
   {
     $postidcondition = "   AND p.ID <> ".intval($postid);
     if (is_array($postid))
     {
       $postidcondition = "   AND p.ID NOT IN (".implode(",", $postid).")";
     }
     $sql = "SELECT MAX(p.ID) AS result "
           ."  FROM ".TABLE_POSTS." p, ".TABLE_THREADS." t "
           ." WHERE p.threadid = t.ID "
           ."   AND t.boardid = ".intval($boardid)." "
           .$postidcondition;
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       $returnid = $row['result'];
     }
     
     return $returnid;
   }
   
   Function boardStats()
   {
     global $configoptions;
     
     // Item 1: Posts (public) total
     $sql = "SELECT COUNT(*) AS result FROM ".TABLE_POSTS." WHERE status IS NULL";
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       $totalpostcount = $row['result'];
     }
     
     // Item 2: Boards count
     $sql = "SELECT COUNT(*) AS result FROM ".TABLE_BOARDS;
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       $boardcount = $row['result'];
     }
     
     // Item 3: Posts (public) today
     $now = Date("U");
     
     // First, find out what timezone adjustment has to be made to the user's time
     if ($configoptions['timezonechange'])
     {
       $adjustment = ($configoptions['timezonechange']*3600);
     }
     //echo "Adjustment is $adjustment<BR>\n";
     
     // Adjust our time to be theirs
     $usertime = $now+$adjustment;
     
     // Get the timestamp for midnight in their time, not ours
     $midnight = Date("U", mktime(0, 0, 0, Date("m", $usertime), Date("d", $usertime), Date("Y", $usertime)));
     //echo "Midnight SEEMS to be ".Date("d M Y H:i:s", $midnight)." ($midnight)<BR>\n";
     
     // Reverse the previous adjustment to turn it back into our timezone
     $midnight = $midnight-$adjustment;
     //echo "But, adjusted, its... ".Date("d M Y H:i:s", $midnight)." ($midnight)<BR>\n";
     
     $sql = "SELECT COUNT(*) AS result FROM ".TABLE_POSTS." WHERE postdate >= ".$midnight." AND status IS NULL";
     if ($exe = runQuery($sql))
     {
       $row = fetchResultArray($exe);
       $totalpoststoday = $row['result'];
     }
     
     $return['total']  = $totalpostcount;
     $return['boards'] = $boardcount;
     $return['today']  = $totalpoststoday;
     
     return $return;
   }
   
   Function overThreshold ($thresholdtype, $userid)
   {
     global $configoptions;
     
     $threshold = $configoptions['threshold'][$thresholdtype];
     
     $limit = $threshold['limit'];
     $time  = $threshold['time'];
     
     //echo "Threshold is $limit in $time...<BR>\n";
     
     if ($limit)
     {
       $now = Date("U");
       $threshtime = $now-$time;

       if ($thresholdtype == "post")
       {
         $sql = "SELECT COUNT(*) AS result FROM ".TABLE_POSTS." WHERE authorid = ".$userid." AND status IS NULL AND postdate > ".$threshtime;
         $exe = runQuery($sql);
         $row = fetchResultArray($exe);
         $postcount = $row['result'];
       }
       if ($thresholdtype == "poll")
       {
         $sql = "SELECT COUNT(*) AS result FROM ".TABLE_POSTS." WHERE authorid = ".$userid." AND pollid > 1 AND postdate > ".$threshtime;
         $exe = runQuery($sql);
         $row = fetchResultArray($exe);
         $postcount = $row['result'];
       }
       
       //echo "$postcount vs $limit<BR>\n";
       if ($postcount >= $limit)
       {
         return 1;
       }
       else
       {
         return 0;
       }
     }
     else
     {
       return 0;
     }
   }
   
   Function getWatchedUsers($userid)
   {
     // Fetch the user's watched user ids
     $sql = "SELECT f.ID, f.userid, u.postcount, u.displayname, u.displayformat "
           ."  FROM ".TABLE_FAVOURITES." f, ".TABLE_USERS." u"
           ." WHERE ownerid = ".$userid
           ."   AND f.userid = u.ID "
           ."   AND userid IS NOT NULL "
           ." ORDER BY u.displayname";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $user[] = $row;
       }
     }
     
     return $user;
   }
   
   Function formatWatchedUsers($userids)
   {
     if (count($userids))
     {
       // Turn the thread IDs into a list of OR conditions in SQL...
       for ($i = 0; $i < count($userids); $i++ )
       {
         $displaydata = array("postcount" => $userids[$i]['postcount'],
                              "displayname" => $userids[$i]['displayname'],
                              "displayformat" => $userids[$i]['displayformat']);
         $useroutput = usernameDisplay($userids[$i]['userid'], "", "", $displaydata);

         // Comma-delimited version for profiles
         $usernamearray[] = $useroutput;
         
         // Build a table list for WUL management
         $usernamelistrows .= "<TR><TD CLASS='BoardRowBody'>".$useroutput."</TD>\n"
                         ."    <TD ALIGN=CENTER CLASS='BoardRowBody'>".inputCheckbox("select".($i+1), $userids[$i]['userid'])."</TD></TR>\n";
       }
       
       $usernamelist = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
                      ."<TR><TD WIDTH=90% CLASS='BoardColumn'>Username</TD>\n"
                      ."    <TD WIDTH=10% CLASS='BoardColumn' ALIGN=CENTER>?</TD></TR>\n"
                      .$usernamelistrows
                      ."<TR><TD CLASS='BoardColumn'>&nbsp;</TD>\n"
                      ."    <TD CLASS='BoardColumn' ALIGN=CENTER>".inputSubmit("Remove")."</TD></TR>\n"
                      ."</TABLE>\n";
       $dontwrapoutput = 1;
     }
     else
     {
       $usernamearray[] = "None";
       $usernamelist = "You are not watching any users";
       $dontwrapoutput = 0;
     }
     
     $return['commadelimited'] = implode(", ", $usernamearray);
     $return['html'] = $usernamelist;
     $return['checkboxcount'] = count($userids);
     $return['dontwrapoutput'] = $dontwrapoutput;
     
     return $return;
   }

   Function usersWatching($userid)
   {
     $sql = "SELECT f.ownerid AS userid, uwatching.postcount, uwatching.displayname, uwatching.displayformat "
           ."  FROM ".TABLE_FAVOURITES." f, ".TABLE_USERS." uwatching "
           ." WHERE f.ownerid = uwatching.ID "
           ."   AND f.userid = ".$userid
           ." ORDER BY uwatching.displayname";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $watching[] = $row;
       }
     }
     
     $return['watchinguserids'] = $watching;
     $return['watchingcount'] = resultCount($exe);

     return $return;
   }
   
   Function watchedThreads($userid)
   {
     // Fetch the user's watched threads
     $sql = "SELECT ID, threadid "
           ."  FROM ".TABLE_FAVOURITES." "
           ." WHERE ownerid = ".$userid
           ."   AND threadid IS NOT NULL "
           ." ORDER BY ID ASC";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $thread[] = $row['threadid'];
       }
     }
     
     return $thread;
   }
   
   Function outputWatchedThreads ($threadids)
   {
     //$output['threadlist'] = "listing threads ".implode(",", $threadids);
     
     if (count($threadids))
     {
       // Turn the thread IDs into a list of OR conditions in SQL...
       for ($i = 0; $i < count($threadids); $i++ )
       {
         $threadconditionsql[] = "t.ID = ".$threadids[$i];
       }
       $threadcondition = "   AND ( ".implode(" OR ", $threadconditionsql)." )";
       
       $sql = "SELECT t.ID, t.sticky, t.status, t.viewcount, t.postcount, t.oldboardid, "
             ."       fp.subject, fp.authorid, fp.pollid, LENGTH(fp.body) AS  postlength, "
             ."       fp.authorid as firstpostuserid, "
             ."       ufp.displayname as firstpostusername, "
             ."       ufp.displayformat as firstpostusernameformat, "
             ."       fp.postdate AS firstpostdate, "
             ."       lp.ID AS lastpostid, "
             ."       lp.authorid AS lastpostuserid, "
             ."       ulp.displayname AS lastpostusername, "
             ."       ulp.displayformat AS lastpostusernameformat, "
             ."       lp.postdate AS lastpostdate"
             ."  FROM ".TABLE_THREADS." t, ".TABLE_USERS." ufp, ".TABLE_USERS." ulp, ".TABLE_POSTS." fp, ".TABLE_POSTS." lp "
             ." WHERE fp.ID = t.postidfirst "
             ."   AND lp.ID = t.postidlast "
             ."   AND ufp.ID = fp.authorid "
             ."   AND ulp.ID = lp.authorid "
             //."   AND t.boardid = $boardid "
             .$threadcondition
             ." ORDER BY sticky DESC, lastpostdate DESC"
             //." LIMIT ".intval($startrow).", ".intval($configoptions['perpagethreads'])
             ."";
       if ($exe = runQuery($sql))
       {
         $threadlist = formatThreadList($exe, array("extracol" => "checkbox", "submit" => "Remove"));
         
         $return['threadlist'] = $threadlist['html'];
         $return['checkboxcount'] = $threadlist['checkboxcount'];
       }
       $return['dontwrapoutput'] = 1;
     }
     else
     {
       $return['threadlist'] = "<DIV ALIGN=CENTER>You are not watching any threads</DIV>";
       $return['dontwrapoutput'] = 0;
     }
     return $return;
   }
   
   Function getSystemBans()
   {
     $sql = "SELECT sb.*, admin.displayname AS admindisplayname, admin.displayformat AS admindisplayformat"
           ."  FROM ".TABLE_SYSTEM_BAN." sb, ".TABLE_USERS." admin "
           ." WHERE sb.adminid = admin.ID ";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         $bans[] = $row;
         
         // Format usernames properly
         $username = "-";
         if ($row['userid'])
         {
           $username = usernameDisplay($row['userid']);
         }
         $admindataarray = array("displayname" => $row['admindisplayname'], 
                                "displayformat" => $row['admindisplayformat']);
         $adminname = usernameDisplay($row['adminid'], "", "", $admindataarray);
         
         $ipoutput = long2ip($row['ip_start']);
         if ($row['ip_stop'] != $row['ip_start'])
         {
           $ipoutput = long2ip($row['ip_start'])." to ".long2ip($row['ip_stop']);
         }
         
         $active = "Inactive";
         if ($row['active'])
         {
           $active = "Active";
         }
         
         $tabledata .= "<TR><TD CLASS='BoardRowBody'>".$ipoutput."</TD>\n"
                      ."    <TD CLASS='BoardRowBody'>".$username."</TD>\n"
                      ."    <TD CLASS='BoardRowBody'>".$adminname."</TD>\n"
                      ."    <TD CLASS='BoardRowBody'>".$active."</TD>\n"
                      ."    <TD CLASS='BoardRowBody' ALIGN=CENTER>".inputCheckBox("ban".$row['ID'], "del")."</TD></TR>\n";
       }
       $bancount = resultCount($exe);
       if (!$bancount)
       {
         $tabledata = "<TR><TD CLASS='BoardRowBody' COLSPAN=4 ALIGN=CENTER>No system IP bans found</TD></TR>\n";
       }
       else
       {
         $tabledata .= "<TR><TD COLSPAN=4 CLASS='BoardColumn'>&nbsp;</TD>\n"
                      ."    <TD CLASS='BoardColumn'>".inputSubmit("Delete")."</TD></TR>\n";
       }
       
       $table = "<TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=1 BORDER=0>\n"
               ."<TR><TD WIDTH=30% CLASS='BoardColumn'>IP Address</TD>\n"
               ."    <TD WIDTH=25% CLASS='BoardColumn'>Username</TD>\n"
               ."    <TD WIDTH=25% CLASS='BoardColumn'>Set By</TD>\n"
               ."    <TD WIDTH=20% CLASS='BoardColumn'>Status</TD>\n"
               ."    <TD WIDTH=20% CLASS='BoardColumn' ALIGN=CENTER>?</TD></TR>\n"
               .$tabledata
               ."</TABLE>\n";
     }
     
     $return['html'] = $table;
     $return['bancount'] = $bancount;
     $return['bans'] = $bans;
     
     return $return;
   }
   
   Function isValidIP($ip)
   {
     if(!is_string($ip))
     return false;
  
     $ip_long = ip2long($ip);
     $ip_reverse = long2ip($ip_long);
     if($ip == $ip_reverse)
       return true;
     else
       return false;
   }

   Function assessBanStatus($ipaddress, $userid)
   {
     $iplong = ip2long($ipaddress);
     
     $sql = "SELECT * "
           ."  FROM ".TABLE_SYSTEM_BAN
           ." WHERE ".$iplong." BETWEEN ip_start AND ip_stop "
           ."   AND active = 1";
     if ($exe = runQuery($sql))
     {
       while ($row = fetchResultArray($exe))
       {
         // Okay, we've found a matching ban - decide if the username is correct or not
         if ($row['userid'] == $userid)
         {
           $banned = 1;
         }
         elseif (!$row['userid'])
         {
           $banned = 1;
         }
       }
     }
     $return['banned'] = $banned;
     return $return;
   }
?>