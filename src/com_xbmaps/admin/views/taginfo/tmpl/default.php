<?php
/*******
 * @package xbMaps
 * @version 0.3.0.h 21st September 2021
 * @filesource admin/views/tag/tmpl/edit.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$item = $this->item;
$telink = 'index.php?option=com_tags&task=tag.edit&id=';
$xblink = 'index.php?option=com_xbmaps';
?>
<div class="row-fluid">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container" class="span12">
<?php endif;?>
	<?php if ($this->global_use_tags == 0) : ?>
		<div class="j-toggle-main alert alert-error">
	      <button type="button" class="close" data-dismiss="alert">×</button>
			<h4 class="alert-heading"><?php echo Text::_('XBMAPS_TAGS_GLOBAL_DISABLED'); ?></h4> 
			<div class="alert-message"><?php echo Text::_('XBMAPS_TAGS_GLOBAL_DISABLED_INFO'); ?></div>      
	      </div>
		<div>
	<?php  elseif (!$this->maptags || !$this->mrktags || !$this->trktags) : ?>
		<div class="j-toggle-main alert alert-warn">
	      <button type="button" class="close" data-dismiss="alert">×</button>
			<h4 class="alert-heading">
				<?php if(!$this->maptags) echo 'Map '; ?>
				<?php if(!$this->mrktags) echo 'Marker '; ?>
				<?php if(!$this->trktags) echo 'Track '; ?>
				<?php echo Text::_('XBMAPS_TAGSTYPE_DISABLED'); ?>
			</h4> 
			<div class="alert-message"><?php echo Text::_('XBMAPS_TAGSTYPE_DISABLED_INFO'); ?>
			</div>      
	      </div>
		<div>
	<?php endif; ?>
	

		<div style="max-width:1080px;">
		<form action="index.php?option=com_xbmaps&view=tag" method="post" id="adminForm" name="adminForm">
		<div class="row-fluid xbmb8">
			<div class= "span3">
				  <h3><?php echo Text::_('XBMAPS_TAG_ITEMS'); ?></h3>
				  <p class="xbnit"><?php Text::_('XBMAPS_TAGINFO_INFO'); ?></p>
			</div>
			<div class="span5">
				<a href="<?php echo $telink.$item->id; ?>" class="badge badge-info">
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
							<p><i><?php echo Text::_('XBMAPS_TAG').' '.Text::_('XBMAPS_HIERARCHY'); ?>: </i>
							<?php $path = str_replace('/', ' - ', $item->path);
								echo 'root - '.$path; ?>
							</p>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid xbmb8">
			<div class= "span2">
				<p><i><?php echo Text::_('XBMAPS_DESCRIPTION'); ?>:</i></p>
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
					<p><i><?php echo Jtext::_('XBMAPS_ADMIN_NOTE'); ?>:</i>  <?php echo $item->note; ?></p>
				<?php endif; ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class= "span6">
 				<div class="xbbox <?php echo $this->maptags ? 'xbboxcyan' : 'xbboxgrey'; ?>">
					<p><?php echo $item->mapcnt; ?> maps tagged <span class="label label-info"><?php echo $item->title; ?></span></p>
					<?php if ($item->mapcnt > 0 ) : ?>
						<ul>
						<?php foreach ($item->maps as $i=>$bk) { 
							echo '<li><a href="'.$xblink.'&view=map&task=map.edit&id='.$bk->id.'">'.$bk->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
				</div>
				<div class="xbbox <?php echo $this->mrktags ? 'xbboxmag' : 'xbboxgrey'; ?>">
					<p><?php echo $item->mrkcnt; ?> markers tagged <span class="label label-info"><?php echo $item->title; ?></span></p>
					<?php if ($item->mrkcnt > 0 ) : ?>
						<ul>
						<?php foreach ($item->markers as $i=>$rev) { 
							echo '<li><a href="'.$xblink.'&view=marker&task=marker.edit&id='.$rev->id.'">'.$rev->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
				</div>
			</div>

            <div class= "span6">
   				<div class="xbbox <?php echo $this->trktags ? 'xbboxgrn' : 'xbboxgrey'; ?>">
					<p><?php echo $item->trkcnt; ?> tracks tagged <span class="label label-info"><?php echo $item->title; ?></span></p>
					<?php if ($item->trkcnt > 0 ) : ?>
						<ul>
						<?php foreach ($item->tracks as $i=>$per) { 
							echo '<li><a href="'.$xblink.'&view=track&task=track.edit&id='.$per->id.'">'.$per->title.'</a></li> ';
						} ?>				
						</ul>
					<?php endif; ?>
				</div>
				<div class="xbbox xbboxgrey">
					<p><?php echo $item->othercnt; ?> other items also tagged <span class="label label-info"><?php echo $item->title; ?></span></p>
					<?php if ($item->othercnt > 0 ) : ?>
						<ul>
						<?php foreach ($item->others as $i=>$oth) { 
							$comp = substr($oth->type_alias, 0,strpos($oth->type_alias, '.'));
							$ctype = substr($oth->type_alias,strpos($oth->type_alias, '.')+1);
							echo '<li><a href="index.php?option='.$comp.'">'.$comp.'</a> - '.$ctype.': '.$oth->core_title.'</li> ';
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
<div class="clearfix"></div>
<p class="xbtc xbmt16">
	<a href="<?php echo $xblink; ?>&view=tagslist" class="btn btn-small">
		<?php echo Text::_('XBMAPS_TAGLIST'); ?></a>
</div>
<p><?php echo XbmapsGeneral::credit();?></p>
