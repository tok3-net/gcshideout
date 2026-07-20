<?php
   $starttime = microtime();
   $protectedpage = 1;
   $functionset   = "admin";
   include("common.php");
   
   $navigation[] = array("name" => "Edit TOS",
                         "url"  => "admin-edittos.php");
   if ($_REQUEST['action'] == "save" && !$_POST) { exit; }

if(checkAccess("accessadmin"))
{
  if(!$action)
{ 
$action = "edit"; 
}
  if($action == "edit")
{
    $tosfile = "elements/tos.txt";
    $handle = fopen($tosfile, "rb");
    $termsofservice = fread($handle, filesize($tosfile));
    fclose($handle);
    
    if (false)
    {
      $termsofservice = stripSlashes($termsofservice);
    }
    include("elements/admintos.php");
    $output ="<table width=\"100%\" cellpadding=\"20\" cellspacing=\"1\" border=\"0\">\n"
            ."  <tr>\n"
            ."    <td class=\"BoardRowBody\">\n"
 	      ."    <form action=\"admin-edittos.php\" method=\"post\">\n"
            .inputHidden("action", "save")
            .$edittosform
            ."    </form>\n"
            ."    </td>\n"
            ."  </tr>\n"
            ."</table>\n";
    

  }
  if($action == "save")
{
    if (false)
    {
      $termsofservice = stripSlashes($termsofservice);
    }
    $out = fopen("elements/tos.txt", "w");
    fwrite($out, $termsofservice);
    fclose($out);
    $action = "edit";
    $sysmsg = "custom";
    $sysmsgcustomcontent = "TOS Updated";
    
    $action = "edit";
    $description = "Terms of Service updated";
    
    $output ="<table width=\"100%\" cellpadding=\"20\" cellspacing=\"1\" border=\"0\">\n"
            ."  <tr>\n"
            ."    <td class=\"BoardRowBody\">\n"
            ."    <span class=\"InputSection\">Terms of Service:</span><br><br>\n"
            .$termsofservice
            ."    </td>\n"
            ."  </tr>\n"
            ."</table>\n";
  }
}
else
{
header("Location: noaccess.php");
}

$pagecontents = $output;
include("layout.php");
?>