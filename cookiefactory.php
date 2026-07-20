<?php
   $starttime = microtime();
 $protectedpage = 1;
$functionset = "admin";
include("common.php");

   $navigation[] = array("name" => "Cookie Factory",
                         "url"  => $PHP_SELF);

if (checkAccess("accessadmin"))
{
   if ($action == "set")
   {
     $expiretime = 0;
     if (($expday) && ($expmonth) && ($expyear) && ($exphour) && ($expmin) && ($expsec))
     { $expiretime = mktime($exphour, $expmin, $expsec, $expmonth, $expday, $expyear); }
     
     if (false)
     {
       $cookiename  = stripSlashes($cookiename);
       $cookievalue = stripSlashes($cookievalue);
     }
     
     setcookie($cookiename, $cookievalue, intval($expiretime), "/");

     if ($cookievalue)
     {
       $status = "<B CLASS='green'>Set <I>$cookiename</I> as <I>$cookievalue</I></B><BR>\n"
                ."<P>\n";
       $newcookie['name']  = $cookiename;
       $newcookie['value'] = $cookievalue;
     }
     else
     {
       $status = "<B CLASS='red'>Deleted <I>$cookiename</I></B>\n"
                ."<P>\n";
       $hidecookie = $cookiename;
     }
     $action = "";
   }
   
   if (!$action)
   {
     foreach ($_COOKIE as $key => $value)
     {
       if ($key != $hidecookie)
       {
         $cookierow .= "<TR><TD CLASS='smallclear'>".$key."</TD>\n"
                      ."    <TD CLASS='smallclear'>".wordWrap(htmlentities($value), 80, "<BR>\n", 1)."</TD>\n"
                      ."    <TD CLASS='smallclear'><A HREF='".$PHP_SELF."?action=set&cookiename=".urlencode($key)."&cookievalue='>Delete</A></TD></TR>\n";
       }
     }
     if ($newcookie)
     {
       //if ($newcookie['name'] != $cookiename)
       //{
         $cookierow .= "<TR><TD CLASS='smallclear'>".$newcookie['name']."</TD>\n"
                      ."    <TD CLASS='smallclear'>".wordWrap($newcookie['value'], 80, "<BR>\n", 1)."</TD>\n"
                      ."    <TD CLASS='smallclear'><A HREF='".$PHP_SELF."?action=set&cookiename=".urlencode($newcookie['name'])."&cookievalue='>Delete</A></TD></TR>\n";
       //}
     }
     
     $output = $status
              ."<B>Your browser is currently sending me these cookies:</B>\n"
              ."<P>\n"
              ."<TABLE WIDTH=100% CELLSPACING=0 BORDER=1>\n"
              .$cookierow
              ."</TABLE>\n"
              ."<P>\n"
              ."<B>Set a new cookie:</B>\n"
              ."<P><SPAN CLASS='body'>\n"
              ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
              ."<INPUT TYPE=HIDDEN NAME='action' VALUE='set'>\n"
              ."Name: <INPUT TYPE=TEXT NAME='cookiename' VALUE=''><BR>\n"
              ."Value: <INPUT TYPE=TEXT NAME='cookievalue' VALUE=''><BR>\n"
              ."Expiry Date (leave blank for a session cookie):<BR>\n"
              ." &nbsp; DD/MM/YYYY: <INPUT TYPE=TEXT NAME='expday' VALUE='' SIZE=3>\n"
              ." / <INPUT TYPE=TEXT NAME='expmonth' VALUE='' SIZE=3>\n"
              ." / <INPUT TYPE=TEXT NAME='expyear' VALUE='' SIZE=5><BR>\n"
              ." &nbsp; H:M:S: <INPUT TYPE=TEXT NAME='exphour' VALUE='' SIZE=3>\n"
              ." : <INPUT TYPE=TEXT NAME='expmin' VALUE='' SIZE=3>\n"
              ." : <INPUT TYPE=TEXT NAME='expsec' VALUE='' SIZE=3>\n"
              ."<P>\n"
              ."<INPUT TYPE=SUBMIT VALUE='Set Cookie'>\n"
              ."</FORM>\n";
   }
}
else
{
header("Location: noaccess.php");
}
$pagecontents = $output;
include("layout.php");
?>