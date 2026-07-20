<?php 
   define("GENERIC_FUNCTIONS_AVAILABLE", 1);

   // --------------------------------------------------------------------------
   // DATABASE FUNCTIONS
   // --------------------------------------------------------------------------
   
   // runQuery provides some level of database-abstraction by taking an 
   // SQL query, running it and returning the result set.
   Function runQuery($sql, $debugmode = "")
   {
     global $mysql;
     global $globaldebugmode;
     
     $debug .= "<B>Running</B><BR>\n"
              .$sql."<BR>\n";
     $exe = mysqli_query($mysql, $sql);
     
     if (!$exe)
     {
       //$debugmode = "yes";
       $debug .= "<B>ERROR</B><BR>\n"
                .mysqli_error($mysql)."<BR>\n";
     }
     
     if (($debugmode == "yes") || ($globaldebugmode))
     {
       echo $debug;
     }
     
     return $exe;
   }
   
   // Multi-query breaks up multiple inbound queries based on an assumed 
   // ;-space pattern at the end of SQL statements and passes them to 
   // runQuery()
   Function multiQuery($sql, $debugmode = "")
   {
     $sqllines = explode("\n", $sql);
     foreach ($sqllines as $key => $value)
     {
       $value = trim($value);
       if (!preg_match('/^\#/', $value))
       {
         $cleansql .= $value." ";
       }
     }
     $sqlarray = explode("; ", $cleansql);
     foreach( $sqlarray as $value )
     {
       if (trim($value))
       {
         $exe = runQuery($value, $debugmode);
         if (!$exe)
         {
           return 0;
         }
       }
     }
     return 1;
   }
   
   // Returns the number of rows in the result set
   Function resultCount($exe)
   {
     return mysqli_num_rows($exe);
   }
   
   // Returns the next row of the result set as a named array
   Function fetchResultArray($exe)
   {
     return mysqli_fetch_array($exe);
   }
   
   // Returns the value of the last updated/changed auto-increment field 
   // from the last operation
   Function fetchLastInsert()
   {
     global $mysql;
     return mysqli_insert_id($mysql);
   }

   // Fetches a row from the database and returns it as an array.
   Function fetchRow ($id, $table, $idfield = "ID", $idfieldistext = "", $dontcareifnoresult = "", $whereclause = "")
   {
     if (!$idfield) { $idfield = "ID"; }
     if ($whereclause) 
     { $whereclause = " AND ".$whereclause; }
     
     if (!$idfieldistext)
     {
       $fetchsql = "SELECT * FROM $table WHERE $idfield = $id ".$whereclause;
     }
     else
     {
       $fetchsql = "SELECT * FROM $table WHERE $idfield = '$id' ".$whereclause;
     }
     //echo "$fetchsql<BR>\n";
    
     $fetchexe = runQuery($fetchsql);
     if ($fetchexe)
     {
       $fetchrow = mysqli_num_rows($fetchexe);
       if ((!$idfieldistext) && (intval($id) == 0))
       {
         return "";
         exit;
       }
       if (!$fetchexe)
       {
         //echo "<B>fetchRow</B>: error in query ($fetchsql), table=$table id=$id\n";
         exit;
       }
  
       if (($fetchrow != 1) && (!$dontcareifnoresult))
       {
         //echo "<B>fetchRow</B>: invalid result set, $fetchrow rows ($fetchsql)\n";
         exit;
       }
  
       $data = array("id" => $id );
       $i=0;
       for ($i = 0; $i < mysqli_num_fields($fetchexe); $i++ )
       {
         //echo "created ".mysqli_field_name($fetchexe, $i)." with ".mysqli_result($fetchexe, 0, $i)." in it<BR>\n";
         if ($fetchrow)
         {
           // We definitely have data...
           $data[mysqli_field_name($fetchexe, $i)] = mysqli_result($fetchexe, 0, $i);
         }
       }
       mysqli_free_result($fetchexe);
     }
     return $data;
   }

   // Returns the number of rows found in a database table under certain
   // conditions.
   Function countRows($tablename, $countfield, $conditionfield = "", $conditionvalue = "")
   {
     global $mysql;
     if (($conditionfield) && ($conditionvalue))
     {
       $where = "WHERE $conditionfield = $conditionvalue";
     }
     $sql = "SELECT COUNT(ID) AS result FROM $tablename $where";
     if ($exe = mysqli_query($mysql, $sql))
     {
       $res = mysqli_result($exe, 0, "result");
     }
     return $res;
   }

   
   // --------------------------------------------------------------------------
   // COOKIE SETTING FUNCTIONS
   // --------------------------------------------------------------------------

   // Sends the user a cookie. Use this function to send cookies
   // so we can make sure they're all sent with the same parameters.
   Function sendCookie($cookiename, $cookievalue, $expiretime = "")
   {
     setcookie($cookiename, $cookievalue, 0, "/");
   }

   // Sends the user a cookie. Use this function to send cookies
   // so we can make sure they're all sent with the same parameters.
   Function sendExpiringCookie($cookiename, $cookievalue)
   {
     // Cookie expiry
     // Make the cookies last for a year
     $year = (3600 * 24 * 366);
     $cookieexpirytime = Date("U") + $year;
     
     setcookie($cookiename, $cookievalue, $cookieexpirytime, "/");
   }
   
   // --------------------------------------------------------------------------
   // BACKEND FUNCTIONS
   // --------------------------------------------------------------------------

   // Decides if the email address is valid. Checks syntax and MX records,
   // for total smartass value. Returns "valid", "invalid-mx" or 
   // "invalid-form".
   Function validEmail($emailaddress)
   { 
     // Validates the email address. I guess it works. *shrug*
     if (preg_match('/^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\.[a-z]{2,4}$/i', $emailaddress, $check))
     {
       //if ( getmxrr(substr(strstr($check[0], '@'), 1), $validate_email_temp) ) 
       if ( checkdnsrr(substr(strstr($check[0], '@'), 1), "ANY") ) 
       { return "valid"; }
       else
       { return "invalid-mx"; } 
     }
     else
     {
       return "invalid-form"; 
     }
   }

   function dateMysqlToUnix($mysqltime)
   {
     $stamp = preg_replace('/[^0-9]/', "", $mysqltime);
     $year  = substr($stamp, 0, 4);
     $month = substr($stamp, 4, 2);
     $day   = substr($stamp, 6, 2);
     $hour  = substr($stamp, 8, 2);
     $min   = substr($stamp, 10, 2);
     $sec   = substr($stamp, 12, 2);
     $unix = mktime($hour, $min, $sec, $month, $day, $year);
     //echo "Unix timestamp: $unix<BR>\n";
     return $unix;
   }

   // Returns a unique timestamp with a suffix string
   Function timeStampValue($value)
   {
     $microtime = microtime();
     $mtbits = explode(" ", $microtime);
     $milliseconds = preg_replace('/[^0-9]/', "", strstr($mtbits[0], "."));
     $uniqueid = (Date("YmdHis").$milliseconds)."$value";
     return $uniqueid;
   }
   
   // Short way to access the access flags stored in $userdata
   Function checkAccess($resource)
   {
     global $userdata;
     
     return $userdata[$resource];
   }

   // --------------------------------------------------------------------------
   // COSMETIC FUNCTIONS
   // --------------------------------------------------------------------------
   
   // Wraps a plaintext buffer at character X, with configurable break string 
   // and padding
   Function word_wrap ($String, $breaksAt = 78, $breakStr = "\n", $padStr="")
   {
     $newString="";
     $lines = explode($breakStr, $String);
     $cnt = count($lines);
     for($x=0;$x<$cnt;$x++)
     {
       if(strlen($lines[$x])>$breaksAt)
       {
         $str = $lines[$x];
         while(strlen($str)>$breaksAt)
         {
           $pos = strrpos(chop(substr($str, 0, $breaksAt)), " ");
           if ($pos == false)
           {
             break;
           }
           $newString .= $padStr.substr($str, 0, $pos).$breakStr;
           $str = trim(substr($str, $pos));
         }
         $newString .= $padStr.$str.$breakStr;
       }
       else
       {
         $newString .= $padStr.$lines[$x].$breakStr;
       }
     }
     return $newString;
   }
   
   
?>
