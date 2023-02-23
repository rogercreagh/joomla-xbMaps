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

class JFormFieldGpxFolderList extends JFormFieldFolderList {
    
    protected $type = 'GpxFolderList';
    
    public function getOptions() {
 
        $options = parent::getOptions();
        foreach ($options as $opt) {
            $opt->text = ' -- '.$opt->text;
        }
        $params = ComponentHelper::getParams('com_xbmaps');
        $def_folder = trim($params->get('def_tracks_folder','xbmaps-tracks'),'/');
        $def = new stdClass;
        $def->text = '..';
        $def->value = '..';
        $default = array($def);
        $def->text = $def_folder;
        $def->value = $def_folder;
        $default[] = $def;
        
        $options = array_merge($default, $options );
        return $options;
    }
}