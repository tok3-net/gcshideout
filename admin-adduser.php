<?php
   $starttime = microtime();
   $protectedpage = 1;
   include("common.php");
   $functionset = "admin";
   
   $navigation[] = array("name" => "Add User",
                         "url"  => "$PHP_SELF");
if (checkAccess("accessadmin"))
{
   if ($action == "create")
   {
     if (!$displayname)  { $err[] = "No display name was entered"; }
     else
     {
       // Check for invalid characters in the displayname
       $notallowedchars = "[^a-zA-Z0-9\.\_\+-]";
       if (preg_match('/'.$notallowedchars.'/', $displayname))
                         { $err[] = "Your display name contains invalid characters"; }
     }
     if (!$firstname)    { $err[] = "No first name was entered"; }
     if (!$lastname)     { $err[] = "No last name was entered"; }
     if (!$email)        { $err[] = "No email address was entered"; }
     else
     {
       $validemail = validEmail($email);
       if ($validemail == "invalid-form")
                         { $err[] = "Your email address is malformed"; }
       if ($validemail == "invalid-mx")
                         { $err[] = "Your email address cannot be reached"; }
       
       if (countUserEmails($email) > 0)
                         { $err[] = "Your email address has already been used"; }
     }
     if (!$birthdayyear) { $err[] = "No birthday year was entered"; }
     else
     {
       if (!checkdate($birthdaymonth, $birthdayday, $birthdayyear))
                         { $err[] = "The birthday entered was invalid"; }
     }
     
     if ($err)
     {
       // Errors were encountered, stop...
       $reason = implode("<BR>\n", $err);
       $action = "";
     }
     else
     {
       // No errors, go ahead
       $data['displayname'] = $displayname;
       $data['password']    = $pass;
	   $data['setclassid']  = $setclassid;       
       $data['firstname']   = $firstname;
       $data['lastname']    = $lastname;
       $data['email']       = $email;
       $data['country']     = $country;
       $data['birthday']    = mkTime(0, 0, 0, $birthdaymonth, $birthdayday, $birthdayyear);
       $data['gender']      = $gender;
       $data['ipaddress']   = $REMOTE_ADDR;
       
       //print_r($data);
       
       $user = admincreateUser($data);
       
       if ($user)
       {
         // User creation worked
         $output = "Thankyou, your account has been created.\n"
                  ."<P>\n"
                  ."Login information has been emailled to your supplied email \n"
                  ."address, $email.\n"
                  ."<P>\n"
                  .makeLink($returnurl, "Continue using $DiscoBoardName");
       }
     }
   }
   
   if (!$action)
   {
     if ($reason)
     {
       $reasonoutput = "<B CLASS='red'>".$reason."</B>\n"
                      ."<P>\n";
     }
     
     $mandatory = "<SPAN CLASS='red'>•</SPAN>";
     
     include("elements/adminadduser.php");
     $output = "Please complete the form below to register to use the system.<BR>\n"
              ."Required fields are marked: ".$mandatory."<BR>\n"
              ."<P>\n"
              .$reasonoutput
              ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
              .inputHidden("action", "create")
              .inputHidden("returnurl", $returnurl)
              .$userform
              ."<P>\n"
              .inputSubmit("Register")
              ."</FORM>\n";
   }

   $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
            ."<TR><TD CLASS='BoardRowBody'>\n"
            ."        ".str_replace("\n", "\n        ", $output)."</TD></TR>\n"
            ."</TABLE>\n";
}
else
{
header("Location: noaccess.php");
}
            
   $pagecontents = $output;
   include("layout.php");
?>