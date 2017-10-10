<?php
defined('_JEXEC') or die;

class JS_Pagination{
	public $pcount = 0;
	public $limit = 0;
	public $pages = 1;
	public $page  = 0;
	public $show_links = 10;
	public $limit_array = array(5,10,20,50,100,0);
	
	function __construct($count,$page = 1, $limit=20){
		$this->pcount = $count;
		$this->limit = $limit;
		$this->page = $page;
		if($this->pcount > $this->limit && $this->limit){
			$this->pages = ceil($this->pcount/$this->limit);
		}
	}
	
	function getPageLinks($link,$val=''){
		$kl = "";
		if($this->pages > 1){
			$kl .= '<div class="js_pagination">';
			$kl .= '<ul>';
			
			if($this->page == 1){
				
				$kl .= "<li class='js_prevpage'></li>";
			}else{
				//$link_li = $link."&page".$val."=1";
				//$kl .= "<li><a href='".JRoute::_($link_li)."'>".JText::_("BLFA_PAGE_START")."</a></li>";
				$link_li = $link."&page".$val."=".($this->page-1);
				$kl .= "<li class='js_prevpage'><a href='".JRoute::_($link_li)."'></a></li>";
			}
			$start = ($this->page<ceil($this->show_links/2))?1:($this->page - ceil($this->show_links/2));
			$start = $start?$start:1;
			$end = $start + ($this->show_links);
			if($start != 1){
				$step = 1;
				if($start > 50){
					$step = 10;
				}
				$kl .= "<li class='page_more'>";
				$kl .= "<a href='javascript:void(0);' onclick='javascript:subPcount(\"jsul_start\")'></a>";
				$kl .= "<ul id='jsul_start'>";
				for($z=1;$z<ceil($start/$step)+1;$z++){
					$link_li = $link."&page".$val."=".(($step==1)?$z:(($z-1)*$step + 1));
					$endp = ($z == ceil($start/$step))?$start:$z*$step;
					$kl .= "<li class='js_innerli".$step."'><a href='".JRoute::_($link_li)."'>".(($step==1)?$z:((($z-1)*$step + 1)."-".($endp)))."</a></li>";
				}
				$kl .= "</ul>";
				$kl .= "</li>";
				$start++;
			}
			for($i=$start;$i<$end;$i++){

				if($this->pages >= $i){
					$link_li = $link."&page".$val."=".$i;
					if($this->page == $i){
						$kl .= "<li class='li_page_active'><span>".$i."</span></li>";
					}else{
						$kl .= "<li><a href='".JRoute::_($link_li)."'>".$i."</a></li>";
					}
				}	
			}
			
			if($this->page != $this->pages && $end <= $this->pages){
				$step = 1;
				if($this->pages - $end > 50){
					$step = 10;
				}
				$kl .= "<li class='page_more'>";
				$kl .= "<a href='javascript:void(0);' onclick='javascript:subPcount(\"jsul_last\")'></a>";
				$kl .= "<ul style='margin-left:-218px !important;text-align:right;' id='jsul_last'>";
				
				for($z=$end;$z<ceil(($this->pages - $end)/$step)+$end + 1;$z++){
					$link_li = $link."&page".$val."=".(($step==1)?$z:(($z-$end)*$step + $end));
					$endp = ($z == ceil(($this->pages - $end)/$step) + $end - 1)?$this->pages:($z+1-$end)*$step + $end - 1;
					$kl .= "<li class='js_innerli".$step."'><a href='".JRoute::_($link_li)."'>".(($step==1)?$z:((($z-$end)*$step + $end)."-".($endp)))."</a></li>";
				}
				$kl .= "</ul>";
				$kl .= "</li>";

			}
			
			if($this->page == $this->pages){
				//$kl .= "<li><span>".JText::_("BLFA_PAGE_NEXT")."</span></li>";
				$kl .= "<li class='js_endpage'></li>";
			}else{
				$link_li = $link."&page".$val."=".($this->page+1);
				//$kl .= "<li><a href='".JRoute::_($link_li)."'>".JText::_("BLFA_PAGE_NEXT")."</a></li>";
				//$link_li = $link."&page".$val."=".($this->pages);
				$kl .= "<li class='js_endpage'><a href='".JRoute::_($link_li)."'></a></li>";
			}
			$kl .= '</ul>';
			$kl .= '</div>';
		}
		return $kl;
	}
	
	
	function getLimitBox($val=''){
		$kl = '';
			$jas = 'onChange = "document.adminForm.submit();"';
			foreach($this->limit_array as $lim){
				$limbox[] = JHTML::_('select.option',  $lim, $lim?$lim:JText::_('BLFA_ALL'), 'id', 'name' ); 
			}
			$kl .= JHTML::_('select.genericlist',   $limbox, 'jslimit'.$val, 'class="inputboxPag" size="1" '.$jas, 'id', 'name', $this->limit );
		
		
		return $kl;
	}
	
	function getLimitPage(){
		$kl = '';
			$kl .= JText::_('BL_TAB_PAGE').'&nbsp;'.$this->page.'&nbsp;'.JText::_('BL_TAB_OF').'&nbsp;'.$this->pages;
		
		return $kl;
	}
	
}