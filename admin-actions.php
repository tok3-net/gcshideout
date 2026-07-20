<?php
   $starttime = microtime();
   $protectedpage = 1;
   $functionset   = "admin";
   include("common.php");

   $refinfo = parse_url($_SERVER['HTTP_REFERER']);
   if ($refinfo['host'] != $_SERVER['HTTP_HOST'] && $refinfo['host']) { exit; }

   if (checkAccess("accessmoderator"))
   {
   $navigation[] = array("name" => "Administration",
                         "url"  => "admin/main.php");
   $navigationhead = "Admin Action Log";

if($list == "all"){
  $limit = "";
  $areaoptions[] = array("name" => "List Last 50 Actions",
                         "url"  => "admin/aal.php");
}else{
  $limit = "LIMIT 0,50";
  $areaoptions[] = array("name" => "List All Actions",
                         "url"  => "admin/aal.php?list=all");
}
if($search){
  $extra = "WHERE ";
  if($admin){
    $extra .= "adminusername LIKE $admin ";
  }
  if($action){
    $extra .= "action LIKE $action ";
  }
  if($ipaddress){
    $extra .= "ip_address = '$ip' ";
  }
}else{
  $extra = "";
}
   
$output = "<table width=\"100%\" cellspacing=\"2\" cellpadding=\"1\">\n"
         ."  <tr>\n"
         //."    <td class=\"BoardRowA\" style=\"font-weight: bold\">ID</td>\n"
         ."    <td class=\"BoardRowA\" style=\"font-weight: bold\">Admin</td>\n"
         ."    <td class=\"BoardRowA\" style=\"font-weight: bold\">IP Address</td>\n"
         ."    <td class=\"BoardRowA\" style=\"font-weight: bold\">Action</td>\n"
         ."    <td class=\"BoardRowA\" style=\"font-weight: bold\">Description</td>\n"
         ."    <td class=\"BoardRowA\" style=\"font-weight: bold\">Date</td>\n"
         ."  </tr>";
$StartID = 0;
$sql = "SELECT id, adminid, adminusername, ip_address, action, description, date FROM ".TABLE_ADMIN_ACTIONS." "
      .$extra."ORDER BY date DESC ".$limit;
if ($exe = runQuery($sql))
{
  while ($row = fetchResultArray($exe))
    {
      $adminlink = usernameDisplay($row['adminid'], "", "", "");
      //$adminlink = "<a href=\"user.php?userid=".$row['id']."\" class=\"AuthorLink\">"
      //            .$row['adminusername']."</a>";
      $output .= "  <tr>\n"
              //."    <td class=\"BoardRowB\" align=\"center\">".$row['id']."</td>\n"
              ."    <td class=\"BoardRowB\">".$adminlink."</td>\n"
              ."    <td class=\"BoardRowB\">".$row['ip_address']."</td>\n"
              ."    <td class=\"BoardRowB\">".$row['action']."</td>\n"
              ."    <td class=\"BoardRowB\">".$row['description']."</td>\n"
              ."    <td class=\"BoardRowB\">".dateNeat($row['date'], "admintime")."</td>\n"
              ."  </tr>\n";
    }
}

$output .= "</table>";
   }
else
	{
header("Location: noaccess.php");
   }

   $pagecontents = $output;
   include("layout.php");
?>