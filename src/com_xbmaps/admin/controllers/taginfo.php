<?php
/*******
 * @package xbMaps
 * @version 0.3.0.de 19th September 2021
 * @filesource admin/controlers/taginfo.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbmapsControllerTaginfo extends JControllerAdmin {
    
    public function getModel($name = 'Taginfo', $prefix = 'XbmapsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);        
        return $model;
    }
    
    function tagslist() {
    	$this->setRedirect('index.php?option=com_xbmaps&view=tagslist');
    }

    function tagedit() {
    	$id =  Factory::getApplication()->input->get('tid');
    	$this->setRedirect('index.php?option=com_tags&task=tag.edit&id='.$id);
    }
    
}