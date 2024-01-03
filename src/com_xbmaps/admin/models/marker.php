<?php
/*******
 * @package xbMaps Component
 * @version 1.5.1.0 3rd January 2024
 * @filesource admin/models/marker.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Application\ApplicationHelper;

class XbmapsModelMarker extends JModelAdmin {
	
    public $typeAlias = 'com_xbmaps.marker';
    
    public function getItem($pk = null) {
        
        $item = parent::getItem($pk);
        
        if (!empty($item->id)) {
            $tagsHelper = new TagsHelper;
            $item->tags = $tagsHelper->getTagIds($item->id, 'com_xbmaps.marker');
        }
        //fix null dates for calendar controls
        if (empty($item->created)) {
            $item->created = Factory::getDate()->toSql();
        }
        if (empty($item->created_by)) {
            $item->created_by = Factory::getUser()->id;
        }
        if (empty($item->modified)) {
            $item->modified = '0000-00-00 00:00:00'; //$date->toSql();
        }        
        
        return $item;
    }
    
    public function getTable($type = 'Marker', $prefix = 'XbmapsTable', $config = array()) {
        
        return JTable::getInstance($type, $prefix, $config);
    }
    
    public function getForm($data = array(), $loadData = true) {
        
        $form = $this->loadForm( 'com_xbmaps.marker', 'marker',
            array('control' => 'jform','load_data' => $loadData)
            );
        
        if (empty($form)) {
            return false;
        }
        
        $params = ComponentHelper::getParams('com_xbmaps');
        // set any field attributes according to params if needed
        $def_markers_folder = 'images/'.$params->get('def_markers_folder','');
        $form->setFieldAttribute('marker_image','directory',$def_markers_folder,'params');
        if ($params->get('w3w_api')!='') {
            $form->setFieldAttribute('marker_popcoords','default','4','params');
        }
        
        return $form;
    }
    
    protected function loadFormData() {
        $data = Factory::getApplication()->getUserState('com_xbmaps.edit.marker.data', array() );
        
        $params = ComponentHelper::getParams('com_xbmaps');
        
        if (empty($data)) {
            
            $data = $this->getItem();
	        if ($data->latitude=='') {
	            $data->latitude = $params->get('centre_latitude','');
	        }
	        $data->dmslatitude = XbmapsGeneral::Deg2DMS($data->latitude,true,false);
	        if ($data->longitude=='') {
	            $data->longitude = $params->get('centre_longitude','');
	        }
	        $data->dmslongitude = XbmapsGeneral::Deg2DMS($data->longitude,false,false);

            //load subform data as required
            if ($data->id) {
                $data->maplist=$this->getMaplist($data->id);
                $data->tracklist=$this->getTracklist($data->id);
            }
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
                ->from($db->quoteName('#__xbmaps_markers'));
                
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
	            ->update($db->quoteName('#__xbmaps_markers'))
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
	        $table = $this->getTable('marker');
	        foreach ($pks as $i=>$item) {
	            $table->load($item);
	            if (!$table->delete($item)) {
	                $word = ($cnt == 1)?  Text::_('XBMAPS_MARKER') : Text::_('XBMAPS_MARKERS');
	                Factory::getApplication()->enqueueMessage($cnt.$word.Text::_('XBMAPS_DEL_BEFORE_ERROR'));
	                $this->setError($table->getError());
	                return false;
	            }
	            $table->reset();
	            $cnt++;
	        }
	        $word = ($cnt == 1)? Text::_('XBMAPS_MARKER') : Text::_('XBMAPS_MARKERS');
	        Factory::getApplication()->enqueueMessage($cnt.$word.' '.Text::_('XBMAPS_DELETED'));
	        return true;
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
	    
	    if (parent::save($data)) {
	        //other stuff if req - eg saving subform data
	        $tid = $this->getState('marker.id');
	        $this->storeMapList($tid, $data['maplist']);
	        $this->storeTrackList($tid, $data['tracklist']);
	        
	        return true;
	    }
	    
	    return false;
	}
	
	private function getMapList($mkid) {
	    $db = $this->getDbo();
	    $query = $db->getQuery(true);
	    $query->select('mt.map_id as map_id,  mt.listorder AS maplistorder');
	    $query->from('#__xbmaps_mapmarkers AS mt');
	    $query->innerjoin('#__xbmaps_maps AS a ON mt.map_id = a.id');
	    $query->where('mt.marker_id = '.$mkid);
	    $query->order('a.title ASC');
	    $db->setQuery($query);
	    $list = $db->loadAssocList();
	    return $list;
	}
	
	private function getTrackList($mkid) {
	    $db = $this->getDbo();
	    $query = $db->getQuery(true);
	    $query->select('mt.track_id as track_id,  mt.listorder AS tracklistorder');
	    $query->from('#__xbmaps_trackmarkers AS mt');
	    $query->innerjoin('#__xbmaps_tracks AS a ON mt.track_id = a.id');
	    $query->where('mt.marker_id = '.$mkid);
	    $query->order('a.title ASC');
	    $db->setQuery($query);
	    $list = $db->loadAssocList();
	    return $list;
	}
	
	private function storeMapList($marker_id, $mapList) {
	    //delete existing role list
	    $db = $this->getDbo();
	    $query = $db->getQuery(true);
	    $query->delete($db->quoteName('#__xbmaps_mapmarkers'));
	    $query->where('marker_id = '.$marker_id);
	    $db->setQuery($query);
	    $db->execute();
	    //restore the new list
	    //$listorder=0;
	    foreach ($mapList as $map) {
	        if ($map['map_id'] > 0) {
	            //$listorder ++;
	            $query = $db->getQuery(true);
	            $query->insert($db->quoteName('#__xbmaps_mapmarkers'));
	            $query->columns('map_id,marker_id,listorder');
	            $query->values('"'.$map['map_id'].'","'.$marker_id.'","'.$map['maplistorder'].'"');
	            //try
	            $db->setQuery($query);
	            $db->execute();
	        }
	    }
	}

	private function storeTrackList($marker_id, $trackList) {
	    //delete existing role list
	    $db = $this->getDbo();
	    $query = $db->getQuery(true);
	    $query->delete($db->quoteName('#__xbmaps_trackmarkers'));
	    $query->where('marker_id = '.$marker_id);
	    $db->setQuery($query);
	    $db->execute();
	    //restore the new list
	    //$listorder=0;
	    foreach ($trackList as $track) {
	        if ($track['track_id'] > 0) {
	            //$listorder ++;
	            $query = $db->getQuery(true);
	            $query->insert($db->quoteName('#__xbmaps_trackmarkers'));
	            $query->columns('track_id,marker_id,listorder');
	            $query->values('"'.$track['track_id'].'","'.$marker_id.'","'.$track['tracklistorder'].'"');
	            //try
	            $db->setQuery($query);
	            $db->execute();
	        }
	    }
	}
	
}
