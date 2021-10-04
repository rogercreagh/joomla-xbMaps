<?php
/*******
 * @package xbMaps
 * @version 0.6.0.d 4th October 2021
 * @filesource site/views/track/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Registry\Registry;

class XbmapsViewTrack extends JViewLegacy {
	
	protected $item;
	
	public function display($tpl = null) {
		
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
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
		//TODO set default for all params
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
		
		$this->centre_latitude = $this->params->get('centre_latitude');
		$this->centre_longitude = $this->params->get('centre_longitude');
		$this->default_zoom = $this->params->get('default_zoom','12');
		$this->track_map_type = $this->params->get('track_map_type');
		
		$this->mapstyle = 'margin:0;padding:0;width:100%;height:';
		$this->mapstyle .= ($this->params->get('map_height')>0) ? $this->params->get('map_height').$this->params->get('height_unit').';' : '500px;';
		
		
		$this->header = array();
		$this->header['showheading'] = $this->params->get('show_page_heading',0,'int');
		$this->header['heading'] = $this->params->get('page_heading','','text');
		if ($this->header['heading'] =='') {
			$this->header['heading'] = $this->params->get('page_title','','text');
		}
		$this->header['title'] = $this->params->get('mappage_title','','text');
		$this->header['subtitle'] = $this->params->get('mappage_subtitle','','text');
		$this->header['text'] = $this->params->get('mappage_headtext','','text');
		
		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
			return false;
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
		
		//the userstate films.sortorder will be updated by whatever list (films or category) was last viewed
		//if we have arrived here directly then we probably ought to load a default sort to determine prev/next
		//we also need to determine where we need to go back to (catlist of allfilmslist)
		$app = Factory::getApplication();
		$srt = $app->getUserState('tracks.sortorder');
		if (!empty($srt)) {
			$i = array_search($this->item->id, $srt);
			if ($i<count($srt)-1) {
				$this->item->next = $srt[$i+1];
			} else { $this->item->next = 0; }
			if ($i>0) {
				$this->item->prev = $srt[$i-1];
			} else { $this->item->prev = 0; }
			
		} else {
			$this->item->prev = 0;
			$this->item->next = 0;
		}
		//TODO now test pagination for next page
		
		$tagsHelper = new TagsHelper;
		$this->item->tags = $tagsHelper->getItemTags('com_xbmaps.track' , $this->item->id);
		
		$document = $this->document; //Factory::getDocument();
		$document->setTitle($this->item->title);
		
		parent::display($tpl);
	}
	
}