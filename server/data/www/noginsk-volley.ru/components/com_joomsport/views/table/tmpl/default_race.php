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
defined( '_JEXEC' ) or die( 'Restricted access' );?>
<?php
	if(isset($this->message)){
		$this->display('message');
	}
	$lists = $this->lists;
	$Itemid = JRequest::getInt('Itemid');
    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
?>
<?php
if($this->tmpl != 'component'){
	echo $lists["panel"];
	$lnk = "
		var arr = document.getElementsByClassName('joomsporttab');
		var tabName = '';
		for(var i =0; i<arr.length;i++){
			if(arr[i].className.indexOf('active')!= '-1'){
				tabName=arr[i].id;
				document.getElementById(tabName+'_div').style.display='block';break;
			}
		}
	
	 window.open('".JURI::base()."index.php?tmpl=component&option=com_joomsport&amp;view=table&amp;sid=".$lists["s_id"]."&amp;tab_n='+tabName,'jsmywindow','width=700,height=700');
	";
}else{
	$lnk = "window.print();";
}
?>


<!-- <module middle> -->
			<div class="module-middle solid">
				
				<!-- <back box> -->
				<?php if($this->lists['socbut']){?>
				<div class="back dotted">
					<div class="div_for_socbut">
						<?php echo $this->lists['socbut'];?>
					</div>
					<div class="clear"></div>
				</div>
				<?php } ?>
				<!-- </back box> -->
				
				<!-- <title box> -->
				<div class="title-box">
					<h2>
						
						<span itemprop="name"><?php echo $this->escape($this->ptitle); ?></span>
					</h2>
                    <a class="print" href="#" onClick="<?php echo $lnk;?>" title="<?php echo JText::_("BLFA_PRINT");?>"><?php echo JText::_("BLFA_PRINT");?></a>
				</div>
				<!-- </div>title box> -->
				<?php
				$tLogo = '';
				if($lists["curseas"]->logo && is_file('media/bearleague/'.$lists["curseas"]->logo)){
					$tLogo = "<img itemprop='image' ".getImgPop($lists["curseas"]->logo,4)." width='100' alt='".$this->params->get('page_title')."' style='margin-bottom:10px;' />";
					
				}
				if($lists['ext_fields'] || $tLogo){
				?>
				<div class="gray-box">
					<?php echo $tLogo;?>
					<table cellpadding="0" cellspacing="0" border="0" class="adf-fields-table">
						<?php echo $lists['ext_fields']?>		
					</table>
					<div class="gray-box-cr tl"><!-- --></div>
					<div class="gray-box-cr tr"><!-- --></div>
					<div class="gray-box-cr bl"><!-- --></div>
					<div class="gray-box-cr br"><!-- --></div>
				</div>
				<?php }?>
				<div class='jscontent'><span itemprop="description"><?php echo $lists["curseas"]->descr;?></span></div>
				<div style="clear:both;"></div>
				<!-- <tab box> -->
				<ul class="tab-box">
					<?php 
					if($this->tmpl!= 'component'){
						require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_joomsport'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'tabs.php');
						$etabs = new esTabs();
                                          if ($lists['is_group_matches'] || !$lists['is_matches']) {
                                            echo $etabs->newTab(JText::_('BL_TAB_TBL'),'etab_main','table',(($lists["unable_reg"] && $lists['season_par']->s_rules)?'hide':'vis'));
                                          }
					  if($lists['season_par']->s_rules){
						echo $etabs->newTab(JText::_('BL_TAB_RULES'),'etab_rules','tab_flag',($lists["unable_reg"]?'vis':'hide'));
					  }
					  if($lists['season_par']->s_descr){
						echo $etabs->newTab(JText::_('BL_TAB_ABOUTSEAS'),'etab_aboutm','tab_flag');
					  }
					}
					?>
				</ul>
				<!-- </tab box> -->
				
			</div>
			<!-- </module middle> -->
		<div class="content-module">
