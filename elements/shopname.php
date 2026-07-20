<?php
$changenamesform = "
<TABLE WIDTH=80%>
<TR><TD ALIGN=RIGHT>Current Username</TD>
    <TD>".$mandatory."</TD>
    <TD>".usernameDisplay($userdata['ID'])."</TD></TR>
<TR><TD ALIGN=RIGHT>New Username</TD>  
    <TD>".$mandatory."</TD>
    <TD>".inputText("displayname", $displayname, 15, 30)."</TD></TR>
</TABLE>
";
?>