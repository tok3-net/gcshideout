<?php
   define("HTML_FUNCTIONS_AVAILABLE", 1);

   // HTML-Related Functions
   // ---------------------------
   
   Function inputTimeZoneSelect($name, $value)
   {
     $now = Date("U");
     $hour = 3600;
     
     $start = -24;
     $end = 24;
     
     for ($i = $start; $i <= $end; $i++ )
     {
       if ($i > 0)
       {
         $prefix = "+";
       }
       
       $displayvalue = "Offset ".$prefix.$i.": ".Date("d M Y H:i:s", ($now+($hour*$i)));
       
       $selected = "";
       if ($i == $value)
       {
         $selected = " SELECTED";
       }
       
       $option[] = " <OPTION VALUE='".$i."'".$selected."> ".$displayvalue." </OPTION>";
     }
     
     $output = "<SELECT NAME='".$name."'>\n"
              .implode("\n", $option)."\n"
              ."</SELECT>\n";
     
     return $output;
   }
   
   // Makes a nice pretty-looking box with a visible dropshadow.
   // Jason ripped this code off from TFAW.com when they attempted
   // to rip him off with $30 shipping for a $27 item. Fair trade.
   Function attentionBox($content, $maincolor = "#E9E9E9", $shadowcolor = "#888888", $contentclass = "smallclear", $extraattributes = "")
   {
     global $GFXRoot;
     
     if (!$extraattributes['tablealignment']) { $extraattributes['tablealignment'] = "CENTER"; }
     if (!$extraattributes['tablewidth'])     { $extraattributes['tablewidth']     = "100%"; }
     
     $output = "<TABLE ALIGN='".$extraattributes['tablealignment']."' WIDTH=".$extraattributes['tablewidth']." CELLPADDING=0 CELLSPACING=0 BORDER=0>\n"
              ."<TR>\n"
              ."    <TD WIDTH=564 BGCOLOR='".$maincolor."' COLSPAN='4'>\n"
              ."        <IMG SRC='$GFXRoot/blank.gif' WIDTH='2' HEIGHT='2'></TD>\n"
              ."    <TD WIDTH=1 BGCOLOR='".$shadowcolor."' ROWSPAN='4'>\n"
              ."        <IMG SRC='$GFXRoot/blank.gif' WIDTH='1' HEIGHT='1'></TD></TR>\n"
              ."<TR>\n"
              ."    <TD WIDTH=2 BGCOLOR='".$maincolor."'>\n"
              ."        <IMG SRC='$GFXRoot/blank.gif' WIDTH='5' HEIGHT='2'></TD>\n"
              ."    <TD WIDTH=100% BGCOLOR='".$maincolor."' ALIGN='left' CLASS='".$contentclass."'>\n"
              ."        ".preg_replace("/\n/", "\n        ", $content)
              ."        </TD>\n"
              ."    <TD WIDTH=100% BGCOLOR='".$maincolor."' ALIGN=RIGHT CLASS='smallclear'>\n"
              ."        <IMG SRC='$GFXRoot/blank.gif' WIDTH='5' HEIGHT='2'></TD>\n"
              ."    <TD WIDTH=2 BGCOLOR='".$maincolor."'>\n"
              ."        <IMG SRC='$GFXRoot/blank.gif' WIDTH='2' HEIGHT='2'></TD></TR>\n"
              ."<TR>\n"
              ."    <TD WIDTH=563 BGCOLOR='".$maincolor."' COLSPAN='4'>\n"
              ."        <IMG SRC='$GFXRoot/blank.gif' WIDTH='2' HEIGHT='2'></TD></TR>\n"
              ."<TR>\n"
              ."    <TD WIDTH=564 BGCOLOR='".$shadowcolor."' COLSPAN='4'>\n"
              ."        <IMG SRC='$GFXRoot/blank.gif' WIDTH='1' HEIGHT='1'></TD></TR>\n"
              ."</TABLE><BR>\n";

     return $output;
   }

   function handleError($errors, $style = "default", $topline = "X", $width = 80)
   {
     global $errmsg;
     global $language;
     
     if ($topline == "X") { $topline = $errmsg['prereggenerictop'][$language]; }
     
     switch ($style)
     {
       default:
         for($i = 0; $i < count($errors); $i++ )
         {
           $erroroutput .= "        &nbsp; ".preg_replace("/\n/", "\n        &nbsp; ", $errors[$i])."<BR>\n";
         }
         $return = "<TABLE WIDTH=".$width."% CELLPADDING=2 CELLSPACING=0 BORDER=0 ALIGN=CENTER><TR><TD BGCOLOR='orange'>\n"
                  ."<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=0 BORDER=0>\n"
                  ."<TR><TD>\n"
                  ."        <B>$topline</B><BR>\n"
                  ."        ".$erroroutput."</TD></TR>\n"
                  ."</TABLE>\n"
                  ."</TABLE><BR>\n";
     }
     
     return $return;
   }

   Function inputDBCycle ($name, $currentvalue, $tablename, $realvalue, $displayvalue, $editorshow = "edit", $orderby = "", $jsname = "", $classname = "", $firstoptionvalue = "", $firstoptiondisplay = "")
   { 
     if (is_array($displayvalue))
     {
       $displayvaluefieldname = $displayvalue['result'];
       $displayvaluefieldcontents = $displayvalue['real']." AS ".$displayvaluefieldname;
     }
     else
     {
       $displayvaluefieldname     = $displayvalue;
       $displayvaluefieldcontents = $displayvalue;
     }
     
     if ($orderby) { $order = " ORDER BY $orderby"; }
     if ($editorshow == "show")
     { 
       $sql = "SELECT $realvalue, $displayvaluefieldcontents FROM $tablename WHERE $realvalue = '$currentvalue' $order";
       //echo $sql."<BR>\n";
       if ($exe = runQuery($sql))
       {
         if (resultCount($exe))
         {
           $row = fetchResultArray($exe);
           $res = $row[$displayvaluefieldname];
         }
       }

       return $res;
     }
     else
     { 
       $sql = "SELECT $realvalue, $displayvaluefieldcontents FROM $tablename $order";
       //$ret .= $sql."<BR>\n";
       $exe = runQuery($sql);
       $rowcount = resultCount($exe);
       if ($classname)
       { $classname = " CLASS='$classname'"; }
       $ret .= "<SELECT NAME='$name'$classname>\n";
       if (($firstoptionvalue) || ($firstoptiondisplay))
       {
         // Only show this if we have a value or a display value
         $ret .= " <OPTION VALUE='$firstoptionvalue'>$firstoptiondisplay</OPTION>\n";
       }
       $i = 0;
       while($row = fetchResultArray($exe))
       { 
         $code = $row[$realvalue];
         $desc = $row[$displayvaluefieldname];
         
         $extra = "";
         if ($code == $currentvalue) { $extra = " SELECTED"; }
         
         $ret .= " <OPTION VALUE='$code'$extra> $desc </OPTION>\n";
       }
       $ret .= "</SELECT>\n";
       return $ret;
     }
   }
   
   Function inputChoice($selecttype, $selectname, $currentvalue, $editorshow = "edit", $withblankoption = "", $Xonitemselectarray = "")
   {
     // Knackered function
     //
     // $Xonitemselectarray needs to be changed to $onitemselectarray to activate
     // custom functions in the onChange properties
     
     global $language;
     global $label;
     global $text;
     
     //echo "genericSelect: $selecttype / $selectname / $currentvalue / $editorshow / $withblankoption / $Xonitemselectarray<BR>\n";
     
     // $onitemselectarray
     // {
     //   key "id"        << The ID of the item to match on
     //   key "content"   << 
     // }
     
     if ($onitemselectarray)
     {
       $javascript = "<SCRIPT LANGUAGE='JavaScript1.2'>\n"
                    ."<!-- \n"
                    ." function form${selectname}change () \n"
                    ." { \n";
       for ($i = 0; $i < count($onitemselectarray); $i++ )
       {
         $javascript .= "   if (document.form.${selectname}.selectedIndex == ".$onitemselectarray[$i]['id'].") \n"
                       ."   { \n"
                       ."     ".$onitemselectarray[$i]['content']." \n"
                       ."   } \n"
                       //."   else \n"
                       //."   { \n"
                       //."     alert(document.form.${selectname}.selectedIndex); \n"
                       //."   } \n"
                       ."";
       }
       $javascript .= " } \n"
                     ." // --> \n"
                     ."</SCRIPT>\n";
     }
     
     // Blank option to start 
     $options[] = "-- Please Select --";

     //echo "SelType: $selecttype<BR>\n";
     switch ($selecttype)
     {
       case "sessiontime":
         $options[] = array("keyword" => "40320", "description" => "Session lasts 4 Weeks");
         $options[] = array("keyword" => "20160", "description" => "Session lasts 2 Weeks");
         $options[] = array("keyword" => "10080", "description" => "Session lasts One Week");
         $options[] = array("keyword" => "1440", "description" => "Session lasts One Day");
         $options[] = array("keyword" => "720", "description" => "Session lasts 12 Hours");
         $options[] = array("keyword" => "480", "description" => "Session lasts 8 Hours");
         $options[] = array("keyword" => "240", "description" => "Session lasts 4 Hours");
         $options[] = array("keyword" => "60", "description" => "Session lasts One Hour");
         break;
       case "colours":
         $options[] = array("keyword" => "", "description" => "None");
         $options[] = array("keyword" => "#F0F8FF", "description" => "AliceBlue");
         $options[] = array("keyword" => "#FAEBD7", "description" => "AntiqueWhite");
         $options[] = array("keyword" => "#00FFFF", "description" => "Aqua");
         $options[] = array("keyword" => "#7FFFD4", "description" => "Aquamarine");
         $options[] = array("keyword" => "#F0FFFF", "description" => "Azure");
         $options[] = array("keyword" => "#F5F5DC", "description" => "Beige");
         $options[] = array("keyword" => "#FFE4C4", "description" => "Bisque");
         $options[] = array("keyword" => "#000000", "description" => "Black");
         $options[] = array("keyword" => "#FFEBCD", "description" => "BlanchedAlmond");
         $options[] = array("keyword" => "#0000FF", "description" => "Blue");
         $options[] = array("keyword" => "#8A2BE2", "description" => "BlueViolet");
         $options[] = array("keyword" => "#A52A2A", "description" => "Brown");
         $options[] = array("keyword" => "#DEB887", "description" => "BurlyWood");
         $options[] = array("keyword" => "#5F9EA0", "description" => "CadetBlue");
         $options[] = array("keyword" => "#7FFF00", "description" => "Chartreuse");
         $options[] = array("keyword" => "#D2691E", "description" => "Chocolate");
         $options[] = array("keyword" => "#FF7F50", "description" => "Coral");
         $options[] = array("keyword" => "#6495ED", "description" => "CornflowerBlue");
         $options[] = array("keyword" => "#FFF8DC", "description" => "Cornsilk");
         $options[] = array("keyword" => "#DC143C", "description" => "Crimson");
         $options[] = array("keyword" => "#00FFFF", "description" => "Cyan");
         $options[] = array("keyword" => "#00008B", "description" => "DarkBlue");
         $options[] = array("keyword" => "#008B8B", "description" => "DarkCyan");
         $options[] = array("keyword" => "#B8860B", "description" => "DarkGoldenRod");
         $options[] = array("keyword" => "#A9A9A9", "description" => "DarkGray");
         $options[] = array("keyword" => "#006400", "description" => "DarkGreen");
         $options[] = array("keyword" => "#BDB76B", "description" => "DarkKhaki");
         $options[] = array("keyword" => "#8B008B", "description" => "DarkMagenta");
         $options[] = array("keyword" => "#556B2F", "description" => "DarkOliveGreen");
         $options[] = array("keyword" => "#FF8C00", "description" => "Darkorange");
         $options[] = array("keyword" => "#9932CC", "description" => "DarkOrchid");
         $options[] = array("keyword" => "#8B0000", "description" => "DarkRed");
         $options[] = array("keyword" => "#E9967A", "description" => "DarkSalmon");
         $options[] = array("keyword" => "#8FBC8F", "description" => "DarkSeaGreen");
         $options[] = array("keyword" => "#483D8B", "description" => "DarkSlateBlue");
         $options[] = array("keyword" => "#2F4F4F", "description" => "DarkSlateGray");
         $options[] = array("keyword" => "#00CED1", "description" => "DarkTurquoise");
         $options[] = array("keyword" => "#9400D3", "description" => "DarkViolet");
         $options[] = array("keyword" => "#FF1493", "description" => "DeepPink");
         $options[] = array("keyword" => "#00BFFF", "description" => "DeepSkyBlue");
         $options[] = array("keyword" => "#696969", "description" => "DimGray");
         $options[] = array("keyword" => "#1E90FF", "description" => "DodgerBlue");
         $options[] = array("keyword" => "#D19275", "description" => "Feldspar");
         $options[] = array("keyword" => "#B22222", "description" => "FireBrick");
         $options[] = array("keyword" => "#FFFAF0", "description" => "FloralWhite");
         $options[] = array("keyword" => "#228B22", "description" => "ForestGreen");
         $options[] = array("keyword" => "#FF00FF", "description" => "Fuchsia");
         $options[] = array("keyword" => "#DCDCDC", "description" => "Gainsboro");
         $options[] = array("keyword" => "#F8F8FF", "description" => "GhostWhite");
         $options[] = array("keyword" => "#FFD700", "description" => "Gold");
         $options[] = array("keyword" => "#DAA520", "description" => "GoldenRod");
         $options[] = array("keyword" => "#808080", "description" => "Gray");
         $options[] = array("keyword" => "#008000", "description" => "Green");
         $options[] = array("keyword" => "#ADFF2F", "description" => "GreenYellow");
         $options[] = array("keyword" => "#F0FFF0", "description" => "HoneyDew");
         $options[] = array("keyword" => "#FF69B4", "description" => "HotPink");
         $options[] = array("keyword" => "#CD5C5C", "description" => "IndianRed");
         $options[] = array("keyword" => "#4B0082", "description" => "Indigo");
         $options[] = array("keyword" => "#FFFFF0", "description" => "Ivory");
         $options[] = array("keyword" => "#F0E68C", "description" => "Khaki");
         $options[] = array("keyword" => "#E6E6FA", "description" => "Lavender");
         $options[] = array("keyword" => "#FFF0F5", "description" => "LavenderBlush");
         $options[] = array("keyword" => "#7CFC00", "description" => "LawnGreen");
         $options[] = array("keyword" => "#FFFACD", "description" => "LemonChiffon");
         $options[] = array("keyword" => "#ADD8E6", "description" => "LightBlue");
         $options[] = array("keyword" => "#F08080", "description" => "LightCoral");
         $options[] = array("keyword" => "#E0FFFF", "description" => "LightCyan");
         $options[] = array("keyword" => "#FAFAD2", "description" => "LightGoldenRodYellow");
         $options[] = array("keyword" => "#D3D3D3", "description" => "LightGrey");
         $options[] = array("keyword" => "#90EE90", "description" => "LightGreen");
         $options[] = array("keyword" => "#FFB6C1", "description" => "LightPink");
         $options[] = array("keyword" => "#FFA07A", "description" => "LightSalmon");
         $options[] = array("keyword" => "#20B2AA", "description" => "LightSeaGreen");
         $options[] = array("keyword" => "#87CEFA", "description" => "LightSkyBlue");
         $options[] = array("keyword" => "#8470FF", "description" => "LightSlateBlue");
         $options[] = array("keyword" => "#778899", "description" => "LightSlateGray");
         $options[] = array("keyword" => "#B0C4DE", "description" => "LightSteelBlue");
         $options[] = array("keyword" => "#FFFFE0", "description" => "LightYellow");
         $options[] = array("keyword" => "#00FF00", "description" => "Lime");
         $options[] = array("keyword" => "#32CD32", "description" => "LimeGreen");
         $options[] = array("keyword" => "#FAF0E6", "description" => "Linen");
         $options[] = array("keyword" => "#FF00FF", "description" => "Magenta");
         $options[] = array("keyword" => "#800000", "description" => "Maroon");
         $options[] = array("keyword" => "#66CDAA", "description" => "MediumAquaMarine");
         $options[] = array("keyword" => "#0000CD", "description" => "MediumBlue");
         $options[] = array("keyword" => "#BA55D3", "description" => "MediumOrchid");
         $options[] = array("keyword" => "#9370D8", "description" => "MediumPurple");
         $options[] = array("keyword" => "#3CB371", "description" => "MediumSeaGreen");
         $options[] = array("keyword" => "#7B68EE", "description" => "MediumSlateBlue");
         $options[] = array("keyword" => "#00FA9A", "description" => "MediumSpringGreen");
         $options[] = array("keyword" => "#48D1CC", "description" => "MediumTurquoise");
         $options[] = array("keyword" => "#C71585", "description" => "MediumVioletRed");
         $options[] = array("keyword" => "#191970", "description" => "MidnightBlue");
         $options[] = array("keyword" => "#F5FFFA", "description" => "MintCream");
         $options[] = array("keyword" => "#FFE4E1", "description" => "MistyRose");
         $options[] = array("keyword" => "#FFE4B5", "description" => "Moccasin");
         $options[] = array("keyword" => "#FFDEAD", "description" => "NavajoWhite");
         $options[] = array("keyword" => "#000080", "description" => "Navy");
         $options[] = array("keyword" => "#FDF5E6", "description" => "OldLace");
         $options[] = array("keyword" => "#808000", "description" => "Olive");
         $options[] = array("keyword" => "#6B8E23", "description" => "OliveDrab");
         $options[] = array("keyword" => "#FFA500", "description" => "Orange");
         $options[] = array("keyword" => "#FF4500", "description" => "OrangeRed");
         $options[] = array("keyword" => "#DA70D6", "description" => "Orchid");
         $options[] = array("keyword" => "#EEE8AA", "description" => "PaleGoldenRod");
         $options[] = array("keyword" => "#98FB98", "description" => "PaleGreen");
         $options[] = array("keyword" => "#AFEEEE", "description" => "PaleTurquoise");
         $options[] = array("keyword" => "#D87093", "description" => "PaleVioletRed");
         $options[] = array("keyword" => "#FFEFD5", "description" => "PapayaWhip");
         $options[] = array("keyword" => "#FFDAB9", "description" => "PeachPuff");
         $options[] = array("keyword" => "#CD853F", "description" => "Peru");
         $options[] = array("keyword" => "#FFC0CB", "description" => "Pink");
         $options[] = array("keyword" => "#DDA0DD", "description" => "Plum");
         $options[] = array("keyword" => "#B0E0E6", "description" => "PowderBlue");
         $options[] = array("keyword" => "#800080", "description" => "Purple");
         $options[] = array("keyword" => "#FF0000", "description" => "Red");
         $options[] = array("keyword" => "#BC8F8F", "description" => "RosyBrown");
         $options[] = array("keyword" => "#4169E1", "description" => "RoyalBlue");
         $options[] = array("keyword" => "#8B4513", "description" => "SaddleBrown");
         $options[] = array("keyword" => "#FA8072", "description" => "Salmon");
         $options[] = array("keyword" => "#F4A460", "description" => "SandyBrown");
         $options[] = array("keyword" => "#2E8B57", "description" => "SeaGreen");
         $options[] = array("keyword" => "#FFF5EE", "description" => "SeaShell");
         $options[] = array("keyword" => "#A0522D", "description" => "Sienna");
         $options[] = array("keyword" => "#C0C0C0", "description" => "Silver");
         $options[] = array("keyword" => "#87CEEB", "description" => "SkyBlue");
         $options[] = array("keyword" => "#6A5ACD", "description" => "SlateBlue");
         $options[] = array("keyword" => "#708090", "description" => "SlateGray");
         $options[] = array("keyword" => "#FFFAFA", "description" => "Snow");
         $options[] = array("keyword" => "#00FF7F", "description" => "SpringGreen");
         $options[] = array("keyword" => "#4682B4", "description" => "SteelBlue");
         $options[] = array("keyword" => "#D2B48C", "description" => "Tan");
         $options[] = array("keyword" => "#008080", "description" => "Teal");
         $options[] = array("keyword" => "#D8BFD8", "description" => "Thistle");
         $options[] = array("keyword" => "#FF6347", "description" => "Tomato");
         $options[] = array("keyword" => "#40E0D0", "description" => "Turquoise");
         $options[] = array("keyword" => "#EE82EE", "description" => "Violet");
         $options[] = array("keyword" => "#D02090", "description" => "VioletRed");
         $options[] = array("keyword" => "#F5DEB3", "description" => "Wheat");
         $options[] = array("keyword" => "#FFFFFF", "description" => "White");
         $options[] = array("keyword" => "#F5F5F5", "description" => "WhiteSmoke");
         $options[] = array("keyword" => "#FFFF00", "description" => "Yellow");
         $options[] = array("keyword" => "#9ACD32", "description" => "YellowGreen");
         break;
       case "yesno":
         $options[] = array("keyword" => "YES", "description" => "Yes");
         $options[] = array("keyword" => "NO", "description" => "No");
         break;
       case "gender":
         $options[] = array("keyword" => "M", "description" => "Male");
         $options[] = array("keyword" => "F", "description" => "Femle");
         break;
       case "perpage":
         $options[] = array("keyword" => "5", "description" => "5");
         $options[] = array("keyword" => "10", "description" => "10");
         $options[] = array("keyword" => "25", "description" => "25");
         $options[] = array("keyword" => "50", "description" => "50");
         break;
     }
     
     //echo "WithBlank: $withblankoption<BR>\n";
     
     $start = 1;
     if ($withblankoption)
     {
       $start = 0;
     }

     //echo "Got ".count($options)." options<BR>\n";
     for ($i = intval($start); $i < count($options); $i++ )
     {
       $selected = "";
       if (is_array($options[$i]))
       {
         // We're using a 2-d keyword/description array
         if (strval($options[$i]['keyword']) == strval($currentvalue))
         { 
           if ($editorshow == "showdescription")
           {
             //echo "showdescr mode<BR>\n";
             $actualvalue = $options[$i]['description'];
           }
           else
           {
             $actualvalue = $currentvalue; 
           }
           $selected = " SELECTED";
         }
         $optionlist .= " <OPTION VALUE='".$options[$i]['keyword']."'$selected> ".$options[$i]['description']." </OPTIONS>\n";
         //echo "I: $i, Option: ".$options[$i]['keyword'].", Desc: ".$options[$i]['description'].", CurValue: $currentvalue, Selected: $selected<BR>\n";
       }
       else
       {
         // We're using integers to refer to our 1-d array
         //                           ... but we may have passed in the real value
         if (($i == $currentvalue) || ($currentvalue == $options[$i]))
         { 
           $actualvalue = $options[$i]; 
           $selected = " SELECTED"; 
         }
         $optionlist .= " <OPTION VALUE='$i'$selected> $options[$i] </OPTIONS>\n";
         //echo "I: $i, CurValue: $currentvalue, Selected: $selected<BR>\n";
       }
     }
     
     if ($onitemselectarray)
     {
       $onchange = " onChange='form${selectname}change();'";
     }
     
     $out .= "<SELECT NAME='$selectname'$onchange>\n"
            .$optionlist
            ."</SELECT>\n";
     
     if (strstr($editorshow, "show"))
     { 
       $out = $actualvalue;
       if (!isset($currentvalue))
       { $out = ""; }
     }
     
     return $javascript
           .$out;
   }

   Function inputCountry ($selectname, $currentvalue, $editorshow = "edit")
   {
     // FORM INPUT FUNCTION
     // Uses the country_codes table in the database to produce a nice pretty
     // cycle gadget containing all the country codes. Use sparingly if you can,
     // cos it is large!

     global $language;
     
     $countrysql = "SELECT ID, abbrev, descr FROM ".TABLE_COUNTRY_CODES." ORDER BY descr";
     $countryexe = runQuery($countrysql);
     $countryrow = resultCount($countryexe);
     
     $out .= "<SELECT NAME='$selectname'>\n"
            ." <OPTION VALUE=''> -- Select Country -- </OPTION>\n";
     while ($row = fetchResultArray($countryexe))
     {
       $countryabbrev = $row['abbrev'];
       $countrydescr = $row['descr'];
       
       $select = "";
       if (strtoupper($currentvalue) == strtoupper($countryabbrev)) { $select = " SELECTED"; $value = $countrydescr; }
       
       if ($countrydescr != $lastcountrydescr)
       {
         $out .= " <OPTION VALUE='$countryabbrev'$select> $countrydescr </OPTION>\n";
       }
       $lastcountrydescr = $countrydescr;
     }
     $out .= "</SELECT>\n";
     
     if ($editorshow == "edit")
     {
       return $out;
     }
     else
     {
       return "$value";
     }
   }
   
   Function titleCycle ($name, $title, $editorshow = "", $noclass = "")
   {
     // FORM INPUT FUNCTION
     // Uses the title array in the definitions file to create
     // a cycle gadget.

     global $titlearray;
     
     $a  = "<SELECT NAME='$name'>\n"
          ." <OPTION VALUE=''></OPTION>\n";

     for ($i = 0; $i < count($titlearray); $i++ )
     { 
       $dbtitle = $titlearray[$i];
       //echo "title = $title<br>";
       //echo "dbtitle = $dbtitle<br>";
       $select = "";
       if ($title == $dbtitle)
       {
         $select = " SELECTED"; 
         $actual = $title; 
       }
       
       $a .= " <OPTION VALUE='$dbtitle'$select> $dbtitle </OPTION>\n";
     }
     $a .= "</SELECT>\n";
     
     $returnvalue = $a;
     if ($editorshow == "show")
     { 
       if (!$noclass)
       { $returnvalue = "<B CLASS='userdata'>$actual</B>"; }
       else
       { $returnvalue = "<B>$actual</B>"; }
     }
     return $returnvalue;
   }

   Function inputHidden ($name, $value)
   { 
     // FORM INPUT FUNCTION
     // Returns an input type=hidden for a name/value pair.
     return  "<INPUT TYPE=HIDDEN NAME='$name' VALUE=\"$value\">\n";
   }

   Function inputDMY ($prefix, $unixtime = 1, $editorshow = "edit", $withblanks = "", $textclass = "")
   { 
     // FORM INPUT FUNCTION
     // Returns three cycle gadgets for day, month, year. Uses the
     // input Unix Timestamp to decide what options are selected.
     
     if ($editorshow == "show")
     { 
       $day   = Date("d", $unixtime);
       $month = Date("M", $unixtime);
       $year  = Date("Y", $unixtime);

       $a = "$month $day, $year";
     }
     else
     {
       if ($unixtime == 1) { $unixtime = Date("U"); }
       $day   = Date("d", $unixtime);
       $month = Date("n", $unixtime);
       $year  = Date("Y", $unixtime);
       
       $a .= "<SELECT NAME='".$prefix."day'>\n";
       if ($withblanks)
       { $a .= " <OPTION VALUE=''></OPTION>\n"; }
       for ($dayi = 1; $dayi <= 31; $dayi++ )
       { 
         $selected = "";
         if ($day == $dayi)
         { $selected = " SELECTED"; }
         $a .= " <OPTION VALUE='$dayi'$selected> $dayi </OPTION>\n";
       }
       $a .= "</SELECT>\n"
            ."<SELECT NAME='".$prefix."month'>\n";
       if ($withblanks)
       { $a .= " <OPTION VALUE=''></OPTION>\n"; }
       for ($monthi = 1; $monthi <= 12; $monthi++ )
       { 
         $selected = "";
         if ($month == $monthi)
         { $selected = " SELECTED"; }
         $a .= " <OPTION VALUE='$monthi'$selected> ".Date("F", mktime("", "", "", $monthi, 1, 2000))."</OPTION>\n";
       }
       $a .= "</SELECT>\n"
            .inputText($prefix."year", $year, 5, 4, $textclass);
       //     ."<SELECT NAME='".$prefix."year'>\n";
       //if ($withblanks)
       //{ $a .= " <OPTION VALUE=''></OPTION>\n"; }
       //for ($yeari = 2000; $yeari <= Date("Y"); $yeari++ )
       //{ 
       //  $selected = "";
       //  if ($year == $yeari)
       //  { $selected = " SELECTED"; }
       //  $a .= " <OPTION VALUE='$yeari'$selected> $yeari </OPTION>\n";
       //}
       //$a .= "</SELECT>\n";
     }
     return $a;
   }
    
   Function inputDMYtext ($prefix, $selectday, $selectmonth, $selectyear, $editorshow = "edit", $withblanks = "", $size="", $textclass = "")
   { 
     // FORM INPUT FUNCTION
     // Returns two cycle gadgets for day, month, and a text gadget for
     // year (in case it's got a large range). Uses the input Unix 
     // Timestamp to decide what options are selected.
     
     //echo "edit/show: $editorshow - unix time: $unixtime<BR>\n";
     if ($editorshow == "show")
     { 
       $a = Date("M", mktime(0, 0, 0, $selectmonth, 1, 2000))." $selectday, $selectyear";
     }
     else
     {
       $day   = $selectday;
       $month = $selectmonth;
       $year  = $selectyear;
       
       $a .= "<SELECT NAME='".$prefix."day'>\n";
       if ($withblanks)
       { $a .= " <OPTION VALUE=''></OPTION>\n"; }
       for ($dayi = 1; $dayi <= 31; $dayi++ )
       { 
         $selected = "";
         if ($selectday == $dayi)
         { $selected = " SELECTED"; }
         $a .= " <OPTION VALUE='$dayi'$selected> $dayi </OPTION>\n";
       }
       $a .= "</SELECT>\n"
            ."<SELECT NAME='".$prefix."month'>\n";
       if ($withblanks)
       { $a .= " <OPTION VALUE=''></OPTION>\n"; }
       for ($monthi = 1; $monthi <= 12; $monthi++ )
       { 
         $selected = "";
         if ($selectmonth == $monthi)
         { $selected = " SELECTED"; }
         $a .= " <OPTION VALUE='$monthi'$selected> ".Date("F", mktime("", "", "", $monthi, 1, 2000))."</OPTION>\n";
       }
       $a .= "</SELECT>\n"
            .inputText($prefix."year", $selectyear, 5, 4, $textclass)."\n";
     }
     return $a;
   }
   
   // TABLE "MONTHS" DOES NOT EXIST, DONT USE THIS FUNCTION BEFORE CREATING IT
   Function inputMY ($prefix, $yearstart, $yearend, $thismonth, $thisyear)
   {
     // FORM INPUT FUNCTION
     // Returns two cycle gadgets for month, year. Uses the yearstart
     // and yearend to define the bounds for the year gadget, and accepts
     // the input thismonth and thisyear to decide what options are selected.
     
     global $language;
     
     $monthout = "<SELECT NAME='".$prefix."month'>\n";
     for ($i = 1; $i <= 12; $i++ )
     {
       // Pull out names of the month from the db table 'months'.
       $monthdata = fetchRow($i, "months");
       $monthnamevalue['en'] = $monthdata['name_en'];
       $monthnamevalue['es'] = $monthdata['name_es'];
       $monthname = $monthnamevalue[$language];
       //echo "Row $i. Month: $monthname<BR>\n";
       $monthselect = ""; 
       if ($i == $thismonth)
       {
         $monthselect = " SELECTED"; 
       }
       $monthout .= " <OPTION VALUE='$i'$monthselect> $monthname </OPTION>\n";
     }
     $monthout .= "</SELECT>\n";
     
     $yearout .= "<SELECT NAME='".$prefix."year'>\n";
     for ($i = $yearstart; $i <= $yearend; $i++ )
     {
       $yearselect = ""; 
       if ($i == $thisyear)
       {
         $yearselect = " SELECTED"; 
       }
       $yearout .= " <OPTION VALUE='$i'$yearselect> $i </OPTION>\n";
     }
     $yearout .= "</SELECT>\n";
     
     $result['month'] = $monthout;
     $result['year'] = $yearout;
     
     return $result;
   }

   Function inputHM ($prefix, $thishour, $thisminute, $returnarray = "")
   {
     // FORM INPUT FUNCTION
     // Returns two cycle gadgets for hour, minute.
     
     global $language;
     
     //echo "Thishour $thishour, Thisminute $thisminute<BR>\n";
     $hourout = "<SELECT NAME='".$prefix."hour'>\n";
     for ($i = 0; $i <= 23; $i++ )
     {
       $hourselect = ""; 
       if ($i == $thishour)
       {
         $hourselect = " SELECTED"; 
       }
       $hourout .= " <OPTION VALUE='".substr("0".$i, -2)."'$hourselect> ".substr("0".$i, -2)." </OPTION>\n";
     }
     $hourout .= "</SELECT>\n";

     $minuteout = "<SELECT NAME='".$prefix."minute'>\n";
     for ($i = 0; $i <= 59; $i++ )
     {
       $minuteselect = ""; 
       if ($i == $thisminute)
       {
         $minuteselect = " SELECTED"; 
       }
       $minuteout .= " <OPTION VALUE='".substr("0".$i, -2)."'$minuteselect> ".substr("0".$i, -2)." </OPTION>\n";
     }
     $minuteout .= "</SELECT>\n";
     
     if ($returnarray)
     {
       $result['hour'] = $hourout;
       $result['minute'] = $minuteout;
     }
     else
     {
       $result = $hourout.":".$minuteout;
     }
     
     return $result;
   }

   Function inputTextArea ($name, $value, $cols = 5, $rows = 50, $class = "", $javascript = "", $editorshow = "", $linewrapfix = "", $wraptype = "VIRTUAL")
   { 
     // FORM INPUT FUNCTION
     // Returns a textarea gadget.

     if (!$editorshow)
     {
       $editorshow = "edit";
     }
     
     if ($editorshow == "edit")
     { 
       $rows = " ROWS=$rows";
       $cols = " COLS=$cols";
       $size = "$rows $cols";
       if ($class)
       { $class = " CLASS='$class'"; }
       else
       { 
         //$class = " CLASS='nonprop' STYLE=\"font-family: Courier, monospace;\""; 
       }
       if ($javascript)
       { $javascript = " $javascript"; }
       
       $actualvalue = htmlentities($value);
       if ($linewrapfix)
       {
         $actualvalue = preg_replace("/\n/", "\n#%#", htmlentities($value));
       }
       return "<TEXTAREA NAME='$name' WRAP=$wraptype ".$size.$class.$javascript.">"
             .$actualvalue
             ."</TEXTAREA>\n";
     }
     else
     { return "<B CLASS='userdata'>$value</SPAN>"; }
   }
   
   Function inputSubmit($value, $class = "", $name = "button")
   {
     if ($class)
     { $classtag = " CLASS='$class'"; }
     else
     { $classtag = " CLASS='submitbutton'"; }
     if ($name)
     { $nametag = " NAME='$name'"; }
     
     $tag = "<INPUT TYPE=SUBMIT VALUE='$value'".$classtag.$nametag.">";
     
     return $tag;
   }
   
   Function inputImageSubmit($image, $width, $height, $alt)
   {
     $tag = "<INPUT TYPE=IMAGE SRC='$image' WIDTH=$width HEIGHT=$height ALT='$alt'>";
     
     return $tag;
   }
   
   Function inputPassword ($name, $value, $size = "", $maxsize = "", $class = "", $javascript = "", $editorshow = "edit", $password = "")
   { 
     $res = inputText ($name, $value, $size, $maxsize, $class, $javascript, $editorshow, $password);
     $res = preg_replace("/TYPE=TEXT/", "TYPE=PASSWORD", $res);
     return $res;
   }
   
   Function inputText ($name, $value, $size = "", $maxsize = "", $class = "", $javascript = "", $editorshow = "edit", $password = "")
   { 
     // FORM INPUT FUNCTION
     // Returns a text input gadget.

     // Find out if we're on a multilingual page
     global $encoding;
     
     if ($editorshow == "edit")
     { 
       if ($size)
       {
         $size = " SIZE=$size"; 
       }
       
       if (($maxsize) && ($maxsize != "-"))
       { $maxsize = " MAXLENGTH=$maxsize"; }
       
       if ($class)
       { $class = " CLASS='$class'"; }
       else
       { 
         //$class = " CLASS='body'"; 
         $class = "";
       }
       
       if ($javascript)
       { $javascript = " $javascript"; }
       
       $type = "TEXT";
       if ($password)
       { $type = "PASSWORD"; }
       
       // Multilingual input. We can't use HTML Entities for the string.
       if (!$encoding)
       { $value = htmlentities($value); }
       
       return "<INPUT TYPE=$type NAME='$name' VALUE=\"$value\"".$size.$maxsize.$class.$javascript.">";
     }
     else
     { return $value; }
   }

   Function inputNumberCycle($name, $value, $low, $high, $editorshow = "edit", $years ="")
   {
     if($years == "") {
       $a .= "<SELECT NAME='$name'>\n"
            ." <OPTION VALUE=''> </OPTION>\n";
     } else {
       $a .= "<SELECT NAME='$name'>\n";
     }

     for ($i = $low; $i <= $high; $i++ )
     {
       $select = "";
       if ($value == $i)
        { $select = " SELECTED"; }
       
       
       if ($years == 1)
        { if ($i == 1)
            { $year = "year";}
          else
            { $year = "years"; }
        }
                            
       $a .= " <OPTION VALUE='$i'$select> $i $year </OPTION>\n";
     }
     $a .= "</SELECT>\n";
     
     if ($editorshow == "edit")
     {
       return $a;
     }
     else
     {
       return $value;
     }
   }

   Function inputCheckbox($name, $value, $currentvalue = "")
   {
     if ($currentvalue == $value)
     { $checked = " CHECKED"; }
     
     return "<INPUT TYPE=CHECKBOX NAME='$name' VALUE='$value'".$checked.">";
   }
?>
