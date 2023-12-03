<?php
/*******
 * @package xbMaps Component
 * @version 1.3.2.0 3rd December 2023
 * @filesource admin/models/fields/tracks.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

class JFormFieldTracks extends JFormFieldList {
    
    protected $type = 'Tracks';
    
    /**
     * @desc gets a list of all tracks with three most recently added and published at top, 
     * then all published ones sorted by name (first/last as per params), then any unpublished ones at the end
     * excludes trashed and archived
     * {@inheritDoc}
     * @see JFormFieldList::getOptions()
     */
    public function getOptions() {
        
    	$params = ComponentHelper::getParams('com_xbmaps');
    	$options = array();
        
        $db = Factory::getDbo();
        $query  = $db->getQuery(true);
        
        $query->select('id As value')
        ->select('CONCAT(title," : ", DATE_FORMAT(rec_date, "%y %b %d") ) AS text')
	        ->from('#__xbmaps_tracks')
	        ->where('state = 1')
	        ->order('rec_date DESC, text ASC');
        $db->setQuery($query);
        $all = $db->loadObjectList();
        
        $query->clear();
        $query->select('id As value')
        	->select('title AS text')
	        ->from('#__xbmaps_tracks')
	        ->where('state = 1')
	        ->order('created DESC')
	        ->setLimit('3');
        $db->setQuery($query);
        $recent = $db->loadObjectList();
        //add separator between recent and alpha
        $blank = new stdClass();
        $blank->value = 0;
        $blank->text = '------------';
        $recent[] = $blank;
        
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $recent, $all);
        return $options;
    }
}
