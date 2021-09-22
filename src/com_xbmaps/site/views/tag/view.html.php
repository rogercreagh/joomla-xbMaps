<?php 
/*******
 * @package xbMaps
 * @version 0.3.0.h 22nd September 2021
 * @filesource site/views/tag/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;
use Joomla\CMS\Factory;

class XbmapsViewTag extends JViewLegacy {

	public function display($tpl = null) {
		
		$this->item = $this->get('Item');
		
		$this->state = $this->get('State');
		$this->params = $this->state->get('params');
		//if tags disabled on front-end redirect to maps view (we shouldn't even be here)
		if ($this->params->get('global_use_tags')==0) {
			$app = Factory::getApplication();
			$app->redirect('index.php?option=com_xbmaps&view=maplist');
			$app->close();
		}
		$this->maptags = $this->params->get('maps_use_tags');
		$this->mrktags = $this->params->get('markers_use_tags');
		$this->trktags = $this->params->get('tracks_use_tags');
		
		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
			return false;
		}
		
		$this->hide_empty = $this->params->get('hide_empty','','int');
		
		$document = $this->document; //Factory::getDocument();
		$document->setTitle('Tag view: '.$this->item->title);
		$document->setMetaData('title', JText::_('Tag details').' '.$this->item->title);
		$metadata = json_decode($this->item->metadata,true);
		if (!empty($metadata['metadesc'])) { $document->setDescription($metadata['metadesc']); }
		if (!empty($metadata['metakey'])) { $document->setMetaData('keywords', $metadata['metakey']);}
		if (!empty($metadata['rights'])) { $document->setMetaData('rights', $metadata['rights']);}
		if (!empty($metadata['robots'])) { $document->setMetaData('robots', $metadata['robots']);}
		if (!empty($metadata['author'])) { $document->setMetaData('author', $metadata['author']);}
		
		parent::display($tpl);
	}
	
}

