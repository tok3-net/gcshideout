<?php
   $starttime = microtime();
   $protectedpage = 1;
   $functionset   = "admin";
   include("common.php");
   
   $navigation[] = array("name" => "Edit Stylesheet",
                         "url"  => "admin-editstyle.php");
   if ($_REQUEST['action'] == "save" && !$_POST) { exit; }

if(checkAccess("accessadmin"))
{
  if(!$action)
{ 
$action = "edit"; 
}
  if($action == "edit")
{
    $stylefile = "stylesheets/default.php";
    $handle = fopen($stylefile, "rb");
    $stylesheet = fread($handle, filesize($stylefile));
    fclose($handle);
    
    if (false)
    {
      $stylesheet = stripSlashes($stylesheet);
    }
    include("elements/adminstyle.php");
    $output ="<table width=\"100%\" cellpadding=\"20\" cellspacing=\"1\" border=\"0\">\n"
            ."  <tr>\n"
            ."    <td class=\"BoardRowBody\">\n"
 	      ."    <form action=\"admin-editstyle.php\" method=\"post\">\n"
            .inputHidden("action", "save")
            .$editstyleform
            ."    </form>\n"
            ."    </td>\n"
            ."  </tr>\n"
            ."</table>\n";
    

  }
  if($action == "save")
{
    if (false)
    {
      $stylesheet = stripSlashes($stylesheet);
    }
    $out = fopen("stylesheets/default.php", "w");
    fwrite($out, $stylesheet);
    fclose($out);
    $action = "edit";
    $sysmsg = "custom";
    $sysmsgcustomcontent = "Stylesheet Updated";
    
    $action = "edit";
    $description = "Stylesheet Updated";
    
    $output ="<table width=\"100%\" cellpadding=\"20\" cellspacing=\"1\" border=\"0\">\n"
            ."  <tr>\n"
            ."    <td class=\"BoardRowBody\">\n"
            ."    <span class=\"InputSection\">Stylesheet Uploaded Successfully.</span><br><br>\n"
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