<?php 
if(($this->tmpl != 'component' || $lists["table_n"]=='etab_main')){?>
<div id="etab_main_div" class="tabdiv" <?php echo ($lists["unable_reg"] && $lists['season_par']->s_rules)?(($lists["table_n"]=='etab_main')?"style='display:block;'":"style='display:none;'"):"";?>>
<?php
for($intX = 0; $intX < count($lists['rounds_name']); $intX ++){

    echo '<div><h2 class="dotted">'.$lists['rounds_name'][$intX]."</h2></div>";

    if(count($lists['rounds'])){
        for($intQ = 0; $intQ < count($lists['rounds'][$intX]); $intQ ++){
            ?>
            <div class="round_container">
   
                <div class="js_round_header_div">
                    <div>
                        <?php
                            echo '<h3 class="dotted">'.$lists['rounds'][$intX][$intQ]->round_title."</h3>";
                        ?>
                    </div>    
                </div>
                <div class="js_round_main_div">
                    <table class="season-list team-list">
                        <tr>
                            <th class="sort asc" axis="int">
                                <?php echo JText::_( 'BL_PARTICS' ); ?>
                            </th>
                            <?php
                            for($intB = 0; $intB < intval($lists['options']->attempts); $intB ++){
                                ?>
                                <th class="sort asc" axis="int">
                                    <?php echo JText::_( 'BLFA_ATTEMPTS' ); ?>&nbsp;<?php echo ($intB + 1);?>
                                </th>
                                <?php
                            }
                            ?>
                            <?php 
                            if($lists['options']->penalty == 1){
                                ?>
                                <th class="sort asc" axis="int">
                                    <?php echo JText::_( 'BLFA_PENALTY' ); ?> (<?php echo $lists['options']->postfix;?>)
                                </th>

                                <?php
                            }
                            ?>
                            <?php
                            for($intB = 0; $intB < count($lists['extracol'][$intX]); $intB ++){
                                ?>
                                <th class="sort asc" axis="int">
                                    <?php echo $lists['extracol'][$intX][$intB]->name;?>
                                </th>
                                <?php
                            }
                            ?>     
                            <th class="sort asc" axis="int">
                                <?php echo JText::_( 'BLFA_RESULTS' ); ?> (<?php echo $lists['options']->postfix;?>)
                            </th>

                        </tr>    
                        <?php
                        for($intA = 0; $intA < count($lists['rounds'][$intX][$intQ]->res); $intA ++){
                            ?>
                                <tr <?php echo ($intA % 2)?'':'class="gray"';?>>
                                    <td class="teams" nowrap>
                                        <?php echo $lists['rounds'][$intX][$intQ]->res[$intA]->t_name?>
                                    </td>
                                    <?php
                                    $attempts = isset($lists['rounds'][$intX][$intQ]->res[$intA]->attempts)?$lists['rounds'][$intX][$intQ]->res[$intA]->attempts:'';
                                    $attempts_col = explode('|', $attempts);
                                    for($intB = 0; $intB < intval($lists['options']->attempts); $intB ++){
                                        ?>
                                        <td>
                                            <?php echo isset($attempts_col[$intB])?$attempts_col[$intB]:'';?>
                                        </td>
                                        <?php
                                    }
                                    ?>
                                    <?php 
                                    if($lists['options']->penalty == 1){
                                        ?>
                                        <td>
                                            <?php echo ($lists['rounds'][$intX][$intQ]->res[$intA]->penalty);?>
                                        </td>

                                        <?php
                                    }
                                    //var_dump($lists['race']['rounds'][$index]);
                                    ?>
                                        
                                    <?php
                                    $ecol = isset($lists['rounds'][$intX][$intQ]->res[$intA]->extracol)?$lists['rounds'][$intX][$intQ]->res[$intA]->extracol:'';
                                    $ecol_col = explode('|', $ecol);
                                    for($intB = 0; $intB < count($lists['extracol'][$intX]); $intB ++){
                                        ?>
                                        <td>
                                            <?php echo isset($ecol_col[$intB])?$ecol_col[$intB]:'';?>
                                        </td>
                                        <?php
                                    }
                                    ?>    
                                        
                                    <td class="js_div_round_result">
                                        <?php echo ($lists['rounds'][$intX][$intQ]->res[$intA]->result_string);?>
                                    </td>
                                </tr>

                            <?php
                        }
                        ?>
                    </table>   

                </div>
            </div>
            <?php
        }
    }  
}    
    ?>
</div>
</form>
<?php } ?>
<?php if($lists["season_par"]->s_rules):?>
<?php if($this->tmpl != 'component' || $lists["table_n"]=='etab_rules'){?>
<div id="etab_rules_div" class="tabdiv" <?php echo ($lists["unable_reg"] || $lists["table_n"]=='etab_rules')?"style='display:block;'":"style='display:none;'";?>>
	<?php echo $lists["season_par"]->s_rules;?>
</div>
<?php }?>
<?php endif;?>
<?php if($lists['season_par']->s_descr):
        JPluginHelper::importPlugin('content'); 
        $dispatcher = JDispatcher::getInstance(); 
        $results = @$dispatcher->trigger('onContentPrepare', array ('content'));
        $lists['season_par']->s_descr = JHTML::_('content.prepare', $lists['season_par']->s_descr);
?> 
<?php if($this->tmpl != 'component' || $lists["table_n"]=='etab_aboutm'){?>
<div id="etab_aboutm_div" class="tabdiv" <?php echo ($lists["table_n"]=='etab_aboutm')?"style='display:block;'":"style='display:none;'";?>>
	<?php echo $lists['season_par']->s_descr;?>
</div>
<?php }?>
<?php endif;?>
<br />

</div>
<input type="hidden" name="jscurtab" id="jscurtab" value="" />
<!-- Attention! To remove the branding link you must pay the branding removal license here http://www.joomsport.com/web-shop/custom-fees/branding-free-license.html . Please support us by doing so using the free version of JoomSport!  Thank you!-->


<!-----KNOCK ----->
<div class="content-module">
   <!-- <div id="etab_main_div" class="tabdiv" <?php echo ($this->lists["unable_reg"] && $this->lists['season_par']->s_rules)?"style='display:none;'":"style='display:block;'";?>>
        <?php

        ?>
    </div>-->
    <?php if($this->lists['season_par']->s_rules):?>
    <!--<div id="etab_rules_div" class="tabdiv" <?php echo $this->lists["unable_reg"]?"style='display:block;'":"style='display:none;'";?>>
        <?php echo $this->lists['season_par']->s_rules;?>
    </div>-->
    <?php endif;?>
    <?php if($lists["curseas"]->s_descr):
    JPluginHelper::importPlugin('content');
    $dispatcher = JDispatcher::getInstance();
    $results = @$dispatcher->trigger('onContentPrepare', array ('content'));
    $lists["curseas"]->s_descr = JHTML::_('content.prepare', $lists["curseas"]->s_descr);
    ?>
    <div id="etab_aboutm_div" class="tabdiv" style='display:none;'>
        <?php echo $this->lists["curseas"]->s_descr;?>
    </div>
    <?php endif;?>
    <input type="hidden" name="jscurtab" id="jscurtab" value="" />
    
    <?php if($this->lists['jsbrand_on'] == 1):?>
    <div id="copy" class="copyright"><a href="http://joomsport.com">JoomSport - sport Joomla league</a></div>
    <?php endif;?>
</div>