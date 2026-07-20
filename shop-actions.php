<?php
   $protectedpage = 1;
   include("common.php");
   $navigation[] = array("name" => "Shop",
                         "url"  => "shop.php");
global $userdata;

if ($item == "namechange")
{
if (checkAccess("accessmoderator"))
{     
     if ($action == "savename")
     {
       $navigationhead[] = array("name" => "Change Username",
								 "url"  => $PHP_SELF);

       if (!$username)     { $err[] = "No username was entered"; }
       {
         $userinfo = fetchRow($userdata['displayname'], TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
         if (!$userinfo['ID'])
                           { $err[] = "User '".$username."' doesn't exist"; }
       }
       
       if ($err)
       {
         $reason = implode("<BR>\n", $err);
	$item = "namechange";
         $action = "changename";
       }
       else
       {
         $data['displayname'] = $displayname;
         
        $sql = "UPDATE ".TABLE_USERS." SET displayname='$displayname' WHERE ID='$userinfo[ID]'";
        $exe = mysqli_query($mysql, $sql); 
         if ($exe)
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "<I>".$username."</I>'s username has been changed to <I>".$displayname."</I>.\n";
	$item = "namechange";
           $action = "changename";
         }
         else
         {
           $sysmsg = "custom";
           $sysmsgcustomcontent = "The username for <I>".$username."</I> was not changed.\n";
	$item = "namechange";
           $action = "display";
         }
       }
     }
     
     if (!$action || $action == "changename")
     {
       $navigationhead = "Change Username";
       $mandatory = "<SPAN CLASS='red'>•</SPAN>";
       
$changenameform = "
<TABLE WIDTH=80%>
<TR><TD ALIGN=RIGHT>Current Username</TD>
    <TD>".$mandatory."</TD>
    <TD>".usernameDisplay($userdata['ID'])."</TD></TR>
<TR><TD ALIGN=RIGHT>New Username</TD>  
    <TD>".$mandatory."</TD>
    <TD>".inputText("displayname", $displayname, 15, 30)."</TD></TR>
</TABLE>
";
       $output = "<TABLE BODER=0 WIDTH=100%><TR><TD CLASS='BoardRowBody' WIDTH=100%>"
				."<SPAN CLASS='InputSection'>Change Username</SPAN><BR>\n"
                ."Enter the username and new username below.<BR>\n"
                ."<FORM ACTION='shop-actions.php' METHOD=POST>\n"
	  .inputHidden("item", "namechange")
                .inputHidden("action", "savename")
                .$changenameform
                ."<P>\n"
                .inputSubmit("Change Username")
                ."</FORM>\n"
				."</TD></TR></TABLE>";
     }
}
else
{
header("Location: noaccess.php");
}
}

if ($item == "color")
{
$navigationhead = "Buy Username Colors";
if ($userdata['dollars'] >= "1000")
{
$vars['dollars'] = ($userdata['dollars'] - "1000");
$vars['color'] = ($userdata['color'] + "1");
$update = updateUser($userdata['ID'], $vars);
$output = "
	<B>You Successfully Bought This Item:<BR><BR></B>
	<B>Click <A HREF='options.php?action=nameformat'>Here</A> To Change Your Username Colors.</B>";
}
else
{
$output = "<TABLE BORDER=0 WIDTH=100% CLASS='BoardRowBody'>
	<TR><TD>
	<B>Sorry You Do Not Have Enough Money To Buy This.</B>
	</TD></TR></TABLE>";
}
}

if ($item == "title")
{
$navigationhead = "Buy A Title";
if ($userdata['dollars'] >= "500")
{
$vars['dollars'] = ($userdata['dollars'] - "500");
$vars['shoptitle'] = ($userdata['shoptitle'] + "1");
$update = updateUser($userdata['ID'], $vars);
$output = "<B>You Successfully Bought This Item<BR><BR></B>
	<B>Click <A HREF='own-title.php'>Here</A> To Change Your Title.</B>";
}
else
{
$output = "<TABLE BORDER=0 WIDTH=100% CLASS='BoardRowBody'>
	<TR><TD>
	<B>Sorry You Do Not Have Enough Money To Buy This.</B>
	</TD></TR></TABLE>";
}
}

