<?php
$icongroupform = "
<SPAN CLASS='InputSection'>Current Settings</SPAN><BR>
Icon Group ID: ".$currentgroupid."<BR>
Icon Group Name: ".$currentgroupname."<BR>
Icon Group Class: ".inputUserClass("classid", $currentclassid, "show", "No restriction", "", "Only for ")."
<P>
<SPAN CLASS='InputSection'>Group Name</SPAN><BR>
".inputText("groupname", $groupname, 30)."<BR>
<SPAN CLASS='InputSection'>Class restriction</SPAN><BR>
".inputUserClass("classid", $classid, "edit", "No restriction", "", "Only for ")."
";
?>
