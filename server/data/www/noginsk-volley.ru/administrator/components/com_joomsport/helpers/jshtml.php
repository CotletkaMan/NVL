<?php

// no direct access
defined('_JEXEC') or die;

class JHtmljshtml {

    public static function order($rows, $image = 'filesave.png', $task = 'saveorder') {
        return '&nbsp;&nbsp;<a href="javascript:saveorder(' . (count($rows) - 1) . ', \'' . $task . '\')" rel="tooltip" class="saveorder btn btn-micro" title="'
                . JText::_('JLIB_HTML_SAVE_ORDER') . '"><i class="icon-menu-2"></i>'.JText::_('JLIB_HTML_SAVE_ORDER').'</a>';
    }

}