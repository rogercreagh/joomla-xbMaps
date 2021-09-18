<?php 
/*******
  * @package xbMaps
 * @version 0.3.0.b 18th September 2021
 * @filesource admin/views/catinfo/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

class XbmapsViewCatinfo extends JViewLegacy {

	public function display($tpl = null) {
		
		$this->item = $this->get('Item');
		
		$this->addToolBar();
		XbmapsHelper::addSubmenu('catinfo');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
		// Set the document
		$this->setDocument();
	}
	
	protected function addToolBar() {
		$canDo = XbmapsHelper::getActions();
		
		ToolBarHelper::title(Text::_( 'XBMAPS' ).': '.Text::_( 'XBMAPS_TITLE_CATINFO' ), 'tag' );
		
		ToolbarHelper::custom('catinfo.catslist', 'folder', '', 'XBMAPS_CATLIST', false) ;
		
		if ($canDo->get('core.admin')) {
			ToolBarHelper::preferences('com_xbmaps');
		}
	}
	
	protected function setDocument() {
		$document = Factory::getDocument();
		$document->setTitle(strip_tags(Text::_('XBMAPS_TITLE_CATINFO')));
	}
	
}
