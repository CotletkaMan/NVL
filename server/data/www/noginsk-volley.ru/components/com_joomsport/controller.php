<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.controller');
$mainframe = JFactory::getApplication();
require_once('includes/func.php');
$tmpl = JRequest::getVar( 'tmpl', '', 'get', 'string' );
$task = JRequest::getVar('task', null, 'default', 'cmd');

$noRenderTasks = array('add_comment', 'del_comment');
if(!in_array($task, $noRenderTasks) && ($tmpl != 'component' || ( isset($_GET['view']) && $_GET['view'] == 'calendar') || ( isset($_GET['view']) && $_GET['view'] == 'table'))){
    $doc = JFactory::getDocument();

if(getVer() >= '1.6'){
	JHtml::_('behavior.framework', true);
	$doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/includes/slimbox/js/slimbox.js"></script>' );
}else{
	JHtml::_('behavior.mootools');
	if(isset($mainframe->MooToolsVersion) && $mainframe->MooToolsVersion){
		$doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/includes/slimbox/js/slimbox.js"></script>' );
	}else{
		$doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/includes/slimbox/js15/slimbox.js"></script>' );
	}
}

$doc->addCustomTag( '<link rel="stylesheet" type="text/css"  href="components/com_joomsport/css/admin_bl.css" />' );
$doc->addCustomTag( '<link rel="stylesheet" type="text/css"  href="components/com_joomsport/css/joomsport.css" />' );
$doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/js/joomsport.js"></script>' );
$doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/js/styled-long.js"></script>' );



$doc->addCustomTag( '<link rel="stylesheet" type="text/css"  href="components/com_joomsport/includes/slimbox/css/slimbox.css" />' );
$doc->addCustomTag( '<script type="text/javascript" src="'.JURI::base().'components/com_joomsport/js/grid.js"></script>');
}


class JoomsportController extends JControllerLegacy
{
	protected $js_prefix = '';
	protected $mainframe = null;
	protected $option = 'com_joomsport';

	function __construct(){
		parent::__construct();
		$this->mainframe = JFactory::getApplication();
		$this->js_SetPrefix();
		$this->js_GetDBTables();
		$this->session =  JFactory::getSession();
	}
	private function js_SetPrefix(){
		$this->js_prefix = '';
		$db			= JFactory::getDBO();
		$query = "SELECT name FROM #__bl_addons WHERE published='1'";
		$db->setQuery($query);
		$addon = $db->loadResult();
		if($addon){
			$this->js_prefix = $addon;
		}

	}
	private function js_GetDBTables(){
		$path = dirname(__FILE__).'/tables/';
		if($this->js_prefix){
			if(is_file($path.$this->js_prefix.".php")){
				require($path.$this->js_prefix.".php");
			}else{
				require($path."default.php");
			}
		}else{
			require($path."default.php");
		}
	}
	private function js_Model($name){
		$path = dirname(__FILE__).'/models/';
		if($this->js_prefix){
			if(is_file($path.$this->js_prefix."/".$name.".php")){
				require($path.$this->js_prefix."/".$name.".php");
			}else{
				require($path."default/".$name.".php");
			}
		}else{
			require($path."default/".$name.".php");
		}
	}
	private function js_Layout($task){
		$path = dirname(__FILE__).'/views/'.$task;

		require($path."/view.html.php");

	}
//update
	function set_sess($msg,$typeMess){
		$this->session->set('errMess', $msg);
		$this->session->set('typeMess', $typeMess);
	}

	function display($cachable = false, $urlparams = false)
	{
		$view = JRequest::getCmd( 'view' );
		$task = JRequest::getCmd( 'task' );

		if(!$view) {
			$view = 'table';
		}
		$la = JRequest::getCmd( 'layout' );
		if($la == 'calendar'){
			$view = 'calendar';
			JRequest::setVar( 'layout', 'default' );
		}


		$vName = JRequest::getCmd('view', 'table');

		if($vName == 'moderedit_umatchday' || $vName == 'edit_matchday'){
			$vName = 'edit_matchday';
			$this->js_Model($vName);
			$classname = $vName."JSModel";
			$model = new $classname(3);
		}elseif($vName == 'moderedit_umatch' || $vName == 'edit_match'){
			$vName = 'edit_match';
			$this->js_Model($vName);
			$classname = $vName."JSModel";
			$model = new $classname(3);
		}else{

			$this->js_Model($vName);
			$classname = $vName."JSModel";
			$model = new $classname();
		}
		$this->js_Layout($vName);
		$classname_l = "JoomsportView".$vName;

		$layout = new $classname_l($model);
                
                $tpl = null;

                if (js_mobile::isMobile()) {
                    $this->mobile();
                    $tpl = 'mobile';
                }
		$layout->display($tpl);


		return $this;

	}

