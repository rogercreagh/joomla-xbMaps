<?php
/*******
 * @package xbMaps
 * @version 0.7.0.e 13th October 2021
 * @filesource admin/tables/map.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Table\Observer\Tags;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

class XbmapsTableMap extends Table
{
	public function __construct(&$db) {
		$this->setColumnAlias('published', 'state');
		parent::__construct('#__xbmaps_maps', 'id', $db);
		Tags::createObserver($this, array('typeAlias' => 'com_xbmaps.map'));
	}
	
	public function check() {
		$params = ComponentHelper::getParams('com_xbmaps');
		
		$title = trim($this->title);
		//require title
		if ($title == '') {
			$this->setError(Text::_('XBMAPS_PROVIDE_VALID_TITLE'));
			return false;
		}
		
		if (($this->id == 0) && (XbmapsHelper::checkTitleExists($title,'#__xbmaps_maps'))) {
			$this->setError(Text::sprintf('XBMAPS_TITLE_EXISTS',$title));			
			return false;
		}
		
		$this->title = $title;
		//create alias if not set - title is already unique
		if (trim($this->alias) == '') {
			$this->alias = $title;
		}
		$this->alias = OutputFilter::stringURLSafe(strtolower($this->alias));
		
		//require summary or make summary from description
		if (trim($this->summary) =='') {
		    $this->summary = XbmapsGeneral::makeSummaryText($this->description,180,true);
		}
		if (trim($this->summary) =='') {
		    $this->setError(Text::_('XBMAPS_SUM_OR_DESC_MISSING'));
		    return false;
		}
		
		//if cat not set then set default category 
		if (!$this->catid>0) {
			$defcat = $params->get('def_new_mapcat');
			if ($defcat == 0) {
				$defcat = XbmapsHelper::getIdFromAlias('#__categories', 'uncategorised');
			}
			if ($defcat>0) {
				$this->catid = $defcat;
				if (($params->get('global_use_cats') && ($params->get('maps_use_cats')))) {
    				Factory::getApplication()->enqueueMessage(JText::_('XBMAPS_CATEGORY_DEFAULT_SET').' ('.XbmapsHelper::getCat($this->catid)->title.')');				    
				}
			} else {
				// this shouldn't happen unless uncategorised has been deleted
				$this->setError(JText::_('Please set a category'));
				return false;
			}
		}
		
		//if maptype not set then get default type
		if ($this->map_type == '') {
			$this->map_type = $params->get('map_type','osm');
		}
				
		//set metadata to defaults
		$metadata = json_decode($this->metadata,true);
		// meta.author will be created_by_alias (see above)
		if ($metadata['author'] == '') {
			if ($this->created_by_alias =='') {
				$metadata['author'] = $params->get('def_author');
			} else {
				$metadata['author'] = $this->created_by_alias;
			}
		}
		//meta.description can be set to first 150 chars of summary if not otherwise set and option is set
		$summary_metadesc = $params->get('summary_metadesc');
		if (($summary_metadesc) && (trim($metadata['metadesc']) == '')) {
			$metadata['metadesc'] = HtmlHelper::_('string.truncate', strip_tags($this->description),150,true,false);
		}
		//meta.rights will be set to default if not otherwise set
		$def_rights = $params->get('def_rights');
		if (($def_rights != '') && (trim($metadata['rights']) == '')) {
			$metadata['rights'] = $def_rights;
		}
		//meta.keywords will be set to a list of tags unless otherwise set if the option is set
		//TODO update this when tags are added
		// convert existing keyword list to array, get tag names as array, merge arrays and implode to a list
		$tags_keywords = $params->get('tags_keywords');
		if (($tags_keywords) && (trim($metadata['metakey']) == '')) {
			$tagsHelper = new TagsHelper;
			$tags = implode(',',$tagsHelper->getTagNames(explode(',',$tagsHelper->getTagIds($this->id,'com_xbmaps.map'))));
			$metadata['metakey'] = $tags;
		}
		$this->metadata = json_encode($metadata);
		
		return true;
	}

	public function bind($array, $ignore = '') {
		
		if (isset($array['params']) && is_array($array['params'])) {
			// Convert the params field to a string.
			$parameters = new Registry;
			$parameters->loadArray($array['params']);
			$array['params'] = (string)$parameters;
		}
		
		// 		if (isset($array['rules']) && is_array($array['rules'])) {
		//             $rules = new JAccessRules($array['rules']);
		//             $this->setRules($rules);
		//         }
		
		if (isset($array['metadata']) && is_array($array['metadata'])) {
			$registry = new Registry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string)$registry;
		}
		return parent::bind($array, $ignore);
		
	}

	protected function _getAssetParentId(Table $table = null, $id = null) {
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = Table::getInstance('Asset');
		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();		
		// Find the parent-asset
		if (($this->catid)&& !empty($this->catid)) {
			// The item has a category as asset-parent
			$assetParent->loadByName('com_xbmaps.category.' . (int) $this->catid);
		} else {
			// The item has the component as asset-parent
			$assetParent->loadByName('com_xbmaps');
		}		
		// Return the found asset-parent-id
		if ($assetParent->id) {
			$assetParentId=$assetParent->id;
		}
		return $assetParentId;
	}
	
	/**
	 * Replacement for table class checkIn() function to write null instead of zeros in the datetime field.
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::checkIn()
	 */
	public function checkIn($pk = null) {
	    $checkedOutField = $this->getColumnAlias('checked_out');
	    $checkedOutTimeField = $this->getColumnAlias('checked_out_time');
	    
	    // If there is no checked_out or checked_out_time field, just return true.
	    if (!property_exists($this, $checkedOutField) || !property_exists($this, $checkedOutTimeField)) {
	        return true;
	    }
	    
	    if (is_null($pk)) {
	        $pk = array();
	        
	        foreach ($this->_tbl_keys as $key) {
	            $pk[$this->$key] = $this->$key;
	        }
	    } elseif (!is_array($pk)) {
	        $pk = array($this->_tbl_key => $pk);
	    }
	    
	    foreach ($this->_tbl_keys as $key) {
	        $pk[$key] = empty($pk[$key]) ? $this->$key : $pk[$key];
	        
	        if ($pk[$key] === null) {
	            throw new \UnexpectedValueException('Null primary key not allowed.');
	        }
	    }
	    	    
	    // Check the row in by primary key.
	    $query = $this->_db->getQuery(true)
	       ->update($this->_tbl)
	       ->set($this->_db->quoteName($checkedOutField) . ' = 0' )
	       ->set($this->_db->quoteName($checkedOutTimeField) . ' = NULL' );
	    parent::appendPrimaryKeys($query, $pk);
	    $this->_db->setQuery($query);
	    
	    // Check for a database error.
	    $this->_db->execute();
	    
	    // Set table values in the object.
	    $this->$checkedOutField     =  0;
	    $this->$checkedOutTimeField =  '';
	    
	    $dispatcher = \JEventDispatcher::getInstance();
	    $dispatcher->trigger('onAfterCheckin', array($this->_tbl));
	    
	    return true;
	}
	
}