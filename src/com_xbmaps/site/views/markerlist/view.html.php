<?php
/*******
 * @package xbMaps
 * @version 0.5.0.d 30th September 2021
 * @filesource site/views/markerlist/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Component\ComponentHelper;

class XbmapsViewMarkerlist extends JViewLegacy {
		
	public function display($tpl = null) {
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->params      = $this->state->get('params');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		$this->searchTitle = $this->state->get('filter.search');
		$this->catid = $this->state->get('catid');
		
		$cparams = ComponentHelper::getParams('com_xbmaps');
		$gcat = $cparams->get('global_use_cats');
		$mcat = $cparams->get('markers_use_cats');
		$this->show_cats = 0;
		if ($gcat>0) {
			$this->show_cats = $mcat;
		}
		
		$gtags = $cparams->get('global_use_tags');
		$mtags = $cparams->get('markers_use_tags');
		$this->show_tags = 0;
		if ($gtags) {
			$this->show_tags = $mtags;
		}
		$this->marker_image_path = '/images/'.$cparams->get('def_markers_folder','');
		
		$this->header = array();
		$this->header['showheading'] = $this->params->get('show_page_heading',0,'int');
		$this->header['heading'] = $this->params->get('page_heading','','text');
		if ($this->header['heading'] =='') {
			$this->header['heading'] = $this->params->get('page_title','','text');
		}
		$this->header['title'] = $this->params->get('list_title','','text');
		$this->header['subtitle'] = $this->params->get('list_subtitle','','text');
		$this->header['text'] = $this->params->get('list_headtext','','text');
		
		$this->search_bar = $this->params->get('search_bar','1','int');
		$this->hide_catsch = $this->params->get('menu_category_id',0)>0 ? true : false;
		$this->hide_tagsch = (!empty($this->params->get('menu_tag',''))) ? true : false;
		
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
		}
				
		parent::display($tpl);
		
	}
	
}