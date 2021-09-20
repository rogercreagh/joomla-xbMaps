<?php
/*******
 * @package xbMaps
 * @version 0.3.0.f 20th September 2021
 * @filesource site/models/tag.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbmapsModelTag extends JModelItem {
	
	protected function populateState() {
		$app = Factory::getApplication('site');
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('tag.id', $id);
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
	}
	
	public function getItem($id = null) {
		if (!isset($this->item) || !is_null($id)) {
			$id    = is_null($id) ? $this->getState('tag.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('t.id AS id, t.path AS path, t.title AS title, t.note AS note, t.description AS description,
				t.published AS published,  t.metadata AS metadata' );
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS mb WHERE mb.type_alias='.$db->quote('com_xbmaps.map').' AND mb.tag_id = t.id) AS mapcnt');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS mp WHERE mp.type_alias='.$db->quote('com_xbmaps.marker').' AND mp.tag_id = t.id) AS mrkcnt');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS mr WHERE mr.type_alias='.$db->quote('com_xbmaps.track').' AND mr.tag_id = t.id) AS trkcnt');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS ma WHERE ma.tag_id = t.id) AS allcnt ');
			$query->from('#__tags AS t');
			$query->where('t.id = '.$id);
			$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
			
			$db->setQuery($query);
			
			if ($this->item = $db->loadObject()) {
				$item = &$this->item;
				//calculate how many non xbmaps items the tag applies to to save doing it later
				$item->othercnt = $item->allcnt - ($item->mapcnt + $item->mrkcnt + $item->trkcnt);
				//get titles and ids of films, people and reviews with this tag
				$db    = Factory::getDbo();
				if ($item->mapcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('b.id AS id, b.title AS title')->from('#__tags AS t');
					$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
					$query->join('LEFT','#__xbmaps_maps AS b ON b.id = m.content_item_id');
					$query->where("t.id='".$item->id."' AND m.type_alias='com_xbmaps.map'");
					$query->order('b.title');
					$db->setQuery($query);
					$item->maps = $db->loadObjectList();
				} else {
					$item->maps = '';
				}
				if ($item->mrkcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('p.id AS id, p.title AS title')->from('#__tags AS t');
					$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
					$query->join('LEFT','#__xbmaps_markers AS p ON p.id = m.content_item_id');
					$query->where("t.id='".$item->id."' AND m.type_alias = 'com_xbmaps.marker'");
					$query->order('p.title');
					$db->setQuery($query);
					$item->markers = $db->loadObjectList();
				} else {
					$item->markers='';
				}
				if ($item->trkcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('r.id AS id, r.title AS title')->from('#__tags AS t');
					$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
					$query->join('LEFT','#__xbmaps_tracks AS r ON r.id = m.content_item_id');
					$query->where("t.id='".$item->id."' AND m.type_alias='com_xbmaps.track'");
					$query->order('r.title');
					$db->setQuery($query);
					$item->tracks = $db->loadObjectList();
				} else {
					$item->tracks = '';
				}
				if ($item->othercnt > 0) {
					$query = $db->getQuery(true);
					$query->select('m.type_alias AS type_alias, m.core_content_id, m.content_item_id AS othid, c.core_title AS core_title, c.core_content_item_id AS item_id');
					$query->from('#__contentitem_tag_map AS m');
					$query->join('LEFT','#__ucm_content AS c ON m.core_content_id = c.core_content_id');
					$query->where('m.tag_id = '.$item->id);
					$query->where('m.type_alias NOT IN ('.$db->quote('com_xbmaps.map').','.$db->quote('com_xbmaps.marker').','.$db->quote('com_xbmaps.track').')');
					$query->order('m.type_alias, c.core_title');
					$db->setQuery($query);
					$item->others = $db->loadObjectList();
					$item->othcnts = array();
					foreach ($item->others as $i=>$oth) {
						$comp = substr($oth->type_alias, 0,strpos($oth->type_alias, '.'));
						if (array_key_exists($comp,$item->othcnts)) {
							$item->othcnts[$comp] ++;
						} else {
							$item->othcnts[$comp] = 1;
						}
					}
				} else {
					$item->others = '';
				}
			}
			
			return $this->item;
		} //endif isset
	} //end function getItem
}
		
