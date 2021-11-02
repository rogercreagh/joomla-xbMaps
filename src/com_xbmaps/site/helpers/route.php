<?php
/*******
 * @package xbMaps
 * @version 0.1.2.a 30th August 2021
 * @filesource site/helpers/route.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

class XbmapsHelperRoute
{
	public static function &getItems() {
		static $items;
		
		// Get the menu items for this component.
		if (!isset($items)) {
			$component = ComponentHelper::getComponent('com_xbmaps');
			$items     = Factory::getApplication()->getMenu()->getItems('component_id', $component->id);			
			// If no items found, set to empty array.
			if (!$items) {
				$items = array();
			}
		}		
		return $items;
	}

	/**
	 * @name getMapsRoute
	 * @desc Get menu itemid maplist view in default layout
	 * @param boolean $retstr if false return integer id, if true return return string with "&Itemid="
	 * @return string|int|NULL
	 */
	public static function getMapsRoute($retstr=false) {
		$items  = self::getItems();
		foreach ($items as $item) {
			if ((isset($item->query['view']) && $item->query['view'] === 'maplist')
					&& ((empty($item->query['layout']) || $item->query['layout'] === 'default')) ) {
						return ($retstr)? '&Itemid='.$item->id : $item->id;
					}
		}
		return null;
	}
	
	/**
	 * @name getMapsLink
	 * @desc Get link to maps view
	 * @return string
	 */
	public static function getMapsLink() {
		$link = 'index.php?option=com_xbmaps';
		$items  = self::getItems();
		foreach ($items as $item) {
		    if ((isset($item->query['view']) && $item->query['view'] === 'maplist') 
		        && ((empty($item->query['layout']) || $item->query['layout'] === 'default')) ) {
			        return $link.'&Itemid='.$item->id;              
			}
		}
		return $link.'&view=maplist';
	}

	/**
	 * @name getMapRoute
	 * @desc returns the itemid for a menu item for map view with id  $fd, if not found returns menu id for a maplist, if not found null
	 * @param int $fid
	 * @return int|string|NULL
	 */
	public static function getMapRoute($fid) {
		$items  = self::getItems();
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'map' && isset($item->query['id']) && $item->query['id'] == $fid ) {
				return $item->id;
			}
		}
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'maplist' &&
				(empty($item->query['layout']) || $item->query['layout'] === 'default')) {
					return $item->id.'&view=map&id='.$fid;
			}
		}
		return null;
	}

	/**
	 * @name getMapLink
	 * @desc gets a comlete link for a map menu item either dedicated, or maplist menu or generic
	 * @param int $fid
	 * @return string
	 */
	public static function getMapLink($fid) {
	    $link = 'index.php?option=com_xbmaps';
	    $items  = self::getItems();
	    foreach ($items as $item) {
	        if (isset($item->query['view']) && $item->query['view'] === 'map' && isset($item->query['id']) && $item->query['id'] == $fid ) {
	            return $link.'&Itemid='.$item->id;
	        }
	    }
	    foreach ($items as $item) {
	        if (isset($item->query['view']) && $item->query['view'] === 'maplist' &&
	            (empty($item->query['layout']) || $item->query['layout'] === 'default')) {
	                return $link.'&Itemid='.$item->id.'&view=map&id='.$fid;
	            }
	    }
	    return $link.'&view=map&id='.$fid;
	}
	
	/**
	 * @name getTracksRoute
	 * @desc Get menu itemid tracklist view in default layout
	 * @param boolean $retstr if false return integer id, if true return return string with "&Itemid="
	 * @return string|int|NULL
	 */
	public static function getTracksRoute($retstr=false) {
	    $items  = self::getItems();
	    foreach ($items as $item) {
	        if ((isset($item->query['view']) && $item->query['view'] === 'tracklist')
	            && ((empty($item->query['layout']) || $item->query['layout'] === 'default')) ) {
	                return ($retstr)? '&Itemid='.$item->id : $item->id;
	            }
	    }
	    return null;
	}
	
	/**
	 * @name getTracksLink
	 * @desc Get link to maps view
	 * @return string
	 */
	public static function getTracksLink() {
		$link = 'index.php?option=com_xbmaps';
		$items  = self::getItems();
		foreach ($items as $item) {
			if ((isset($item->query['view']) && $item->query['view'] === 'tracklist')
					&& ((empty($item->query['layout']) || $item->query['layout'] === 'default')) ) {
						return $link.'&Itemid='.$item->id;
					}
		}
		return $link.'&view=tracklist';
	}
	
	/**
	 * @name getTrackRoute
	 * @desc returns the itemid for a menu item for track view with id  $fd, if not found returns menu id for a tracklist, if not found null
	 * @param int $fid
	 * @return int|string|NULL
	 */
	public static function getTrackRoute($fid) {
	    $items  = self::getItems();
	    foreach ($items as $item) {
	        if (isset($item->query['view']) && $item->query['view'] === 'track' && isset($item->query['id']) && $item->query['id'] == $fid ) {
	            return $item->id;
	        }
	    }
	    foreach ($items as $item) {
	        if (isset($item->query['view']) && $item->query['view'] === 'tracklist' &&
	            (empty($item->query['layout']) || $item->query['layout'] === 'default')) {
	                return $item->id.'&view=track&id='.$fid;
	            }
	    }
	    return null;
	}
	
	/**
	 * @name getTrackLink
	 * @desc gets a complete link for a map menu item either dedicated, or maplist menu or generic
	 * @param int $fid
	 * @return string
	 */
	public static function getTrackLink($fid) {
		$link = 'index.php?option=com_xbmaps';
		$items  = self::getItems();
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'track' && isset($item->query['id']) && $item->query['id'] == $fid ) {
				return $link.'&Itemid='.$item->id;
			}
		}
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'tracklist' &&
					(empty($item->query['layout']) || $item->query['layout'] === 'default')) {
						return $link.'&Itemid='.$item->id.'&view=track&id='.$fid;
					}
		}
		return $link.'&view=track&id='.$fid;
	}
	
	/**
	 * @name getMarkerRoute
	 * @desc returns the itemid for a menu item for marker view with id  $fd, if not found returns menu id for a markerlist, if not found null
	 * @param int $fid
	 * @return int|string|NULL
	 */
	public static function getMarkerRoute($fid) {
		$items  = self::getItems();
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'marker' && isset($item->query['id']) && $item->query['id'] == $fid ) {
				return $item->id;
			}
		}
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'markerlist' &&
					(empty($item->query['layout']) || $item->query['layout'] === 'default')) {
						return $item->id.'&view=marker&id='.$fid;
					}
		}
		return null;
	}
	
	/**
	 * @name getCategoriesRoute
	 * @desc returns itemid for menu item for the category list if exists
	 * @return int|NULL
	 */
	public static function getCategoriesRoute() {
		$items  = self::getItems();
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'catlist' &&
				(empty($item->query['layout']) || $item->query['layout'] === 'default')) {
					return $item->id;
			}
		}
		return null;
	}
	
	/**
	 * @name getTagsRoute
	 * @desc returns itemid for menu item for the tag list if exists
	 * @return int|NULL
	 */
	public static function getTagsRoute() {
		$items  = self::getItems();
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'tags' &&
				(empty($item->query['layout']) || $item->query['layout'] === 'default')) {
					return $item->id;
			}
		}
		return null;
	}
	
}
