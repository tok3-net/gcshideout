<?php
$postform = "
<SPAN CLASS='InputSection'>Subject</SPAN><BR>
".inputText("subject", $subject, 40)."
<P>
<SPAN CLASS='InputSection'>Body</SPAN><BR>
".inputTextArea("body", $body, 45, 15, "", "", "", "linewrapfix")."<BR>
<SPAN STYLE='font-size: 8pt;'>Check the <A HREF='help.php' TARGET='_blank'>Help Area</A> for supported Markup Codes</SPAN>
<P>
";
?>