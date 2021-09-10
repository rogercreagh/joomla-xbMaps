<?php
/*******
 * @package xbMaps
 * @version 0.1.1.i 23rd August 2021
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
		$iparams = $this->item->params;
		$this->params = $iparams;
		//$sparams is used to get menu options that are not global and may override item params
		$sparams = $this->state->get('params');
		
		$gcat = $this->params->get('global_use_cats');
		$mcat = $this->params->get('tracks_use_cats');
		$this->show_cats = 0;
		if ($gcat>0) {
		    $this->show_cats = $mcat;
		    if ($this->params['tracks_use_cats']!=='') {
		        $this->show_cats = $this->params['tracks_use_cats'];
		    }
		}
		
		$gtags = $this->params->get('global_use_tags');
		$mtags = $this->params->get('tracks_use_tags');
		$this->show_tags = false;
		if ($gtags >0) {
		    $this->show_tags = $mtags;
		    if ($this->params['tracks_use_tags']!=='') {
		        $this->show_tags = $this->params['tracks_use_tags'];
		    }
		}
		//aren't these the wrong way round - iparams should take priority, and aren't they merged anyway
		$this->showtracktitle = $sparams['show_track_title']=='' ? $iparams['show_track_title'] : $sparams['show_track_title'];
		$this->showtrackdesc = $sparams['show_track_desc']=='' ? $iparams['show_track_desc'] : $sparams['show_track_desc'];
		$this->trackdescpos = $sparams['track_desc_position']=='' ? $iparams['track_desc_position'] : $sparams['track_desc_position'];
		$this->centre_latitude = $this->params->get('centre_latitude');
		$this->centre_longitude = $this->params->get('centre_longitude');
		$this->default_zoom = $this->params->get('default_zoom');
		$this->track_map_type = $sparams->get('track_map_type');
		$this->borderstyle = 'border:1px solid #3f3f3f;';
		$this->mapstyle = 'margin:0;padding:0;width:100%;height:50vh;';
		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
			return false;
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