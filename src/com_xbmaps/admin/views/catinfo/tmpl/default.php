<?php
/*******
 * @package xbMaps
 * @version 0.8.0.g 21st October 2021
 * @filesource admin/views/catinfo/tmpl/edit.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$item = $this->item;
$celink = 'index.php?option=com_categories&task=category.edit&id=';
$xblink = 'index.php?option=com_xbmaps';

?>
<div class="row-fluid">
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" >
	<?php if ($this->global_use_cats == 0) : ?>
		<div class="j-toggle-main alert alert-error">
	      <button type="button" class="close" data-dismiss="alert">×</button>
			<h4 class="alert-heading"><?php echo Text::_('XBMAPS_CATS_GLOBAL_DISABLED'); ?></h4> 
			<div class="alert-message"><?php echo Text::_('XBMAPS_CATS_GLOBAL_DISABLED_INFO'); ?>      
	      </div>
		</div>
	<?php  elseif (!$this->mapcats || !$this->mrkcats || !$this->trkcats) : ?>
		<div class="j-toggle-main alert alert-warn">
	      <button type="button" class="close" data-dismiss="alert">×</button>
			<h4 class="alert-heading">
				<?php if(!$this->mapcats) echo 'Map '; ?>
				<?php if(!$this->mrkcats) echo 'Marker '; ?>
				<?php if(!$this->trkcats) echo 'Track '; ?>
				<?php echo Text::_('XBMAPS_CATSTYPE_DISABLED'); ?>
			</h4> 
			<div class="alert-message"><?php echo Text::_('XBMAPS_CATS_GLOBAL_DISABLED_INFO'); ?>
	      	</div>
		</div>
	<?php endif; ?>
		<div style="max-width:1080px;">
		<form action="index.php?option=com_xbmaps&view=catinfo" method="post" id="adminForm" name="adminForm">
		<div class="row-fluid xbmb8">
			<div class= "span3">
				  <h3><?php echo Text::_('XBMAPS_CAT_ITEMS'); ?></h3>
				  <p class="xbnit"><?php Text::_('XBMAPS_CATINFO_INFO'); ?></p>
			</div>
			<div class= "span5">
				<a href="<?php echo $celink.$item->id; ?>" class="badge badge-success">
					<h2><?php echo $item->title; ?></h2>
				</a>
			</div>
			<div class="span4">
				<div class="row-fluid">
		            <div class="span7">
		                <p><?php echo '<i>'.Text::_('XBMAPS_ALIAS').'</i>: '.$item->alias; ?></p>
		            </div>
					<div class= "span5">
						<p><?php echo '<i>'.Text::_('JGRID_HEADING_ID').'</i>: '.$item->id; ?></p>
		 			</div>
				</div>
				<div class="row-fluid xbmb8">
					<div class= "span12">
							<p>
								<i><?php echo Text::_('XBMAPS_CATEGORY').' '.Text::_('XBMAPS_HIERARCHY'); ?>: </i> 
								<?php $path = str_replace('/', ' - ', $item->path);
								echo 'root - '.$path; ?>
							</p>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid xbmb8">
			<div class= "span2">
				<p><i><?php echo Text::_('XBMAPS_CATEGORY').' '.Text::_('XBMAPS_DESCRIPTION'); ?>:</i></p>
			</div>
   			<div class="span6">
			<?php if ($item->description != '') : ?>
     			<div class="xbbox xbboxgrey" style="max-width:400px;">
    				<?php echo $item->description; ?>
    			</div>
    		<?php else: ?>
    			<p><i>(<?php echo Text::_('XBMAPS_NO_DESCRIPTION'); ?>)</i></p>
			<?php endif; ?>
			</div>
			<div class= "span4">
				<?php if (!empty($item->note)) : ?>
					<p><i><?php Jtext::_('XBMAPS_ADMIN_NOTE'); ?>:</i>  <?php echo $item->note; ?></p>
				<?php endif; ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class= "span4">
				<div class="xbbox <?php echo $this->mapcats ? 'xbboxcyan' : 'xbboxgrey'; ?>">
					<p><?php echo $item->mapcnt.' '.Text::_('XBMAPS_MAPS').' '.Text::_('XBMAPS_IN_CATEGORY'); ?>  <span class="label label-success"><?php echo $item->title; ?></span></p>
					<?php if ($item->mapcnt > 0 ) : ?>
						<ul>
						<?php foreach ($item->maps as $i=>$bk) { 
							echo '<li><a href="'.$xblink.'&view=map&task=map.edit&id='.$bk->id.'">'.$bk->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
				</div>
			</div>
			<div class= "span4">
				<div class="xbbox <?php echo $this->mrkcats ? 'xbboxgrn' : 'xbboxgrey'; ?>">
					<p><?php echo $item->mrkcnt.' '.Text::_('XBMAPS_MARKERS').' '.Text::_('XBMAPS_IN_CATEGORY'); ?>  <span class="label label-success"><?php echo $item->title; ?></span></p>
					<?php if ($item->mrkcnt > 0 ) : ?>
						<ul>
						<?php foreach ($item->markers as $i=>$rev) { 
							echo '<li><a href="'.$xblink.'&view=marker&task=marker.edit&id='.$rev->id.'">'.$rev->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
				</div>
			</div>
			<div class= "span4">
				<div class="xbbox <?php echo $this->trkcats ? 'xbboxmag' : 'xbboxgrey'; ?>">
					<p><?php echo $item->trkcnt.' '.Text::_('XBMAPS_TRACKS').' '.Text::_('XBMAPS_IN_CATEGORY'); ?>  <span class="label label-success"><?php echo $item->title; ?></span></p>
					<?php if ($item->trkcnt > 0 ) : ?>
						<ul>
						<?php foreach ($item->tracks as $i=>$per) { 
							echo '<li><a href="'.$xblink.'&view=track&task=track.edit&id='.$per->id.'">'.$per->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="tid" value="<?php echo $item->id;?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
		</form>
		</div>
	</div>
</div>
<center>
		<a href="<?php echo $xblink; ?>&view=catslist" class="btn btn-small">
			<?php echo Text::_('XBMAPS_CATLIST'); ?></a>
		</center>
<div class="clearfix"></div>
<p><?php echo XbmapsGeneral::credit();?></p>

