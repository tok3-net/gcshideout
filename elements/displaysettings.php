<?php
$displaysettingsform = "
<SPAN CLASS='InputSection'>Posts Per Page</SPAN><BR>
Controls the maximum number of posts you will see on a page at a time when 
viewing a thread.<BR>
".inputChoice("perpage", "perpageposts", $userinfo['perpageposts'])."
<P>
<SPAN CLASS='InputSection'>Threads Per Page</SPAN><BR>
Controls the number of threads you will see on a page at a time when viewing
the threads posted on a board.<BR>
".inputChoice("perpage", "perpagethreads", $userinfo['perpagethreads'])."
<P>
<SPAN CLASS='InputSection'>Timezone Settings</SPAN><BR>
The current system time is <I>".Date("d M Y H:i:s")."</I><BR>
Your configuration shows the current time as <I>".dateNeat(Date("U"), "datetime")."</I><BR>
Select the number of hours offset required to change this to your local time.<BR>
<I>Default setting is currently <U>".$TimeZoneChange."</U></I><BR>
".inputTimeZoneSelect("timezone", $userinfo['timezone'])."
";

$stylesheetform = "
<SPAN CLASS='InputSection'>Board Stylesheet</SPAN><BR>
Choose the stylesheet you would like to view the board with.<BR>
<SELECT NAME='stylesheet'>
<option value='default'>".$DiscoBoardName."</option>
<option value='bluesmall'>Blue Small</option>
<option value='cam-black-white'>Cam Black & White</option>
<option value='greenstyle-cam'>Cams Green Style</option>
<option value='crscolors'>CRS Colors</option>
<option value='darkblue'>Dark Blue</option>
<option value='disco'>Disco</option>
<option value='gameshark'>GameShark</option>
<option value='orange'>GameSharks Orange</option>
<option value='ubb'>GameSharks UBB</option>
<option value='gc'>GC</option>
<option value='gc2'>GC 2</option>
<option value='gcshideout'>GCs Hideout</option>
<option value='greenmix'>Green Mix</option>
<option value='ign'>IGN</option>
<option value='jc'>JC</option>
<option value='matrix'>Matrix</option>
<option value='pittsgreen'>Pitts Green</option>
<option value='prappl'>Prappl</option>
<option value='retsel'>Retsel</option>
<option value='summer'>Summer</option>
<option value='tfn-comms'>TFN Comms</option>
<option value='tfn-rmff'>TFN Rmff</option>
<option value='tfn-scifi'>TFN Sci-Fi</option>
<option value='theforce'>The Force</option>
<option value='ve3d'>VE3D</option>
<option value='zaderp'>Zader P</option>
<option value='zklook'>ZK Look</option>
</SELECT>
<P>
";

$starform = "
<SPAN CLASS='InputSection'>Choose A Star System</SPAN><BR>
Choose the star system you would like to use.<BR>
<INPUT TYPE='RADIO' NAME='starsystem' VALUE='1'>
<img src='".$GFXRoot."/stars/star1.gif'><img src='".$GFXRoot."/stars/star2.gif'><img src='".$GFXRoot."/stars/star3.gif'>
<img src='".$GFXRoot."/stars/star4.gif'><img src='".$GFXRoot."/stars/star5.gif'><img src='".$GFXRoot."/stars/star6.gif'>
<img src='".$GFXRoot."/stars/star7.gif'><img src='".$GFXRoot."/stars/star8.gif'><img src='".$GFXRoot."/stars/star9.gif'>
<img src='".$GFXRoot."/stars/star10.gif'><BR>
<INPUT TYPE='RADIO' NAME='starsystem' VALUE='2'>
<img src='".$GFXRoot."/stars/IGN/star1.gif'><img src='".$GFXRoot."/stars/IGN/star2.gif'><img src='".$GFXRoot."/stars/IGN/star3.gif'>
<img src='".$GFXRoot."/stars/IGN/star4.gif'><img src='".$GFXRoot."/stars/IGN/star5.gif'><img src='".$GFXRoot."/stars/IGN/star6.gif'>
<img src='".$GFXRoot."/stars/IGN/star7.gif'><img src='".$GFXRoot."/stars/IGN/star8.gif'><img src='".$GFXRoot."/stars/IGN/star9.gif'>
<img src='".$GFXRoot."/stars/IGN/star10.gif'><BR>
<INPUT TYPE='RADIO' NAME='starsystem' VALUE='3'>
<img src='".$GFXRoot."/stars/Red/star1.gif'><img src='".$GFXRoot."/stars/Red/star2.gif'><img src='".$GFXRoot."/stars/Red/star3.gif'>
<img src='".$GFXRoot."/stars/Red/star4.gif'><img src='".$GFXRoot."/stars/Red/star5.gif'><img src='".$GFXRoot."/stars/Red/star6.gif'>
<img src='".$GFXRoot."/stars/Red/star7.gif'><img src='".$GFXRoot."/stars/Red/star8.gif'><img src='".$GFXRoot."/stars/Red/star9.gif'>
<img src='".$GFXRoot."/stars/Red/star10.gif'>
<P>
";
?>