<?php

/* ------------------------------------------------------------------------
  # JoomSport Professional
  # ------------------------------------------------------------------------
  # BearDev development company
  # Copyright (C) 2011 JoomSport.com. All Rights Reserved.
  # @license - http://joomsport.com/news/license.html GNU/GPL
  # Websites: http://www.JoomSport.com
  # Technical Support:  Forum - http://joomsport.com/helpdesk/
  ------------------------------------------------------------------------- */
// No direct access.
defined('_JEXEC') or die;

require(dirname(__FILE__) . '/../models.php');
if (js_mobile::isMobile()){
    require(dirname(__FILE__).'/../../includes/pagination_mobile.php');
} else {
    require(dirname(__FILE__).'/../../includes/pagination.php');
}
require_once(JPATH_SITE.'/administrator/components/com_joomsport/classes/class-matchday-race.php');

class playerJSModel extends JSPRO_Models {

    var $_lists = null;
    var $s_id = null;
    var $player_id = null;
    var $seasplayed = array();
    var $limit = null;
    var $limitstart = null;
    var $_total = null;
    var $_pagination = null;
    var $seassingle = array();
    var $title = null;
    var $p_title = null;

    function __construct() {
        parent::__construct();

        $this->player_id = JRequest::getVar('id', 0, '', 'int');
        $this->s_id = JRequest::getVar('sid', 0, '', 'int');
        $this->limit = JRequest::getVar('jslimit', 20, '', 'int');
        $this->limitstart = JRequest::getVar('page', 1, '', 'int');
        $this->limitstart = intval($this->limitstart) > 1 ? $this->limitstart : 1;
        $this->_lists['jscurtab'] = JRequest::getVar('jscurtab', 'etab_player', '', 'string');

        $this->title = JFactory::getDocument()->getTitle();

        if (isset($_REQUEST['limitstart']) && $this->_lists['jscurtab'] == 'etab_player') {
            $this->_lists['jscurtab'] = 'etab_match';
        }

        $query = "SELECT first_name FROM #__bl_players WHERE id = " . $this->player_id;
        $this->db->setQuery($query);
        $player_id = $this->db->loadResult();
        if (!$player_id) {
            JError::raiseError(403, JText::_('Access Forbidden'));
            return;
        }
        $this->getPagination();
    }

    function getData() {

        $query = "SELECT p.*,c.country,c.ccode FROM #__bl_players as p LEFT JOIN #__bl_countries as c ON c.id=p.country_id  WHERE p.id = " . $this->player_id;
        $this->db->setQuery($query);
        $player = $this->db->loadObject();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $this->_lists["player"] = $player;
        //title
        $this->p_title = $player->first_name . " " . $player->last_name;

        $this->_params = $this->JS_PageTitle($this->title ? $this->title : $this->p_title);

        $query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = " . $this->player_id;
        $this->db->setQuery($query);
        $photos = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $this->_lists["photos"] = $photos;

        $def_img = '';
        if ($player->def_img) {
            $query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = " . $player->def_img;
            $this->db->setQuery($query);
            $def_img = $this->db->loadResult();
        } else if (isset($photos[0])) {
            $def_img = $photos[0]->filename;
        }
        $this->_lists["def_img"] = $def_img;

        $this->_lists["matches"] = $this->getSingleMatches();
//        var_dump($this->_lists["matches"]); exit();
        if (count($this->_lists["matches"])) {
            for ($z = 0; $z < count($this->_lists["matches"]); $z++) {
                $this->_lists["matches"][$z]->home = $this->selectPlayerName($this->_lists["matches"][$z]);
                $this->_lists["matches"][$z]->away = $this->selectPlayerName($this->_lists["matches"][$z], "fn2", "ln2", "nick2");
            }
        }

        $this->_lists["enbl_extra"] = 0;
        if (!$this->s_id) {
            $this->_lists["enbl_extra"] = 1;
        }
        if ($this->s_id) {
            $this->_lists["unable_reg"] = $this->unblSeasonReg();
        }
        $this->_lists["teams_season"] = $this->teamsToModer();
        ;
        $this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"], @$this->_lists["unable_reg"], $this->s_id, 1);
        $this->seasplFilter();

        $this->playerEvents();
        ///-----EXTRAFIELDS---//

        $this->_lists['ext_fields'] = $this->getAddFields($this->player_id, '0', "player", $this->s_id);

        //social buttons
        $ogimg = '';
        if ($this->_lists["def_img"] && is_file('media/bearleague/' . $this->_lists["def_img"])) {
            $ogimg = JURI::base() . 'media/bearleague/' . $this->_lists["def_img"];
        }
        $this->_lists['socbut'] = $this->getSocialButtons('jsbp_player', $player->first_name . " " . $player->last_name, $ogimg, htmlspecialchars(strip_tags($this->_lists["player"]->about)));

        $this->_lists['locven'] = $this->getJS_Config("cal_venue");

        $this->_lists["tourn_name"] = $this->getTournName($this->s_id);

        $this->_lists["teams_name"] = $this->getTeamName($player);
        
        
        //version 3.3 race tourn

        $this->_lists["race_res"] = MatchdayRace::getParticipantResults($this->s_id, 1, $this->player_id);
        
    }

