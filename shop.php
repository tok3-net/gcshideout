<?php
   $protectedpage = 1;
   include("common.php");
   $navigation[] = array("name" => "Shop",
                         "url"  => "shop.php");
global $userdata;

$output = "
	<TABLE BORDER=0 WIDTH=100% CLASS='BoardRowBody'>
	<TR><TD>
	<CENTER><H2>".$DiscoBoardName." Shop</H2></CENTER>
	</TD></TR>
	<TR><TD>
	Welcome To The Shop ".$userdata['displayname']." (<A HREF='shop-actions.php?item=view'>View Your Items</A>)<BR><BR>
	You Currently Have: ".number_format($userdata['dollars'])." Dollars
	<HR WIDTH=100%>
	</TD></TR>
	<TR><TD ALIGN=CENTER>
	<TABLE BORDER=1 CLASS='BoardRowBody'><TR><TD>
	<CENTER><B><U>Items</U></B></CENTER></TD><TD><CENTER><B><U>Price</U></B></CENTER></TD></TR>
	<TR><TD>
	<A HREF='shop-actions.php?item=donate'>Donate Money</A></TD><TD><B>(0 Dollars)</B></TD></TR>";
global $EditOwnTitle;
if (!$EditOwnTitle)
{
$output .= "<TR><TD>
	<A HREF='shop-actions.php?item=title'>Change Your Title</A></TD><TD><B>(500 Dollars)</B></TD></TR>";
}
global $UseOwnIcons;
if (!$UseOwnIcons)
{
$output .= "<TR><TD>
	<A HREF='shop-actions.php?item=ownicon'>Use Your Own Icon</A></TD><TD><B>(500 Dollars)</B></TD></TR>";
}
$output .= "<TR><TD>
	<A HREF='shop-actions.php?item=postcount'>Increase Postcount By 100</A></TD><TD><B>(750 Dollars)</B></TD></TR>";
if (!checkAccess("accessnameformat"))
{
$output .= "<TR><TD>
	<A HREF='shop-actions.php?item=color'>Username Colors</A></TD><TD><B>(1000 Dollars)</B></TD></TR>";
}
$output .= "</TABLE>
	</TD></TR>
	</TABLE>
	";

   $pagecontents = $output;
  include("layout.php");

?>