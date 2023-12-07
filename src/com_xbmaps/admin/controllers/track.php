<?php
/*******
 * @package xbMaps Component
 * @version 1.4.0.0 7th December 2023
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
        $this->registerTask('setgpxfile', 'save');
        $this->registerTask('setelevfile', 'save');
    }
    
    protected function postSaveHook(JModelLegacy $model, $validData = array()) {
	    
        $task = $this->getTask();
        $item = $model->getItem();
	    
        if (isset($item->params) && is_array($item->params)) {
            $gpxfolder = $item->params['gpx_folder'];
            Factory::getSession()->set('gpxfolder',$gpxfolder);
            
            $elevfolder = $item->params['elev_folder'];
            Factory::getSession()->set('elevfolder',$elevfolder);
            
            $registry = new Registry($item->params);
            $item->params = (string) $registry;
        }
        
        if ($task == 'savepreview') {
	        $tid = $validData['id'];
	        if ($tid>0) {
	            $this->setRedirect('index.php?option=com_xbmaps&view=trackview&id='.$tid);
	        }
	    }
	    if (($task=='importgpx') || ($task=='setgpxfile') || ($task=='importelev') || ($task=='setelevfile')) {
	        $tid = $validData['id'];
	        if ($tid>0) {
	            $this->setRedirect('index.php?option=com_xbmaps&view=track&layout=edit&id='.$tid);
	        }	        
	    }
    }
	
 /****   
    function oldimportgpx() {
        $msg = '';
        $msgtype = 'Success';
        $jinput = Factory::getApplication()->input;
        $post   = $jinput->get('jform', '', 'RAW');
        $id = $post['id'];
        $link = 'index.php?option=com_xbmaps&view=track&layout=edit&id='.$id;
        if ($id != 0)  {
            //get the destination folder
            $folder = $post['params']['gpx_folder'];
            //		$folder .= '/'.$post['gpx_upload_folder'];
            $importfile = $jinput->files->get('jform', null, 'files', 'array' );
            if ($post['upload_newname'] != '') {
                $filename = File::makeSafe($post['upload_newname']);
                if (pathinfo($filename, PATHINFO_EXTENSION)=='') {
                    $filename .= '.gpx';
                }
            } else {
                $filename = File::makeSafe($importfile['upload_gpxfile']['name']);
            }
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
            Factory::getApplication()->enqueueMessage($msg,$msgtype);
            $this->save();
            $this->setRedirect($link, $msg, $msgtype);
        }
    }
***/
    
    function importgpx() {
        $this->import('gpx');
    }
    
    function importelec() {
        $this->import('elev');
    }
    
    function import($type) {
        $msg = '';
        $msgtype = 'Success';
        $jinput = Factory::getApplication()->input;
        $post   = $jinput->get('jform', '', 'RAW');
        $id = $post['id'];
        $link = 'index.php?option=com_xbmaps&view=track&layout=edit&id='.$id;
        if ($id != 0)  {
            // get the destination folder
            $folder = $post['params'][$type.'_folder'];
            // get uploaded filename
            $importfile = $jinput->files->get('jform', null, 'files', 'array' );
            $filename = File::makeSafe($importfile['upload_file_'.$type]['name']);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            // check if we have given it a new name
            if ($post['upload_newname_'.$type] != '') {
                $filename = File::makeSafe($post['upload_newname_'.$type]);
                if (pathinfo($filename, PATHINFO_EXTENSION)=='') {
                    // if extension not specified in name use the incoming extension
                    $filename .= '.'.$ext;
                    // this does mean we could override the uploaded extension by specifying a new one in the new name
                    $filename = pathinfo($filename, PATHINFO_FILENAME).'.'.$ext;
                }
            } else {
            }
            // now we'll check we don't already have a file of that name
            // TODO allow overwriting with checkbox on form and exit with error if exists and not overwrite allowed
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
//           $n = 0;
//            $suffix='';
            if (file_exists(JPATH_ROOT .'/'.$folder.'/'. $name.'.'.$ext)) {
                $msg = 'File <b>'.$name.'.'.$ext.'</b> already exists in <code>'.$folder.'</code> Please supply unique name within the folder';
                $msgtype = 'Error';
//                Factory::getApplication()->enqueueMessage($msg,$msgtype);
                $this->setRedirect($link, $msg, $msgtype);
                return;
            }
            $src = $importfile['upload_file_'.$type]['tmp_name'];
            $dest = JPATH_ROOT .'/'.$folder.'/'. $filename;
            if (File::upload($src, $dest)) {
                $msg .= 'file '.$filename.' uploaded ok to '.$folder;
                $this->save();
            } else {
                $msg = 'Problem uploading file';
                $msgtype = 'error';
            }
            $this->setRedirect($link, $msg, $msgtype);
        }
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
