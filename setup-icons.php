<?php
   $starttime = microtime();
   $pagetitle = "Setup";

   $functionset = "admin";
   
   include("common.php");
   
   $ThemeName = "plain";
   $bodycolor = "#E9E9E9";
   $marginx = $marginy = 15;
   
   Function miniMime($filename)
   {
     if (preg_match('/\.gif$/i', $filename))
     {
       return "image/gif";
     }
     if (preg_match('/\.(jpg|jpeg)$/i', $filename))
     {
       return "image/jpeg";
     }
   }
   
   $sql = "SELECT * FROM ".TABLE_ICONS." WHERE data IS NULL LIMIT 0, 25";
   $exe = runQuery($sql);
   if (resultCount($exe))
   {
     $results = resultCount($exe)." results.<BR>\n";
     while ($row = fetchResultArray($exe))
     {
       $path = $DocRoot."/gfx/icons/".$row['filename'];
       $mimetype = miniMime($row['filename']);
       if (file_exists($path))
       {
         $filedata = filetoBase64($path);
         $fileupdate['data']     = $filedata;
         $fileupdate['mimetype'] = $mimetype;
         if ($update = updateIcon($row['ID'], $fileupdate))
         {
           $status = "Data inserted";
         }
         else
         {
           $status = mysqli_error($mysql);
         }
       }
       $rowdata[] = "<TR><TD>".$row['ID']."</TD>\n"
                   ."    <TD>".$row['iconname']."</TD>\n"
                   ."    <TD>".$row['filename']."</TD>\n"
                   ."    <TD>".$mimetype."</TD>\n"
                   ."    <TD>".$status."</TD></TR>\n";
     }
     $data = "<TABLE CELLPADDING=3 BORDER=1>\n"
            .implode("", $rowdata)
            ."</TABLE>\n";
   }
   else
   {
     $data = "No icons require action at this time";
   }
   $output = "<B>DiscoBoard Icon Migration</B>\n"
            ."<P>\n"
            .$results
            .$data;
            
   $pagecontents = $output;
   include("layout.php");
?>