if ($item == "view")
{
$navigationhead = "View Your Items";
$output = "<TABLE BORDER=0 WIDTH=100% CLASS='BoardRowBody'><TR><TD>";
  if ($userdata['color'])
  {
  $output .= "<A HREF='options.php?action=nameformat'>Change Username Colors</A> <B>(".$userdata['color'].")</B><BR>";
  }
  if ($userdata['shoptitle'])
  {
  $output .= "<A HREF='own-title.php'>Change Your Title</A> <B>(".$userdata['shoptitle'].")</B><BR>";
  }
  if ($userdata['chooseicon'])
  {
  $output .= "<A HREF='icon.php?action=setownicon'>Use Your Own Icon</A> <B>(".$userdata['chooseicon'].")</B><BR>";
  }
  if (!$userdata['color'] && !$userdata['shoptitle'] && !$userdata['chooseicon'])
  {
  $output .= "<B>You Currently Have No Items.</B>";
  }
$output .= "</TD></TR></TABLE>";
}

if ($item == "donate")
{
$navigationhead = "Donate Money";
  if ($step == "2")
  {
    $username = $_POST['username'];
    $amount = $_POST['amount'];
    if ($userdata['dollars'] >= $amount)
    {
    $userinfo = fetchRow($username, TABLE_USERS, "displayname", "idfieldistext", "dontcareifblank");
    $vars['dollars'] = ($userinfo['dollars'] + $amount);
    $varss['dollars'] = ($userdata['dollars'] - $amount);
    $update = updateUser($userinfo['ID'], $vars);
    $updates = updateUser($userdata['ID'], $varss);
    $output = "<B>You Successfully Donated ".$amount." Dollars To ".$username."</B>";
    }
    else
    {
    $output = "<B>You Dont Have That Much Money.</B>";
    }
  }
  if (!$step || $step == "1")
  {
  $output = "
  <FORM ACTION='shop-actions.php' METHOD=POST>
<B>Username:</B><BR>
  <INPUT TYPE=TEXT NAME='username'><BR>
<B>Amount Of Money To Donate:</B><BR>
  <INPUT TYPE=TEXT NAME='amount'><BR>
  <INPUT TYPE=HIDDEN NAME='item' VALUE='donate'>
  <INPUT TYPE=HIDDEN NAME='step' VALUE='2'>
  <INPUT TYPE=SUBMIT VALUE='Donate'>
  </FORM>
  ";
  }
}

if ($item == "postcount")
{
$navigationhead = "Increase Your Postcount";
if ($userdata['dollars'] >= "750")
{
$vars['dollars'] = ($userdata['dollars'] - "750");
$vars['postcount'] = ($userdata['postcount'] + "100");
$update = updateUser($userdata['ID'], $vars);
$output = "Your Postcount Has Been Changed.<BR><BR>Your New Postcount Is ".$userdata['postcount']."</B>";
}
else
{
$output = "<TABLE BORDER=0 WIDTH=100% CLASS='BoardRowBody'>
	<TR><TD>
	<B>Sorry You Do Not Have Enough Money To Buy This.</B>
	</TD></TR></TABLE>";
}
}

if ($item == "ownicon")
{
$navigationhead = "Use Your Own Icon";
if ($userdata['dollars'] >= "500")
{
$vars['dollars'] = ($userdata['dollars'] - "500");
$vars['chooseicon'] = ($userdata['chooseicon'] + "1");
$update = updateUser($userdata['ID'], $vars);
$output = "
	<B>You Successfully Bought This Item:<BR><BR></B>
	<B>Click <A HREF='icon.php?action=setownicon'>Here</A> To Use Your Own Icon.</B>";
}
else
{
$output = "<TABLE BORDER=0 WIDTH=100% CLASS='BoardRowBody'>
	<TR><TD>
	<B>Sorry You Do Not Have Enough Money To Buy This.</B>
	</TD></TR></TABLE>";
}
}

$output = "<TABLE WIDTH=100% BORDER=0 CLASS='BoardRowBody'><TR><TD WIDTH=100% CLASS='BoardRowBody'>"
	.$output
	."</TD></TR></TABLE>";

   $pagecontents = $output;
   include("layout.php");
?>