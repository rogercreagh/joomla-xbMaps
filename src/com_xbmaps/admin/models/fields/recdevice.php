<?php
/*******
 * @package xbMaps
 * @version 0.4.0.a 24th September 2021
 * @filesource admin/models/fields/recdevice.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Factory;

FormHelper::loadFieldClass('combo');

class JFormFieldRecdevice extends JFormFieldCombo {
	
	protected $type = 'Recdevice';
	
	public function getOptions() {
		
		$options = array();
				
		$db = Factory::getDbo();
		$query  = $db->getQuery(true);
		
		$query->select('DISTINCT rec_device AS text, rec_device AS value')
		->from('#__xbmaps_tracks')
		->order('rec_device');
		
		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
