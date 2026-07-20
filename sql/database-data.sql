# phpMyAdmin MySQL-Dump
# version 2.3.0

# http://phpwizard.net/phpMyAdmin/

# http://www.phpmyadmin.net/ (download page)

#

# Host: localhost

# Generation Time: Mar 10, 2003 at 02:13 PM

# Server version: 3.23.24

# PHP Version: 4.0.3pl1

# Database : `boards`


#

# Dumping data for table `disco_access_class`

#


INSERT INTO disco_access_class VALUES (1, 'Administrator', 'a:11:{s:8:"hlcolour";s:0:"";s:9:"txtcolour";s:7:"#000000";s:9:"stylebold";s:1:"1";s:11:"styleitalic";N;s:14:"styleunderline";N;s:13:"styleoverline";N;s:12:"bottomborder";s:1:"1";s:9:"topborder";s:1:"1";s:10:"leftborder";N;s:11:"rightborder";N;s:12:"bordercolour";s:0:"";}');

INSERT INTO disco_access_class VALUES (2, 'Manager', 'a:11:{s:8:"hlcolour";s:0:"";s:9:"txtcolour";s:7:"#000000";s:9:"stylebold";s:1:"1";s:11:"styleitalic";N;s:14:"styleunderline";N;s:13:"styleoverline";N;s:12:"bottomborder";s:1:"1";s:9:"topborder";s:1:"1";s:10:"leftborder";N;s:11:"rightborder";N;s:12:"bordercolour";s:0:"";}');

INSERT INTO disco_access_class VALUES (3, 'Moderator', 'a:11:{s:8:"hlcolour";s:7:"#F5F5F5";s:9:"txtcolour";s:7:"#A52A2A";s:9:"stylebold";N;s:11:"styleitalic";N;s:14:"styleunderline";N;s:13:"styleoverline";N;s:12:"bottomborder";N;s:9:"topborder";N;s:10:"leftborder";N;s:11:"rightborder";N;s:12:"bordercolour";s:0:"";}');

INSERT INTO disco_access_class VALUES (4, 'VIP', 'a:11:{s:8:"hlcolour";s:0:"";s:9:"txtcolour";s:7:"#FF0000";s:9:"stylebold";s:1:"1";s:11:"styleitalic";N;s:14:"styleunderline";N;s:13:"styleoverline";N;s:12:"bottomborder";N;s:9:"topborder";N;s:10:"leftborder";N;s:11:"rightborder";N;s:12:"bordercolour";s:0:"";}');

INSERT INTO disco_access_class VALUES (5, 'Insider', 'a:11:{s:8:"hlcolour";s:0:"";s:9:"txtcolour";s:7:"#FF0000";s:9:"stylebold";s:1:"1";s:11:"styleitalic";N;s:14:"styleunderline";N;s:13:"styleoverline";N;s:12:"bottomborder";N;s:9:"topborder";N;s:10:"leftborder";N;s:11:"rightborder";N;s:12:"bordercolour";s:0:"";}');

INSERT INTO disco_access_class VALUES (6, 'Member',  NULL);

INSERT INTO disco_access_class VALUES (7, 'Banned', 'a:11:{s:8:"hlcolour";s:7:"#808080";s:9:"txtcolour";s:7:"#000000";s:9:"stylebold";N;s:11:"styleitalic";N;s:14:"styleunderline";N;s:13:"styleoverline";N;s:12:"bottomborder";N;s:9:"topborder";N;s:10:"leftborder";N;s:11:"rightborder";N;s:12:"bordercolour";s:0:"";}');

INSERT INTO disco_access_class VALUES (8, 'Locked Out', 'a:11:{s:8:"hlcolour";s:7:"#808080";s:9:"txtcolour";s:7:"#000000";s:9:"stylebold";N;s:11:"styleitalic";N;s:14:"styleunderline";N;s:13:"styleoverline";N;s:12:"bottomborder";N;s:9:"topborder";N;s:10:"leftborder";N;s:11:"rightborder";N;s:12:"bordercolour";s:0:"";}');


#

# Dumping data for table `disco_class_permission`

#


INSERT INTO disco_class_permission (ID, classid, accessadmin, accessmanager, accessmoderator, accessvip, accessinsider, accessread, accesswrite, accessdelete, accesstimeedit, accessfulledit, accessnameformat, accesslogin, boardid) VALUES (2, 1, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 0);

