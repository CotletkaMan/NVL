<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of roundhelper
 *
 * @author andreykarhalev
 */
class roundhelper {
    static function roundHTML($index, $lists){
        ?>
        <div class="round_container">
            <div>
                <a href="javascript:void(0);" onclick="javascript:(this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode));">
                    <span class="icon-delete"></span>
                </a>
            </div>    
            <div class="js_round_header_div">
                <div>
                    <div class="js_round_header_subfloat">
                        <?php echo JText::_( 'BLBE_ROUND_TITLE' ); ?> &nbsp;
                        <input type="text" name="round_title[<?php echo $index;?>]" value="<?php echo isset($lists['race']['rounds'][$index]->round_title)?$lists['race']['rounds'][$index]->round_title:'';?>" />
                    </div>
                    <div class="js_round_header_subfloat">
                        <fieldset class="jsRadio">
                        <div class="controls">
                            <?php
                            $is_unpubl = (!isset($lists['race']['rounds'][$index]->round_status) || $lists['race']['rounds'][$index]->round_status != '0') ? false : true;
                            
                            ?>
                                <label for="round_status<?php echo $index;?>0" id="round_status<?php echo $index;?>0-lbl" class="radio btn<?php echo (isset($lists['race']['rounds'][$index]->round_status) && $lists['race']['rounds'][$index]->round_status == '0') ?' jsRactive btn-danger':'';?>">
                                    <input type="radio" name="round_status[<?php echo $index;?>]" id="round_status<?php echo $index;?>0" value="0" class="inputbox" <?php echo (isset($lists['race']['rounds'][$index]->round_status) && $lists['race']['rounds'][$index]->round_status == '0')?' checked':'';?>><?php echo JText::_( 'JUNPUBLISHED' ); ?>
                                </label>
                                <label for="round_status<?php echo $index;?>1" id="round_status<?php echo $index;?>1-lbl" class="radio btn<?php echo (!isset($lists['race']['rounds'][$index]->round_status) || $lists['race']['rounds'][$index]->round_status != '0')?' jsRactive btn-success':'';?>">
                                    <input type="radio" name="round_status[<?php echo $index;?>]" id="round_status<?php echo $index;?>1" value="1" class="inputbox" <?php echo (!isset($lists['race']['rounds'][$index]->round_status) || $lists['race']['rounds'][$index]->round_status != '0')?' checked':'';?>><?php echo JText::_( 'JPUBLISHED' ); ?>
                                </label>
                                

                        </div>
                        </fieldset>
                        <?php
                        $round_status_adv = isset($lists['race']['rounds'][$index]->round_status) ? $lists['race']['rounds'][$index]->round_status : 2;
                        ?>
                        <fieldset id="round_status_<?php echo $index;?>_adv" class="jsRadio" <?php echo $is_unpubl ? 'style="display:none;"' : '';?>>
                        <div class="controls">
                                <label for="round_status_adv<?php echo $index;?>2" id="round_status<?php echo $index;?>2-lbl" class="radio btn<?php echo ($round_status_adv == '2')?' active btn-success':'';?>">
                                    <input type="radio" name="round_status_adv[<?php echo $index;?>]" id="round_status_adv<?php echo $index;?>2" value="2" class="inputbox" <?php echo ($round_status_adv == '2')?' checked':'';?>><?php echo JText::_( 'BLBE_ROUND_FINAL' ); ?>
                                </label>
                                <label for="round_status_adv<?php echo $index;?>1" id="round_status<?php echo $index;?>1-lbl" class="radio btn<?php echo ($round_status_adv == '1')?' active btn-success':'';?>">
                                    <input type="radio" name="round_status_adv[<?php echo $index;?>]" id="round_status_adv<?php echo $index;?>1" value="1" class="inputbox" <?php echo ($round_status_adv == '1')?' checked':'';?>><?php echo JText::_( 'BLBE_ROUND_QUALIFICATION' ); ?>
                                </label>
                                

                        </div>
                        </fieldset>
                    </div>    
                </div>    
            </div>
            <div class="js_round_main_div">
                <table>
                    <tr>
                        <th>
                            <?php echo JText::_( 'BLBE_LANGVIEWSOTH_PARTIC' ); ?>
                        </th>
                        <?php
                        for($intB = 0; $intB < intval($lists['race']['options']->attempts); $intB ++){
                            ?>
                            <th>
                                <?php echo JText::_( 'BLBE_ROUND_ATTEMPT' ); ?>&nbsp;<?php echo ($intB + 1);?>
                            </th>
                            <?php
                        }
                        ?>
                        <?php 
                        if($lists['race']['options']->penalty == 1){
                            ?>
                            <th>
                                <?php echo JText::_( 'BLBE_ROUND_PENALTY' ); ?>
                            </th>

                            <?php
                        }
                        ?>
                        <?php
                        for($intB = 0; $intB < count($lists['race']['extracol']); $intB ++){
                            ?>
                            <th>
                                <?php echo $lists['race']['extracol'][$intB]->name;?>
                            </th>
                            <?php
                        }
                        ?>    
                        <th class="js_div_round_result_header">
                            <?php echo JText::_( 'BLBE_ROUND_RESULT' ); ?>
                        </th>

                    </tr>    
                    <?php
                    for($intA = 0; $intA < count($lists['race']['participiants']); $intA ++){
                        ?>
                            <tr>
                                <td>
                                    <?php echo $lists['race']['participiants'][$intA]->t_name?>
                                    <input type="hidden" name="round_partic[<?php echo $index;?>][]" value="<?php echo $lists['race']['participiants'][$intA]->id;?>" />
                                </td>
                                <?php
                                $attempts = isset($lists['race']['rounds'][$index]->res[$intA]->attempts)?$lists['race']['rounds'][$index]->res[$intA]->attempts:'';
                                $attempts_col = explode('|', $attempts);
                                for($intB = 0; $intB < intval($lists['race']['options']->attempts); $intB ++){
                                    ?>
                                    <td>
                                        <input type="text" class="js_round_resultbox" value="<?php echo isset($attempts_col[$intB])?$attempts_col[$intB]:'';?>" name="round_attempts[<?php echo $index;?>][<?php echo $lists['race']['participiants'][$intA]->id;?>][]" />
                                    </td>
                                    <?php
                                }
                                ?>
                                <?php 
                                if($lists['race']['options']->penalty == 1){
                                    ?>
                                    <td>
                                        <input type="text" value="<?php echo isset($lists['race']['rounds'][$index]->res[$intA]->penalty)?($lists['race']['rounds'][$index]->res[$intA]->penalty):'';?>" name="round_penalty[<?php echo $index;?>][]" class="js_roundbox js_round_resultbox" />
                                    </td>

                                    <?php
                                }
                                //var_dump($lists['race']['rounds'][$index]);
                                ?>
                                <?php
                                $ecol = isset($lists['race']['rounds'][$index]->res[$intA]->extracol)?$lists['race']['rounds'][$index]->res[$intA]->extracol:'';
                                $ecol_col = explode('|', $ecol);
                                for($intB = 0; $intB < count($lists['race']['extracol']); $intB ++){
                                    ?>
                                    <td class="extra_col_id_<?php echo $intB;?>">
                                        <input type="text" class="extra_col_input" value="<?php echo isset($ecol_col[$intB])?$ecol_col[$intB]:'';?>" name="extracol_input_<?php echo $intB;?>[<?php echo $index;?>][]" />
                                    </td>
                                    <?php
                                }
                                ?> 
                                <td class="js_div_round_result">
                                    <input type="text" value="<?php echo isset($lists['race']['rounds'][$index]->res[$intA]->result_string)?$lists['race']['rounds'][$index]->res[$intA]->result_string:'';?>" name="round_result[<?php echo $index;?>][]" class="js_round_result" />
                                </td>
                            </tr>

                        <?php
                    }
                    ?>
                </table>   
                <input type="hidden" name="round_order[]" value="<?php echo $index;?>" />
            </div>
        </div>
        <?php
    }
}
