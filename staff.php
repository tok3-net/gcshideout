<?php
   $starttime = microtime();
   include("common.php");
$protectedpage = 1;
$navigationhead = "Staff List";

// $sql = "SELECT * FROM ".TABLE_USERS." ORDER BY ID ASC";
$sql = "SELECT u.ID, u.displayname, u.lastlogin, u.created, u.classid, c.ID AS classid, c.classname FROM ".TABLE_USERS." u, ".TABLE_ACCESS_CLASS." c WHERE c.ID = u.classid ORDER BY ID ASC";
$exe = runQuery($sql);
$output = "<TABLE CLASS='BoardRowBody' WIDTH=100%><TR><TD WIDTH=100% ALIGN=CENTER><H1>Staff List</H1></TD></TR></TABLE>"
."<TABLE BORDER=1 CELLSPACING=1 CELLPADDING=2 CLASS='BoardRowBody' WIDTH=100%>";
while ($row = fetchResultArray($exe))
{
$classid = $row['classid'];
if ($classid == "1")
{
$adminnames = "<H3><U>".$row['classname']."</U></H3>";
$admin .= "<A HREF='user.php?user=".$row['ID']."' CLASS='AuthorLink'>".usernameDisplay($row['ID'],'','showstar')."</A><BR>";
}
if ($classid == "2")
{
$managername = "<H3><U>".$row['classname']."</U></H3>";
$manager .= "<A HREF='user.php?user=".$row['ID']."' CLASS='AuthorLink'>".usernameDisplay($row['ID'],'','showstar')."</A><BR>";
}
if ($classid == "3")
{
$modname = "<H3><U>".$row['classname']."</U></H3>";
$mod .= "<A HREF='user.php?user=".$row['ID']."' CLASS='AuthorLink'>".usernameDisplay($row['ID'],'','showstar')."</A><BR>";
}
if ($classid == "4")
{
$vipname = "<H3><U>".$row['classname']."</U></H3>";
$vip .= "<A HREF='user.php?user=".$row['ID']."' CLASS='AuthorLink'>".usernameDisplay($row['ID'],'','showstar')."</A><BR>";
}
}

$output .= "<TR><TD WIDTH=25% ALIGN=CENTER VALIGN=TOP>".$adminnames
.$admin
."</TD>"
."<TD WIDTH=25% ALIGN=CENTER VALIGN=TOP>".$managername
.$manager
."</TD>"
."<TD WIDTH=25% ALIGN=CENTER VALIGN=TOP>".$modname
.$mod
."</TD>"
."<TD WIDTH=25% ALIGN=CENTER VALIGN=TOP>".$vipname
.$vip
."</TD>"
."</TR></TABLE>";
   
$pagecontents = $output;
   include("layout.php");
?>