<?php
   $starttime = microtime();
   $functionset = "iconview";
   
   include("common.php");
   
   $iconinfo = fetchRow($iconid, TABLE_ICONS);
   
   $iconmime = $iconinfo['mimetype'];

   $icondata = base64ToFile($iconinfo['data']);
   
   Header("Content-type: ".$iconmime);
   echo $icondata;
   exit();
?>