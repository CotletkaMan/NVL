<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class-matchday-race
 *
 * @author beardev
 */
class MatchdayRace {
    
    public $id = null;
    public $tourn = null;
    
    public function __construct($id, $tournament_obj) {
        $this->tourn = $tournament_obj;
        $this->id = $id;
    }
    public function editMd(){
        $db = JFactory::getDBO();
        $round_options = array();
        
        $round_options['participiants'] = $this->_getParticipiant();
        $round_options['options'] = json_decode($this->tourn->season_options);
        $round_options['tmpl'] = 'race';
        
        $query = " SELECT * FROM #__bl_rounds_extracol"
                ." WHERE round_id = ".$this->id
                ." ORDER BY ordering";
        $db->setQuery($query);
        $round_options['extracol'] = $db->loadObjectList();
        
        $rounds = array();
        
        if($this->id){
            $query = "Select * FROM #__bl_rounds WHERE md_id = ".intval($this->id);
            $query .= " ORDER BY ordering";
            $db->setQuery($query);
            $rounds =  $db->loadObjectList();
            
            for($intA = 0; $intA < count($rounds); $intA ++){
                if($this->tourn->t_single){
                    $query = "SELECT p.* FROM #__bl_rounds_participiants as p"
                            . " JOIN #__bl_players as t ON p.participiant_id=t.id "
                            . " WHERE p.round_id = ".intval($rounds[$intA]->id)
                            ." ORDER BY t.first_name, t.id";
                }else{
                    $query = "SELECT p.* FROM #__bl_rounds_participiants as p"
                            . " JOIN #__bl_teams as t ON p.participiant_id=t.id "
                            . " WHERE p.round_id = ".intval($rounds[$intA]->id)
                            ." ORDER BY t.t_name, t.id";
                    
                }
                
                $db->setQuery($query);
                $rounds[$intA]->res =  $db->loadObjectList();
            }
            
        }
        $round_options['rounds'] = $rounds;
        
        return $round_options;
    }
    
    public function saveMd(){
        $db = JFactory::getDBO();
        $post		= JRequest::get( 'post' );
        
        $query = " DELETE r,p"
                ." FROM #__bl_rounds as r"
                ." LEFT JOIN #__bl_rounds_participiants as p"
                ." ON r.id = p.round_id"
                ." WHERE r.md_id = ".$this->id;
        $db->setQuery($query);
        $db->query();
        
        // extra column
        
        $query = " DELETE FROM #__bl_rounds_extracol"
                ." WHERE round_id = ".$this->id;
        $db->setQuery($query);
        $db->query();
        
        $extracol_arr = array();
        
        if(isset($post['round_extra_col_id'])){
            $intZ = 0;
            foreach ($post['round_extra_col_id'] as $excol){
                
                $query = "INSERT INTO #__bl_rounds_extracol(round_id,name,ordering)"
                    ." VALUES({$this->id}, '".$post['round_extra_col'][$intZ]."',{$intZ})";
                $db->setQuery($query);
                $db->query();
                $extracol_arr[] = $excol;
                $intZ ++;
            }
        }
        
        
        if(isset($post['round_title'])){
            for($intA = 0; $intA < count($post['round_title']); $intA ++ ){
                
                $post['round_status'][$intA] = ($post['round_status'][$intA] == '1') ? $post['round_status_adv'][$intA] : $post['round_status'][$intA];
                
                $query = "INSERT INTO #__bl_rounds(round_title,round_status,md_id,ordering)"
                    ." VALUES('".$post['round_title'][$intA]."', '".$post['round_status'][$intA]."',{$this->id},{$intA})";
                $db->setQuery($query);
                $db->query();
                $round_id = $db->insertid();
                
                for($intB = 0; $intB < count($post['round_partic'][$intA]); $intB ++ ){
                    $attempts_val = '';
                    for($intC = 0; $intC < count($post['round_attempts'][$intA][$post['round_partic'][$intA][$intB]]); $intC ++ ){
                        if($intC){
                            $attempts_val .= "|";
                        }
                        $attempts_val .= $post['round_attempts'][$intA][$post['round_partic'][$intA][$intB]][$intC];
                    }
                    
                    $extracol_to_db = '';
                    if(count($extracol_arr)){
                        $intG = 0;
                        foreach($extracol_arr as $ex){
                            if($intG){
                                $extracol_to_db .= "|";
                            }
                            $extracol_to_db .= $post['extracol_input_'.$ex][$intA][$intB];
                            $intG ++;
                        }
                    }
                    
                    $result_str = $post['round_result'][$intA][$intB];
                    $result_int = floatval(str_replace(':', '', $result_str));
                    
                    $query = "INSERT INTO #__bl_rounds_participiants(round_id,participiant_id,attempts,result_string,result_num,penalty,extracol)"
                    ." VALUES({$round_id}, ".$post['round_partic'][$intA][$intB].",'".$attempts_val."','".$result_str."',{$result_int},'".($post['round_penalty'][$intA][$intB])."','".$extracol_to_db."')";
                    $db->setQuery($query);
                    $db->query();
                    
                }
                //order result
                $add_points = $post['round_status'][$intA] == '2' ? true : false;
                    $this->_orderRound($post, $round_id, $add_points);
                
            }
            
        }
        
    }
    
