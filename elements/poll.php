<?php
$pollform = "
<SPAN CLASS='InputSection'>Question</SPAN><BR>
Enter your poll topic<BR>
".inputText("question", $question, 40)."
<P>
<SPAN CLASS='InputSection'>Answers</SPAN><BR>
Enter at least two answers<BR>
<TABLE>
<TR><TD>1</TD>
    <TD>".inputText("answer1", $answer1, 30)."</TD></TR>
<TR><TD>2</TD>
    <TD>".inputText("answer2", $answer2, 30)."</TD></TR>
<TR><TD>3</TD>
    <TD>".inputText("answer3", $answer3, 30)."</TD></TR>
<TR><TD>4</TD>
    <TD>".inputText("answer4", $answer4, 30)."</TD></TR>
<TR><TD>5</TD>
    <TD>".inputText("answer5", $answer5, 30)."</TD></TR>
<TR><TD>6</TD>
    <TD>".inputText("answer6", $answer6, 30)."</TD></TR>
<TR><TD>7</TD>
    <TD>".inputText("answer7", $answer7, 30)."</TD></TR>
<TR><TD>8</TD>
    <TD>".inputText("answer8", $answer8, 30)."</TD></TR>
<TR><TD>9</TD>
    <TD>".inputText("answer9", $answer9, 30)."</TD></TR>
<TR><TD>10</TD>
    <TD>".inputText("answer10", $answer10, 30)."</TD></TR>
</TABLE>
<P>
<SPAN CLASS='InputSection'>Expiry Date / Time</SPAN><BR>
".inputDMYtext("expiry", $expiryday, $expirymonth, $expiryyear)." / 
".inputHM("expiry", $expiryhour, $expiryminute)."
<P>
<SPAN CLASS='InputSection'>Body</SPAN><BR>
".inputTextArea("body", $body, 45, 5, "", "", "", "linewrapfix")."<BR>
<SPAN STYLE='font-size: 8pt;'>Check the <A HREF='help.php'>Help Area</A> for supported Markup Codes</SPAN>
";
?>
