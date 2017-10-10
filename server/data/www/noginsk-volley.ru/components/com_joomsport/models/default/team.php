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

class teamJSModel extends JSPRO_Models {

    var $_data = null;
    var $_lists = null;
    var $s_id = null;
    var $team_id = null;
    var $limit = null;
    var $limitstart = null;
    var $_total = null;
    var $_pagination = null;
    var $limit2 = null;
    var $limitstart2 = null;
    var $_total2 = null;
    var $_pagination2 = null;
    var $_seaslist = null;

    function __construct() {
        parent::__construct();

        $this->team_id = JRequest::getVar('tid', 0, '', 'int');
        $this->s_id = JRequest::getVar('sid', 0, '', 'int');
        $this->limit = JRequest::getVar('jslimit', 20, '', 'int');
        $this->limitstart = JRequest::getVar('page', 1, '', 'int');
        $this->limitstart = intval($this->limitstart) > 1 ? $this->limitstart : 1;
        $this->limit2 = JRequest::getVar('jslimit2', 20, '', 'int');
        $this->limitstart2 = JRequest::getVar('page2', 1, '', 'int');
        $this->limitstart2 = intval($this->limitstart2) > 1 ? $this->limitstart2 : 1;
        $this->_lists['jscurtab'] = JRequest::getVar('jscurtab', 'etab_team', '', 'string');
        $this->sortfield = $this->mainframe->getUserStateFromRequest('com_joomsport.sortfield', 'sortfield', '', 'string');
        $this->sortdest = $this->mainframe->getUserStateFromRequest('com_joomsport.sortdest', 'sortdest', 0, 'string');

        $this->_lists['s_id'] = $this->s_id;
        //print_r($this->s_id);
        if (isset($_REQUEST['limitstart'])) {
            if ($_REQUEST['limitstart'] != 0) {
                $this->_lists['jscurtab'] = 'etab_match';
            }
        }

        $query = "SELECT t_name FROM #__bl_teams WHERE id = " . $this->team_id;
        $this->db->setQuery($query);
        $team_id = $this->db->loadResult();
        if (!$team_id) {
            JError::raiseError(403, JText::_('Access Forbidden'));
            return;
        }
    }

