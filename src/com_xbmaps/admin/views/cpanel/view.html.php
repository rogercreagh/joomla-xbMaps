<?php
/*******
 * @package xbMaps
 * @version 0.1.2.d 10th September 2021
 * @filesource admin/views/cpanel/view.html.php
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
use Joomla\CMS\Installer\Installer;

class XbmapsViewCpanel extends JViewLegacy {
		
	public function display($tpl = null) {
		
		// Check for errors.
//		if (count($errors = $this->get('Errors'))) {
//			throw new Exception(implode("\n", $errors), 500);
//		}
	    $this->params = ComponentHelper::getParams('com_xbmaps');
	    $this->mapcats = $this->params->get('global_use_cats') && $this->params->get('maps_use_cats');
	    $this->mrkcats = $this->params->get('global_use_cats') && $this->params->get('markers_use_cats');
	    $this->trkcats = $this->params->get('global_use_cats') && $this->params->get('tracks_use_cats');
	    $this->maptags = $this->params->get('global_use_tags') && $this->params->get('maps_use_tags');
	    $this->mrktags = $this->params->get('global_use_tags') && $this->params->get('markers_use_tags');
	    $this->trktags = $this->params->get('global_use_tags') && $this->params->get('tracks_use_tags');
	    $this->trkview = $this->params->get('enable_track_view');
	    
		$this->mapStates = $this->get('MapStates');
		$this->markerStates = $this->get('MarkerStates');
		$this->trackStates = $this->get('TrackStates');
		$this->trackCnts = $this->get('TrackCounts');
		$this->markerCnts = $this->get('MarkerCounts');
		
		$cat = XbmapsHelper::getCat($this->params->get('def_new_mapcat'));
		$this->mapcat = (is_null($cat)) ? '<i>not set</i>' : '<b>'.$cat->title.'</b>';
		$cat = XbmapsHelper::getCat($this->params->get('def_new_markercat'));
		$this->markercat = (is_null($cat)) ? '<i>not set</i>' : '<b>'.$cat->title.'</b>';
		$cat = XbmapsHelper::getCat($this->params->get('def_new_trackcat'));
		$this->trackcat = (is_null($cat)) ? '<i>not set</i>' : '<b>'.$cat->title.'</b>';
		
		$this->xmldata = Installer::parseXMLInstallFile(JPATH_COMPONENT_ADMINISTRATOR . '/xbmaps.xml');
		$this->client = $this->get('Client');
		
		$params = ComponentHelper::getParams('com_xbmaps');
		
		$this->addToolbar();
		XbmapsHelper::addSubmenu('cpanel');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
		
		$this->setDocument();
	}
	
	protected function addToolbar() {
		$canDo = XbmapsHelper::getActions();
		
		ToolbarHelper::title(Text::_( 'XBMAPS_TITLE_CPANEL' ), '' );
		
		if ($canDo->get('core.admin')) {
			ToolbarHelper::preferences('com_xbmaps');
		}
		ToolbarHelper::help( '', false,'https://crosborne.uk/xbmaps/doc?tmpl=component#admin' );
	}
	
	protected function setDocument() {
		$document = Factory::getDocument();
		$document->setTitle(strip_tags(Text::_('XBMAPS_TITLE_CPANEL')));
	}
	
}