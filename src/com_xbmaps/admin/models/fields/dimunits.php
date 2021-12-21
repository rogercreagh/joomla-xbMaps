<?php
/*******
 * @package xbMaps Component
 * @version 0.1.0.m 24th July 2021
 * @filesource admin/models/fields/dimunits.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldDimunits extends JFormFieldList {
	
	protected $type = 'Dimunits';
	public function getOptions() {
		
		$options = array();
		$options[] = (object) array('value' => 'px', 'text' => 'px - Pixels');
		$options[] = (object) array('value' => '%', 'text' => '% - Percent');
		$options[] = (object) array('value' => 'vw', 'text' => 'vw - Viewport width');
		$options[] = (object) array('value' => 'vh', 'text' => 'vh - Viewport height');
		$options[] = (object) array('value' => 'vmax', 'text' => 'vmax - Viewport max');
		$options[] = (object) array('value' => 'vmin', 'text' => 'vmin - Viewport min');
		$options[] = (object) array('value' => 'in', 'text' => 'in - Inches');
		$options[] = (object) array('value' => 'cm', 'text' => 'cm - Centimeters');
		$options = array_merge(parent::getOptions(), $options);
		return $options;
		
	}
}