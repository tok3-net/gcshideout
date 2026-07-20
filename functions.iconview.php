<?php
   define("ICONVIEWER_FUNCTIONS_AVAILABLE", 1);

   Function base64ToFile($filedata)
   {
     $dataarray = explode("\r\n", $filedata);
     $data = implode("", $dataarray);
     $data = base64_decode($data);
     
     return $data;
   }
?>