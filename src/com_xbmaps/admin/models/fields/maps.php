<?php
/*******
 * @package xbMaps
 * @version 0.1.0.k 16th July 2021
 * @filesource admin/models/fields/maps.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

JFormHelper::loadFieldClass('list');

class JFormFieldMaps extends JFormFieldList {
	
	protected $type = 'Maps';
	
	/**
	 * @desc gets a list of all maps with three most recently added at top,
	 * then published ones sorted by title
	 * {@inheritDoc}
	 * @see JFormFieldList::getOptions()
	 */
	public function getOptions() {
		
		$options = array();
		
		$db = Factory::getDbo();
		$query  = $db->getQuery(true);
		$query->select('id As value')
		->select('title AS text')
		->from('#__xbmaps_maps')
		->where('state = 1')  //only published items
		->order('title ASC'); 
		$db->setQuery($query);
		$all = $db->loadObjectList();

		$query->clear();
		$query->select('id As value')
		->select('title AS text')
		->from('#__xbmaps_maps')
		->order('created DESC')
		->setLimit('3');
		$recent = $db->loadObjectList();
		//add a separator between recent and alpha
		$blank = new stdClass();
		$blank->value = 0;
		$blank->text = '------------';
		$recent[] = $blank;
		//the recent ones will also appear in the full list
		// Merge any additional options in the XML definition with recent (top 3) and alphabetical list.
		$options = array_merge(parent::getOptions(), $recent, $all);
		return $options;
				
	}
}