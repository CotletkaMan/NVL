<?php
/**
* @package Sj Basic News
* @version 3.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @copyright (c) 2012 YouTech Company. All Rights Reserved.
* @author YouTech Company http://www.smartaddons.com
*
*/
defined( '_JEXEC' ) or die;

JFactory::getLanguage()->load('com_content');

if(!class_exists('sj_basic_new_helper')){
	class sj_basic_new_helper{
		protected static $helper = null;
		
		protected $categories = null;
		protected $children = array();
		protected $items = null;
		protected $params = null;
		
		public static function getList($params , $module){
			$helper = self::getInstance();
			return $helper->_getList($params, $module);
		}
		
		protected function _getList($params, $module){
			$this->params = $params;
			$list = array();
			$source_category = $params->get('source');
			
			$items = $this->getCategoryItems($source_category, $params);	
			
			if(!empty($items)) {
				include_once JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php';											
				$custom = $this->_getCustomUrl($params);
				foreach($items as $key => $item){
					$category = $this->getCategory($item->catid);
					$item->catitle = $category->title;
					if(!$this->getItemImage($item)){
						$item->image = 'modules/'.Ytools::getModule()->module . '/assets/images/nophoto.png';
					}
					if(array_key_exists($item->id, $custom)){
						$item->link=  trim($custom[$item->id]->url);
					}else{						
						$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid));
					}												
						$item->description = strip_tags($item->introtext);
				}
			}	
			return $items;
		}
		

		private function _getCustomUrl($params, $key = 'id') {
			$custom = array();
			$params_custom = $params->get('custom');
			if (isset($params_custom)){
				if (count($params_custom)){
					foreach($params_custom as $obj){
						if (is_array($obj)){
							$obj = JArrayHelper::toObject($obj);
						}
						if (isset($obj->$key) && !empty($obj->$key)){
							$custom[$obj->$key] = $obj;
						}
					}
				}
			}
			return $custom;
		}
		
		
		
		public function itemImages($item){
			$_images4 = array();
			if (is_int($item)){
				$item = $this->getItem($item);
			}
			if (!isset($this->_images4[$item->id])){
				$this->_images4[$item->id] = array();
		
				// image extract
				if (strpos($item->images, '{')!==false || strpos($item->images, '[')!==false){
					$json = json_decode($item->images, true);
					
					if (isset($json['image_intro'])){
						if (YTools::isUrl($json['image_intro']) || file_exists($json['image_intro'])){
							array_push($this->_images4[$item->id], $json['image_intro']);
						}
					}
					
					if (isset($json['image_fulltext'])){
						if (YTools::isUrl($json['image_fulltext']) || file_exists($json['image_fulltext'])){
							array_push($this->_images4[$item->id], $json['image_fulltext']);
						}
					}
					
				}
		
				if (!isset($item->image_extracted)){
					$item_images = YTools::extractImages($item->introtext);
					$item->image_extracted = true;
				}
		
				if (isset($item_images) && count($item_images)){
					// get first exists image
					foreach ($item_images as $i => $image_url) {
						if (YTools::isUrl($image_url) || file_exists($image_url)){
							array_push($this->_images4[$item->id], $image_url);
						}
					}
				}
			}
			
			return $this->_images4[$item->id];
		}
		
		public function getItemImage($item){
			$images = $this->itemImages($item);
			$has_image = count($images)>0;
			if ($has_image){
				$item->image = $images[0];
			}
			return $has_image;
		}
		
		
		
		public function getItem($id){
			$item = null;
			if ( isset($this->items[$id]) || $this->getItemsFromDb($id, false) ){
				$item = $this->items[$id];
			}
			return $item;
		}
		
		public function getItems($ids = null){
			$items = array();
			if ( is_string($ids) ){
				$ids = explode(',', $ids);
			}
			if ( is_array($ids) ){
				array_map('intval', $ids);
				$ids = array_unique($ids);
				$missing = array();
		
				foreach ($ids as $id) {
					if (!isset($this->items[$id])){
						$missing[$id] = $id;
					}
				}
					
				empty($missing) OR $this->getItemsFromDb($missing, false);
				foreach ($ids as $id){
					if (isset($this->items[$id])){
						$items[$id] = $this->items[$id];
					}
				}
			}
			return $items;
		}
		
		
		public function getCategory($cid=0){
			$categories = $this->getCategoriesFromDb();
			if ( isset($categories[$cid]) ){
				return $categories[$cid];
			}
			return false;
		}
		
		public function getCategories($cids = null){
			$category_ids = null;
			if (isset($cids) && !empty($cids)){
				$category_ids = $cids;
				if (is_string($category_ids)){
					$category_ids = explode(',', $category_ids);
				}
				if (!is_array($category_ids)){
					$category_ids = array($category_ids);
				}
			
				// load published categories
				
				$categories = $this->getCategoriesFromDb();
				if ( !is_array($category_ids) ){
					settype($category_ids, 'array');
				}
				foreach ($category_ids as $i => $cid) {
					if (!isset($categories[$cid])){
						unset($category_ids[$i]);
					}
				}
			}
			return $category_ids;
		}

		public function getChildCategoryIds( $cid = 0, $depth = 0 ){
			if ( isset($this->children[$cid]) ){
				$childrens = array();
				$depths = array();
				foreach ($this->children[$cid] as $child){
					if ( isset($this->children[$child]) ){
						$queue = array( $child );
						$depths[$child] = 1;
						while ( count($queue) ){
							$c0 = array_shift($queue);
							if ($depth > 0 && $depth < $depths[$c0]) continue;
							array_push($childrens, $c0);
							if ( isset($this->children[$c0]) ){
								foreach (array_reverse($this->children[$c0]) as $c1){
									array_unshift($queue , $c1);
									$depths[$c1] = $depths[$c0] + 1;
								}
							}
						}
					} 
				}
				
				return $childrens;
			}
				
			return array();
		}
		
		public function getCategoryItems($cid, $params){
			$cid = $this->getCategories($cid);
			$subcategories = $params->get('subcategories',1);
			if (isset($subcategories) && (int)$subcategories){
				// include sub categories's items
				if (is_string($cid)){
					$cid = explode(",",$cid);
				}
				if (!is_array($cid)){
					$cid = array($cid);
				}
				count($this->children) or $this->getCategoriesFromDb();
				foreach($cid as $id){
					$temp = $this->getChildCategoryIds($id,1);
					$child_count = count($temp);
					for($i = 0 ;$i < $child_count;$i++){
						array_push($cid, $temp[$i]);
					}
				}
			} else {
				if (is_string($cid)){
					$cid = explode(",",$cid);
				}
				if (!is_array($cid)){
					$cid = array($cid);
				}
		
			}
			 if(!is_array($cid)){
				settype($cid, 'array');
			} 
			
			$cid = array_unique($cid);
			
			$item_ids = $this->getItemsIn($cid, $params);
		
			return $this->getItems($item_ids, $params);
		}
		
		
		public function getItemsFromDb($ids, $overload = false){
			if (!is_array($ids)){
				$ids = array((int)$ids);
			}
			if(!empty($ids)){
				$db = JFactory::getDbo();
				$query = "SELECT *  FROM #__content AS e WHERE e.id IN (" . implode(',', $ids)  . ") GROUP BY e.id;";
				$db->setQuery($query);
				$rows = $db->loadObjectList();
				$item_count = 0;
				if ( !is_null($rows) ){
					foreach($rows as $item){
						if ($overload || !isset($this->items[$item->id])){
							$this->items[$item->id] = $item;
							$item_count++;
						}
					}
				}
			
				return $item_count;
			}
		}
		
		public function getItemsIn($cids, $params){
				
			$db = JFactory::getDbo();
			$now = JFactory::getDate()->toSql();
			$nulldate = $db->getNullDate();
		
			if (isset($cids) && is_array($cids)){
				$category_filter_set = implode(',', $cids);
			}
			
			if(!empty($category_filter_set)){
				$query = "
				SELECT *
				FROM #__content as e
				WHERE
				e.state IN(1)
				AND e.catid IN ($category_filter_set)
				" . ($this->_getContentAccessFilter() ? 'AND '.$this->_getContentAccessFilter() : '') . " -- Access condition
				AND (e.publish_up   = {$db->quote($nulldate)} OR e.publish_up   <= {$db->quote($now)})
				AND (e.publish_down = {$db->quote($nulldate)} OR e.publish_down >= {$db->quote($now)})
				AND e.language IN ({$db->quote(JFactory::getLanguage()->getTag())} , {$db->quote('*')})
				{$this->_itemFilter($params)}
				GROUP BY e.id
				ORDER BY {$this->_itemOrders($params)}
				{$this->_queryLimit($params)}
				";
				$db->setQuery($query);
				$ids = $db->loadColumn();
			return $ids;
			}
		}
		
		
		public function getCategoriesFromDb(){
			if (is_null($this->categories)){
				$db = JFactory::getDbo();
				$query = "
				SELECT *
				FROM #__categories AS e
				WHERE
				e.published IN (1)
				AND e.extension = 'com_content'
				AND e.parent_id > 0
				" . ($this->_getContentAccessFilter() ? 'AND '.$this->_getContentAccessFilter() : '') . " -- Access condition
						AND e.language IN ({$db->quote(JFactory::getLanguage()->getTag())} , {$db->quote('*')})
						GROUP BY e.id
						ORDER BY e.lft
						";
				$db->setQuery($query);
				$rows = $db->loadObjectList();
				if ( !is_null($rows) ){
					$_categories = array();
					foreach($rows as $i=> $category){
					/* 	$category->children = array(); */
						$_categories[$category->id] = $rows[$i];
					}
					$this->categories = $_categories;
					$this->buildCategoriesTree();
				}else{
					$this->categories = array();
				}
			}
			return $this->categories;
		}
		
		protected function buildCategoriesTree(){
			$categories = $this->getCategoriesFromDb();
			if ( count($categories) ){
				foreach ($categories as $cid => $category){
					$cid = $category->id;
					$pid = $category->parent_id;
					if (isset($categories[$pid])){
						if (!isset($this->children[$pid])){
							$this->children[$pid] = array();
						}
						$this->children[$pid][$cid] = $cid;
					}
				}
				return 1;
			}
			return 0;
		}
		
		
		protected function _getContentAccessFilter($alias='e'){
			$condition = false;
			$app  = JFactory::getApplication();
			$params = $app->getParams();
			if ($params instanceof JRegistry && !$params->get('show_noauth', 0)){
				$user = JFactory::getUser();
				$condition = $alias . '.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')';
			}
			return $condition;
		}
		
		protected function _itemFilter($params, $alias='e'){
			$join_filter="";
			$source_filter = $params->get('source_filter');
			if ( isset($source_filter) ){
				// frontpage filter.
				switch ($source_filter){
					default:
					case '0':
						$join_filter = "";
						break;
					case '1':
						$join_filter = "AND $alias.featured=0";
						break;
					case '2':
						$join_filter = "AND $alias.featured=1";
						break;
				}
			}
			return $join_filter;
		}
		
		
		protected function _itemOrders($params, $alias='e'){
			// set order by default
			$item_order_by = "$alias.ordering";
			$source_order_by = $params->get('source_order_by');
			if ( isset($source_order_by) ){
				$string_order_by = trim($source_order_by);
				switch ($string_order_by){
					default:
					case 'ordering':
						$item_order_by = "$alias.ordering";
						break;
					case 'mostview':
					case 'hits':
						$item_order_by = "$alias.hits DESC";
						break;
					case 'recently_add':
					case 'created':
						$item_order_by = "$alias.created DESC";
						break;
					case 'recently_mod':
					case 'modified':
						$item_order_by = "$alias.modified DESC";
						break;
					case 'title':
						$item_order_by = "$alias.title";
						break;
					case 'random':
						$item_order_by = 'rand()';
						break;
				}
			}
			return $item_order_by;
		}
		
		protected function _queryLimit($params){
			$source_limit = '';
			$limitation = $params->get('source_limit');
			if (isset($limitation) && (int)$limitation>=0){
				
				$source_limit_total = (int)$limitation;
				$source_limit = "LIMIT 0, $source_limit_total";
			}
			return $source_limit;
		}
		
		
		public static function getInstance(){
			if(is_null(self::$helper)){
				$classname = __CLASS__;
				self::$helper = new $classname;
			}
			return self::$helper;			
		}
	}
}
