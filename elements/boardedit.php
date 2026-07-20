<?php
if ($boardvippost == "1")
{
$bvippost = "True";
}
if (!$boardvippost)
{
$bvippost = "False";
}
if ($boardprivate == "1")
{
$bprivate = "VIP+";
}
if ($boardprivate == "2")
{
$bprivate = "Insider";
}
if (!$boardprivate)
{
$bprivate = "False";
}
$boardform = "
<SPAN CLASS='InputSection'>Current Settings</SPAN><BR>
<TABLE>
<TR><TD CLASS='BoardColumn'>Board ID</TD>    <TD>".$boardid."</TD></TR>
<TR><TD CLASS='BoardColumn'>Name</TD>        <TD>".$boardname."</TD></TR>
<TR><TD CLASS='BoardColumn'>Description</TD> <TD>".$boarddescription."</TD></TR>
<TR><TD CLASS='BoardColumn'>Rank</TD>        <TD>".$boardrank."</TD></TR>
<TR><TD CLASS='BoardColumn'>Group</TD>       <TD>".inputDBCycle("", $boardgroupid, TABLE_GROUPS, "ID", "groupname", "show", "grouprank")."</TD></TR>
<TR><TD CLASS='BoardColumn'>VIP+ Only Post</TD> <TD>".$bvippost."</TD></TR>
<TR><TD CLASS='BoardColumn'>Private Board</TD> <TD>".$bprivate."</TD></TR>
</TABLE>
<P>
<SPAN CLASS='InputSection'>Board Name</SPAN><BR>
".inputText("boardname", $boardname, 30)."<BR>
<SPAN CLASS='InputSection'>Board Description</SPAN><BR>
".inputText("boarddescription", $boarddescription, 30)."<BR>
<SPAN CLASS='InputSection'>Board Rank</SPAN><BR>
".inputText("boardrank", $boardrank, 3, 1)."<BR>
<SPAN CLASS='InputSection'>Board Post Limits</SPAN><BR>
<SELECT NAME='boardvippost'>
<OPTION VALUE='0'>No Limits</OPTION>
<OPTION VALUE='1'>VIP+ Only</OPTION>
</SELECT><BR>
<SPAN CLASS='InputSection'>Board Private</SPAN><BR>
<SELECT NAME='boardprivate'>
<OPTION VALUE='0'>No</OPTION>
<OPTION VALUE='2'>Insider</OPTION>
<OPTION VALUE='1'>VIP+</OPTION>
</SELECT><BR>
<SPAN CLASS='InputSection'>Group</SPAN><BR>
".inputDBCycle("boardgroupid", $boardgroupid, TABLE_GROUPS, "ID", "groupname", "edit", "grouprank")."
<P>";
?>
