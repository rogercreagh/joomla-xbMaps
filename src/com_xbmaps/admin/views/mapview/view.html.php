<?php
/*******
 * @package xbMaps
 * @version 0.8.0.f 20th October 2021
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
		
		$gcat = $this->params->get('global_use_cats');
		$mcat = $this->params->get('maps_use_cats');
		$this->show_cats = 0;
		if ($gcat>0) {
		    $this->show_cats = $mcat;
		}
		
		$gtags = $this->params->get('global_use_tags');
		$mtags = $this->params->get('maps_use_tags');
		$this->show_tags = false;
		if ($gtags >0) {
		    $this->show_tags = $mtags;
		}
		
		$this->fit_bounds = $this->params->get('fit_bounds');
		$this->clustering = $this->params->get('marker_clustering');
		$this->homebutton = $this->params->get('map_home_button');
		$this->show_scale = $this->params->get('show_scale');
		
		$this->show_map_title = $this->params->get('show_map_title');
		$this->marker_image_path = 'images/'.$this->params->get('def_markers_folder','');

		$this->mapstyle = 'margin:0;padding:0;width:100%;height:';
		$this->mapstyle .= ($this->params->get('map_height')>0) ? $this->params->get('map_height').$this->params->get('height_unit').';' : '500px;';
		
		$mapborder = $this->params->get('map_border');
		$this->borderstyle = '';
		if ($mapborder==1) {
			$this->borderstyle = 'border:'.$this->params->get('map_border_width').'px solid '.$this->params->get('map_border_colour').';';
		}
		$this->show_map_info = $this->params->get('show_map_info');
		$this->map_info_width = $this->params->get('map_info_width');
		$this->mainspan = 12 - $this->map_info_width;
		$this->show_info_summary = $this->params->get('show_info_summary',1);
		$this->show_map_desc = $this->params->get('show_map_desc');
		$this->map_desc_class = $this->params->get('map_desc_class','');
		$this->desc_title = $this->params->get('desc_title','');
		$this->show_map_key = $this->params->get('show_map_key','');
		$this->track_infodetails = $this->params->get('track_infodetails','');
		$this->marker_infocoords = $this->params->get('marker_infocoords','');
		
		$this->map_click_marker = $this->params->get('map_click_marker','0');
		$this->w3w_api =  trim($this->params->get('w3w_api',''));
		
		if (count($errors = $this->get('Errors'))) {
		    throw new Exception(implode("\n", $errors), 500);
		}
		
		$this->infopos = 'topbot';
		if (($this->show_map_info=='left') || ($this->show_map_info=='right')) {
			$this->infopos = 'side';
		}
		
		$this->descbox = '';
		if ($this->show_map_desc) {
			if ($this->infopos == 'topbot') $this->descbox .= '<div class="row-fluid"><div class="span12">';
				$this->descbox .= '<div class="'.$this->map_desc_class.'">';
				if ($this->desc_title) {
					$this->descbox .= '<h4>'.$this->desc_title.'</h4>';		
				}
				$this->descbox .= $this->item->description.'</div>';
			if ($this->infopos == 'topbot') $this->descbox .= '</div></div>';
		}
		
		$this->keybox = '';
		if ($this->show_map_info) {
			if ($this->infopos == 'topbot') $this->keybox .= '<div class="row-fluid"><div class="span12">';
			$this->keybox .= '<div class="xbbox xbboxgrn">';
			$this->keybox .= '<h4>'.$this->item->title.'</h4>';
			if ($this->show_info_summary) {
				$this->keybox .= '<p>'.$this->item->summary.'</p>';
			}
			if ($this->show_map_key) {
				if ((!empty($this->item->tracks)) || (!empty($this->item->markers))) {
					$this->keybox .= ($this->infopos == 'topbot')? '<div class="row-fluid">' : '';
					if (!empty($this->item->tracks)) {
						$this->keybox .= ($this->infopos == 'topbot')? '<div class="span6">' : '';
						$this->keybox .= '<p><b>Tracks</b></p><ul class="xblist" style="margin:0;">';
						$this->keybox .= XbmapsGeneral::buildTrackList($this->item->tracks, $this->infopos).'</ul>';
						$this->keybox .= ($this->infopos == 'topbot')? '</div>' : '';
					}
					if (($this->infopos != 'topbot') && ((!empty($this->item->tracks)) && (!empty($this->item->markers)))) {
						$this->keybox .=  '<hr style="margin:8px 0;" />';
					}
					if (!empty($this->item->markers)) {
						$this->keybox .= ($this->infopos == 'topbot')? '<div class="span6">' : '';
						$this->keybox .= '<p><b>Markers</b></p><ul class="xblist" style="margin:0;">';
						$this->keybox .= XbmapsGeneral::buildMarkerList($this->item->markers, $this->infopos, $this->marker_image_path).'</ul>';
						$this->keybox .= ($this->infopos == 'topbot')? '</div>' : '';
					}
					$this->keybox .= ($this->infopos == 'topbot')? '</div>' : '';
				}
			}
			$this->keybox .= '</div>';
			if ($this->infopos == 'topbot') $this->keybox .= '</div></div>';
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