<?php
   $starttime = microtime();
   $protectedpage = 1;
   $functionset   = "admin";
   include("common.php");
   
   $navigation[] = array("name" => "Edit / View A File",
                         "url"  => "admin-editfile.php");
global $AllowFileViewer;
if(checkAccess("accessadmin"))
{

$output = "<TABLE BORDER='0' WIDTH=100%><TR><TD CLASS='BoardRowBody' WIDTH=100%>" 
	."&nbsp;<h3>Editable Files:</h3>"
	."&nbsp;<A HREF='admin-editstyle.php' CLASS='SubjectLink'>Default Stylesheet</A> - Edits the default stylesheet the boards are viewed with.<BR>"
	."&nbsp;<A HREF='admin-edittos.php' CLASS='SubjectLink'>Terms Of Service</A> - Edits the boards terms of service.";
if ($AllowFileViewer)
{
$output .= "&nbsp;<h3>View Files:</h3>"
."&nbsp;<A HREF='admin-fileviewer.php' CLASS='SubjectLink'>View A File</A> - Lets you view any file in the DiscoBoard directory.";
}
	$output .= "</TD></TR></TABLE>";
}
else
{
header("Location: noaccess.php");
}

$pagecontents = $output;
include("layout.php");
?>