    function getData() {
        $user = JFactory::getUser();
        $Itemid = JRequest::getInt('Itemid');
        $query = "SELECT * FROM #__bl_teams WHERE id = " . $this->team_id;
        $this->db->setQuery($query);
        $team = $this->db->loadObject();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $this->_lists["team"] = $team;
        //club

        $query = "SELECT c_name FROM #__bl_club WHERE id = " . $team->club_id;
        $this->db->setQuery($query);
        $club_name = $this->db->loadResult();
        $this->_lists["team"]->club_name = $club_name;

        $query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 2 AND cat_id = " . $this->team_id;
        $this->db->setQuery($query);
        $this->_lists["photos"] = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        //tmp
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

        $query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id=md.id AND md.s_id= -1 AND m.m_single='0' AND (m.team1_id=" . $this->team_id . " OR m.team2_id=" . $this->team_id . ")";
        $this->db->setQuery($query);
        $frm = $this->db->loadResult();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        if ($frm) {
            $jqre .= '<option value="-1" ' . ((-1 == $this->s_id) ? "selected" : "") . '>' . JText::_('BLFA_FRIENDLY_MATCHES') . '</option>';
        }
        $this->_lists['tour_name_s'] = array();
        /////////!!!!!
        $ind_s = 0;
        for ($i = 0; $i < count($tourn); $i++) {
            $is_tourn2 = array();
            $query = "SELECT s.s_id as id,s.s_name as s_name FROM #__bl_seasons as s LEFT JOIN #__bl_tournament as t ON t.id = s.t_id, #__bl_season_teams as st WHERE s.published='1' AND st.team_id=" . $this->team_id . " AND s.s_id=st.season_id AND t.id=" . $tourn[$i]->id . "  ORDER BY s.s_name";
            $this->db->setQuery($query);
            $rows = $this->db->loadObjectList();

            if (count($rows)) {
                $ind_s = 1;
                $jqre .= '<optgroup label="' . htmlspecialchars($tourn[$i]->name) . '">'; ///this
                array_push($this->_lists['tour_name_s'], $tourn[$i]->name);
                for ($g = 0; $g < count($rows); $g++) {
                    $jqre .= '<option value="' . $rows[$g]->id . '" ' . (($rows[$g]->id == $this->s_id) ? "selected" : "") . '>' . $rows[$g]->s_name . '</option>';
                    $seasplayed[] = $rows[$g]->id;
                }
                $jqre .= '</optgroup>';
            }
        }
        $jqre .= '</select>';

        $this->_lists['tourn'] = $jqre;

        $seaslist = '';
        if (isset($seasplayed) && count($seasplayed)) {
            $seaslist = implode(',', $seasplayed);
        }
        if ($this->s_id == 0) {
            $seaslist = empty($seaslist) ? '-1' : $seaslist . ',-1';
        }
        $this->_seaslist = $seaslist;

        //pagination
        $this->getPagination();

        $this->_lists["def_img"] = '';
        if ($team->def_img) {
            $query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = " . $team->def_img;
            $this->db->setQuery($query);
            $this->_lists["def_img"] = $this->db->loadResult();
        } else if (isset($this->_lists["photos"][0])) {
            $this->_lists["def_img"] = $this->_lists["photos"][0]->filename;
        }
        if ($this->s_id) {
            $query = "SELECT md.m_name,m.id as mid,m.m_date,m.m_time, t1.t_name as home, t2.t_name as away,t1.id as home_id, t2.id as away_id, score1,score2,m.is_extra, m.m_played,t1.t_emblem as emb1,t2.t_emblem as emb2, m.betavailable, IF(CONCAT(m.betfinishdate, ' ', m.betfinishtime)>NOW(),1,0) betfinish FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND md.s_id = " . $this->s_id . " " . ($this->s_id == -1 ? "AND m.m_single = '0'" : "") . " AND m.published = 1 AND (m.team1_id = " . $this->team_id . " OR m.team2_id = " . $this->team_id . ") AND m.team1_id = t1.id AND m.team2_id = t2.id ORDER BY m.m_date,m.m_time";
        } else {
            $query = "SELECT DISTINCT(m.id),md.m_name,m.id as mid,m.m_date,m.m_time, t1.t_name as home, t2.t_name as away,t1.id as home_id, t2.id as away_id, m.score1,m.score2,m.is_extra, m.m_played,t1.t_emblem as emb1,t2.t_emblem as emb2, m.betavailable, IF(CONCAT(m.betfinishdate, ' ', m.betfinishtime)>NOW(),1,0) betfinish"
                    . " FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2, #__bl_seasons as s, #__bl_tournament as tr"
                    . " WHERE ((tr.id=s.t_id AND tr.t_single = '0' AND md.s_id=s.s_id " . ($seaslist ? " AND s.s_id IN (" . $seaslist . ")" : " AND s.s_id is NULL") . ") OR (md.s_id = -1 AND m.m_single='0')) AND m.m_id = md.id AND m.published = 1 AND (m.team1_id = " . $this->team_id . " OR m.team2_id = " . $this->team_id . ") AND m.team1_id = t1.id AND m.team2_id = t2.id AND s.published='1' AND tr.published='1'"
                    . " ORDER BY md.s_id,m.m_date,m.m_time";
        }

        $this->db->setQuery($query, ($this->limitstart - 1) * $this->limit, $this->limit);
        $this->_lists["matshes"] = $this->db->loadObjectList();
        $query = '';
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        //betting

        if (count($this->_lists["matshes"]) && $this->isBet()) {
            for ($z = 0; $z < count($this->_lists["matshes"]); $z++) {
                $this->_lists["matshes"][$z]->betevents = $this->getMatchBetEvents($this->_lists["matshes"][$z]->mid);
            }
        }

        $pllist_order = $this->getJS_Config('pllist_order');

        if ($pllist_order) {
            $pl_type_m = explode('_', $pllist_order);
            if (count($pl_type_m)) {
                $pl_id = isset($pl_type_m[0]) ? $pl_type_m[0] : null;
                $pl_type = isset($pl_type_m[1]) ? $pl_type_m[1] : null;
                if (isset($pl_type) && $pl_id && !$this->sortfield) {
                    $this->sortfield = $pl_type == 1 ? 'ef' . $pl_id : $pl_id;
                }
            }
        }
        if (!$this->sortfield) {
            $this->sortfield = 'name';
        }
        $sortdest_val = $this->sortdest ? "DESC" : "ASC";
        $sortdest_val_sel = $this->sortdest ? "ASC" : "DESC";

        if ($ind_s || $this->hasAnyFriendlySquad($this->team_id)) { //is registration in season? <----this
            switch ($this->sortfield) {
                case 'name':
                    if ($this->s_id == -1) {
                        $query = "SELECT DISTINCT z.* FROM (SELECT p.* FROM #__bl_players as p JOIN #__bl_squard as s ON (p.id=s.player_id AND s.team_id=" . $this->team_id . ") JOIN #__bl_match as m ON m.id=s.match_id JOIN #__bl_matchday as md ON (m.m_id=md.id AND md.s_id=-1) UNION "
                                . "SELECT p.* FROM #__bl_players as p JOIN #__bl_match_events as s ON (p.id=s.player_id AND s.t_id=" . $this->team_id . ") JOIN #__bl_match as m ON m.id=s.match_id AND m.m_played =1 JOIN #__bl_matchday as md ON (m.m_id=md.id AND md.s_id=-1) ) AS z ORDER BY z.first_name " . $sortdest_val . ",z.last_name ";
                    } elseif ($this->s_id == 0) {
                        $query = "SELECT DISTINCT z.* FROM (
                                                (SELECT p.* FROM #__bl_players as p JOIN #__bl_squard as s ON (p.id=s.player_id AND s.team_id=" . $this->team_id . ") JOIN #__bl_match as m ON m.id=s.match_id JOIN #__bl_matchday as md ON (m.m_id=md.id AND md.s_id=-1)) UNION 
                                                (SELECT p.* FROM #__bl_players as p JOIN #__bl_match_events as s ON (p.id=s.player_id AND s.t_id=" . $this->team_id . ") JOIN #__bl_match as m ON m.id=s.match_id AND m.m_played =1 JOIN #__bl_matchday as md ON (m.m_id=md.id AND md.s_id=-1)) UNION 
                                                (SELECT p.* FROM #__bl_players as p, #__bl_players_team as t WHERE t.confirmed='0' AND p.id=t.player_id AND t.team_id = " . $this->team_id . " " . ($this->s_id ? " AND t.season_id=" . $this->s_id : ($seaslist ? " AND t.season_id IN (" . $seaslist . ")" : " AND t.season_id is NULL")) . " AND t.player_join='0' )) AS z ORDER BY z.first_name " . $sortdest_val . ",z.last_name";
                    } else {
                        $query = "SELECT DISTINCT(p.id),p.* FROM #__bl_players as p, #__bl_players_team as t WHERE t.confirmed='0' AND p.id=t.player_id AND t.team_id = " . $this->team_id . " " . ($this->s_id ? " AND t.season_id=" . $this->s_id : ($seaslist ? " AND t.season_id IN (" . $seaslist . ")" : " AND t.season_id is NULL")) . " AND t.player_join='0' ORDER BY p.first_name " . $sortdest_val . ",p.last_name ";
                    }
                    break;
                case 'played':

                    if ($this->s_id != -1) {
                        $query = "SELECT p.*,COUNT(DISTINCT(m.id)) as esum FROM #__bl_players as p JOIN #__bl_players_team as t ON (p.id=t.player_id AND t.confirmed='0' AND t.team_id=" . $this->team_id . " " . ($this->s_id ? " AND t.season_id=" . $this->s_id : ($seaslist ? " AND t.season_id IN (" . $seaslist . ")" : "")) . ")"
                                . " LEFT JOIN ( #__bl_match as m  JOIN #__bl_matchday as md ON"
                                . " (m.m_played='1' AND md.id=m.m_id " . ($this->s_id ? " AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : ""))
                                . " AND (m.team1_id = " . $this->team_id . " OR m.team2_id = " . $this->team_id . "))"
                                . " LEFT JOIN #__bl_squard as s ON m.id=s.match_id"
                                . " LEFT JOIN #__bl_subsin as sb ON (m.id=sb.match_id))"
                                . " ON ((p.id=s.player_id AND s.mainsquard='1' AND s.team_id = " . $this->team_id . ") OR (sb.player_in=p.id AND s.team_id = " . $this->team_id . "))"
                                . " GROUP BY p.id "
                                . " ORDER BY esum " . $sortdest_val;
                    } else {
                        $query = "SELECT p.*,COUNT(DISTINCT(m.id)) as esum FROM #__bl_players as p JOIN #__bl_squard as t ON (p.id=t.player_id AND t.team_id=" . $this->team_id . ")"
                                . "  JOIN ( #__bl_match as m  JOIN #__bl_matchday as md ON"
                                . " (m.m_played='1' AND md.id=m.m_id AND md.s_id='-1'"
                                . " AND (m.team1_id = " . $this->team_id . " OR m.team2_id = " . $this->team_id . "))"
                                . " LEFT JOIN #__bl_subsin as sb ON (m.id=sb.match_id))"
                                . " ON ( t.match_id = m.id AND ((p.id=t.player_id AND t.mainsquard='1') OR (sb.player_in=p.id)))"
                                . " GROUP BY p.id "
                                . " ORDER BY esum " . $sortdest_val;
                    }

                    break;
                default :
                    if (substr($this->sortfield, 0, 2) == 'ef') {
                        $efid = intval(substr($this->sortfield, 2, strlen($this->sortfield)));
                        if ($this->s_id != -1) {
                            $query = "SELECT p.* FROM #__bl_players as p JOIN #__bl_players_team as t ON (p.id=t.player_id AND t.confirmed='0' AND t.team_id=" . $this->team_id . " " . ($this->s_id ? " AND t.season_id=" . $this->s_id : ($seaslist ? " AND t.season_id IN (" . $seaslist . ")" : "")) . ")"
                                    . " LEFT JOIN  #__bl_match as m ON (m.m_played='1' AND (m.team1_id = " . $this->team_id . " OR m.team2_id = " . $this->team_id . "))"
                                    . " LEFT JOIN #__bl_matchday as md ON"
                                    . " ( md.id=m.m_id " . ($this->s_id ? " AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")) . ")"
                                    . " LEFT JOIN (#__bl_extra_filds as ef "
                                    . " LEFT JOIN #__bl_extra_values as ev ON (ef.id=ev.f_id"
                                    . " AND ef.published=1 AND ef.type = '0' AND ef.e_table_view = '1' AND ef.id='" . $efid . "'"
                                    //." AND (ef.season_related == 0 OR (ef.season_related == 1 AND ev.season_id='".$this->s_id."' AND ev.season_id != 0))"
                                    . " AND ef.fdisplay = '1' " . ($user->get('guest') ? " AND ef.faccess='0'" : "") . ")"
                                    . " LEFT JOIN #__bl_extra_select as es ON (ef.field_type='3' AND ev.fvalue=es.id) )"
                                    . " ON ev.uid=p.id"
                                    . " GROUP BY p.id"
                                    . " ORDER BY IF(ef.field_type=2,ev.fvalue_text,IF(ef.field_type=3,es.eordering,ev.fvalue)) " . $sortdest_val; //$sortdest_val_sel
                        } else {
                            $query = "SELECT p.* FROM #__bl_players as p JOIN #__bl_squard as t ON (p.id=t.player_id AND t.team_id=" . $this->team_id . ")"
                                    . " JOIN  #__bl_match as m ON ((m.team1_id=t.team_id OR m.team2_id=t.team_id) AND m.m_played='1' AND t.match_id = m.id) JOIN #__bl_matchday as md ON"
                                    . " (md.id=m.m_id AND md.s_id='-1')"
                                    // ." AND (m.team1_id = ".$this->team_id." OR m.team2_id = ".$this->team_id."))"
                                    . " LEFT JOIN (#__bl_extra_filds as ef "
                                    . " LEFT JOIN #__bl_extra_values as ev ON (ef.id=ev.f_id"
                                    . " AND ef.published=1 AND ef.type = '0' AND ef.e_table_view = '1' AND ef.id='" . $efid . "'"
                                    //." AND (ef.season_related == 0 OR (ef.season_related == 1 AND ev.season_id='".$this->s_id."' AND ev.season_id != 0))"
                                    . " AND ef.fdisplay = '1' " . ($user->get('guest') ? " AND ef.faccess='0'" : "") . ")"
                                    . " LEFT JOIN #__bl_extra_select as es ON (ef.field_type='3' AND ev.fvalue=es.id) )"
                                    . " ON ev.uid=p.id"
                                    . " GROUP BY p.id"
                                    . " ORDER BY IF(ef.field_type=2,ev.fvalue_text,IF(ef.field_type=3,es.sel_value,ev.fvalue)) " . $sortdest_val;
                        }
                    } else {
                        //print_r($this->sortfield);////
                        $query = "SELECT * FROM #__bl_events  WHERE id='" . $this->sortfield . "'";
                        $this->db->setQuery($query);
                        $event = $this->db->loadObject();
                        //print_r($event);///
                        if (isset($event)) {
                            $sum = ($event->result_type == 1) ? "AVG(me.ecount)" : "SUM(me.ecount)";
                            if ($this->s_id == -1) {
                                $query = "SELECT p.*," . $sum . " as esum FROM #__bl_players as p JOIN #__bl_squard as t ON (p.id=t.player_id AND t.team_id=" . $this->team_id . ")"
                                        . " JOIN #__bl_match as m ON ((m.team1_id=t.team_id OR m.team2_id=t.team_id) AND m.m_played='1' AND t.match_id = m.id) JOIN #__bl_matchday as md ON (m.m_id=md.id AND md.s_id=-1)"
                                        . " LEFT JOIN #__bl_match_events as me ON (me.match_id = m.id AND me.player_id = p.id AND t.team_id=me.t_id AND "
                                        . " " . ($event->player_event == '2' ? "(me.e_id = " . $event->sumev1 . " OR me.e_id = " . $event->sumev2 . ")" : "me.e_id = '" . intval($this->sortfield) . "'") . ")"
                                        . " WHERE m.m_played = 1"
                                        . " GROUP BY p.id"
                                        . " ORDER BY esum " . $sortdest_val . ",p.first_name,p.last_name";
                            } elseif ($this->s_id == 0) {
                                $query_p = "SELECT p.id FROM #__bl_players as p JOIN #__bl_players_team as t ON (p.id=t.player_id AND t.team_id=" . $this->team_id . ")"
                                        . " WHERE t.confirmed='0'"
                                        . " GROUP BY p.id";
                                $this->db->setQuery($query_p);
                                $players_t = $this->db->loadColumn();

                                $query = "SELECT p.*," . $sum . " as esum FROM #__bl_players as p "
                                        . " LEFT JOIN (#__bl_matchday as md"
                                        . "  JOIN #__bl_match as m ON (m.m_id=md.id AND m.m_played = 1)"
                                        . "  JOIN #__bl_match_events as me ON (me.match_id = m.id   AND "
                                        . " " . ($event->player_event == '2' ? "(me.e_id = " . $event->sumev1 . " OR me.e_id = " . $event->sumev2 . ")" : "me.e_id = '" . intval($this->sortfield) . "'") . ") )"
                                        . " ON ( (m.team1_id=" . $this->team_id . " OR m.team2_id=" . $this->team_id . ") AND " . $this->team_id . "=me.t_id AND me.player_id = p.id)"
                                        . " WHERE p.id IN (" . implode(',', $players_t) . ")"
                                        //."".($this->s_id?" AND t.season_id=".$this->s_id:($seaslist?" AND t.season_id IN (".$seaslist.")":" AND t.season_id is NULL"))." AND t.player_join='0'"
                                        . " GROUP BY p.id"
                                        . " ORDER BY esum " . $sortdest_val . ",p.first_name,p.last_name";
                            } else {

                                $query = "SELECT p.*," . $sum . " as esum FROM #__bl_players as p JOIN #__bl_players_team as t ON (p.id=t.player_id AND t.team_id=" . $this->team_id . " " . ($this->s_id ? " AND t.season_id=" . $this->s_id : ($seaslist ? " AND t.season_id IN (" . $seaslist . ")" : "")) . ")"
                                        . " LEFT JOIN (#__bl_matchday as md"
                                        . "  JOIN #__bl_match as m ON (m.m_id=md.id AND m.m_played = 1)"
                                        . "  JOIN #__bl_match_events as me ON (me.match_id = m.id   AND "
                                        . " " . ($event->player_event == '2' ? "(me.e_id = " . $event->sumev1 . " OR me.e_id = " . $event->sumev2 . ")" : "me.e_id = '" . intval($this->sortfield) . "'") . ") )"
                                        . " ON (md.s_id = t.season_id AND (m.team1_id=t.team_id OR m.team2_id=t.team_id) AND t.team_id=me.t_id AND me.player_id = p.id)"
                                        . " WHERE t.confirmed='0'"
                                        //."".($this->s_id?" AND t.season_id=".$this->s_id:($seaslist?" AND t.season_id IN (".$seaslist.")":" AND t.season_id is NULL"))." AND t.player_join='0'"
                                        . " GROUP BY p.id"
                                        . " ORDER BY esum " . $sortdest_val . ",p.first_name,p.last_name";
                            }
                        } else {
                            return JError::raiseError(500, '');
                        }
                    }
            }
        }
        // print_r($query);die;
        $players = null;
        if ($query) {
            $this->db->setQuery($query, ($this->limitstart2 - 1) * $this->limit2, $this->limit2);
            $players = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
        }

        $query = "SELECT DISTINCT(ev.id),ev.* FROM #__bl_events as ev, #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md WHERE (ev.id = me.e_id OR (ev.sumev1 = me.e_id OR ev.sumev2 = me.e_id)) AND me.match_id = m.id AND m.m_id=md.id " . ($this->s_id ? "AND md.s_id=" . $this->s_id : "") . " AND (ev.player_event = 1 OR ev.player_event = 2) ORDER BY ev.ordering";
        $this->db->setQuery($query);
        $events = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        $this->_lists["events"] = $events;




        $unbl_matchplayed = $this->getJS_Config('played_matches');
        $this->_lists["unbl_matchplayed"] = $unbl_matchplayed;

        //- -- CREATE OUTPUT TABLE
        $player_table = array();

        $query = "SELECT ef.name,ef.id FROM #__bl_extra_filds as ef  WHERE ef.published=1 AND ef.type = '0' AND ef.fdisplay = '1' AND ef.e_table_view = '1' " . ($user->get('guest') ? " AND ef.faccess='0'" : "") . " ORDER BY ef.ordering";
        $this->db->setQuery($query);

        $ext_fields_name = $this->db->loadObjectList();
        $this->_lists["ext_fields_name"] = $ext_fields_name;
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }

        $sort_arg = 0;

        for ($i = 0; $i < count($players); $i++) {

            if (!isset($players[$i]->id)) {
                $players[$i]->id = 0;
            }
            $query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = " . $players[$i]->id;
            $this->db->setQuery($query);
            $photos2 = $this->db->loadObjectList();

            $def_img2 = '';
            if (!empty($players[$i]->def_img)) {
                $query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = " . $players[$i]->def_img;
                $this->db->setQuery($query);
                $def_img2 = $this->db->loadResult();
            } else if (isset($photos2[0])) {
                $def_img2 = $photos2[0]->filename;
            }
            $players[$i]->img = $def_img2;
            //print_R($players[$i]);
            $players[$i]->name = (isset($players[$i]->first_name)) ? $this->selectPlayerName($players[$i]) : '';

            if ($unbl_matchplayed) {
                $query = "SELECT COUNT(*) FROM #__bl_squard as s, #__bl_match as m, #__bl_matchday as md WHERE md.id=m.m_id " . ($this->s_id ? " AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")) . " AND m.id=s.match_id AND m.m_played='1' AND s.mainsquard='1' AND (m.team1_id = " . $this->team_id . " OR m.team2_id = " . $this->team_id . ") AND s.team_id = " . $this->team_id . " AND s.player_id=" . $players[$i]->id;
                $this->db->setQuery($query);
                $gamepl = intval($this->db->loadResult());
                $query = "SELECT m.id FROM #__bl_squard as s, #__bl_match as m, #__bl_matchday as md WHERE md.id=m.m_id " . ($this->s_id ? " AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")) . " AND m.id=s.match_id AND m.m_played='1' AND s.mainsquard='1' AND (m.team1_id = " . $this->team_id . " OR m.team2_id = " . $this->team_id . ") AND s.team_id = " . $this->team_id . " AND s.player_id=" . $players[$i]->id;
                $this->db->setQuery($query);
                $mids = $this->db->loadColumn();

                $query = "SELECT COUNT(DISTINCT(m.id)) FROM #__bl_subsin as s, #__bl_match as m, #__bl_matchday as md WHERE md.id=m.m_id " . ($this->s_id ? " AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")) . " AND m.id=s.match_id AND m.m_played='1' AND (m.team1_id = " . $this->team_id . " OR m.team2_id = " . $this->team_id . ") AND s.team_id = " . $this->team_id . " AND s.player_in='" . $players[$i]->id . "'";
                if (count($mids)) {
                    $midss = implode(',', $mids);
                    $query .= " AND m.id NOT IN (" . $midss . ")";
                }

                $this->db->setQuery($query);
                $gamepl += intval($this->db->loadResult());
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
                $players[$i]->played = $gamepl;
            }


            for ($j = 0; $j < count($events); $j++) {

                if ($events[$j]->result_type == '1') {
                    $this->db->setQuery("SELECT AVG(me.ecount) FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md WHERE me.e_id = " . $events[$j]->id . " AND me.t_id=" . $this->team_id . " AND me.player_id = '" . $players[$i]->id . "' AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id " . ($this->s_id ? "AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")));
                } else {
                    $this->db->setQuery("SELECT SUM(me.ecount) FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md WHERE me.e_id = " . $events[$j]->id . " AND me.t_id=" . $this->team_id . " AND me.player_id = '" . $players[$i]->id . "' AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id " . ($this->s_id ? "AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")));
                }

