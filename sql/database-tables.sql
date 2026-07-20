#
# Table structure for table `disco_access_class`
#

CREATE TABLE disco_access_class (
  ID int(11) default NULL auto_increment,
  classname text,
  nameformat text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_board_sessions`
#

CREATE TABLE disco_board_sessions (
  ID int(11) default NULL auto_increment,
  username text NOT NULL,
  password text,
  magickey text NOT NULL,
  issuetime int(11) default NULL,
  expirytime int(11) default NULL,
  usertype text,
  session_minutes int(11) default NULL,
  lastactivity int(11) NOT NULL default '0',
  login_ip varchar(15) NOT NULL default '               ',
  PRIMARY KEY (ID)
) TYPE=ISAM PACK_KEYS=1;
# --------------------------------------------------------

#
# Table structure for table `disco_boards`
#

CREATE TABLE disco_boards (
  ID int(11) default NULL auto_increment,
  boardname text,
  description text,
  boardrank char(1) NOT NULL default 'z',
  groupid int(11) default NULL,
  postidlast int(11) default NULL,
  private int(11) NOT NULL default '0',
  vippost int(11) NOT NULL default '0',
  PRIMARY KEY (ID),
  KEY groupid(groupid)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_class_permission`
#

CREATE TABLE disco_class_permission (
  ID int(11) default NULL auto_increment,
  classid int(11) default NULL,
  accessadmin tinyint(4) default NULL,
  accessmanager tinyint(4) default NULL,
  accessmoderator tinyint(4) default NULL,
  accessvip tinyint(4) default NULL,
  accessinsider tinyint(4) default NULL,
  accessread tinyint(4) default NULL,
  accesswrite tinyint(4) default NULL,
  accessdelete tinyint(4) default NULL,
  accesstimeedit tinyint(4) default NULL,
  accessfulledit tinyint(4) default NULL,
  accessnameformat tinyint(4) default NULL,
  accesslogin tinyint(4) NOT NULL default '1',
  boardid int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_country_codes`
#

CREATE TABLE disco_country_codes (
  ID int(11) default NULL auto_increment,
  abbrev char(2) default NULL,
  descr text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_favourites`
#

CREATE TABLE disco_favourites (
  ID int(11) default NULL auto_increment,
  ownerid int(11) default NULL,
  boardid int(11) default NULL,
  userid int(11) default NULL,
  threadid int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_groups`
#

CREATE TABLE disco_groups (
  ID int(11) default NULL auto_increment,
  themeid int(11) default NULL,
  groupname text,
  grouprank char(1) NOT NULL default 'z',
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_icon_groups`
#

CREATE TABLE disco_icon_groups (
  ID int(11) default NULL auto_increment,
  groupname text,
  classid int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_icons`
#

CREATE TABLE disco_icons (
  ID int(11) default NULL auto_increment,
  groupid int(11) default NULL,
  iconname text,
  filename text,
  lastmod timestamp(14) default NULL,
  data blob default NULL,
  mimetype varchar(40) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_poll_options`
#

CREATE TABLE disco_poll_options (
  ID int(11) default NULL auto_increment,
  pollid int(11) default NULL,
  optionname text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_poll_responses`
#

CREATE TABLE disco_poll_responses (
  ID int(11) default NULL auto_increment,
  pollid int(11) default NULL,
  userid int(11) default NULL,
  optionid int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_polls`
#

CREATE TABLE disco_polls (
  ID int(11) default NULL auto_increment,
  authorid int(11) default NULL,
  question text,
  expirydate int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_posts`
#

CREATE TABLE disco_posts (
  ID int(11) default NULL auto_increment,
  threadid int(11) default NULL,
  postdate int(11) default NULL,
  authorid int(11) default NULL,
  recipientid int(11) default NULL,
  subject text,
  body text,
  edituserid int(11) default NULL,
  editdate int(11) default NULL,
  editcount int(11) default NULL,
  pollid int(11) default NULL,
  status char(1) default NULL,
  ipaddress varchar(15) NOT NULL default '               ',
  PRIMARY KEY (ID),
  KEY status(status)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_recoveryverification`
#

CREATE TABLE disco_recoveryverification (
  ID int(11) default NULL auto_increment,
  userid int(11) default NULL,
  token text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_sticky_threads`
#

CREATE TABLE disco_sticky_threads (
  ID int(11) default NULL auto_increment,
  threadid int(11) default NULL,
  expirydate int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_system_ban`
#

CREATE TABLE disco_system_ban (
  ID int(11) default NULL auto_increment,
  ip_start int(11) NOT NULL default '0',
  ip_stop int(11) NOT NULL default '0',
  userid int(11) NOT NULL default '0',
  adminid int(11) NOT NULL default '0',
  active tinyint(4) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_theme`
#

CREATE TABLE disco_theme (
  ID int(11) default NULL auto_increment,
  themename text,
  bgcontrol text,
  bgheading text,
  bginfo text,
  bgbody text,
  bgoptions text,
  fgcontrol text,
  fgheading text,
  fginfo text,
  fgbody text,
  fgoptions text,
  fontmainname text,
  fontmainsize int(11) default NULL,
  fontsmallname text,
  fontsmallsize text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_threads`
#

CREATE TABLE disco_threads (
  ID int(11) default NULL auto_increment,
  boardid int(11) default NULL,
  status char(1) default NULL,
  postcount int(11) NOT NULL default '0',
  viewcount int(11) NOT NULL default '0',
  postidfirst int(11) default NULL,
  postidlast int(11) default NULL,
  sticky tinyint(4) default NULL,
  oldboardid int(11) default NULL,
  PRIMARY KEY (ID),
  KEY boardid(boardid)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_user_notes`
#

CREATE TABLE disco_user_notes (
  ID int(11) default NULL auto_increment,
  userid int(11) NOT NULL default '0',
  authorid int(11) NOT NULL default '0',
  notedate int(11) NOT NULL default '0',
  note text NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `disco_users`
#

CREATE TABLE disco_users (
  ID int(11) default NULL auto_increment,
  classid int(11) default NULL,
  created int(11) default NULL,
  updated int(11) default NULL,
  lastlogin int(11) default NULL,
  displayname varchar(40) default NULL,
  displayformat text,
  encpassword text,
  iconid int(11) default NULL,
  ownicon varchar(120) NOT NULL default '                                                                                                                        ',
  fname text,
  sname text,
  dateofbirth int(11) default NULL,
  gender char(1) default NULL,
  profiletitle text,
  company text,
  jobtitle text,
  country char(2) default NULL,
  picurl text,
  wwwurl text,
  contactemail text,
  contacticq int(11) default NULL,
  contactmsn text,
  contactaim text,
  contactyahoo text,
  bio text,
  perpageposts int(11) default NULL,
  perpagethreads int(11) default NULL,
  sig1 varchar(120) default NULL,
  sig2 varchar(120) default NULL,
  sig3 varchar(120) default NULL,
  sig4 varchar(120) default NULL,
  sig5 varchar(120) default NULL,
  postcount int(11) NOT NULL default '0',
  timezone int(11) default NULL,
  ip_signup varchar(15) default NULL,
  title varchar(150) default NULL,
  stylesheet varchar(50) default NULL,
  starsystem int(11) NOT NULL default '1',
  dollars int(20) NOT NULL default '0',
  color int(10) NOT NULL default '0',
  shoptitle int(10) NOT NULL default '0',
  namechange int(10) NOT NULL default '0',
  chooseicon int(10) NOT NULL default '0',
  PRIMARY KEY (ID),
  UNIQUE KEY displayname(displayname),
  KEY displayname_2(displayname)
) TYPE=MyISAM;
