<?php
/*******
 * @package xbMaps
 * @version 0.8.0.i 26th October 2021
 * @filesource admin/views/marker/view.html.php
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

class XbmapsViewMarker extends JViewLegacy {
		
	protected $form = null;
	
	public function display($tpl = null) {
		
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->canDo = XbmapsHelper::getActions('com_xbmaps', 'marker', $this->item->id);
		
		//$this->params      = $this->get('State')->get('params');
		$this->params = ComponentHelper::getParams('com_xbmaps');
		$this->map_type = $this->params->get('map_type','');
		$this->centre_latitude = $this->params->get('centre_latitude','');
		$this->centre_longitude = $this->params->get('centre_longitude','');
		$this->default_zoom = $this->params->get('default_zoom','');
		$this->marker_image_path = 'images/'.$this->params->get('def_markers_folder','');
		$this->w3w_api = $this->params->get('w3w_api','');
		$this->w3w_lang = $this->params->get('w3w_lang','');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
		}
		
		if ($this->getLayout() !== 'preview')
		{
		    $this->addToolBar();
		}
		
		parent::display($tpl);
		
		$this->setDocument();
	}
	
	protected function addToolbar() {
		$input = Factory::getApplication()->input;
		$input->set('hidemainmenu', true);
		$user = Factory::getUser();
		$userId = $user->get('id');
		$checkedOut     = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		
		$canDo = $this->canDo;
		
		$isNew = ($this->item->id == 0);
		
		if ($isNew) {
			$title = Text::_('XBMAPS_TITLE_NEWMARKER');
		} elseif ($checkedOut) {
			$title = Text::_('XBMAPS_TITLE_VIEWMARKER');
		} else {
			$title = Text::_('XBMAPS_TITLE_EDITMARKER');
		}
		ToolBarHelper::title($title, '');
		
		ToolbarHelper::apply('marker.apply');
		ToolbarHelper::save('marker.save');
		ToolbarHelper::save2new('marker.save2new');
		ToolbarHelper::save2copy('marker.save2copy');
		if ($isNew) {
			ToolbarHelper::cancel('marker.cancel','JTOOLBAR_CANCEL');
		} else {
			ToolbarHelper::cancel('marker.cancel','JTOOLBAR_CLOSE');
		}
//		ToolbarHelper::custom('','spacer');
//		ToolBarHelper::custom('track.savepreview', 'eye', '', 'Preview Marker', false) ;
		
		ToolbarHelper::help( '', false,'https://crosborne.uk/xbmaps/doc?tmpl=component#markeredit' );
		
	}
	
	protected function setDocument() {
		$document = Factory::getDocument();
		$document->setTitle(strip_tags(($this->item->id == 0) ? Text::_('XBMAPS_TITLE_NEWMARKER') : Text::_('XBMAPS_TITLE_EDITMARKER')));
	}
	
}