                if ($events[$j]->player_event == '2') {
                    $this->db->setQuery("SELECT SUM(me.ecount) FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md WHERE (me.e_id = " . $events[$j]->sumev1 . " OR me.e_id = " . $events[$j]->sumev2 . ") AND me.t_id=" . $this->team_id . " AND me.player_id = '" . $players[$i]->id . "' AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id " . ($this->s_id ? "AND md.s_id=" . $this->s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")));
                }

                $curcount = $this->db->loadResult();
                $error = $this->db->getErrorMsg();
                if ($error) {
                    return JError::raiseError(500, $error);
                }
// now friendly matches has team_id so its events already got to $curcount
//                $this->db->setQuery("SELECT SUM(me.ecount) FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md WHERE me.e_id = " . $events[$j]->id . " AND me.t_id = " . $this->team_id . " AND me.player_id = '" . $players[$i]->id . "' AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id AND md.s_id=-1");
//                $f_curcount = $this->db->loadResult();
//                $curcount += $f_curcount;
//                echo $this->db->getQuery();
                $players[$i]->events[] = floatval($curcount) ? floatval($curcount) : 0;
            }
            //////////////////////////////////////////////////////////////EXTRA!!! 
            $query = "SELECT * FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid='" . $players[$i]->id . "' WHERE ef.published=1 AND ef.type = '0' AND ef.e_table_view = '1' AND ef.fdisplay = '1' " . ($user->get('guest') ? " AND ef.faccess='0'" : "") . "  AND ((ef.season_related = 0 AND ev.season_id=0) OR (ef.season_related = 1 AND ev.season_id='" . $this->s_id . "' AND ev.season_id != 0)) ORDER BY ef.ordering";
            $this->db->setQuery($query);

            $ext_pl_z = $this->db->loadObjectList();
            $this->_list["ext_pl_z"] = $ext_pl_z;
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
            $mj = 0;
            if (isset($ext_pl_z)) {
                for ($j = 0; $j < count($ext_fields_name); $j++) {
                    foreach ($ext_pl_z as $extr) { //ext_fields_name
                        if ($ext_fields_name[$j]->id == $extr->id) {
                            if ($extr->field_type == '3' && $extr->fvalue) {

                                $query = "SELECT sel_value FROM #__bl_extra_select WHERE id='" . $extr->fvalue . "'";
                                $this->db->setQuery($query);
                                $selvals = $this->db->loadResult();

                                if (isset($selvals) && $selvals) {
                                    $ext_pl[$mj] = $selvals;
                                } else {
                                    $ext_pl[$mj] = '&nbsp;';
                                }
                            } else if ($extr->field_type == '1') {
                                $ext_pl[$mj] = $extr->fvalue ? JText::_("Yes") : JText::_("No");
                            } else if ($extr->field_type == '2') {
                                $ext_pl[$mj] = $extr->fvalue_text ? $extr->fvalue_text : '&nbsp;';
                            } else {
                                $ext_pl[$mj] = $extr->fvalue ? $extr->fvalue : '&nbsp;';
                            }
                            if ($extr->field_type == '4' && $extr->fvalue) {
                                $ext_pl[$mj] = "<a target='_blank' href='" . (substr($extr->fvalue, 0, 7) == 'http://' ? $extr->fvalue : "http://" . $extr->fvalue) . "'>" . $extr->fvalue . "</a>";
                            }
                            $players[$i]->extraf[$j] = $ext_pl[$mj];
                        }

                        $mj++;
                    }
                }
            }
        }


