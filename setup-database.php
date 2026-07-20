<?php
   $pagetitle = "Setup";
   
   include("common.php");
   
   $ThemeName = "plain";
   $bodycolor = "#E9E9E9";
   $marginx = $marginy = 15;
   
   if (!$mysql)
   {
     echo "Please set up your MySQL connection in common.php";
     Exit();
   }
   else
   {
     if ($action == "runquery")
     {
       if (false)
       {
         $sql = stripSlashes($sql);
       }
       $exe = multiQuery($sql, "yes");
       if ($exe)
       {
         $status = "<B CLASS='green'>Your query was executed successfully</B>\n";
         $sql = "";
       }
       else
       {
         $status = "<B CLASS='red'>Your query was not executed:</B><BR>\n"
                  ."MySQL said: ".mysqli_error($mysql)."\n";
       }
       $status .= "<P>\n";
       $action = "";
     }
     
     if (!$action)
     {
       $output = "Enter the SQL query or queries to execute here:<BR>\n"
                ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                .inputHidden("action", "runquery")
                .inputTextArea("sql", $sql, 70, 15)."<BR>\n"
                .inputSubmit("Process Query")
                ."</FORM>\n";
     }
   }
   
   $output = "<B>DiscoBoard Database Setup</B>\n"
            ."<P>\n"
            .$status
            .$output;
            
   $pagecontents = $output;
   include("layout.php");
?>