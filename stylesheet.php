<?php
if((!$userdata['stylesheet']) || ($userdata['stylesheet'] == "NULL"))
{
   $css = "default";
}
else
{
   $css = $userdata['stylesheet'];
}
include("stylesheets/".$css.".php");
?>