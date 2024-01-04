<?php
/*******
 * @package xbMaps Component
 * @version 1.5.2.0 4th January 2024
 * @filesource admin/views/dashboard/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;

?>
<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=dashboard'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div id="j-sidebar-container">
			<?php echo $this->sidebar; ?>
			<hr />
			<?php echo HTMLHelper::_('bootstrap.startAccordion', 'slide-info', array('active' => 'sysinfo')); ?>
        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-info', Text::_('XBMAPS_SYSINFO'), 'sysinfo','xbaccordion'); ?>
    			<p><b><?php echo Text::_( 'XBMAPS_COMPONENT' ); ?></b>
					<br /><?php echo Text::_('XBMAPS_VERSION').': <b>'.$this->xmldata['version'].'</b><br/>'.
						$this->xmldata['creationDate'];?>
				</p>
				<p><b><?php echo Text::_( 'XBMAPS_CONTENT_PLUGIN' ); ?></b>: ;
				<?php if(PluginHelper::isEnabled('content','xbmaps')) {
					$man = XbmapsGeneral::getExtManifest('plugin','xbmaps','content');
					if ($man) {
						$man = json_decode($man);
						echo '<br />'.Text::_('XBMAPS_VERSION').': '.$man->version;
						echo '<br />'.$man->creationDate;
					} else {
						echo 'problem with manifest';
					}
				} else {
					echo Text::_( 'XBMAPS_NOT_INSTALLED' ); 
				}?>
				</p>
				<p><b><?php echo Text::_( 'XBMAPS_BUTTON_PLUGIN' ); ?></b>: ;
				<?php if(PluginHelper::isEnabled('editor-xtd','xbmaps')) {
					$man = XbmapsGeneral::getExtManifest('plugin','xbmaps','editors-xtd');
					if ($man) {
						$man = json_decode($man);
						echo '<br />'.Text::_('XBMAPS_VERSION').': '.$man->version;
						echo '<br />'.$man->creationDate;
					} else {
						echo 'problem with manifest';
					}
				} else {
					echo Text::_( 'XBMAPS_NOT_INSTALLED' ); 
				}?>
				</p>
				<p><b><?php echo Text::_( 'XBMAPS_YOUR_CLIENT' ); ?></b>
					<br/><?php echo $this->client['platform'].'<br/>'.$this->client['browser']; ?>
				</p>
    			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
        			<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-info', Text::_('XBMAPS_LICENSE'), 'license','xbaccordion'); ?>
        			<p><?php echo Text::_( 'XBMAPS_LICENSE_INFO' ); ?></p>
        			<hr />
        				<?php echo Text::_( 'XBMAPS' ).' '.$this->xmldata['copyright']; ?>       			
				<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
            	<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-info', Text::_('XBMAPS_REGINFO'), 'reginfo','xbaccordion'); ?>
            		 <?php if (XbmapsGeneral::penPont()) : ?>
            			<p><?php echo Text::_('XBMAPS_THANKS_REG'); ?></p>
            		<?php else : ?>
            			<p><b><?php echo Text::_('XBMAPS'); ?></b> <?php echo Text::_('XBMAPS_REG_ASK'); ?></p>
            			<p class="xbtc"><?php echo Text::_('XBMAPS_BEER_TAG').'<br />'.Text::_('XBMAPS_BEER_FORM');?></p>
            		<?php endif; ?>
        		<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
			<?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
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
        									<?php echo Text::_('XBMAPS_MAPS_WITH_TRACKS'); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span12">
        									<span class="badge <?php echo $this->markerMapCnts['mapswithmarkers']>0 ?'badge-mag' : ''; ?> xbmr10"><?php echo $this->markerMapCnts['mapswithmarkers']; ?></span>
        									<?php echo Text::_('XBMAPS_MAPS_WITH_MARKERS'); ?>
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
        									<?php echo Text::_('XBMAPS_TRACKS_WITH_MAPS'); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span12">
        									<span class="badge <?php echo $this->markerTrackCnts['trackswithmarkers']>0 ?'badge-mag' : ''; ?> xbmr10"><?php echo $this->markerTrackCnts['trackswithmarkers']; ?></span>
        									<?php echo Text::_('XBMAPS_TRACKS_WITH_MARKERS'); ?>
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
        							<a href="index.php?option=com_xbmaps&view=markers"><?php echo ucfirst(Text::_('XBMAPS_MARKERS')); ?></a>
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
        									<span class="badge <?php echo $this->markerMapCnts['markersonmaps']>0 ?'badge-mag' : ''; ?> xbmr10"><?php echo $this->markerMapCnts['markersonmaps']; ?></span>
        									<?php echo Text::_('XBMAPS_MARKERS_WITH_MAPS'); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span12">
        									<span class="badge <?php echo $this->markerTrackCnts['markersontracks']>0 ?'badge-mag' : ''; ?> xbmr10"><?php echo $this->markerTrackCnts['markersontracks']; ?></span>
        									<?php echo Text::_('XBMAPS_MARKERS_WITH_TRACKS'); ?>
        								</div>
        							</div>
        						</div>
        					</div>
            			
            			</div>
            		</div>
              	</div>
				<div id="xbinfo" class="span4">
					<div class="row-fluid">
			        	<?php echo HTMLHelper::_('bootstrap.startAccordion', 'slide-dashboard', array('active' => 'keyconfig')); ?>
		        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBMAPS_KEY_CONFIG'), 'keyconfig','xbaccordion'); ?>
		        		<?php echo Text::_('XBMAPS_UNINSTALL_OPTIONS'); ?>
		        		<ul>
		        			<li>
		        				<?php echo Text::_('XBMAPS_UNINSTALL_DATA').': '; ?>
		        				<b>
		        				<?php echo ($this->savedata) ? Text::_('XBMAPS_KEEP') : Text::_('XBMAPS_DELETE'); ?>
		        				</b>
		        			</li>
		        			<li>
		        				<?php echo Text::_('XBMAPS_UNINSTALL_FILES').': '; ?>
		        				<b>
		        				<?php echo ($this->savefiles) ? Text::_('XBMAPS_KEEP') : Text::_('XBMAPS_DELETE'); ?>
		        				</b>
		        			</li>
		        		</ul>
		        		<?php echo Text::_('XBMAPS_FASOURCE'); ?>
		        		<ul>
		        			<li><b>
		        				<?php switch ($this->fasource) {
		        				    case 1:
		        				        echo Text::_('XBMAPS_FAKIT');
		        				        break;
		        				    case 2:
		        				        echo Text::_('XBMAPS_CDN');
		        				        break;		        				        
		        				    default:
		        				        echo Text::_('XBMAPS_NOFA');
    		        				    break;
		        				}?>
		        			</b></li>
		        			<?php if ($this->fasource==1) : ?>
		        				<li>
		        					<?php echo Text::_('XBMAPS_FAKITID').': <b>'.$this->fakitid.'</b>'; ?>
		        				</li>
		        			<?php endif; ?>
		        		</ul>
		        		<?php echo ucfirst(Text::_('XBMAPS_MAPS')); ?>
		        		<ul>
    		        		<li><?php echo Text::_('XBMAPS_DEF_MAP_TYPE'); ?>: <b><?php echo $this->params->get('map_type')?></b>
    		        		</li>
		        			<li><?php echo Text::_('XBMAPS_CATEGORIES'); ?> 
		        				<?php if (!$this->mapcats) {
		        				    echo ': <b>'.Text::_('XBMAPS_DISABLED').'</b>';
		        				} else {
		        				    echo Text::_('XBMAPS_DEFAULT').': <b>'.$this->mapcat.'</b>';
		        				} ?>		        				
		        			</li>
		        			<li><?php echo Text::_('XBMAPS_TAGS'); ?>:
		        			 <b><?php echo Text::_($this->maptags ? 'XBMAPS_ENABLED' : 'XBMAPS_DISABLED'); ?></b>
		        			</li>
		        		</ul>
		        		<?php echo ucfirst(Text::_('XBMAPS_MARKERS')); ?>
		        		<ul>
		        			<li><?php echo Text::_('XBMAPS_CATEGORIES'); ?>     			
		        				<?php if (!$this->mrkcats) {
		        				    echo ': <b>'.Text::_('XBMAPS_DISABLED').'</b>';
		        				} else {
		        				    echo Text::_('XBMAPS_DEFAULT').': <b>'.$this->markercat.'</b>';
		        				} ?>		        				
		        			</li>
		        			<li><?php echo Text::_('XBMAPS_TAGS'); ?>: 
		        			 <b><?php echo Text::_($this->mrktags ? 'XBMAPS_ENABLED' : 'XBMAPS_DISABLED'); ?></b>
		        			</li>
    		        		<li><?php echo Text::_('XBMAPS_IMG_FOLDER'); ?>: <b><code>/images/<?php echo $this->params->get('def_markers_folder')?></code></b>
    		        		</li>
		        		</ul>
		        		<?php echo ucfirst(Text::_('XBMAPS_TRACKS')); ?>
		        		<ul>
		        			<li><?php echo Text::_('XBMAPS_CATEGORIES'); ?>:		        			
		        				<?php if (!$this->trkcats) {
		        				    echo ': <b>'.Text::_('XBMAPS_DISABLED').'</b>';
		        				} else {
		        				    echo Text::_('XBMAPS_DEFAULT').': <b>'.$this->trackcat.'</b>';
		        				} ?>
		        				
		        			</li>
		        			<li><?php echo Text::_('XBMAPS_TAGS'); ?>: <b>
		        				<?php echo Text::_($this->trktags ? 'XBMAPS_ENABLED' : 'XBMAPS_DISABLED'); ?></b>
		        			</li>
		        			<li>GPX Folder: <b><code><?php echo $this->params->get('base_gpx_folder')?></code></b>
		        			<li>Elevation Images: <b><code>/images/xbmaps/elevations</code></b>
		        			</li>
		        			<li>Single Track View: <b>
		        				<?php echo Text::_($this->trkview ? 'XBMAPS_ENABLED' : 'XBMAPS_DISABLED');?></b>
		        			</li>
		        		</ul>
	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
	        			<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBMAPS_ABOUT'), 'about','xbaccordion'); ?>
	        			<p><?php echo Text::_( 'XBMAPS_ABOUT_INFO' ); ?></p>
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
                      <?php echo 'Maps: ';
						echo '<span class="bkcnt badge ';
						if ($this->tags['tagcnts']['mapcnt'] > 0 ) echo 'badge-cyan ';
						echo 'pull-right">'.$this->tags['tagcnts']['mapcnt'].'</span>'; ?>
                    </div>  
                    <div class="row-fluid">
                      <?php echo 'Narkers: ';
						echo '<span class="percnt badge ';
						if ($this->tags['tagcnts']['mrkcnt'] > 0 ) echo 'badge-cyan ';
						echo 'pull-right">'.$this->tags['tagcnts']['mrkcnt'].'</span>'; ?>
                    </div>  
                    <div class="row-fluid">
                      <?php echo 'Tracks: ';
						echo '<span class="revcnt badge ';
						if ($this->tags['tagcnts']['trkcnt'] > 0 ) echo 'badge-cyan ';
						echo 'pull-right">'.$this->tags['tagcnts']['trkcnt'].'</span>'; ?>
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

                  