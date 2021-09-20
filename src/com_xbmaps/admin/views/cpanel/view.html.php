<?php
/*******
 * @package xbMaps
 * @version 0.3.0.g 20th September 2021
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
		
		$this->catStates = $this->get('CatStates');
		$this->cats = $this->get('Cats');
		$this->tags = $this->get('Tagcnts');
		
		$cat = XbmapsHelper::getCat($this->params->get('def_new_mapcat','0'));
		$this->mapcat = (is_null($cat)) ? '<i>not set</i>' : '<b>'.$cat->title.'</b>';
		$cat = XbmapsHelper::getCat($this->params->get('def_new_markercat'));
		$this->markercat = (is_null($cat)) ? '<i>not set</i>' : '<b>'.$cat->title.'</b>';
		$cat = XbmapsHelper::getCat($this->params->get('def_new_trackcat'));
		$this->trackcat = (is_null($cat)) ? '<i>not set</i>' : '<b>'.$cat->title.'</b>';
		
		$this->xmldata = Installer::parseXMLInstallFile(JPATH_COMPONENT_ADMINISTRATOR . '/xbmaps.xml');
		$this->client = $this->get('Client');
		
		$params = ComponentHelper::getParams('com_xbmaps');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
		    throw new Exception(implode("\n", $errors), 500);
		}
		
		$clink='index.php?option=com_xbmaps&view=catinfo&id=';
		$this->catlist = '<ul class="inline">';
		foreach ($this->cats as $key=>$value) {
		    $this->catlist .= '<li>';
		    if ($value['level']==1) {
		        $this->catlist .= '&nbsp;&nbsp;&nbsp;';
		    } else {
		        $this->catlist .= str_repeat('-&nbsp;', $value['level']-1);
		    }
		    $lbl = $value['published']==1 ? 'label-success' : '';
		    $this->catlist .='<a class="label label-success" href="'.$clink.$value['id'].'">'.$value['title'].'</a>&nbsp;(<i>'.$value['mapcnt'].':'.$value['mrkcnt'].':'.$value['trkcnt'].'</i>) ';
	        $this->catlist .= '</li>';
		}
		$this->catlist .= '</ul>';
		
		$tlink='index.php?option=com_xbmaps&view=taginfo&id=';
		$this->taglist = '<ul class="inline">';
		foreach ($this->tags['tags'] as $key=>$value) {
		    //       	$result[$key] = $t->tagcnt;
		    $this->taglist .= '<li><a class="label label-info" href="'.$tlink.$value['id'].'">'.$key.'</a>&nbsp;(<i>'.$value['mapcnt'].':'.$value['mrkcnt'].':'.$value['trkcnt'].')</i></li> ';
		}
		$this->taglist .= '</ul>';
		//        $result['taglist'] = trim($result['taglist'],', ');
		
	
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