        function mobile(){
            $doc = JFactory::getDocument();
            $doc->addCustomTag( '<link rel="stylesheet" type="text/css"  href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800" />' );
            $doc->addCustomTag( '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">' );   
            $doc->addCustomTag( '<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">' );            
            $doc->addCustomTag( '<link rel="stylesheet" type="text/css"  href="components/com_joomsport/css/mobile/tableStyle.css" />' );
            $doc->addCustomTag( '<link rel="stylesheet" type="text/css"  href="components/com_joomsport/css/mobile/tableStyle2.css" />' );
            $doc->addCustomTag( '<link rel="stylesheet" type="text/css"  href="components/com_joomsport/css/mobile/lightbox.css" />' );           
            JHtml::_('bootstrap.framework');
            $doc->addCustomTag( '<script type="text/javascript" src="components/com_joomsport/js/imagelightbox.min.js"></script>' );
        }
        

	function team()
	{
		JRequest::setVar( 'view', 'team' );
		$this->display();
	}
	function player()
	{
		JRequest::setVar( 'view', 'player' );
		$this->display();
	}
	function club()
	{
		JRequest::setVar( 'view', 'club' );
		$this->display();
	}
	function venue()
	{
		JRequest::setVar( 'view', 'venue' );
		$this->display();
	}
	function view_match()
	{
		JRequest::setVar( 'view', 'match' );
		$this->display();
	}

	function calendar()
	{
		JRequest::setVar( 'view', 'calendar' );


		$this->display();
	}
	function regplayer()
	{

		JRequest::setVar( 'view', 'regplayer' );
		$this->display();
	}
	function playerreg_save(){
		$Itemid = JRequest::getInt('Itemid');

		$vName = 'regplayer';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->SaveRegPlayer();

		$link = "index.php?option=com_joomsport&task=regplayer&Itemid=".$Itemid;

		if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
			$return = base64_decode($return);
			if (!JURI::isInternal($return)) {
				$return = '';
			}
		}