    function getPagination() {
        if (empty($this->_pagination)) {
            $this->_pagination = new JS_Pagination($this->getTotal(), $this->limitstart, $this->limit);
        }

        return $this->_pagination;
    }

    function getTotal() {
        if (empty($this->_total)) {
            $this->_total = 0;
            $query = "SELECT s.s_id as id FROM #__bl_tournament as t, #__bl_seasons as s WHERE t.published='1' AND s.published='1' AND t.t_single='1' AND s.t_id = t.id ORDER BY t.name, s.s_name";
            $this->db->setQuery($query);
            $tourna = $this->db->loadColumn();
            if (count($tourna)) {
                if ($this->s_id) {
                    $query = "SELECT COUNT(m.id)"
                            . " FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
                            . " WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=" . $this->s_id . " " . ($this->s_id == -1 ? "" : "AND md.s_id IN (" . implode(',', $tourna) . ")") . "  AND m.team1_id = t1.id AND m.team2_id = t2.id " . ($this->player_id ? " AND (t1.id=" . $this->player_id . " OR t2.id=" . $this->player_id . ")" : "")
                            . " ORDER BY m.m_date,m.m_time,md.id";
                } else {
                    $query = "SELECT COUNT(m.id)"
                            . " FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
                            . " WHERE m.m_id = md.id AND m.published = 1 AND m.team1_id = t1.id AND (md.s_id IN (" . implode(',', $tourna) . ") OR (md.s_id = -1 AND m.m_single='1')) AND m.team2_id = t2.id " . ($this->player_id ? " AND (t1.id=" . $this->player_id . " OR t2.id=" . $this->player_id . ")" : "")
                            . " ORDER BY m.m_date,m.m_time,md.id";
                }
                $this->db->setQuery($query);
                $this->_total = $this->db->loadResult();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
            }
        }
        if ($this->_total == 0) {
            $this->_lists['jscurtab'] = 'etab_player';
        }
        return $this->_total;
    }

