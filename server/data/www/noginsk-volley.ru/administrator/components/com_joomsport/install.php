<?php 
/*------------------------------------------------------------------------
# JoomSport Professional 
# ------------------------------------------------------------------------
# BearDev development company 
# Copyright (C) 2011 JoomSport.com. All Rights Reserved.
# @license - http://joomsport.com/news/license.html GNU/GPL
# Websites: http://www.JoomSport.com 
# Technical Support:  Forum - http://joomsport.com/helpdesk/
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
class com_joomsportInstallerScript{
 function postflight($type, $parent) 
        {
			$jBasePath = dirname(JPATH_BASE);
			$adminDir = dirname(__FILE__);

			@mkdir($jBasePath .DIRECTORY_SEPARATOR. "media".DIRECTORY_SEPARATOR."bearleague");

			@chmod($jBasePath .DIRECTORY_SEPARATOR. "media".DIRECTORY_SEPARATOR."bearleague", 0755);

			@mkdir($jBasePath .DIRECTORY_SEPARATOR. "media".DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."events");

			@chmod($jBasePath .DIRECTORY_SEPARATOR. "media".DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."events", 0755);

			

			@copy( $adminDir. DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."events".DIRECTORY_SEPARATOR."red_card.png", $jBasePath . DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."events".DIRECTORY_SEPARATOR."red_card.png");

			@copy( $adminDir. DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."events".DIRECTORY_SEPARATOR."yellow_card.png", $jBasePath . DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."events".DIRECTORY_SEPARATOR."yellow_card.png"); 

			@copy( $adminDir. DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."events".DIRECTORY_SEPARATOR."yellow-red_card.png", $jBasePath . DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."events".DIRECTORY_SEPARATOR."yellow-red_card.png");

			@copy( $adminDir. DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."events".DIRECTORY_SEPARATOR."boot.png", $jBasePath . DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."events".DIRECTORY_SEPARATOR."boot.png"); 

			@copy( $adminDir. DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."events".DIRECTORY_SEPARATOR."ball.png", $jBasePath . DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."events".DIRECTORY_SEPARATOR."ball.png"); 


			@copy( $adminDir. DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."player_st.png", $jBasePath . DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."player_st.png");

			@copy( $adminDir. DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."teams_st.png", $jBasePath . DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."bearleague".DIRECTORY_SEPARATOR."teams_st.png"); 
			
			
			$database = JFactory::getDBO();
			$query = "SELECT `extension_id` FROM #__extensions WHERE `element` = 'com_joomsport'";
			$database->setQuery( $query );
			$exid = $database->loadResult();
			
			$query = "UPDATE #__menu SET component_id = ".$exid." WHERE link LIKE 'index.php?option=com_joomsport%'";
			$database->setQuery( $query );
			$database->query();
			$query = "UPDATE #__extensions SET name='com_joomsport' WHERE `element` = 'com_joomsport'";
			$database->setQuery( $query );
			$database->query();
			
			$query = "SELECT cfg_value FROM `#__bl_config` WHERE cfg_name='date_format'";

			$database->setQuery($query);

			if(!$database->loadResult()){

				$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('date_format', '%d-%m-%Y %H:%M')");

				$database->query();

			}

			$query = "SELECT cfg_value FROM `#__bl_config` WHERE cfg_name='yteam_color'";

			$database->setQuery($query);

			if(!$database->loadResult()){

				$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('yteam_color', '#FFFFFF')");

				$database->query();

			}
			
			//--- add countries------//
			$query = "SELECT COUNT(*) FROM `#__bl_countries`";
			$database->setQuery($query);
			
			if(!$database->loadResult()){
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (1, 'AF', 'Afghanistan')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (2, 'AX', 'Aland Islands')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (3, 'AL', 'Albania')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (4, 'DZ', 'Algeria')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (5, 'AS', 'American Samoa')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (6, 'AD', 'Andorra')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (7, 'AO', 'Angola')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (8, 'AI', 'Anguilla')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (9, 'AQ', 'Antarctica')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (10, 'AG', 'Antigua and Barbuda')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (11, 'AR', 'Argentina')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (12, 'AM', 'Armenia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (13, 'AW', 'Aruba')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (14, 'AU', 'Australia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (15, 'AT', 'Austria')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (16, 'AZ', 'Azerbaijan')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (17, 'BS', 'Bahamas')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (18, 'BH', 'Bahrain')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (19, 'BD', 'Bangladesh')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (20, 'BB', 'Barbados')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (21, 'BY', 'Belarus')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (22, 'BE', 'Belgium')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (23, 'BZ', 'Belize')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (24, 'BJ', 'Benin')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (25, 'BM', 'Bermuda')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (26, 'BT', 'Bhutan')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (27, 'BO', 'Bolivia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (28, 'BA', 'Bosnia and Herzegovina')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (29, 'BW', 'Botswana')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (30, 'BV', 'Bouvet Island')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (31, 'BR', 'Brazil')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (32, 'IO', 'British Indian Ocean Territory')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (33, 'BN', 'Brunei Darussalam')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (34, 'BG', 'Bulgaria')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (35, 'BF', 'Burkina Faso')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (36, 'BI', 'Burundi')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (37, 'KH', 'Cambodia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (38, 'CM', 'Cameroon')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (39, 'CA', 'Canada')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (40, 'CV', 'Cape Verde')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (41, 'KY', 'Cayman Islands')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (42, 'CF', 'Central African Republic')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (43, 'TD', 'Chad')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (44, 'CL', 'Chile')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (45, 'CN', 'China')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (46, 'CX', 'Christmas Island')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (47, 'CC', 'Cocos (Keeling) Islands')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (48, 'CO', 'Colombia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (49, 'KM', 'Comoros')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (50, 'CG', 'Congo')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (51, 'CD', 'Congo, The Democratic Republic of the')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (52, 'CK', 'Cook Islands')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (53, 'CR', 'Costa Rica')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (54, 'CI', 'Cote D''Ivoire')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (55, 'HR', 'Croatia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (56, 'CU', 'Cuba')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (57, 'CY', 'Cyprus')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (58, 'CZ', 'Czech Republic')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (59, 'DK', 'Denmark')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (60, 'DJ', 'Djibouti')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (61, 'DM', 'Dominica')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (62, 'DO', 'Dominican Republic')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (63, 'EC', 'Ecuador')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (64, 'EG', 'Egypt')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (65, 'SV', 'El Salvador')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (66, 'GQ', 'Equatorial Guinea')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (67, 'ER', 'Eritrea')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (68, 'EE', 'Estonia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (69, 'ET', 'Ethiopia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (70, 'FK', 'Falkland Islands (Malvinas)')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (71, 'FO', 'Faroe Islands')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (72, 'FJ', 'Fiji')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (73, 'FI', 'Finland')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (74, 'FR', 'France')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (75, 'GF', 'French Guiana')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (76, 'PF', 'French Polynesia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (77, 'TF', 'French Southern Territories')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (78, 'GA', 'Gabon')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (79, 'GM', 'Gambia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (80, 'GE', 'Georgia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (81, 'DE', 'Germany')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (82, 'GH', 'Ghana')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (83, 'GI', 'Gibraltar')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (84, 'GR', 'Greece')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (85, 'GL', 'Greenland')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (86, 'GD', 'Grenada')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (87, 'GP', 'Guadeloupe')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (88, 'GU', 'Guam')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (89, 'GT', 'Guatemala')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (90, 'GG', 'Guernsey')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (91, 'GN', 'Guinea')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (92, 'GW', 'Guinea-Bissau')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (93, 'GY', 'Guyana')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (94, 'HT', 'Haiti')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (95, 'HM', 'Heard Island and McDonald Islands')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (96, 'VA', 'Holy See (Vatican City State)')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (97, 'HN', 'Honduras')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (98, 'HK', 'Hong Kong')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (99, 'HU', 'Hungary')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (100, 'IS', 'Iceland')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (101, 'IN', 'India')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (102, 'ID', 'Indonesia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (103, 'IR', 'Iran, Islamic Republic of')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (104, 'IQ', 'Iraq')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (105, 'IE', 'Ireland')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (107, 'IL', 'Israel')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (108, 'IT', 'Italy')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (109, 'JM', 'Jamaica')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (110, 'JP', 'Japan')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (111, 'JE', 'Jersey')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (112, 'JO', 'Jordan')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (113, 'KZ', 'Kazakhstan')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (114, 'KE', 'Kenya')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (115, 'KI', 'Kiribati')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (116, 'KP', 'Korea, Democratic People''s Republic of')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (117, 'KR', 'Korea, Republic of')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (118, 'KW', 'Kuwait')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (119, 'KG', 'Kyrgyzstan')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (120, 'LA', 'Lao People''s Democratic Republic')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (121, 'LV', 'Latvia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (122, 'LB', 'Lebanon')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (123, 'LS', 'Lesotho')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (124, 'LR', 'Liberia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (125, 'LY', 'Libyan Arab Jamahiriya')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (126, 'LI', 'Liechtenstein')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (127, 'LT', 'Lithuania')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (128, 'LU', 'Luxembourg')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (129, 'MO', 'Macao')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (130, 'MK', 'Macedonia, The Former Yugoslav Republic of')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (131, 'MG', 'Madagascar')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (132, 'MW', 'Malawi')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (133, 'MY', 'Malaysia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (134, 'MV', 'Maldives')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (135, 'ML', 'Mali')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (136, 'MT', 'Malta')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (137, 'MH', 'Marshall Islands')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (138, 'MQ', 'Martinique')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (139, 'MR', 'Mauritania')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (140, 'MU', 'Mauritius')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (141, 'YT', 'Mayotte')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (142, 'MX', 'Mexico')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (143, 'FM', 'Micronesia, Federated States of')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (144, 'MD', 'Moldova, Republic of')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (145, 'MC', 'Monaco')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (146, 'MN', 'Mongolia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (147, 'ME', 'Montenegro')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (148, 'MS', 'Montserrat')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (149, 'MA', 'Morocco')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (150, 'MZ', 'Mozambique')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (151, 'MM', 'Myanmar')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (152, 'NA', 'Namibia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (153, 'NR', 'Nauru')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (154, 'NP', 'Nepal')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (155, 'NL', 'Netherlands')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (156, 'AN', 'Netherlands Antilles')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (157, 'NC', 'New Caledonia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (158, 'NZ', 'New Zealand')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (159, 'NI', 'Nicaragua')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (160, 'NE', 'Niger')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (161, 'NG', 'Nigeria')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (162, 'NU', 'Niue')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (163, 'NF', 'Norfolk Island')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (164, 'MP', 'Northern Mariana Islands')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (165, 'NO', 'Norway')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (166, 'OM', 'Oman')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (167, 'PK', 'Pakistan')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (168, 'PW', 'Palau')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (169, 'PS', 'Palestinian Territory, Occupied')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (170, 'PA', 'Panama')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (171, 'PG', 'Papua New Guinea')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (172, 'PY', 'Paraguay')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (173, 'PE', 'Peru')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (174, 'PH', 'Philippines')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (175, 'PN', 'Pitcairn')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (176, 'PL', 'Poland')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (177, 'PT', 'Portugal')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (178, 'PR', 'Puerto Rico')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (179, 'QA', 'Qatar')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (180, 'RE', 'Reunion')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (181, 'RO', 'Romania')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (182, 'RU', 'Russian Federation')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (183, 'RW', 'Rwanda')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (185, 'SH', 'Saint Helena')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (186, 'KN', 'Saint Kitts and Nevis')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (187, 'LC', 'Saint Lucia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (188, 'MF', 'Saint Martin')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (189, 'PM', 'Saint Pierre and Miquelon')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (190, 'VC', 'Saint Vincent and the Grenadines')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (191, 'WS', 'Samoa')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (192, 'SM', 'San Marino')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (193, 'ST', 'Sao Tome and Principe')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (194, 'SA', 'Saudi Arabia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (195, 'SN', 'Senegal')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (196, 'RS', 'Serbia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (197, 'SC', 'Seychelles')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (198, 'SL', 'Sierra Leone')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (199, 'SG', 'Singapore')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (200, 'SK', 'Slovakia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (201, 'SI', 'Slovenia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (202, 'SB', 'Solomon Islands')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (203, 'SO', 'Somalia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (204, 'ZA', 'South Africa')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (205, 'GS', 'South Georgia and the South Sandwich Islands')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (206, 'ES', 'Spain')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (207, 'LK', 'Sri Lanka')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (208, 'SD', 'Sudan')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (209, 'SR', 'Suriname')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (210, 'SJ', 'Svalbard and Jan Mayen')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (211, 'SZ', 'Swaziland')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (212, 'SE', 'Sweden')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (213, 'CH', 'Switzerland')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (214, 'SY', 'Syrian Arab Republic')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (215, 'TW', 'Taiwan, Province Of China')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (216, 'TJ', 'Tajikistan')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (217, 'TZ', 'Tanzania, United Republic of')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (218, 'TH', 'Thailand')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (219, 'TL', 'Timor-Leste')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (220, 'TG', 'Togo')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (221, 'TK', 'Tokelau')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (222, 'TO', 'Tonga')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (223, 'TT', 'Trinidad and Tobago')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (224, 'TN', 'Tunisia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (225, 'TR', 'Turkey')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (226, 'TM', 'Turkmenistan')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (227, 'TC', 'Turks and Caicos Islands')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (228, 'TV', 'Tuvalu')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (229, 'UG', 'Uganda')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (230, 'UA', 'Ukraine')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (231, 'AE', 'United Arab Emirates')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (232, 'GB', 'United Kingdom')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (233, 'US', 'United States')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (234, 'UM', 'United States Minor Outlying Islands')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (235, 'UY', 'Uruguay')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (236, 'UZ', 'Uzbekistan')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (237, 'VU', 'Vanuatu')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (238, 'VE', 'Venezuela')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (239, 'VN', 'Viet Nam')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (240, 'VG', 'Virgin Islands, British')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (241, 'VI', 'Virgin Islands, U.S.')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (242, 'WF', 'Wallis And Futuna')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (243, 'EH', 'Western Sahara')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (244, 'YE', 'Yemen')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (245, 'ZM', 'Zambia')"); $database->query();
				$database->setQuery("INSERT INTO `#__bl_countries` VALUES (246, 'ZW', 'Zimbabwe')"); $database->query();
			}
			
			
			//reg config
			$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='nick_reg'";

			$database->setQuery($query);

			if(!$database->loadResult()){

				$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('nick_reg', '0')");

				$database->query();

			}
			$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='nick_reg_rq'";

			$database->setQuery($query);

			if(!$database->loadResult()){

				$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('nick_reg_rq', '0')");

				$database->query();

			}
			$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='country_reg'";

			$database->setQuery($query);

			if(!$database->loadResult()){

				$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('country_reg', '0')");

				$database->query();

			}
			$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='country_reg_rq'";

			$database->setQuery($query);

			if(!$database->loadResult()){

				$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('country_reg_rq', '0')");

				$database->query();

			}
			$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='mcomments'";

			$database->setQuery($query);

			if(!$database->loadResult()){

				$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('mcomments', '0')");

				$database->query();

			}
			
			$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='player_reg'";

			$database->setQuery($query);

			if(!$database->loadResult()){

				$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('player_reg', '0')");

				$database->query();

			}
			
			$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='team_reg'";

			$database->setQuery($query);

			if(!$database->loadResult()){

				$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('team_reg', '0')");

				$database->query();

			}


		
			
				//add player function moderator
				$query = "SELECT cfg_value FROM `#__bl_config` WHERE cfg_name='moder_addplayer'";

				$database->setQuery($query);

				if(!$database->loadResult()){

					$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('moder_addplayer', '0')");

					$database->query();

				}
				
				//add player default ordering
				$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='pllist_order'";

				$database->setQuery($query);

				if(!$database->loadResult()){

					$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('pllist_order', '0')");

					$database->query();

				}
		//SELECT		
				$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='pllist_order_se'";

				$database->setQuery($query);

				if(!$database->loadResult()){

					$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('pllist_order_se', '0')");

					$database->query();

				}
				//add width logo team
				$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='teamlogo_height'";

				$database->setQuery($query);

				if(!$database->loadResult()){

					$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('teamlogo_height', '30')");

					$database->query();

				}
				
				//account limits
				$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='teams_per_account'";

				$database->setQuery($query);

				if(!$database->loadResult()){

					$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('teams_per_account', '5')");

					$database->query();

				}
				$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='players_per_account'";

				$database->setQuery($query);

				if(!$database->loadResult()){

					$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('players_per_account', '10')");

					$database->query();

				}
				//for venue
				$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='unbl_venue'";

				$database->setQuery($query);

				if(!$database->loadResult()){

					$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('unbl_venue', '0')");

					$database->query();

				}
				$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='cal_venue'";

				$database->setQuery($query);

				if(!$database->loadResult()){

					$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('cal_venue', '0')");

					$database->query();

				}
				
				

				
				
				//match played
				$query = "SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='played_matches'";
				$database->setQuery($query);
				if(!$database->loadResult()){
					$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('played_matches', '1')");
					$database->query();
				}
				
				//	nick or name	
				$database->SetQuery("INSERT INTO  `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('player_name',  '0')");
				$database->query();
				//esport config
				$database->SetQuery("INSERT INTO  `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('esport_invite_player',  '0')");
				$database->query();
				$database->SetQuery("INSERT INTO  `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('esport_invite_confirm',  '0')");
				$database->query();
				$database->SetQuery("INSERT INTO  `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('esport_invite_unregister',  '0')");
				$database->query();
				$database->SetQuery("INSERT INTO  `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('esport_join_team',  '0')");
				$database->query();
				$database->SetQuery("INSERT INTO  `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('esport_invite_match', '0')");
				$database->query();

				//admin rights
				$database->SetQuery("INSERT INTO `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('jssa_editplayer',  '1')");
				$database->query();
				//UPDATE
				$database->SetQuery("INSERT INTO `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('jssa_editplayer_single',  '1')");
				$database->query();
				
				
				
				$database->SetQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jssa_deleteplayers', '1')");
				$database->query();
				
				$database->SetQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jssa_deleteplayers_single', '1')");
				$database->query();

				
				
				//knock_style
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('knock_style', '0')");
				$database->query();
				
				
				
				//templates
				
				$database->setQuery("INSERT IGNORE INTO  `#__bl_templates` (`id` ,`name` ,`isdefault`) VALUES ('1',  'default',  '1')");
				$database->query();

				//social buttons
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsb_twitter', '0')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsb_gplus', '0')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsb_fbshare', '0')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsb_fblike', '0')");
				$database->query();
				
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsbp_season', '0')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsbp_team', '0')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsbp_player', '0')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsbp_match', '0')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsbp_venue', '0')");
				$database->query();
				
				//add existing team for season admin
				$database->setQuery("INSERT INTO  `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('jssa_addexteam',  '0')");
				$database->query();
				$database->setQuery("INSERT INTO  `#__bl_config` (`cfg_name` ,`cfg_value`) VALUES ('jssa_addexteam_single',  '0')");
				$database->query();
		//add existing UPDATE
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jssa_addexplayer', '0')");
				$database->query(); 
				//JS player add new team
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('player_team_reg', '1')");
				$database->query();

				//auto registered
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('autoreg_player', '0')");
				$database->query();
				
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('reg_lastname', '1')");
				$database->query();
				
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('reg_lastname_rq', '1')");
				$database->query();
				
				
				//seas adm rights
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jssa_editteam', '1')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jssa_delteam', '1')");
				$database->query();
				
				//branding
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsbrand_on', '1')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsbrand_epanel_image', '/components/com_joomsport/img/logo.png')");
				$database->query();

				//moder rights
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsmr_mark_played', '0')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsmr_editresult_yours', '1')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsmr_editresult_opposite', '0')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsmr_edit_playerevent_yours', '1')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsmr_edit_playerevent_opposite', '0')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsmr_edit_matchevent_yours', '1')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsmr_edit_matchevent_opposite', '0')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsmr_edit_squad_yours', '1')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('jsmr_edit_squad_opposite', '0')");
				$database->query();
			////paypal
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('paypal_acc', 'your@mail.com')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('paypalval_val', '0')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('paypalvalleast_val', '0')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('pllist_order', 'USD')");
				$database->query();
				$database->setQuery("INSERT INTO `#__bl_config` (`cfg_name`, `cfg_value`) VALUES ('paypal_org', 'Join season')");
				$database->query();
				
				
				
				
				
			///unique maps for season
				$database->setQuery("ALTER TABLE  `#__bl_seas_maps` ADD UNIQUE (`season_id` ,`map_id`)");
				$database->query();
                
			//events
				$database->setQuery("SHOW COLUMNS FROM `#__bl_events` LIKE 'result_type'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_events` ADD `result_type` VARCHAR( 1 ) NOT NULL DEFAULT  '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_events` LIKE 'sumev1'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_events` ADD `sumev1` INT NOT NULL");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_events` LIKE 'sumev2'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_events` ADD `sumev2` INT NOT NULL");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_events` LIKE 'ordering'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_events` ADD `ordering` INT NOT NULL");
                    $database->query();
                }
			//extra_filds
				$database->setQuery("SHOW COLUMNS FROM `#__bl_extra_filds` LIKE 'field_type'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_extra_filds` ADD `field_type` char(1) NOT NULL default '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_extra_filds` LIKE 'reg_exist'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_extra_filds` ADD `reg_exist` char(1) NOT NULL default '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_extra_filds` LIKE 'reg_require'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_extra_filds` ADD `reg_require` char(1) NOT NULL default '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_extra_filds` LIKE 'fdisplay'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_extra_filds` ADD `fdisplay` char(1) NOT NULL default '1'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_extra_filds` LIKE 'season_related'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_extra_filds` ADD `season_related` VARCHAR( 1 ) NOT NULL DEFAULT  '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_extra_filds` LIKE 'faccess'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_extra_filds` ADD `faccess` VARCHAR( 1 ) NOT NULL DEFAULT  '0'");
                    $database->query();
                }
			//extra_values
				$database->setQuery("SHOW COLUMNS FROM `#__bl_extra_values` LIKE 'fvalue_text'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_extra_values` ADD `fvalue_text` text NOT NULL");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_extra_values` LIKE 'season_id'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_extra_values` ADD `season_id` INT NOT NULL");
                    $database->query();
                }
			//match
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'k_ordering'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `k_ordering` int(11) NOT NULL DEFAULT '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'k_title'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `k_title` varchar(255) NOT NULL DEFAULT ''");
                    $database->query();
                }
		$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'k_stage'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `k_stage` int(11) NOT NULL DEFAULT '1'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'points1'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `points1` decimal(10,2) NOT NULL DEFAULT '0.00'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'points2'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `points2` decimal(10,2) NOT NULL DEFAULT '0.00'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'new_points'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `new_points` char(1) NOT NULL DEFAULT '0'");
                    $database->query();
                }
				
				$database->setQuery("SELECT COUNT(*) FROM `#__bl_config` WHERE cfg_name='custom_fields'");
				if (! $database->loadResult()) {
					$fields = serialize(array(
						'team_city' => array(
							'title' => 'BLFA_TT_CITY',
							'enabled' => true,
							'required' => false,
						),        
					));
					$database->SetQuery("INSERT INTO `#__bl_config` (cfg_name,cfg_value) VALUES ('custom_fields', '$fields')");
					$database->query();
				}
		
				
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'venue_id'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `venue_id` INT NOT NULL");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'aet1'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `aet1` INT NOT NULL");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'aet2'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `aet2` INT NOT NULL");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'p_winner'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `p_winner` INT NOT NULL");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'm_single'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `m_single` VARCHAR( 1 ) NOT NULL DEFAULT  '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'betavailable'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `betavailable` TINYINT(4) NOT NULL");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'betfinishdate'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `betfinishdate` DATE NOT NULL DEFAULT '0000-00-00'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'betfinishtime'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match` ADD `betfinishtime` VARCHAR(10) NOT NULL");
                    $database->query();
                }
			//matchday
				$database->setQuery("SHOW COLUMNS FROM `#__bl_matchday` LIKE 'k_format'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_matchday` ADD `k_format` int(11) NOT NULL default '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_matchday` LIKE 'ordering'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_matchday` ADD `ordering` INT NOT NULL");
                    $database->query();
                }
			//match_events
				$database->setQuery("SHOW COLUMNS FROM `#__bl_match_events` LIKE 'eordering'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_match_events` ADD `eordering` INT NOT NULL");
                    $database->query();
                }
			//players
				$database->setQuery("SHOW COLUMNS FROM `#__bl_players` LIKE 'usr_id'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_players` ADD `usr_id` int(11) NOT NULL default '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_players` LIKE 'country_id'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_players` ADD `country_id` int(11) NOT NULL default '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_players` LIKE 'registered'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_players` ADD `registered` char(1) NOT NULL default '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_players` LIKE 'created_by'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_players` ADD `created_by` INT NOT NULL DEFAULT  '0'");
                    $database->query();
                }
			//seasons
				$database->setQuery("SHOW COLUMNS FROM `#__bl_seasons` LIKE 's_participant'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_seasons` ADD `s_participant` int(11) NOT NULL DEFAULT '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_seasons` LIKE 's_reg'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_seasons` ADD `s_reg` char(1) NOT NULL DEFAULT '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_seasons` LIKE 'reg_start'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_seasons` ADD `reg_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_seasons` LIKE 'reg_end'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_seasons` ADD `reg_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_seasons` LIKE 's_rules'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_seasons` ADD `s_rules` text NOT NULL");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_seasons` LIKE 'ordering'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_seasons` ADD `ordering` INT NOT NULL");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_seasons` LIKE 'idtemplate'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_seasons` ADD `idtemplate` INT NOT NULL");
                    $database->query();
                }
			//season_option
				$database->setQuery("SHOW COLUMNS FROM `#__bl_season_option` LIKE 'ordering'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_season_option` ADD `ordering` INT NOT NULL");
                    $database->query();
                }
			//season_teams
				$database->setQuery("SHOW COLUMNS FROM `#__bl_season_teams` LIKE 'regtype'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_season_teams` ADD `regtype` VARCHAR(1) NOT NULL DEFAULT '0'");
                    $database->query();
                }
			//squard
				$database->setQuery("SHOW COLUMNS FROM `#__bl_squard` LIKE 'accepted'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_squard` ADD `accepted` VARCHAR(1) NOT NULL DEFAULT '1'");
                    $database->query();
                }
			//teams
				$database->setQuery("SHOW COLUMNS FROM `#__bl_teams` LIKE 'created_by'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_teams` ADD `created_by` INT NOT NULL DEFAULT '0'");
                    $database->query();
                }
				$database->setQuery("SHOW COLUMNS FROM `#__bl_teams` LIKE 'venue_id'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_teams` ADD `venue_id` INT NOT NULL");
                    $database->query();
                }
			//tournament
				//$database->setQuery("SHOW COLUMNS FROM `#__bl_tournament` LIKE 't_type'");
                //$is_col = $database->loadResult();
                //if(!$is_col){
                   // $database->setQuery("ALTER TABLE `#__bl_tournament` ADD `t_type` int(1) NOT NULL default '0'");
                   // $database->query();
                //}
				$database->setQuery("SHOW COLUMNS FROM `#__bl_tournament` LIKE 't_single'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_tournament` ADD `t_single` char(1) NOT NULL default '0'");
                    $database->query();
                }
				
				
				//add
                $database->setQuery("SHOW COLUMNS FROM `#__bl_matchday` LIKE 't_type'");
                $is_col = $database->loadResult();

                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_matchday` ADD `t_type` int(1) NOT NULL default '0'");
                    $database->query();
                }
				
				$database->setQuery("SHOW COLUMNS FROM `#__bl_tournament` LIKE 't_type'");
                $is_col = $database->loadResult();
				if($is_col){
					$database->setQuery("SELECT md.id  FROM `#__bl_tournament` as t, `#__bl_seasons` as s, `#__bl_matchday` as md WHERE t.id = s.t_id AND s.s_id = md.s_id AND t.t_type = '1'");
					$smatch = $database->loadColumn();

					if(count($smatch)){
						foreach($smatch as $sm){
							$database->setQuery("UPDATE `#__bl_matchday` SET `t_type` = '1' WHERE `id` = '".$sm."' ");
							$database->query();
						}
					}
				}
            ////
            $database->setQuery("SHOW COLUMNS FROM `#__bl_match` LIKE 'k_type'");
            $is_col = $database->loadResult();

            if(!$is_col){
                $database->setQuery("ALTER TABLE `#__bl_match` ADD `k_type` int(1) NOT NULL default '0'");
                $database->query();
            }
			
			//club
			$database->setQuery("SHOW COLUMNS FROM `#__bl_teams` LIKE 'club_id'");
            $is_col = $database->loadResult();
            if(!$is_col){
				$database->setQuery("ALTER TABLE `#__bl_teams` ADD `club_id` int(1) NOT NULL default '0'");
				$database->query();
			}
			$database->setQuery("SHOW COLUMNS FROM `#__bl_tournament` LIKE 't_type'");
            $is_col = $database->loadResult();
            if($is_col){
				$database->setQuery("ALTER TABLE `#__bl_tournament` DROP t_type");
				$database->query();
			}
			
			//seas_payments
			$database->setQuery("SHOW COLUMNS FROM `#__bl_seasons` LIKE 'is_pay'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_seasons` ADD `is_pay` VARCHAR( 1 ) NOT NULL DEFAULT  '0'");
                    $database->query();
                }
			
                $database->setQuery("UPDATE `#__bl_extra_filds` SET `fdisplay` = '1'");
                $database->query();
                
                
                // for 3.3 new tournament type
                $database->setQuery("SHOW COLUMNS FROM `#__bl_tournament` LIKE 'tournament_type'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_tournament` ADD `tournament_type` VARCHAR(1) NOT NULL DEFAULT '0'");
                    $database->query();
                }
                
                $database->setQuery("SHOW COLUMNS FROM `#__bl_seasons` LIKE 'season_options'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_seasons` ADD `season_options` TEXT NULL DEFAULT NULL");
                    $database->query();
                }
                
                $database->setQuery("SHOW COLUMNS FROM `#__bl_matchday` LIKE 'start_date'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_matchday` ADD `start_date` DATE NULL DEFAULT NULL , ADD `end_date` DATE NULL DEFAULT NULL");
                    $database->query();
                }
                
                
                
                //rounds tables in update
                $database->setQuery("CREATE TABLE IF NOT EXISTS `#__bl_rounds` (
                    `id` int(11) NOT NULL auto_increment,
                    `round_title` varchar(255) NOT NULL,
                    `round_status` varchar(1) NOT NULL DEFAULT '0',
                    `md_id` int(11) NOT NULL,
                    `ordering` int(11) NOT NULL,
                    PRIMARY KEY  (`id`)
                  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
                $database->query();
                $database->setQuery("CREATE TABLE IF NOT EXISTS `#__bl_rounds_participiants` (
                    `id` int(11) NOT NULL auto_increment,
                    `round_id` int(11) NOT NULL,
                    `participiant_id` int(11) NOT NULL,
                    `attempts` varchar(100) NOT NULL,
                    `result_string` varchar(255) NOT NULL,
                    `result_num` decimal(10,0) NOT NULL,
                    `rank` TINYINT NOT NULL ,
                    `penalty` DECIMAL NOT NULL,
                    `points` tinyint(4) NOT NULL DEFAULT '0',
                    PRIMARY KEY  (`id`)
                  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
                $database->query();
                
                $database->setQuery("SHOW COLUMNS FROM `#__bl_seasons` LIKE 'season_options'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_seasons` ADD `season_options` text");
                    $database->query();
                }
                
                $database->setQuery("ALTER TABLE `#__bl_rounds_participiants` CHANGE `penalty` `penalty` VARCHAR(50) NOT NULL");
                $database->query();    
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'jbl_start.php');
                
                $database->setQuery("SHOW COLUMNS FROM `#__bl_rounds_participiants` LIKE 'extracol'");
                $is_col = $database->loadResult();
                if(!$is_col){
                    $database->setQuery("ALTER TABLE `#__bl_rounds_participiants` ADD `extracol` TEXT NOT NULL ;");
                    $database->query();  
                }
                
                $database->setQuery("CREATE TABLE IF NOT EXISTS `#__bl_rounds_extracol` (
                    `id` int(11) NOT NULL,
                      `round_id` int(11) NOT NULL,
                      `name` varchar(255) NOT NULL,
                      `ordering` int(11) NOT NULL,
                        PRIMARY KEY  (`id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;");
                $database->query();
        }
}