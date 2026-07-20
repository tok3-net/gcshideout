<?php
   // ===============================================================================
   // DiscoBoard Configuration File
   // ===============================================================================

   // Description of this file
   // ------------------------
   // This file forms the guts of DiscoBoard's configuration - filling in the right
   // values here is the bare minimum you can get away with in order to get the 
   // system up and running.
   // 
   // You will find that the file is broken up into 5 major areas:
   //  - Server Configuration
   //  - MySQL Configuration
   //  - Session Settings
   //  - User Restriction Settings
   //  - Cosmetic Settings
   // 
   // Each option is explained by the // comments above it
   // 
   // ===============================================================================

   // Server Configuration
   // -------------------
   
   // AccessProtocol is the protocol that a web browser will use to access your
   // DiscoBoard. This will either be "http" for a URL starting with http://,
   // or "https" for https://
   $AccessProtocol = "http";
   
   // ServerAddress is hostname of the server you are running DiscoBoard on. It 
   // should pick up $HTTP_HOST, but if it doesn't you should set it here. This 
   // shouldn't have a filename, nor HTTP protocol or trailing slash. 
   // Eg, boards.yourdomain.com
   $ServerAddress = $HTTP_HOST;
   
   // DocRoot is the DocumentRoot of the server you're running DiscoBoard on.
   // It should pick up $DOCUMENT_ROOT, but if it doesn't you should set it 
   // here. Once again, it should not have a trailing slash.
   // Eg, /usr/local/apache/virtual/boards.yourdomain.com/htdocs
   $DocRoot = $DOCUMENT_ROOT;

   // BaseDir is the directory to which you've installed DiscoBoard. If it's in 
   // the DocumentRoot of the server you're running DiscoBoard on, you can leave
   // this blank. This should have a leading slash.
   // Eg, /forum
   $BaseDir = "/boards";
   
   // MySQL Configuration
   // -------------------
   
   // MySQLServer is the hostname of the MySQL server you've set up the
   // database for DiscoBoard on. If you need to connect on a specific port
   // number, you should specify it here. If you're not sure what value to 
   // put here, you should consult your system administrator or hostmaster.
   // Eg, mysql.yourdomain.com or mysql.yourdomain.com:3188
   $MySQLServer = "localhost";
   
   // MySQLUsername and MySQLPassword are used for authentication of your
   // connection to the MySQL database server. If you're not sure what value 
   // to put here, you should consult your system administrator or hostmaster.
   $MySQLUsername = "";
   $MySQLPassword = "";
   
   // MySQLDatabase is the name of the database which has been set up to hold
   // the DiscoBoard data. If you're not sure what value to put here, you 
   // should consult your system administrator or hostmaster.
   // Eg, boards
   $MySQLDatabase = "";
   
   // DatabasePrefix is a prefix added to the front of all DiscoBoard table names 
   // within the system's database - when in use, a table named "name" will be referred 
   // to as "prefix_name" instead. This allows DiscoBoard to coexist happily with other 
   // tables sharing its database. If you want to rename all the tables, you will need
   // to use "rename-tables.sql" in the support/ directory of the distribution. You
   // may need to edit the SQL before running it.
   $DatabasePrefix = "disco";

   // Session Settings
   // ----------------
   
   // SessionTime is the default length of time (in minutes) a login session 
   // will last for. It should just be an integer value.
   // Eg, 60
   $SessionTime = 120;
   
   // MultipleLogins defines whether or not a user is allowed to log into the
   // system (start a session) while a current, not-expired session exists in
   // the session table. Should just be an integer value, 0 or no or 1 for yes.
   $MultipleLogins = 1;
   
   // User Restriction Settings
   // -------------------------
   
   // GraceTime is the number of minutes a user is allowed to edit their posts
   // for after the post is first created. Usually this is 90 minutes.
   // Eg, 90
   $GraceTime = 90;
   
   // ShowThreadsPerPage and ShowPostsPerPage control the number of threads and
   // posts shown on a page in the board view and thread view respectively (the
   // board view shows threads, the thread view shows posts). These are the
   // default settings that apply to all users - members can set their own
   // options which will take effect after they log in. Should just be an
   // integer value
   // Eg, 20
   $ShowThreadsPerPage = 25;
   $ShowPostsPerPage = 25;
   
   // ProfanityFilter is a case INsensitive group of replacement texts. The
   // first element of the array is replaced with the second when the post
   // is viewed.
   // Set ProfanityFilterActive to 0 for it to be off or 1 for it to be on.
   $ProfanityFilterActive = 0;
   $ProfanityFilter[] = array("fuck", 'f@%&');
   $ProfanityFilter[] = array("cunt", 'c&!$');
   $ProfanityFilter[] = array("shit", 's#!%');
   $ProfanityFilter[] = array("bitch", 'b%$#@');
   $ProfanityFilter[] = array("bastard", 'b$#@!*%');
   $ProfanityFilter[] = array("ass", '@$$');

   // Threshold limits a user to posting X amount of items in Y seconds. Note
   // there's 3600 seconds in an hour, 86400 in a day, 604800 in a week. Set
   // a limit to 0 to apply no limit.
   $Threshold['post'] = array("limit" => 0, "time" => 86400);
   $Threshold['poll'] = array("limit" => 0, "time" => 604800);
   
   // UseOwnIcons is a boolean flag which tells DiscoBoard whether or not you want users to be able
   // to link to their own icons via an external URL. Bear in mind that loading icons from a remote
   // server may get you into trouble, and could cause problems if the remote server is down. This
   // should be set to 0 for off, and 1 for on.
   // If it is set to 0 people who can use the name format will still be able to use their own icons.
   // Eg, 1
   $UseOwnIcons = 0;

   // EditOwnTitle is a variable that you can edit to choose whether or not users can edit their own 
   // titles.  If you set the variable to 0 they won't be able to if you set it to 1 they will be 
   // able to.  If you set it to 0 anyone who can use Name Formatting will still have access to it.
   $EditOwnTitle = 0;

   // AllowSigMarkup will either allow or disallow markup codes to be used in signatures.
   $AllowSigMarkup = 1;

   // AllowShop will either turn on or off the shop on your boards
   // Set it to 1 for the shop to be on and 0 for it to be off.
   $AllowShop = "1";

   // AllowFileViewer is a variable that will control if File Edit / View can be 
   // accessed.  File Edit / View lets an Administrator view / edit files in his/her
   // DiscoBoard directory.  Setting AllowFileViewer to 0 will make it not
   // accessible (not even by admins), and setting it to 1 will make it
   // accessible to admins only.  If you have FTP access to this I suggest
   // setting it to 0 because if you get hacked it is a security risk because
   // the hacker could just view this file and find out your MySQL Database
   // password and delete all of your data.  And also the hacker would know
   // a password you use.
   $AllowFileViewer = 1;

   // Cosmetic Settings
   // -----------------
   
   // A ThemeName settings is the name of the theme.[ThemeName].php file that 
   // DiscoBoard will use for its page layout. This basically controls the top 
   // and bottom (and sides if you get fancy with your tables) of the page, into 
   // which the output from DiscoBoard is placed. Actual configuration of the 
   // output from DiscoBoard is not done here.
   // Eg, disco
   $ThemeName = "gcshideout";
   
   // SiteCode should be a short canonical name or abbreviation for your 
   // site. It should only be a few letters long to avoid taking up screen
   // space
   // Eg, SN, PCW, MLBIT
   $SiteCode = "GCs Hideout";

   // SiteName should be the full name you use for your site. This is mainly
   // used in communications with users (emails, etc)
   // Eg, ShadowNet
   $SiteName = "GC Boards";
   
   // SiteURL should be the full URL of your website
   // Eg, http://www.yourdomainname.com/path/to/your/site/
   $SiteURL = "http://gcshideout.com";
   
   // DiscoBoardName should be the full name you want to give to your
   // DiscoBoard installation
   // Eg, "DiscoBoard"
   $DiscoBoardName = "GC Boards";

   // Allow quick reply will turn the quick reply feature on or off.  Set it to 1 for on
   // or 0 for off.
   $AllowQuickReply = 1;

   // EmailAddress is your main email address, that hack attempts will be sent to.
   $EmailAddress = "gc@gcshideout.com";

   // DBVersion is just the version of discoboard you are running.  Most people
   // add one to the DBVersion every time they add something new or fix something.
   // Eg, 1.0, 1.1, 1.2, 1.3, 2.0, etc...
   $DBVersion = "2.1";
   
   // DiscoBoard awards stars to users based on the number of posts they've
   // made. The levels at which the stars are awarded can be set by you. There
   // are 10 levels. This should just be an integer value.
   // Eg, 10
   $PostLevel[0]  = 0; // There Is No Star For This Level
   $PostLevel[1]  = 50;
   $PostLevel[2]  = 250;
   $PostLevel[3]  = 500;
   $PostLevel[4]  = 1000;
   $PostLevel[5]  = 5000;
   $PostLevel[6]  = 10000;
   $PostLevel[7]  = 20000;
   $PostLevel[8]  = 30000;
   $PostLevel[9]  = 40000;
   $PostLevel[10] = 50000;
   
   // HiddenClasses are class names that are not displayed below a user's
   // name in a message display mode. This is a comma-separated list.
   // Eg: Admin,Standard,Drunkard
   $HiddenClasses = "Administrator,Manager,Moderator,VIP,Insider,Member,Banned,Locked Out";
   
   // OpenAllGroupsSetting tells DiscoBoard to show all boards in all groups
   // by default. If you've only got a small number of boards, you might want
   // to set this to "all" so that users don't have to click on a group name
   // to see the boards contained in it. Should either be "all" or "".
   // Eg, all
   $OpenAllGroupsSetting = "all";

   // TimeZoneChange is the number of hours + or - from the server's default
   // that you want DiscoBoard to adjust all the times by. In future this
   // setting may become user-specific. You may need to experiment a bit in
   // order to set this value, it depends where your host is located.
   // Eg: +5, 3, -1
   $TimeZoneChange = -2;
   
   // Emoticon substitution is defined as arrays below. Each element in the array
   // defines one emoticon substitution rule and holds three important elements:
   // 1. The general name of the face used in [face_X] text substitution, 2. The
   // shortcut :) face used in text substitution and 3. The actual filename of
   // the related image, relative to the gfx/faces subdirectory.

   $EmoticonSet[] = array("name" => "angel",     "code" => "O:)",  "filename" => "face_angel.gif");
   $EmoticonSet[] = array("name" => "angry",     "code" => "x-(",  "filename" => "face_angry.gif");
   $EmoticonSet[] = array("name" => "applause",  "code" => "=D=",  "filename" => "face_applause.gif");
   $EmoticonSet[] = array("name" => "batting",   "code" => ";;)",  "filename" => "face_batting.gif");
   $EmoticonSet[] = array("name" => "beatup",    "code" => "b-(",  "filename" => "face_beatup.gif");
   $EmoticonSet[] = array("name" => "blush",     "code" => ":8}",  "filename" => "face_blush.gif");
   $EmoticonSet[] = array("name" => "clown",     "code" => ":o)",  "filename" => "face_clown.gif");
   $EmoticonSet[] = array("name" => "cool",      "code" => "B-)",  "filename" => "face_cool.gif");
   $EmoticonSet[] = array("name" => "confused",  "code" => "?:|",  "filename" => "face_confused.gif");
   $EmoticonSet[] = array("name" => "cowboy",    "code" => "]):)", "filename" => "face_cowboy.gif");
   $EmoticonSet[] = array("name" => "cry",       "code" => ":_|",  "filename" => "face_cry.gif");
   $EmoticonSet[] = array("name" => "dancing",   "code" => "/:D/", "filename" => "face_dancing.gif");
   $EmoticonSet[] = array("name" => "devil",     "code" => "]:)",  "filename" => "face_devil.gif");
   $EmoticonSet[] = array("name" => "doh",       "code" => "#-o",  "filename" => "face_doh.gif");
   $EmoticonSet[] = array("name" => "drooling",  "code" => "=P~",  "filename" => "face_drooling.gif");
   $EmoticonSet[] = array("name" => "grin",      "code" => ":D",   "filename" => "face_grin.gif");
   $EmoticonSet[] = array("name" => "happy",     "code" => ":)",   "filename" => "face_happy.gif");
   $EmoticonSet[] = array("name" => "hugs",      "code" => "[:D]", "filename" => "face_hugs.gif");
   $EmoticonSet[] = array("name" => "hypnotized","code" => "@-)",  "filename" => "face_hypnotized.gif");
   $EmoticonSet[] = array("name" => "kiss",      "code" => ":*",   "filename" => "face_kiss.gif");
   $EmoticonSet[] = array("name" => "laugh",     "code" => ":^O",  "filename" => "face_laugh.gif");
   $EmoticonSet[] = array("name" => "liarliar",  "code" => ":^o",  "filename" => "face_liarliar.gif");
   $EmoticonSet[] = array("name" => "love",      "code" => ":x",   "filename" => "face_love.gif");
   $EmoticonSet[] = array("name" => "mischief",  "code" => ";/",   "filename" => "face_mischief.gif");
   $EmoticonSet[] = array("name" => "money_eyes","code" => "$-)",  "filename" => "face_money_eyes.gif");
   $EmoticonSet[] = array("name" => "nerd",      "code" => ":-B",  "filename" => "face_nerd.gif");
   $EmoticonSet[] = array("name" => "not_talking","code" => "[-(", "filename" => "face_not_talking.gif");
   $EmoticonSet[] = array("name" => "peace",     "code" => "=}=",  "filename" => "face_peace.gif");
   $EmoticonSet[] = array("name" => "plain",     "code" => ":|",   "filename" => "face_plain.gif");
   $EmoticonSet[] = array("name" => "praying",   "code" => "[-o|", "filename" => "face_praying.gif");
   $EmoticonSet[] = array("name" => "raised_brow","code" => "/:)", "filename" => "face_raised_brow.gif");
   $EmoticonSet[] = array("name" => "rolling_eyes","code" => "8-|","filename" => "face_rolling_eyes.gif");
   $EmoticonSet[] = array("name" => "sad",       "code" => ":(",   "filename" => "face_sad.gif");
   $EmoticonSet[] = array("name" => "shame_on_you","code" => "[-X","filename" => "face_shame_on_you.gif");
   $EmoticonSet[] = array("name" => "shhh",      "code" => ":-$",  "filename" => "face_shhh.gif");
   $EmoticonSet[] = array("name" => "shocked",   "code" => ":O",   "filename" => "face_shocked.gif");
   $EmoticonSet[] = array("name" => "sick",      "code" => ":-8",  "filename" => "face_sick.gif");
   $EmoticonSet[] = array("name" => "silly",     "code" => ":p",   "filename" => "face_silly.gif");
   $EmoticonSet[] = array("name" => "skull",     "code" => "8-X",  "filename" => "face_skull.gif");
   $EmoticonSet[] = array("name" => "sleep",     "code" => "I-)",  "filename" => "face_sleep.gif");
   $EmoticonSet[] = array("name" => "talk_hand", "code" => "=;",   "filename" => "face_talk_hand.gif");
   $EmoticonSet[] = array("name" => "thinking",  "code" => ":-?",  "filename" => "face_thinking.gif");
   $EmoticonSet[] = array("name" => "tired",     "code" => "(:|",  "filename" => "face_tired.gif");
   $EmoticonSet[] = array("name" => "whistling", "code" => ":-oo", "filename" => "face_whistling.gif");
   $EmoticonSet[] = array("name" => "wink",      "code" => ";)",   "filename" => "face_wink.gif");
   $EmoticonSet[] = array("name" => "worried",   "code" => ":-s",  "filename" => "face_worried.gif");


   // MenuBarOptions defines an array of options you want added to your menu bar. These options
   // go after the standard ones on the Logged-in-as or Login/Register line. You can define a
   // link name, destination URL and hex #RRGGBB colour code for it
   // If your going to use this remove the 2 slashes at the start of the next 3 lines below:
//   $MenuBarOptions[] = array("name"   => "User FAQ", 
//                             "url"    => "board.php?boardid=1",
//                             "colour" => "white");
   
   // ShowLastPosterName tells the system whether or not it should output the username (and
   // format it correctly) of the last poster in a thread when looking at a board. This should
   // be set to 0 for no, and 1 for yes
   $ShowLastPosterName = 1;
?>