    function seasplFilter() {
        $is_tourn = array();
        $is_tourn[] = JHTML::_('select.option', 0, JText::_('BLFA_ALL'), 'id', 's_name');
        $query = "SELECT * FROM #__bl_tournament WHERE published = '1' ORDER BY name";
        $this->db->setQuery($query);
        $tourn = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        $javascript = " onchange='document.adminForm.submit();'";
        $jqre = '<select name="sid" id="sid" class="'.(js_mobile::isMobile()?'form-control':'styled jfsubmit').'" size="1" ' . $javascript . '>';
        $jqre .= '<option value="0">' . JText::_('BLFA_ALL') . '</option>';
        //$query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m, #__bl_seasons as s, #__bl_tournament as t WHERE md.s_id=s.s_id AND s.t_id=t.id AND t.published=1 AND s.published=1 AND m.m_id=md.id AND md.s_id= -1 AND m.m_single='1' AND (m.team1_id=".$this->player_id." OR m.team2_id=".$this->player_id.")";
        $query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id=md.id AND md.s_id= -1 AND m.m_single='1' AND (m.team1_id=" . $this->player_id . " OR m.team2_id=" . $this->player_id . ")";
        $this->db->setQuery($query);
        $frm = $this->db->loadResult();

        $error = $this->db->getErrorMsg();

        if ($error) {
            return JError::raiseError(500, $error);
        }

        $query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m, #__bl_squard as sc WHERE sc.match_id=m.id AND sc.player_id=" . $this->player_id . " AND m.m_id=md.id AND md.s_id= -1 ";
        $this->db->setQuery($query);
        $frm2 = $this->db->loadResult();

        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        if ($frm || $frm2) {
            $jqre .= '<option value="-1" ' . ((-1 == $this->s_id) ? "selected" : "") . '>' . JText::_('BLFA_FRIENDLY_MATCHES') . '</option>';
            $this->seasplayed[] = -1;
        }
        $this->seassingle = array();
        for ($i = 0; $i < count($tourn); $i++) {
            $tsingl = $tourn[$i]->t_single;
            //print_r($tsingl);
            if ($tsingl) {
                $query = "SELECT s.s_id as id,s.s_name as s_name"
                        . " FROM #__bl_season_players as sp, #__bl_seasons as s"
                        . " WHERE s.published = '1' AND s.t_id=" . $tourn[$i]->id . " AND s.s_id=sp.season_id AND sp.player_id=" . $this->player_id;
            } else {
                $query = "SELECT DISTINCT(s.s_id) as id,s.s_name as s_name"
                        . " FROM #__bl_seasons as s, #__bl_season_teams as st, #__bl_players_team as pt"
                        . " WHERE pt.confirmed='0' AND s.published = '1' AND s.t_id=" . $tourn[$i]->id . " AND st.season_id=s.s_id AND st.team_id=pt.team_id"
                        . " AND pt.season_id=s.s_id AND pt.player_id=" . $this->player_id
                        . "  ORDER BY s.s_name";
            }
            $is_tourn2 = array();
            $this->db->setQuery($query);
            $rows = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }

            if (count($rows)) {
                $jqre .= '<optgroup label="' . htmlspecialchars($tourn[$i]->name) . '">';
                for ($g = 0; $g < count($rows); $g++) {
                    $jqre .= '<option value="' . $rows[$g]->id . '" ' . (($rows[$g]->id == $this->s_id) ? "selected" : "") . '>' . $rows[$g]->s_name . '</option>';
                    $this->seasplayed[] = $rows[$g]->id;
                    if ($tsingl) {
                        $this->seassingle[] = $rows[$g]->id;
                    }
                }
                $jqre .= '</optgroup>';
            }
        }
        $jqre .= '</select>';



//<---------------------------------		
        $this->_lists['tourn'] = $jqre;
    }

    function playerEvents() {
        $seaslist = '';
        if (count($this->seasplayed)) {
            $seaslist = implode(',', $this->seasplayed);
        }

        $stat_array = array();

        $query = "SELECT DISTINCT(ev.id),ev.* FROM #__bl_events as ev, #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md"
                . " WHERE (ev.id = me.e_id OR (ev.sumev1 = me.e_id OR ev.sumev2 = me.e_id)) AND me.match_id = m.id"
                . " AND m.m_id=md.id " . ($this->s_id ? " AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")) . " AND (ev.player_event = 1 OR ev.player_event = 2)"
                . " ORDER BY ev.ordering";
        $this->db->setQuery($query);
        $events = ($seaslist || $this->s_id) ? $this->db->loadObjectList() : array();
        //print_r($events);
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        // print_r($seaslist);	
        ///////getTeams
        $query = "SELECT team_id FROM #__bl_players_team as t WHERE t.player_id = '" . $this->player_id . "' " . ($this->s_id ? " AND t.season_id=" . $this->s_id : ($seaslist ? " AND t.season_id IN (" . $seaslist . ")" : "")) . "
                            UNION 
                          SELECT s.t_id FROM #__bl_match_events as s JOIN #__bl_match as m ON m.id=s.match_id JOIN #__bl_matchday as md ON (m.m_id=md.id " . ($this->s_id ? " AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")) . ")";
        //print_r($query);
        $this->db->setQuery($query);
        $teams = $this->db->loadColumn();

        $teamss = array();
        if (count($teams)) {
            $teamss = implode(',', $teams);
            // $query .= " AND m.id NOT IN (".$midss.")";
            if ($this->s_id == 0) {
                $teamss .= ",''";
            }
        }

        ///////////////



        $unbl_matchplayed = $this->getJS_Config('played_matches');

        if ($unbl_matchplayed) {
            //
            $single = 1;
            if ($this->s_id) {
                $tourn = $this->getTournOpt($this->s_id);
                $single = (isset($tourn->t_single)) ? ($tourn->t_single) : ('');
            }


            //
            $stat_array[0][0] = JText::_('BLFA_MATCHPLAYED');

            $query = "SELECT COUNT(*) FROM #__bl_squard as s, #__bl_match as m, #__bl_matchday as md WHERE md.id=m.m_id " . ($this->s_id ? " AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : '')) . " AND m.id=s.match_id AND m.m_played='1' AND s.mainsquard='1' AND s.player_id=" . $this->player_id;
            $this->db->setQuery($query);
            $gamepl = ($this->s_id || $seaslist) ? intval($this->db->loadResult()) : '';

            $query = "SELECT m.id FROM #__bl_squard as s, #__bl_match as m, #__bl_matchday as md WHERE md.id=m.m_id " . ($this->s_id ? " AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")) . " AND m.id=s.match_id AND m.m_played='1' AND s.mainsquard='1' AND s.player_id=" . $this->player_id;
            $this->db->setQuery($query);
            $mids = $this->db->loadColumn();
            if ($this->s_id == -1) {
                $single = 1;
            }
            if ($single) {
                $this->s_id ? $this->seassingle : array_push($this->seassingle, -1);

                // print_r($this->seassingle);
                $query = "SELECT COUNT(*)"
                        . " FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
                        . " WHERE m.m_id = md.id  AND m.published = 1 AND m.m_played='1' " . (($this->s_id < 0) ? " AND m.m_single = 1" : "") . "  " . ($this->s_id ? "AND md.s_id=" . $this->s_id : (count($this->seassingle) ? " AND md.s_id IN (" . implode(',', $this->seassingle) . ")" : "")) . "  AND m.team1_id = t1.id AND m.team2_id = t2.id " . ($this->player_id ? " AND (t1.id=" . $this->player_id . " OR t2.id=" . $this->player_id . ")" : "");

                $this->db->setQuery($query);
                $gamepl +=count($this->seassingle) > 1 ? intval($this->db->loadResult()) : 0;                
            }
            //print_r($this->s_id	);		
            $query = "SELECT COUNT(DISTINCT(m.id)) FROM #__bl_subsin as s, #__bl_match as m, #__bl_matchday as md WHERE md.id=m.m_id " . ($this->s_id ? " AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")) . " AND m.id=s.match_id AND m.m_played='1' AND s.player_in=" . $this->player_id;
            if (count($mids)) {
                $midss = implode(',', $mids);
                $query .= " AND m.id NOT IN (" . $midss . ")";
            }
            $this->db->setQuery($query);
            $gm = intval($this->db->loadResult());
            $gamepl += $gm;
            $stat_array[0][1] = $gamepl;
            $stat_array[0][2] = '&nbsp;';
        }

        for ($j = 0; $j < count($events); $j++) {
            $jn = $unbl_matchplayed ? ($j + 1) : ($j);
            $stat_array[$jn][0] = $events[$j]->e_name;

            $query_all = " FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md"
                    . " WHERE me.e_id = " . $events[$j]->id . " AND me.player_id = " . $this->player_id . " " . (count($teamss) ? " AND me.t_id IN(" . $teamss . ")" : " AND me.t_id = ''")
                    . " AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id " . ($this->s_id ? " AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : ""));
            //print_r($query_all);
            if ($events[$j]->result_type == '1') {

                $query = "SELECT AVG(me.ecount) " . $query_all;

                $this->db->setQuery($query);
            } else {

                $query = "SELECT SUM(me.ecount) " . $query_all;

                $this->db->setQuery($query);
            }
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            if ($events[$j]->player_event == '2') {

                $this->db->setQuery("SELECT SUM(me.ecount) FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md WHERE (me.e_id = " . $events[$j]->sumev1 . " OR me.e_id = " . $events[$j]->sumev2 . ") AND me.player_id = " . $this->player_id . " AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id " . ($this->s_id ? "AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")));
            }

            $stat_array[$jn][1] = floatval($this->db->loadResult());
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            if (!$stat_array[$jn][1]) {
                $stat_array[$jn][1] = 0;
            }
            $stat_array[$jn][2] = '&nbsp;';
            if ($events[$j]->e_img && is_file('media/bearleague/events/' . $events[$j]->e_img)) {
                // $stat_array[$jn][2] = '<img src="media/bearleague/events/'.$events[$j]->e_img.'" title="'.$events[$j]->e_name.'" height="20" />';
                $stat_array[$jn][2] = '<img class="team-embl  player-ico" ' . getImgPop($events[$j]->e_img, 6) . '  alt="' . $events[$j]->e_name . '" />';
            }
        }
        $this->_lists["stat_array"] = $stat_array;
    }

    function getSingleMatches() {
        $matches = array();
        $query = "SELECT s.s_id as id FROM #__bl_tournament as t, #__bl_seasons as s WHERE t.published='1' AND s.published='1' AND t.t_single='1' AND s.t_id = t.id ORDER BY t.name, s.s_name";
        $this->db->setQuery($query);
        $tourna = $this->db->loadColumn();
        if (count($tourna)) {
            if ($this->s_id) {
                $query = "SELECT m.*,md.m_name,md.id as mdid,m.id as mid,t1.first_name, t1.last_name,t1.nick,t2.first_name as fn2,t2.last_name as ln2,t2.nick as nick2,t1.id as hm_id,t2.id as aw_id"
                        . " FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
                        . " WHERE m.m_id = md.id AND m.published = 1 AND md.s_id=" . $this->s_id . " " . ($this->s_id == -1 ? "AND m.m_single=1 AND md.s_id = -1" : "AND md.s_id IN (" . implode(',', $tourna) . ")") . " AND m.team1_id = t1.id AND m.team2_id = t2.id " . ($this->player_id ? " AND (t1.id=" . $this->player_id . " OR t2.id=" . $this->player_id . ")" : "")
                        . " ORDER BY m.m_date,m.m_time,md.id";
            } else {
                $query = "SELECT m.*,md.m_name,md.id as mdid,m.id as mid,t1.first_name, t1.last_name,t1.nick,t2.first_name as fn2,t2.last_name as ln2,t2.nick as nick2,t1.id as hm_id,t2.id as aw_id"
                        . " FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
                        . " WHERE m.m_id = md.id AND m.published = 1 AND m.team1_id = t1.id AND (md.s_id IN (" . implode(',', $tourna) . ") OR (md.s_id=-1 AND m.m_single='1')) AND m.team2_id = t2.id " . ($this->player_id ? " AND (t1.id=" . $this->player_id . " OR t2.id=" . $this->player_id . ")" : "")
                        . " ORDER BY m.m_date,m.m_time,md.id";
            }
            $this->db->setQuery($query, ($this->limitstart - 1) * $this->limit, $this->limit);
            $matches = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
        }



        return $matches;
    }

    function getTeamName($player) {
        $Itemid = JRequest::getInt('Itemid');
        $query = "SELECT DISTINCT t.id,t.t_name FROM #__bl_teams as t, #__bl_players_team as p " . ($this->s_id ? ",#__bl_season_teams as pt" : "") . " WHERE p.team_id=t.id AND p.player_id=" . $player->id . " " . ($this->s_id ? " AND pt.season_id=" . $this->s_id . " AND pt.team_id=t.id AND p.season_id=" . $this->s_id : "") . " ORDER BY t.t_name";
        $this->db->setQuery($query);
        $teamzar = $this->db->loadObjectList(); //
        $teams = '';
        $mx = 0;
        if (count($teamzar)) {
            foreach ($teamzar as $tmz) {
                $link2 = JRoute::_("index.php?option=com_joomsport&task=team&tid=" . $tmz->id . "&sid=" . $this->s_id . "&Itemid=" . $Itemid);
                if ($mx) {
                    $teams .= "<br/>";
                }
                $teams .= '<a href="' . $link2 . '">' . $tmz->t_name . '</a> ';
                $mx++;
            }
        }
        if ($teams) {
            $teams = "" . $teams . "";
        }
        return $teams;
    }

}

