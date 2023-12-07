<?php
/*******
 * @package xbMaps Component
 * @version 1.4.0.0 7th December 2023
 * @filesource admin/models/track.php
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

class XbmapsModelTrack extends JModelAdmin {
	
    public $typeAlias = 'com_xbmaps.track';
    
    public function getItem($pk = null) {
        
		$item = parent::getItem($pk);

		if (!empty($item->id)) {		    
		    $tagsHelper = new TagsHelper;
		    $item->tags = $tagsHelper->getTagIds($item->id, 'com_xbmaps.track');
		}
		//fix null dates for calendar controls
		if (empty($item->created)) {
			$item->created = Factory::getDate()->toSql();
		}
		if (empty($item->created_by)) {
			$item->created_by = Factory::getUser()->id;
		}
		if (empty($item->modified)) {
			$item->modified = ''; //'0000-00-00 00:00:00'; //$date->toSql();
		}
		if (!empty($item->gpx_filename)) {
			//$item->gpx_folder = pathinfo($item->gpx_filename,PATHINFO_DIRNAME);
//			$item->select_gpxfile = pathinfo($item->gpx_filename,PATHINFO_BASENAME);			
			//$item->gpx_folder = '';
		}
//		$params = ComponentHelper::getParams('com_xbmaps');
//		$item->gpx_folder = $params->get('base_gpx_folder','');
		
		
		return $item;
	}

	public function getTable($type = 'Track', $prefix = 'XbmapsTable', $config = array()) {
	    
	    return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) {
	    
	    $form = $this->loadForm( 'com_xbmaps.track', 'track',
	        array('control' => 'jform','load_data' => $loadData)
	        );
	    
	    if (empty($form)) {
	        return false;
	    }
	    
	    // set any field attributes according to params if needed
	    $params = ComponentHelper::getParams('com_xbmaps');
	    $base_gpx_folder = $params->get('base_gpx_folder','');
	    $form->setFieldAttribute('gpx_upload_folder','directory',$base_gpx_folder);
    	$form->setFieldAttribute('gpx_folder','directory',$base_gpx_folder,'params');
    	
    	$gpxfolder = Factory::getSession()->get('gpxfolder','');
    	$form->setFieldAttribute('gpx_file','directory', $gpxfolder,'params');
    	
    	$elevfolder = Factory::getSession()->get('elevfolder','');
    	if ($elevfolder != '') $form->setFieldAttribute('elev_file','directory', $elevfolder,'params');
    	
    	$def_track_colour = $params->get('def_track_colour','');
    	$form->setFieldAttribute('track_colour','default',$def_track_colour);
    	
	    return $form;
	}
	
	protected function loadFormData() {
	    $data = Factory::getApplication()->getUserState('com_xbmaps.edit.track.data', array() );
	    
	    $params = ComponentHelper::getParams('com_xbmaps');
	    
	    if (empty($data)) {
	    	
 	        $data = $this->getItem();
 	        
 	        //load default activity
 	        if ($data->activity =='') {
 	            $data->activity = $params->get('def_activity','');
 	        }

	    	//load subform data as required
 	        if ($data->id) {
 	        	$data->maplist=$this->getMaplist($data->id, $data->track_colour);
 	        }
 	        
 	        //if we have a gpx file read rec date and rec_device if empty from file i
 	        if (is_file(JPATH_ROOT.'/'.$data->gpx_filename)) {
 	            $gpxinfo = XbMapsHelper::parseGpxHeader($data->gpx_filename);
 	            if (($data->rec_date == '0000-00-00 00:00:00') || is_null($data->rec_date)) {
 	                $data->rec_date = $gpxinfo['recdate'];
 	            }
 	            if ($data->rec_device == '') {
 	                $data->rec_device = $gpxinfo['creator'];
 	            }
 	            
 	        }
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
//	    if (empty($table->rec_date)) {
//	    	$table->rec_date = $date->toSql();
//	    }
	    // 		if (empty($table->created_by_alias)) {
	    // 			$table->created_by_alias = Factory::getUser()->username; //make it an option to use name instead of username
	    // 		}
	    if (empty($table->id)) {
	        
	        // Set ordering to the last item if not set
	        if (empty($table->ordering)) {
	            $query = $db->getQuery(true)
	            ->select('MAX(ordering)')
	            ->from($db->quoteName('#__xbmaps_tracks'));
	            
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
                ->update($db->quoteName('#__xbmaps_tracks'))
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
            $table = $this->getTable('track');
            foreach ($pks as $i=>$item) {
                $table->load($item);
                if (!$table->delete($item)) {
                    $word = ($cnt == 1)?  Text::_('XBMAPS_TRACK') : Text::_('XBMAPS_TRACKS');
                    Factory::getApplication()->enqueueMessage($cnt.$word.Text::_('XBMAPS_DEL_BEFORE_ERROR'));
                    $this->setError($table->getError());
                    return false;
                }
                $table->reset();
                $cnt++;
            }
            $word = ($cnt == 1)? Text::_('XBMAPS_TRACK') : Text::_('XBMAPS_TRACKS');
            Factory::getApplication()->enqueueMessage($cnt.$word.' '.Text::_('XBMAPS_DELETED'));
            return true;
        }
    }
    
    public function save($data) {
        $input = Factory::getApplication()->input;
        $task = $input->get('task');
        
        if ($task == 'save2copy') {
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
        if ($task == 'setgpxfile') {
            //            $data['gpx_filename'] = $data['params']['base_gpx_folder'] .'/'.$data['params']['gpx_folder'].'/'.$data['params']['gpx_file'];
            $gpxfilename = '/'.$data['params']['gpx_folder'].'/'.$data['params']['gpx_file'];
            if (is_file(JPATH_ROOT.'/'.$gpxfilename)) {
                $data['gpx_filename'] = $gpxfilename;
            } else {
                $data['gpx_file'] = '';
                $data['gpx_filename'] = '';
            }
        }
        
        if ($task == 'setelevfile') {
            //            $data['gpx_filename'] = $data['params']['base_gpx_folder'] .'/'.$data['params']['gpx_folder'].'/'.$data['params']['gpx_file'];
            $elevfilename = '/'.$data['params']['elev_folder'].'/'.$data['params']['elev_file'];
            if (is_file(JPATH_ROOT.'/'.$elevfilename)) {
                $data['elev_filename'] = $elevfilename;
            } else {
                $data['elev_file'] = '';
                $data['elev_filename'] = '';
            }
        }
        
        //set empty dates to null to stop j3 creating zero dates in mysql
        if ($data['rec_date']=='') { $data['rec_date'] = NULL; }
        
        if (parent::save($data)) {
            //other stuff if req - eg saving subform data
        	$tid = $this->getState('track.id');
        	$this->storeMapList($tid, $data['maplist'],$data['track_colour']);
        	
            return true;
        }
        
        return false;
    }
    
    private function getMapList($tid, $tcol) {
    	$db = $this->getDbo();
    	$query = $db->getQuery(true);
    	$query->select('mt.map_id as map_id,  mt.track_colour AS track_colour, mt.listorder AS maplistorder');
    	$query->from('#__xbmaps_maptracks AS mt');
    	$query->innerjoin('#__xbmaps_maps AS a ON mt.map_id = a.id');
    	$query->where('mt.track_id = '.$tid);
    	$query->order('a.title ASC');
    	$db->setQuery($query);
    	$list = $db->loadAssocList();
    	foreach ($list as &$trk) {
    		if ($trk['track_colour']=='') {
    			$trk['track_colour'] = $tcol;
    		}
    	}
    	return $list;
    }
    
    private function storeMapList($track_id, $mapList, $tcol) {
    	//delete existing role list
    	$db = $this->getDbo();
    	$query = $db->getQuery(true);
    	$query->delete($db->quoteName('#__xbmaps_maptracks'));
    	$query->where('track_id = '.$track_id);
    	$db->setQuery($query);
    	$db->execute();
    	//restore the new list
    	//$listorder=0;
    	foreach ($mapList as $map) {
    		if ($map['map_id'] > 0) {
    			//$listorder ++;
    			if ($map['track_colour']=='') {
    				$map['track_colour']=$tcol;
    			}
    			$query = $db->getQuery(true);
    			$query->insert($db->quoteName('#__xbmaps_maptracks'));
    			$query->columns('map_id,track_id,track_colour,listorder');
    			$query->values('"'.$map['map_id'].'","'.$track_id.'","'.$map['track_colour'].'","'.$map['maplistorder'].'"');
    			//try
    			$db->setQuery($query);
    			$db->execute();
    		}
    	}
    }
    
}
