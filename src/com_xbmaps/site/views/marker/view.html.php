<?php
/*******
 * @package xbMaps
 * @version 0.5.0.d 30th September 2021
 * @filesource site/views/marker/view.html.php
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
		
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->params = $this->item->params;
		
		$gcat = $this->params->get('global_use_cats');
		$mcat = $this->params->get('markers_use_cats');
		$this->show_cats = 0;
		if ($gcat>0) {
		    $this->show_cats = $mcat;
		    if ($this->params['markers_use_cats']!=='') {
		        $this->show_cats = $this->params['markers_use_cats'];
		    }
		}
		
		$gtags = $this->params->get('global_use_tags');
		$mtags = $this->params->get('markers_use_tags');
		$this->show_tags = false;
		if ($gtags >0) {
		    $this->show_tags = $mtags;
		    if ($this->params['markers_use_tags']!=='') {
		        $this->show_tags = $this->params['markers_use_tags'];
		    }
		}
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
		
		parent::display($tpl);
		
	}
	
	
}