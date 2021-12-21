<?php
/*******
 * @package xbMaps Component
 * @version 0.1.0 28th June 2021
 * @filesource admin/views/map/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class XbmapsViewMap extends JViewLegacy {
	
	protected $form = null;
	
	public function display($tpl = null) {
		
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->canDo = XbmapsHelper::getActions('com_xbmaps', 'map', $this->item->id);
		
		$params      = $this->get('State')->get('params');
		
		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
			return false;
		}
		
		$this->addToolBar();
		
		parent::display($tpl);
		// Set the document
		$this->setDocument();
		
	}
	
	protected function addToolBar()
	{
		$input = Factory::getApplication()->input;
		$input->set('hidemainmenu', true);
		$user = Factory::getUser();
		$userId = $user->get('id');
		$checkedOut     = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		
		$canDo = $this->canDo;
		
		$isNew = ($this->item->id == 0);
				
		if ($isNew) {
			$title = Text::_('XBMAPS_TITLE_NEWMAP');
		} elseif ($checkedOut) {
			$title = Text::_('XBMAPS_TITLE_VIEWMAP');
		} else {
			$title = Text::_('XBMAPS_TITLE_EDITMAP');
		}
		ToolBarHelper::title($title, '');
		
		ToolbarHelper::apply('map.apply');
		ToolbarHelper::save('map.save');
		ToolbarHelper::save2new('map.save2new');
		ToolbarHelper::save2copy('map.save2copy');
		if ($isNew) {
			ToolbarHelper::cancel('map.cancel','JTOOLBAR_CANCEL');
		} else {
			ToolbarHelper::cancel('map.cancel','JTOOLBAR_CLOSE');
		}
		ToolbarHelper::custom('','spacer');
		if (!$isNew) {
			ToolBarHelper::custom('map.savepreview', 'eye', '', 'Preview Map', false) ;
		}
		ToolbarHelper::help( '', false,'https://crosborne.uk/xbmaps/doc?tmpl=component#mapedit' );
	}
	
	protected function setDocument()
	{
		$document = Factory::getDocument();
		$document->setTitle(strip_tags(($this->item->id == 0) ? Text::_('XBMAPS_TITLE_NEWMAP') : Text::_('XBMAPS_TITLE_EDITMAP')));
	}
	
}