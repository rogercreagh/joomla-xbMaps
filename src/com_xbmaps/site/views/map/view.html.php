<?php
/*******
 * @package xbMaps
 * @version 0.1.2.d 10th September 2021
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
		$iparams = $this->item->params;
		$this->params = $iparams;
		//$sparams is used to get menu options that are not global and may override item params
		$sparams = $this->state->get('params');
		
		$gcat = $this->params->get('global_use_cats');
		$mcat = $this->params->get('maps_use_cats');
		$this->show_cats = 0;
		if ($gcat>0) {
		    $this->show_cats = $mcat;
		    if ($this->params['maps_use_cats']!=='') {
		        $this->show_cats = $this->params['maps_use_cats'];
		    }
		}
		
		$gtags = $this->params->get('global_use_tags');
		$mtags = $this->params->get('maps_use_tags');
		$this->show_tags = false;
		if ($gtags >0) {
		    $this->show_tags = $mtags;
		    if ($this->params['maps_use_tags']!=='') {
		        $this->show_tags = $this->params['maps_use_tags'];
		    }
		}
		
		$this->clustering = $this->params['marker_clustering'];
		$this->homebutton = $this->params['map_home_button'];
		$this->centremarker = $sparams['centre_marker']=='' ? $iparams['centre_marker'] : $sparams['centre_marker'];
		$this->showmaptitle = $sparams['show_map_title']=='' ? $iparams['show_map_title'] : $sparams['show_map_title'];
		$this->showmapdesc = $sparams['show_map_desc']=='' ? $iparams['show_map_desc'] : $sparams['show_map_desc'];
		$this->mapdescpos = $sparams['map_desc_position']=='' ? $iparams['map_desc_position'] : $sparams['map_desc_position'];
		$this->marker_image_path = 'images/'.$this->params->get('def_markers_folder','');
		$mapborder = $sparams['map_border']=='' ? $iparams['map_border'] : $sparams['map_border'];
		$this->borderstyle = '';
		if ($mapborder==1) {
		    $this->borderstyle = 'border:'.$this->params->get('map_border_width').'px solid '.$this->params->get('map_border_colour').';';
		}
		
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
		
		parent::display($tpl);
	}
	
}