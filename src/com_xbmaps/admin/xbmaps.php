<?php
/*******
 * @package xbMaps Component
 * @version 1.5.2.0 3rd January 2023
 * @since 0.1.0 2nd July 2021
 * @filesource admin/xbmaps.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;

if (!Factory::getUser()->authorise('core.manage', 'com_xbmaps')) {
	Factory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'),'warning');
	return false;
}

//add the component, xbculture and fontawesome css
$document = Factory::getDocument();
$cssFile = Uri::root(true)."/media/com_xbmaps/css/xbmaps.css";
$document->addStyleSheet($cssFile);
$cssFile = Uri::root(true)."/media/com_xbmaps/css/xblib.css";
$document->addStyleSheet($cssFile);

$params = ComponentHelper::getParams('com_xbmaps');
$fasource = $params->get('fasource',2);	
if ($fasource==2) {
    $cssFile = "https://use.fontawesome.com/releases/v5.8.1/css/all.css\" integrity=\"sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf\" crossorigin=\"anonymous";
    $document->addStyleSheet($cssFile);
} elseif ($fasource==1) {
    $fascript="https://kit.fontawesome.com/".$params->get('fakitid').".js\" crossorigin=\"anonymous";
    $document->addScript($fascript);   
}

JLoader::register('XbmapsHelper', JPATH_ADMINISTRATOR . '/components/com_xbmaps/helpers/xbmaps.php');
JLoader::register('XbMapHelper', JPATH_ADMINISTRATOR . '/components/com_xbmaps/helpers/xbmaphelper.php');
JLoader::register('XbmapsGeneral', JPATH_ADMINISTRATOR . '/components/com_xbmaps/helpers/xbmapsgeneral.php');

// Get an instance of the controller prefixed
$controller = JControllerLegacy::getInstance('Xbmaps');

// Perform the Request task and Execute request task
$controller->execute(Factory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();

