<?php
/*******
 * @package xbMaps Component
 * @version 0.1.0.n 4th August 2021
 * @filesource site/xbmaps.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined( '_JEXEC' ) or die();

use Joomla\CMS\Factory;

$document = Factory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_xbmaps/css/xbmaps.css', array('version'=>'auto'));
$cssFile = "https://use.fontawesome.com/releases/v5.8.1/css/all.css\" integrity=\"sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf\" crossorigin=\"anonymous";
$document->addStyleSheet($cssFile);

JLoader::register('XbMapHelper', JPATH_ADMINISTRATOR . '/components/com_xbmaps/helpers/xbmaphelper.php');
JLoader::register('XbmapsGeneral', JPATH_ADMINISTRATOR . '/components/com_xbmaps/helpers/xbmapsgeneral.php');

// Get an instance of the controller
$controller = JControllerLegacy::getInstance('Xbmaps');

// Perform the Request task
$input = Factory::getApplication()->input;
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();


