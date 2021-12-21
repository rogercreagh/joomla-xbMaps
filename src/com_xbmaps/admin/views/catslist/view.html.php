<?php
/*******
 * @package xbMaps Component
 * @version 0.3.0.h 21st September 2021
 * @filesource admin/views/catslist/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;

class XbmapsViewCatslist extends JViewLegacy {
    
    function display($tpl = null) {
        // Get data from the model
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        
        $this->searchTitle = $this->state->get('filter.search');
        
        $this->params = ComponentHelper::getParams('com_xbmaps');
        //we are not bothered whether cats and tags are linked or not so a simple && will suffice
        $this->global_use_cats = $this->params->get('global_use_cats');
        $this->mapcats = $this->global_use_cats && $this->params->get('maps_use_cats');
        $this->mrkcats = $this->global_use_cats && $this->params->get('markers_use_cats');
        $this->trkcats = $this->global_use_cats && $this->params->get('tracks_use_cats');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
        	
            return false;
        }
                
        XbmapsHelper::addSubmenu('catslist');
        $this->sidebar = JHtmlSidebar::render();
        
        // Set the toolbar
        $this->addToolBar();
        
        // Display the template
        parent::display($tpl);
    }
    
    protected function addToolBar() {
        $canDo = XbmapsHelper::getActions();
        
        ToolbarHelper::title(Text::_( 'XBMAPS_TITLE_CATSLIST' ), '' );
        
        if ($canDo->get('core.create') > 0) {
        	ToolbarHelper::custom('catslist.categorynew','new','','XBMAPS_NEWCAT',false);
        }
        if ($canDo->get('core.admin')) {
        	ToolbarHelper::editList('catslist.categoryedit', 'XBMAPS_EDITCAT');       	
         }
                  
         if ($canDo->get('core.admin')) {
        	ToolbarHelper::preferences('com_xbmaps');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbmaps/doc?tmpl=component#admin-cats' );
    }

    protected function setDocument() {
    	$document = Factory::getDocument();
    	$document->setTitle(strip_tags(Text::_('XBMAPS_TITLE_CATSLIST')));
    }
}
