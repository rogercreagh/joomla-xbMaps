<?php
/*******
 * @package xbMaps Component
 * @filesource script.xbmaps.php
 * @version 1.4.0.0 9th December 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Version;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;

class com_xbmapsInstallerScript 
{	
    protected $jminver = '3.10';
    protected $jmaxver = '4.0';
    protected $extension = 'com_xbmaps';
    protected $ver = 'v0';
    protected $date = '';
    
    function preflight($type, $parent) {
        $jversion = new Version();
        $jverthis = $jversion->getShortVersion();
        if ((version_compare($jverthis, $this->jminver,'lt')) || (version_compare($jverthis, $this->jmaxver, 'ge'))) {
            throw new RuntimeException('xbMaps requires Joomla version minimum '.$this->jminver. ' and less than '.$this->jmaxver.'. You have '.$jverthis);
        }
        $message='';
        if ($type=='update') {
	        $componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbmaps/xbmaps.xml'));
	        $this->ver = $componentXML['version'];
	        $this->date = $componentXML['creationDate'];        	
	        $message = 'Updating xbMaps component from '.$componentXML['version'].' '.$componentXML['creationDate'];
        	$message .= ' to '.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate;
        }
        if ($message!='') { Factory::getApplication()->enqueueMessage($message,'');}
    }
    
    function install($parent) {        
    }
    
    function uninstall($parent) {
        $componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbmaps/xbmaps.xml'));
        $message = 'Uninstalling xbMaps component v.'.$componentXML['version'].' '.$componentXML['creationDate'].'<br />';
        
        $savedata = ComponentHelper::getParams('com_xbmaps')->get('savedata',0);
        if ($savedata == 0) {
            $tables = array(  '#__xbmaps_maps',
                '#__xbmaps_tracks',
                '#__xbmaps_maptracks',
                '#__xbmaps_markers',
                '#__xbmaps_mapmarkers'
            );
            $message .= $this->uninstallData($tables);
        } else {
            $message .= 'Data tables NOT removed, ';
            $message .= $this->saveCategories('com_xbmaps');            
        }
        $savefiles = ComponentHelper::getParams('com_xbmaps')->get('savefiles',0);
        if ($savefiles == 0) {
            $folders = array('/xbmaps-tracks','/images/xbmaps/gpx','/images/xbmaps/markers','/images/xbmaps/elevations');
            $message .= $this->uninstallFolders($folders);
        } else {
            $message .= 'Files in <code>/xbmaps/tracks</code> and <code>/images/xbmaps</code> have NOT been deleted';
        }
        
        Factory::getApplication()->enqueueMessage($message,'');
    }
    
    function update($parent) {
    	
        if (!file_exists(JPATH_ROOT.'/images/xbmaps/gpx')) {
            $res = mkdir(JPATH_ROOT.'/images/xbmaps/gpx', 0775, true);
            $message = ($res) ? 'New alternate GPX tracks folder <code>/images/xbmaps/gpx</code> created.<br />' : 'error creating new alternate track upload folder';
            Factory::getApplication()->enqueueMessage($message,'Info');
        }
        if (!file_exists(JPATH_ROOT.'/images/xbmaps/elevations')) {
            $res = mkdir(JPATH_ROOT.'/images/xbmaps/elevations', 0775, true);
            $message = ($res) ? 'New alternate Elevation images tracks folder <code>/images/xbmaps/elevations</code> created.<br />' : 'error creating new alternate elevations folder';
            Factory::getApplication()->enqueueMessage($message,'Info');
        }
        $message = '<br />Visit the <a href="index.php?option=com_xbmaps&view=dashboard" class="btn btn-small btn-info">';
    	$message .= 'xbMaps Dashboard</a> page for overview of status.</p>';
    	$message .= '<br />For ChangeLog see <a href="http://crosborne.co.uk/xbmaps/changelog" target="_blank">
            www.crosborne.co.uk/xbmaps/changelog</a></p>';
     	
    	Factory::getApplication()->enqueueMessage($message,'Message');
    }
    
    function postflight($type, $parent) {
    	$componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbmaps/xbmaps.xml'));
    	if ($type=='install') {
    		$message = 'xbMaps '.$componentXML['version'].' '.$componentXML['creationDate'].'<br />';
    		
    		//create aliases for tables - can't do in installsql in case they already exist in saved data
//     		$message .= 'creating xbMaps table indicies on alias : ';
//             $tables = array(
//                 array('name'=>'xbmaps_maps', 'alias'=>'map'),
//                 array('name'=>'xbmaps_tracks','alias'=>'track'),
//                 array('name'=>'xbmaps_markers','alias'=>'marker')
//                 );
//             $message .= $this->createTableIndexes($tables);

         	//create xbmaps gpx and images folders
         	$message .= '<i>Creating default folders</i><br />';
         	$folders = array(         	
         	    array('folder'=>'/xbmaps-tracks','title'=>'Default GPX tracks folder'),  		
         	    array('folder'=>'/images/xbmaps/gpx','title'=>'Alternate track folder'),
         	    array('folder'=>'/images/xbmaps/markers','title'=>'Marker images folder'),
         	    array('folder'=>'/images/xbmaps/elevations','title'=>'Elevation images folder')
         	);
    		$message .= $this->createFolders($folders);
    		
            $message .= $this->recoverCategories('com_xbmaps');
         	// create default categories using category table if they haven't been recovered
         	$cats = array(
	         			array("title"=>"Uncategorised","desc"=>"default fallback category for all xbMaps items"),
	         			array("title"=>"Maps","desc"=>"default parent category for xbMaps Maps"),
	         			array("title"=>"Markers","desc"=>"default parent category for xbMaps Markers"),
	         			array("title"=>"Tracks","desc"=>"default parent category for xbMaps Tracks")
         	);
         	$message .= $this->createCategory($cats);
         	
	        Factory::getApplication()->enqueueMessage($message,'Info');        
	              
	        echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
		border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';
	        echo '<h3>xbMaps Component installed</h3>';
	        echo '<p>version '.$componentXML['version'].' '.$componentXML['creationDate'].'<br />';
	        echo '<p>For help and information see <a href="https://crosborne.co.uk/xbmaps/doc" target="_blank">
	            www.crosborne.co.uk/xbmaps/doc</a> or use Help button in xbMaps Dashboard</p>';
	        echo '<h4>Next steps:</h4>';
		        echo '<p>IMPORTANT - <i>Review &amp; set the options</i>&nbsp;&nbsp;';
		        echo '<a href="index.php?option=com_config&view=component&component=com_xbmaps" class="btn btn-small btn-info">xbMaps Options</a>';
		        echo ' <br /><i>check the defaults match your expectations and save them.</i></p>';
		        echo '</div>';
    	}
	}
     
	public function createCategory($cats) {
		$message = 'Creating '.$this->extension.' categories. ';
		foreach ($cats as $cat) {
			$db = Factory::getDBO();
			$query = $db->getQuery(true);
			$query->select('id')->from($db->quoteName('#__categories'))
			->where($db->quoteName('title')." = ".$db->quote($cat['title']))
			->where($db->quoteName('extension')." = ".$db->quote('com_xbmaps'));
			$db->setQuery($query);
			if ($db->loadResult()>0) {
			    $message .= '"'.$cat['title'].' already exists<br /> ';
			} else {				
				$category = Table::getInstance('Category');
				$category->extension = $this->extension;
				$category->title = $cat['title'];
				$category->description = $cat['desc'];
				$category->published = 1;
				$category->access = 1;
				$category->params = '{"category_layout":"","image":"","image_alt":""}';
				$category->metadata = '{"page_title":"","author":"","robots":""}';
				$category->language = '*';
				// Set the location in the tree
				$category->setLocation(1, 'last-child');
				// Check to make sure our data is valid
				if ($category->check()) {
					if ($category->store(true)) {
					// Build the path for our category
						$category->rebuildPath($category->id);
						$message .= $cat['title'].' id:'.$category->id.' created ok. ';
					} else {						
						throw new Exception(500, $category->getError());
						//return '';
					}
				} else {
					throw new Exception(500, $category->getError());
					//return '';					
				}
			}
 		}
		return $message;
	}
	
	public function createFolders(array $folders) {
	    $message = '';
	    foreach ($folders as $folder) {
    	    $title = $folder['title'].' <code>'.$folder['folder'].'</code> ';
    	    if (!file_exists(JPATH_ROOT . $folder['folder'])) {
    	        if (mkdir(JPATH_ROOT . $folder['folder'], 0775)) {
    	            copy(JPATH_ROOT.'/media/com_xbmaps/index.html', JPATH_ROOT.$folder['folder'].'/index.html' );
    	            $message .= $title.' created okay.<br />';
    	        } else {
    	            $errmess = 'Error creating '.lcfirst($title).'. Please create folder manually to avoid errors<br />';
    	            Factory::getApplication()->enqueueMessage($errmess,'Error');
    	        }
    	    } else{
    	        $message .= $title.' already exists.<br />';
    	    }
	    }
	    return $message;
	}
	
	protected function uninstallData($tablenames) {
	    $message = '';
	    $db = Factory::getDBO();
 	    foreach ($tablenames as $table) {
     	    $db->setQuery('DROP TABLE IF EXISTS '.$db->qn($table));
     	    $res = $db->execute();
     	    if ($res === false) {
     	        $errmess .= 'Error deleting table '.$table.', please check manually';
     	        Factory::getApplication()->enqueueMessage($errmess,'Error');
//     	        return 'Not all data deleted';
     	    } else {
     	        $message .= ' - '.$table.' dropped, all data deleted<br />';
     	    } 
 	    }
// 	    $db->setQuery('DELETE FROM `#__categories` WHERE `extension` = '.$db->q('com_xbmaps'));
// 	    $res = $db->execute();
 	    $aliaslist = "'com_xbmaps.map','com_xbmaps.track','com_xbmaps.marker','com_xbmaps.category'";
 	    $subquery = 'select type_id from `#__content_types` where type_alias in ('.$aliaslist.')';
 	    $db->setQuery('DELETE FROM `#__ucm_history` WHERE `ucm_type_id` in ('.$subquery.')');
 	    $res = $db->execute();
 	    $db->setQuery('DELETE FROM `#__ucm_base` WHERE `ucm_type_id` in ('.$subquery.')');
 	    $res = $db->execute();
 	    $db->setQuery('DELETE FROM `#__ucm_content` WHERE `core_type_alias` in ('.$aliaslist.')');
 	    $res = $db->execute();
 	    $db->setQuery('DELETE FROM `#__contentitem_tag_map` WHERE `type_alias` in ('.$aliaslist.')');
 	    $res = $db->execute();
 	    $db->setQuery('DELETE FROM `#__content_types` WHERE `type_alias` in ('.$aliaslist.')');
 	    $res = $db->execute();

 	    // 	    $query = file_get_contents(JPATH_ADMINISTRATOR.'/components/com_xbmaps/sql/uninstall.mysql.utf8.sql');
// 	    $db->setQuery($query);
// //	    $qs=$db->getQuery();
// //	    Factory::getApplication()->enqueueMessage($qs->dump());
// 	    try {
//     	    $res = $db->execute();	        
// 	    } catch (Exception $e) {
// 	        Factory::getApplication()->enqueueMessage($e->getMessage(),'Error');
// 	        return '';
// 	    }
// 	    if ($res) {
// 	        $message = 'xbMaps data tables dropped, content_types deleted, tag references deleted byt tags themselves not cleared.';
// 	    } else {
// 	        $message = 'Error while unistalling xbMaps data, please check manually';
// 	    }
// 	    $typelist = $db->q('com_xbmaps.map').'.'.$db->q('com_xbmaps.marker').'.'.$db->q('com_xbmaps.track');
//         $query = $db->getQuery(true);
//         $query = 'DELETE FROM `#__ucm_history` WHERE ucm_type_id in 
// 	       (select type_id from `#__content_types` where type_alias in ($typelist))';

//	    if ($message == '') $message = 'problems deleting all tables<br />';
	    return $message;
	}
	
	protected function uninstallFolders(array $foldernames) {
	    $message = 'Deleting folders...<br />';
	    foreach ($foldernames as $folder) {
	        if (JFolder::exists(JPATH_ROOT.$folder)) {
	            if (JFolder::delete(JPATH_ROOT.$folder)){
	                $message .= ' - '.$folder.' deleted okay<br />';
	            } else {
	                $errmess = 'Problem deleting '.$folder.' - please check with a file manager (eg PhocaCommander component)';
	                Factory::getApplication()->enqueueMessage($errmess,'Error');
	            }
	        } else {
	            $message .= ' - '.$folder.' not found. Have you already deleted it? please check.<br />';
	        }
	    }
        return $message;
	}
	
    protected function saveCategories(string $component) {
	    $mess = 'saving categories... ';
        $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->update('#__categories')
	       ->set('extension='.$db->q('!'.$component.'!'))
	       ->where('extension='.$db->q($component));
        try {
	        $query->execute();
            $cnt = $db->getAffectedRows();	        
	    } catch (Exception $e) {
	        Factory::getApplication()->enqueueMessage($e->getMessage(),'Error');
	        return;
	    }
        if ($cnt>0) {
            $mess .='xbMaps categories.extension renamed as "<b>!</b>'.$component.'<b>!</b>". They will be recovered with original ids if reinstalling '.$component.'<br />';
        } else {
            $mess .= 'failed to save any '.$component.' categories<br />';
        }
        return $mess;
	}
	
	protected function recoverCategories(string $component) {
	    // Recover categories if they exist assigned to extension !com_xbfilms!
	    $cnt = 0;
	    $mess = 'recovering saved categories if they exist... ';
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->update('#__categories')
	       ->set('extension='.$db->q($component))
	       ->where('extension='.$db->q('!'.$component.'!'));
	    $db->setQuery($query);
	    try {
	        $db->execute();
	        $cnt = $db->getAffectedRows();
	    } catch (Exception $e) {
	        Factory::getApplication()->enqueueMessage($e->getMessage(),'Error');
	    }
	    $mess .= $cnt.' previous '.$component.' categories restored.<br />';
	    return $mess;
	}

	protected function createTableIndices(array $tables) {
	    $db = Factory::getDbo();
	    $prefix = Factory::getApplication()->get('dbprefix');
        $message = 'Checking existing indicies... ';
	    foreach ($tables as $table) {
    	    $querystr = 'ALTER TABLE '.$prefix.$table['name'].' ADD INDEX '.$table['alias'].'aliasindex (alias)';
	        $db->setQuery($querystr);
	        $err = false;
	        try {
        	    $db->execute();
    	    } catch (Exception $e) {
    	        if($e->getCode() == 1061) {
    	            $message .= '- '.$table['alias'].' already exists. ';
    	        } else {
    	            $errmess .= '[ERROR] creating '.$table['name'].' index : '.$e->getCode().' '.$e->getMessage().']';
    	            Factory::getApplcation()->enqueueMessage($errmess, 'Error');
    	            $err = true;
    	        }
    	    }
    	    if (!$err) {
    	        $message .= '- '.$table['alias'].' ok, ';
    	    }
	    }
	    return $message.'<br />';
	}
}

