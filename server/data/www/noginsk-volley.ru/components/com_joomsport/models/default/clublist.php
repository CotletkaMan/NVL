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
// No direct access.
defined('_JEXEC') or die;

require(dirname(__FILE__) . '/../models.php');
if (js_mobile::isMobile()){
    require(dirname(__FILE__).'/../../includes/pagination_mobile.php');
} else {
    require(dirname(__FILE__).'/../../includes/pagination.php');
}

class clublistJSModel extends JSPRO_Models {

    var $_data = null;
    var $_lists = null;
    var $_total = null;
    var $id = null;
    var $_pagination = null;
    var $limit = null;
    var $limitstart = null;
    var $_params = null;
    var $_tinfo = null;
    var $title = null;
    var $p_title = null;

    function __construct() {
        parent::__construct();

        $mainframe = JFactory::getApplication();
        $this->title = JFactory::getDocument()->getTitle();

        
        $this->limit = JRequest::getVar('jslimit', 20, '', 'int');
        $this->limitstart = JRequest::getVar('page', 1, '', 'int');
        $this->limitstart = intval($this->limitstart) > 1 ? $this->limitstart : 1;

        $this->getPagination();

        $this->getData();
        
        $this->p_title = JText::_('BLFA_CLUBLIST');
        $this->_params = $this->JS_PageTitle($this->title ? $this->title : $this->p_title);
        $this->_lists["teams_season"] = $this->teamsToModer();
        $this->_lists["panel"] = $this->getePanel($this->_lists["teams_season"], 0, null, 0);
    }

    function getData() {
        if (empty($this->_data)) {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query);
            $error = $this->db->getErrorMsg();
            if ($error) {
                return JError::raiseError(500, $error);
            }
        }

        return $this->_data;
    }

    function getTotal() {
        if (empty($this->_total)) {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }

    function _getListCount($query) {
        $this->db->setQuery($query);
        $tot = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        return count($tot);
    }

    function _getList($query) {
        $this->db->setQuery($query, ($this->limitstart - 1) * $this->limit, $this->limit);
        $tot = $this->db->loadObjectList();
        $error = $this->db->getErrorMsg();
        if ($error) {
            return JError::raiseError(500, $error);
        }
        return $tot;
    }

    function getPagination() {
        if (empty($this->_pagination)) {
            //jimport('joomla.html.pagination');
            $this->_pagination = new JS_Pagination($this->getTotal(), $this->limitstart, $this->limit);
        }

        return $this->_pagination;
    }

    function _buildQuery() {

        $query = "SELECT t.* "
                . " FROM #__bl_club as t"
                . " ORDER BY t.c_name";
        return $query;
    }

}

