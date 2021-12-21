<?php
/*******
 * @package xbMaps Component
 * @version 0.4.0.a 24th September 2021
 * @filesource admin/models/fields/activity.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Factory;

FormHelper::loadFieldClass('combo');

class activity {
	public $text;
	public $value;
}

class JFormFieldActivity extends JFormFieldCombo {
	
	protected $type = 'Activity';
	
	public function getOptions() {
		
		$options = array();
		
		$defaults = array();
		$defaults[0] = new activity();
		$defaults[0]->text = 'Cycle';
		$defaults[0]->value = 'Cycle';
		$defaults[1] = new activity();
		$defaults[1]->text = 'Run';
		$defaults[1]->value = 'Run';
		$defaults[2] = new activity();
		$defaults[2]->text = 'Walk';
		$defaults[2]->value = 'Walk';
		
		
		$db = Factory::getDbo();
		$query  = $db->getQuery(true);
		
		$query->select('DISTINCT activity AS text, activity AS value')
		->from('#__xbmaps_tracks')
		->where("activity NOT IN ('','Cycle','Run','Walk')")
		->order('activity');
		
		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();
		//merege default options
		$options = array_merge($defaults,$options);
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
