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
// no direct access
defined('_JEXEC') or die('Restricted access');
if (isset($this->message)) {
    $this->display('message');
}
global $Itemid;
$Itemid = JRequest::getInt('Itemid');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
?>
<div class="page-content">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <?php echo $this->lists["panel"];?>
    </nav>
    <!-- /.navbar -->

    <div class="main clubLayout">
        <div class="history col-xs-12 col-lg-12">
            <ol class="breadcrumb">
                <li><a href="#" onclick="history.back(-1);return false;"><i class="fa fa-long-arrow-left"></i><?php echo JText::_("BL_BACK")?></a></li>
            </ol>
            <div class="socialMediaCont pull-right">                     
                <div class="addthis_native_toolbox"><?php echo $this->lists['socbut'];?></div>
            </div>
        </div>
        <div class="heading col-xs-12 col-lg-12">
            <h1 class="pull-left col-xs-12 col-sm-12 col-md-12 col-lg-12"><?php echo $this->escape($this->ptitle); ?></h1>
        </div>
        <div class="clubLayout"> 
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#club" role="tab" data-toggle="tab"><i class="flag"></i><?php echo JText::_('BLFA_CLUB')?></a></li>
                <?php if(count($this->lists["teams"])){ ?>
                <li role="presentation"><a href="#teams" role="tab" data-toggle="tab"><i class="users"></i><?php echo JText::_('BLFA_ADMIN_TEAM')?></a></li>
                <?php } ?>
                <li role="presentation"><a href="#gallery" role="tab" data-toggle="tab"><i class="photos"></i><?php echo JText::_('BL_TAB_PHOTOS')?></a></li>
            </ul>

            <!-- Tab panels -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="club">
                    <div class="col-xs-12"> 
                        <?php 
                        echo JHtml::_('images.getViewContent',  $Itemid, $this->lists["club"],(isset($this->lists["ext_fields"])?$this->lists["ext_fields"]:null),$this->lists["club"]->c_descr,4,$this->lists['photos'],'',$this->escape($this->params->get('page_title')));
                        ?>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="teams">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo JText::_('BLFA_ADMIN_TEAM');?></th>
                            </tr>
                        </thead>
                        <tbody>           
                            <?php
                            for ($i = 0; $i < count($this->lists["teams"]); $i++) {
                                $teams = $this->lists["teams"][$i];
                                $link = JRoute::_('index.php?option=com_joomsport&amp;task=team&amp;id='.$teams->id.'&amp;sid=0&amp;Itemid='.$Itemid);
                                ?>
                                <tr class="<?php echo $i % 2 ? "" : "active"; ?>">
                                    <td class="left">                                        
                                        <a href="<?php echo $link ?>"><?php echo $teams->t_name;?></a>
                                    </td>
                                </tr>
                            <?php } ?>                        
                        </tbody>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane" id="gallery">                    
                    <?php echo JHtml::_('images.getGalleryHTML',  $this->lists["photos"]); ?>                    
                </div>
            </div>
        </div>
    </div>
</div>
<script>
jQuery( 'a' ).imageLightbox(
{
    selector:       'id="imagelightbox"',   // string;
    allowedTypes:   'png|jpg|jpeg|gif',     // string;
    animationSpeed: 250,                    // integer;
    preloadNext:    true,                   // bool;            silently preload the next image
    enableKeyboard: true,                   // bool;            enable keyboard shortcuts (arrows Left/Right and Esc)
    quitOnEnd:      false,                  // bool;            quit after viewing the last image
    quitOnImgClick: false,                  // bool;            quit when the viewed image is clicked
    quitOnDocClick: true,                   // bool;            quit when anything but the viewed image is clicked
    onStart:        false,                  // function/bool;   calls function when the lightbox starts
    onEnd:          false,                  // function/bool;   calls function when the lightbox quits
    onLoadStart:    false,                  // function/bool;   calls function when the image load begins
    onLoadEnd:      false                   // function/bool;   calls function when the image finishes loading
});
</script>