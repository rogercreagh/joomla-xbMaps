<?php
/*******
 * @package xbMaps Component
 * @version 0.3.0.f 20th September 2021
 * @filesource site/models/tags.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbmapsModelTags extends JModelList {
	
	public function __construct($config = array()) {
		if (empty($config['filterfileds'])) {
			$config['filter_fields'] = array ('id','title','path', 'parent','mapcnt','mrkcnt','trkcnt' );
		}
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null) {
		// Load state from the request.
		$app = Factory::getApplication();
		
		$ip_type = $app->input->getStr('type');
		$mn_type = $app->input->getStr('mn_type');
		$fm_type = $app->input->getStr('fmtype');
		
		$type = $ip_type;
		if ($type =='') { $type = $mn_type; }
		if ($type =='') {
			$type = $fm_type;
			$no_btns = false;
		} else {
			$no_btns = true;
		}
		$this->setState('no_btns',$no_btns);				
		$this->setState('tagtype', $type);
		$app->setUserState('fmtype', $type);
		
		// Load the parameters.
		$params = Factory::getApplication()->getParams();
		$this->setState('params', $params);
		
		parent::populateState($ordering, $direction);
		//pagination limit
		$limit = $this->getUserStateFromRequest($this->context.'.limit', 'limit', 25 );
		$this->setState('limit', $limit);
		$this->setState('list.limit', $limit);
		$limitstart = $app->getUserStateFromRequest('limitstart', 'limitstart', $app->get('start'));
		$this->setState('list.start', $limitstart);		
		
	}
	
	protected function getListQuery() {
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('t.`id` AS id, t.`path` AS path, t.level AS level, t.`title` AS title, t.`description` AS description,
		 t.`note` AS note, t.`published` AS published,  t.`checked_out` AS checked_out,
         t.`checked_out_time` AS checked_out_time, t.`lft`');
		$query->from('#__tags AS t');
		$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
		
		//input group will override search group
		$filtype = $this->getState('filter.tagtype');
		$tagtype = $this->getState('tagtype');
		if ($tagtype =='') {$tagtype = $filtype;}
		if ($tagtype != '') {
			$query->where('m.type_alias = '.$db->quote('com_xbmaps.'.$tagtype));
		} else {
			$query->where("m.type_alias IN ('com_xbmaps.map','com_xbmaps.marker','com_xbmaps.track')");
		}
		
		$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS tb WHERE tb.tag_id = t.id AND tb.type_alias='.$db->quote('com_xbmaps.map').') AS mapcnt');
		$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS tp WHERE tp.tag_id = t.id AND tp.type_alias='.$db->quote('com_xbmaps.marker').') AS mrkcnt');
		$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS tr WHERE tr.tag_id = t.id AND tr.type_alias='.$db->quote('com_xbmaps.track').') AS trkcnt');
		
		// Filter by published state
		$query->where('published = 1');
		
		// Search in title/id/synop
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search,'s:')===0) {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
				$query->where('(t.description LIKE ' . $search.')');
			} else {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(t.title LIKE ' . $search . ')');
			}
		}
		
		//filter by branch
		$branch = $this->getState('filter.branch');
		if ($branch != '') {
			$query->where('t.alias LIKE '.$db->quote('%'.$branch.'%'));
		}
		
		// Add the list ordering clause.
		$orderCol       = $this->state->get('list.ordering', 'title');
		$orderDirn      = $this->state->get('list.direction', 'ASC');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		$query->group('t.id');
		
		return $query;		
		
	}
	
	public function getItems() {
		$items  = parent::getItems();
		return $items;
	}
}