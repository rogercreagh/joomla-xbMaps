<?php
/*******
 * @package xbMaps
 * @version 0.1.0 1st July 2021
 * @filesource admin/controllers/tracks.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

class XbmapsControllerTracks extends JControllerAdmin {
	
	public function getModel($name = 'Track', $prefix = 'XbmapsModel', $config = array('ignore_request' => true)) {
				$model = parent::getModel($name, $prefix, $config );
				return $model;
	}
	
}
