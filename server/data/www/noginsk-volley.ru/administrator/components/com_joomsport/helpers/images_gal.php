<?php

// no direct access
defined('_JEXEC') or die;



class JHtmlImages_gal
{
	static function getImageGalleryUpl($photo,$def_img,$num = 0){
        $html ='';

        $html .='<table class="adminlist">
                    <tr>
                        <th class="title" width="30">'.JText::_( 'BLBE_DELETE' ).'</th>
                        <th class="title" width="30">'.JText::_( 'BLBE_DEFAULT' ).'</th>
                        <th class="title">'.JText::_( 'BLBE_TITLE' ).'</th>
                        <th class="title" width="250">'.JText::_( 'BLBE_IMAGE' ).'</th>
                    </tr>';
        foreach($photo as $photos){
            $html .='<td align="center">
                        <a href="javascript:void(0);" title="'.JText::_( 'BLBE_REMOVE' ).'" onClick="javascript:Delete_tbl_row(this);"><img src="'.JURI::base().'components/com_joomsport/img/publish_x.png" title="'.JText::_( 'BLBE_REMOVE' ).'" /></a>
                    </td>
                    <td align="center">';
            if(!$num){
                $ph_checked = ($def_img == $photos->id) ? 'checked="true"' : "";
                $html .='<input type="radio" name="ph_default" value="'.$photos->id.'"'.$ph_checked.'/>';
            }
            $html .='<input type="hidden" name="photos_id[]" value="'.$photos->id.'"/>
                    </td>
                    <td>
                        <input type="text" maxlength="255" size="60" name="ph_names[]" value="'.htmlspecialchars($photos->name).'" />
                    </td>
                    <td align="center">';
            $imgsize = getimagesize('../media/bearleague/'.$photos->filename);
            if($imgsize[0] > 200){
                $width = 200;
            }else{
                $width  = $imgsize[0];
            }
            $scr = "{handler: 'image'}";
            $html .='<a rel="'.$scr.'" href="'.JURI::base().'../media/bearleague/'.$photos->filename.'" title="'.JText::_( 'BLBE_IMAGE' ).'" class="modal-button"><img src="'.JURI::base().'../media/bearleague/'.$photos->filename.'" width="'.$width.'" /></a>
			        </td>
			    </tr>';
        }
        $html .='</table>';

        return $html;

    }
    
}
