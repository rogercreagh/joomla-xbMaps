<?php
/*******
 * @package xbMaps Component
 * @version 1.2.1.6 23rd February 2023
 * @filesource admin/models/fields/gpxfolderlist.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('folderlist');

/**
 * @name GpxFolderList
 * @desc extends FolderList to set directory to the parameter base_gpx_folder and allow selection of directory itself as well as subfolders
 * @author rogerco
 *
 */
class JFormFieldGpxFolderList extends JFormFieldFolderList {
    
    protected $type = 'GpxFolderList';
    
    public function getOptions() {
 
        $params = ComponentHelper::getParams('com_xbmaps');
        $def_folder = trim($params->get('base_gpx_folder','xbmaps-tracks'),'/');
        $this->element['directory'] = $def_folder;
        $def = new stdClass;
        $def->text = $def_folder;
        $def->value = $def_folder;
        $default = array($def);
        $options = parent::getOptions();
        foreach ($options as $opt) {
            $opt->text = ' -- '.$opt->text;
            $opt->value = $def_folder.'/'.$opt->value;
        }
        
        $options = array_merge($default, $options );
        return $options;
    }
}