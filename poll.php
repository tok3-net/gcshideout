<?php
   $starttime = microtime();
   $protectedpage = 1;
   include("common.php");

   if (!$action)
   { $action = "newpoll"; }
   
   if ($action == "vote")
   {
     if (!checkPollVote($pollid, $userdata['ID']))
     {
       $vote = answerPoll($pollid, $userdata['ID'], $option);
       $sysmsg = "sysmsg=voterecorded";
     }
     else
     {
       $sysmsg = "sysmsg=alreadyvoted";
     }
     
     if ($returnurl)
     {
       
       if (!strstr($returnurl, "?"))
       { $returnurl = $returnurl."?".$sysmsg; }
       else
       { $returnurl = $returnurl."&".$sysmsg; }
       
       Header("Location: $returnurl");
       Exit();
     }
     else
     {
       $post = fetchRow($pollid, TABLE_POSTS, "pollid");
       $output = "Your response to this poll has been recorded.<BR>\n"
                .makeLink("thread.php?threadid=$post[threadid]", "Click here to see it");
     }
   }
   
   if ($action == "savepoll")
   {
     if (!$question)                 { $err[] = "No question was entered"; }
     if ((!$answer1) && (!$answer2)) { $err[] = "At least 2 answers are required"; }
     if (!$expiryyear)               { $err[] = "No expiry year was entered"; }
     else
     {
       if (!checkdate($expirymonth, $expiryday, $expiryyear))
                                     { $err[] = "The expiry date entered was invalid"; }       
     }
     if (overThreshold ("poll", $userdata['ID']))
                                     { $err[] = "Sorry, your poll threshold has been reached"; }
     
     if ($err)
     {
       $action = "newpoll";
     }
     else
     {
       $pollinfo['body']     = $body;
       $pollinfo['ownerid']  = $userdata['ID'];
       $pollinfo['question'] = $question;
       $pollinfo['expiry']   = mktime($expiryhour, $expiryminute, 0, $expirymonth, $expiryday, $expiryyear);
       $pollinfo['boardid']  = $boardid;
       $pollinfo['answer1']  = $answer1;
       $pollinfo['answer2']  = $answer2;
       $pollinfo['answer3']  = $answer3;
       $pollinfo['answer4']  = $answer4;
       $pollinfo['answer5']  = $answer5;
       $pollinfo['answer6']  = $answer6;
       $pollinfo['answer7']  = $answer7;
       $pollinfo['answer8']  = $answer8;
       $pollinfo['answer9']  = $answer9;
       $pollinfo['answer10'] = $answer10;
       
       foreach ($pollinfo as $key => $value)
       {
         if (!false)
         {
           $pollinfo[$key] = addSlashes($value);
         }
       }
       
       $poll = newPoll($pollinfo);
       if ($poll)
       {
         if ($returnurl)
         {
           $sysmsg = "sysmsg=pollcreated";
           
           if (!strstr($returnurl, "?"))
           { $returnurl = $returnurl."?".$sysmsg; }
           else
           { $returnurl = $returnurl."&".$sysmsg; }
           
           Header("Location: $returnurl");
           Exit();
         }
         else
         {
           $output = "Your poll, <I>".$question."</I> has been posted.<BR>\n"
                    .makeLink("thread.php?threadid=$poll", "Click here to see it");
         }
       }
       else
       {
         $output = "Sorry, something went wrong.<BR>\n";
       }
     }
   }
   
   if ($action == "newpoll")
   {
     if (!$boardid) { $err[] = "No board specified"; }
     
     if ($err)
     {
       $reason = implode("<BR>\n", $err);
     }
     
     if ($reason)
     {
       if ($returnurl)
       {
         $sysmsg = "sysmsg=noboard";
         
         if (!strstr($returnurl, "?"))
         { $returnurl = $returnurl."?".$sysmsg; }
         else
         { $returnurl = $returnurl."&".$sysmsg; }
         
         Header("Location: $returnurl");
         Exit();
       }
       else
       {
         $reasonoutput = "<B CLASS='red'>".$reason."</B>\n"
                        ."<P>\n";
       }
     }
     
     $proceedbuttonlabel = "Create Poll";
     
     if (!$expiryday)     { $expiryday    = date("d")+1; }
     if (!$expirymonth)   { $expirymonth  = date("m"); }
     if (!$expiryyear)    { $expiryyear   = date("Y"); }
     if (!$expiryhour)    { $expiryhour   = date("H"); }
     if (!$expiryminute)  { $expiryminute = date("i"); }
     
     $cancontinue = 1;
     if (overThreshold ("poll", $userdata['ID']))
     {
       $cancontinue = 0;
       $thresholdreached = 1;
     }
        $sql = "SELECT vippost FROM " . TABLE_BOARDS . " WHERE ID = '" . $_GET['boardid'] . "'";
   $exe = mysqli_query($mysql, $sql);
   $row = mysqli_fetch_array($exe);
	$vippost = $row['vippost'];
if ((!$vippost) || (checkAccess("accessvip")))
{
$canpost = 1;
}
     if ($cancontinue && $canpost)
     {
       include("elements/poll.php");
       $output = $reasonoutput
                ."<FORM ACTION='$PHP_SELF' METHOD=POST>\n"
                .inputHidden("action", "savepoll")
                .inputHidden("boardid", $boardid)
                .inputHidden("returnurl", $returnurl)
                .$pollform
                //."<SPAN CLASS='InputSection'>Lock Topic?</SPAN> ".inputChoice("yesno", "locked", $locked)
                ."<P>\n"
                .inputSubmit(" ".$proceedbuttonlabel." ")." "
                .inputSubmit("Preview")
                ."</FORM>\n";
     }
     else
     {
       if ($thresholdreached)
       {
         $output = "Sorry, your poll threshold has been reached";
       }
       elseif ($vippost)
       {
header("Location: noaccess.php");
       }
     }
   }

   $output = "<TABLE WIDTH=100% CELLPADDING=20 CELLSPACING=1 BORDER=0>\n"
            ."<TR><TD CLASS='BoardRowBody'>\n"
            ."        ".str_replace("\n", "\n        ", $output)."</TD></TR>\n"
            ."</TABLE>\n";

   $pagecontents = $output;
   include("layout.php");
?>
