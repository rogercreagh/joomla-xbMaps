<?php
/*******
 * @package xbMaps Component
 * @version 1.5.1.0 3rd January 2024
 * @filesource site/views/tracklist/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;

class XbmapsViewTracklist extends JViewLegacy {
	
	protected $item;
	
	public function display($tpl = null) {
		
		$this->items 		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->params      = $this->state->get('params');
		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');
		$this->searchTitle = $this->state->get('filter.search');
		
		$cparams = ComponentHelper::getParams('com_xbmaps');
		
		$gcat = $cparams->get('global_use_cats');
		$mcat = $cparams->get('tracks_use_cats');
		$this->show_cats = 0;
		if ($gcat>0) {
		    $this->show_cats = $mcat;
		    if ($this->params['tracks_use_cats']!=='') {
		        $this->show_cats = $this->params['tracks_use_cats'];
		    }
		}
				
		$gtags = $cparams->get('global_use_tags');
		$mtags = $cparams->get('tracks_use_tags');
		$this->show_tags = false;
		if ($gtags >0) {
			$this->show_tags = $mtags;
			if ($this->params['tracks_use_tags']!=='') {
			    $this->show_tags = $this->params['tracks_use_tags'];
			}
		}
		
		$this->enable_track_view = $this->params['enable_track_view']; //$cparams->get('enable_track_view'); //
		$this->marker_image_path = '/images/'.$this->params->get('def_markers_folder','');
		
		$this->header = array();
		$this->header['showheading'] = $this->params->get('show_page_heading',0,'int');
		$this->header['heading'] = $this->params->get('page_heading','','text');
		if ($this->header['heading'] =='') {
			$this->header['heading'] = $this->params->get('page_title','','text');
		}
		$this->header['title'] = $this->params->get('list_title','','text');
		$this->header['subtitle'] = $this->params->get('list_subtitle','','text');
		$this->header['text'] = $this->params->get('list_headtext','','text');
		
		$this->search_bar = $this->params->get('search_bar','1','int');
		$this->hide_catsch = $this->params->get('menu_category_id',0)>0 ? true : false;
		$this->hide_tagsch = (!empty($this->params->get('menu_tag',''))) ? true : false;
		
		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
			return false;
		}
		//set metadata
		$document=$this->document;
		$document->setMetaData('title', Text::_('XBMAPS_MAPS_LISTING').': '.$document->title);
		$metadesc = $this->params->get('menu-meta_description');
		if (!empty($metadesc)) { $document->setDescription($metadesc); }
		$metakey = $this->params->get('menu-meta_keywords');
		if (!empty($metakey)) { $document->setMetaData('keywords', $metakey);}
		$metarobots = $this->params->get('robots');
		if (!empty($metarobots)) { $document->setMetaData('robots', $metarobots);}
		$document->setMetaData('generator', $this->params->get('def_generator'));
		$metaauthor = $this->params->get('def_author');
		if (!empty($metaauthor)) { $document->setMetaData('author', $metadata['author']);}
		
		parent::display($tpl);
	}
	
}