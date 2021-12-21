<?php 
/*******
 * @package xbMaps Component
 * @version 0.3.0.h 22nd September 2021
 * @filesource site/views/tags/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbmapsViewTags extends JViewLegacy {
	
	public function display($tpl = null) {
		
		$this->items 		= $this->get('Items');
		$this->state		= $this->get('State');
		$this->params      = $this->state->get('params');
		//if tags disabled on front-end redirect to maps view (we shouldn't even be here)
		if ($this->params->get('global_use_tags')==0) {
			$app = Factory::getApplication();
			$app->redirect('index.php?option=com_xbmaps&view=maplist');
			$app->close();
		}
		$this->maptags = $this->params->get('maps_use_tags');
		$this->mrktags = $this->params->get('markers_use_tags');
		$this->trktags = $this->params->get('tracks_use_tags');
		
		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');
		$this->searchTitle = $this->state->get('filter.search');
		
		$this->header = array();
		$this->header['showheading'] = $this->params->get('show_page_heading',0,'int');
		$this->header['heading'] = $this->params->get('page_heading','','text');
		if ($this->header['heading'] =='') {
			$this->header['heading'] = $this->params->get('page_title','','text');
		}
		$this->header['title'] = $this->params->get('list_title','','text');
		$this->header['subtitle'] = $this->params->get('list_subtitle','','text');
		$this->header['text'] = $this->params->get('list_headtext','','text');
		
		$this->show_desc = $this->params->get('show_desc','1','int');
		$this->show_parent = $this->params->get('show_parent','1','int');
		
		$app = Factory::getApplication();
		$this->tagtype = $app->getUserState('fmtype');
		$this->hide_type = ($this->tagtype=='') ? false : true;
		
		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
			return false;
		}
		
		parent::display($tpl);
	} // end function display()
}
