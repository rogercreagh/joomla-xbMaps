<?php
/*******
 * @package xbMaps Component
 * @version 1.5.2.0 4th January 2023
 * @filesource site/xbmaps.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined( '_JEXEC' ) or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;

$document = Factory::getDocument();
$document->addStyleSheet(Uri::root(true) . '/media/com_xbmaps/css/xbmaps.css', array('version'=>'auto'));
$cssFile = Uri::root(true)."/media/com_xbmaps/css/xblib.css";
$document->addStyleSheet($cssFile);
//$cssFile = "https://use.fontawesome.com/releases/v5.8.1/css/all.css\" integrity=\"sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf\" crossorigin=\"anonymous";
//$document->addStyleSheet($cssFile);
//$fascript="https://kit.fontawesome.com/012857417f.js\" crossorigin=\"anonymous";
//$document->addScript($fascript);

$params = ComponentHelper::getParams('com_xbmaps');
$fasource = $params->get('fasource',2);
if ($fasource==2) {
    $cssFile = "https://use.fontawesome.com/releases/v5.8.1/css/all.css\" integrity=\"sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf\" crossorigin=\"anonymous";
    $document->addStyleSheet($cssFile);
} elseif ($fasource==1) {
    $fascript="https://kit.fontawesome.com/".$params->get('fakitid').".js\" crossorigin=\"anonymous";
    $document->addScript($fascript);
}

JLoader::register('XbMapHelper', JPATH_ADMINISTRATOR . '/components/com_xbmaps/helpers/xbmaphelper.php');
JLoader::register('XbmapsGeneral', JPATH_ADMINISTRATOR . '/components/com_xbmaps/helpers/xbmapsgeneral.php');

// Get an instance of the controller
$controller = JControllerLegacy::getInstance('Xbmaps');

// Perform the Request task
$input = Factory::getApplication()->input;
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();


