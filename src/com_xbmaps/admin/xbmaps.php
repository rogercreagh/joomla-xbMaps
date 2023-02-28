<?php
/*******
 * @package xbMaps Component
 * @version 0.8.0.g 20th October 2021
 * @since 0.1.0 2nd July 2021
 * @filesource admin/xbmaps.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

if (!Factory::getUser()->authorise('core.manage', 'com_xbmaps')) {
	Factory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'),'warning');
	return false;
}

//add the component, xbculture and fontawesome css
$document = Factory::getDocument();
$cssFile = Uri::root(true)."/media/com_xbmaps/css/xbmaps.css";
$document->addStyleSheet($cssFile);
$cssFile = "https://use.fontawesome.com/releases/v5.8.1/css/all.css\" integrity=\"sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf\" crossorigin=\"anonymous";
$document->addStyleSheet($cssFile);

JLoader::register('XbmapsHelper', JPATH_ADMINISTRATOR . '/components/com_xbmaps/helpers/xbmaps.php');
JLoader::register('XbMapHelper', JPATH_ADMINISTRATOR . '/components/com_xbmaps/helpers/xbmaphelper.php');
JLoader::register('XbmapsGeneral', JPATH_ADMINISTRATOR . '/components/com_xbmaps/helpers/xbmapsgeneral.php');

// Get an instance of the controller prefixed
$controller = JControllerLegacy::getInstance('Xbmaps');

// Perform the Request task and Execute request task
$controller->execute(Factory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();

