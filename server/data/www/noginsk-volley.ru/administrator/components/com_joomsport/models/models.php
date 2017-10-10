<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.model');

class JSPRO_Models{
	
	public $db = null;
	public $uri = null;
	public $mainframe = null;
	
	protected $js_table = null;
	
	function __construct()
	{
		$this->db		= JFactory::getDBO();
		$this->uri		= JFactory::getURI();
		$this->mainframe = JFactory::getApplication();
	}
	function getJS_Config($val){
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='".$val."'";
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
	
	function js_publish($table,$cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "UPDATE `".$table."` SET published = '1' WHERE id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
		}
		
	}
	
	function js_unpublish($table,$cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "UPDATE `".$table."` SET published = '0' WHERE id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}
		}
	}
	
	function js_delete($table,$cid,$task){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "DELETE FROM `".$table."` WHERE id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
			$error = $this->db->getErrorMsg();
			if ($error)
			{
				return JError::raiseError(500, $error);
			}

            switch($task){
                case "fields_list":
                    $query = "DELETE FROM `#__bl_extra_select`  WHERE fid IN (".$cids.")";
                    $this->db->setQuery($query);
                    $this->db->query();

                    $query = "DELETE FROM `#__bl_extra_values`  WHERE f_id IN (".$cids.")";

                    break;
                default:break;
            }
            $this->db->setQuery($query);
            $this->db->query();

		}
	}
    ///COPY
    function js_copy($table,$cid,$task){
        if(count($cid)){
            $cids = implode(',',$cid);
            $query = "SELECT * FROM `".$table."` WHERE s_id IN (".$cids.")";///s_id rewrite in id/ ONLY SEASON!!!!!
            $this->db->setQuery($query);
            $seas_list = $this->db->loadObjectList();
            $error = $this->db->getErrorMsg();
            if ($error)
            {
                return JError::raiseError(500, $error);
            }
            if(count($seas_list)){
                foreach($seas_list as $seas){
                    //print_r((array)$seas); echo "<hr>";
                    $seas->s_name =  $seas->s_name."_copy";
                    $q = (array)$seas;
                    array_shift($q);
                    $seas_val = implode("','",$q);

                   $query = "INSERT INTO `".$table."` VALUE('','".$seas_val."')";
                   $this->db->setQuery($query);
                   $this->db->query();

                   $query = "SELECT @@IDENTITY AS 'Identity'";
                   $this->db->setQuery($query);
                   $res = $this->db->loadResult();


                    switch($task){
                        case 'season_list':
                            $query = "SELECT * FROM #__bl_season_option WHERE s_id = ".$seas->s_id;
                            $this->db->setQuery($query);
                            $sopt_list = $this->db->loadObjectList();
                            if(count($sopt_list)){
                                foreach($sopt_list as $sopt){
                                    $o = (array)$sopt;
                                    array_shift($o);
                                    $sopt_val = implode("','",$o);
                                    $query = "INSERT INTO `#__bl_season_option` VALUE('".$res."','".$sopt_val."')";
                                    $this->db->setQuery($query);
                                    $this->db->query();

                                }
                            }

                            $query = "SELECT * FROM #__bl_season_players WHERE season_id = ".$seas->s_id;
                            $this->db->setQuery($query);
                            $splayer_list = $this->db->loadObjectList();
                            if(count($splayer_list)){
                                foreach($splayer_list as $splayer){
                                    $query = "INSERT INTO `#__bl_season_players` VALUE('".$splayer->player_id."','".$res."','0','".$splayer->regtype."')";
                                    $this->db->setQuery($query);
                                    $this->db->query();

                                }
                            }

                            $query = "SELECT * FROM #__bl_season_teams WHERE season_id = ".$seas->s_id;
                            $this->db->setQuery($query);
                            $steam_list = $this->db->loadObjectList();
                            if(count($steam_list)){
                                foreach($steam_list as $steams){
                                    $query = "INSERT INTO `#__bl_season_teams` VALUE('".$res."','".$steams->team_id."','0','".$steams->regtype."')";
                                    $this->db->setQuery($query);
                                    $this->db->query();
                                    $teams_ids[] =$steams->team_id;
                                }
                                //copy players team

                                $query = "SELECT * FROM #__bl_players_team WHERE team_id IN(".implode(',',$teams_ids).") AND season_id = ".$seas->s_id;
                                $this->db->setQuery($query);
                                $tplayers_list = $this->db->loadObjectList();
                                if(count($tplayers_list)){
                                    foreach($tplayers_list as $tplayers){
                                        $query = "INSERT INTO `#__bl_players_team` VALUE('".$tplayers->team_id."','".$tplayers->player_id."','".$tplayers->confirmed."','".$res."','".$tplayers->invitekey."','".$tplayers->player_join."')";
                                        $this->db->setQuery($query);
                                        $this->db->query();
                                    }
                                }

                             }

                            $query = "SELECT * FROM #__bl_seas_maps WHERE season_id = ".$seas->s_id;
                            $this->db->setQuery($query);
                            $smap_list = $this->db->loadObjectList();
                            if(count($smap_list)){
                                foreach($smap_list as $smaps){
                                    $query = "INSERT INTO `#__bl_seas_maps` VALUE('".$res."','".$smaps->map_id."')";
                                    $this->db->setQuery($query);
                                    $this->db->query();
                                }
                            }

                            $query = "SELECT * FROM #__bl_feadmins WHERE season_id = ".$seas->s_id;
                            $this->db->setQuery($query);
                            $sadm_list = $this->db->loadObjectList();
                            if(count($sadm_list)){
                                foreach($sadm_list as $sadm){
                                    $query = "INSERT INTO `#__bl_feadmins` VALUE('".$sadm->user_id."','".$res."')";
                                    $this->db->setQuery($query);
                                    $this->db->query();
                                }
                            }

                            $query = "SELECT ev.* FROM #__bl_extra_filds as ef, #__bl_extra_values as ev WHERE ef.id = ev.f_id AND ef.type = 3 AND ev.uid = ".$seas->s_id;
                            $this->db->setQuery($query);
                            $extra_s = $this->db->loadObjectList();
                            if(count($extra_s)){
                                foreach($extra_s as $exs){
                                    $query = "INSERT INTO `#__bl_extra_values` VALUE('".$exs->f_id."','".$res."','".$exs->fvalue."','".$exs->fvalue_text."','".$exs->season_id."')";
                                    $this->db->setQuery($query);
                                    $this->db->query();
                                }
                            }
                            $query = "SELECT * FROM `#__bl_extra_values` WHERE season_id = ".$seas->s_id;
                            $this->db->setQuery($query);
                            $extra_sval = $this->db->loadObjectList();
                            if(count($extra_sval)){
                                foreach($extra_sval as $sval){
                                    $query = "INSERT INTO `#__bl_extra_values` VALUE('".$sval->f_id."','".$sval->uid."','".$sval->fvalue."','".$sval->fvalue_text."','".$res."')";
                                    $this->db->setQuery($query);
                                    $this->db->query();
                                }
                            }


                            $query = "SELECT * FROM #__bl_ranksort WHERE seasonid = ".$seas->s_id;
                            $this->db->setQuery($query);
                            $srank_list = $this->db->loadObjectList();
                            if(count($srank_list)){
                                foreach($srank_list as $srank){
                                    $query = "INSERT INTO `#__bl_ranksort` VALUE('".$res."','".$srank->sort_field."','".$srank->sort_way."','".$srank->ordering."')";
                                    $this->db->setQuery($query);
                                    $this->db->query();
                                }
                            }

                            $query = "SELECT * FROM #__bl_groups WHERE s_id = ".$seas->s_id;
                            $this->db->setQuery($query);
                            $group_list = $this->db->loadObjectList();
                            if(count($group_list)){
                                foreach($group_list as $group){
                                    $query = "INSERT INTO `#__bl_groups` VALUE('','".$group->group_name."_copy','".$res."','".$group->ordering."')";
                                    $this->db->setQuery($query);
                                    $this->db->query();

                                    $query = "SELECT @@IDENTITY AS 'Identity'";
                                    $this->db->setQuery($query);
                                    $grres = $this->db->loadResult();

                                    $query = "SELECT * FROM #__bl_grteams WHERE g_id = ".$group->id."";
                                    $this->db->setQuery($query);
                                    $group_part = $this->db->loadObjectList();
                                    foreach($group_part as $part){
                                        $query = "INSERT INTO `#__bl_grteams` VALUE('".$grres."','".$part->t_id."')";
                                        $this->db->setQuery($query);
                                        $this->db->query();
                                    }

                                }





                            }





                            break;
                        default:break;
                    }

                }

            }

        }
    }
	
	
	function uploadFile( $filename, $userfile_name, $dir = '') 
	{
		$msg = '';
		if(!$dir){
			$baseDir =  JPATH_ROOT . '/media/bearleague/' ;
		}else{
			$baseDir = $dir;
		}
		jimport('joomla.filesystem.path');
		if (file_exists( $baseDir )) {
			if (is_writable( $baseDir )) {
				if (move_uploaded_file( $filename, $baseDir . $userfile_name )) {
				
					if (JPath::setPermissions( $baseDir . $userfile_name )) {
						return true;
					} else {
						$msg = JText::_("BLBE_UPL_PERM");
					}
				} else {
					$msg = JText::_("BLBE_UPL_MOVE");
				}
			} else {
				$msg = JText::_("BLBE_UPL_TMP");
			}
		} else {
			$msg = JText::_("BLBE_UPL_TMPEX");
		}
		if($msg != ''){
			JError::raiseError(500, $msg );
		}
		return false;
	}
	function getAdditfields($type, $id, $sid=0){
		$query = "SELECT ef.*,ev.fvalue as fvalue,ev.fvalue_text
		            FROM #__bl_extra_filds as ef
		            LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".($id?intval($id):-1)."
		            WHERE ef.published=1 AND ef.type='".$type."'
		            ORDER BY ef.ordering";
		if($type == 1 || $type == 0){
			$query = "SELECT DISTINCT(ef.id),ef.*,ev.fvalue as fvalue,ev.fvalue_text
			        FROM #__bl_extra_filds as ef
			        LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".($id?intval($id):-1)."
			        AND ((ev.season_id={$sid} AND ef.season_related = '1') OR (ev.season_id=0 AND ef.season_related = '0'))
			        WHERE ef.published=1 AND ef.type='".$type."'
			        ORDER BY ef.ordering";
			$this->db->setQuery($query);
			$ext_fields_teams = $this->db->loadObjectList();
			if(!count($ext_fields_teams)){
				$query = "SELECT DISTINCT(ef.id),ef.*,ev.fvalue as fvalue,ev.fvalue_text
				    FROM #__bl_extra_filds as ef
				    LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".($id?intval($id):-1)."
				    WHERE ef.published=1 AND ef.type='".$type."' AND ev.season_id=0
				    ORDER BY ef.ordering";
			}
		}

		$this->db->setQuery($query);
		$ext_fields = $this->db->loadObjectList();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		$mj=0;
		if(isset($ext_fields)){
			foreach ($ext_fields as $extr){
				if($extr->field_type == '3'){
					$tmp_arr = array();
					$query = "SELECT * FROM #__bl_extra_select WHERE fid=".$extr->id." ORDER BY eordering";
					$this->db->setQuery($query);
					$selvals = $this->db->loadObjectList();
					$error = $this->db->getErrorMsg();
					if ($error)
					{
						return JError::raiseError(500, $error);
					}
					if(count($selvals)){
						$tmp_arr[] = JHTML::_('select.option',  0, JText::_('BLBE_SELECTVALUE'), 'id', 'sel_value' ); 
						$selvals = array_merge($tmp_arr,$selvals);
						$ext_fields[$mj]->selvals = JHTML::_('select.genericlist',   $selvals, 'extraf['.$extr->id.']', 'class="inputbox" size="1"', 'id', 'sel_value', $extr->fvalue );
					}
				}
				if($extr->field_type == '1'){
					$ext_fields[$mj]->selvals	= JHTML::_('select.booleanlist',  'extraf['.$extr->id.']', 'class="inputbox"', $extr->fvalue );
				}
				$mj++;
			}
		}
		return $ext_fields;
	}
	
	function getSeasDList($s_id, $query_s, $frm, $javascript, $sel = 0, $hname){
		$query = "SELECT * FROM #__bl_tournament WHERE published = '1' ORDER BY name";
		$this->db->setQuery($query);
		$tourn = $this->db->loadObjectList();
		
		$jqre = '<select name="'.$hname.'" id="'.$hname.'" class="styled jfsubmit" size="1" '.$javascript.'>';
		if($sel == 1){
			$jqre .= '<option value="0">'.JText::_('BLBE_SELECTIONNO').'</option>';
		}
		if($frm){
			$jqre .= '<option value="-1" '.((-1 == $s_id)?"selected":"").'>'.JText::_('BLBE_FRIENDLYMATCH').'</option>';
		}	
		

		for($i=0;$i<count($tourn);$i++){

			$query = str_replace('{tourid}',$tourn[$i]->id,$query_s);
			$this->db->setQuery($query);
			$rows = $this->db->loadObjectList();
			if(count($rows)){
				$jqre .= '<optgroup label="'.htmlspecialchars($tourn[$i]->name).'">';///this
				for($g=0;$g<count($rows);$g++){
					$jqre .= '<option value="'.$rows[$g]->id.'" '.(($rows[$g]->id == $s_id)?"selected":"").'>'.$rows[$g]->s_name.'</option>';
				}
				$jqre .= '</optgroup>';
			}
		}
		$jqre .= '</select>';

		return $jqre;

	}
	
	function getSeasAttr($s_id){
		$query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name,t.t_single,s.s_enbl_extra,t.tournament_type,s.season_options
		            FROM #__bl_tournament as t, #__bl_seasons as s
		            WHERE s.s_id = ".(intval($s_id))." AND s.t_id = t.id
		            ORDER BY t.name, s.s_name";
		$this->db->setQuery($query);
		$tourn = $this->db->loadObject();
		$error = $this->db->getErrorMsg();
		if ($error)
		{
			return JError::raiseError(500, $error);
		}
		if($tourn){
			return $tourn;
		}else{
			return null;
		}
	}
	function get_kn_cfg(){
		//variables for knockout
		$cfg = new stdClass();
		$cfg->wdth = 150;
		$cfg->height = 50;
		$cfg->step = 70; 
		$cfg->top_next = 50;
		return $cfg;
	}
	
	function addonexist($addon_name){
		$query = "SELECT name FROM #__bl_addons WHERE published='1' AND name='".$addon_name."'";
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
	function isBet(){
		$query = "SELECT name FROM #__bl_addons WHERE published='1' AND name='betting'";
		$this->db->setQuery($query);
		$is_betting = $this->db->loadResult();
		return $is_betting;
	}
    function getPhotos($type,$id){
        $query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = ".$type." AND cat_id = '".$id."'";
        $this->db->setQuery($query);
        $photos = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error)
        {
            return JError::raiseError(500, $error);
        }
        return $photos;
    }
    function getValSettingsServ($val){
        $val_sett = ini_get($val);
        switch (substr ($val_sett, -1))
        {
            case 'M': case 'm': return (int)$val_sett * 1048576;
            case 'K': case 'k': return (int)$val_sett * 1024;
            case 'G': case 'g': return (int)$val_sett * 1073741824;
            default: return $val_sett;
        }
    }
}

?>