<?php
/*******
 * @package xbMaps Component
 * @version 0.7.0.d 11th October 2021
 * @filesource admin/controllers/track.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Component\ComponentHelper;

class XbmapsControllerTrack extends FormController {
			
    public function __construct($config = array(), MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);
        $this->registerTask('savepreview', 'save');
    }
    
    protected function postSaveHook(JModelLegacy $model, $validData = array()) {
	    
        $task = $this->getTask();
        $item = $model->getItem();
	    
	    if (isset($item->params) && is_array($item->params)) {
	        $registry = new Registry($item->params);
	        $item->params = (string) $registry;
	    }
	    
	    if ($task == 'savepreview') {	    	
	        $tid = $validData['id'];
	        if ($tid>0) {
	        	$this->setRedirect('index.php?option=com_xbmaps&view=trackview&id='.$tid);	   
	        }
	    }
	}
	
	function import() {
		$msg = '';
		$msgtype = 'Success';
		$jinput = Factory::getApplication()->input;
		$post   = $jinput->get('jform', 'array()', 'ARRAY');
		$id = $post['id'];
		$link = 'index.php?option=com_xbmaps&view=track&layout=edit&id='.$id;
		//get the destination folder
		$params = ComponentHelper::getParams('com_xbmaps');
		$folder = $params->get('def_tracks_folder','xbmaps-tracks');
		if ($folder != 'xbmaps-tracks') { $folder = 'images/'.$folder; }
		//get the filename and copy it in to tmp folder
		$importfile = $jinput->files->get('jform', null, 'files', 'array' );
		$filename = File::makeSafe($importfile['upload_gpxfile']['name']);
		$name = pathinfo($filename, PATHINFO_FILENAME);
		$n = 0;
		$suffix='';
		while (file_exists(JPATH_ROOT .'/'.$folder.'/'. $name.$suffix.'.gpx')) {
			$n ++;
			$suffix = '-'.$n;
		}
		if ($suffix) {
			$msg = 'File '.$filename.' already exists in '.$folder.'.<br />Saving as '.$name.$suffix.'.gpx<br />';
			$msgtype = 'Warning';
			$filename = $name.$suffix.'.gpx';
		}
		$src = $importfile['upload_gpxfile']['tmp_name'];
		$dest = JPATH_ROOT .'/'.$folder.'/'. $filename;
		if (File::upload($src, $dest)) {
			$msg .= 'gpx file '.$filename.' uploaded ok to '.$folder;
		//TODO check file for valid track data			
		} else {
			$msg = 'Problem uploading file';
			$msgtype = 'error';
		}
		$this->setRedirect($link, $msg, $msgtype);
	}
	
	public function publish() {
	    $jip =  Factory::getApplication()->input;
	    $pid =  $jip->get('cid');
	    $model = $this->getModel('track');
	    $wynik = $model->publish($pid);
	    $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=tracks');
	    $this->setRedirect($redirectTo );
	}
	
	public function unpublish() {
	    $jip =  Factory::getApplication()->input;
	    $pid =  $jip->get('cid');
	    $model = $this->getModel('track');
	    $wynik = $model->publish($pid,0);
	    $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=tracks');
	    $this->setRedirect($redirectTo );
	}
	
	public function archive() {
	    $jip =  Factory::getApplication()->input;
	    $pid =  $jip->get('cid');
	    $model = $this->getModel('track');
	    $wynik = $model->publish($pid,2);
	    $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=tracks');
	    $this->setRedirect($redirectTo);
	}
	
	public function delete() {
	    $jip =  Factory::getApplication()->input;
	    $pid =  $jip->get('cid');
	    $model = $this->getModel('track');
	    $wynik = $model->delete($pid);
	    $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=tracks');
	    $this->setRedirect($redirectTo );
	}
	
	public function trash() {
	    $jip =  Factory::getApplication()->input;
	    $pid =  $jip->get('cid');
	    $model = $this->getModel('track');
	    $wynik = $model->publish($pid,-2);
	    $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=tracks');
	    $this->setRedirect($redirectTo );
	}
	
	public function checkin() {
	    $jip =  Factory::getApplication()->input;
	    $pid =  $jip->get('cid');
	    $model = $this->getModel('track');
	    $wynik = $model->checkin($pid);
	    $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=tracks');
	    $this->setRedirect($redirectTo );
	}
	
	public function batch($model = null) {
	    $model = $this->getModel('track');
	    $this->setRedirect((string)Uri::getInstance());
	    return parent::batch($model);
	}

	public function preview() {
	    $jip =  Factory::getApplication()->input;
	    $pid =  $jip->get('cid');
	    $redirectTo =('index.php?option=com_xbmaps&task=display&view=trackview&id='.$pid[0]);
	    $this->setRedirect($redirectTo );
	}
	
	
}
