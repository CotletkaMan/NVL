CREATE TABLE IF NOT EXISTS `#__bl_rounds_extracol` (
`id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_rounds` (
  `id` int(11) NOT NULL auto_increment,
  `round_title` varchar(255) NOT NULL,
  `round_status` varchar(1) NOT NULL DEFAULT '0',
  `md_id` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_rounds_participiants` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_moders` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`,`tid`)
);
CREATE TABLE IF NOT EXISTS `#__bl_ranksort` (
  `seasonid` int(11) NOT NULL,
  `sort_field` varchar(255) NOT NULL,
  `sort_way` varchar(1) NOT NULL,
  `ordering` int(11) NOT NULL
)ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_squard` (
	`match_id` int(11) NOT NULL default '0',
	`team_id` int(11) NOT NULL default '0',
	`player_id` int(11) NOT NULL default '0',
	`mainsquard` char(1) NOT NULL default '1',
	`accepted` VARCHAR( 1 ) NOT NULL DEFAULT  '1',
	PRIMARY KEY  (`match_id`,`team_id`,`player_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_mapscore` (
	`m_id` int(11) NOT NULL default '0',
	`map_id` int(11) NOT NULL default '0',
	`m_score1` int(11) NOT NULL default '0',
	`m_score2` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_extra_select` (
  `fid` int(11) NOT NULL default '0',
  `sel_value` varchar(255) NOT NULL default '',
  `id` int(11) NOT NULL auto_increment,
  `eordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fid` (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_comments` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `match_id` int(11) NOT NULL default '0',
  `date_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `match_id` (`match_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_maps` (
`id` int(11) NOT NULL auto_increment,
`m_name` varchar(255) NOT NULL default '',
`map_descr` text NOT NULL,
`map_img` VARCHAR( 255 ) NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_config` (
`cfg_name` varchar(255) NOT NULL default '',
`cfg_value` varchar(255) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_countries` (
  `id` int(11) NOT NULL auto_increment,
  `ccode` varchar(2) NOT NULL default '',
  `country` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_feadmins` (
`user_id` int(11) NOT NULL default '0',
`season_id` int(11) NOT NULL default '0',
PRIMARY KEY  (`user_id`,`season_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_assign_photos` (
`photo_id` int(11) NOT NULL default '0',
`cat_id` int(11) NOT NULL default '0',
`cat_type` int(11) NOT NULL default '0',
PRIMARY KEY  (`photo_id`,`cat_id`,`cat_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_events` (
  `id` int(11) NOT NULL auto_increment,
  `e_name` varchar(255) NOT NULL default '',
  `e_img` varchar(255) NOT NULL default '',
  `e_descr` text NOT NULL,
  `player_event` char(1) NOT NULL default '0',
  `result_type` VARCHAR( 1 ) NOT NULL DEFAULT  '0',
  `sumev1` INT NOT NULL,
  `sumev2` INT NOT NULL,
  `ordering` INT NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_extra_filds` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `published` char(1) NOT NULL default '1',
  `type` char(1) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `e_table_view` char(1) NOT NULL default '0',
  `field_type` char(1) NOT NULL default '0',
  `reg_exist` char(1) NOT NULL default '0',
  `reg_require` char(1) NOT NULL default '0',
  `fdisplay` char(1) NOT NULL default '1',
  `season_related` VARCHAR( 1 ) NOT NULL DEFAULT  '0',
  `faccess` VARCHAR( 1 ) NOT NULL DEFAULT  '0',
  PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__bl_extra_values` (
  `f_id` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `fvalue` varchar(255) NOT NULL default '',
  `fvalue_text` text NOT NULL,
  `season_id` INT NOT NULL,
  PRIMARY KEY  (`f_id` ,  `uid` ,  `season_id` ),
  KEY `uid` (`uid`)
);
CREATE TABLE IF NOT EXISTS `#__bl_groups` (
  `id` int(11) NOT NULL auto_increment,
  `group_name` varchar(255) NOT NULL default '',
  `s_id` int(11) NOT NULL default '0',
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `s_id` (`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_grteams` (
  `g_id` int(11) NOT NULL default '0',
  `t_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`g_id`,`t_id`),
  KEY `g_id` (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_match` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `m_id` int(11) NOT NULL DEFAULT '0',
  `team1_id` int(11) NOT NULL DEFAULT '0',
  `team2_id` int(11) NOT NULL DEFAULT '0',
  `score1` int(11) NOT NULL DEFAULT '0',
  `score2` int(11) NOT NULL DEFAULT '0',
  `match_descr` text NOT NULL,
  `published` char(1) NOT NULL DEFAULT '0',
  `is_extra` char(1) NOT NULL DEFAULT '0',
  `m_played` char(1) NOT NULL DEFAULT '1',
  `m_date` date NOT NULL DEFAULT '0000-00-00',
  `m_time` varchar(10) NOT NULL DEFAULT '',
  `m_location` varchar(255) NOT NULL DEFAULT '',
  `k_ordering` int(11) NOT NULL DEFAULT '0',
  `k_title` varchar(255) NOT NULL DEFAULT '',
  `k_stage` int(11) NOT NULL DEFAULT '1',
  `points1` decimal(10,2) NOT NULL DEFAULT '0.00',
  `points2` decimal(10,2) NOT NULL DEFAULT '0.00',
  `new_points` char(1) NOT NULL DEFAULT '0',
  `bonus1` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bonus2` decimal(10,2) NOT NULL DEFAULT '0.00',
  `venue_id` INT NOT NULL,
  `aet1` INT NOT NULL,
  `aet2` INT NOT NULL,
  `p_winner` INT NOT NULL,
  `m_single` VARCHAR( 1 ) NOT NULL DEFAULT  '0',
  `betavailable` TINYINT(4) NOT NULL,
  `betfinishdate` DATE NOT NULL DEFAULT '0000-00-00',
  `betfinishtime` VARCHAR(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `m_id` (`m_id`),
  KEY `team1_id` (`team1_id`),
  KEY `team2_id` (`team2_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_match_events` (
  `e_id` int(11) NOT NULL default '0',
  `player_id` int(11) NOT NULL default '0',
  `match_id` int(11) NOT NULL default '0',
  `ecount` int(11) NOT NULL default '0',
  `minutes` varchar(255) NOT NULL default '',
  `t_id` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL auto_increment,
  `eordering` INT NOT NULL,
	  PRIMARY KEY  (`id`),
	  KEY `player_id` (`player_id`,`match_id`,`t_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_matchday` (
  `id` int(11) NOT NULL auto_increment,
  `m_name` varchar(255) NOT NULL default '',
  `m_descr` text NOT NULL,
  `s_id` int(11) NOT NULL default '0',
  `is_playoff` char(1) NOT NULL default '0',
  `k_format` int(11) NOT NULL default '0',
  `ordering` INT NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `t_id` (`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_photos` (
  `id` int(11) NOT NULL auto_increment,
  `ph_name` varchar(255) NOT NULL default '',
  `ph_filename` varchar(255) NOT NULL default '',
  `ph_descr` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_players` (
  `id` int(11) NOT NULL auto_increment,
  `first_name` varchar(255) NOT NULL default '',
  `last_name` varchar(255) NOT NULL default '',
  `nick` varchar(255) NOT NULL default '',
  `about` text NOT NULL,
  `position_id` int(11) NOT NULL default '0',
  `def_img` int(11) NOT NULL default '0',
  `team_id` int(11) NOT NULL default '0',
  `usr_id` int(11) NOT NULL default '0',
  `country_id` int(11) NOT NULL default '0',
  `registered` char(1) NOT NULL default '0',
  `created_by` INT NOT NULL DEFAULT  '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_players_team` (
  `team_id` int(11) NOT NULL default '0',
  `player_id` int(11) NOT NULL default '0',
  `confirmed` char(1) NOT NULL default '0',
  `season_id` INT NOT NULL,
  `invitekey` VARCHAR( 255 ) NOT NULL,
  `player_join` VARCHAR( 1 ) NOT NULL DEFAULT  '0',
  PRIMARY KEY  (  `team_id` ,  `player_id` ,  `season_id` ),
  KEY `player_id` (`player_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_seas_maps` (
  `season_id` int(11) NOT NULL default '0',
  `map_id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_season_teams` (
  `season_id` int(11) NOT NULL default '0',
  `team_id` int(11) NOT NULL default '0',
  `bonus_point` int(11) NOT NULL default '0',
  `regtype` VARCHAR( 1 ) NOT NULL DEFAULT  '0',
  PRIMARY KEY  (`season_id`,`team_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_seasons` (
  `s_id` int(11) NOT NULL AUTO_INCREMENT,
  `s_name` varchar(255) NOT NULL DEFAULT '',
  `s_descr` text NOT NULL,
  `s_rounds` int(11) NOT NULL DEFAULT '1',
  `t_id` int(11) NOT NULL DEFAULT '0',
  `published` char(1) NOT NULL DEFAULT '0',
  `s_win_point` decimal(10,2) NOT NULL DEFAULT '3.00',
  `s_lost_point` decimal(10,2) NOT NULL DEFAULT '0.00',
  `s_enbl_extra` int(11) NOT NULL DEFAULT '0',
  `s_extra_win` decimal(10,2) NOT NULL DEFAULT '0.00',
  `s_extra_lost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `s_draw_point` decimal(10,2) NOT NULL DEFAULT '0.00',
  `s_groups` int(11) NOT NULL DEFAULT '0',
  `s_win_away` decimal(10,2) NOT NULL DEFAULT '0.00',
  `s_draw_away` decimal(10,2) NOT NULL DEFAULT '0.00',
  `s_lost_away` decimal(10,2) NOT NULL DEFAULT '0.00',
  `s_participant` int(11) NOT NULL DEFAULT '0',
  `s_reg` char(1) NOT NULL DEFAULT '0',
  `reg_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reg_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `s_rules` text NOT NULL,
  `ordering` INT NOT NULL,
  `idtemplate` INT NOT NULL,
  `season_options` text,
  PRIMARY KEY  (`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_tblcolors` (
  `s_id` int(11) NOT NULL default '0',
  `place` varchar(35) NOT NULL default '',
  `color` varchar(10) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_season_players` (
  `player_id` int(11) NOT NULL default '0',
  `season_id` int(11) NOT NULL default '0',
  `bonus_point` int(11) NOT NULL default '0',
  `regtype` VARCHAR( 1 ) NOT NULL DEFAULT  '0',
  PRIMARY KEY  (`player_id`,`season_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_teams` (
  `id` int(11) NOT NULL auto_increment,
  `t_name` varchar(255) NOT NULL default '',
  `t_descr` text NOT NULL,
  `t_yteam` char(1) NOT NULL default '0',
  `def_img` int(11) NOT NULL default '0',
  `t_emblem` varchar(255) NOT NULL default '',
  `t_city` varchar(255) NOT NULL default '',
  `created_by` INT NOT NULL DEFAULT '0',
  `venue_id` INT NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_tournament` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `descr` text NOT NULL,
  `published` char(1) NOT NULL default '0',
  `t_single` char(1) NOT NULL default '0',
  `logo` varchar(255) NOT NULL,
  `tournament_type` VARCHAR(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_season_option` (
  `s_id` int(11) NOT NULL default '0',
  `opt_name` varchar(255) NOT NULL default '',
  `opt_value` varchar(255) NOT NULL default '',
  `ordering` INT NOT NULL,
  PRIMARY KEY  (`s_id`,`opt_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_venue` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`v_name` VARCHAR( 255 ) NOT NULL ,
`v_address` VARCHAR( 255 ) NOT NULL ,
`v_descr` TEXT NOT NULL ,
`v_defimg` INT NOT NULL ,
`v_coordx` FLOAT NOT NULL ,
`v_coordy` FLOAT NOT NULL
) ENGINE = MYISAM ;
CREATE TABLE IF NOT EXISTS `#__bl_addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `version` varchar(255) NOT NULL,
  `published` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_subsin` (
`match_id` INT NOT NULL ,
`team_id` INT NOT NULL ,
`player_in` INT NOT NULL ,
`player_out` INT NOT NULL ,
`minutes` INT NOT NULL ,
`season_id` INT NOT NULL,
KEY `match_id` (`match_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_club` (
  `id` int(11) NOT NULL auto_increment,
  `c_name` varchar(255) NOT NULL default '',
  `c_descr` text NOT NULL,
  `def_img` int(11) NOT NULL default '0',
  `c_emblem` varchar(255) NOT NULL default '',
  `c_city` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
CREATE TABLE IF NOT EXISTS `#__bl_templates` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`isdefault` VARCHAR( 1 ) NOT NULL DEFAULT  '0',
`variable1` VARCHAR( 255 ) NOT NULL ,
`variable2` VARCHAR( 255 ) NOT NULL ,
`variable3` VARCHAR( 255 ) NOT NULL ,
`variable4` VARCHAR( 255 ) NOT NULL ,
`variable5` VARCHAR( 255 ) NOT NULL ,
`variable6` VARCHAR( 255 ) NOT NULL ,
`variable7` VARCHAR( 255 ) NOT NULL 
) ENGINE = MYISAM ;
CREATE TABLE IF NOT EXISTS `#__bl_betting_events` ( `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `type` varchar(20) NOT NULL, `difffrom` varchar(255) NOT NULL DEFAULT '', `diffto` varchar(255) NOT NULL DEFAULT '', `isdeleted` tinyint(4) NOT NULL DEFAULT '0', PRIMARY KEY (`id`) ) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_betting_logs` ( `id` int(11) NOT NULL AUTO_INCREMENT, `iduser` int(11) NOT NULL, `points` float NOT NULL, `date` datetime NOT NULL, PRIMARY KEY (`id`) ) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_betting_templates` ( `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `description` text NOT NULL, `isdeleted` tinyint(4) NOT NULL DEFAULT '0', PRIMARY KEY (`id`) ) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_betting_templates_events` ( `id` int(11) NOT NULL AUTO_INCREMENT, `idtemplate` int(11) NOT NULL, `idevent` int(11) NOT NULL, PRIMARY KEY (`id`), KEY `idtemplate` (`idtemplate`,`idevent`) ) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_betting_users` ( `id` int(11) NOT NULL AUTO_INCREMENT, `iduser` int(11) NOT NULL, `points` float NOT NULL, PRIMARY KEY (`id`) ) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_betting_requests_cash` ( `id` int(11) NOT NULL AUTO_INCREMENT, `iduser` int(11) NOT NULL, `points` float NOT NULL, `status` VARCHAR(50) NOT NULL, `date` DATETIME NOT NULL, PRIMARY KEY (`id`) ) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__bl_betting_requests_points` ( `id` int(11) NOT NULL AUTO_INCREMENT, `iduser` int(11) NOT NULL, `points` float NOT NULL, `status` VARCHAR(50) NOT NULL, `date` DATETIME NOT NULL, PRIMARY KEY (`id`) ) DEFAULT CHARSET=utf8;