<?php
$changepostform = "
<TABLE WIDTH=80%>
<TR><TD ALIGN=RIGHT>Username</TD>
    <TD>".$mandatory."</TD>
    <TD>".inputText("username", $username, 15, 30)."</TD></TR>
<TR><TD ALIGN=RIGHT>New Postcount</TD>  
    <TD>".$mandatory."</TD>
    <TD>".inputText("postcount", $postcount, 15, 30)."</TD></TR>
</TABLE>
";
?>