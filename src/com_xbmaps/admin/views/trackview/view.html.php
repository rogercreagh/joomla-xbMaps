<?php
/*******
 * @package xbMaps
 * @version 0.1.1.j 26th August 2021
 * @filesource admin/views/trackview/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class XbmapsViewTrackview extends JViewLegacy {

	public function display($tpl = null) {
		
	    $this->item = $this->get('Item');
	    $this->params = $this->item->params;
	    $this->centre_latitude = $this->params['centre_latitude'];
	    $this->centre_longitude = $this->params['centre_longitude'];
	    $this->default_zoom = $this->params['default_zoom'];
	    $this->track_map_type = $this->params['track_map_type'];

	    $this->borderstyle = 'border:1px solid #3f3f3f;';
	    $this->mapstyle = 'margin:0;padding:0;width:100%;height:50vh;';
	    
	    if (count($errors = $this->get('Errors'))) {
	        throw new Exception(implode("\n", $errors), 500);
	    }
	    
	    $this->addToolbar();
	    
	    parent::display($tpl);
		
	    $this->setDocument();
	}
	
	protected function addToolbar() {
	    
        $title = Text::_('XBMAPS_TITLE_VIEWTRACK');

	    ToolBarHelper::title($title, '');
	    
	    ToolBarHelper::custom('trackview.edit', 'edit', '', 'Edit Track', false) ;
	    ToolBarHelper::custom('trackview.list', 'list', '', 'List Tracks', false) ;
	    
	    ToolbarHelper::help( '', false,'https://crosborne.uk/xbmaps/doc?tmpl=component#trackedit' );
	    
	}
	
	protected function setDocument() {
	    $document = Factory::getDocument();
	    $document->setTitle(strip_tags(Text::_('XBMAPS_TITLE_VIEWTRACK')));
	}
	
}