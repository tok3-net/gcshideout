<?php

$birthdayday   = date("d", $userinfo['dateofbirth']);
$birthdaymonth = date("m", $userinfo['dateofbirth']);
$birthdayyear  = date("Y", $userinfo['dateofbirth']);

$profileeditform = "
<TABLE WIDTH=80%>
<TR><TD COLSPAN=3>
        <SPAN CLASS='InputSection'>Required Information</SPAN><BR>
        <SPAN CLASS='PlainText'>Enter updated information where neccessary.</SPAN><BR>
        </TD></TR>
<TR><TD WIDTH=25% ALIGN=RIGHT>First Name</TD>
    <TD>".$mandatory."</TD>
    <TD WIDTH=75%>".inputText("fname", $userinfo['fname'], 15, 30)."</TD></TR>
<TR><TD ALIGN=RIGHT>Last Name</TD>
    <TD>".$mandatory."</TD>
    <TD>".inputText("sname", $userinfo['sname'], 15, 30)."</TD></TR>
<TR><TD ALIGN=RIGHT>Birthday</TD>
    <TD>".$mandatory."</TD>
    <TD>".inputDMYtext("birthday", $birthdayday, $birthdaymonth, $birthdayyear)."</TD></TR>
<TR><TD ALIGN=RIGHT>Gender</TD>
    <TD>".$mandatory."</TD>
    <TD>".inputChoice("gender", "gender", $userinfo['gender'])."</TD></TR>
<TR><TD ALIGN=RIGHT>Country</TD>
    <TD>".$mandatory."</TD>
    <TD>".inputCountry("country", $userinfo['country'])."</TD></TR>
<TR><TD COLSPAN=3>
        <SPAN CLASS='InputSection'>Contact Information</SPAN><BR>
        <SPAN CLASS='PlainText'>This information allows you to specify different
        ways you can be contacted by other users.</SPAN><BR>
        </TD></TR>
<TR><TD ALIGN=RIGHT>Email Address</TD>
    <TD>".$mandatory."</TD>
    <TD>".inputText("contactemail", $userinfo['contactemail'], 30, 50)."</TD></TR>
<TR><TD ALIGN=RIGHT>ICQ UIN</TD>
    <TD></TD>
    <TD>".inputText("contacticq", $userinfo['contacticq'], 10, 50)."</TD></TR>
<TR><TD ALIGN=RIGHT>MSN ID</TD>
    <TD></TD>
    <TD>".inputText("contactmsn", $userinfo['contactmsn'], 20, 50)."</TD></TR>
<TR><TD ALIGN=RIGHT>Yahoo ID</TD>
    <TD></TD>
    <TD>".inputText("contactyahoo", $userinfo['contactyahoo'], 20, 50)."</TD></TR>
<TR><TD ALIGN=RIGHT>AIM ID</TD>
    <TD></TD>
    <TD>".inputText("contactaim", $userinfo['contactaim'], 15, 50)."</TD></TR>
<TR><TD COLSPAN=3>
        <SPAN CLASS='InputSection'>User Details</SPAN><BR>
        <SPAN CLASS='PlainText'>This information will provide a little bit more
        information about yourself to other board members.</SPAN><BR>
        </TD></TR>
<TR><TD WIDTH=25% ALIGN=RIGHT>Profile Title</TD>
	<TD></TD>
	<TD WIDTH=75%>".inputText("profiletitle", $userinfo['profiletitle'], 60, 200)."</TD></TR>
<TR><TD ALIGN=RIGHT>Company</TD>
    <TD></TD>
    <TD>".inputText("company", $userinfo['company'], 25, 30)."</TD></TR>
<TR><TD ALIGN=RIGHT>Job Title</TD>
    <TD></TD>
    <TD>".inputText("jobtitle", $userinfo['jobtitle'], 25, 30)."</TD></TR>
<TR><TD ALIGN=RIGHT>Pic URL</TD>
    <TD></TD>
    <TD>".inputText("picurl", $userinfo['picurl'], 60, 150)."</TD></TR>
<TR><TD ALIGN=RIGHT>Website URL</TD>
    <TD></TD>
    <TD>".inputText("wwwurl", $userinfo['wwwurl'], 60, 150)."</TD></TR>
<TR VALIGN=TOP>
    <TD ALIGN=RIGHT>Bio</TD>
    <TD></TD>
    <TD>".inputTextArea("bio", $userinfo['bio'], 50, 7, "", "", "", "linewrapfix")."<BR>
        <SPAN STYLE='font-size: 8pt;'>Check the <A HREF='help.php'>Help Area</A> for supported Markup Codes</SPAN></TD></TR>
<TR VALIGN=TOP>
    <TD ALIGN=RIGHT>Signature</TD>
    <TD></TD>
    <TD>".inputText("sig1", $userinfo['sig1'], 60, 120)."<BR>
        ".inputText("sig2", $userinfo['sig2'], 60, 120)."<BR>
        ".inputText("sig3", $userinfo['sig3'], 60, 120)."<BR>
        ".inputText("sig4", $userinfo['sig4'], 60, 120)."<BR>
        ".inputText("sig5", $userinfo['sig5'], 60, 120)."<BR>
        </TD></TR>
</TABLE>
";
?>