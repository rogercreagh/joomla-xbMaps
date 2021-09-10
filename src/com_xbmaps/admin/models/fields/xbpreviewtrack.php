<?php
/*******
 * @package xbMaps
 * @version 0.1.1.j 26th August 2021
 * @filesource admin/models/fields/xbpreviewtrack.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

class JFormFieldXbPreviewTrack extends JFormField
{
	public $type = 'XbPreviewTrack';

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		
		
//		$maptype	= ( (string)$this->element['maptype'] ? $this->element['maptype'] : 'track_map_type' );
		$maptype	= (string)$this->track_map_type;
		
			
			$link = 'index.php?option=com_xbmaps&amp;view=trackview&amp;tmpl=component&amp;field='.$this->id;
		
			// Load the modal behavior script.
			HtmlHelper::_('behavior.modal', 'a.modal_'.$this->id);
		
		
		// Initialize JavaScript field attributes.
		$onchange = (string) $this->element['onchange'];
		$onchangeOutput = ' onChange="'.(string) $this->element['onchange'].'"';

		$idA	= 'xbpreviewtrack';
		
		// Build the script.
		$script = array();
		$script[] = '	function xbPreviewTrack_'.$this->id.'(title) {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = title;';
		$script[] = '		'.$onchange;
		$script[] = '	}';
		
		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));
		
		if ($this->id == 'jform_centre_latitude') {
			
			$html[] = '<div class="input-append">';
//			$html[] = '<span class="input-append"><input type="number" id="' . $this->id . '_id" name="' . $this->name . '"'
//					. ' value="' . $this->value . '" '.$class.$disabled.$readonly.$onchangeOutput.'  max="90.000000" min="-90.000000" step="0.000001" style="width:100px;" />';
			$html[] = '<a href="#'.$idA.'" role="button" class="btn " data-toggle="modal" title="' . Text::_('Preview Track') . '">'
							. '<span class="icon-list icon-white"></span> '
									. JText::_('Preview Track') . '</a></span>';
			$html[] = '</div>'. "\n";
									
			$html[] = HtmlHelper::_('bootstrap.renderModal',
							$idA,
							array(
									'url'    => $link,
									'title'  => Text::_('new title'),
									'width'  => '780px',
									'height' => '580px',
									'modalWidth' => '50',
									'bodyHeight' => '70',
									'footer' => '<button type="button" class="btn btn-success" data-dismiss="modal" aria-hidden="true" >'.Text::_('JCLOSE').'</button>'
							)
							);
			//								<button type="button" class="btn" data-dismiss="modal" >'.Text::_('JCANCEL').'</button>
			
		}
		
		return implode("\n", $html);
	}
}