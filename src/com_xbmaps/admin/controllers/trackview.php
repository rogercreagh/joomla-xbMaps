<?php
/*******
 * @package xbMaps
 * @version 0.1.1.j 26th August 2021
 * @filesource admin/controllers/trackview.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbmapsControllerTrackview extends JControllerAdmin {
	
    public function edit() {
    	$jip =  Factory::getApplication()->input;
    	$tid = $jip->get('id');
    	$redirectTo =('index.php?option=com_xbmaps&task=track.edit&id='.$tid);
        $this->setRedirect($redirectTo );
    }
    
    public function list() {
        $redirectTo =('index.php?option=com_xbmaps&view=tracks');
        $this->setRedirect($redirectTo );
    }
}
