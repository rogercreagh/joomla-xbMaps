<?php
/*******
 * @package xbMaps
 * @version 0.7.0.a 5th October 2021
 * @filesource site/models/map.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

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
				
/* 				$item = &$this->item;
				// Load the JSON string
				$params = new JRegistry;
				$params->loadString($item->params, 'JSON');
				
				// Merge global params with item params
				$comparams = clone $this->getState('params');
//				$params->merge($item->params);
				$params->merge($comparams);
				$item->params = $params;
 */		
				// Load the JSON string
				$params = new Registry;
				$params->loadString($this->item->params, 'JSON');
				$this->item->params = $params;
				
				// Merge global params with item params
				$params = clone $this->getState('params');
				$params->merge($this->item->params);
				$this->item->params = $params;
				$this->item->tracks = XbmapsGeneral::mapTracksArray($this->item->id,1);
				//get markers
				$this->item->markers = XbmapsGeneral::mapMarkersArray($this->item->id,1);
				
				return $this->item;			
			}
		} //end if item or id not exists
		return false;
	}
	
}