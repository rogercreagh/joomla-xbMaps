<?php 
/*******
 * @package xbMaps
 * @version 0.3.0.h 22nd September 2021
 * @filesource site/views/category/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbmapsViewCategory extends JViewLegacy {
	
	public function display($tpl = null) {
		
		$this->item = $this->get('Item');
		
		$this->state = $this->get('State');
		$this->params = $this->state->get('params');
		//if cats disabled on front-end redirect to maps view (we shouldn't even be here)
		if ($this->params->get('global_use_cats')==0) {
			$app = Factory::getApplication();
			$app->redirect('index.php?option=com_xbmaps&view=maplist');
			$app->close();
		}
		$this->mapcats = $this->params->get('maps_use_cats');
		$this->mrkcats = $this->params->get('markers_use_cats');
		$this->trkcats = $this->params->get('tracks_use_cats');
		
		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
			return false;
		}
		
		$this->hide_empty = $this->params->get('hide_empty','','int');
		
		$document = $this->document; //Factory::getDocument();
		$document->setTitle('Category view: '.$this->item->title);
		$document->setMetaData('title', JText::_('Category details').' '.$this->item->title);
		$metadata = json_decode($this->item->metadata,true);
		if (!empty($metadata['metadesc'])) { $document->setDescription($metadata['metadesc']); }
		if (!empty($metadata['metakey'])) { $document->setMetaData('keywords', $metadata['metakey']);}
		if (!empty($metadata['rights'])) { $document->setMetaData('rights', $metadata['rights']);}
		if (!empty($metadata['robots'])) { $document->setMetaData('robots', $metadata['robots']);}
		if (!empty($metadata['author'])) { $document->setMetaData('author', $metadata['author']);}
		
		
		parent::display($tpl);
	}
	
}
