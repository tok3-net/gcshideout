<?php
$titleform = "
<TABLE>
<TR><TD ALIGN=RIGHT>Current Title</TD>
    <TD>".$currenttitle."</TD></TR>
<TR><TD ALIGN=RIGHT>New Title</TD>
    <TD>".inputText("newtitle", $sqltitle, 40, 550)."</TD></TR>
</TABLE>";

?>