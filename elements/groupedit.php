<?php
$groupform = "
<SPAN CLASS='InputSection'>Current Settings</SPAN><BR>
Group ID: ".$groupid."<BR>
Group Name: ".$groupname."<BR>
Group Rank: ".$grouprank."<BR>
<P>
<SPAN CLASS='InputSection'>Group Name</SPAN><BR>
".inputText("groupname", $groupname, 30)."<BR>
<SPAN CLASS='InputSection'>Group Rank: </SPAN>".inputText("grouprank", $grouprank, 3, 1)."<BR>
";
?>
