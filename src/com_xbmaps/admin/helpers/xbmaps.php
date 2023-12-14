<?php
/*******
 * @package xbMaps Component
 * @version 1.1.0 21st December 2021
 * @filesource admin/helpers/xbmaps.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Language\Text;


class XbmapsHelper extends ContentHelper {

	public static function getActions($component = 'com_xbmaps', $section = 'component', $categoryid = 0) {
		
		$user 	= Factory::getUser();
		$result = new JObject;
		if (empty($categoryid)) {
			$assetName = $component;
			$level = $section;
		} else {
			$assetName = $component.'.category.'.(int) $categoryid;
			$level = 'category';
		}
		$actions = Access::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_xbmaps/access.xml');
		foreach ($actions as $action) {
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}
		return $result;
	}
	
	public static function addSubmenu($vName = 'dashboard') {
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_DASHBOARD'),
					'index.php?option=com_xbmaps&view=dashboard',
					$vName == 'dashboard'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_MAPS'),
					'index.php?option=com_xbmaps&view=maps',
					$vName == 'maps'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_NEWMAP'),
					'index.php?option=com_xbmaps&view=map&layout=edit',
					$vName == 'map'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_MARKERS'),
					'index.php?option=com_xbmaps&view=markers',
					$vName == 'markers'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_NEWMARKER'),
					'index.php?option=com_xbmaps&view=marker&layout=edit',
					$vName == 'marker'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_TRACKS'),
					'index.php?option=com_xbmaps&view=tracks',
					$vName == 'tracks'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_NEWTRACK'),
					'index.php?option=com_xbmaps&view=track&layout=edit',
					$vName == 'track'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_CATSLIST'),
					'index.php?option=com_xbmaps&view=catslist',
					$vName == 'catslist'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_NEWCAT'),
					'index.php?option=com_categories&view=category&task=category.edit&extension=com_xbmaps',
					$vName == 'category'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_CATEGORIES'),
					'index.php?option=com_categories&view=categories&extension=com_xbmaps',
					$vName == 'categories'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_TAGSLIST'),
					'index.php?option=com_xbmaps&view=tagslist',
					$vName == 'listtags'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_TAGS'),
					'index.php?option=com_tags',
					$vName == 'tags'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_OPTIONS'),
					'index.php?option=com_config&view=component&component=com_xbmaps',
					$vName == 'options'
					);
	}
	
	/**
	 * @name checkTitleExists()
	 * @desc checks (case insensitive) if a given title exists in a given db table
	 * @param string $title - the title to search for
	 * @param string $table - the table name to search
	 * @param string $col - the column in the table (default 'title')
	 * @return int - the id if found otherwise false
	 */
	public static function checkTitleExists($title, $table, $col = 'title') {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')->from($db->quoteName($table))
		->where('LOWER('.$db->quoteName($col).')='.$db->quote(strtolower($title)));
		$db->setQuery($query);
		$res = $db->loadResult();
		if ($res > 0) {
			return $res;
		}
		return false;
	}
		
	/**
	 * @name getCat()
	 * @desc returns details of a category given the id
	 * @param integer $catid
	 * @return array of objects or null if not found
	 */
	public static function getCat($catid) {
	    if ($catid==0) return null;
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*')
		->from('#__categories AS a ')
		->where('a.id = '.$catid);
		$db->setQuery($query);
		return $db->loadObjectList()[0];
	}
	
	/**
	 * @name parseGpxHeader
	 * @desc reads xml attributes from given gpx file
	 * @param string $gpxfile
	 * @return array
	 */
	public static function parseGpxHeader(string $gpxfile) {
		$gpxinfo=array();
		$xml = simplexml_load_file(JPATH_ROOT.$gpxfile);
		$root_attributes = $xml->attributes();
		$gpxinfo['creator'] = $root_attributes->creator;
		$gpxinfo['gpxname'] = $xml->metadata->name;
		$gpxinfo['trkname'] = $xml->trk->name;
		$gpxinfo['recdate'] = $xml->trk->trkseg[0]->trkpt[0]->time;
		return $gpxinfo;
	}
}
