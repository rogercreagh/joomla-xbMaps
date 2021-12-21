<?php
/*******
 * @package xbMaps Component
 * @version 0.1.0 2nd July 2021
 * @filesource admin/controller.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

class XbmapsController extends JControllerLegacy {
	
	protected $default_view = 'cpanel';
	
	public function display ($cachable = false, $urlparms = false){
		
		return parent::display();
	}
}

