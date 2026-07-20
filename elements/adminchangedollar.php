<?php
$changedollarform = "
<TABLE WIDTH=80%>
<TR><TD ALIGN=RIGHT>Username</TD>
    <TD>".$mandatory."</TD>
    <TD>".inputText("username", $username, 15, 30)."</TD></TR>
<TR><TD ALIGN=RIGHT>New Amount Of Money</TD>  
    <TD>".$mandatory."</TD>
    <TD>".inputText("dollars", $dollars, 15, 30)."</TD></TR>
</TABLE>
";
?>