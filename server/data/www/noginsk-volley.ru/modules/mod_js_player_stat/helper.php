<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class modBlPlayerStatHelper {

    public static function &getStatistic(&$params) {
        $db = JFactory::getDBO();
        $player_id = $params->get('player_id');
        $ssss_id = $params->get('sidgid');
        if ($ssss_id == 0) {
            $s_id = 0;
            $gr_id = 0;
        } else {
            $ex = explode('|', $ssss_id);
            $s_id = $ex[0];
            $gr_id = $ex[1];

            $query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name,t.t_single FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = " . ($s_id) . " AND s.t_id = t.id ORDER BY t.name, s.s_name";
            $db->setQuery($query);
            $tourn = $db->loadObjectList();

            $t_single = $tourn[0]->t_single;
        }

        $query = "SELECT * FROM #__bl_tournament WHERE published = '1' ORDER BY name";
        $db->setQuery($query);
        $tourn = $db->loadObjectList();
        $error = $db->getErrorMsg();
        if ($error) {
            return array();
        }

        $query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m WHERE m.m_id=md.id AND md.s_id= -1 AND m.m_single='1' AND (m.team1_id=" . $player_id . " OR m.team2_id=" . $player_id . ")";
        $db->setQuery($query);
        $frm = $db->loadResult();
        $error = $db->getErrorMsg();
        if ($error) {
            return array();
        }

        $query = "SELECT COUNT(*) FROM #__bl_matchday as md, #__bl_match as m, #__bl_squard as sc WHERE sc.match_id=m.id AND sc.player_id=" . $player_id . " AND m.m_id=md.id AND md.s_id= -1 ";
        $db->setQuery($query);
        $frm2 = $db->loadResult();
        $error = $db->getErrorMsg();
        if ($error) {
            return array();
        }
        $seassingle = $seasplayed = array();
        if ($frm || $frm2) {
            $seasplayed[] = -1;
        }        
        for ($i = 0; $i < count($tourn); $i++) {
            $tsingl = $tourn[$i]->t_single;
            if ($tsingl) {
                $query = "SELECT s.s_id as id,s.s_name as s_name"
                        . " FROM #__bl_season_players as sp, #__bl_seasons as s"
                        . " WHERE s.published = '1' AND s.t_id=" . $tourn[$i]->id . " AND s.s_id=sp.season_id AND sp.player_id=" . $player_id;
            } else {
                $query = "SELECT DISTINCT(s.s_id) as id,s.s_name as s_name"
                        . " FROM #__bl_seasons as s, #__bl_season_teams as st, #__bl_players_team as pt"
                        . " WHERE pt.confirmed='0' AND s.published = '1' AND s.t_id=" . $tourn[$i]->id . " AND st.season_id=s.s_id AND st.team_id=pt.team_id"
                        . " AND pt.season_id=s.s_id AND pt.player_id=" . $player_id
                        . "  ORDER BY s.s_name";
            }            
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            $error = $db->getErrorMsg();
            if ($error) {
                return array();
            }

            if (count($rows)) {
                for ($g = 0; $g < count($rows); $g++) {
                    $seasplayed[] = $rows[$g]->id;
                    if ($tsingl) {
                        $seassingle[] = $rows[$g]->id;
                    }
                }
            }
        }


        $seaslist = '';
        if (count($seasplayed)) {
            $seaslist = implode(',', $seasplayed);
        }

        $stat_array = array();

        $query = "SELECT DISTINCT(ev.id),ev.* FROM #__bl_events as ev, #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md"
                . " WHERE (ev.id = me.e_id OR (ev.sumev1 = me.e_id OR ev.sumev2 = me.e_id)) AND me.match_id = m.id"
                . " AND m.m_id=md.id " . ($s_id ? " AND md.s_id=" . $s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")) . " AND (ev.player_event = 1 OR ev.player_event = 2)"
                . " ORDER BY ev.ordering";
        $db->setQuery($query);
        $events = ($seaslist || $s_id) ? $db->loadObjectList() : array();
        $error = $db->getErrorMsg();
        if ($error) {
            return array();
        }

        $query = "SELECT team_id FROM #__bl_players_team as t WHERE t.player_id = '" . $player_id . "' " . ($s_id ? " AND t.season_id=" . $s_id : ($seaslist ? " AND t.season_id IN (" . $seaslist . ")" : "")) . "
                            UNION 
                          SELECT s.t_id FROM #__bl_match_events as s JOIN #__bl_match as m ON m.id=s.match_id JOIN #__bl_matchday as md ON (m.m_id=md.id " . ($s_id ? " AND md.s_id=" . $s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")) . ")";

        $db->setQuery($query);
        $teams = $db->loadColumn();

        $teamss = array();
        if (count($teams)) {
            $teamss = implode(',', $teams);
            if ($s_id == 0) {
                $teamss .= ",''";
            }
        }

        $unbl_matchplayed = self::getJS_Config('played_matches');

        if ($unbl_matchplayed) {
            $single = 1;
            if ($s_id) {
                $tourn = self::getTournOpt($s_id);
                $single = ($t_single) ? ($t_single) : ('');
            }

            $stat_array[0][0] = JText::_('BLFA_MATCHPLAYED');

            $query = "SELECT COUNT(*) FROM #__bl_squard as s, #__bl_match as m, #__bl_matchday as md WHERE md.id=m.m_id " . ($s_id ? " AND md.s_id=" . $s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : '')) . " AND m.id=s.match_id AND m.m_played='1' AND s.mainsquard='1' AND s.player_id=" . $player_id;
            $db->setQuery($query);
            $gamepl = ($s_id || $seaslist) ? intval($db->loadResult()) : '';

            $query = "SELECT m.id FROM #__bl_squard as s, #__bl_match as m, #__bl_matchday as md WHERE md.id=m.m_id " . ($s_id ? " AND md.s_id=" . $s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")) . " AND m.id=s.match_id AND m.m_played='1' AND s.mainsquard='1' AND s.player_id=" . $player_id;
            $db->setQuery($query);
            $mids = $db->loadColumn();
            if ($s_id == -1) {
                $single = 1;
            }
            if ($single) {
                $s_id ? $seassingle : array_push($seassingle, -1);
                $query = "SELECT COUNT(*)"
                        . " FROM #__bl_matchday as md, #__bl_match as m, #__bl_players as t1, #__bl_players as t2"
                        . " WHERE m.m_id = md.id AND m.m_single = 1 AND m.published = 1 AND m.m_played='1' " . (($s_id < 0) ? " AND m.m_single = 1" : "") . "  " . ($s_id ? "AND md.s_id=" . $s_id : (count($seassingle) ? " AND md.s_id IN (" . implode(',', $seassingle) . ")" : "")) . "  AND m.team1_id = t1.id AND m.team2_id = t2.id " . ($player_id ? " AND (t1.id=" . $player_id . " OR t2.id=" . $player_id . ")" : "");

                $db->setQuery($query);
                $gamepl +=count($seassingle) > 1 ? intval($db->loadResult()) : 0;
            }

            $query = "SELECT COUNT(DISTINCT(m.id)) FROM #__bl_subsin as s, #__bl_match as m, #__bl_matchday as md WHERE md.id=m.m_id " . ($s_id ? " AND md.s_id=" . $s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")) . " AND m.id=s.match_id AND m.m_played='1' AND s.player_in=" . $player_id;
            if (count($mids)) {
                $midss = implode(',', $mids);
                $query .= " AND m.id NOT IN (" . $midss . ")";
            }
            $db->setQuery($query);
            $gm = intval($db->loadResult());
            $gamepl += $gm;
            $stat_array[0][1] = $gamepl;
            $stat_array[0][2] = '&nbsp;';
        }

        for ($j = 0; $j < count($events); $j++) {
            $jn = $unbl_matchplayed ? ($j + 1) : ($j);
            $stat_array[$jn][0] = $events[$j]->e_name;

            $query_all = " FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md"
                    . " WHERE me.e_id = " . $events[$j]->id . " AND me.player_id = " . $player_id . " " . (count($teamss) ? " AND me.t_id IN(" . $teamss . ")" : " AND me.t_id = ''")
                    . " AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id " . ($s_id ? " AND md.s_id=" . $s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : ""));

            if ($events[$j]->result_type == '1') {

                $query = "SELECT AVG(me.ecount) " . $query_all;

                $db->setQuery($query);
            } else {

                $query = "SELECT SUM(me.ecount) " . $query_all;

                $db->setQuery($query);
            }
            $error = $db->getErrorMsg();
            if ($error) {
                return array();
            }
            if ($events[$j]->player_event == '2') {

                $db->setQuery("SELECT SUM(me.ecount) FROM #__bl_match_events as me, #__bl_match as m, #__bl_matchday as md WHERE (me.e_id = " . $events[$j]->sumev1 . " OR me.e_id = " . $events[$j]->sumev2 . ") AND me.player_id = " . $player_id . " AND me.match_id = m.id AND m.m_played = 1 AND md.id=m.m_id " . ($s_id ? "AND md.s_id=" . $s_id : ($seaslist ? " AND md.s_id IN (" . $seaslist . ")" : "")));
            }

            $stat_array[$jn][1] = floatval($db->loadResult());
            $error = $db->getErrorMsg();
            if ($error) {
                return array();
            }
            if (!$stat_array[$jn][1]) {
                $stat_array[$jn][1] = 0;
            }
            $stat_array[$jn][2] = '&nbsp;';
            if ($events[$j]->e_img && is_file('media/bearleague/events/' . $events[$j]->e_img)) {
                $stat_array[$jn][2] = '<img class="team-embl  player-ico" ' . getImgPop($events[$j]->e_img, 6) . '  alt="' . $events[$j]->e_name . '" />';
            }
        }
        
        return $stat_array;
    }

    public static function getTournOpt($sid) {
        $db = JFactory::getDBO();
        $query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name,t.t_single,s.s_enbl_extra,t.tournament_type,s.season_options FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = " . ($sid) . " AND s.t_id = t.id ORDER BY t.name, s.s_name";
        $db->setQuery($query);
        $tourn = $db->loadObject();
        return $tourn;
    }

    public static function getJS_Config($val) {
        $db = JFactory::getDBO();
        $query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='" . $val . "'";
        $db->setQuery($query);
        return $db->loadResult();
    }

}
