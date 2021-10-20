<?php
/*******
 * @package xbMaps
 * @version 0.8.0.g 20th October 2021
 * @filesource site/views/map/view.html.php
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

class XbmapsViewMap extends JViewLegacy {
	
	protected $item;
	
	public function display($tpl = null) {
		
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
//		$this->sparams = $this->state->get('params');
		$this->params = $this->item->params;
		
		$this->show_empty = $this->params->get('show_empty',1);
		
		$gcat = $this->params->get('global_use_cats',1);
		$mcat = $this->params->get('maps_use_cats',1);
		$this->show_cats = 0;
		if ($gcat>0) {
		    $this->show_cats = $mcat;
//		    if ($this->params['maps_use_cats']!=='') {
//		        $this->show_cats = $this->params['maps_use_cats'];
//		    }
		}
		
		$gtags = $this->params->get('global_use_tags',1);
		$mtags = $this->params->get('maps_use_tags',1);
		$this->show_tags = false;
		if ($gtags >0) {
		    $this->show_tags = $mtags;
//		    if ($this->params['maps_use_tags']!=='') {
//		        $this->show_tags = $this->params['maps_use_tags'];
//		    }
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
		$this->show_map_key = $this->params->get('show_map_key');
		$this->track_infodetails = $this->params->get('track_infodetails','');
		$this->marker_infocoords = $this->params->get('marker_infocoords','');
		
		$this->map_click_marker = $this->params->get('map_click_marker','0');
		$this->w3w_api =  trim($this->params->get('w3w_api',''));
		
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
		
		//the userstate films.sortorder will be updated by whatever list (films or category) was last viewed
		//if we have arrived here directly then we probably ought to load a default sort to determine prev/next
		//we also need to determine where we need to go back to (catlist of allfilmslist)
		$app = Factory::getApplication();
		$srt = $app->getUserState('maps.sortorder');
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
		
		$document = $this->document; //Factory::getDocument();
		$document->setTitle($this->item->title);
		$document->setMetaData('title', Text::_('xbMaps Map').' '.$this->item->title);
		$metadata = json_decode($this->item->metadata,true);
		if (!empty($metadata['metadesc'])) { $document->setDescription($metadata['metadesc']); }
		if (!empty($metadata['metakey'])) { $document->setMetaData('keywords', $metadata['metakey']);}
		if (!empty($metadata['rights'])) { $document->setMetaData('rights', $metadata['rights']);}
		if (!empty($metadata['robots'])) { $document->setMetaData('robots', $metadata['robots']);}
		if (!empty($metadata['author'])) { $document->setMetaData('author', $metadata['author']);}
		
		$this->infopos = 'topbot';
		if (($this->show_map_info=='left') || ($this->show_map_info=='right')) {
			$this->infopos = 'side';
		}

		$this->descbox = '';
		if ($this->show_map_desc) {
			$this->descbox .= '<div class="'.$this->map_desc_class.'">';
			if ($this->desc_title) {
				$this->descbox .= '<h4>'.$this->desc_title.'</h4>';
			}
			$this->descbox .= $this->item->description.'</div>';
		}
		
		$this->infopos = 'topbot';
		if (($this->show_map_info=='left') || ($this->show_map_info=='right')) {
			$this->infopos = 'side';
		}
		
		$this->keybox = '';
		if ($this->show_map_info) {
		    $this->keybox .= '<div class="xbbox xbboxgrn">';
		    $this->keybox .= '<h4>'.$this->item->title.'</h4>';
		    if ($this->show_info_summary) {
		    	$this->keybox .= '<p>'.$this->item->summary.'</p>';
		    }
			if ($this->show_map_key && ((!empty($this->item->tracks)) || (!empty($this->item->markers)))) {
		    	$this->keybox .= ($this->infopos == 'topbot') ? '<div class="row-fluid"><div class="span6">' : '';
			    if (count($this->item->tracks)>0) {
		        	$this->keybox .= '<p><b>Tracks</b></p>';
		        	$this->keybox .= XbmapsGeneral::buildTrackList($this->item->tracks, $this->infopos,$this->track_infodetails); 
			    } elseif ($this->show_empty) {
			    	$this->keybox .= '<p><i>No tracks assigned</i></p>';
			    }
	    		if ((count($this->item->tracks)>0) && (count($this->item->markers)>0)) {
	    			$this->keybox .= ($this->infopos == 'topbot') ? '</div><div class="span6"' : '<hr style="margin:8px 0;" />';
	    		}
	    		if (count($this->item->markers)>0) {
	    			$this->keybox .= ($this->infopos == 'topbot') ? '<div class="span6">' : '';
	    			$this->keybox .= '<p><b>Markers</b></p>';
	    			$this->keybox .= XbmapsGeneral::buildMarkerList($this->item->markers, $this->infopos, $this->marker_image_path,$this->marker_infocoords );
	    			$this->keybox .= ($this->infopos == 'topbot') ? '</div>' : '';
	    		} elseif ($this->show_empty) {
	    			$this->keybox .= '<p><i>No markers assigned</i></p>';
	    		}
	    		$this->keybox .= ($this->infopos == 'topbot') ? '</div></div>' : '';
			}
    		$this->keybox .= '</div>';
		}
				
		parent::display($tpl);
	}
		
}
