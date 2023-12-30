<?php
/*******
 * @package xbMaps Component
 * @version 1.4.4.2 29th December 2023
 * @filesource site/models/map.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Helper\TagsHelper;

class XbmapsModelMap extends JModelItem {
		
	protected function populateState() {
		$app = Factory::getApplication();
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('map.id', $id);
		
		// Load the parameters.
		$this->setState('params', Factory::getApplication()->getParams());
		parent::populateState();
		
	}
	
	public function getItem($id = null) {
	    $app = Factory::getApplication();
		if (!isset($this->item) || !is_null($id)) {
			
			$id    = is_null($id) ? $this->getState('map.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('a.id AS id, a.title AS title, a.description AS description,
				a.centre_latitude AS centre_latitude, a.centre_longitude as centre_longitude,
				a.default_zoom AS default_zoom, a.map_type AS map_type, a.summary AS summary,
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata ');
			$query->from('#__xbmaps_maps AS a');
			$query->select('c.title AS category_title');
			$query->leftJoin('#__categories AS c ON c.id = a.catid');
			$query->where('a.id = '.$id);
			$db->setQuery($query);
			if ($this->item = $db->loadObject()) {
				
				// Load the JSON string
				$params = new Registry;
				$params->loadString($this->item->params, 'JSON');
				$this->item->params = $params;
				
				// Merge global params with item params
				$params = clone $this->getState('params');
				$params->merge($this->item->params);
				$this->item->params = $params;
				$this->item->tracks = XbmapsGeneral::mapTracksArray($this->item->id,1);
				$sum = 0;
				$trackstate = array();				
				foreach ($this->item->tracks as $track) {
				    $trackstate[$track->id] = 1;
				    $cookie_name = 'track'.$track->id;
				    if(!isset($_COOKIE[$cookie_name])) {
				        $trackstate[$track->id] = 0;
				    } else {
				        $trackstate[$track->id] = $_COOKIE[$cookie_name];
				    }
				}
				//if no checkboxes check we'll check them all as if a map has any tracks we must show at least 1
				if (array_sum($trackstate) == 0) {
				    foreach ($trackstate as $track=>$state) {
				        $trackstate[$track] = 1;
				        setcookie('track'.$track, 1, time() + 86400, "/");
				    }
				}
				$this->item->trackstate = $trackstate;
				//get markers
				$this->item->markers = XbmapsGeneral::mapMarkersArray($this->item->id,1);
				
				$tagsHelper = new TagsHelper;
				$this->item->tags = $tagsHelper->getItemTags('com_xbmaps.map' , $this->item->id);
				
				return $this->item;			
			}
		} //end if item or id not exists
		return false;
	}
	
}