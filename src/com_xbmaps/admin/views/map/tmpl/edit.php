<?php
/*******
 * @package xbMaps
 * @version 0.9.0.c 6th November 2021
 * @filesource admin/views/map/tmpl/edit.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
HTMLHelper::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => JText::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
HTMLHelper::_('formbehavior.chosen', 'select');

?>
<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm">
 	<div class="row-fluid">
		<div class="span10">
         	<div class="row-fluid">
        		<div class="span11">
        			<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
        		</div>
        		<div class="span1"><?php echo $this->form->renderField('id'); ?></div>
        	</div>
        </div>
	</div>
	<div class="row-fluid">
		<div class="span8 form-horizontal">
			<?php echo $this->form->renderField('summary'); ?>            	    	 					
		</div>		
	</div>
    <div class="row-fluid form-horizontal">
		<div class="span12">
			<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
	
			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'details', Text::_('XBMAPS_DETAILS')); ?>
			<div class="row-fluid">
	    		<div class="span6">
	          		<h4>Content</h4>
	          		<p>Description</p>
	    			<?php echo $this->form->getInput('description'); ?>
	    		</div>
	    		<div class="span3 form-vertical">
	          		<h4>Map Info</h4>
	          		<?php echo $this->form->renderField('map_type'); ?>   					
	          		<?php echo $this->form->renderField('centre_latitude'); ?>   					
	          		<?php echo $this->form->renderField('dmslatitude'); ?>   					
	        		<?php echo $this->form->renderField('centre_longitude'); ?>
	        		<?php echo $this->form->renderField('dmslongitude'); ?>
	        		<?php echo $this->form->renderField('default_zoom'); ?>
	   			</div>
				<div class="span3">
					<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
				</div>
	   		</div>
	 		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'params', JText::_('XBMAPS_MAP_CONTROLS')); ?>
			
			<div class="row-fluid form-horizontal-desktop">
				<div class="span6">
					<?php echo $this->form->renderField('map_zoom_control','params'); ?>
					<?php echo $this->form->renderField('map_home_button','params'); ?>
					<?php echo $this->form->renderField('map_zoom_wheel','params'); ?>
					<?php echo $this->form->renderField('map_show_scale','params'); ?>
				</div>
				<div class="span6">
					<?php echo $this->form->renderField('map_full_screen','params'); ?>
					<?php echo $this->form->renderField('map_search','params'); ?>
					<?php echo $this->form->renderField('map_easyprint','params'); ?>
          	    	<?php echo $this->form->renderField('hid_w3wapi','params'); ?> 
					<?php echo $this->form->renderField('map_click_marker','params'); ?>
				</div>
			</div>
			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'layout', Text::_('XBMAPS_MAP_LAYOUT')); ?>
			<div class="row_fluid">
				<div class="span7 form-horizontal-desktop">
					<h4><?php echo Text::_('Layout Options'); ?></h4>
					<?php echo $this->form->renderField('show_map_title','params'); ?>
					<?php echo $this->form->renderField('show_map_desc','params'); ?>
					<?php echo $this->form->renderField('map_desc_class','params'); ?>
					<?php echo $this->form->renderField('desc_title','params'); ?>
					<hr />
					<?php echo $this->form->renderField('show_map_info','params'); ?>
					<?php echo $this->form->renderField('map_info_width','params'); ?>
					<?php echo $this->form->renderField('show_info_summary','params'); ?>
					<?php echo $this->form->renderField('show_map_key','params'); ?>
					<?php echo $this->form->renderField('track_infodetails','params'); ?>
					<?php echo $this->form->renderField('marker_infocoords','params'); ?>
					<?php //echo $this->form->renderField('show_mrk_desc','params'); ?>
				</div>
				<div class="span5">
					<h4><?php echo Text::_('XBMAPS_MAP_DIMS_BORDER'); ?></h4>
					<div class="row-fluid form-vertical">
						<div class="span6">
							<div class="pull-left"><?php echo $this->form->renderField('map_height','params'); ?></div>
							<div class="pull-left"><?php echo $this->form->renderField('height_unit','params'); ?></div>
							<div class="clearfix"></div>
						</div>
						<div class="span6">
						</div>
					</div>
					<div class="row-fluid form-vertical">
						<div class="span12">
							<div class="pull-left"><?php echo $this->form->renderField('map_border','params'); ?></div>
							<div class="clearfix"></div>
							<div class="pull-left xbmr20"><?php echo $this->form->renderField('map_border_width','params'); ?></div>
							<div class="pull-left"><?php echo $this->form->renderField('map_border_colour','params'); ?></div>						
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>			
			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'markers', JText::_('Markers &amp; Tracks')); ?>
			<div class="row-fluid form-vertical">
	    		<div class="span6">
	          		<h4>Markers</h4>
					<?php echo $this->form->renderField('marker_clustering','params'); ?>
	        		<?php echo $this->form->renderField('markerlist'); ?>
	          	</div>
	    		<div class="span6">
	          		<h4>Tracks</h4>
	        		<?php echo $this->form->renderField('fit_bounds','params'); ?>
	        		<?php echo $this->form->renderField('tracklist'); ?>
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
			
	 	</div>
	 </div>
    <input type="hidden" name="task" value="map.edit" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbmapsGeneral::credit();?></p>

<script>
jQuery(document).ready(function(){
    jQuery('#ajax-modal').on('show', function () {
        // Load view vith AJAX
        jQuery(this).find('.modal-content').load(jQuery('a[data-target="#'+jQuery(this).attr('id')+'"]').attr('href'));
    })
    jQuery('#ajax-modal').on('hidden', function () {
     document.location.reload(true);
    })
});
</script>
<div class="modal fade" id="ajax-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
