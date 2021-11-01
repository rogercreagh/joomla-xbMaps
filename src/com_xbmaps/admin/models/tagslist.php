<?php
/*******
 * @package xbMaps
 * @version 0.3.0.d 19th September 2021
 * @filesource admin/models/tagslist.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbmapsModelTagslist extends JModelList {
	
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'id', 'title', 'path','mapcnt','mrkcnt','trkcnt',
					'published', 'parent'
			);
		}
		parent::__construct($config);
	}
		
	protected function getListQuery() {
		// Initialize variables.
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT t.`id` AS id, t.`path` AS path, t.level AS level, t.`title` AS title, t.`description` AS description,
		 t.`note` AS note, t.`published` AS published,  t.`checked_out` AS checked_out, 
         t.`checked_out_time` AS checked_out_time, t.`lft`');
		$query->from('#__tags AS t');
		$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
		$query->where("m.type_alias IN ('com_xbmaps.map','com_xbmaps.marker','com_xbmaps.track')");
		$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS tb WHERE tb.tag_id = t.id AND tb.type_alias='.$db->quote('com_xbmaps.map').') AS mapcnt');
		$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS tp WHERE tp.tag_id = t.id AND tp.type_alias='.$db->quote('com_xbmaps.marker').') AS mrkcnt');
		$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS tr WHERE tr.tag_id = t.id AND tr.type_alias='.$db->quote('com_xbmaps.track').') AS trkcnt');
		$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS toth WHERE toth.tag_id = t.id AND toth.type_alias NOT IN 
            ('.$db->quote('com_xbmaps.map').','.$db->quote('com_xbmaps.marker').','.$db->quote('com_xbmaps.track').')) AS othcnt');
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('published = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(published IN (0, 1))');
		}
		
		//filter by tag branch
		$branchid = $this->getState('filter.branch');
		if (is_numeric($branchid)) {
			//need to have subquery to get the alias to find in the path
			$query->where("t.path LIKE CONCAT('%',(SELECT alias FROM #__tags WHERE id = ".$branchid."),'%')" );
		}
		
		// Filter by search in title/id/desc
		$search = $this->getState('filter.search');		
		if (!empty($search)) {
			if (stripos($search, 'i:') === 0) {
				$query->where($db->quoteName('t.id') . ' = ' . (int) substr($search, 2));
			} elseif ((stripos($search,'s:')===0) || (stripos($search,'d:')===0)) {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
				$query->where('(t.description LIKE ' . $search.')');
			} elseif (stripos($search,':')!= 1) {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(t.title LIKE ' . $search . ')');
			}
		}
		
		// Add the list ordering clause.
		$orderCol       = $this->state->get('list.ordering', 'title');
		$orderDirn      = $this->state->get('list.direction', 'ASC');		
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}

	public function getItems() {
		$items  = parent::getItems();
		return $items;
	}
}