        $this->_lists["players"] = $players;



        if ($this->s_id && $this->s_id != -1) {

            $this->_lists["unable_reg"] = $this->unblSeasonReg();
        } else {
            $this->_lists["enbl_extra"] = 1;
        }
        $this->_lists["teams_season"] = $this->teamsToModer();


        $this->_lists['locven'] = $this->getJS_Config("cal_venue");
        $this->_lists["teams_season"] = $this->teamsToModer();
        $this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"], @$this->_lists["unable_reg"], $this->s_id, 1, $this->team_id);
        ///-----EXTRAFIELDS---//

        $this->_lists['ext_fields'] = $this->getAddFields($this->team_id, '1', "team", $this->s_id);

        //social buttons
        $ogimg = '';
        if ($this->_lists['team']->t_emblem && is_file('media/bearleague/' . $this->_lists['team']->t_emblem)) {
            $ogimg = JURI::base() . 'media/bearleague/' . $this->_lists['team']->t_emblem;
        }
        $this->_lists['socbut'] = $this->getSocialButtons('jsbp_team', $this->_lists['team']->t_name, $ogimg, htmlspecialchars(strip_tags($this->_lists['team']->t_descr)));
        ////
        ///
        if ($this->getJS_Config("unbl_venue") && $team->venue_id) {
            $query = "SELECT * FROM #__bl_venue WHERE id={$team->venue_id}";
            $this->db->setQuery($query);
            $this->_lists["venue"] = $this->db->loadObject();
        }

