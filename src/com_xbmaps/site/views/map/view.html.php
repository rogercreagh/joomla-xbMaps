<?php
/*******
 * @package xbMaps Component
 * @version 1.1.0.c 24th December 2021
 * @filesource site/views/map/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class XbmapsViewMap extends JViewLegacy {
	
	protected $item;
	
	public function display($tpl = null) {
		
		$input = Factory::getApplication()->input;

		$this->tmplcomp = ($input->getString('tmpl', '')=='component') ? true : false; 
		
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->params = $this->item->params;
		
		$this->show_empty = $this->params->get('show_empty',1);
		
		$gcat = $this->params->get('global_use_cats',1);
		$mcat = $this->params->get('maps_use_cats',1);
		$this->show_cats = 0;
		if ($gcat>0) {
		    $this->show_cats = $mcat;
		}
		
		$gtags = $this->params->get('global_use_tags',1);
		$mtags = $this->params->get('maps_use_tags',1);
		$this->show_tags = false;
		if ($gtags >0) {
		    $this->show_tags = $mtags;
		}
		
		$this->fit_bounds = $this->params->get('fit_bounds');
		$this->clustering = $this->params->get('marker_clustering');
		$this->homebutton = $this->params->get('map_home_button');
		$this->show_scale = $this->params->get('map_show_scale');
		
		if ($input->exists('title')) {
			$this->show_map_title = $input->getInt('title',0);
		} else {
			$this->show_map_title = $this->params->get('show_map_title',0);
		}
		
		$this->marker_image_path = 'images/'.$this->params->get('def_markers_folder','');

		// the map itself is always 100% width of container div, 
		// height is set by parameter but may be overidden by input string from plugin
		$this->mapstyle = 'margin:0;padding:0;width:100%;height:';		
		$ht = 0;
		$htstr = '';
		if ($input->exists('ht')) {
			$ht = $input->getInt('ht',0);
		}
		if ($ht>0) {
			$htstr .= $ht.'px';
		} else {
			$ht = $this->params->get('map_height',0);
			if ($ht>0) {
				$htstr = $ht.$this->params->get('height_unit');
			} else {
				$htstr = '500px'; //TODO replace this with a default component parameter
			}
		}		
		$this->mapstyle .= $htstr.';';
		
		$mapborder = $this->params->get('map_border');
		$this->borderstyle = '';
		if ($mapborder==1) {
		    $this->borderstyle = 'border:'.$this->params->get('map_border_width').'px solid '.$this->params->get('map_border_colour').';';
		}
		
		if ($input->exists('info')) {
			$info = strtolower($input->getString('info',''));
			$validvalues = array('above','right','left','below');
			if (in_array($info, $validvalues)) {
				$this->show_map_info = $info;
			} else {
				$this->show_map_info = 0;
			}
		} else {
			$this->show_map_info = $this->params->get('show_map_info',0);
		}
		$this->show_map_key = $this->params->get('show_map_key',0);

		$this->mainspan = 12;
		$this->map_info_width = $this->params->get('map_info_width');
		if (($this->show_map_info === 'right') || ($this->show_map_info === 'left')) {
			$this->mainspan = 12 - $this->map_info_width;
		} 
		$this->show_info_summary = $this->params->get('show_info_summary',1);

		if ($input->exists('desc')) {
			$this->show_map_desc = $input->getInt('desc',0);
		} else {
			$this->show_map_desc = $this->params->get('show_map_desc',0);
		}
		
		$this->map_desc_class = $this->params->get('map_desc_class','');
		$this->desc_title = $this->params->get('desc_title','');
		$this->track_infodetails = $this->params->get('track_infodetails','');
		$this->marker_infocoords = $this->params->get('marker_infocoords','');
		
		$this->map_click_marker = $this->params->get('map_click_marker','0');
		$this->w3w_api =  trim($this->params->get('w3w_api',''));
		$this->w3w_lang = $this->params->get('w3w_lang');
		
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
		$document->setMetaData('title', Text::_('XBMAPS_MAPS_MAP').' '.$this->item->title);
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
		if (($this->show_map_desc)  && ($this->item->description !='')) {
		    if ($this->infopos == 'topbot') $this->descbox .= '<div class="row-fluid"><div class="span12">';
		    $this->descbox .= '<div class="'.$this->map_desc_class.'">';
		    if ($this->desc_title) {
		        $this->descbox .= '<h4>'.$this->desc_title.'</h4>';
		    }
		    $this->descbox .= $this->item->description.'</div>';
		    if ($this->infopos == 'topbot') $this->descbox .= '</div></div>';
		}
		
		$this->infopos = 'topbot';
		if (($this->show_map_info=='left') || ($this->show_map_info=='right')) {
			$this->infopos = 'side';
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
		                $this->keybox .= '<p><b>Tracks</b></p>';
		                $this->keybox .= XbmapsGeneral::buildTrackList($this->item->tracks, $this->infopos,$this->track_infodetails);
		                $this->keybox .= ($this->infopos == 'topbot')? '</div>' : '';
		            }
		            if (($this->infopos != 'topbot') && ((!empty($this->item->tracks)) && (!empty($this->item->markers)))) {
		                $this->keybox .=  '<hr style="margin:8px 0;" />';
		            }
		            if (!empty($this->item->markers)) {
		                $this->keybox .= ($this->infopos == 'topbot')? '<div class="span6">' : '';
		                $this->keybox .= '<p><b>Markers</b></p>';
		                $this->keybox .= XbmapsGeneral::buildMarkerList($this->item->markers, $this->infopos, $this->marker_image_path,$this->marker_infocoords);
		                $this->keybox .= ($this->infopos == 'topbot')? '</div>' : '';
		            }
		            $this->keybox .= ($this->infopos == 'topbot')? '</div>' : '';
		        }
		    }
		    $this->keybox .= '</div>';
		    if ($this->infopos == 'topbot') $this->keybox .= '</div></div>';
		}
		
		parent::display($tpl);
	}
		
}
