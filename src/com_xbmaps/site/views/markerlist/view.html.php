<?php
/*******
 * @package xbMaps
 * @version 0.5.0.c 30th September 2021
 * @filesource site/views/markerlist/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

class XbmapsViewMarkerlist extends JViewLegacy {
		
	public function display($tpl = null) {
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
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
		
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
		}
				
		parent::display($tpl);
		
	}
	
}