<?php
/*******
 * @package xbMaps
 * @version 0.1.0 1st July 2021
 * @filesource site/controller.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined( '_JEXEC' ) or die();

class XbmapsController extends JControllerLegacy
{
	public function display ($cachable = false, $urlparms = false){
		require_once JPATH_COMPONENT.'/helpers/xbmaps.php';
//		require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/xbfilmsgeneral.php';
//		require_once JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbculture.php';
		
		return parent::display();
	}
	
}
