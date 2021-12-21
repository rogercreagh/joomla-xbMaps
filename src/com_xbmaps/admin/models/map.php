<?php
/*******
 * @package xbMaps Component
 * @version 0.8.0.i 26th October 2021
 * @filesource admin/models/map.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\Registry\Registry;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Application\ApplicationHelper;

class XbmapsModelMap extends JModelAdmin {
	
	public $typeAlias = 'com_xbmaps.map';
	
	public function getItem($pk = null) {
		
		$item = parent::getItem($pk);
		
		if (!empty($item->id)) {
			// Convert the metadata field to an array.
			$registry = new Registry($item->metadata);
			$item->metadata = $registry->toArray();
			
			$tagsHelper = new TagsHelper;
			$item->tags = $tagsHelper->getTagIds($item->id, 'com_xbmaps.map');
		}
		//fix null dates for clendar controls
		if (empty($item->created)) {
			$item->created = Factory::getDate()->toSql();
		}
		if (empty($item->created_by)) {
			$item->created_by = Factory::getUser()->id;
		}
		if (empty($item->modified)) {
			$item->modified = '0000-00-00 00:00:00'; 
		}
		
		return $item;
	}
	
	public function getTable($type = 'Map', $prefix = 'XbmapsTable', $config = array()) {
		
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) {
		
		$form = $this->loadForm( 'com_xbmaps.map', 'map',
					array('control' => 'jform','load_data' => $loadData)
				);
		
		if (empty($form)) {
			return false;
		}
		
		$params = ComponentHelper::getParams('com_xbmaps');
		if ($params->get('w3w_api')!='') {
		    $form->setFieldAttribute('marker_infocoords','default','4','params');
		}
		
		return $form;
	}
	
	protected function loadFormData() {
		$data = Factory::getApplication()->getUserState('com_xbmaps.edit.map.data', array() );
		
		$params = ComponentHelper::getParams('com_xbmaps');
		
		if (empty($data)) {
 			$data = $this->getItem();
 			if ($data->centre_latitude=='') {
 			    $data->centre_latitude = $params->get('centre_latitude','');
 			}
 			$data->dmslatitude = XbmapsGeneral::Deg2DMS($data->centre_latitude,true,false);
 			if ($data->centre_longitude=='') {
 			    $data->centre_longitude = $params->get('centre_longitude','');
 			}
 			$data->dmslongitude = XbmapsGeneral::Deg2DMS($data->centre_longitude,false,false);
 			if ($data->default_zoom=='') {
 			    $data->default_zoom = $params->get('default_zoom','');
 			}
 			//load subform data as required
 			$data->tracklist=$this->getTrackList();
 			$data->markerlist=$this->getMarkerList();
 			$data->params['hid_w3wapi'] = $params->get('w3w_api','');
		}
		
		return $data;
	}
	
	protected function prepareTable($table) {
		$date = Factory::getDate();
		$user = Factory::getUser();
		$db = Factory::getDbo();
		
		$table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias = ApplicationHelper::stringURLSafe($table->alias);
		
		if (empty($table->alias)) {
			$table->alias = ApplicationHelper::stringURLSafe($table->title);
		}
		// Set the values
		if (empty($table->created)) {
			$table->created = $date->toSql();
		}
		if (empty($table->created_by)) {
			$table->created_by = Factory::getUser()->id;
		}
		if (empty($table->id)) {
			
			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$query = $db->getQuery(true)
				->select('MAX(ordering)')
				->from($db->quoteName('#__xbmaps_maps'));
				
				$db->setQuery($query);
				$max = $db->loadResult();
				
				$table->ordering = $max + 1;
			}
			//set modified to null to stop joomla defaulting to zero
			$table->modified = NULL;
		} else {
			// not new so set/update the modified details
			$table->modified    = $date->toSql();
			$table->modified_by = $user->id;
		}
	}
	
	public function publish(&$pks, $value = 1) {
		if (!empty($pks)) {
			foreach ($pks as $item) {
				$db = $this->getDbo();
				$query = $db->getQuery(true)
				->update($db->quoteName('#__xbmaps_maps'))
				->set('state = ' . (int) $value)
				->where('id='.$item);
				$db->setQuery($query);
				if (!($db->execute())) {
					$this->setError($db->getErrorMsg());
					return false;
				}
			}
			return true;
		}
	}
	
	public function delete(&$pks, $value = 1) {
		if (!empty($pks)) {
			$cnt = 0;
			$table = $this->getTable('map');
			foreach ($pks as $i=>$item) {
				$table->load($item);
				if (!$table->delete($item)) {
					$mapword = ($cnt == 1)?  Text::_('XBMAPS_MAP') : Text::_('XBMAPS_MAPS');
					Factory::getApplication()->enqueueMessage($cnt.$mapword.Text::_('XBMAPS_DEL_BEFORE_ERROR'));
					$this->setError($table->getError());
					return false;
				}
				$table->reset();
				$cnt++;
			}
			$mapword = ($cnt == 1)? Text::_('XBMAPS_MAP') : Text::_('XBMAPS_MAPS');
			Factory::getApplication()->enqueueMessage($cnt.$mapword.' '.Text::_('XBMAPS_DELETED'));
			return true;
			//?what about deleting any track links???
		}
	}
	
	public function save($data) {
		$input = Factory::getApplication()->input;
		
		if ($input->get('task') == 'save2copy') {
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));
			
			if ($data['title'] == $origTable->title) {
				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['title'] = $title;
				$data['alias'] = $alias;
			} else {
				if ($data['alias'] == $origTable->alias) {
					$data['alias'] = '';
				}
			}
			// standard Joomla practice is to set the new copy record as unpublished
			$data['state'] = 0;
		}
		//set empty dates to null to stop j3 creating zero dates in mysql
		if ($data['rec_date']=='') { $data['rec_date'] = NULL; }
		
		if (parent::save($data)) {
			//other stuff if req - eg saving subform data
		    $mid = $this->getState('map.id');
		    $this->storeTrackList($mid, $data['tracklist']);
		    $this->storeMarkerList($mid, $data['markerlist']);
		    
			return true;
		}
		
		return false;
	}

	private function getTrackList() {
	    $db = $this->getDbo();
	    $query = $db->getQuery(true);
	    $query->select('mt.track_id as track_id,  mt.track_colour AS track_colour');
	    $query->from('#__xbmaps_maptracks AS mt');
	    //$query->innerjoin('#__xbmaps_tracks AS a ON mt.track_id = a.id');
	    $query->where('mt.map_id = '.(int) $this->getItem()->id);
	    $query->order('mt.listorder ASC');
	    $db->setQuery($query);
	    $list = $db->loadAssocList();
	    foreach ($list as &$trk) {
	        if ($trk['track_colour']=='') {
	            $query = $db->getQuery(true);
	            $query->select('track_colour')->from('#__xbmaps_tracks')->where('id ='.$trk['track_id']);
	            $db->setQuery($query);
	            $trk['track_colour'] = $db->loadResult();	            
	        }
	    }
	    return $list;
	}
	
	private function storeTrackList($map_id, $trackList) {
	    //delete existing role list
	    $db = $this->getDbo();
	    $query = $db->getQuery(true);
	    $query->delete($db->quoteName('#__xbmaps_maptracks'));
	    $query->where('map_id = '.$map_id);
	    $db->setQuery($query);
	    $db->execute();
	    //restore the new list
	    $listorder=0;
	    foreach ($trackList as $trk) {
	        if ($trk['track_id'] > 0) {
	            $listorder ++;
	            $query = $db->getQuery(true);
	            $query->select('track_colour')->from('#__xbmaps_maptracks')->where('id ='.$trk['track_id']);
	            $db->setQuery($query);
	            $tc = $db->loadResult();
	            if ($trk['track_colour']=='') {
	                $trk['track_colour']=$tc;
	            }
	            $query = $db->getQuery(true);
	            $query->insert($db->quoteName('#__xbmaps_maptracks'));
	            $query->columns('map_id,track_id,track_colour,listorder');
	            $query->values('"'.$map_id.'","'.$trk['track_id'].'","'.$trk['track_colour'].'","'.$listorder.'"');
	            //try
	            $db->setQuery($query);
	            $db->execute();
	        }
	    }
	}
	
	private function getMarkerList() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('mt.marker_id as marker_id');
		$query->from('#__xbmaps_mapmarkers AS mt');
		$query->where('mt.map_id = '.(int) $this->getItem()->id);
		$query->order('mt.listorder ASC');
		$db->setQuery($query);
		$list = $db->loadAssocList();
		return $list;
	}
	
	private function storeMarkerList($map_id, $markerList) {
		//delete existing role list
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__xbmaps_mapmarkers'));
		$query->where('map_id = '.$map_id);
		$db->setQuery($query);
		$db->execute();
		//restore the new list
		$listorder=0;
		foreach ($markerList as $mrk) {
			if ($mrk['marker_id'] > 0) {
				$listorder ++;
				$query = $db->getQuery(true);
				$query->insert($db->quoteName('#__xbmaps_mapmarkers'));
				$query->columns('map_id,marker_id,listorder');
				$query->values('"'.$map_id.'","'.$mrk['marker_id'].'","'.$listorder.'"');
				//try
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
	
}
