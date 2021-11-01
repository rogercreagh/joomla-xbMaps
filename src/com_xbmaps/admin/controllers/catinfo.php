<?php
/*******
 * @package xbMaps
 * @version 0.3.0.b 18th September 2021
 * @filesource admin/controlers/catinfo.php 
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

class XbmapsControllerCatinfo extends JControllerAdmin {
    
    public function getModel($name = 'Category', $prefix = 'XbmapsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);        
        return $model;
    }
    
    function catslist() {
    	$this->setRedirect('index.php?option=com_xbfilms&view=catlist');
    }
    
}