		$message = $model->usrnew?JText::_('BLMESS_UPDSUCC'):JText::_('BLFA_REGSUCC');
		$typeMess = 1;
		$this->set_sess($message,$typeMess);
		$this->setRedirect($return?$return:$link);
	}

	function regteam()
	{
		JRequest::setVar( 'view', 'regteam' );
		$this->display();
	}

	function teamreg_save(){
		$Itemid = JRequest::getInt('Itemid');
		$vName = 'regteam';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$model->regTeamSave();

		$link = "index.php?option=com_joomsport&view=seasonlist&oreg=1&Itemid=".$Itemid;
		//$link = "index.php/tour?oreg=1&Itemid=".$Itemid;
		$msg = JText::_('BLFA_NEWTEAMMSG');
		$typeMess = 1;
		$this->set_sess($msg,$typeMess);
		//
		$this->setRedirect($link);
	}

	function add_comment(){
		$addcomm = JRequest::getVar( 'addcomm', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$addcomm = strip_tags($addcomm);

		$math_id = JRequest::getVar('mid',0,'post','int');
		$user	= JFactory::getUser();
		$db = JFactory::getDBO();
		if ( $user->get('guest')) {
			return false;
			//return;
		}
		$query = "INSERT INTO `#__bl_comments` ( `id` , `user_id` , `match_id` , `date_time` , `comment` ) VALUES(0,".$user->id.",".$math_id.",'".gmdate("Y-m-d H:i:s")."','".addslashes($addcomm)."')";
		$db->setQuery($query);
		$db->query();
		$curid = $db->insertid();

		//$query = "SELECT IF(pl.nick <> '',pl.nick,p.name) FROM #__users as p LEFT JOIN #__bl_players as pl ON p.id=pl.usr_id WHERE p.id=".$user->id;
		//$db->setQuery($query);
		//$name = $db->loadResult();
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='player_name'";
		$db->setQuery($query);
		if($db->loadResult()){
			$fullname = "IF(pl.nick <> '',pl.nick,p.name)";
		}else{
			$fullname = "CONCAT(pl.first_name,' ',pl.last_name)";
		}

		$query = "SELECT ".$fullname." FROM #__users as p LEFT JOIN #__bl_players as pl ON p.id=pl.usr_id WHERE p.id=".$user->id;

		$db->setQuery($query);
		$name = $db->loadResult();
		if(!$name){
			$query = "SELECT p.name FROM #__users as p WHERE p.id=".$user->id;
			$db->setQuery($query);
			$name = $db->loadResult();
		}
		/////
		$query = "SELECT pl.def_img,pl.id FROM #__users as p LEFT JOIN #__bl_players as pl ON p.id=pl.usr_id WHERE p.id=".$user->id;
		$db->setQuery($query);
		$playesr = $db->loadObject();
		$def_img = '';
		if(!empty($playesr)){
            if ($pl_id = $playesr->id) {
                $pl_image = $playesr->def_img;
                $query = "SELECT p.ph_name as name,p.id as id,p.ph_filename as filename FROM #__bl_assign_photos as ap, #__bl_photos as p WHERE ap.photo_id = p.id AND cat_type = 1 AND cat_id = ".$pl_id;
                $db->setQuery($query);
                $photos = $db->loadObjectList();
                if($pl_image){
                    $query = "SELECT ph_filename FROM  #__bl_photos as p WHERE p.id = ".$pl_image;
                    $db->setQuery($query);
                    $def_img = $db->loadResult();
                }else if(isset($photos[0])){
                    $def_img = $photos[0]->filename;
                }
            }
		}
		$avatar = JURI::base()."components/com_joomsport/img/ico/season-list-player-ico.gif";

		if(is_file('media/bearleague/'.$def_img)){
			$avatar = JURI::base()."media/bearleague/".$def_img;
		}
		?>
		<li id="divcomb_<?php echo $curid?>">
			<img src="<?php echo $avatar;?>" width="30" height="30" alt="" />
			<div class="comments-box-inner">
				<span class="date">
					<?php

					echo "<img src='".JURI::base()."components/com_joomsport/img/ico/close.png' width='15' border=0 style='cursor:pointer;' onClick='javascript:delCom(".$curid.");' />";
					?>
					<?php
					jimport('joomla.utilities.date');
					if(getVer() > '1.6'){
						$tz	= new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
						$jdate = new JDate(time());

						$jdate->setTimezone($tz);
					}else{

						$jdate = new JDate('now',JFactory::getApplication()->getCfg('offset'));


					}


					echo $jdate->format('Y-m-d H:i:s', true, false);
					?>
				</span>
				<h4 class="nickname"><?php echo $name;?></h4>
				<p><?php echo str_replace("\n",'<br />',htmlspecialchars($addcomm));?></p>
			</div>
		</li>
		<?php
	}
	function del_comment(){
		$c_id = JRequest::getVar('cid',0,'get','int');

		$user	=& JFactory::getUser();
		$dend = false;
		$db = &JFactory::getDBO();
		if(getVer() >= '1.6'){
			$query = "SELECT group_id FROM #__user_usergroup_map WHERE user_id=".$user->id;
			$db->setQuery($query);
			if($db->loadresult() == 8){
				$dend = true;
			}
			$query = "SELECT user_id FROM  `#__bl_comments` WHERE `id` = ".$c_id;
			$db->setQuery($query);
			if($db->loadResult() == $user->id){
				$dend = true;
			}
		}else{
			if($user->gid == 25){
				$dend = true;
			}
		}
		$query = "SELECT s_id FROM #__bl_matchday as md, #__bl_match as m,#__bl_comments as c  WHERE c.match_id = m.id AND md.id=m.m_id AND c.id = '".$c_id."'";
		$db->setQuery($query);
		$sid = $db->loadResult();
		if($sid){
			$query = "SELECT COUNT(*) FROM #__users as u, #__bl_feadmins as f WHERE f.user_id = u.id AND f.season_id=".$sid." AND u.id = ".intval($user->id);
			$db->setQuery($query);
			if($db->loadResult()){
				$dend = true;
			}
		}

		if ( $user->get('guest') || !$dend) {
			echo 'Denide';
			return false;
			//return;
		}
		$query = "DELETE FROM  #__bl_comments WHERE id = ".$c_id;
		$db->setQuery($query);
		$db->query();
        exit(); // Clean output in a dirty way.
	}



function join_season()
	{
		JRequest::setVar( 'view', 'join_season' );
		$this->display();
	}
function joinme(){

	$vName = 'join_season';
	$this->js_Model($vName);
	$classname = $vName."JSModel";
	$model = new $classname();
	$message = $model->joinSave();
	$Itemid = JRequest::getInt('Itemid');

	$this->set_sess($message[0],$message[1]);
	$this->setRedirect('index.php?option=com_joomsport&view=table&sid='.$model->s_id.'&Itemid='.$Itemid);
}
function joinmePaypl(){

        $sid = JRequest::getVar('sid',0,'get','int');
        $payment_status = JRequest::getVar('payment_status',null,'post','cmd');
        $usr_j = JRequest::getVar('usr_j',0,'get','int');
    //print_r($usr_j);echo "<hr>";

        $is_team = JRequest::getVar('is_team',0,'get','int');
        //$reg_team = JRequest::getVar('reg_team',0,'','int');99E77406FV516612D

    //print_r($reg_team);echo "--<hr>";

    $txn_id = JRequest::getVar('txn_id',null,'post','varchar');
    $mc_gross = JRequest::getVar('mc_gross',null,'post','cmd');
    $payment_date = JRequest::getVar('payment_date',null,'post','string ');
    $order_date = date("Y-m-d H:i:s",strtotime ($payment_date));
    /*
     * $_POST['txn_id'] //���������� ����� ����������
     * $_POST['mc_gross'] //������ �������
     * $_POST['payment_date'] //���� �������
     * $sid //����� ������
     * $usr_j // ���� ������������ ($reg_team)
     *
     *
     *
     * */
        $vName = 'join_season';
        $this->js_Model($vName);
        $classname = $vName."JSModel";
        $model = new $classname();
        $message = $model->joinSavePaypl($sid,$is_team,$payment_status,$usr_j,$txn_id,$mc_gross,$order_date);
        $Itemid = JRequest::getInt('Itemid');

        $this->set_sess($message[0],$message[1]);
        $this->setRedirect('index.php?option=com_joomsport&view=table&sid='.$model->s_id.'&Itemid='.$Itemid);
    }
	
	///---------------Matchday--------------------------/
	
	
	
	
	function matchday_save(){

		$vName = 'edit_matchday';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname(3);
		$message = $model->AdmMDSave();

		$msg = JText::_('BLFA_MSG_ADDSCHED');

		$Itemid = JRequest::getInt('Itemid');

		$link = "index.php?option=com_joomsport&view=moderedit_umatchday&mid=".$model->mid."&sid=".$model->season_id."&Itemid=".$Itemid;

		$this->setRedirect( $link );
	}




	///---------------Match--------------------------/


	function moderedit_umatch()
	{
		JRequest::setVar( 'view', 'moderedit_umatch' );
		JRequest::setVar( 'edit', true );
		$this->display();
	}


	function match_save(){
		$vName = 'edit_match';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname(3);
		$model->saveAdmmatch();

		$s_id = JRequest::getVar( 'sid', 0, '', 'int' );


		$Itemid = JRequest::getInt('Itemid');
		$isapply = JRequest::getVar( 'isapply', 0, '', 'int' );
		if(!$isapply){
			$this->setRedirect("index.php?option=com_joomsport&view=moderedit_umatchday&mid=".$model->m_id."&sid=".$s_id."&Itemid=".$Itemid);
		}else{
			$this->setRedirect("index.php?option=com_joomsport&view=moderedit_umatch&cid[]=".$model->id."&Itemid=".$Itemid);
		}
	}


	//inviting confirm
	function confirm_invitings(){
		$vName = 'inviting';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$messaga = $model->getData();
		$Itemid = JRequest::getInt('Itemid');
		$this->set_sess($messaga[0],$messaga[1]);
		$this->setRedirect("index.php?option=com_joomsport&task=regplayer&Itemid=".$Itemid);
	}
	function reject_invitings(){
		$vName = 'inviting';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$messaga = $model->rejectInv();
		$Itemid = JRequest::getInt('Itemid');
		$this->set_sess($messaga[0],$messaga[1]);
		$this->setRedirect("index.php?option=com_joomsport&task=regplayer&Itemid=".$Itemid);
	}
	function unreg_inviting(){
		$vName = 'inviting';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$messaga = $model->unregInvite();
		$Itemid = JRequest::getInt('Itemid');
		$this->set_sess($messaga[0],$messaga[1]);
		$this->setRedirect("index.php?option=com_joomsport&task=regplayer&Itemid=".$Itemid);
	}
	function unreg_inviting_reject(){
		$vName = 'inviting';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$messaga = $model->rejectInv();
		$Itemid = JRequest::getInt('Itemid');
		$this->set_sess($messaga[0],$messaga[1]);
		$this->setRedirect("index.php?option=com_joomsport&task=regplayer&Itemid=".$Itemid);
	}

	function match_inviting(){
		$vName = 'inviting';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$messaga = $model->matchInvite();
		$Itemid = JRequest::getInt('Itemid');
		$mid = JRequest::getVar( 'mid', 0, '', 'int' );
		$this->set_sess($messaga[0],$messaga[1]);
		$this->setRedirect("index.php?option=com_joomsport&view=match&id=".$mid."Itemid=".$Itemid);
	}

	function jointeam(){
		$vName = 'inviting';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$messaga = $model->JoinTeam();
		$Itemid = JRequest::getInt('Itemid');
		$team_id = JRequest::getVar( 'tid', 0, '', 'int' );
		$s_id = JRequest::getVar( 'sid', 0, '', 'int' );
		$this->set_sess($messaga[0],$messaga[1]);
		$this->setRedirect("index.php?option=com_joomsport&task=team&tid=".$team_id."&sid=".$s_id."&Itemid=".$Itemid);
	}

	function get_js_version(){
		$js_version = '2.1.2';
		echo $js_version;
		exit();

	}

	///betting
	function bet_cash_request(){
		$vName = 'userarea';

		$this->js_Model($vName);
		$classname = $vName."JSModel";

		$model = new $classname();

        $view = 'bet_cash_request';
		$this->js_Layout($view);
		$classname_l = "JoomsportView".$view;

		$layout = new $classname_l($model);

		$layout->display();


		return $this;
    }

    function bet_request_cash_submit(){
		$Itemid = JRequest::getInt('Itemid');
		$vName = 'userarea';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$result = $model->submitCashRequest();
		$msg = JText::_('BLFA_BET_REQUEST_SUBMITTED');
        $link = "index.php?option=com_joomsport&task=userarea&Itemid=".$Itemid;
        $this->set_sess($msg,1);
        $this->setRedirect($link);
    }

    function bet_request_points_submit(){
		$Itemid = JRequest::getInt('Itemid');
		$vName = 'userarea';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$result = $model->submitPointsRequest();
		$msg = JText::_('BLFA_BET_REQUEST_SUBMITTED');
        $link = "index.php?option=com_joomsport&task=userarea&Itemid=".$Itemid;
        $this->set_sess($msg,1);
        $this->setRedirect($link);
    }

    function bet_points_request(){
		$vName = 'userarea';

		$this->js_Model($vName);
		$classname = $vName."JSModel";

		$model = new $classname();

        $view = 'bet_points_request';
		$this->js_Layout($view);
		$classname_l = "JoomsportView".$view;

		$layout = new $classname_l($model);

		$layout->display();

		return $this;
    }

    function currentbets()
    {
		$vName = 'userarea';

		$this->js_Model($vName);
		$classname = $vName."JSModel";

		$model = new $classname();

        $view = 'currentbets';
		$this->js_Layout($view);
		$classname_l = "JoomsportView".$view;

		$layout = new $classname_l($model);

		$layout->display();


		return $this;
    }

    function pastbets()
    {
		$vName = 'userarea';

		$this->js_Model($vName);
		$classname = $vName."JSModel";

		$model = new $classname();

        $view = 'pastbets';
		$this->js_Layout($view);
		$classname_l = "JoomsportView".$view;

		$layout = new $classname_l($model);

		$layout->display();


		return $this;
    }

    function bet_matches()
    {
		$vName = 'userarea';

		$this->js_Model($vName);
		$classname = $vName."JSModel";

		$model = new $classname();

        $view = 'bet_matches';
		$this->js_Layout($view);
		$classname_l = "JoomsportView".$view;

		$layout = new $classname_l($model);

		$layout->display();


		return $this;
    }

    function bet_calendar_save(){
		$Itemid = JRequest::getInt('Itemid');
		$vName = 'calendar';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$result = $model->saveBets();
		$msg = JText::_('BLFA_BET_BETSSAVED');
		$typeMess = 1;
		if ($result != 1){
            $msg = $result;
			$typeMess = 2;
        }
        $s_id = JRequest::getVar( 'sid', 0, '', 'int' );
		$link = "index.php?option=com_joomsport&task=calendar&sid=".$s_id."&Itemid=".$Itemid;

		$this->set_sess($msg, $typeMess);
		$this->setRedirect($link);
    }

    function bet_team_save(){
		$Itemid = JRequest::getInt('Itemid');
		$vName = 'team';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$result = $model->saveBets();
		$msg = JText::_('BLFA_BET_BETSSAVED');
		$typeMess = 1;
		if ($result != 1){
            $msg = $result;
			$typeMess = 2;
        }
        $s_id = JRequest::getVar( 'sid', 0, '', 'int' );
        $tid = JRequest::getVar( 'tid', 0, '', 'int' );
		$link = "index.php?option=com_joomsport&task=team&tid=".$tid."&sid=".$s_id."&Itemid=".$Itemid;

		$this->set_sess($msg, $typeMess);
		$this->setRedirect($link);
    }

    function bet_match_save(){
		$Itemid = JRequest::getInt('Itemid');
		$vName = 'match';
		$this->js_Model($vName);
		$classname = $vName."JSModel";
		$model = new $classname();
		$result = $model->saveBets();
		$msg = JText::_('BLFA_BET_BETSSAVED');
		$typeMess = 1;
		if ($result != 1){
            $msg = $result;
			$typeMess = 2;
        }
        $mid = JRequest::getVar( 'm_id', 0, '', 'int' );
		$link = 'index.php?option=com_joomsport&task=view_match&id='.$mid.'&Itemid='.$Itemid;

		$this->set_sess($msg, $typeMess);
		$this->setRedirect($link);
    }
	function userarea(){
		JRequest::setVar( 'view', 'userarea' );
		$this->display();
    }
	public function chkvers(){
		echo  @file_get_contents('http://joomsport.com/index2.php?option=com_chkversion&id=1&no_html=1&tmpl=component');
	}

    function get_formatkn(){
        $vName = 'knockout';
        //$this->js_Model($vName);
        $path = JPATH_SITE.'/components/com_joomsport/models/default/';
        require($path.$vName.".php");
        $classname = 'JS_Knockout';
        $model = new $classname();
        if (js_mobile::isMobile()) {
            $this->mobile();
            $model->getFormatkn_mobile();
        } else {
            $model->getFormatkn();
        }
    }
    function get_format(){
        $vName = 'knockout';
        $path = JPATH_SITE.'/components/com_joomsport/models/default/';
        require($path.$vName.".php");
        $classname = 'JS_Knockout';
        $model = new $classname();
        if (js_mobile::isMobile()) {
            $this->mobile();
            $model->getFormat_mobile();
        } else {
            $model->getFormat();
        }
    }
    function imgres(){
        @ob_clean();@ob_clean();
        include(JPATH_SITE.'/components/com_joomsport/includes/imgres.php');
        die;
    }	
}