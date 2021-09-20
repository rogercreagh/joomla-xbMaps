<?php
/*******
 * @package xbMaps
 * @version 0.3.0.f 20th September 2021
 * @filesource site/models/category.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbmapsModelCategory extends JModelItem {
	
	protected function populateState() {
		$app = Factory::getApplication('site');
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('cat.id', $id);
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
	}
	
	public function getItem($id = null) {
		if (!isset($this->item) || !is_null($id)) {
			$id    = is_null($id) ? $this->getState('cat.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('c.id AS id, c.path AS path, c.title AS title, c.description AS description, c.note AS note, c.metadata AS metadata, c.extension AS extension' );
			$query->select('(SELECT COUNT(*) FROM #__xbmaps_maps AS mb WHERE mb.catid = c.id) AS mapcnt');
			$query->select('(SELECT COUNT(*) FROM #__xbmaps_markers AS mp WHERE mp.catid = c.id) AS mrkcnt');
			$query->select('(SELECT COUNT(*) FROM #__xbmaps_tracks AS mr WHERE mr.catid = c.id) AS trkcnt');
			$query->from('#__categories AS c');
			$query->where('c.id = '.$id);
			
			try {
				$db->setQuery($query);
				$this->item = $db->loadObject();
			} catch (Exception $e) {
				$dberr = $e->getMessage();
				Factory::getApplication()->enqueueMessage($dberr.'<br />Query: '.$query, 'error');
			}
			if ($this->item) {
				$item = &$this->item;
				//get titles and ids of films, people and reviews in this category
				if ($item->mapcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('b.id AS id, b.title AS title')
						->from('#__categories AS c');
					$query->join('LEFT','#__xbmaps_maps AS b ON b.catid = c.id');
					$query->where('c.id='.$item->id);
					$query->order('b.title');
					$db->setQuery($query);
					$item->maps = $db->loadObjectList();
				} else {
					$item->maps = '';
				}
				if ($item->mrkcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('b.id AS id, b.title AS title')
					->from('#__categories AS c');
					$query->join('LEFT','#__xbmaps_markers AS b ON b.catid = c.id');
					$query->where('c.id='.$item->id);
					$query->order('b.title');
					$db->setQuery($query);
					$item->markers = $db->loadObjectList();
				} else {
					$item->markers='';
				}
				if ($item->trkcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('r.id AS id, r.title AS title')
						->from('#__categories AS c');
					$query->join('LEFT','#__xbmaps_tracks AS r ON r.catid = c.id');
					$query->where('c.id='.$item->id);
					$query->order('r.title');
					$db->setQuery($query);
					$item->tracks = $db->loadObjectList();
				} else {
					$item->tracks = '';
				}
			}
			
			return $this->item;
		} //endif isset
	} //end function getItem
}
		
