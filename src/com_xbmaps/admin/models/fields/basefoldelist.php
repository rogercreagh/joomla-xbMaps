<?php
/*******
 * @package xbMaps Component
 * @version 1.4.0.0 7th December 2023
 * @filesource admin/models/fields/gpxfolderlist.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('folderlist');

/**
 * @name BaseFolderList
 * @desc extends FolderList class to all selectrion of the base directory as well as subdirectories
 */
class JFormFieldBaseFolderList extends JFormFieldFolderList {
    
    protected $type = 'BaseFolderList';
    
    public function getOptions() {
        $base_folder = trim($this->element['directory'],"/ ");
        $base = new stdClass;
        $base->text = $base_folder;
        $base->value = $base_folder;
        $default = array($base);
        $options = parent::getOptions();
        foreach ($options as $opt) {
            $opt->text = '└─ '.$opt->text;
            $opt->value = $base_folder.'/'.$opt->value;
        }       
        $options = array_merge($default, $options );
        return $options;
    }
}