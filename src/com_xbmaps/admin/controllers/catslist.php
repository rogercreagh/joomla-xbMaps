<?php
/*******
 * @package xbMaps
 * @version 0.3.0.a 17th September 2021
 * @filesource admin/controlers/catslist.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

class XbmapsControllerCatslist extends JControllerAdmin {
 
	protected $edcatlink = 'index.php?option=com_categories&task=category.edit&extension=com_xbmaps&id=';
	
    public function getModel($name = 'Categories', $prefix = 'XbmapsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    function categoryedit() {
    	$ids =  Factory::getApplication()->input->get('cid');
    	$id=$ids[0];
     	$this->setRedirect($this->edcatlink.$id);    		
   }
    
    function categorynew() {
    	$this->setRedirect($this->edcatlink.'0');
    }
        
}