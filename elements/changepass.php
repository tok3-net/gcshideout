<?php
$changepassform = "
<TABLE WIDTH=80%>
<TR><TD ALIGN=RIGHT>Current Password</TD>
    <TD>".$mandatory."</TD>
    <TD>".inputPassword("currpass", $currpass, 15, 30)."</TD></TR>
<TR><TD COLSPAN=3><BR></TD></TR>
<TR><TD ALIGN=RIGHT>Password</TD>
    <TD>".$mandatory."</TD>
    <TD>".inputPassword("password1", $password1, 15, 30)."</TD></TR>
<TR><TD ALIGN=RIGHT>Retype Password</TD>  
    <TD>".$mandatory."</TD>
    <TD>".inputPassword("password2", $password2, 15, 30)."</TD></TR>
</TABLE>
";
?>