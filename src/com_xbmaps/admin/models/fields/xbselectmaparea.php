<?php
/*******
 * @package xbMaps
 * @version 0.1.0.0 7th August 2021
 * @filesource admin/models/fields/xbselectmaparea.php
 * @author Jan Pavelka www.phoca.cz, Roger C-O
 * @copyright Copyright (c) Jan Pavelka, 2019
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class JFormFieldXbSelectMapArea extends JFormField
{
	public $type = 'XbSelectMapArea';

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		
		// Initialize some field attributes.
//		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
//		$maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		
		$maptype	= ( (string)$this->element['maptype'] ? $this->element['maptype'] : '' );
		
		if ($this->id == 'jform_centre_latitude') {
			// One link for latitude, longitude, zoom
			$lat	= $this->form->getValue('centre_latitude');
			$lng	= $this->form->getValue('centre_longitude');
			$zoom	= $this->form->getValue('default_zoom');
			$suffix	= '';
			if ($lat != '') { $suffix .= '&amp;lat='.$lat;}
			if ($lng != '') { $suffix .= '&amp;lng='.$lng;}
			if ($zoom != '' && (int)$zoom > 0) { $suffix .= '&amp;zoom='.$zoom;}
			if ($maptype != '') { $suffix .= '&amp;type='.$maptype;}
			
			$link = 'index.php?option=com_xbmaps&amp;view=mapselector&amp;tmpl=component&amp;field='.$this->id. $suffix;
		
			// Load the modal behavior script.
			JHtml::_('behavior.modal', 'a.modal_'.$this->id);
		
		}
		
		// Initialize JavaScript field attributes.
		$onchange = (string) $this->element['onchange'];
		$onchangeOutput = ' onChange="'.(string) $this->element['onchange'].'"';

		$idA	= 'pgselectmap';
		
		// Build the script.
		$script = array();
		$script[] = '	function xbSelectMapArea_'.$this->id.'(title) {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = title;';
		$script[] = '		'.$onchange;
		$script[] = '	}';
		
		
		// Hide Info box on start
		if ($this->id == 'jform_centre_latitude') {
			$script[] = ' jQuery(document).ready(function() {';
			$script[] = '    jQuery(\'#'.$idA.'\').on(\'shown.bs.modal\', function () {';
			$script[] = '	    jQuery(\'#phmPopupInfo\').html(\'\');';
			$script[] = '	  })';
			$script[] = ' })';
		}
		

		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));
		
		if ($this->id == 'jform_centre_latitude') {
			
			$html[] = '<div class="input-append">';
			$html[] = '<span class="input-append"><input type="number" id="' . $this->id . '_id" name="' . $this->name . '"'
					. ' value="' . $this->value . '" '.$class.$disabled.$readonly.$onchangeOutput.'  max="90.000000" min="-90.000000" step="0.000001" style="width:100px;" />';
			$html[] = '<a href="#'.$idA.'" role="button" class="btn " data-toggle="modal" title="' . Text::_('Set Coordinates') . '">'
							. '<span class="icon-list icon-white"></span> '
									. Text::_('Set Coordinates') . '</a></span>';
			$html[] = '</div>'. "\n";
									
			$html[] = JHtml::_('bootstrap.renderModal',
											$idA,
											array(
													'url'    => $link,
													'title'  => Text::_('Click location to set centre and zoom'),
													'width'  => '780px',
													'height' => '580px',
													'modalWidth' => '50',
													'bodyHeight' => '70',
													'footer' => '<div id="coordInfo" class="pull-left"></div>
								<button type="button" class="btn btn-success" data-dismiss="modal" aria-hidden="true" >'.Text::_('JSAVE').'</button>'
											)
											);
			//								<button type="button" class="btn" data-dismiss="modal" >'.Text::_('JCANCEL').'</button>
			
		} elseif ($this->id == 'jform_default_zoom') {
			$html[] = '<div>';
			$html[] = '	<input type="number" id="'.$this->id.'_id" name="'.$this->name.'" value="'. $this->value.'"' .
					' '.$class.$disabled.$readonly.$onchangeOutput.' max="20" min="1" step="1" style="width:100px;" />';
			$html[] = '</div>'. "\n";
			
		} elseif ($this->id == 'jform_centre_longitude') {
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