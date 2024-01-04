<?php
/*******
 * @package xbMaps Component
 * @version 0.1.2.a 29th August 2021
 * @filesource admin/controllers/marker.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;

class XbmapsControllerMarker extends FormController {
		
	public function __construct($config = array(), MVCFactoryInterface $factory = null) {
		parent::__construct($config, $factory);
	}
	
	protected function postSaveHook(JModelLegacy $model, $validData = array()) {
		
		$task = $this->getTask();
		$item = $model->getItem();
		
		if (isset($item->params) && is_array($item->params)) {
			$registry = new Registry($item->params);
			$item->params = (string) $registry;
		}
	}
	
	public function publish() {
		$jip =  Factory::getApplication()->input;
		$pid =  $jip->get('cid');
		$model = $this->getModel('marker');
		$wynik = $model->publish($pid);
		$redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=markers');
		$this->setRedirect($redirectTo );
	}
	
	public function unpublish() {
		$jip =  Factory::getApplication()->input;
		$pid =  $jip->get('cid');
		$model = $this->getModel('marker');
		$wynik = $model->publish($pid,0);
		$redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=markers');
		$this->setRedirect($redirectTo );
	}
	
	public function archive() {
		$jip =  Factory::getApplication()->input;
		$pid =  $jip->get('cid');
		$model = $this->getModel('marker');
		$wynik = $model->publish($pid,2);
		$redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=markers');
		$this->setRedirect($redirectTo);
	}
	
	public function delete() {
		$jip =  Factory::getApplication()->input;
		$pid =  $jip->get('cid');
		$model = $this->getModel('marker');
		$wynik = $model->delete($pid);
		$redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=markers');
		$this->setRedirect($redirectTo );
	}
	
	public function trash() {
		$jip =  Factory::getApplication()->input;
		$pid =  $jip->get('cid');
		$model = $this->getModel('marker');
		$wynik = $model->publish($pid,-2);
		$redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=markers');
		$this->setRedirect($redirectTo );
	}
	
	public function checkin() {
		$jip =  Factory::getApplication()->input;
		$pid =  $jip->get('cid');
		$model = $this->getModel('marker');
		$wynik = $model->checkin($pid);
		$redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=markers');
		$this->setRedirect($redirectTo );
	}
	
	public function batch($model = null) {
		$model = $this->getModel('marker');
		$this->setRedirect((string)Uri::getInstance());
		return parent::batch($model);
	}
	
// 	public function preview() {
// 		$jip =  Factory::getApplication()->input;
// 		$pid =  $jip->get('cid');
// 		$redirectTo =('index.php?option=com_xbmaps&task=display&view=markerview&id='.$pid[0]);
// 		$this->setRedirect($redirectTo );
// 	}
	
}
