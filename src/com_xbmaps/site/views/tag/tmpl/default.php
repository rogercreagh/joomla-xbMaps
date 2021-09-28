<?php
/*******
 * @package xbMaps
 * @version 0.4.0.1 28th September 2021
 * @filesource site/views/tag/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$item = $this->item;
$xblink = 'index.php?option=com_xbmaps&view=';

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbmapsHelperRoute::getMapsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$maplink = $xblink.'map' . $itemid.'&id=';

//$itemid = XbmapsHelperRoute::getMarkersRoute();
//$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
//$mrklink = $xblink.'marker' . $itemid.'&id=';

$itemid = XbmapsHelperRoute::getTracksRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$trklink = $xblink.'track' . $itemid.'&id=';

$itemid = XbmapsHelperRoute::getTagsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$tclink = $xblink.'tags' . $itemid;

?>
<div class="xbmaps">
<div class="row-fluid" style="margin-bottom:20px;">
	<div class="span3">
		<h4><?php echo Text::_('XBMAPS_ITEMSTAGGED').': '; ?></h4>		
	</div>	
	<div class="span9">
		<div class="badge badge-info pull-left"><h3><?php echo $item->title; ?></h3></div>
		
		<?php if (($this->show_empty) && (strpos($item->path,'/')!==false)) : ?>
			<div class="xb11 pull-left" style="padding-top:20px;margin-left:40px;">
				<i><?php echo Text::_('XBMAPS_TAGPARENTS'); ?>:</i> 
				<?php  $path = substr($item->path, 0, strrpos($item->path, '/'));
					$path = str_replace('/', ' - ', $path);
					echo $path; ?>
        	</div>
        <?php endif; ?>
	</div>	
</div>
<?php if ($item->description != '') : ?>
	<div class="row-fluid">
		<div class= "span2">
			<p><i><?php echo Text::_('XBMAPS_DESCRIPTION'); ?>:</i></p>
		</div>
		<div class="span10">
			<?php echo $item->description; ?>
		</div>
	</div>
<?php endif; ?>
<div class="row-fluid">
<?php if ($this->maptags) : ?>
	<div class= "span4">
		<div class="xbbox xbboxcyan xbmh200 xbyscroll">
			<p><?php echo $item->mapcnt.' '.Text::_('XBMAPS_MAPS_TAGGED'); ?></p>
			<?php if ($item->mapcnt > 0 ) : ?>
				<ul>
				<?php foreach ($item->maps as $i=>$bk) { 
					echo '<li><a href="'.JRoute::_($maplink.$bk->id).'">'.$bk->title.'</a></li> ';
				} ?>				
				</ul>
			<?php endif; ?>
		</div>
	</div>
 <?php endif; ?>
 <?php if ($this->mrktags) : ?>
	<div class= "span4">
		<div class="xbbox xbboxgrn xbmh200 xbyscroll">
			<p><?php echo $item->mrkcnt.' '.Text::_('XBMAPS_MARKERS_TAGGED'); ?></p>
			<?php if ($item->mrkcnt > 0 ) : ?>
				<ul>
				<?php foreach ($item->markers as $i=>$per) { 
					echo '<li><a href="'.JRoute::_($mrklink.$per->pid).'">'.$per->title.'</a></li> ';
					echo '<li>'.$per->title.'</li> ';
				} ?>				
				</ul>
			<?php endif; ?>
		</div>
	</div>
 <?php endif; ?>
 <?php if ($this->trktags) : ?>
	<div class= "span4">
		<div class="xbbox xbboxmag xbmh200 xbyscroll">
			<p><?php echo $item->trkcnt.' '.Text::_('XBMAPS_TRACKS_TAGGED'); ?></p>
			<?php if ($item->trkcnt > 0 ) : ?>
				<ul>
				<?php foreach ($item->tracks as $i=>$rev) { 
					echo '<li><a href="'.JRoute::_($trklink.$rev->id).'">'.$rev->title.'</a></li> ';
				} ?>				
				</ul>
			<?php endif; ?>
		</div>
	</div>
 <?php endif; ?>
</div>
<div class="row-fluid">
	<div class= "span12">
		<div class="xbbox xbboxgrey xbmh200 xbyscroll">
			<p><?php echo $item->othercnt.' '.Text::_('XBMAPS_OTHERS_TAGGED'); ?></p>
			<?php if ($item->othercnt > 0 ) : ?>
						<?php $span = intdiv(12, count($item->othcnts)); ?>
						<div class="row-fluid">
						<?php $thiscomp=''; $firstcomp=true; $thisview = ''; $firstview=true; 
						foreach ($item->others as $i=>$oth) {
							$comp = substr($oth->type_alias, 0,strpos($oth->type_alias, '.'));
							$view = substr($oth->type_alias,strpos($oth->type_alias, '.')+1);
							$isnewcomp = ($comp!=$thiscomp) ? true : false;
							$newview= ($view!=$thisview) ? true : false;
							// if it isnewcomp
							if ($isnewcomp) {
								if ($firstcomp) {
									$firstcomp = false;
								} else {
									echo '</ul></div>';
								}
								$thiscomp = $comp;
								$firstview=true;
								echo '<div class="span'.$span.'"><h4>'.ucfirst(substr($comp,4)).'</h4><ul>';
							}
							if ($newview) {
								if ($firstview) {
									$firstview = false;
								} else {
									echo '<br />';
								}
								$thisview = $view;
							}
							echo '<li><i>'.ucfirst($view);
							echo '</i> : <a href="index.php?option='.$comp.'&view='.$view.'&id='.$oth->othid.'">'.$oth->core_title.'</a></li> ';
							// 				<ul>
				} ?>			
				</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<p class="xbtc xbmt16">
	<a href="<?php echo JRoute::_($tclink); ?>" class="btn btn-small">
		<?php echo Text::_('XBMAPS_TAGSLIST'); ?>
	</a>
</p>
<div class="clearfix"></div>
<p><?php echo XbmapsGeneral::credit();?></p>
</div>

