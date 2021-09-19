<?php
/*******
 * @package xbMaps
 * @version 0.3.0.d 17th September 2021
 * @filesource admin/views/catslist/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class XbmapsViewCatslist extends JViewLegacy {
    
    function display($tpl = null) {
        // Get data from the model
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        
        $this->searchTitle = $this->state->get('filter.search');
        
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
        
        //index.php?option=com_categories&view=category&layout=edit&extension=com_xbfilms
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
