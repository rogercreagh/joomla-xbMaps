<?php
/*******
 * @package xbMaps
 * @version 0.7.0.c 9th October 2021
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
	    
	    $gcat = $this->params->get('global_use_cats');
	    $mcat = $this->params->get('tracks_use_cats');
	    $this->show_cats = 0;
	    if ($gcat>0) {
	        $this->show_cats = $mcat;
	    }
	    
	    $gtags = $this->params->get('global_use_tags');
	    $mtags = $this->params->get('tracks_use_tags');
	    $this->show_tags = false;
	    if ($gtags >0) {
	        $this->show_tags = $mtags;
	    }
	    
	    $this->show_track_title = $this->params->get('show_track_title');
	    $mapborder = $this->params->get('map_border');
	    $this->borderstyle = '';
	    if ($mapborder==1) {
	        $this->borderstyle = 'border:'.$this->params->get('map_border_width').'px solid '.$this->params->get('map_border_colour').';';
	    }
	    $this->show_track_info = $this->params->get('show_track_info');
	    $this->track_info_width = $this->params->get('track_info_width');
	    $this->mainspan = 12 - $this->track_info_width;
	    $this->show_track_desc = $this->params->get('show_track_desc');
	    $this->track_desc_class = $this->params->get('track_desc_class','');
	    $this->show_stats = $this->params->get('show_stats','1');
	    $this->show_device = $this->params->get('show_device','1');
	    $this->show_track_popover = $this->params->get('show_track_popover','1');
	    
	    $this->centre_latitude = $this->params->get('centre_latitude');
	    $this->centre_longitude = $this->params->get('centre_longitude');
	    $this->default_zoom = $this->params->get('default_zoom');
	    $this->track_map_type = $this->params->get('track_map_type');

	    $this->mapstyle = 'margin:0;padding:0;width:100%;height:';
	    $this->mapstyle .= ($this->params->get('map_height')>0) ? $this->params->get('map_height').$this->params->get('height_unit').';' : '500px;';
	    
	    if (count($errors = $this->get('Errors'))) {
	        throw new Exception(implode("\n", $errors), 500);
	    }
	    
	    $this->descbox = '';
	    if ($this->show_track_desc) {
	        $this->descbox .= '<div class="'.$this->track_desc_class.'">';
	        $this->descbox .= '<p><b>Track Description</b></p>';
	        $this->descbox .= $this->item->description.'</div>';
	    }
	    
	    $this->infobox = '';
	    if ($this->show_track_info) {
	        $this->infobox .= '<div class="xbbox xbboxmag">';
	        if (($this->show_stats) && ($this->show_track_info == 'above') || ($this->show_track_info == 'below')) {
	            $this->infobox .= '<div class="row-fluid"><div class="span6">';
	        }
	        $this->infobox .= '<ul class="xbhlist">';
	        $this->infobox .= '<li><i>Recording start : </i>'.$this->item->rec_date.'</li>';
	        $this->infobox .= '<li><i>Activity type: </i>'.$this->item->activity.'</li>';
	        if ($this->show_device) {
	            $this->infobox .= '<li><i>Record device: </i>'.$this->item->rec_device.'</li>';
	        }
	        $this->infobox .= '</ul>';
	        if ($this->show_stats) {
	            if (($this->show_track_info == 'above') || ($this->show_track_info == 'below')) {
	                $this->infobox .= '</div><div class="span6">';
	            }
	            $this->infobox .= '<ul class="xbhlist">';
	            $this->infobox .= '<div id="'.str_replace('-','_',$this->item->alias).'">';
	            $this->infobox .= '</div>';
	            $this->infobox .= '</ul></div>';
	            if (($this->show_track_info == 'above') || ($this->show_track_info == 'below')) {
	                $this->infobox .= '</div></div>';
	            }
	        }
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