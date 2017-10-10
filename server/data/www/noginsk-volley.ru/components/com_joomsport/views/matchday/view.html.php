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
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.php';

class joomsportViewmatchday extends JSBaseView {

    function display($tpl = null) {
        $this->_model->getData();
        $lists = $this->_model->_lists;
        $tpl = $this->_model->_layout.($tpl? '_'.$tpl: $tpl);

        $params = $this->_model->_params;

        $p_title = $this->_model->p_title;
        $title = $this->_model->title;

        $lists["t_single"] = $this->_model->t_single;
        $lists["s_id"] = $this->_model->s_id;

        $this->assignRef('params', $params);

        $this->assignRef('ptitle', $p_title);
        $this->assignRef('title', $title);

        $this->assignRef('lists', $lists);
        $this->assignRef('m_id', $this->_model->m_id);
        $this->assignRef('page', $this->_model->_pagination);

        require_once(dirname(__FILE__) . '/tmpl/default' . $tpl . '.php');
    }

}
