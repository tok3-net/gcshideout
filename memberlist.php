<?php
   $starttime = microtime();
   include("common.php");
$navigationhead = "Member List";

// $sql = "SELECT * FROM ".TABLE_USERS." ORDER BY ID ASC";
$sql = "SELECT u.ID, u.displayname, u.lastlogin, u.created, u.classid, c.ID AS classid, c.classname FROM ".TABLE_USERS." u, ".TABLE_ACCESS_CLASS." c WHERE c.ID = u.classid ORDER BY ID ASC";
$exe = runQuery($sql);
$output = "<CENTER><TABLE BORDER=1 CELLSPACING=1 CELLPADDING=2 CLASS='BoardRowBody'>";
$output .= "<TR><TD CLASS='MenuHeading' ALIGN=CENTER><B>ID</B></TD>
	<TD CLASS='MenuHeading' ALIGN=CENTER><B>Username</B></TD>
	<TD CLASS='MenuHeading' ALIGN=CENTER><B>Access Class</B></TD>
	<TD CLASS='MenuHeading' ALIGN=CENTER><B>Watch User</B></TD>
	<TD CLASS='MenuHeading' ALIGN=CENTER><B>Private Message</B></TD>
	<TD CLASS='MenuHeading' ALIGN=CENTER><B>Joined Date</B></TD>
	<TD CLASS='MenuHeading' ALIGN+CENTER><B>Last Login Date</B></TD></TR>";
while ($row = fetchResultArray($exe))
{
$output .= "<TR><TD ALIGN=CENTER>".$row['ID']."</TD>
	<TD ALIGN=CENTER>".usernameDisplay($row['ID'],'','showstar')."</TD>
	<TD ALIGN=CENTER>".$row['classname']."</TD>
	<TD ALIGN=CENTER><A HREF='watch.php?action=adduser&userid=".$row['ID']."&returnurl=/boards/user.php?user=".$row['ID']."'>Watch User</A></TD>
	<TD ALIGN=CENTER><A HREF='private.php?action=send&recipient=".$row['displayname']."'>Send PM</A></TD>
	<TD ALIGN=CENTER>".dateNeat($row['created'])."</TD>
	<TD ALIGN=CENTER>".dateNeat($row['lastlogin'])."</TR>\n";
}

$output .= "</TABLE></CENTER>";
   
$pagecontents = $output;
   include("layout.php");
?>