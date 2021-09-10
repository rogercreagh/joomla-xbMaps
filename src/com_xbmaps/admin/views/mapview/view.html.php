<?php
/*******
 * @package xbMaps
 * @version 0.1.2.d 10th September 2021
 * @filesource admin/views/mapview/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;


class XbmapsViewMapview extends JViewLegacy {

	public function display($tpl = null) {
		
		$this->item = $this->get('Item');
		$this->params = $this->item->params;
		
		$this->clustering = $this->params['marker_clustering'];
		$this->homebutton = $this->params['map_home_button'];
		$this->centremarker = $this->params['centre_marker'];
		$this->marker_image_path = 'images/'.$this->params->get('def_markers_folder','');
		$this->borderstyle = '';
		if ($this->params['map_border']==1) {
		    $this->borderstyle = 'border:'.$this->params['map_border_width'].'px solid '.$this->params['map_border_colour'].';';
		}		
		
		if (count($errors = $this->get('Errors'))) {
		    throw new Exception(implode("\n", $errors), 500);
		}
		
		$this->addToolbar();
		
		XbmapsHelper::addSubmenu('maps');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
		
		$this->setDocument();
		
	}

	protected function addToolbar() {
	    
	    $title = Text::_('XBMAPS_TITLE_VIEWMAP');
	    
	    ToolBarHelper::title($title, '');
	    
	    ToolBarHelper::custom('mapview.edit', 'edit', '', 'Edit Map', false) ;
	    ToolBarHelper::custom('mapview.list', 'list', '', 'List Maps', false) ;
	    
	    ToolbarHelper::help( '', false,'https://crosborne.uk/xbmaps/doc?tmpl=component#mapedit' );
	    
	}
	
	protected function setDocument() {
	    $document = Factory::getDocument();
	    $document->setTitle(strip_tags(Text::_('XBMAPS_TITLE_VIEWMAP')));
	}
	
}