<?php
/*******
 * @package xbMaps Component
 * @version 0.1.1.j 26th August 2021
 * @filesource admin/views/tracks/view.html.php
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

class XbmapsViewTracks extends JViewLegacy {
		
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
		$mcat = $cparams->get('tracks_use_cats');
		$this->show_cats = 0;
		if ($gcat>0) {
			$this->show_cats = $mcat;
		}
		
		$gtags = $cparams->get('global_use_tags');
		$mtags = $cparams->get('tracks_use_tags');
		$this->show_tags = 0;
		if ($gtags) {
			$this->show_tags = $mtags;
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
		}
		
		$this->addToolbar();
		XbmapsHelper::addSubmenu('tracks');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
		
		$this->setDocument();
	}
	
	protected function addToolbar() {
		$canDo = XbmapsHelper::getActions();
		
		ToolbarHelper::title(Text::_( 'XBMAPS_TITLE_TRACKS' ), '' );
		
		if ($canDo->get('core.create') > 0) {
			ToolbarHelper::addNew('track.add');
		}
		if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
			ToolbarHelper::editList('track.edit');
		}
		if ($canDo->get('core.edit.state')) {
			ToolbarHelper::publish('track.publish','JTOOLBAR_PUBLISH', true);
			ToolbarHelper::unpublish('track.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			ToolbarHelper::archiveList('track.archive');
		}
		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'track.delete','JTOOLBAR_EMPTY_TRASH');
		} else if ($canDo->get('core.edit.state')) {
			ToolbarHelper::trash('track.trash');
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
		ToolbarHelper::custom('','spacer');
		ToolBarHelper::custom('track.preview', 'eye', '', 'Preview Track', true) ;
				
		if ($canDo->get('core.admin')) {
			ToolbarHelper::preferences('com_xbmaps');
		}
		ToolbarHelper::help( '', false,'https://crosborne.uk/xbmaps/doc?tmpl=component#admin-tracks' );
	
	}
	
	protected function setDocument() {
		$document = Factory::getDocument();
		$document->setTitle(strip_tags(Text::_('XBMAPS_TITLE_TRACKS')));
	}
	
}