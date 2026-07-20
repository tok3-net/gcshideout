<?php
   define("TEXTFORMAT_FUNCTIONS_AVAILABLE", 1);

   // This function handles making everything nice and pretty in general.
   // It calls on other functions for specific effects, and then finally
   // does URL detection itself (for images and links)
   Function bodyText($text, $displayname = "")
   {
     global $configoptions;
     // Change all HTML special characters into their appropriate entities
     // so that we don't output anything odd
     $text = htmlentities($text);
     
     // Change smilies into graphics
     $text = smileEffects($text);
     
     // Perform text formatting markups (colours)
     $text = textEffects($text, $displayname);
     
     // Used for a regex later - these characters are not allowed in 
     $disallowedchars = $configoptions['disallowedchars'];

     while ( preg_match('/\[[a-zA-Z0-9\=\_\:\/\.\~\? -\&]*\]/i', $text, $brackets) )
     { 
       //echo "Found ".count($brackets)." bracket items in text.<BR>\n";
       
       $item = 0;
       while( $brackets[$item] )
       {
         //echo "<HR>Match #".$item.":<BR>\n".htmlentities($brackets[$item])."<HR>\n";
         $thiskeylink = $brackets[$item];
         //echo "Working with: ".$thiskeylink."<BR>\n";
         $thiskeylink = str_replace("[", "", $thiskeylink);
         $thiskeylink = str_replace("]", "", $thiskeylink);
         
         //echo "Found: ".$thiskeylink." (was ".$brackets[$item].")<BR>\n";
         
         $char4 = substr($thiskeylink, 0, 4);
         //echo "That's ".$char4."<BR>\n";
         
         $replacement = "";
         switch($char4)
         {
           case "link":
             $linkurl = trim(str_replace("link=", "", $thiskeylink));
             //echo "linkurl: ".htmlentities($linkurl)."<BR>\n";
             if (preg_match('/'.$disallowedchars.'/', $linkurl))
             {
               //echo "<B>Bad you</B><BR>\n";
               $replacement = "";
             }
             else
             {
               $replacement = "<A HREF='$linkurl' TARGET='_new'>";
             }
             break;
           case "/lin":
             $replacement = "</A>";
             break;

           case "fima":
             $fimageurl = trim(str_replace("fimage=", "", $thiskeylink));
             //echo "fimageurl: ".htmlentities($imageurl)."<BR>\n";
             if (preg_match('/'.$disallowedchars.'/', $fimageurl))
             {
               //echo "<B>Bad you</B><BR>\n";
               $replacement = "";
             }
             else
             {
               $replacement = "<IMG SRC='$fimageurl' BORDER=0>";
             }
             break;
           case "imag":
             $imageurl = trim(str_replace("image=", "", $thiskeylink));
             //echo "imageurl: ".htmlentities($imageurl)."<BR>\n";
             if (preg_match('/'.$disallowedchars.'/', $imageurl))
             {
               //echo "<B>Bad you</B><BR>\n";
               $replacement = "";
             }
             else
             {
               $replacement = "<A HREF='$imageurl' TARGET='_new'><IMG SRC='$imageurl' BORDER=1 WIDTH=80 HEIGHT=80 HSPACE=5 VSPACE=5></A>";
             }
             break;
           case "ucol":
             $sendusername = trim(str_replace("ucol=", "", $thiskeylink));

             if (preg_match('/'.$disallowedchars.'/', $sendusername))
             {
               //echo "<B>Bad you</B><BR>\n";
               $replacement = "";
             }
             else
             {
               $replacement = userColorDisplay($sendusername, 0);
             }
             break;
           case "/uco":
             $replacement = userColorDisplay("", 0, 1)."</SPAN>";
             break;

           case "icon":
             $iconurl = trim(str_replace("icon=", "", $thiskeylink));
             $replacement = "<A HREF='$iconurl' TARGET='_new'><IMG SRC='$iconurl' BORDER=0 WIDTH=80 HEIGHT=80 HSPACE=5 VSPACE=5 STYLE='background-color: #b1b3bc; padding: 8px;'></A>";
             break;
           default:
             // If they just happen to have typed something between [] then let it 
             // through... but in this case we need to kill the brackets so turn it
             // into a placeholder we'll substitute back later...
             $replacement = "%%SQOPEN%%".$thiskeylink."%%SQCLOSE%%";
         }
         //echo "Replacing ".$brackets[$item]." with ".htmlentities($replacement)."...<BR>\n";
         $text = str_replace($brackets[$item], $replacement, $text);
         $item++;
       }
     }
     
     // Now, substitute our placeholders back
     $text = str_replace("%%SQOPEN%%", "[", $text);
     $text = str_replace("%%SQCLOSE%%", "]", $text);
     
     // PHP.net's manual page says this regex represents a URL. It seems to work.
     $url = "[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]";

     // Finally, find URLs on thir own and mark them up into links.
     $text = preg_replace("#^([".$url."]*)#", "<A HREF='\\1' TARGET='_new'>\\1</A>", $text);
     $text = preg_replace("# ([".$url."]*)#", "<A HREF='\\1' TARGET='_new'>\\1</A>", $text);
     $text = preg_replace("#\n([".$url."]*)#", "\n<A HREF='\\1' TARGET='_new'>\\1</A>", $text);

     // Add <BR>s at appropriate places
     $text = nl2br($text);
     
     return $text;
   }
   
   // Uses the Emoticon set defined in the settings file to change certain 
   // parts of messages into graphical smileys. The rule is that a space 
   // must precede the smiley for it to be changed (although if its the 
   // first text of a post, we'll allow it).
   Function smileEffects($text)
   {
     global $configoptions;
     
     $emoticons = $configoptions['emoticons'];
     
     // Add a padding space at the start so that the substitution rule still works
     $text = " ".$text;
     
     for ($i = 0; $i < count($emoticons); $i++ )
     {
       $emoticonsrule = $emoticons[$i];
       
       $emoticonsimage = "<IMG ALIGN=ABSMIDDLE SRC='gfx/faces/".$emoticonsrule['filename']."'>";
       
       // Replace the code
       $text = str_replace(" ".$emoticonsrule['code'], " ".$emoticonsimage, $text);
       $text = str_replace("\n".$emoticonsrule['code'], "\n".$emoticonsimage, $text);
       
       // Replace the name
       $text = str_replace("[face_".$emoticonsrule['name']."]", $emoticonsimage, $text);
     }

     // Strip the padding space at the start
     $text = substr($text, 1, (strlen($text)-1));

     return $text;
   }
   
   // A simple way to apply only the textEffects() function to given text 
   // while still ensuring no weird HTML codes get output. This is used
   // whereever markup code is allowed in things like board names, group
   // names and subjects.
   Function applyOnlyTextEffects($text)
   {
     $text = htmlentities($text);
     $text = textEffects($text);
     return $text;
   }
   
   // This function handles all markup format codes which are applied to 
   // the text in a string. 
   Function textEffects($text, $displayname = "")
   {
     // Simple replacements
     $replace[] = array("[b]", "<B>");
     $replace[] = array("[/b]", "</B>");
     $replace[] = array("[i]", "<I>");
     $replace[] = array("[/i]", "</I>");
     $replace[] = array("[quote]", "<DIV CLASS='QuotedText'>");
     $replace[] = array("[/quote]", "</DIV>");
     $replace[] = array("[u]", "<U>");
     $replace[] = array("[/u]", "</U>");
     $replace[] = array("[o]", "<SPAN STYLE='text-decoration:overline;'>");
     $replace[] = array("[/o]", "</SPAN>");
     $replace[] = array("[blockquote]", "<BLOCKQUOTE>");
     $replace[] = array("[/blockquote]", "</BLOCKQUOTE>");
     $replace[] = array("[spaces]", "<SPAN STYLE='white-space: pre'>");
     $replace[] = array("[/spaces]", "</SPAN>");
     $replace[] = array("[bq]", "<BLOCKQUOTE>");
     $replace[] = array("[/bq]", "</BLOCKQUOTE>");
     $replace[] = array("[hr]", "<HR SIZE=1>");
     $replace[] = array("[ul]", "<UL>");
     $replace[] = array("[/ul]", "</UL>");
     $replace[] = array("[ol]", "<OL>");
     $replace[] = array("[/ol]", "</OL>");
     $replace[] = array("[li]", "<LI>");
     $replace[] = array("[/li]", "</LI>");
     $replace[] = array("[strike]", "<SPAN STYLE='text-decoration: line-through'>");
     $replace[] = array("[/strike]", "</SPAN>");
	 $replace[] = array("[spoiler]", "<SPAN STYLE='color: black; background-color: black; border-right: 1px dashed blue; border-left: 1px dashed blue; border-bottom: 1px dashed blue; border-top: 1px dashed blue;'>");
	 $replace[] = array("[/spoiler]", "</SPAN>");
	 $replace[] = array("[blink]", "<blink>");
	 $replace[] = array("[/blink]", "</blink>");
 	 $replace[] = array("[center]", "<center>");
	 $replace[] = array("[/center]", "</center>");

     // Only do this one if we HAVE a username to substitute...
     if ($displayname)
     {
       $replace[] = array("/me ", " <LI>".$displayname." ");
     }
     
     // Loop through the replace array and apply each one.
     for ($i = 0; $i < count($replace); $i++ )
     {
       $rule = $replace[$i];
       $text = str_replace($rule[0], $rule[1], $text);
     }
     
     // Find [color=blah][/color] and turn it into <SPAN> tags
     if (strstr($text, "[color"))
     {
       $text = preg_replace("#\[color\=([\#0-9a-zA-Z]*)\]#", "<SPAN STYLE='color: \\1;'>", $text);
       $text = preg_replace("#\[/color]#", "</SPAN>", $text);
     }
     
     // Find [glow=blah][/glow]
     if (strstr($text, "[glow"))
     {
       $text = preg_replace("#\[glow\=(\#0-9a-zA-Z]*)\]#", "<SPAN STYLE='height:2;filter: glow(color=\\1, strength=2);'>", $text);
       $text = preg_replace("#\[/glow]#", "</SPAN>", $text);
     }
     
     // Find [hl=blah][/hl]
     if (strstr($text, "[hl"))
     {
       $text = preg_replace("#\[hl\=([\#0-9a-zA-Z]*)\]#", "<SPAN STYLE='background-color: \\1;'>", $text);
       $text = preg_replace("#\[/hl]#", "</SPAN>", $text);
     }

	 // Find [border=blah][/border]
	 if (strstr($text, "[border"))
	 {
	   $text = preg_replace("#\[border=([\#0-9a-zA-Z]*)\]#", "<SPAN STYLE='border-right: 1px solid \\1; border-left: 1px solid \\1; border-bottom: 1px solid \\1; border-top: 1px solid \\1;'>", $text);
	   $text = preg_replace("#\[/border]#", "</SPAN>", $text);
	 }

	 // Find [dashedborder=blah][/dashedborder]
	 if (strstr($text, "[dashedborder"))
	 {
	   $text = preg_replace("#\[dashedborder=([\#0-9a-zA-Z]*)\]#", "<SPAN STYLE='border-right: 1px dashed \\1; border-left: 1px dashed \\1; border-bottom: 1px dashed \\1; border-top: 1px dashed \\1;'>", $text);
	   $text = preg_replace("#\[/dashedborder]#", "</SPAN>", $text);
	 }

	 // Find [right-border=blah][/right-border]
	 if (strstr($text, "[right-border"))
	 {
	   $text = preg_replace("#\[right-border=([\#0-9a-zA-Z]*)\]#", "<SPAN STYLE='border-right: 1px solid \\1;'>", $text);
	   $text = preg_replace("#\[/right-border]#", "</SPAN>", $text);
	 }

	 // Find [left-border=blah][/left-border]
	 if (strstr($text, "[left-border"))
	 {
	   $text = preg_replace("#\[left-border=([\#0-9a-zA-Z]*)\]#", "<SPAN STYLE='border-left: 1px solid \\1;'>", $text);
	   $text = preg_replace("#\[/left-border]#", "</SPAN>", $text);
	 }

	 // Find [top-border=blah][/top-border]
	 if (strstr($text, "[top-border"))
	 {
	   $text = preg_replace("#\[top-border=([\#0-9a-zA-Z]*)\]#", "<SPAN STYLE='border-top: 1px solid \\1;'>", $text);
	   $text = preg_replace("#\[/top-border]#", "</SPAN>", $text);
	 }

	 // Find [bottom-border=blah][/bottom-border]
	 if (strstr($text, "[bottom-border"))
	 {
	   $text = preg_replace("#\[bottom-border=([\#0-9a-zA-Z]*)\]#", "<SPAN STYLE='border-bottom: 1px solid \\1;'>", $text);
	   $text = preg_replace("#\[/bottom-border]#", "</SPAN>", $text);
	 }

               // Find [vip][/vip]
               if (strstr($text, "[vip]"))
               {
               if(checkAccess("accessvip"))
	 {
               $text = preg_replace("~\[vip]([a-zA-Z0-9\=\_\:\/\.\~\,\$\*\<\>\'\? -\&!@#%^]*)\[/vip]~", "<span style='color:yellow;background-color:black;font-weight:bold;'>
	 * * * Start Of VIP Only Message * * *</span><br><br><i>\\1</i><br><span style='color:yellow;background-color:black;font-weight:bold;'>
	 * * * End Of VIP Only Message * * *</span>", $text);
               }
	 else
	 {
               $text = preg_replace("~\[vip]([a-zA-Z0-9\=\_\:\/\.\~\,\$\*\<\>\'\? -\&!@#%^]*)\[/vip]~", "", $text);
               }
               } 



     
     return $text;
   }
?>