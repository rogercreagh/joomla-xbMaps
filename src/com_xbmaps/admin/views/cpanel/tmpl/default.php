<?php
/*******
 * @package xbMaps
 * @version 0.8.0.g 21st October 2021
 * @filesource admin/views/cpanel/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=cpanel'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div id="j-sidebar-container">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" >
			<h3><?php echo Text::_('XBMAPS_STATUS_SUM'); ?></h3>
			<div class="row-fluid">
            	<div class="span8">
            		<div class="row-fluid">
            			<div class="span12">
        					<div class="xbbox xbboxcyan">
        						<h3 class="xbtitle">
        							<span class="badge badge-info pull-right"><?php echo Text::_('XBMAPS_TOTAL').' '. $this->mapStates['total']; ?></span> 
        							<a href="index.php?option=com_xbmaps&view=maps"><?php echo ucfirst(Text::_('XBMAPS_MAPS')); ?></a>
        						</h3>
        						<div class="row-striped">
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge badge-success xbmr10"><?php echo $this->mapStates['published']; ?></span>
        									<?php echo ucfirst(Text::_('XBMAPS_PUBLISHED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->mapStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->mapStates['unpublished']; ?></span>
        									<?php echo ucfirst(Text::_('XBMAPS_UNPUBLISHED')); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge <?php echo $this->mapStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->mapStates['archived']; ?></span>
        									<?php echo ucfirst(Text::_('XBMAPS_ARCHIVED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->mapStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->mapStates['trashed']; ?></span>
        									<?php echo ucfirst(Text::_('XBMAPS_TRASHED')); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span12">
        									<span class="badge <?php echo $this->trackCnts['mapswithtracks']>0 ?'badge-cyan' : ''; ?> xbmr10"><?php echo $this->trackCnts['mapswithtracks']; ?></span>
        									<?php echo Text::_('maps with tracks'); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span12">
        									<span class="badge <?php echo $this->markerCnts['mapswithmarkers']>0 ?'badge-mag' : ''; ?> xbmr10"><?php echo $this->markerCnts['mapswithmarkers']; ?></span>
        									<?php echo Text::_('maps with markers'); ?>
        								</div>
        							</div>
        						</div>
        					</div>
            			
            			</div>
            		</div>
            		<div class="row-fluid">
            			<div class="span12">
        					<div class="xbbox xbboxgrn">
        						<h3 class="xbtitle">
        							<span class="badge badge-info pull-right"><?php echo Text::_('XBMAPS_TOTAL').' '. $this->trackStates['total']; ?></span> 
        							<a href="index.php?option=com_xbmaps&view=tracks"><?php echo ucfirst(Text::_('XBMAPS_TRACKS')); ?></a>
        						</h3>
        						<div class="row-striped">
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge badge-success xbmr10"><?php echo $this->trackStates['published']; ?></span>
        									<?php echo ucfirst(Text::_('XBMAPS_PUBLISHED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->trackStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->trackStates['unpublished']; ?></span>
        									<?php echo ucfirst(Text::_('XBMAPS_UNPUBLISHED')); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge <?php echo $this->trackStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->trackStates['archived']; ?></span>
        									<?php echo ucfirst(Text::_('XBMAPS_ARCHIVED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->trackStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->trackStates['trashed']; ?></span>
        									<?php echo ucfirst(Text::_('XBMAPS_TRASHED')); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span12">
        									<span class="badge <?php echo $this->trackCnts['tracksonmaps']>0 ?'badge-cyan' : ''; ?> xbmr10"><?php echo $this->trackCnts['tracksonmaps']; ?></span>
        									<?php echo Text::_('tracks assigned to maps'); ?>
        								</div>
        							</div>
        						</div>
        					</div>
            			
            			</div>
            		</div>
            		<div class="row-fluid">
            			<div class="span12">
        					<div class="xbbox xbboxblue">
        						<h3 class="xbtitle">
        							<span class="badge badge-info pull-right"><?php echo Text::_('XBMAPS_TOTAL').' '. $this->markerStates['total']; ?></span> 
        							<a href="index.php?option=com_xbmaps&view=markers"><?php echo ucfirst(Text::_('XBMAPS_MARKERS')); ?>
        						</h3>
        						<div class="row-striped">
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge badge-success xbmr10"><?php echo $this->markerStates['published']; ?></span>
        									<?php echo ucfirst(Text::_('XBMAPS_PUBLISHED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->markerStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->markerStates['unpublished']; ?></span>
        									<?php echo ucfirst(Text::_('XBMAPS_UNPUBLISHED')); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge <?php echo $this->markerStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->markerStates['archived']; ?></span>
        									<?php echo ucfirst(Text::_('XBMAPS_ARCHIVED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->markerStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->markerStates['trashed']; ?></span>
        									<?php echo ucfirst(Text::_('XBMAPS_TRASHED')); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span12">
        									<span class="badge <?php echo $this->markerCnts['markersonmaps']>0 ?'badge-mag' : ''; ?> xbmr10"><?php echo $this->markerCnts['markersonmaps']; ?></span>
        									<?php echo Text::_('markers assigned to maps'); ?>
        								</div>
        							</div>
        						</div>
        					</div>
            			
            			</div>
            		</div>
              	</div>
				<div id="xbinfo" class="span4">
					<div class="row-fluid">
			        	<?php echo HTMLHelper::_('bootstrap.startAccordion', 'slide-cpanel', array('active' => 'keyconfig')); ?>
		        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-cpanel', Text::_('XBMAPS_KEY_CONFIG'), 'keyconfig','xbaccordion'); ?>
		        		Maps
		        		<ul>
		        			<li>Categories: 
		        				<?php if (!$this->mapcats) {
		        				    echo Text::_('XBMAPS_DISABLED');
		        				} else {
		        				    echo 'Default '.$this->mapcat;
		        				} ?>
		        				
		        			</li>
		        			<li>Tags: <b><?php echo Text::_($this->maptags ? 'XBMAPS_ENABLED' : 'XBMAPS_DISABLED'); ?></b>
		        			</li>
		        			<li>Default Type: <b><?php echo $this->params->get('map_type')?></b>
		        			</li>
		        		</ul>
		        		Markers
		        		<ul>
		        			<li>Categories:        			
		        				<?php if (!$this->mrkcats) {
		        				    echo Text::_('XBMAPS_DISABLED');
		        				} else {
		        				    echo 'Default '.$this->markercat;
		        				} ?>
		        				
		        			</li>
		        			<li>Tags: <b><?php echo Text::_($this->mrktags ? 'XBMAPS_ENABLED' : 'XBMAPS_DISABLED'); ?></b>
		        			</li>
		        			<li>Images Folder: <b>/images/<?php echo $this->params->get('def_markers_folder')?></b>
		        			</li>
		        		</ul>
		        		Tracks
		        		<ul>
		        			<li>Categories:		        			
		        				<?php if (!$this->trkcats) {
		        				    echo Text::_('XBMAPS_DISABLED');
		        				} else {
		        				    echo 'Default '.$this->trackcat;
		        				} ?>
		        				
		        			</li>
		        			<li>Tags: <b>
		        				<?php echo Text::_($this->trktags ? 'XBMAPS_ENABLED' : 'XBMAPS_DISABLED'); ?></b>
		        			</li>
		        			<li>GPX Folder: <b>/<?php echo $this->params->get('def_tracks_folder')?></b>
		        			</li>
		        			<li>Single Track View: <b>
		        				<?php echo Text::_($this->trkview ? 'XBMAPS_ENABLED' : 'XBMAPS_DISABLED');?></b>
		        			</li>
		        		</ul>
	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
		        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-cpanel', Text::_('XBMAPS_SYSINFO'), 'sysinfo','xbaccordion'); ?>
	        			<p><b><?php echo Text::_( 'XBMAPS_COMPONENT' ); ?></b>
							<br /><?php echo Text::_('XBMAPS_VERSION').': '.$this->xmldata['version'].'<br/>'.
								$this->xmldata['creationDate'];?>
						</p>
						<p><b><?php echo Text::_( 'XBMAPS_PLUGIN' ); ?></b>: <?php echo Text::_( 'XBMAPS_NOT_INSTALLED' ); ?>
						</p>
						<p><b><?php echo Text::_( 'XBMAPS_YOUR_CLIENT' ); ?></b>
							<br/><?php echo $this->client['platform'].'<br/>'.$this->client['browser']; ?>
						</p>
	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
		        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-cpanel', Text::_('XBMAPS_REGINFO'), 'reginfo','xbaccordion'); ?>
		        		 <?php if (XbmapsGeneral::penPont()) : ?>
		        			<p><?php echo Text::_('XBMAPS_THANKS_REG'); ?></p>
		        		<?php else : ?>
		        			<p><b><?php echo Text::_('XBMAPS'); ?></b> <?php echo Text::_('XBMAPS_REG_ASK'); ?></p>
		        			 <?php echo Text::_('XBMAPS_BEER_TAG').'<br />'.Text::_('XBMAPS_BEER_FORM');?>
		        		<?php endif; ?>
	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
	        			<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-cpanel', JText::_('XBMAPS_ABOUT'), 'about','xbaccordion'); ?>
	        			<p><?php echo JText::_( 'XBMAPS_ABOUT_INFO' ); ?></p>
	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
	        			<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-cpanel', JText::_('XBMAPS_LICENSE'), 'license','xbaccordion'); ?>
	        			<p><?php echo JText::_( 'XBMAPS_LICENSE_INFO' ); ?></p>
	        			<hr />
	        			<p>
	        				<?php echo Text::_( 'XBMAPS' ).' '.$this->xmldata['copyright']; ?>
	        			</p>
						<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
						<?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
					</div>		
				</div>
			</div>	
			<div class="row-fluid">
            	<div class="span6">
					<div class="xbbox xbboxyell">
						<h3 class="xbtitle">
							<span class="badge badge-info pull-right"><?php //echo Text::_('XBMAPS_TOTAL').' '. $this->trackStates['total']; ?></span> 
							<a href="index.php?option=com_xbmaps&view=catslist"><?php echo Text::_('XBMAPS_CATEGORIES'); ?></a>
						</h3>
				<div class="row-striped">
					<div class="row-fluid">
						<div class="span6">
							<span class="badge badge-success xbmr10"><?php echo $this->catStates['published']; ?></span>
							<?php echo Text::_('XBMAPS_PUBLISHED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->catStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->catStates['unpublished']; ?></span>
							<?php echo Text::_('XBMAPS_UNPUBLISHED'); ?>
						</div>
					</div>
 					<div class="row-fluid">
						<div class="span6">
							<span class="badge <?php echo $this->catStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->catStates['archived']; ?></span>
							<?php echo Text::_('XBMAPS_ARCHIVED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->catStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->catStates['trashed']; ?></span>
							<?php echo Text::_('XBMAPS_TRASHED'); ?>
						</div>
					</div>
                 </div>
                 <h3 class="xbsubtitle"><?php  echo Text::_('XBMAPS_COUNT_CATS'); ?><span class="xb09 xbnorm"> <i>(<?php echo Text::_('XBMAPS_MAPS_MRKS_TRKS'); ?>)</i></span></h3>
                 <div class="row-striped">
					<div class="row-fluid">
						    <?php echo $this->catlist; ?>
					</div>
				</div>
					</div>            			
            	</div>
            	<div class="span6">
			<div class="xbbox xbboxmag">
				<h3 class="xbtitle">
					<span class="badge badge-info pull-right"><?php echo ($this->tags['tagcnts']['mapcnt'] + $this->tags['tagcnts']['mrkcnt']  + $this->tags['tagcnts']['trkcnt']) ; ?></span> 
					<a href="index.php?option=com_xbmaps&view=tagslist"><?php echo Text::_('XBMAPS_TAGS'); ?></a>
				</h3>
				<div class="row-striped">
					<div class="row-fluid">
                      <?php echo 'Films: ';
						echo '<span class="bkcnt badge  pull-right">'.$this->tags['tagcnts']['mapcnt'].'</span>'; ?>
                    </div>  
                    <div class="row-fluid">
                      <?php echo 'People: ';
						echo '<span class="percnt badge pull-right">'.$this->tags['tagcnts']['mrkcnt'].'</span>'; ?>
                    </div>  
                    <div class="row-fluid">
                      <?php echo 'Reviews: ';
						echo '<span class="revcnt badge pull-right">'.$this->tags['tagcnts']['trkcnt'].'</span>'; ?>
                    </div>  
                 </div>
				 <h3 class="xbsubtitle"><?php echo Text::_('XBMAPS_COUNT_TAGS'); ?><span class="xb09 xbnorm"><i>(<?php echo Text::_('XBMAPS_MAPS_MRKS_TRKS'); ?>)</i></span></h3>
              <div class="row-fluid">
                 <div class="row-striped">
					<div class="row-fluid">
						<?php echo $this->taglist; ?>
                   </div>
                 </div>
			</div>
		</div>
            	</div>
            	</div>
           	</div>
			
		</div>
	</div>
</form>
<div class="clearfix"></div>
<p><?php echo XbmapsGeneral::credit();?></p>

                  