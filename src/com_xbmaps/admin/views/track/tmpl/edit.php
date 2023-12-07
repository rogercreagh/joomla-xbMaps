<?php
/*******
 * @package xbMaps Component
 * @version 1.4.0.0 7th December 2023
 * @filesource admin/views/track/tmpl/edit.php
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
<script language="JavaScript" type="text/javascript">
	function confirmImport(){
		if (document.getElementById('jform_id').value == 0) {
			alert('Please save before uploading file');
			return false;
		}
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
        		<div class="span1 form-horizonal lbl50"><?php echo $this->form->renderField('id'); ?></div>
        	</div>
        </div>
	</div>
	<div class="row-fluid">
		<div class="span5">
			<?php echo $this->form->renderField('summary'); ?>
			<?php if($this->item->id >0) : ?>
    			<p><?php echo Text::_('XBMAPS_GPX_PARENT').' <code>'.$this->basegpxfolder.'</code> ';?>&nbsp;
    			<i><?php echo Text::_('XBMAPS_GPX_FOLDER_NOTE1'); ?>  
    				<a href="index.php?option=com_config&view=component&component=com_xbmaps#Tracks">
    					<?php echo Text::_('XBMAPS_GPX_BASE_FOLDER'); ?></a> 
    				<?php echo Text::_('XBMAPS_GPX_FOLDER_NOTE2'); ?>
    			</i></p>
    			<div class="pull-left">
    	    		<?php echo $this->form->renderField('gpx_folder','params'); ?>   
    			</div>
    			<div>
    	    		<?php echo $this->form->renderField('gpx_file','params'); ?>   
    			</div>  
    			<div class="clearfix"></div>        	    	 					
			<?php else : ?>
				<p class="xbnit">
					<?php echo Text::_('XBMAPS_SAVE_BEFORE_SELECT'); ?>
				</p>
			<?php endif; ?>
		</div>		
		<div class="span7">
			<?php echo $this->form->renderField('maplist'); ?>  
	    	<?php echo $this->form->renderField('gpx_filename'); ?>   
			<div class="form-hortizontal">
        		<?php echo $this->form->renderField('is_loop','params'); ?> 
			</div>          	    	 					
	    	<?php echo $this->form->renderField('elev_filename'); ?>   
		</div>
	</div>
    <div class="row-fluid form-horizontal">
 		<div class="span12">
			<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
    			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'files', Text::_('Files')); ?>
        			<div class="pull-left">
        	    		<?php echo $this->form->renderField('elev_folder','params'); ?>   
        			</div>
        			<div>
        	    		<?php echo $this->form->renderField('elev_file','params'); ?>   
        			</div>  
        			<div class="clearfix"></div>        	    	 					
					<?php if($this->elevfolder != '') : ?>
			        	<?php echo HTMLHelper::_('bootstrap.startAccordion', 'slide-dashboard', array('active' => '')); ?>
		        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBMAPS_GPX_UPLOAD_CLICK'),'upload','xbaccordion'); ?>
		        		
		    				<div class="pull-left">
		    					<p class="xbnit"><?php echo JText::_('XBMAPS_UPLOAD_SAVE_CHANGES'); ?></p>
			    				<?php echo $this->form->renderField('upload_elevfile'); ?>   					
		    				</div> 					
            				<div class="pull-left">
            					<p>File will upload to <code><?php echo $this->elevfolder; ?></code>.
                 	    		<?php echo $this->form->renderField('upload_newname'); ?> 
            				</div>	
		    				<div class="pull-right xbmr20">
		    					<p> </p>
	    						<button class="btn btn-warning" type="submit" 
									onclick="if(confirmImport()) {this.form.submit();}" >
									<i class="icon-upload icon-white"></i><?php echo JText::_('XBMAPS_UPLOAD_ELEV'); ?>
								</button>
		    				</div> 					
		    				<div class="clearfix"></div> 	
	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
	        			<?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
        			<?php else : ?>
        				<p class="xbnit">
        					<?php echo Text::_('XBMAPS_SAVE_BEFORE_UPLOAD'); ?>
        				</p>
        			<?php endif; ?>
    				
        		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>   
				
    			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'details', Text::_('XBMAPS_DETAILS')); ?>
            		<div class="row-fluid">
            			<div class="span9">  
            				<div class="row-fluid">
            					<div class="span12">
            						<?php if($this->gpxfolder != '') : ?>
                			        	<?php echo HTMLHelper::_('bootstrap.startAccordion', 'slide-dashboard', array('active' => '')); ?>
                		        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBMAPS_GPX_UPLOAD_CLICK'),'upload','xbaccordion'); ?>
                		        		
                		    				<div class="pull-left">
                		    					<p class="xbnit"><?php echo JText::_('XBMAPS_UPLOAD_SAVE_CHANGES'); ?></p>
                			    				<?php echo $this->form->renderField('upload_gpxfile'); ?>   					
                		    				</div> 					
                            				<div class="pull-left">
                            					<p>File will upload to <code><?php echo $this->gpxfolder; ?></code>.
                                 	    		<?php echo $this->form->renderField('upload_newname'); ?> 
                            				</div>	
                		    				<div class="pull-right xbmr20">
                		    					<p> </p>
                	    						<button class="btn btn-warning" type="submit" 
                									onclick="if(confirmImport()) {this.form.submit();}" >
                									<i class="icon-upload icon-white"></i><?php echo JText::_('XBMAPS_UPLOAD_GPX'); ?>
                								</button>
                		    				</div> 					
                		    				<div class="clearfix"></div> 	
                	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
                	        			<?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
            	        			<?php else : ?>
            	        				<p class="xbnit">
            	        					<?php echo Text::_('XBMAPS_SAVE_BEFORE_UPLOAD'); ?>
            	        				</p>
            	        			<?php endif; ?>
            					</div>
            	        	</div>              			 	
            	        	
                			<div class="clearfix"></div> 					
            
            	        	<div class="row-fluid">
            	        		<div class="span6">
            	    				<p> </p>				
                        	    	<?php echo $this->form->renderField('rec_date'); ?>  					
                        	    	<?php echo $this->form->renderField('rec_device'); ?>  					
                        	    	<?php echo $this->form->renderField('activity'); ?>  					
                        	    	<?php echo $this->form->renderField('track_colour'); ?> 
                      
            	        		</div>
            	        		<div class="span6">
            			        	<?php if(!empty($this->gpxinfo)) : ?>
            			        	<div class="xbbox xbboxmag">
            			        		<h4>Metadata from file <?php echo pathinfo($this->item->gpx_filename,PATHINFO_BASENAME); ?></h4>
            			        		<ul>
            			        			<li>GPX Name  : <?php echo $this->gpxinfo['gpxname']; ?></li>
            			        			<li>Track Name : <?php echo $this->gpxinfo['trkname']; ?></li>
            			        			<li>Creator (rec_device) : <?php echo $this->gpxinfo['creator']; ?></li>
            			        			<li>Date/Time (rec_date) : <?php echo $this->gpxinfo['recdate']; ?></li>	        			
            			        		</ul>
            			        		<p><i>Copy/paste above info to title, rec_device and rec_date if required
            			        			<br />NB data above will only refresh when track is saved</i></p>
            			        	</div>
            			        	<?php endif;?>
            	        		</div>
            	        	</div>
                        	<?php echo $this->form->renderField('description'); ?>   					
            	    	</div>
               			<div class="span3">
               				<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
               			</div>
               		</div>
        		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>   
        			
    			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'params', Text::_('XBMAPS_TRACK_LAYOUT')); ?>
     	   			<div class="row_fluid">
        				<div class="span7 form-horizontal-desktop">
        					<h4><?php echo Text::_('XBMAPS_LAYOUT_OPTIONS'); ?></h4>
                   	    	<?php echo $this->form->renderField('show_track_title','params'); ?>  					
                   	    	<?php echo $this->form->renderField('show_track_popover','params'); ?> 
                   	    	<?php echo $this->form->renderField('show_track_desc','params'); ?>  					
        					<?php echo $this->form->renderField('track_desc_class','params'); ?>
        					<?php echo $this->form->renderField('desc_title','params'); ?>
                   	    	<hr />
                   	    	<h4><?php echo Text::_('Track Info Box'); ?></h4>
                   	    	<?php echo $this->form->renderField('show_track_info','params'); ?>
                   	    	<?php echo $this->form->renderField('track_info_width','params'); ?>  					
                   	    	<?php echo $this->form->renderField('show_info_summary','params'); ?>  					
                   	    	<?php echo $this->form->renderField('show_info_stats','params'); ?>  					
                   	    	<?php echo $this->form->renderField('track_stats','params'); ?>  					
        				</div>
        				<div class="span5 form-horizontal-desktop">
        					<h4><?php echo Text::_('XBMAPS_MAP_HEIGHT_BORDER'); ?></h4>
        					<div class="row-fluid">
        						<div class="span6">
        							<div class="pull-left"><?php echo $this->form->renderField('map_height','params'); ?></div>
        							<div class="pull-left"><?php echo $this->form->renderField('height_unit','params'); ?></div>
        							<div class="clearfix"></div>
        						</div>
        						<div class="span6">
        						</div>
        					</div>
        					<div class="row-fluid">
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

				<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'publishing', Text::_('XBMAPS_PUBLISHING')); ?>
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
    <input type="hidden" name="task" id="task" value="track.edit" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbmapsGeneral::credit();?></p>
