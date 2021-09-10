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

/*
jimport( 'joomla.application.component.controller' );

class PhocaMapsController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = false)
	{
		
		if ( ! JFactory::getApplication()->input->get('view') ) {
			JFactory::getApplication()->input->set('view', 'map' );
		}
		
		$paramsC 	= JComponentHelper::getParams('com_phocamaps');
		$cache 		= $paramsC->get( 'enable_cache', 0 );
		$cachable 	= false;
		if ($cache == 1) {
			$cachable 	= true;
		}
		
		$document 	= JFactory::getDocument();

		$safeurlparams = array('catid'=>'INT','id'=>'INT','cid'=>'ARRAY','year'=>'INT','month'=>'INT','limit'=>'INT','limitstart'=>'INT',
			'showall'=>'INT','return'=>'BASE64','filter'=>'STRING','filter_order'=>'CMD','filter_order_Dir'=>'CMD','filter-search'=>'STRING','print'=>'BOOLEAN','lang'=>'CMD');

		parent::display($cachable,$safeurlparams);

		return $this;
	}
}
?>
*/