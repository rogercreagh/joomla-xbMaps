<?php
/*******
 * @package xbMaps Component
 * @version 0.3.0.d 19th September 2021
 * @filesource admin/controlers/tagslist.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbmapsControllerTagslist extends JControllerAdmin {
    
    public function getModel($name = 'Tagslist', $prefix = 'XbmapsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    function tagedit() {
    	$ids =  Factory::getApplication()->input->get('cid');
    	$id=$ids[0];
    	$this->setRedirect('index.php?option=com_tags&task=tag.edit&id='.$id);
    }
    
    function tagnew() {
    	$this->setRedirect('index.php?option=com_tags&task=tag.edit&id=0');
    }
        
}