<?php
$highlight = "
Highlight<BR>
".inputChoice("colours", "hlcolour", $hlcolour)."
";

$textcolor = "
Text Colour<BR>
".inputChoice("colours", "txtcolour", $txtcolour)."
";

$textstyle = "
Text Style<BR>
".inputCheckbox("bold", 1, $bold)." Bold<BR>
".inputCheckbox("italic", 1, $italic)." Italic<BR>
".inputCheckbox("underline", 1, $underline)." Underline<BR>
".inputCheckbox("overline", 1, $overline)." Overline<BR>
".inputCheckbox("strikethrough", 1, $strikethrough)." Strikethrough<BR>
";

$borders = "
Borders<BR>
".inputCheckbox("topborder", 1, $topborder)." Top<BR>
".inputCheckbox("bottomborder", 1, $bottomborder)." Bottom<BR>
".inputCheckbox("leftborder", 1, $leftborder)." Left<BR>
".inputCheckbox("rightborder", 1, $rightborder)." Right<BR>
".inputChoice("colours", "bordercolour", $bordercolour)."
";

$nameformatform = "
$highlight
<P>
$textcolor
<P>
$textstyle
<P>
$borders
";

$nameformattable = "
<TABLE WIDTH=100%>
<TR VALIGN=TOP>
    <TD WIDTH=33%>
        $highlight
        <P>
        $textcolor</TD>
    <TD WIDTH=34%>
        $textstyle</TD>
    <TD WIDTH=33%>
        $borders</TD></TR>
</TABLE>";
?>

