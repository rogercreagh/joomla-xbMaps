<?php
/*******
 * @package xbMaps Content Plugin
 * @version 0.1.0.d 24th December 2021
 * @filesource xbmaps.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;

class plgContentXbmapsInstallerScript
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
            $componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_SITE . '/plugins/content/xbmaps/xbmaps.xml'));
            $this->ver = $componentXML['version'];
            $this->date = $componentXML['creationDate'];
            $message = 'Updating xbMaps Content Plugin from '.$componentXML['version'].' '.$componentXML['creationDate'];
            $message .= ' to '.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate;
        }
        if ($message!='') { Factory::getApplication()->enqueueMessage($message,'');}
    }
    
    function install($parent) {
    }
    
    function uninstall($parent) {
        $componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_SITE . '/plugins/content/xbmaps/xbmaps.xml'));
        $message = 'Uninstalling xbMaps Content Plugin v.'.$componentXML['version'].' '.$componentXML['creationDate'];
        Factory::getApplication()->enqueueMessage($message,'');
        $found = $this->findXbrefs();
        if ($found) {
            echo '<div class="alert alert-warning"><p>'.count($found).' articles found with {xbmaps...} shortcodes:</p><ul>';
            foreach ($found as $a) {
                echo '<li><a href="'.Uri::root().'administrator/index.php?option=com_content&task=article.edit&id='.$a['id'].'" target="_blank">'.$a['title'].'</a></li>';
            }
            echo '</ul><p>Clicking the links above will open the edit page for each article in a new tab to enable you to remove them manually if you wish.</p></div>';
        }
    }
    
    function update($parent) {
        
        $message = '<br />For ChangeLog see <a href="http://crosborne.co.uk/xbmaps_plgcon/changelog" target="_blank">
            www.crosborne.co.uk/xbmaps_plgcon/changelog</a></p>';
        
        Factory::getApplication()->enqueueMessage($message,'Message');
    }
    
    function postflight($type, $parent) {
        $componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_SITE . '/plugins/content/xbmaps/xbmaps.xml'));
        if ($type=='install') {
            $message = 'xbMaps Content Plugin '.$componentXML['version'].' '.$componentXML['creationDate'].'<br />';
            //create xbmaps tracks folder
            Factory::getApplication()->enqueueMessage($message,'Info');
            
//            $plginfo = PluginHelper::getPlugin('content','xbmaps');
//            Factory::getApplication()->enqueueMessage('<pre>'.print_r($plginfo,true).'</pre>');

            $myid = $this->pluginid();
            
            echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
		border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';
            echo '<h3>xbMaps Content Plugin installed</h3>';
            echo '<p>version '.$componentXML['version'].' '.$componentXML['creationDate'].'<br />';
            echo '<p>For help and information see <a href="https://crosborne.co.uk/xbmaps_plgcon/doc" target="_blank">
	            www.crosborne.co.uk/xbmaps_plgcon/doc</a> or see the Help tab on the <a href="index.php?option=com_config&view=component&component=com_xbmaps">plugin options page</a></p>';
            echo '<h4>Next steps:</h4>';
            echo '<p>IMPORTANT - <i>The plugin is not yet enabled, visit the options page to set defaults and enable the plugin</i>&nbsp;&nbsp;';
            echo '<a href="index.php?option=com_plugins&view=plugin&layout=edit&id='.$myid.'" class="btn btn-small btn-info">xbMaps Content Plugin Options</a>';
            echo ' <br /><i>check the defaults match your expectations and save them.</i></p>';
            echo '<h4>Shortcode format</h3>';
            echo '<p>Minimal: <code>{xbmaps view=map,id=1}</code>  All options: <code>{xbmaps view=track,alias=tuesday-ride,title=1,desc=1,info=1,float=left,wd=50,ht=400}</code>';
            echo '</div>';
        }
    }
   
    function findXbrefs() {
        $articles = array();
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('id','title','introtext','fulltext')));
        $query->from($db->quoteName('#__content'));
        $query->where($db->quoteName('introtext') . ' LIKE \'%{xbmaps %\'');
        $query->orWhere($db->quoteName('fulltext') . ' LIKE \'%{xbmaps %\'');
        $db->setQuery($query);
        $articles = $db->loadAssocList('id');
        return $articles;
    }
    
    function pluginid() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('extension_id'));
        $query->from($db->quoteName('#__extensions'));
        $query->where($db->quoteName('name') . '= '.$db->quote('Content - xbMaps'));
        $db->setQuery($query);
        return $db->loadResult();
    }
}
    