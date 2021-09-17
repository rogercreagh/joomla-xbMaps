<?php
/*******
 * @package xbMaps
 * @version 0.1.0.m 24th July 2021
 * @filesource admin/helpers/xbmaps.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Version;


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
		//$actions = Access::getActions('com_xbmaps', $level);
		$actions = Access::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_xbmaps/access.xml');
		foreach ($actions as $action) {
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}
		return $result;
	}
	
	public static function addSubmenu($vName = 'cpanel') {
//		if ($vName != 'categories') {
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_CPANEL'),
					'index.php?option=com_xbmaps&view=cpanel',
					$vName == 'cpanel'
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
					Text::_('XBMAPS_ICONMENU_TAGSINFO'),
					'index.php?option=com_xbmaps&view=tagsinfo',
					$vName == 'listtags'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBMAPS_ICONMENU_OPTIONS'),
					'index.php?option=com_config&view=component&component=com_xbmaps',
					$vName == 'options'
					);
// 		} else {
// 			JHtmlSidebar::addEntry(
// 					Text::_('xbBooks cPanel'),
// 					'index.php?option=com_xbbooks&view=cpanel',
// 					$vName == 'cpanel'
// 					);
			
// 			JHtmlSidebar::addEntry(
// 					Text::_('Books'),
// 					'index.php?option=com_xbbooks&view=books',
// 					$vName == 'films'
// 					);
// 			JHtmlSidebar::addEntry(
// 					Text::_('Book Cat.Counts'),
// 					'index.php?option=com_xbbooks&view=bcategories',
// 					$vName == 'bcategories'
// 					);
// 		}
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
	 * @name getIdFromAlias()
	 * @desc gets the id of an item in a given table from the alias
	 * @param string $table - table to search
	 * @param string $alias - alias to find
	 * @param string $ext - the extension to match if searching in #__categories for a different extension 
	 * @return int - item id or 0 if not found
	 */
	public static function getIdFromAlias($table, $alias, $ext = 'com_xbmaps') {
		$alias = trim($alias,"' ");
		$table = trim($table,"' ");
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id')->from($db->quoteName($table))->where($db->quoteName('alias')." = ".$db->quote($alias));
		if ($table === '#__categories') {
			$query->where($db->quoteName('extension')." = ".$db->quote($ext));
		}
		$db->setQuery($query);
		$res =0;
		$res = $db->loadResult();
		return $res;
	}
	
	/**
	 * @name getCat()
	 * @desc returns details of a category given the id
	 * @param integer $catid
	 * @return array of objects or null if not found
	 */
	public static function getCat($catid) {
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*')
		->from('#__categories AS a ')
		->where('a.id = '.$catid);
		$db->setQuery($query);
		return $db->loadObjectList()[0];
	}
	
}
