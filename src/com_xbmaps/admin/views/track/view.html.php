<?php
/*******
 * @package xbMaps Component
 * @version 1.5.0.2 2nd January 2024
 * @filesource admin/views/track/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class XbmapsViewTrack extends JViewLegacy {
		
    protected $form = null;
    
    public function display($tpl = null) {
		
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->canDo = XbmapsHelper::getActions('com_xbmaps', 'track', $this->item->id);
        
        $this->params = ComponentHelper::getParams('com_xbmaps');
        $this->track_map_type = $this->params->get('track_map_type','');
        $this->basegpxfolder = $this->params->get('base_gpx_folder');
        $this->baseelevfolder = 'images/xbmaps/elevations';
        $this->gpxfolder = $this->basegpxfolder;
        if ($this->item->id > 0) {
            $this->gpxfolder = array_key_exists('gpx_folder', $this->item->params) ? $this->item->params['gpx_folder'] : $this->basegpxfolder;
        }
        $this->elevfolder = $this->baseelevfolder;
        if ($this->item->id > 0) {
            $this->elevfolder = array_key_exists('elev_folder', $this->item->params) ? $this->item->params['elev_folder'] : $this->baseelevfolder;
        }
        // Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
		}
		$this->gpxinfo= '';
		if (is_file(JPATH_ROOT.'/'.$this->item->gpx_filename)) {
			$this->gpxinfo = XbmapsHelper::parseGpxHeader($this->item->gpx_filename);
		}
		
		$this->addToolbar();
		
		parent::display($tpl);
		
		$this->setDocument();
	}
	
	protected function addToolbar() {
		
	    $input = Factory::getApplication()->input;
	    $input->set('hidemainmenu', true);
	    $user = Factory::getUser();
	    $userId = $user->get('id');
	    $checkedOut     = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
	    
	    $canDo = $this->canDo;
	    
	    $isNew = ($this->item->id == 0);
	    
	    if ($isNew) {
	        $title = Text::_('XBMAPS_TITLE_NEWTRACK');
	    } elseif ($checkedOut) {
	        $title = Text::_('XBMAPS_TITLE_VIEWTRACK');
	    } else {
	        $title = Text::_('XBMAPS_TITLE_EDITTRACK');
	    }
	    ToolBarHelper::title($title, '');
	    
	    ToolbarHelper::apply('track.apply');
	    ToolbarHelper::save('track.save');
	    ToolbarHelper::save2new('track.save2new');
	    ToolbarHelper::save2copy('track.save2copy');
	    if ($isNew) {
	        ToolbarHelper::cancel('track.cancel','JTOOLBAR_CANCEL');
	    } else {
	        ToolbarHelper::cancel('track.cancel','JTOOLBAR_CLOSE');
	    }
	    
	    if (!$isNew) {
		    ToolbarHelper::custom('','spacer');
//		    ToolBarHelper::custom('track.savepreview', 'eye', '', 'Preview Track', false) ;
		    $bar = Toolbar::getInstance( 'toolbar' );
		    $dhtml = '<a href="#ajax-xbmodal" data-toggle="modal" data-target="#ajax-xbmodal" '
		        .'onclick="window.com=\'maps\';window.view=\'track\';window.pvid='.$this->item->id.'" '
		            .'class="btn btn-small btn-primary"><i class="icon-eye"></i> '.Text::_('Preview').'</a>';
		            $bar->appendButton('Custom', $dhtml);
	    }
	    
	    ToolbarHelper::help( '', false,'https://crosborne.uk/xbmaps/doc?tmpl=component#trackedit' );
	    
	}
	
	protected function setDocument() {
		$document = Factory::getDocument();
		$document->setTitle(strip_tags(($this->item->id == 0) ? Text::_('XBMAPS_TITLE_NEWTRACK') : Text::_('XBMAPS_TITLE_EDITTRACK')));
	}
	
}
