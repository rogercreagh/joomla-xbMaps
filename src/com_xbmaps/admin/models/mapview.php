<?php
/*******
 * @package xbMaps
 * @version 0.7.0.a 5th October 2021
 * @filesource admin/models/mapview.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\Registry\Registry;
//use Joomla\CMS\Helper\TagsHelper;
//use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
//use Joomla\CMS\Application\ApplicationHelper;

class XbmapsModelMapview extends JModelItem {
	
	//public $typeAlias = 'com_xbmaps.mapview';
	
	protected function populateState() {
		$app = Factory::getApplication('admin');
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('map.id', $id);
		
		// Load the parameters.
		$params = ComponentHelper::getParams('com_xbmaps');
		$this->setState('params', $params);
		parent::populateState();
		
	}
	
	public function getItem($id = null) {
		
		if (!isset($this->item) || !is_null($id)) {
			$id    = is_null($id) ? $this->getState('map.id') : $id;
			if (!$id) {
				return false;
			} else {
				$db = $this->getDbo();
				$query = $db->getQuery(true);
				$query->select('a.id AS id, a.title AS title, a.description AS description, a.summary AS summary,
				a.centre_latitude AS centre_latitude, a.centre_longitude as centre_longitude,
				a.default_zoom AS default_zoom, a.map_type AS map_type,
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata ');
				$query->from('#__xbmaps_maps AS a');
				$query->select('c.title AS category_title');
				$query->leftJoin('#__categories AS c ON c.id = a.catid');
				$query->where('a.id = '.$id);
				$db->setQuery($query);
				
				if ($this->item = $db->loadObject()) {
					
					$item = &$this->item;
					// Load the JSON string
					$params = new Registry;
					$params->loadString($item->params, 'JSON');
					$item->params = $params;
					
					// Merge global params with item params
					$params = clone $this->getState('params');
					$params->merge($item->params);
					$item->params = $params;
					
					//get tracks
					$this->item->tracks = XbmapsGeneral::mapTracksArray($this->item->id);
					
					//get markers
					$this->item->markers = XbmapsGeneral::mapMarkersArray($this->item->id);
					
					return $this->item;		
				} //end if loadobject
			}
		} //end if item not ok
		return false;
	} //end getitem()
	
}
