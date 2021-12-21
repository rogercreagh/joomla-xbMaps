<?php
/*******
 * @package xbMaps Component
 * @version 0.5.0.d 30th September 2021
 * @filesource site/models/marker.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

class XbmapsModelMarker extends JModelItem {
		
	protected function populateState() {
		$app = Factory::getApplication();
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('marker.id', $id);
		
		// Load the parameters.
		$this->setState('params', Factory::getApplication()->getParams());
		parent::populateState();
		
	}
	
	public function getItem($id = null) {
		if (!isset($this->item) || !is_null($id)) {
			
			$id    = is_null($id) ? $this->getState('marker.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('a.id AS id, a.title AS title, a.summary AS summary,a.alias AS alias,
                a.latitude AS latitude, a.longitude AS longitude, a.marker_type AS marker_type,
				a.state AS published, a.catid AS catid, a.params AS params ');
			$query->from('#__xbmaps_markers AS a');
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
				
				return $this->item;			
			}
		} //end if item or id not exists
		return false;
	}
	
}