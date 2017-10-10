<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
$document		= JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'modules/mod_js_players/css/mod_js_players.css');
require_once("components/com_joomsport/includes/func.php");
//JHtml::addIncludePath(JPATH_COMPONENT . '/helpers'); 
$ph_width = $params->get( 'photo_width' );
$is_width = $params->get( 'photo_is' );
$displ_team = $params->get( 'displ_team' );
$cItemId = $params->get('customitemid');
$ssss_id = $params->get( 'sidgid' );
$ex = explode('|',$ssss_id );
$s_id = $ex[0];
$Itemid = JRequest::getInt('Itemid');
if(!$cItemId){
	$cItemId = $Itemid;
}
?>
<div class="jsm_playerstat">
<?php if(count($list)){?>
<?php foreach ($list as $player) { ?>
	<div style="width:99%;overflow:hidden;"><?php 
	$defimg = modBlPlayersHelper::getPhoto($player);
	$teams = modBlPlayersHelper::getTeamName($player ,$params);

        if($is_width){
		echo '<div style="float:left;width:'.($ph_width+2).'px;height:'.($ph_width+12).'px;margin:0px 10px 0px 0px;">';
		if($defimg && is_file('media/bearleague/'.$defimg)){
			//echo '<img style="border:1px solid #aaa;" src="media/bearleague/'.$defimg.'" title="'.$player->e_name.'" height="'.$ph_width.'" />';
			
			echo '<div class="team-embl" style="width:'.$ph_width.'px;"><img '.getImgPop($defimg,5,$ph_width,$ph_width).'></div>';
		}else{
		//echo "&nbsp;";
		echo '<div class="team-embl"><img class="player-ico" src="'.JURI::base().'components/com_joomsport/img/ico/season-list-player-ico.gif" width="30" height="30" alt=""></div>';
		}
		echo '</div>';
		
	}
	//else{
		if($player->e_img && is_file('media/bearleague/events/'.$player->e_img)){
			echo '<img src="media/bearleague/events/'.$player->e_img.'" title="'.$player->e_name.'" height="15" />';
		}
	//}
	$link = "<a href='".JRoute::_('index.php?option=com_joomsport&amp;task=player&amp;id='.$player->id.'&amp;sid='.($s_id).'&amp;Itemid='.$cItemId)."'>".(($plname && $player->nick)?$player->nick:$player->name)."</a>";
	
		echo ' <strong>'.$player->cnt.'</strong>  '.$link;
		if($teams && $displ_team){
			echo $teams;
		}
	
	?></div>
<?php } ?>
<?php } ?>
</div>