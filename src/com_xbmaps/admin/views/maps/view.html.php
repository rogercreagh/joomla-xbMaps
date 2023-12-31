<?php
/*******
 * @package xbMaps Component
 * @version 1.5.0.0 31st December 2023
 * @filesource admin/views/maps/view.html.php
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

class XbmapsViewMaps extends JViewLegacy {
	
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
		$mcat = $cparams->get('maps_use_cats');
		$this->show_cats = 0;
		if ($gcat>0) {
		    $this->show_cats = $mcat;
		} 
		
		$gtags = $cparams->get('global_use_tags');
		$mtags = $cparams->get('maps_use_tags');
		$this->show_tags = 0;
		if ($gtags) {
			$this->show_tags = $mtags;
		}
		
		$this->marker_image_path = '/images/'.$cparams->get('def_markers_folder','');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
		}
		
		$this->addToolbar();
		
/** We are NOT doing j4 compatibility 
		$version = new Version();
		if (!$version->isCompatible('4.0.0-alpha')) {
			XbmapsHelper::addSubmenu('dashboard');
			$this->sidebar = JHtmlSidebar::render();			
		}		
 */		
		XbmapsHelper::addSubmenu('maps');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
		
		$this->setDocument();
				
	}
	
	protected function addToolbar() {
		$canDo = XbmapsHelper::getActions();
		
		ToolbarHelper::title(Text::_( 'XBMAPS_TITLE_MAPSMANAGER' ), '' );
		
		if ($canDo->get('core.create') > 0) {
			ToolbarHelper::addNew('map.add');
		}
		if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
			ToolbarHelper::editList('map.edit');
		}
		if ($canDo->get('core.edit.state')) {
			ToolbarHelper::publish('map.publish','JTOOLBAR_PUBLISH', true);
			ToolbarHelper::unpublish('map.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			ToolbarHelper::archiveList('map.archive');
		}
		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'map.delete','JTOOLBAR_EMPTY_TRASH');
		} else if ($canDo->get('core.edit.state')) {
			ToolbarHelper::trash('map.trash');
		}
		ToolbarHelper::custom('','spacer');
		// Add a batch button
		if ($canDo->get('core.create') && $canDo->get('core.edit')
				&& $canDo->get('core.edit.state'))
		{
			// we use a standard Joomla layout to get the html for the batch button
			$bar = Toolbar::getInstance('toolbar');
			$layout = new FileLayout('joomla.toolbar.batch');
			$batchButtonHtml = $layout->render(array('title' => Text::_('JTOOLBAR_BATCH')));
			$bar->appendButton('Custom', $batchButtonHtml, 'batch');
		}
//		ToolbarHelper::custom('','spacer');
//		ToolBarHelper::custom('map.preview', 'eye', '', 'Preview Map', true) ;
		
		
		if ($canDo->get('core.admin')) {
			ToolbarHelper::preferences('com_xbmaps');
		}
		ToolbarHelper::help( '', false,'https://crosborne.uk/xbmaps/doc?tmpl=component#admin-maps' );
	}
	
	protected function setDocument() {
		$document = Factory::getDocument();
		$document->setTitle(strip_tags(Text::_('XBMAPS_TITLE_MAPSMANAGER')));
	}
	
}