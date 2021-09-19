<?php
/*******
 * @package xbMaps
 * @version 0.3.0.d 19th September 2021
 * @filesource admin/views/tagslist/view.html.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class XbmapsViewTagslist extends JViewLegacy {
    
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
              
        XbmapsHelper::addSubmenu('tagslist');
        $this->sidebar = JHtmlSidebar::render();
        
        // Set the toolbar
        $this->addToolBar();
        
        // Display the template
        parent::display($tpl);
    }
    
    protected function addToolBar() {
        $canDo = XbmapsHelper::getActions();
        
        ToolbarHelper::title(Text::_( 'XBMAPS' ).': '.Text::_( 'XBMAPS_TITLE_TAGSLIST' ), 'tags' );
        
        if ($canDo->get('core.create') > 0) {
        	ToolbarHelper::addNew('tagslist.tagnew');
        }
        if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
        	ToolbarHelper::editList('tagslist.tagedit');
        }
        
        ToolbarHelper::custom(); //spacer
        
        if ($canDo->get('core.admin')) {
        	ToolbarHelper::preferences('com_xbmaps');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbmaps/doc?tmpl=component#admin-tags' );
    }
    
    protected function setDocument()
    {
    	$document = Factory::getDocument();
    	$document->setTitle(strip_tags(Text::_('XBMAPS_TTILE_TAGSLIST')));
    }
    
}