INSERT INTO disco_class_permission (ID, classid, accessadmin, accessmanager, accessmoderator, accessvip, accessinsider, accessread, accesswrite, accessdelete, accesstimeedit, accessfulledit, accessnameformat, accesslogin, boardid) VALUES (3, 2, '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 0);

INSERT INTO disco_class_permission (ID, classid, accessadmin, accessmanager, accessmoderator, accessvip, accessinsider, accessread, accesswrite, accessdelete, accesstimeedit, accessfulledit, accessnameformat, accesslogin, boardid) VALUES (4, 3, '0', '0', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', 0);

INSERT INTO disco_class_permission (ID, classid, accessadmin, accessmanager, accessmoderator, accessvip, accessinsider, accessread, accesswrite, accessdelete, accesstimeedit, accessfulledit, accessnameformat, accesslogin, boardid) VALUES (5, 4, '0', '0', '0', '1', '1', '1', '1', '0', '0', '0', '1', '1', 0);

INSERT INTO disco_class_permission (ID, classid, accessadmin, accessmanager, accessmoderator, accessvip, accessinsider, accessread, accesswrite, accessdelete, accesstimeedit, accessfulledit, accessnameformat, accesslogin, boardid) VALUES (6, 5, '0', '0', '0', '0', '1', '1', '1', '0', '1', '0', '1', '1', 0);

INSERT INTO disco_class_permission (ID, classid, accessadmin, accessmanager, accessmoderator, accessvip, accessinsider, accessread, accesswrite, accessdelete, accesstimeedit, accessfulledit, accessnameformat, accesslogin, boardid) VALUES (7, 6, '0', '0', '0', '0', '0', '1', '1', '0', '1', '0',  NULL, '1', 0);

INSERT INTO disco_class_permission (ID, classid, accessadmin, accessmanager, accessmoderator, accessvip, accessinsider, accessread, accesswrite, accessdelete, accesstimeedit, accessfulledit, accessnameformat, accesslogin, boardid) VALUES (8, 7, '0', '0', '0', '0', '0', '1', '0', '0', '0', '0', '0', '0', 0);

INSERT INTO disco_class_permission (ID, classid, accessadmin, accessmanager, accessmoderator, accessvip, accessinsider, accessread, accesswrite, accessdelete, accesstimeedit, accessfulledit, accessnameformat, accesslogin, boardid) VALUES (9, 8, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 0);


#

# Dumping data for table `disco_country_codes`

#


INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (1, 'AF', 'Afghanistan');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (2, 'AL', 'Albania');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (3, 'DZ', 'Algeria');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (4, 'AS', 'American Samoa');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (5, 'AD', 'Andorra');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (6, 'AO', 'Angola');

INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (7, 'AI', 'Anguilla');

INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (8, 'AQ', 'Antarctica');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (9, 'AG', 'Antigua and Barbuda');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (10, 'AR', 'Argentina');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (11, 'AM', 'Armenia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (12, 'AW', 'Aruba');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (14, 'AU', 'Australia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (15, 'AT', 'Austria');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (16, 'AZ', 'Azerbaijan');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (17, 'BS', 'Bahamas');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (18, 'BH', 'Bahrain');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (19, 'BD', 'Banglasdesh');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (20, 'BB', 'Barbados');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (21, 'BY', 'Belarus');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (22, 'BE', 'Belgium');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (23, 'BZ', 'Belize');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (24, 'BJ', 'Benin');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (25, 'BM', 'Bermuda');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (26, 'BT', 'Bhutan');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (27, 'BO', 'Bolivia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (28, 'BA', 'Bosnia and Herzegovina');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (29, 'BW', 'Botswana');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (30, 'BV', 'Bouvet Island');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (31, 'BR', 'Brazil');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (32, 'IO', 'British Indian Ocean Territory');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (33, 'BN', 'Brunei Darussalam');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (34, 'BG', 'Bulgaria');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (35, 'BF', 'Burkina Faso');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (36, 'BI', 'Burundi');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (37, 'KH', 'Cambodia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (38, 'CM', 'Cameroon');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (39, 'CA', 'Canada');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (40, 'CV', 'Cape Verde');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (41, 'KY', 'Cayman Islands');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (42, 'CF', 'Central African Republic');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (43, 'TD', 'Chad');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (44, 'CL', 'Chile');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (45, 'CN', 'China');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (46, 'CX', 'Christmas Island');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (47, 'CC', 'Cocos (Keeling) Islands');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (48, 'CO', 'Colombia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (49, 'KM', 'Comoros');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (50, 'CG', 'Congo');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (51, 'CD', 'Congo, Democratic Republic');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (52, 'CK', 'Cook Islands');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (53, 'CR', 'Costa Rica');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (54, 'CI', 'Cote Divoire');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (55, 'HR', 'Croatia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (56, 'CU', 'Cuba');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (57, 'CY', 'Cyprus');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (58, 'CZ', 'Czech Republic');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (59, 'DK', 'Denmark');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (60, 'DJ', 'Djibouti');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (61, 'DM', 'Dominica');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (62, 'DO', 'Dominican Republic');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (63, 'TP', 'East Timor');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (64, 'EC', 'Ecuador');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (65, 'EG', 'Egypt');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (66, 'SV', 'El Salvador');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (67, 'GQ', 'Equatorial Guinea');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (68, 'ER', 'Eritrea');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (69, 'EE', 'Estonia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (70, 'ET', 'Ethiopia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (71, 'FK', 'Falkland Islands (Malvinas)');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (72, 'FO', 'Faroe Islands');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (73, 'FJ', 'Fiji');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (74, 'FI', 'Finland');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (75, 'FR', 'France');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (76, 'GF', 'French Guiana');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (77, 'PF', 'French Polynesia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (78, 'TF', 'French Southern Territories');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (79, 'GA', 'Gabon');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (80, 'GM', 'Gambia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (81, 'GE', 'Georgia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (82, 'DE', 'Germany');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (83, 'GH', 'Ghana');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (84, 'GI', 'Gibraltar');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (85, 'GR', 'Greece');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (86, 'GL', 'Greenland');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (87, 'GD', 'Grenada');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (88, 'GP', 'Guadeloupe');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (89, 'GU', 'Guam');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (90, 'GT', 'Guatemala');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (91, 'GN', 'Guinea');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (92, 'GW', 'Guinea-Bissau');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (93, 'GY', 'Guyana');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (94, 'HT', 'Haiti');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (95, 'HM', 'Heard / McDonald Isl.');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (96, 'VA', 'Holy See (Vatican City State)');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (97, 'HN', 'Honduras');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (98, 'HK', 'Hong Kong');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (99, 'HU', 'Hungary');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (100, 'IS', 'Iceland');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (101, 'IN', 'India');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (102, 'ID', 'Indonesia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (103, 'IR', 'Iran, Islamic Republic of');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (104, 'IQ', 'Iraq');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (105, 'IE', 'Ireland');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (106, 'IL', 'Israel');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (108, 'IT', 'Italy');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (109, 'JM', 'Jamaica');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (110, 'JP', 'Japan');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (111, 'JO', 'Jordan');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (112, 'KZ', 'Kazakstan');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (113, 'KE', 'Kenya');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (114, 'KI', 'Kiribati');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (115, 'KP', 'Korea, Dem. Peoples Republic');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (116, 'KR', 'Korea, Republic of');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (117, 'KW', 'Kuwait');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (118, 'KG', 'Kyrgyzstan');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (119, 'LA', 'Lao Peoples Dem. Republic');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (120, 'LV', 'Latvia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (121, 'LB', 'Lebanon');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (122, 'LS', 'Lesotho');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (123, 'LR', 'Liberia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (124, 'LY', 'Libyan Arab Jamahiriya');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (125, 'LI', 'Liechtenstein');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (126, 'LT', 'Lithuania');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (127, 'LU', 'Luxembourg');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (128, 'MO', 'Macau');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (129, 'MK', 'Macedonia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (130, 'MG', 'Madagascar');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (131, 'MW', 'Malawi');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (132, 'MY', 'Malaysia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (133, 'MV', 'Maldives');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (134, 'ML', 'Mali');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (135, 'MT', 'Malta');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (136, 'MH', 'Marshall Islands');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (137, 'MQ', 'Martinique');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (138, 'MR', 'Mauritania');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (139, 'MU', 'Mauritius');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (140, 'YT', 'Mayotte');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (141, 'MX', 'Mexico');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (142, 'FM', 'Micronesia, Federated States');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (143, 'MD', 'Moldova, Republic of');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (144, 'MC', 'Monaco');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (145, 'MN', 'Mongolia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (146, 'MS', 'Montserrat');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (147, 'MA', 'Morocco');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (148, 'MZ', 'Mozambique');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (149, 'MM', 'Myanmar');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (150, 'NA', 'Namibia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (151, 'NR', 'Nauru');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (152, 'NP', 'Nepal');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (153, 'NL', 'Netherlands');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (154, 'AN', 'Netherlands Antilles');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (155, 'NC', 'New Caledonia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (156, 'NZ', 'New Zealand');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (157, 'NI', 'Nicaragua');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (158, 'NE', 'Niger');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (159, 'NG', 'Nigeria');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (160, 'NU', 'Niue');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (161, 'NF', 'Norfolk Island');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (162, 'MP', 'Northern Mariana Islands');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (163, 'NO', 'Norway');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (164, 'OM', 'Oman');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (165, 'PK', 'Pakistan');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (166, 'PW', 'Palau');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (168, 'PA', 'Panama');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (169, 'PG', 'Papua New Guinea');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (170, 'PY', 'Paraguay');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (171, 'PE', 'Peru');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (172, 'PH', 'Philippines');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (173, 'PN', 'Pitcairn');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (174, 'PL', 'Poland');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (175, 'PT', 'Portugal');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (176, 'PR', 'Puerto Rico');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (177, 'QA', 'Qatar');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (178, 'RE', 'Reunion');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (179, 'RO', 'Romania');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (180, 'RU', 'Russian Federation');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (181, 'RW', 'Rwanda');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (182, 'SH', 'Saint Helena');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (183, 'KN', 'Saint Kitts and Nevia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (184, 'LC', 'Saint Lucia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (185, 'PM', 'Saint Pierre and Miquelon');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (186, 'VC', 'Saint Vincent / Grenadines');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (187, 'WS', 'Samoa');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (188, 'SM', 'San Marino');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (189, 'ST', 'Sao Tome and Principe');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (190, 'SA', 'Saudi Arabia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (191, 'SN', 'Senegal');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (192, 'SC', 'Seychelles');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (193, 'SL', 'Sierra Leone');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (194, 'SG', 'Singapore');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (195, 'SK', 'Slovakia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (196, 'SI', 'Slovenia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (197, 'SB', 'Solomon Islands');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (198, 'SO', 'Somalia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (199, 'ZA', 'South Africa');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (200, 'GS', 'South Georgia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (201, 'ES', 'Spain');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (202, 'LK', 'Sri Lanka');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (203, 'SD', 'Sudan');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (204, 'SR', 'Suriname');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (205, 'SJ', 'Svalbard and Jan Mayen');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (206, 'SZ', 'Swaziland');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (207, 'SE', 'Sweden');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (208, 'CH', 'Switzerland');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (209, 'SY', 'Syrian Arab Republic');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (210, 'TW', 'Taiwan, Province of China');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (211, 'TJ', 'Tajikistan');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (212, 'TZ', 'Tanzania, United Republic');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (213, 'TH', 'Thailand');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (214, 'TG', 'Togo');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (215, 'TK', 'Tokelau');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (216, 'TO', 'Tonga');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (217, 'TT', 'Trinidad and Tobago');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (218, 'TN', 'Tunisia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (219, 'TR', 'Turkey');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (220, 'TM', 'Turkmenistan');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (221, 'TC', 'Turks and Caicos Islands');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (222, 'TV', 'Tuvalu');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (223, 'UG', 'Uganda');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (224, 'UA', 'Ukraine');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (225, 'AE', 'United Arab Emirates');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (226, 'GB', 'United Kingdom');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (227, 'US', 'United States of America');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (228, 'UM', 'Unites States, Outlying Isl.');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (229, 'UY', 'Uruguay');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (230, 'UZ', 'Uzbekistan');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (231, 'VU', 'Vanuatu');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (232, 'VE', 'Venezuela');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (233, 'VN', 'Vietnam');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (234, 'VG', 'Virgin Islands, British');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (235, 'VI', 'Virgin Islands, U.S.');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (236, 'WF', 'Wallis and Futuna');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (237, 'EH', 'Western Sahara');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (238, 'YE', 'Yemen');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (239, 'YU', 'Yugoslavia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (240, 'ZM', 'Zambia');
INSERT INTO disco_country_codes (ID, abbrev, descr) VALUES (241, 'ZW', 'Zimbabwe');