    private function _getParticipiant(){
        $db = JFactory::getDBO();
        
        if($this->tourn->t_single){
                $query = "SELECT CONCAT(t.first_name,' ',t.last_name) as t_name,t.id FROM #__bl_players as t , #__bl_season_players as st WHERE st.player_id = t.id AND st.season_id = ".($this->tourn->id)." ORDER BY t.first_name, t.id";
        }else{
                $query = "SELECT * FROM #__bl_teams as t , #__bl_season_teams as st WHERE st.team_id = t.id AND st.season_id = ".($this->tourn->id)." ORDER BY t.t_name, t.id";
        }
        
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    private function _orderRound($post, $round_id, $add_points){
        $db = JFactory::getDBO();
        
        $round_options = json_decode($this->tourn->season_options);
        $points_by_place = array();
        
        switch($round_options->race_points){
            case '1':
                if(isset($round_options->rnkpts_rank) && isset($round_options->rnkpts_pts)){
                    for($i = 0; $i < count($round_options->rnkpts_rank); $i ++){
                       if(isset($round_options->rnkpts_rank[$i]) && isset($round_options->rnkpts_pts[$i])){
                           $points_by_place[$round_options->rnkpts_rank[$i]] = $round_options->rnkpts_pts[$i];
                       }     
                    }
                }
                break;
            case '2':
                
                break;
        }
        
        $sort_way = $round_options->ordering_type == '0' ? 'desc' : 'asc'; 
        
        $query = "SELECT id,result_num FROM #__bl_rounds_participiants WHERE round_id = {$round_id} ORDER BY result_num {$sort_way},result_string {$sort_way}";
        $db->setQuery($query);
        $res = $db->loadObjectList();
         //var_dump($res);   
        
        for($intA = 1; $intA <= count($res); $intA ++){
            $points = 0;
            switch($round_options->race_points){
                case '1':
                    if(isset($points_by_place[$intA])){
                        $points = $points_by_place[$intA];
                    }
                    break;
                case '2':
                    if(isset($round_options->rnkpts_fromres) && isset($round_options->rnkpts_tores) && isset($round_options->rnkpts_results)){
                        for($i = 0; $i < count($round_options->rnkpts_fromres); $i ++){
                           if(isset($round_options->rnkpts_fromres[$i]) && isset($round_options->rnkpts_tores[$i]) && isset($round_options->rnkpts_results[$i])){
                               if($res[$intA-1]->result_num >= $round_options->rnkpts_fromres[$i] && $res[$intA-1]->result_num <= $round_options->rnkpts_tores[$i]){
                                   $points = $round_options->rnkpts_results[$i];
                                   break;
                               }
                           }     
                        }
                    }
                    break;
            }
            
            if (!$add_points){
                $points = 0;
            }
            
            $query = "UPDATE #__bl_rounds_participiants SET rank = {$intA}, points=".intval($points)
                    ." WHERE id = ".$res[$intA-1]->id." AND result_num != ''";
            $db->setQuery($query);
            $db->query();
        }
        //die();
    }
    
    function getRoundsById(){
        $db = JFactory::getDBO();
        
        $query = "Select * FROM #__bl_rounds WHERE md_id = ".intval($this->id);
        $query .= " ORDER BY ordering desc";
        $db->setQuery($query);
        $rounds =  $db->loadObjectList();

        for($intA = 0; $intA < count($rounds); $intA ++){
            
            if($this->tourn->t_single){
                $query = "Select r.*, CONCAT(t.first_name,' ',t.last_name) as t_name,t.id as t_id FROM #__bl_rounds_participiants as r"
                        ." JOIN #__bl_players as t"
                        ." ON r.participiant_id = t.id"
                    ." WHERE r.round_id = ".intval($rounds[$intA]->id)
                    ." ORDER BY r.rank asc";

            }else{
                $query = "Select r.*, t.t_name,t.id as t_id FROM #__bl_rounds_participiants as r"
                        ." JOIN #__bl_teams as t"
                        ." ON r.participiant_id = t.id"
                    ." WHERE r.round_id = ".intval($rounds[$intA]->id)
                    ." ORDER BY r.rank asc";
            }
            
            
            $db->setQuery($query);
            $rounds[$intA]->res =  $db->loadObjectList();
        }
        
        return $rounds;
    }
    
    
    
    static function getParticipantResults($s_id, $single, $partic_id){
        $db = JFactory::getDBO();
        
        if($s_id){
            $query = "SELECT m.m_name, m.start_date, m.end_date, r.round_title,p.rank,p.result_string"
                ." FROM #__bl_tournament as t"
                ." JOIN #__bl_seasons as s ON s.t_id = t.id"
                ." JOIN #__bl_matchday as m ON m.s_id = s.s_id"
                ." JOIN #__bl_rounds as r ON r.md_id = m.id"
                ." JOIN #__bl_rounds_participiants as p ON p.round_id = r.id"        
                ." WHERE r.round_status IN ('1', '2')"
                ." AND t.t_single = '".$single."'"
                ." AND p.participiant_id = {$partic_id}"
                ." AND m.s_id = {$s_id}"
                    . " ORDER BY m.start_date desc, m.id, r.ordering";
        }else{
            $query = "SELECT CONCAT(s.s_name,' ',m.m_name) as m_name, m.start_date, m.end_date, r.round_title,p.rank,p.result_string"
                ." FROM #__bl_tournament as t"
                ." JOIN #__bl_seasons as s ON s.t_id = t.id"
                ." JOIN #__bl_matchday as m ON m.s_id = s.s_id"
                ." JOIN #__bl_rounds as r ON r.md_id = m.id"
                ." JOIN #__bl_rounds_participiants as p ON p.round_id = r.id"        
                ." WHERE r.round_status IN ('1', '2')"
                ." AND t.t_single = '".$single."'"
                ." AND p.participiant_id = {$partic_id}"
                    . " ORDER BY m.start_date desc, m.id, r.ordering";
        }
        $db->setQuery($query);
        $rounds =  $db->loadObjectList();

        
        
        return $rounds;
    }
    
}
