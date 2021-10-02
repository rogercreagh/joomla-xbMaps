<?php
/*******
 * @package xbMaps
 * @version 0.6.0.a 2nd October 2021
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
		
		$gcat = $this->params->get('global_use_cats');
		$mcat = $this->params->get('maps_use_cats');
		$this->show_cats = 0;
		if ($gcat>0) {
		    $this->show_cats = $mcat;
//		    if ($this->params['maps_use_cats']!=='') {
//		        $this->show_cats = $this->params['maps_use_cats'];
//		    }
		}
		
		$this->fit_bounds = $this->params['fit_bounds'];
		$gtags = $this->params->get('global_use_tags');
		$mtags = $this->params->get('maps_use_tags');
		$this->show_tags = false;
		if ($gtags >0) {
		    $this->show_tags = $mtags;
//		    if ($this->params['maps_use_tags']!=='') {
//		        $this->show_tags = $this->params['maps_use_tags'];
//		    }
		}
		
		$this->clustering = $this->params->get('marker_clustering');
		$this->homebutton = $this->params->get('map_home_button');
		//$this->centremarker = $this->params->get('centre_marker');
		$this->show_map_title = $this->params->get('show_map_title');
		$this->marker_image_path = 'images/'.$this->params->get('def_markers_folder','');
		$mapborder = $this->params->get('map_border');
		$this->borderstyle = '';
		if ($mapborder==1) {
		    $this->borderstyle = 'border:'.$this->params->get('map_border_width').'px solid '.$this->params->get('map_border_colour').';';
		}
		$this->show_map_info = $this->params->get('show_map_info');
		$this->map_info_width = $this->params->get('map_info_width');
		$this->mainspan = 12 - $this->map_info_width;
		$this->show_map_desc = $this->params->get('show_map_desc');
		$this->map_desc_class = $this->params->get('map_desc_class','');
		$this->show_map_key = $this->params->get('show_map_key');
		$this->show_trk_dist = $this->params->get('show_trk_dist');
		$this->show_trk_desc = $this->params->get('show_trk_desc');
		$this->show_mrk_desc = $this->params->get('show_mrk_desc');
		
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
		
		$tagsHelper = new TagsHelper;
		$this->item->tags = $tagsHelper->getItemTags('com_xbmaps.map' , $this->item->id);
		
		$document = $this->document; //Factory::getDocument();
		$document->setTitle($this->item->title);
		$document->setMetaData('title', Text::_('xbMaps Map').' '.$this->item->title);
		$metadata = json_decode($this->item->metadata,true);
		if (!empty($metadata['metadesc'])) { $document->setDescription($metadata['metadesc']); }
		if (!empty($metadata['metakey'])) { $document->setMetaData('keywords', $metadata['metakey']);}
		if (!empty($metadata['rights'])) { $document->setMetaData('rights', $metadata['rights']);}
		if (!empty($metadata['robots'])) { $document->setMetaData('robots', $metadata['robots']);}
		if (!empty($metadata['author'])) { $document->setMetaData('author', $metadata['author']);}
		
		$this->keybox = '';
		if (($this->show_map_key) && ((count($this->item->tracks)>0) || (count($this->item->markers)>0))) {
		    $this->keybox .= '<div class="xbbox xbboxgrn">';
		    if (count($this->item->tracks)>0) {
	           $this->keybox .= '<p>Tracks</p><ul';
	           if (($this->show_map_info=='left') || ($this->show_map_info=='right')) {
	               $this->keybox .= ' class="xbhlist"' ;
	           }
	           $this->keybox .= '>'.self::buildTrackList().'</ul>';    						
    		}
    		if ((count($this->item->tracks)>0) && (count($this->item->markers)>0)) {
    		    $this->keybox .= '<hr />';
    		}
    		if (count($this->item->markers)>0) {
    		     $this->keybox .= '<p>Markers</p><ul';
    		     if (($this->show_map_info=='left') || ($this->show_map_info=='right')) {
    		         $this->keybox .= ' class="xbhlist"' ;
    		     }
    		     $this->keybox .= '>'.self::buildMarkerList().'</ul>';
    		 }
    		$this->keybox .= '</div>';
		}
			
//		$this->tracklist = ($this->show_map_key==1) ? self::buildTrackList() : '';
//		$this->markerlist = ($this->show_map_key==1) ? self::buildMarkerList() : '';
		
		parent::display($tpl);
	}
	
	function buildTrackList() {
	    $trklist = '';
	    foreach ($this->item->tracks as $trk) {
	        $trklist .=	'<li><i class="fas fa-project-diagram" style="color:'.$trk->track_colour.'"></i>&nbsp<b>';
	        $trklist .=	$trk->linkedtitle.'</b>&nbsp;'.$trk->rec_date.'&nbsp;';
	        if (($this->show_trk_desc) && ($trk->description != '')) {
	            $trklist .=	 XbmapsGeneral::makeSummaryText($trk->description);
	        }
            $trklist .=	 '</li>';
	    } // endforeach;
	    return $trklist;
	}
	
	function buildMarkerList() {
	    $mrklist = '';
	    foreach ($this->item->markers as $mrk) {
	        $mrklist .=	'<li>';
	        $pv = '<img src="'.Juri::root().'media/com_xbmaps/images/marker-icon.png"  style="height:24px;"/>';
	        switch ($mrk->markertype) {
	            case 1:
	                $pv = '<img src="'.Juri::root().$this->marker_image_path.'/'.$mrk->mkparams['marker_image'].'" style="height:20px;" />';
	                break;
	            case 2:
	                $pv = '<span class="fa-stack fa-2x" style="font-size:8pt;">';
	                $pv .='<i class="'.$mrk->mkparams['marker_outer_icon'].' fa-stack-2x" ';
	                $pv .= 'style="color:'.$mrk->mkparams['marker_outer_colour'].';"></i>';
	                if ($mrk->mkparams['marker_inner_icon']!=''){
	                    $pv .= '<i class="'.$mrk->mkparams['marker_inner_icon'].' fa-stack-1x fa-inverse" ';
	                    $pv .= 'style="color:'.$mrk->mkparams['marker_inner_colour'].';';
	                    if ($mrk->mkparams['marker_outer_icon']=='fas fa-map-marker') {
	                        $pv .= 'line-height:1.75em;font-size:0.8em;';
	                    }
	                    $pv .= '"></i>';
	                }
	                $pv .= '</span>';
	                break;
	            default:
	                break;
	        }
	        $mrklist .=	$pv.'&nbsp;';
	        $mrklist .=	'<b>'.$mrk->linkedtitle.'</b>&nbsp;';
	        if (($this->show_mrk_desc) && ($mrk->description != '')) {
	            $mrklist .=	 XbmapsGeneral::makeSummaryText($mrk->description);
	        }
            $mrklist .=	 '</li>';
	    } // endforeach;
	    return $mrklist;
	}
	
}