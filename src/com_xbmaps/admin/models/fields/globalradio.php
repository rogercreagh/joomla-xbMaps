<?php
/*******
 * @package xbMaps
 * @version 0.1.0.n 5th August 2021
 * @filesource admin/models/fields/globalradio.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('radio');

class JFormFieldGlobalradio extends JFormFieldRadio {
	
	protected $type = 'Globalradio';
	
	public function getOptions() {
		
		$params = ComponentHelper::getParams('com_xbmaps');
		$oparr= FormHelper::parseShowOnConditions((string) $this->element['renderon'], '', '');
		$ans = false;
		foreach ($oparr as $fld) {
			$pfldval = $params->get($fld['field']);
			$sign = $fld['sign'];
			$specvals = $fld['values'];
			$op = $fld['op'];
			$newans = self::checkOperator($pfldval,$sign,$specvals);
			$ans = empty($op) ? $newans : self::checkOperator($ans,$op,$newans);
		}
		$options = array();
		if ($ans) {
			$options = array_merge(parent::getOptions(), $options);
			$this->element['useglobal'] = true;
		} else {
			$options[]= (object) array('value' => '', 'text' => 'Disabled Globally');
		}
		return $options;
	}
	
	private function checkOperator($value1, $operator, $value2) {
		if (is_array($value2)) {
			$res = false;
			foreach ($value2 as $val) {
				switch ($operator) {
					case '=':
						$ans = $value1 == $val;
						break;
					case '!=': // Not equal
						$ans = $value1 != $val;
						break;
					default:
						$ans = false;
				} // end switch
			}
    		$res = ($res || $ans);			
		} else { //is_not_array
			switch ($operator) {
				case '=':
					$res = $value1 == $value2;
					break;
				case '!=': // Not equal
					$res = $value1 != $value2;
					break;
				case 'AND':
					$res = $value1 && $value2;
					break;
				case 'OR':
					$res = $value1 || $value2;
					break;
				default:
					$res = false;
			} // end switch			
		} // end if-else is_array
		return $res;
	}
}
