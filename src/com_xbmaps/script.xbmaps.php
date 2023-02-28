<?php
/*******
 * @package xbMaps Component
 * @filesource script.xbmaps.php
 * @version 1.2.1.7 28th February 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
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
        $message = 'Uninstalling xbMaps component v.'.$componentXML['version'].' '.$componentXML['creationDate'];
    	$message .= '<br />GPX track files and any tags used have <b>not</b> been deleted.';
    	Factory::getApplication()->enqueueMessage($message,'');
    }
    
    function update($parent) {
    	
        if (!file_exists(JPATH_ROOT.'/images/xbmaps/gpx')) {
            $res = mkdir(JPATH_ROOT.'/images/xbmaps/gpx', 0775, true);
            $message .= ($res) ? 'New alternate GPX tracks folder <code>/images/xbmaps/gpx</code> created.<br />' : 'error creating new alternate track upload folder';
        } 
        $message = '<br />Visit the <a href="index.php?option=com_xbmaps&view=cpanel" class="btn btn-small btn-info">';
    	$message .= 'xbMaps Control Panel</a> page for overview of status.</p>';
    	$message .= '<br />For ChangeLog see <a href="http://crosborne.co.uk/xbmaps/changelog" target="_blank">
            www.crosborne.co.uk/xbmaps/changelog</a></p>';
     	
    	Factory::getApplication()->enqueueMessage($message,'Message');
    }
    
    function postflight($type, $parent) {
    	$componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbmaps/xbmaps.xml'));
    	if ($type=='install') {
    		$message = 'xbMaps '.$componentXML['version'].' '.$componentXML['creationDate'].'<br />';
         	//create xbmaps tracks folder
         	$folder = '/xbmaps-tracks';
        	if (!file_exists(JPATH_ROOT . $folder)) {
        	    if (mkdir(JPATH_ROOT . $folder, 0775)) {
                    $message .= 'Default GPX tracks folder <code>'.$folder.'</code> created.';
        	    } else {
                    '<b>Problem</b> creating default track upload folder <code>'.$folder.'</code>';
        	    }
         	}
         	$folder = JPATH_ROOT.'/images/xbmaps/gpx';
         	if (!file_exists($folder)) {
         	    if (mkdir($folder,0775,true)) {
         	        $message .= 'Alternate track folder <code>'.$folder.'<code> created.<br />';
             	} else {
             	    $message .= '<b>Problem</b> creating alternate track folder <code>'.$folder.'</code>.<br />';
         	    }
         	}         	    
         	$folder = JPATH_ROOT.'/images/xbmaps/markers';
         	if (!file_exists($folder)) {
         		$res = mkdir($folder,0775,true);
         		if ($res) {
             		$message .= 'Markers folder <code>'.$folder.'</code> created.';
             		$source = JPATH_ROOT.'/media/com_xbmaps/images/';
                 	$res = copy($source . 'marker-red.png', $folder.'/marker-red.png');
                 	$message .= ($res) ? ' <code>sample marker-red</code> copied.' : ' error copying samle marker file.';
                 	$res = copy($source . 'marker-green.png', $folder.'/marker-green.png');
                 	$message .= ($res) ? ' <code>sample marker-green image</code> copied.' : ' error copying sample marker file.';
                 	$message .= '<br />';
         		} else {
         		    $message .= 'error creating markers folder '.$folder.'.<br />';
         		}
         	}
         	// create default categories using category table
         	$cats = array(
	         			array("title"=>"Uncategorised","desc"=>"default fallback category for all xbMaps items"),
	         			array("title"=>"Maps","desc"=>"default parent category for xbMaps Maps"),
	         			array("title"=>"Markers","desc"=>"default parent category for xbMaps Markers"),
	         			array("title"=>"Tracks","desc"=>"default parent category for xbMaps Tracks"));
         	$message .= $this->createCategory($cats);
         	
	        Factory::getApplication()->enqueueMessage($message,'Info');        
	              
	        echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
		border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';
	        echo '<h3>xbMaps Component installed</h3>';
	        echo '<p>version '.$componentXML['version'].' '.$componentXML['creationDate'].'<br />';
	        echo '<p>For help and information see <a href="https://crosborne.co.uk/xbmaps/doc" target="_blank">
	            www.crosborne.co.uk/xbmaps/doc</a> or use Help button in xbMaps Control Panel</p>';
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
	
	
}

