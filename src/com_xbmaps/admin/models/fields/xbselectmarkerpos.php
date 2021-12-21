<?php
/*******
 * @package xbMaps Component
 * @version 0.1.2.a 30th August 2021
 * @filesource admin/models/fields/xbselectmarkerpos.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
use Joomla\CMS\Factory;

class JFormFieldXbSelectMarkerPos extends JFormField
{
	public $type = 'XbSelectMarkerPos';

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		
		// Initialize some field attributes.
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		
		// Initialize JavaScript field attributes.
		$onchange = (string) $this->element['onchange'];
		$onchangeOutput = ' onChange="'.(string) $this->element['onchange'].'"';

		// Build the script.
		$script = array();
		$script[] = '	function xbSelectMapArea_'.$this->id.'(title) {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = title;';
		$script[] = '		'.$onchange;
		$script[] = '	}';
		
		
		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));
		
		if ($this->id == 'jform_latitude') {
			
			$html[] = '<div class="input-append">';
			$html[] = '<span class="input-append"><input type="number" id="' . $this->id . '_id" name="' . $this->name . '"'
					. ' value="' . $this->value . '" '.$class.$disabled.$readonly.$onchangeOutput.'  max="90.000000" min="-90.000000" step="0.000001" style="width:100px;" />';
			$html[] = '</span></div>'. "\n";
									
		} elseif ($this->id == 'jform_longitude') {
			$html[] = '<div>';
			$html[] = '	<input type="number" id="'.$this->id.'_id" name="'.$this->name.'" value="'. $this->value.'"' .
					' '.$class.$disabled.$readonly.$onchangeOutput.' max="180.000000" min="-179.999999" step="0.000001" style="width:100px;" />';
			$html[] = '</div>'. "\n";
		} else {
			$html[] = '<div>';
			$html[] = '	<input type="text" id="'.$this->id.'_id" name="'.$this->name.'" value="'. $this->value.'"' .
					' '.$class.$disabled.$readonly.$onchangeOutput.' />';
			$html[] = '</div>'. "\n";
			
		}
		
		return implode("\n", $html);
	}
}