        $this->_lists["tourn_name"] = $this->getTournName($this->s_id);
//print_r($this->_lists["tourn_name"]);//update
        
        
        //version 3.3 race tourn
        
        $this->_lists["race_res"] = MatchdayRace::getParticipantResults($this->s_id, 0, $this->team_id);
        
    }

    function getPagination() {
        if (empty($this->_pagination)) {
            $this->_pagination = new JS_Pagination($this->getTotal(), $this->limitstart, $this->limit);
        }

        if (empty($this->_pagination2)) {
            $this->_pagination2 = new JS_Pagination($this->getTotal2(), $this->limitstart2, $this->limit2);
        }
        $this->_lists["limitstart2"] = $this->limitstart2;
    }

    function getTotal() {
        if (empty($this->_total)) {
            if ($this->s_id) {
                $query = "SELECT COUNT(m.id) FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2 WHERE m.m_id = md.id AND md.s_id = " . $this->s_id . " AND m.published = 1 AND (m.team1_id = " . $this->team_id . " OR m.team2_id = " . $this->team_id . ") AND m.team1_id = t1.id AND m.team2_id = t2.id";
            } else {
                $query = "SELECT COUNT(m.id) FROM #__bl_matchday as md, #__bl_match as m, #__bl_teams as t1, #__bl_teams as t2, #__bl_seasons as s, #__bl_tournament as tr WHERE tr.id=s.t_id AND tr.t_single = '0' AND md.s_id=s.s_id AND m.m_id = md.id AND m.published = 1 AND (m.team1_id = " . $this->team_id . " OR m.team2_id = " . $this->team_id . ") AND m.team1_id = t1.id AND m.team2_id = t2.id AND s.published='1' AND tr.published='1'";
            }

            $this->db->setQuery($query);
            $this->_total = $this->db->loadResult();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
        }

        return $this->_total;
    }

    function getTotal2() {
        if (empty($this->_total2)) {
            if ($this->s_id == -1) {
                $query = "SELECT COUNT(DISTINCT(p.id)) FROM #__bl_players as p JOIN #__bl_squard as s ON (p.id=s.player_id AND s.team_id=" . $this->team_id . ") JOIN #__bl_match as m ON m.id=s.match_id JOIN #__bl_matchday as md ON (m.m_id=md.id AND md.s_id=-1) ORDER BY p.first_name,p.last_name";
            } else {
                $query = "SELECT COUNT(DISTINCT(p.id)) FROM #__bl_players as p, #__bl_players_team as t WHERE t.confirmed='0' AND p.id=t.player_id AND t.team_id = " . $this->team_id . " " . ($this->s_id ? " AND t.season_id=" . $this->s_id : ($this->_seaslist ? " AND t.season_id IN (" . $this->_seaslist . ")" : " AND t.season_id is NULL")) . " AND t.player_join='0' ORDER BY p.first_name,p.last_name";
            }

            $this->db->setQuery($query);
            $this->_total2 = $this->db->loadResult();
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
        }

        return $this->_total2;
    }

    //bettings
    function getMatchBetEvents($idmatch) {
        $query = "SELECT bbc.*, bbe.*"
                . "\n FROM #__bl_betting_events bbe"
                . "\n INNER JOIN #__bl_betting_coeffs bbc ON bbc.idevent=bbe.id"
                . "\n WHERE bbc.idmatch =" . $idmatch;

        $this->db->setQuery($query);
        $matchevents = $this->db->loadObjectList();

        return $matchevents;
    }

    function saveBets() {
        $betmatches = JRequest::getVar('bet_match');
        $bet_events_radio = JRequest::getVar('betevents_radio');
        $bet_events_points1 = JRequest::getVar('betevents_points1');
        $bet_events_points2 = JRequest::getVar('betevents_points2');
        if ($betmatches) {
            $userpoints = $this->getUserPoints(JFactory::getUser()->get('id'));
            $points = 0;
            $matches = array();
            foreach ($betmatches as $idmatch) {
                $match = new JTableMatch($this->db);
                $match->load($idmatch);
                print_r($match);
                die;
                if ($match->betfinishdate . ' ' . $match->betfinishtime > date('Y-m-d H:i') && $match->betavailable) {
                    $matches[] = $match;
                    if ($bet_events_radio[$idmatch]) {
                        foreach ($bet_events_radio[$idmatch] as $idevent => $value) {
                            $points += (float) $bet_events_points1[$idmatch][$idevent] + (float) $bet_events_points2[$idmatch][$idevent];
                        }
                    }
                }
            }
            if ($userpoints < $points) {
                return BLFA_BET_NOT_ENOUGH_POINTS;
            }

            if ($matches) {
                foreach ($matches as $match) {
                    $idmatch = $match->id;
                    if ($bet_events_radio[$idmatch]) {
                        foreach ($bet_events_radio[$idmatch] as $idevent => $value) {
                            $who = 0;
                            if ((float) $bet_events_points1[$idmatch][$idevent]) {
                                $currentbetpoints = (float) $bet_events_points1[$idmatch][$idevent];
                                $who = 1;
                            } elseif ((float) $bet_events_points2[$idmatch][$idevent]) {
                                $currentbetpoints = (float) $bet_events_points2[$idmatch][$idevent];
                                $who = 2;
                            }
                            $this->saveBet($currentbetpoints, $idmatch, $idevent, $who);
                        }
                    }
                }
            }
        }
        return 1;
    }

}

