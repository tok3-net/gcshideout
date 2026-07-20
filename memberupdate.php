<?php
   $starttime = microtime();
   include("common.php");
$navigationhead = "Member Update";

if (!$action || $action == "post")
{
// $sql = "SELECT * FROM ".TABLE_USERS." ORDER BY ID ASC";
$sql = "SELECT u.ID, u.displayname, u.created, u.postcount, u.classid, c.ID AS classid, c.classname FROM ".TABLE_USERS." u, ".TABLE_ACCESS_CLASS." c WHERE c.ID = u.classid ORDER BY postcount DESC LIMIT 50";
$exe = runQuery($sql);
$output = "<TABLE BORDER=0 CLASS='BoardRowBody'  WIDTH=100%><TR><TD WIDTH=100% ALIGN=CENTER>"
."<h1>".$SiteName." Member Update</h1></TD></TR><TR ALIGN=CENTER><TD ALIGN=CENTER>"
."<TABLE BORDER=1 CELLSPACING=1 CELLPADDING=2 CLASS='BoardRowBody'>"
."<TR><TD CLASS='MenuHeading' ALIGN=CENTER><B>Rank</B></TD>
	<TD CLASS='MenuHeading' ALIGN=CENTER><B>Username</B></TD>
	<TD CLASS='MenuHeading' ALIGN=CENTER><B>Postcount</B></TD>
	<TD CLASS='MenuHeading' ALIGN=CENTER><B>WUL Count</B></TD>
	<TD CLASS='MenuHeading' ALIGN=CENTER><B>Last Post Date</B></TD></TR>";

$rank = 1;
while ($row = fetchResultArray($exe))
{
$watchinguserdata = usersWatching($row['ID']);
$watchinguserids = $watchinguserdata['watchinguserids'];
$watchingusersoutput = intval($watchinguserdata['watchingcount']);
$output .= "<TR ALIGN=CENTER><TD ALIGN=CENTER>".$rank."</TD>
	<TD ALIGN=CENTER>".usernameDisplay($row['ID'],'','showstar')."</TD>
	<TD ALIGN=CENTER>".$row['postcount']."</TD>
	<TD ALIGN=CENTER>".$watchingusersoutput."</TD>
	<TD ALIGN=CENTER>".getLastPostOnly($row['ID'])."</TD></TR>\n";
$rank++;
}

$output .= "</TABLE></TD></TR></TABLE>";
}

   
$pagecontents = $output;
   include("layout.php");
?>