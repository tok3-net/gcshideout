<?php
$changepassform = "
<TABLE WIDTH=80%>
<TR><TD ALIGN=RIGHT>Username</TD>
    <TD>".$mandatory."</TD>
    <TD>".inputText("username", $username, 15, 30)."</TD></TR>
<TR><TD ALIGN=RIGHT>New Password</TD>  
    <TD>".$mandatory."</TD>
    <TD>".inputText("password", $password, 15, 30)."</TD></TR>
</TABLE>
";
?>