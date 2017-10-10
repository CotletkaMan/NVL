<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldoptions extends JFormField{
    protected $type = 'options';
    
    protected static $initialisedMedia = false;
    
    protected function getLabel(){
    	return '';
    	
    } 
    protected function  getInput() {
    	
		$document = JFactory::getDocument();

        JHTML::_('behavior.modal');  
        JHtml::_('behavior.colorpicker');  

        $document->addStyleDeclaration(
            'div.control-group > div.controls{margin-left:10px;}'
        );
        $document->addStyleSheet( 'https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
        $document->addStyleSheet( JURI::root() . 'modules/mod_joomly_slider/css/base_slider.css');
        $document->addStyleSheet( JURI::root() . 'modules/mod_joomly_slider/css/jquery.contextMenu.min.css');
        $document->addStyleSheet( JURI::root() . 'modules/mod_joomly_slider/css/admin_slider.css');
        $document->addScript( JURI::root() . 'modules/mod_joomly_slider/js/base_slider.min.js');
        $document->addScript( JURI::root() . 'modules/mod_joomly_slider/js/admin_slider.js');
        $document->addScript( JURI::root() . 'modules/mod_joomly_slider/js/mediafield-mootools.min.js');
        $document->addScript( JURI::root() . 'modules/mod_joomly_slider/js/jquery.transit.min.js');
        $document->addScript( JURI::root() . 'modules/mod_joomly_slider/js/jquery.contextMenu.js');
        
        $jinput = JFactory::getApplication()->input;
        $mod_id = $jinput->get('id', 1);
        $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('params')
            ->from('#__modules')
            ->where("id={$mod_id}");
            $db->setQuery($query);
            $array =  $db->loadAssoc();
        $fields =  json_decode($array['params']); 

       $html = '<div>
            <input type="hidden" name="jform[params][option][options]" id="opt" value="' . (isset($fields->option->options) ? htmlentities($fields->option->options) : '') . '" />
            <input type="hidden" id="edit_slide" value="' . JText::_('MOD_JOOMLY_SLIDER_EDIT_SLIDE')  . '" />
            <input type="hidden" id="create_slide" value="' . JText::_('MOD_JOOMLY_SLIDER_CREATE_SLIDE')  . '" />
            <input type="hidden" id="delete_slide" value="' . JText::_('MOD_JOOMLY_SLIDER_DELETE_SLIDE')  . '" />
            <input type="hidden" id="delete_last_slide" value="' . JText::_('MOD_JOOMLY_SLIDER_DELETE_LAST_SLIDE')  . '" />
            <input type="hidden" id="init_text" value="' . JText::_('MOD_JOOMLY_SLIDER_INIT_TEXT')  . '" />
        </div>'; 

		$html .= '<div id="slider-options" class="slider-options settings container span4">
        <br />
        <h3>&nbsp;' . JText::_('MOD_JOOMLY_SLIDER_SLIDER_OPTIONS') . '</h3>
        <form>
            <div class="form-group">
                <input id="width" min="0" type="text" name="width" /> 
                <label for="width">' . JText::_('MOD_JOOMLY_SLIDER_WIDTH') . '<a class="sl-tooltip right"><span class="txt">'. JText::_('MOD_JOOMLY_SLIDER_WIDTH_HINT') . '</span></a></label>
            </div>
            <div class="form-group">
                <input id="height" min="0" type="text" name="height" /> 
                 <label for="height">' . JText::_('MOD_JOOMLY_SLIDER_HEIGHT') . '<a class="sl-tooltip right"><span class="txt">'. JText::_('MOD_JOOMLY_SLIDER_HEIGHT_HINT') . '</span></a></label>
            </div>
			<div class="form-group">
                <input id="aspectRatio" type="checkbox" name="aspectRatio" /> <label for="aspectRatio">' . JText::_('MOD_JOOMLY_SLIDER_ASPECT_RATIO') . '<a class="sl-tooltip right"><span class="txt">'. JText::_('MOD_JOOMLY_SLIDER_ASPECT_RATIO_HINT') . '</span></a></label>
            </div>
            <div class="form-group">
                <select id="effect" class="form-control" name="effect">
                    <option value="0" selected>' . JText::_('MOD_JOOMLY_SLIDER_NONE') . '</option>
                    <option value="effect3">' . JText::_('MOD_JOOMLY_SLIDER_MOVE_LEFT_RIGHT') . '</option>
                    <option value="effect4">' . JText::_('MOD_JOOMLY_SLIDER_MOVE_TOP_BOTTOM') . '</option>
                </select>
                <label for="effect">' . JText::_('MOD_JOOMLY_SLIDER_EFFECT') . '</label>
            </div>
            <div class="form-group">
                <input id="controlButtons" type="checkbox" name="controlButtons" /> <label for="controlButtons">' . JText::_('MOD_JOOMLY_SLIDER_BACK_FORWARD') . '</label>
            </div>
            <div class="form-group">
                <input id="bullets" type="checkbox" name="bullets" /> <label for="bullets">' . JText::_('MOD_JOOMLY_SLIDER_BAR_BUTTON') . '</label>
            </div>
            <div class="form-group">
                <input id="captionOnHover" type="checkbox" name="captionOnHover" /> <label for="captionOnHover">' . JText::_('MOD_JOOMLY_SLIDER_NAME_HOVER') . '</label>
            </div>
            <div class="form-group">
                <input id="loop" type="checkbox" name="loop" /> <label for="loop">' . JText::_('MOD_JOOMLY_SLIDER_LOOP') . '</label>
            </div>
            <div class="form-group">
                <input id="timer" class="form-control" min="0" step="1" type="number" name="timer" value="0" /> 
                <label for="timer">' . JText::_('MOD_JOOMLY_SLIDER_TIMER_VALUE') . '<a class="sl-tooltip right"><span class="txt">'. JText::_('MOD_JOOMLY_SLIDER_TIMER_HINT') . '</span></a></label>
            </div>
        </form>
    </div>';   
    $html .= '<div class="content span8">
        <ul id="slider1" class="joomly-slider"></ul>
    </div>';
    $html .='<div class="modal fade hide" id="slide-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close slider-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">' . JText::_('MOD_JOOMLY_SLIDE_OPTIONS') . '</h4>
                </div>
                <div id="slide-options"  class="modal-body container slider-options ">
                    <div class="span7">
                        <div class="form-group">
                            <label for="text" class="textarea-label">' . JText::_('MOD_JOOMLY_SLIDER_TEXT') . '</label>
                            <textarea id="text" class="form-control modal-text" name="text" cols="150" rows="10"></textarea>
                        </div>  
                        <div id="bgImagediv" class="form-group">
                            <div class="input-prepend input-append">
                                <input type="text" id="bgImage" value="" readonly="readonly" onchange="Setsrc(this.value)" title="" class="input-small field-media-input hasTipImgpath" data-basepath="http://localhost/joomla38/">
                                <a class="modal btn" title="' . JText::_('MOD_JOOMLY_SLIDER_SELECT') . '" href="&#10;index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=187&amp;author=created_by&amp;fieldid=bgImage&amp;folder=" rel="{handler: \'iframe\', size: {x: 800, y: 600}}">Select</a>
                                <a class="btn hasTooltip" title="" href="#" onclick="jInsertFieldValue(\'\', \'bgImage\');  return false;" data-original-title="Clear"><i class="icon-remove"></i></a>
                            </div>     
                            <label for="bgImage">' . JText::_('MOD_JOOMLY_SLIDER_BACKGROUND_IMAGE') . '</label>
                        </div>
                        <div class="form-group">
                             <image id="bgImagePreview" src="" />  
                        </div>       
                       </form>   
                    </div>
                    <div class="span5">
                        <form>
                            <div class="form-group">
                               <input id="fontSize" class="form-control" type="number" name="fontSize" min="1" value="" /> 
                                <label for="fontSize">' . JText::_('MOD_JOOMLY_SLIDER_FONT_SIZE') . '</label>
                            </div>
                            <div class="form-group">
                            </div>
                            <div class="form-group">
                                <select id="textAlign" class="form-control" name="textAlign">
                                    <option value=""></option>
                                    <option value="left">' . JText::_('MOD_JOOMLY_SLIDER_ALIGN_LEFT') . '</option>
                                    <option value="center">' . JText::_('MOD_JOOMLY_SLIDER_ALIGN_CENTER') . '</option>
                                    <option value="right">' . JText::_('MOD_JOOMLY_SLIDER_ALIGN_RIGHT') . '</option>
                                    <option value="justify">' . JText::_('MOD_JOOMLY_SLIDER_ALIGN_JUSTIFY') . '</option>
                                </select>
                                <label for="textAlign">' . JText::_('MOD_JOOMLY_SLIDER_ALIGN') . '</label><br/>
                            </div>
                            <div class="form-group">
                                 <input id="textColor" type="text" name="textColor" class="minicolors" value="#000000" />
                                 <label for="textColor">' . JText::_('MOD_JOOMLY_SLIDER_TEXT_COLOR') . '</label>
                            </div>   
							<div class="form-group">
                                <input id="bgColor" type="text" name="bgColor" class="minicolors" value="#ffffff" />
                                 <label for="bgColor">' . JText::_('MOD_JOOMLY_SLIDER_BACKGROUND_COLOR') . '</label>
                            </div>
                            <div class="form-group">
                                <input id="caption" class="form-control input-big" type="text" name="caption" value="" /> 
                                <label for="caption">' . JText::_('MOD_JOOMLY_SLIDER_SLIDE_NAME') . '</label>
                            </div>
							<div class="form-group">
                                <input id="link" class="form-control input-big" type="text" name="link" value="" /> 
                                <label for="caption">' . JText::_('MOD_JOOMLY_SLIDER_LINK') . '</label>
                            </div>
                        </form>  
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default slider-close" data-dismiss="modal">Close</button>
                    <button id="save" type="button" class="btn btn-primary">Save</button>
                    <button id="add" type="button" class="btn btn-primary">Add</button>
                </div>
            </div>
        </div>
    </div>';
        return $html;
    }
	
 }?> 


<script type="text/javascript">
var root_url = "<?php echo JURI::root();?>";
var jQ = jQuery.noConflict();

function Setsrc(val ) {
    if (val !== '')
    {
        jQ("#bgImagePreview").attr('src', root_url + val);   
    } else 
    {
        jQ("#bgImagePreview").attr('src', '');   
    }
}
</script> 