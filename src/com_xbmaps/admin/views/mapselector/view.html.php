<?php
/*******
 * @package xbMaps
 * @version 0.1.1.j 25th August 2021
 * @filesource admin/views/mapview/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

class XbmapsViewMapselector extends JViewLegacy {

	public function display($tpl = null) {
		
    	//we will be displaying in a modal window
		$params = ComponentHelper::getParams('com_xbmaps');
		$app = Factory::getApplication();
		$this->latitude			= $app->input->get( 'lat', $params->get('centre_latitude','52.152'));
		$this->longitude = $app->input->get( 'lng', $params->get('centre_longitude','-1.149'));
		$this->zoom	= $app->input->get( 'zoom', $params->get('default_zoom','8'));
		$this->map_type	= $app->input->get( 'map_type', $params->get('map_type','osm') );
		$this->settype	= $app->input->get( 'type', 'map');
		$this->homebutton = $params->get('map_home_button');
		$this->clustering = $params->get('marker_clustering');
		$this->searchdisplay = 2 + ($params->get('w3w_api','')!='') ? 4 : 0;
		parent::display($tpl);
		
	}
}