<?php
/*******
 * @package xbMaps
 * @version 0.3.0.c 18th September 2021
 * @filesource site/models/catlist.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

class XbmapsModelCatlist extends JModelList {
	
	public function __construct($config = array()) {
		if (empty($config['filterfileds'])) {
			$config['filter_fields'] = array ('id','title','path', 'parent','mapcnt','mrkcnt','trkcnt' );
		}
		parent::__construct($config);
	}
	
 	protected function populateState($ordering = null, $direction = null) {
		// Load state from the request.
		$app = Factory::getApplication();
		
		// Load the parameters.
		$params = Factory::getApplication()->getParams();
		$this->setState('params', $params);
		
		parent::populateState($ordering, $direction);
		
	}
 	
	protected function getListQuery() {
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT c.`id` AS id, c.`path` AS path, c.level AS level, c.`title` AS title, 
            c.`description` AS description, c.extension AS extension, c.`lft`');
		$query->from('#__categories AS c');
		
		$query->select('(SELECT COUNT(*) FROM #__xbmaps_maps AS tb WHERE tb.catid = c.id ) AS mapcnt');
		$query->select('(SELECT COUNT(*) FROM #__xbmaps_markers AS tr WHERE tr.catid = c.id ) AS mrkcnt');
		$query->select('(SELECT COUNT(*) FROM #__xbmaps_tracks AS tr WHERE tr.catid = c.id ) AS trkcnt');
		
		$query->where('c.extension = '.$db->quote('com_xbmaps'));
		
		// Filter by published state
		$query->where('published = 1');
		
/* 
		// Search in title/id/synop
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search,'s:')===0) {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
				$query->where('(c.description LIKE ' . $search.')');
			} else {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(c.title LIKE ' . $search . ')');
			}
		}
 */	
/* 		
		//filter by branch, using alias to include all children
		$branch = $this->getState('filter.branch');
		if ($branch != '') {
			$query->where('c.alias LIKE '.$db->quote('%'.$branch.'%'));
		}
 */		
		// Add the list ordering clause.
		$orderCol       = $this->state->get('list.ordering', 'title');
		$orderDirn      = $this->state->get('list.direction', 'ASC');
		$query->order($db->escape('extension, '.$orderCol.' '.$orderDirn));
		
		//$query->group('t.id');
		
		return $query;		
		
	}
	
	public function getItems() {
		$items  = parent::getItems();
		foreach ($items as $cat) {
			$cat->allcnt = $cat->mapcnt + $cat->mrkcnt + $cat->trkcnt;
		}
		return $items;
	}
}
