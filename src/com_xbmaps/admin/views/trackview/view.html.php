<?php
/*******
 * @package xbMaps
 * @version 0.8.0. 30th October 2021
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
	    $this->show_info_summary = $this->params->get('show_info_summary',1);
	    $this->show_track_desc = $this->params->get('show_track_desc');
	    $this->track_desc_class = $this->params->get('track_desc_class','');
	    $this->desc_title = $this->params->get('desc_title','');
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
	    if (($this->show_track_desc)  && ($this->item->description !='')) {
	        $this->descbox .= '<div class="'.$this->track_desc_class.'">';
	        if ($this->desc_title) {
	        	$this->descbox .= '<h4>'.$this->desc_title.'</h4>';
	        }
	        $this->descbox .= $this->item->description.'</div>';
	    }
	    
	    $this->infopos = 'topbot';
	    if (($this->show_track_info=='left') || ($this->show_track_info=='right')) {
	    	$this->infopos = 'side';
	    }
	    
	    $this->infobox = '';
	    if ($this->show_track_info) {
	    	$this->infobox .= '<div class="xbbox xbboxmag">';
	    	$this->infobox .= '<h4>'.$this->item->title.'</h4>';
	    	if ($this->show_info_summary) {
	    		$this->infobox .= $this->item->summary;
	    	}
	    	if ($this->infopos == 'topbot') {
	    		$this->infobox .= '<div class="row-fluid"><div class="span4">';
	    	}
	    	$this->infobox .= '<p><b>Track Info.</b></p>';
	    	$this->infobox .= '<dl class="xbdl">';
	    	if ( ($this->item->rec_date!='')) {
	    		$this->infobox .= '<dt>Recording start : </dt><dd>'.$this->item->rec_date.'</dd>';
	    	}
	    	if (($this->item->activity!='')) {
	    		$this->infobox .= '<dt>Activity type: </dt><dd>'.$this->item->activity.'</dd>';
	    	}
	    	if (($this->item->rec_device!='')) {
	    		$this->infobox .= '<dt>Record device: </dt><dd>'.$this->item->rec_device.'</dd>';
	    	}
	    	$this->infobox .= '</dl>';
	    	if ($this->show_stats) {
	    		if ($this->infopos == 'topbot') {
	    			$this->infobox .= '</div><div class="span4">';
	    		}
	    		$this->infobox .= '<p><b>Track Stats.</b></p>';
	    		$this->infobox .= '<ul class="xblist">';
	    		$this->infobox .= '<div id="'.str_replace('-','_',$this->item->alias).'">';
	    		$this->infobox .= '</div>';
	    		$this->infobox .= '</ul>';
	    	}
	    	if ((!empty($this->item->maps))) {
	    		if ($this->infopos == 'topbot') {
	    			$this->infobox .= '</div><div class="span4">';
	    		}
	    		if (!empty($this->item->maps)) {
	    			$this->infobox .= '<p><b>Used on Maps</b></p><ul class="xblist">';
	    			foreach ($this->item->maps as $map) {
	    				$this->infobox .= '<li>'.$map->linkedtitle.'</li>';
	    			}
	    			$this->infobox .= '</ul>';
	    		} elseif ($this->show_empty) {
	    			$this->infobox .= '<p><i>Not assigned to any map</i></p>';
	    		}
	    	}
	    	if ($this->infopos == 'topbot') {
	    		$this->infobox .= '</div></div>';
	    	}
	    	$this->infobox .= '</div>';
	    }
	    
	    $this->addToolbar();
	    
	    XbmapsHelper::addSubmenu('tracks');
	    $this->sidebar = JHtmlSidebar::render();
	    
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