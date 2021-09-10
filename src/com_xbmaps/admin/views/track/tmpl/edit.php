<?php
/*******
 * @package xbMaps
 * @version 0.1.1.h 22nd August 2021
 * @filesource admin/views/track/tmpl/edit.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
HTMLHelper::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => JText::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
HTMLHelper::_('formbehavior.chosen', 'select');

?>
<script language="JavaScript" type="text/javascript">
	function confirmImport(){
		document.getElementById('task').value='track.import';
		return true;
	}
	
</script>

<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=track&layout=edit&id=' . (int) $this->item->id); ?>"
	class="form-validate" enctype="multipart/form-data"
    method="post" name="adminForm" id="adminForm">
 	<div class="row-fluid">
		<div class="span12">
         	<div class="row-fluid">
        		<div class="span11">
        			<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
        		</div>
        		<div class="span1"><?php echo $this->form->renderField('id'); ?></div>
        	</div>
        </div>
	</div>
	<div class="row-fluid form-horizontal">
		<div class="span12">
	    	<?php echo $this->form->renderField('gpx_filename'); ?>   
		</div>
	</div>
    <div class="row-fluid form-horizontal">
 		<div class="span12">
 			<?php if (empty($this->params->def('def_tracks_folder'))) : ?>
 				<div class="alert alert-error">
	            	<p><?php echo Text::_('XBMAPS_FOLDER_WARNING'); ?> 
	            	 <a href="index.php?option=com_config&view=component&component=com_xbmaps#Tracks">
	            	 <?php echo Text::_('XBMAPS_GPX_FOLDER'); ?></a> <?php echo Text::_('XBMAPS_BEFORE_PROCEEDING'); ?></p>
 				</div>
			<?php else :?>
				<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
	
				<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'details', Text::_('Details')); ?>
				<div class="row-fluid">
					<div class="span9">  	
	     	    		<?php echo $this->form->renderField('gpx_folder'); ?> 
	    				<i><?php echo Text::_('XBMAPS_GPXFOLDER_NOTE'); ?>  
	    				<a href="index.php?option=com_config&view=component&component=com_xbmaps#Tracks"><?php echo Text::_('XBMAPS_GPX_FOLDER'); ?></a> 
	    				<?php echo Text::_('XBMAPS_GPXFOLDER_NOTE2'); ?></i></p>
	    				<p><?php echo $this->form->renderField('select_gpxfile'); ?>
	    				<div class="clearfix"></div> 					
	    				<div class="pull-left">
		    				<?php echo $this->form->renderField('upload_gpxfile'); ?>   					
	    				</div> 					
	    				<div class="pull-left">
    						<button class="btn btn-warning" type="submit" 
								onclick="if(confirmImport()) {this.form.submit();}" >
								<i class="icon-upload icon-white"></i><?php echo JText::_('XBMAPS_UPLOAD_GPX'); ?>
							</button>
	    				</div> 					
	    				<div class="clearfix"></div> 	
	    				<p> </p>				
            	    	<?php echo $this->form->renderField('rec_date'); ?>  					
            	    	<?php echo $this->form->renderField('track_colour'); ?> 
            	    	<?php echo $this->form->renderField('maplist'); ?>            	    	 					
            	    	<?php echo $this->form->renderField('description'); ?>   					
          
	    			</div>
        			<div class="span3">
        				<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
        			</div>
        		</div>
    			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
    			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('Publishing')); ?>
    			<div class="row-fluid form-horizontal-desktop">
    				<div class="span6">
    					<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
    				</div>
    				<div class="span6">
    					<?php echo LayoutHelper::render('joomla.edit.metadata', $this); ?>
    				</div>
    			</div>
    			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
     			<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
        	<?php endif; ?>
    	
    </div>
    <input type="hidden" name="task" id="task" value="track.edit" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbmapsGeneral::credit();?></p>

<script>
//jQuery(document).ready(function(){
//    jQuery('#modal-pvtrack').on('show', function () {
        // Load view vith AJAX
//        jQuery(this).find('.modal-content').load(jQuery('a[data-target="#index.php?option=com_xbmaps&view=trackview&id=1&tmpl=component'
//        +'"]').attr('href'));
//    })
//        +jQuery(this).attr('id')
//    jQuery('#ajax-modal').on('hidden', function () {
//     document.location.reload(true);
//    })
//});
</script>
<!-- 
<div class="modal hide fade" id="modal-pvtrack">
  <div class="modal-header">
    <button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
    <h3>Modal title</h3>
  </div>
  <div class="modal-body">
    Modal content here
  </div>
  <div class="modal-footer">
    <button class="btn" type="button" data-dismiss="modal">
      <?php //echo JText::_('JCLOSE'); ?>
    </button>
  </div>
</div>

<div class="modal fade" id="ajax-modal">
    <div class="modal-dialog">
        <div class="modal-content">
             Ajax content will be loaded here 
        </div>
    </div>
</div>
 -->
