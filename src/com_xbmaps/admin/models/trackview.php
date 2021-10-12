<?php
/*******
 * @package xbMaps
 * @version 0.7.0.d 12th October 2021
 * @filesource admin/models/trackview.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Component\ComponentHelper;

class XbmapsModelTrackview extends JModelItem {
		
	protected function populateState() {
		$app = Factory::getApplication();
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('track.id', $id);
		
		// Load the parameters.
		$params = ComponentHelper::getParams('com_xbmaps');
		$this->setState('params', $params);
		parent::populateState();
		
	}
	
	public function getItem($id = null) {
		if (!isset($this->item) || !is_null($id)) {
			
			$id    = is_null($id) ? $this->getState('track.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('a.id AS id, a.title AS title, a.description AS description,a.alias AS alias,
                a.gpx_filename AS gpx_filename, a.rec_date AS rec_date, a.track_colour AS track_colour,
				a.rec_device AS rec_device, a.activity AS activity, a.summary AS summary,
				a.state AS published, a.catid AS catid, a.params AS params ');
			$query->from('#__xbmaps_tracks AS a');
			$query->select('c.title AS category_title');
			$query->leftJoin('#__categories AS c ON c.id = a.catid');
			$query->where('a.id = '.$id);
			$db->setQuery($query);
			if ($this->item = $db->loadObject()) {
				
				//get the list of maps assigned
				$this->item->maps = XbmapsGeneral::trackMapsArray($this->item->id,1);
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