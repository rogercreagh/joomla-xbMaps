<?php
/*******
 * @package xbMaps
 * @version 0.7.0.d 11th October 2021
 * @filesource site/models/maplist.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;

class XbmapsModelMaplist extends JModelList {
	
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array ('title', 'a.title',
					'catid', 'a.catid', 'category_id',
					'category_title' );
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null) {
		$app = Factory::getApplication('site');
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
		$categoryId = $app->getUserStateFromRequest('catid', 'catid','');
		$app->setUserState('catid', '');
		$this->setState('categoryId',$categoryId);
		$tagId = $app->getUserStateFromRequest('tagid', 'tagid','');
		$app->setUserState('tagid', '');
		$this->setState('tagId',$tagId);
		
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
		$query->select('a.id AS id, a.title AS title, a.alias AS alias,
            a.description AS description, a.summary AS summary, a.catid AS catid,
            a.state AS published, a.access AS access,
			a.created AS created, a.created_by AS created_by, a.created_by_alias AS created_by_alias,
			a.modified AS modified, a.modified_by AS modified_by,
            a.checked_out AS checked_out, a.checked_out_time AS checked_out_time,
            a.metadata AS metadata, a.ordering AS ordering, a.params AS params, a.note AS note');
		$query->from('#__xbmaps_maps AS a');
		
		
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
		
		// Filter by published state
		$query->where('state = 1');
//		$published = $this->getState('filter.published');
//		if (is_numeric($published)) {
//			$query->where('state = ' . (int) $published);
//		} else if ($published === '') {
//			$query->where('(state IN (0, 1))');
//		}
		
		// Filter by category.
		$app = Factory::getApplication('site');
		$categoryId = $app->getUserStateFromRequest('catid', 'catid','');
		$app->setUserState('catid', '');
		$subcats=0;
		if ($categoryId=='') {
			$categoryId = $this->getState('filter.category_id');
			//        $subcats = $this->getState('filter.subcats');
		}
		if (is_numeric($categoryId)) {
			//            if ($subcats) {
			//                $query->where('a.catid IN ('.(int)$categoryId.','.self::getSubCategoriesList($categoryId).')');
			//            } else {
			$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
			//            }
		}
		
		// Filter by search in title/id
		$search = $this->getState('filter.search');
		
		if (!empty($search)) {
			if (stripos($search, 'i:') === 0) {
				$query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 2));
			} elseif (stripos($search,':')!= 1) {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('a.title LIKE ' . $search );
			}
		}
		
		//filter by tags
		//TODO move this whole tag filter section to a helper function passing in query object
		$tagId = $app->getUserStateFromRequest('tagid', 'tagid','');
		$app->setUserState('tagid', '');
		if (!empty($tagId)) {
			$tagfilt = array(abs($tagId));
			$taglogic = $tagId>0 ? 0 : 2;
		} else {
			$tagfilt = $this->getState('filter.tagfilt');
			$taglogic = $this->getState('filter.taglogic');  //0=ANY 1=ALL 2= None
		}
		
		if (($taglogic === '2') && (empty($tagfilt))) {
			//if we select tagged=excl and no tags specified then only show untagged items
			$subQuery = '(SELECT content_item_id FROM #__contentitem_tag_map
 				WHERE type_alias = '.$db->quote('com_xbmaps.map').')';
			$query->where('a.id NOT IN '.$subQuery);
		}
		
		if (!empty($tagfilt))  {
			$tagfilt = ArrayHelper::toInteger($tagfilt);
			
			if ($taglogic==2) { //exclude anything with a listed tag
				// subquery to get a virtual table of item ids to exclude
				$subQuery = '(SELECT content_item_id FROM #__contentitem_tag_map
					WHERE type_alias = '.$db->quote('com_xbmaps.map').
					' AND tag_id IN ('.implode(',',$tagfilt).'))';
				$query->where('a.id NOT IN '.$subQuery);
			} else {
				if (count($tagfilt)==1)	{ //simple version for only one tag
					$query->join( 'INNER', $db->quoteName('#__contentitem_tag_map', 'tagmap')
							. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id') )
							->where(array( $db->quoteName('tagmap.tag_id') . ' = ' . $tagfilt[0],
									$db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_xbmaps.map') )
									);
				} else { //more than one tag
					if ($taglogic == 1) { // match ALL listed tags
						// iterate through the list adding a match condition for each
						for ($i = 0; $i < count($tagfilt); $i++) {
							$mapname = 'tagmap'.$i;
							$query->join( 'INNER', $db->quoteName('#__contentitem_tag_map', $mapname).
									' ON ' . $db->quoteName($mapname.'.content_item_id') . ' = ' . $db->quoteName('a.id'));
							$query->where( array(
									$db->quoteName($mapname.'.tag_id') . ' = ' . $tagfilt[$i],
									$db->quoteName($mapname.'.type_alias') . ' = ' . $db->quote('com_xbmaps.map'))
									);
						}
					} else { // match ANY listed tag
						// make a subquery to get a virtual table to join on
						$subQuery = $db->getQuery(true)
						->select('DISTINCT ' . $db->quoteName('content_item_id'))
						->from($db->quoteName('#__contentitem_tag_map'))
						->where( array(
								$db->quoteName('tag_id') . ' IN (' . implode(',', $tagfilt) . ')',
								$db->quoteName('type_alias') . ' = ' . $db->quote('com_xbmaps.map'))
								);
						$query->join('INNER',
								'(' . $subQuery . ') AS ' . $db->quoteName('tagmap')
								. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
								);
						
					} //endif all/any
				} //endif one/many tag
			}
		} //if not empty tagfilt
		
		// Add the list ordering clause.
		$orderCol       = $this->state->get('list.ordering', 'title');
		$orderDirn      = $this->state->get('list.direction', 'ASC');
		if ($orderCol == 'a.ordering' || $orderCol == 'a.catid') {
			$orderCol = 'category_title '.$orderDirn.', a.ordering';
		}
		
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		//$query->group('a.id');
		return $query;		
	}
	
	public function getItems() {
		$items  = parent::getItems();
		$tagsHelper = new TagsHelper;
		
		$app = Factory::getApplication();
		$mps = array();
		for ($i = 0; $i < count($items); $i++) {
			$mps[$i] = $items[$i]->id;
		}
		$app->setUserState('maps.sortorder', $mps);
		
		foreach ($items as $i=>$item) {
			$item->tags = $tagsHelper->getItemTags('com_xbmaps.map' , $item->id);
			$item->markers = XbmapsGeneral::mapMarkersArray($item->id,1);;
    		$item->tracks = XbmapsGeneral::mapTracksArray($item->id,1);
		}
		return $items;
	}
	
}
	