<?php
/*******
 * @package xbMaps Component
 * @version 1.2.0.1 1st February 2023
 * @filesource admin/models/fields/folderfilelist.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;

FormHelper::loadFieldClass('list');

class JFormFieldFolderFileList extends JFormFieldList {
    
    protected $type = 'FolderFileList';
    
    public function getOptions() {
        
        $options = array();
        $params = ComponentHelper::getParams('com_xbpeople');
        $def_folder = $params->get('def_tracks_folder','xbmaps-tracks');
        
        $path = $def_folder; //$this->element['directory'];
        //set path from component options
        
        if (!is_dir($path)) {
            if (is_dir(JPATH_ROOT . '/' . $path)) {
                $path = JPATH_ROOT . '/' . $path;
            } else {
                return;
            }
        }
        
        $path = Path::clean($path);
        
        $files = Folder::files($path,'\.gpx$',true, true);
        $prevfolder = '';
        if (is_array($files)) {
            foreach ($files as $file) {
                $file = str_replace(JPATH_ROOT, '', $file);
                $path_parts = pathinfo($file);
                $filepath =  $path_parts['dirname'];
                if ($prevfolder == $filepath) {
                    $name = $path_parts['filename'];
                } else {
                    $splen = 60-strlen($path_parts['filename'].$filepath);
                    $splen = ($splen>0) ? $splen : 0; 
                    $name = $path_parts['filename'].str_repeat('&nbsp;',$splen).' ('.$filepath.')';
                    $prevfolder = $filepath;
                }
                $options[] = HTMLHelper::_('select.option', $file, $name);
            }
            $options = array_merge(parent::getOptions(), $options);
        }
        return $options;                   
    }
}
