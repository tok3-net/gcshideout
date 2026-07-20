<?php
$loginform = "
<TABLE>
<TR VALIGN=BASELINE>
    <TD WIDTH=25% ALIGN=RIGHT>Username</TD>
    <TD>".$mandatory."</TD>
    <TD WIDTH=75%>
        ".inputText("username", $username)."</TD></TR>
<TR VALIGN=BASELINE>
    <TD ALIGN=RIGHT>Password</TD>
    <TD>".$mandatory."</TD>
    <TD>".inputPassword("password", $password)."</TD></TR>
<TR><TD COLSPAN=2></TD>
    <TD><INPUT TYPE=SUBMIT VALUE='Login' NAME='loggingin' onClick=\"document.loginnow.loggingin.value='Processing...Please Wait...'\">
    <BR><BR></TD></TR>
<TR VALIGN=BASELINE>
    <TD ALIGN=RIGHT>Login Options</TD>
    <TD></TD>
    <TD><INPUT TYPE=CHECKBOX NAME='rememberusername' CHECKED VALUE='1'> Remember my username<BR>
        ".inputChoice("sessiontime", "sessiontime", $sessiontime)."
        </TD></TR>
</TABLE>
";
?>