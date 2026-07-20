<?php
$ipbanform = "
<TABLE>
<TR><TD ALIGN=RIGHT>IP Address</TD>
    <TD>".$mandatory."</TD>
    <TD>".inputText("ipaddress1", $ipaddress1, 15, 15)."
        to (optional: 
        ".inputText("ipaddress2", $ipaddress2, 15, 15).")</TD></TR>
<TR><TD ALIGN=RIGHT>Username</TD>
    <TD></TD>
    <TD>".inputText("username", $username, 15, 30)." (Leave blank to block all access)</TD></TR>
<TR><TD ALIGN=RIGHT>Active?</TD>
    <TD></TD>
    <TD>".inputCheckBox("active", 1, $active)."</TD></TR>
</TABLE>
";
?>