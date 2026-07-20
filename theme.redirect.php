<?php
   // ASSEMBLE REDIRECTION STRING
   // ==========================================================================   
   // ==========================================================================   

   // Get POST'd vars
   foreach($_POST as $key => $value)
   { 
     // <// Homesite code highlight marker
     if ($querystring)
     {
       $querystring .= "&";
     }
     $querystring .= urlencode($key)."=".urlencode($value);
   }
   
   // Get GET'd vars
   foreach($_GET as $key => $value)
   { 
     // <// Homesite code highlight marker
     if ($querystring)
     {
       $querystring .= "&";
     }
     $querystring .= urlencode($key)."=".urlencode($value);
   }
   
   if ($querystring)
   {
     $querystring = "?".$querystring;
   }
   
   $scriptname = str_replace("old-directory-name", "new-directory-name", $PHP_SELF);
   Header("Location: http://new.domainname.com".$scriptname.$querystring);
   Exit();
?>
